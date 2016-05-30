<?php

namespace Codefocus\Vernacular;

use Codefocus\Vernacular\Interfaces\TokenizerInterface;
use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Services\BigramLookupService;
use Codefocus\Vernacular\Services\WordLookupService;
use Codefocus\Vernacular\Services\BigramKeyService;
use Codefocus\Vernacular\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Codefocus\Vernacular\Models\Source;
use Codefocus\Vernacular\Models\Bigram;
use Codefocus\Vernacular\Models\Dummy;
use Codefocus\Vernacular\Models\Word;
use Codefocus\Vernacular\Models\Url;
use Exception;
use DB;

class Vernacular
{
    protected $tokenizer;
    protected $config;
    protected static $sources = [];
    protected static $documents = [];
    
    protected static $wordCache;
    protected static $bigramCache;
    

    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = config('vernacular');
        }
        $this->config = $config;
        //  Instantiate the configured tokenizer.
        $this->tokenizer = new $this->config['tokenizer']();
        if (false === in_array(TokenizerInterface::class, class_implements($this->tokenizer))) {
            throw new VernacularException('Tokenizer should implement '.TokenizerInterface::class);
        }
        
        //  Instantiate caches.
        if (!(static::$wordCache instanceof WordLookupService)) {
            static::$wordCache = new WordLookupService;
        }
        if (!(static::$bigramCache instanceof BigramLookupService)) {
            static::$bigramCache = new BigramLookupService;
        }
    }
    
    /**
     * Learn a Url.
     * 
     * @param string $url
     * 
     * @return void
     */
    public function learnUrl($url)
    {
        $model = Url::where('url', '', $url)->first();
        if (!$model) {
            $model = new Url;
            $model->url = $url;
            $model->save();
        }
        else {
            $model->touch();
        }
    }
    
    /**
     * Return a Document instance for this model,
     * either from cache or from the database.
     * 
     * @param Model $model
     *
     * @return Document
     */
    protected function getDocumentForModel(Model $model)
    {
        //  Ensure this Model exists in the database.
        if (!$model->exists()) {
            throw new VernacularException('Cannot learn an unsaved Model.');
        }
        //  Lookup, load or create this Model's Source (reference to Model).
        $className = get_class($model);
        if (!isset(static::$sources[$className])) {
            static::$sources[$className] = Source::firstOrCreate(['model_class' => $className]);
        }
        //  Lookup, load or create this Document (reference to Model instance).
        $sourceId = static::$sources[$className]->id;
        $modelId = $model->id;
        if (!isset(static::$documents[$sourceId]) or !isset(static::$documents[$sourceId][$modelId])) {
            $document = Document
                ::where('source_id', '=', $sourceId)
                ->where('source_model_id', '=', $modelId)
                ->first();
            if (!$document) {
                $document = new Document;
                $document->source_id = $sourceId;
                $document->source_model_id = $modelId;
            }
            static::$documents[$sourceId][$modelId] = $document;
        }
        return static::$documents[$sourceId][$modelId];
    }
    
    
    
    /**
     * Learn a Document.
     * 
     * @param string $content
     * @param Document $document (default: null)
     * 
     * @throws VernacularException
     * 
     * @return Document
     */
    public function learnDocument($content, Document $document = null)
    {
        
        return DB::transaction(function () use ($content, $document) {
        
            if (null === $document) {
                //  Create a Dummy model to serve as the source Document,
                //  if none is provided.
                $model = new Dummy;
                $model->save();
                $document = $this->getDocumentForModel($model);
            }
            
            //  Apply HTML filter, if configured.
            if ($this->config['filters']['html']) {
                //  @TODO: Abstract to Codefocus\Vernacular\Filters\HtmlFilter
                //  @TODO: strip_tags is not sufficient, use Html2Text or similar.
            }
            
            //  Apply URL filter, if configured.
            if ($this->config['filters']['urls']) {
                //  @TODO: Abstract to Codefocus\Vernacular\Filters\UrlFilter
                //  @TODO: word boundaries around url.
                //  @TECHDEBT: hardcoded regex, and inefficient replacement string.
                $content = preg_replace('/[a-z]{2,8}:\/\/([a-z0-9-\.]+(\/[^\/\s]+)?)?/', ' ___ ', $content);
            }
            
            //  @TODO:  Split the content into sentences.
            //          https://github.com/codefocus/vernacular/issues/11
            //  
            // $sentences = $this->sentenceTokenizer->tokenize($content);
            // foreach($sentences as $sentence) {
            //     //  Extract tokens from this sentence.
            //     $tokens = $this->tokenizer->tokenize($sentence);
            //     dump($tokens);
            // }

            //  Extract tokens from this content.
            $tokens = $this->tokenizer->tokenize($content);
            
            //  @TODO:  Filter stopwords here, if configured.
            //          https://github.com/codefocus/vernacular/issues/9

            $numTokens = count($tokens);
            if (0 == $numTokens) {
                //  No tokens in this document.
                throw new VernacularException('No words found in this Model.');
            }
            
            //  Store document word count
            $document->word_count = $numTokens;
            $document->save();
            
            //  Count occurrences of each token.
            $uniqueCountedTokens = array_count_values($tokens);
            
            //  Get Word ids for our tokens.
            $wordIds = static::$wordCache->getAll(array_keys($uniqueCountedTokens));
            
            //  Link these Words to the document.
            $documentWordLinkData = [];
            foreach ($uniqueCountedTokens as $token => $frequency) {
                $documentWordLinkData[] = [
                    'document_id'       => $document->id,
                    'word_id'           => $wordIds[$token],
                    'frequency'         => $frequency,
                ];
            }
            //  Link Words to the Document.
            $maxRowsPerQuery = config('vernacular.max_rows_per_query');
            $documentWordLinkDataChunked = array_chunk($documentWordLinkData, $maxRowsPerQuery);
            unset($documentWordLinkData);
            foreach ($documentWordLinkDataChunked as $dataChunk) {
                DB::table('vernacular_document_word')->insert($dataChunk);
            }
            
            // $fart = DB::table('vernacular_document_word AS dw');
            
            // $r = new \ReflectionMethod($fart, 'join');
            // $params = $r->getParameters();
            // foreach ($params as $param) {
            //     //$param is an instance of ReflectionParameter
            //     dump($param);
            // }
            
            // dd('done');
            
            //  Increment Word frequencies.
            //  
            //  Update the frequency and document frequency for all
            //  words linked to this document in a single query.
            DB::table('vernacular_document_word AS dw')
                ->join('vernacular_word AS w', 'w.id', '=', 'dw.word_id')
                ->where('dw.document_id', '=', $document->id)
                ->update([
                    'w.frequency' => DB::raw('w.frequency + dw.frequency'),
                    'w.document_frequency' => DB::raw('w.document_frequency + 1')
                ])
                ;
            
            $this->createBigrams($document, $tokens, $wordIds);
            
            return $document;
        
        }); //  transaction

    }   //  function learnDocument
    
    
    // public function tagDocument($document, $tag) {
    //         //  @TODO:  if model->vernacularTags:
    //         //          - tag document
    //         //          - recalculate tag confidence for bigrams.
    //         //          https://github.com/codefocus/vernacular/issues/4
    // }
    
    
    /**
     * Index all learnable attributes of an Eloquent Model.
     * 
     * @param Model $model
     *
     * @throws VernacularException
     * @throws Exception
     *
     * @return boolean
     */
    public function learnModel(Model $model)
    {
        //  Ensure this Model exists in the database.
        if (!$model->exists()) {
            throw new VernacularException('Cannot learn an unsaved Model.');
        }
        //  Ensure all required attributes are set and valid.
        if (!isset($model->vernacularAttributes)) {
            throw new VernacularException('Required $vernacularAttributes not defined.');
        }
        if (!is_array($model->vernacularAttributes)) {
            throw new VernacularException('$vernacularAttributes should be an array.');
        }
        if (0 == count($model->vernacularAttributes)) {
            //  No learnable attributes specified.
            //  Nothing to learn.
            return false;
        }
        
        return DB::transaction(function () use ($model, $document) {
            //  Lookup, load or create the source Document for this Model.
            $document = $this->getDocumentForModel($model);
            
            //  Extract content from each learnable attribute.
            $content = '';
            foreach ($model->vernacularAttributes as $attribute) {
                $content .= (string)$model->$attribute.' ';
            }
            
            $this->learnDocument($content, $document);
            
            return $document;
        
        }); //  transaction
        
    }   //  function learnModel

    
    
    
    public function updateLearnedModel(Model $model)
    {
        //  @TODO
        //  https://github.com/codefocus/vernacular/issues/3
    }
    
    
    /**
     * Create raw bigrams from an array of tokens.
     * 
     * @param array $tokens
     * @param array $wordIds
     *
     * @return array
     */
    protected function getRawBigrams(array $tokens, array $wordIds)
    {
        $minDistance = min(4, max(
            1,
            (empty($this->config['word_distance']['min']) ? 1 : $this->config['word_distance']['min'])
        ));
        $maxDistance = min(4, max(
            $minDistance,
            (empty($this->config['word_distance']['max']) ? $minDistance : $this->config['word_distance']['max'])
        ));
        $numTokens = count($tokens);
        $rawBigrams = [];
        for ($distance = $minDistance; $distance <= $maxDistance; ++$distance) {
            //  Generate raw bigrams from the tokens, consisting of
            //  an array of the ids of both Words, and the distance.
            $iMaxTokenA = $numTokens - $distance;
            for ($iTokenA = 0; $iTokenA < $iMaxTokenA; ++$iTokenA) {
                $iTokenB = $iTokenA + $distance;
                //  Get the Word ids for these tokens,
                //  and combine them into a single unique lookup key.
                $lookupKey = BigramKeyService::make(
                    $wordIds[$tokens[$iTokenA]],
                    $wordIds[$tokens[$iTokenB]],
                    $distance
                );
                //  Increment this bigram's frequency.
                if (!isset($rawBigrams[$lookupKey])) {
                    $rawBigrams[$lookupKey] = [
                        'frequency'         => 0,
                        'first_instance'    => $iTokenA,
                    ];
                }
                ++$rawBigrams[$lookupKey]['frequency'];
            }
        }
        return $rawBigrams;
    }   //  function getRawBigrams

    
    /**
     * Create Bigrams from an array of tokens,
     * link them to the Document and
     * update their frequencies.
     * 
     * @param Document $document
     * @param array $tokens
     * @param array $wordIds
     *
     * @return boolean
     */
    protected function createBigrams(Document $document, array $tokens, array $wordIds)
    {
        $bigramDataToLinkToDocument = [];
        $rawBigrams = $this->getRawBigrams($tokens, $wordIds);
        
        //  Get or create a Bigram instance for each raw bigram.
        $lookupTokens = array_map('intval', array_keys($rawBigrams));
        $bigrams = static::$bigramCache->getAll($lookupTokens);
        
        //  Link these Bigrams to the document.
        $documentBigramLinkData = [];
        foreach ($rawBigrams as $lookupKey => $rawBigram) {
            $documentBigramLinkData[] = [
                'document_id'       => $document->id,
                'bigram_id'         => $bigrams[$lookupKey],
                'frequency'         => $rawBigram['frequency'],
                'first_instance'    => $rawBigram['first_instance'],
            ];
        }
        
        //  Link Bigrams to the Document.
        $maxRowsPerQuery = config('vernacular.max_rows_per_query');
        $documentBigramLinkDataChunked = array_chunk($documentBigramLinkData, $maxRowsPerQuery);
        unset($documentBigramLinkData);
        foreach ($documentBigramLinkDataChunked as $dataChunk) {
            DB::table('vernacular_document_bigram')->insert($dataChunk);
        }
        
        //  Increment bigram frequencies.
        //  
        //  Update the frequency and document frequency for all
        //  bigrams linked to this document in a single query.
        DB::table('vernacular_document_bigram AS db')
            ->join('vernacular_bigram AS b', 'b.id', '=', 'db.bigram_id')
            ->where('db.document_id', '=', $document->id)
            ->update([
                'b.frequency' => DB::raw('b.frequency + db.frequency'),
                'b.document_frequency' => DB::raw('b.document_frequency + 1')
            ])
            ;
        return true;
    }   //  function createBigrams
}    //	class Vernacular

