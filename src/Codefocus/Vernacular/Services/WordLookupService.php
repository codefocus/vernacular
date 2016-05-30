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
    public function getAll(array $tokens)
    {
        $wordsLocal = [];
        $tokensRemaining = [];
        
        //  Get memoized Words.
        foreach ($tokens as $token) {
            $id = $this->getLocal($token);
            if ($id) {
                $wordsLocal[$token] = $id;
            } else {
                $tokensRemaining[$token] = $token;
            }
        }
        
        if (0 === count($tokensRemaining)) {
            return $wordsLocal;
        }
        
        //  Get Words from database, and memoize them.
        $wordsToMemoize = Word::whereIn('word', $tokensRemaining)
            ->get()
            ->keyBy('word')
            ->all();
            
        //  Fetch known Words in one query, and memoize them.
        $wordsToMemoize = DB::table('vernacular_word')
            ->select('word', 'id')
            ->whereIn('word', $tokensRemaining)
            ->pluck('id', 'word')
            ;
        foreach ($wordsToMemoize as $word => $id) {
            //  Memoize word.
            $this->words[$word] = $id;
            $wordsLocal[$word] = $id;
            unset($tokensRemaining[$word]);
        }
        
        if (0 === count($tokensRemaining)) {
            return $wordsLocal;
        }
        
        //  Stage the remaining Words for creation.
        $wordsStaged = [];
        foreach ($tokensRemaining as $token) {
            $wordsStaged[] = [
                'word'                  => $token,
                'soundex'               => soundex($token),
                'frequency'             => 0,
                'document_frequency'    => 0,
            ];
        }
        //  Insert new Words into the db
        DB::table('vernacular_word')->insert($wordsStaged);
        //  Load the Words we just created from the DB, to get the ids.
        $wordsToMemoize = DB::table('vernacular_word')
            ->select('word', 'id')
            ->whereIn('word', $tokensRemaining)
            ->pluck('id', 'word')
            ;
        foreach ($wordsToMemoize as $word => $id) {
            //  Memoize word.
            $this->words[$word] = $id;
            $wordsLocal[$word] = $id;
        }

        return $wordsLocal;
    }
    
    /**
     * Return a Word if locally memoized. 
     *
     *
     */
    public function getLocal($token)
    {
        if (isset($this->words[$token])) {
            return $this->words[$token];
        }
        if (isset($this->wordsStaged[$token])) {
            return $this->wordsStaged[$token];
        }
        return false;
    }
}
