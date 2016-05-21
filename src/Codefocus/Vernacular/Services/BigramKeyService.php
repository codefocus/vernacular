<?php

namespace Codefocus\Vernacular\Services;

class BigramKeyService
{
    
    
    public static function make($wordAId, $wordBId, $distance) {
        //  The lookup key is an unsigned 64-bit integer (BIGINT in MySQL),
        //  containing both word ids and the distance.
        //  
        //  The 64 available bits are organised as follows:
        //  AAAAAAAA AAAAAAAA AAAAAAAA AAAAAAAB BBBBBBBB BBBBBBBB BBBBBBBB BBBBBBDD
        //  
        //  As you can see, 1 bit is stripped off both 32-bit integer ids
        //  to create a 2-bit space for the distance. 
        //  This makes the key 100% unique, but imposes two limitations:
        //  -   Maximum number of words is reduced to 2147483647.
        //  -   Maximum distance is 4.
        //  
        
        //  @TODO: Impose max distance when loading the config.
        
        //  @TODO: Use bcmath on 32bit systems
        
        return ($wordAId << 33) + ($wordBId << 2) + ($distance - 1);
    }
    
    
    
    public static function toArray($key, $frequency = 1, $documentFrequency = 1) {
        return [
            'lookup_key'            => $key,
            'word_a_id'             => $key >> 33,
            'word_b_id'             => ($key & 0xFFFFFFFC) >> 2,
            'distance'              => ($key & 0x03) + 1,
            'frequency'             => $frequency,
            'document_frequency'    => $documentFrequency,
        ];
    }
    
    
    
}
