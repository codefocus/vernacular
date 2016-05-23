<?php

namespace Codefocus\Vernacular\Tokenizers;

use Codefocus\Vernacular\Interfaces\TokenizerInterface;
use Config;

class Whitespace implements TokenizerInterface
{
    const REGEX_LOOKBEHIND_NO_ALPHANUMERIC = '(?<!\pL|\pN)';
    const REGEX_LOOKAHEAD_NO_ALPHANUMERIC = '(?!\pL|\pN)';
    const REGEX_ALPHANUMERIC = '[\pL\pN]';
    
    const REGEX_APOSTROPHE_SUFFIXES = '(?:[\'`‘’]\pL{1,3})';
    
    const REGEX_ALPHA = '\pL';

    protected $minWordLength;
    protected $maxWordLength;

    public function __construct()
    {
        $this->minWordLength = Config::get('vernacular.word_length.min', 1);
        $this->maxWordLength = Config::get('vernacular.word_length.max', 16);
    }

    /**
     * Extract all words from a string.
     * Unicode-safe (will catch "你的妈" as well as "lølwüt").
     * 
     * @param string $document
     *
     * @return array
     */
    public function tokenize($document)
    {
        //	Strip HTML tags
        //  
        //  @NOTE:  These are not a tokenizer tasks.
        //          Tokenizer expects text.
        //  
        //  $document	= strip_tags($document);
        //	Strip links
        //  $document	= preg_replace('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', self::TOKEN_URL, $document);
        //	Strip email
        //  $document	= preg_replace('/[a-z0-9_\.]+@[a-z0-9-]{2,64}\.[a-z][a-z\.]{1,16}[a-z]/i', self::TOKEN_EMAIL, $document);

        //	Convert to lowercase
        $document = mb_strtolower($document);
        
        
        //  Extract tokens.
        $regex = self::REGEX_LOOKBEHIND_NO_ALPHANUMERIC.
                      self::REGEX_ALPHANUMERIC.'{'.$this->minWordLength.','.$this->maxWordLength.'}'.
                      self::REGEX_APOSTROPHE_SUFFIXES.'?'.
                      self::REGEX_LOOKAHEAD_NO_ALPHANUMERIC;

        // //  Extract tokens.
        // $regex = self::REGEX_LOOKBEHIND_NO_ALPHANUMERIC.
        //               self::REGEX_ALPHA.'{'.$this->minWordLength.','.$this->maxWordLength.'}'.
        //               self::REGEX_LOOKAHEAD_NO_ALPHANUMERIC;
        if (false === preg_match_all('/'.$regex.'/u', $document, $matches)) {
            //  No acceptable tokens found in this document.
            return false;
        }

        //  Return found tokens.
        return $matches[0];
    }    //	function tokenize
}
