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
    | Note: Indexing more distances (and thus more bigrams) will increase
    |       processing time, memory, and storage requirements.
    |       Only indexing adjecent words (distance 1) is more than adequate
    |       for most use cases.
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

    'max_rows_per_query' => 1200,
    
    'filters' => [
        /*
        |--------------------------------------------------------------------------
        | Filter stopwords
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
        | Filter URLs
        |--------------------------------------------------------------------------
        |
        | Whether to ignore urls when parsing a document.
        | 
        | Supported: true, false
        |
        */

        'urls' => true,
        
        /*
        |--------------------------------------------------------------------------
        | Filter HTML tags
        |--------------------------------------------------------------------------
        |
        | Whether to ignore HTML tags when parsing a document.
        | 
        | Supported: true, false
        |
        */

        'html' => true,
        
        /*
        |--------------------------------------------------------------------------
        | Filter numbers
        |--------------------------------------------------------------------------
        |
        | Whether to ignore numbers or treat them as words.
        | 
        | Supported: true (filter numbers), false (treat numbers as words)
        |
        */

        'numbers' => true,
    
    ],

    /*
    |--------------------------------------------------------------------------
    | Tokenizer
    |--------------------------------------------------------------------------
    | 
    |
    */

    'tokenizer' => \Codefocus\Vernacular\Tokenizers\Whitespace::class,
    
    'urls' => [
        
        /*
        |--------------------------------------------------------------------------
        | User Agent
        |--------------------------------------------------------------------------
        |
        | The user-agent header sent when fetching a remote url.
        |
        */
        
        'user_agent' => 'Vernacular/1.0',
        
        /*
        |--------------------------------------------------------------------------
        | Accept content types
        |--------------------------------------------------------------------------
        |
        | The content types to accept when fetching a remote url.
        |
        */
        
        'accept' => [
            'text/plain',
            'text/html',
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Maximum content length
        |--------------------------------------------------------------------------
        |
        | When fetching a remote url, reject documents larger than this size,
        | specified in bytes.
        |
        */
        
        'max_content_length' => 500000,
    
    ]
    

];
