<?php

namespace Codefocus\Vernacular\Services;

use Codefocus\Vernacular\Models\Bigram;
use Exception;
use DB;

/**
 * Bigram lookup service.
 * Serves as a local memoization cache / lookup table.
 * 
 * 
 * 
 * 
 */
class BigramLookupService
{
    
    private $bigrams = [];
    
    /**
     * Return Bigrams for the specified lookup tokens.
     *
     */
    public function getAll(array $tokens) {
        
        $bigramsLocal = [];
        $bigramsStaged = [];
        $tokensRemaining = [];
        
        //  Get memoized Bigrams.
        foreach($tokens as $token) {
            $bigram = $this->getLocal($token);
            if ($bigram) {
                $bigramsLocal[$token] = $bigram;
            }
            else {
                $tokensRemaining[$token] = $token;
            }
        }
        
        //  Fetch known Bigrams in one query, and memoize them.
        //  
        //  Using whereRaw reduces a 2300ms query to 600ms,
        //  when processing a 6000-bigram document.
        $existingBigrams = DB::table('vernacular_bigram')
            ->select('lookup_key', 'id')
            ->whereRaw('lookup_key IN ('.implode(', ', $tokensRemaining).')')
            ->pluck('id', 'lookup_key')
            ;
        foreach($existingBigrams as $lookupKey => $id) {
            //  Memoize Bigram.
            $this->bigrams[$lookupKey] = $id;
            $bigramsLocal[$lookupKey] = $id;
            unset($tokensRemaining[$lookupKey]);
        }
        //  Stage the remaining Bigrams for creation.
        if (count($tokensRemaining)) {
            //  Create bigrams that do not yet exist in the DB.
            $bigramDataToInsert = [];
            foreach($tokensRemaining as $tokenToCreate) {
                $bigramDataToInsert[] = BigramKeyService::toArray($tokenToCreate, 0);
            }
            //  Split into multiple insert statements,
            //  to prevent "Too many SQL variables" exception.
            //  
            //  Insert new bigrams into the database.
            $maxRowsPerQuery = config('vernacular.max_rows_per_query');
            $chunkedBigramDataToInsert = array_chunk($bigramDataToInsert, $maxRowsPerQuery);
            foreach ($chunkedBigramDataToInsert as $bigramDataChunk) {
                //  Insert this chunk of new Bigrams.
                DB::table('vernacular_bigram')->insert($bigramDataChunk);
            }
            unset($chunkedBigramDataToInsert);
            unset($bigramDataChunk);
            //  Load the Bigrams we just created from the DB, to get the ids.
            $bigramsToMemoize = DB::table('vernacular_bigram')
                ->select('lookup_key', 'id')
                ->whereRaw('lookup_key IN ('.implode(', ', $tokensRemaining).')')
                ->pluck('id', 'lookup_key')
                ;
            foreach($bigramsToMemoize as $lookupKey => $id) {
                //  Memoize Bigram.
                $this->bigrams[$lookupKey] = $id;
                $bigramsLocal[$lookupKey] = $id;
            }
            unset($tokensRemaining);
        }
        return $bigramsLocal;
    }
    
    /**
     * Return a Bigram if locally memoized. 
     *
     *
     */
    public function getLocal($token) {
        if (isset($this->bigrams[$token])) {
            return $this->bigrams[$token];
        }
        return false;
    }
    
    
    
    
}
