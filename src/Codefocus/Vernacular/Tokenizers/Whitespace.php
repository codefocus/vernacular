<?php namespace Codefocus\Vernacular\Tokenizers;

use Codefocus\Vernacular\Interfaces;

class Whitespace implements TokenizerInterface {
    
    
    /**
     * Extract all words from a string.
     * Unicode-safe (will catch "你的妈" as well as "lølwüt").
     * 
     * @access public
     * @param string $document
     * @return array
     */
    public static function tokenize($document) {
    //	Strip HTML tags
        $document	= strip_tags($document);
    //	Strip links
        $document	= preg_replace('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', self::TOKEN_URL, $document);
/*
    //	Strip email
        $document	= preg_replace('/[a-z0-9_\.]+@[a-z0-9-]{2,64}\.[a-z][a-z\.]{1,16}[a-z]/i', self::TOKEN_EMAIL, $document);
*/
    //	Convert to lowercase
        $document	= strtolower($document);
    //	Extract words and @users
        if (preg_match_all('/(?<!\pL|\pN)@?\pL{'.self::MIN_TOKEN_LENGTH.','.self::MAX_TOKEN_LENGTH.'}(?!\pL|\pN)/u', $document, $matches)) {
        //	Filter out @users
            $matches = array_filter($matches[0], function($match){
                if ('@' == substr($match, 0, 1)) {
                    return false;
                }
                return true;
            });
        //	Rekey the array
            return array_values($matches);
        }
        return array();
    }	//	function tokenize
    
}
