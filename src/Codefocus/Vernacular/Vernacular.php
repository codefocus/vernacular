<?php

namespace Codefocus\Vernacular;

use Codefocus\Vernacular\Interfaces\TokenizerInterface;
use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Models\Word;
use DB;

class Vernacular
{
    protected $tokenizer;
    protected $config;

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
    
    
    protected function getBigrams() {
        
    }
    
    
    
}    //	class Vernacular

