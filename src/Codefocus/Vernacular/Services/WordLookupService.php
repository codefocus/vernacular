<?php

namespace Codefocus\Vernacular\Services;

use Codefocus\Vernacular\Models\Word;
use Exception;
use DB;

/**
 * Word lookup service.
 * Serves as a local memoization cache / lookup table.
 * 
 * 
 * 
 * 
 */
class WordLookupService
{
    
    private $words = [];
    
    /**
     * Return Words for the specified tokens.
     *
     */
    public function getAll(array $tokens) {
        
        $wordsLocal = [];
        $wordsStaged = [];
        $tokensRemaining = [];
        
        //  Get memoized Words.
        foreach($tokens as $token) {
            $word = $this->getLocal($token);
            if ($word) {
                $wordsLocal[$token] = $word;
            }
            else {
                $tokensRemaining[$token] = $token;
            }
        }
        
        //  Get Words from database, and memoize them.
        $wordsToMemoize = Word::whereIn('word', $tokensRemaining)
            ->get()
            ->keyBy('word')
            ->all();
        foreach($wordsToMemoize as $word) {
            //  Memoize word.
            $this->words[$word->word] = $word;
            $wordsLocal[$word->word] = $this->words[$word->word];
            unset($tokensRemaining[$word->word]);
        }
        
        //  Stage the remaining Words for creation.
        foreach($tokensRemaining as $token) {
            $wordsStaged[] = [
                'word'                  => $token,
                'soundex'               => soundex($token),
                'frequency'             => 0,
                'document_frequency'    => 0,
            ];
        }
        //  Insert new tokens into the db
        DB::table('vernacular_word')->insert($wordsStaged);
        
        //  Load the Words we just created from the DB, to get the ids.
        $wordsToMemoize = Word::whereIn('word', $tokensRemaining)
            ->get()
            ->keyBy('word')
            ->all();
        foreach($wordsToMemoize as $word) {
            //  Memoize word.
            $this->words[$word->word] = $word;
            $wordsLocal[$word->word] = $this->words[$word->word];
            unset($tokensRemaining[$word->word]);
        }
        return $wordsLocal;
    }
    
    /**
     * Return a Word if locally memoized. 
     *
     *
     */
    public function getLocal($token) {
        if (isset($this->words[$token])) {
            return $this->words[$token];
        }
        if (isset($this->wordsStaged[$token])) {
            return $this->wordsStaged[$token];
        }
        return false;
    }
    
    
    
    
}
