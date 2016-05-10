<?php namespace Codefocus\Vernacular;

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
        dump($words);
        //  @TODO:  Verify that we have words.
        
        //$bigrams    = [];
        
    }
        
    
}	//	class Vernacular
