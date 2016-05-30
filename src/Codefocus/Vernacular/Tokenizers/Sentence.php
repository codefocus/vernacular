<?php

namespace Codefocus\Vernacular\Tokenizers;

use Codefocus\Vernacular\Interfaces\TokenizerInterface;
use Config;

class Sentence implements TokenizerInterface
{
    /**
     * Extract all sentences from a string.
     * 
     * @param string $document
     *
     * @return array
     */
    public function tokenize($document)
    {
        return preg_split(
            '/(?<=[.?!;:])\s+/',
            $document,
            -1,
            PREG_SPLIT_NO_EMPTY
        );
    }    //	function tokenize
}
