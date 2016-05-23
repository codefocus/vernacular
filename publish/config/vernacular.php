<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Word distance
    |--------------------------------------------------------------------------
    |
    | Specifies which bigrams (combinations of two words) are stored.
    | A distance of "1" means that the two words in the bigram directly follow
    | each other. A distance of "2" means there is one word in between, etc.
    |
    | Example: "chocolate covered walnuts"
    |     Bigrams stored for distance "1":
    |         - chocolate covered
    |         - covered walnuts
    |     Bigrams stored for distance "2":
    |         - chocolate walnuts
    |     Bigrams stored for distance "3":
    |         none
    |
    */

    'word_distance' => [
        'min' => 1,
        'max' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Word length
    |--------------------------------------------------------------------------
    |
    | Specifies the minimum and maximum length of words to include.
    | Longer and shorter words are ignored.
    |
    */

    'word_length' => [
        'min' => 1,
        'max' => 16,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Rows per query
    |--------------------------------------------------------------------------
    |
    | Vernacular combines multiple INSERT and UPDATE statements into a single
    | query. Because most database engines limit the number of variables per
    | statement, these statements are chunked.
    | This setting specifies the maximum number of rows per INSERT or UPDATE
    | statement. 
    |
    */

    'max_rows_per_query' => 128,

    /*
    |--------------------------------------------------------------------------
    | Stopwords
    |--------------------------------------------------------------------------
    |
    | Ignore frequently used words that do not add significant information.
    | 
    | Supported: true, false, <filename>
    |
    */

    'stopwords' => true,

    /*
    |--------------------------------------------------------------------------
    | Tokenizer
    |--------------------------------------------------------------------------
    | 
    |
    */

    'tokenizer' => \Codefocus\Vernacular\Tokenizers\Whitespace::class,

];
