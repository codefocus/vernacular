<?php

namespace Codefocus\Vernacular;

use Codefocus\Vernacular\Interfaces\TokenizerInterface;
use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Codefocus\Vernacular\Models\Source;
use Codefocus\Vernacular\Models\Word;
use Exception;
use DB;

class Vernacular
{
    protected $tokenizer;
    protected $config;
    protected static $sources = [];
    protected static $documents = [];
    

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
    }
    
    
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
    public function learnModel(Model $model) {
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
        
        DB::transaction(function() use ($model) {
            //  Lookup, load or create this Source (reference to Model).
            $className = get_class($model);
            $classBaseName = class_basename($className);
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
                    $document->save();
                }
                static::$documents[$sourceId][$modelId] = $document;
            }
            
            //  Extract tokens from each learnable attribute.
            $tokens = [];
            foreach($model->vernacularAttributes as $attribute) {
                $tokens += $this->tokenizer->tokenize($model->$attribute);
            }
            
            //  @TODO:  Filter stopwords here.
            
            if (0 == count($tokens)) {
                //  No tokens in this document.
                throw new Exception('No words found in this '.$classBaseName.'.');
            }
            //  Count occurrences of each token.
            $uniqueCountedTokens = array_count_values($tokens);
            //  Load existing Words, and create a Model instance for new Words.
            $words = Word::whereIn('word', array_keys($uniqueCountedTokens))
                ->get()
                ->keyBy('word')
                ->all();
            foreach ($uniqueCountedTokens as $token => $tokenCount) {
                if (!isset($words[$token])) {
                    $word = new Word();
                    $word->word = $token;
                    $word->soundex = soundex($token);
                    $word->frequency = 0;
                    $word->document_frequency = 0;
                    $words[$token] = $word;
                }
                //  Increase this Word's frequency by the number of occurrences.
                $words[$token]->frequency += $tokenCount;
                //  Increment this Word's document frequency.
                $words[$token]->document_frequency++;
                //  Save.
                $words[$token]->save();
            }
            //  Create raw bigrams.
            $uniqueCountedRawBigrams = $this->getBigrams($tokens, $words);
            foreach($uniqueCountedRawBigrams as $wordAId => $wordBIds) {
                foreach($wordBIds as $wordBId => $bigramFrequency) {
                    
                    
                }
            }
            
            
            dump($uniqueCountedRawBigrams);
            
            //  Load existing Bigrams, and create a Model instance for new Bigrams.
            // $bigrams = Bigram::whereIn('word', array_keys($uniqueCountedRawBigrams))
            //     ->get()
            //     ->keyBy('word');
            
            // foreach($tokens as $token) {
                
            // }
            
            //  @TODO:  if model->vernacularTags, tag document and bigrams.
            
        
        }); //  transaction
        
    }   //  function learnModel
    
    
    
    
    public function updateLearnedModel(Model $model) {
        //  @TODO
    }
    
    
    /**
     * Create raw bigrams from an array of tokens.
     * 
     * @param array $tokens
     * @param array $words
     *
     * @return array
     */
    protected function getBigrams(array $tokens, array $words)
    {
        $minDistance = (empty($this->config['word_distance']['min']) ? 1 : $this->config['word_distance']['min']);
        $minDistance = max($minDistance, 1);
        $maxDistance = (empty($this->config['word_distance']['max']) ? 1 : $this->config['word_distance']['max']);
        $maxDistance = max($minDistance, $maxDistance);
        
        
        $numTokens = count($tokens);
        $distance = 1;
        $iMaxTokenA = $numTokens - $distance;
        
        $rawBigrams = [];
        
        for ($iTokenA = 0; $iTokenA < $iMaxTokenA; ++$iTokenA) {
            $iTokenB = $iTokenA + $distance;
            //  Get the Word ids for these tokens,
            //  and combine them into a single unique lookup key.
            $wordAId = $words[$tokens[$iTokenA]]->id;
            $wordBId = $words[$tokens[$iTokenB]]->id;
            
            $lookupKey = $wordAId << 32 + $wordBId;
            if (!isset($rawBigrams[$lookupKey])) {
                $rawBigrams[$lookupKey] = [
                    'word_a_id' => $wordAId,
                    'word_b_id' => $wordBId,
                    'distances' => [],
                ];
            }
            //  Increment this bigram's frequency for the current distance.
            if (!isset($rawBigrams[$lookupKey]['distances'][$distance])) {
                $rawBigrams[$lookupKey]['distances'][$distance] = 1;
            }
            else {
                ++$rawBigrams[$lookupKey]['distances'][$distance];
            }
        }
        //  Now that we have the lookup keys,
        //  pull all known Bigrams in one query.
        $bigrams = Bigram::whereIn('lookup_key', array_keys($rawBigrams))->get();
        
        //  @TODO: Create Bigram Model instances for new bigrams.
        
        return $bigrams;
    }
    
    
    
}    //	class Vernacular

