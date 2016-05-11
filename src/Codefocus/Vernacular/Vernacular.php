<?php namespace Codefocus\Vernacular;

use Codefocus\Vernacular\Models\Word;
use Config;

class Vernacular {
    
    private $tokenizer;
    
    
    public function __construct() {
        //  Instantiate configured tokenizer.
        $tokenizerClass = Config::get('vernacular.tokenizer', \Codefocus\Vernacular\Tokenizers\Whitespace::class);
        $this->tokenizer = new $tokenizerClass;
    }
    
    
    
    /**
     * Index a document
     *
     */
    public function learn($document, array $tags = []) {
        //  Extract words.
        $words = $this->tokenizer->tokenize($document);
        if (!$words) {
            //  No words in this document.
            return false;
        }
        
        //  @TODO: Wrap this in a transaction.
        
        //  Load existing Words, and
        //  prep a model instance for each new Word.
        $wordsUnique        = array_unique($words);
        $vernacularWords    = Word::whereIn('word', $words)->get()->keyBy('word');
        foreach($wordsUnique as $word) {
            if (!isset($vernacularWords[$word])) {
                $vernacularWords[$word] = new Word;
                $vernacularWords[$word]->word               = $word;
                $vernacularWords[$word]->soundex            = soundex($word);
                $vernacularWords[$word]->frequency          = 0;
                $vernacularWords[$word]->document_frequency = 0;
            }
        }
        
        //  Increase the frequency of each word by the number of occurrences.
        //  @TODO
        
        //  Save Words.
        //  @TODO
        
        
        
        //$bigrams    = [];
        
    }
        
    
}	//	class Vernacular
