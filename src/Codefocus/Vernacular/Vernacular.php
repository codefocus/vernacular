<?php namespace Codefocus\Vernacular;


class Vernacular {
    
    
    /**
     * Index a document
     *
     */
    public function learn($document, array $tags = []) {
        
        $words      = $this->tokenize($document);
        
        //  @TODO:  Verify that we hav words.
        
        $bigrams    = [];
        
    }
    
    
    
    
}	//	class Vernacular
