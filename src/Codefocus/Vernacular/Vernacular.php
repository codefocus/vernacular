<?php

namespace Codefocus\Vernacular;

use Codefocus\Vernacular\Interfaces\TokenizerInterface;
use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Codefocus\Vernacular\Models\Source;
use Codefocus\Vernacular\Models\Word;

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
     *
     * @return boolean
     */
    public function learnModel(Model $model) {
        //  Ensure this Model exists in the database.
        if (!$model->exists()) {
            throw new VernacularException('Cannot learn an unsaved Model.');
        }
        //  Ensure all required attributes are set and valid.
        if (!isset($model->learnableAttributes)) {
            throw new VernacularException('Required $learnableAttributes not defined.');
        }
        if (!is_array($model->learnableAttributes)) {
            throw new VernacularException('$learnableAttributes should be an array.');
        }
        if (0 == count($model->learnableAttributes)) {
            //  No learnable attributes specified.
            //  Nothing to learn.
            return false;
        }
        
        DB::transaction(function() use ($model) {
            //  Lookup, load or create this Source (Model).
            $className = get_class($model);
            $classBaseName = class_basename($className);
            if (!isset(static::$sources[$className])) {
                static::$sources[$className] = Source::firstOrCreate(['model_class' => $className]);
            }
            //  Lookup, load or create this Document (Model instance).
            $sourceId = static::$sources[$className];
            $modelId = $model->id;
            if (!isset(static::$documents[$sourceId]) or !isset(static::$documents[$sourceId][$modelId])) {
                static::$documents[$sourceId][$modelId] = Document::firstOrCreate([
                    'source_id' => $sourceId,
                    'model_id' => $modelId,
                ]);
            }
            //  Extract tokens from each learnable attribute.
            $tokens = [];
            foreach($model->learnableAttributes as $attribute) {
                $tokens += $this->tokenizer->tokenize($model->attribute);
            }
            if (0 == count($tokens)) {
                //  No tokens in this document.
                throw new Exception('No words found in this '.$classBaseName.'.');
            }
            //  Count occurrences of each token.
            $uniqueCountedTokens = array_count_values($tokens);
            //  Load existing Words, and create a Model instance for new Words.
            $words = Word::whereIn('word', array_keys($uniqueCountedTokens))
                ->get()
                ->keyBy('word');
            foreach ($uniqueCountedTokens as $token => $tokenCount) {
                if (!isset($words[$token])) {
                    $word = new Word([
                        'word' => $token,
                        'soundex' => soundex($token),
                        'frequency' => 0,
                        'document_frequency' => 0,
                    ]);
                    $words[$token] = $word;
                }
                //  Increase this Word's frequency by the number of occurrences.
                $words[$token]->frequency += $tokenCount;
                //  Increment this Word's document frequency.
                $words[$token]->document_frequency++;
                //  Save.
                $words[$token]->save();
            }
            
            //  @TODO:  create bigrams.
            
            //  @TODO:  if model->vernacularTags, tag document and bigrams.
            
        
        }); //  transaction
        
    }   //  function learnModel
    
    
    
    
    public function updateLearnedModel(Model $model) {
        //  @TODO
    }
    
    
    
    protected function getBigrams()
    {
        //  @TODO:  
        
    }
    
    
    
}    //	class Vernacular

