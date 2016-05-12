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
        
        //  vernacularTags
        
        
        foreach($model->learnableAttributes as $attribute) {
            $this->learnModelAttribute($model, $attribute);
        }
        
    }
    
    /**
     * Index an attribute of an Eloquent Model.
     * 
     * @param Model $model
     * @param string $attribute
     *
     * @throws VernacularException
     *
     * @return boolean
     */
    public function learnModelAttribute(Model $model, $attribute) {
        if (!is_string($attribute)) {
            throw new VernacularException('The $attribute parameter should be a string.');
        }
        if (!$model->exists()) {
            throw new VernacularException('Cannot learn an unsaved Model.');
        }
        
        //  Lookup, load or create this Source (model/attribute).
        $className = get_class($model);
        if (!isset(static::$sources[$className]) or !isset(static::$sources[$className][$attribute])) {
            static::$sources[$className][$attribute] = Source::firstOrCreate([
                'model_class' => $className,
                'attribute' => $attribute,
            ]);
        }
        
        //  Lookup, load or create this Document.
        $sourceId = $sources[$className][$attribute];
        $modelId = $model->id;
        if (!isset(static::$documents[$sourceId]) or !isset(static::$documents[$sourceId][$modelId])) {
            static::$documents[$sourceId][$modelId] = Document::firstOrCreate([
                'source_id' => $sourceId,
                'model_id' => $modelId,
            ]);
        }
        
        
        
        //  static::$sources[$className][$attribute]
        
        
    }
    
    
    
    public function updateLearnedModel(Model $model) {
        //  @TODO
    }
    
    
    

    /**
     * Index a document.
     */
    public function learn($document, array $tags = [])
    {
        //  Load / create document identifier.
        //  @TODO:  This happens before learn().
        //          learn() should take a DocumentIdentifier and text.

        //  Extract words.
        $words = $this->tokenizer->tokenize($document);
        if (!$words) {
            //  No words in this document.
            return false;
        }
        
        
        //$vernacularWords;
        $wordsUnique = array_count_values($words);
        
        //  @TODO: Move to separate function.
        //DB::transaction(function() use ($words, $vernacularWords) {
            //  Load existing Words, and
            //  prep a model instance for each new Word.

            $vernacularWords    = Word::whereIn('word', array_keys($wordsUnique))
                                      ->get()
                                      ->keyBy('word');
        foreach ($wordsUnique as $word => $wordOccurrences) {
            if (!isset($vernacularWords[$word])) {
                $vernacularWords[$word] = new Word();
                $vernacularWords[$word]->word = $word;
                $vernacularWords[$word]->soundex = soundex($word);
                $vernacularWords[$word]->frequency = 0;
                $vernacularWords[$word]->document_frequency = 0;
            }
                //  Increase this Word's frequency by the number of occurrences.
                $vernacularWords[$word]->frequency += $wordOccurrences;
                //  Increment this Word's document frequency.
                $vernacularWords[$word]->document_frequency++;
                //  Save.
                $vernacularWords[$word]->save();
        }
        //});

        
        //$bigrams    = [];
    }   //  function learn

    
    protected function getBigrams()
    {
    }
}    //	class Vernacular

