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
        'max' => 2,
    ],
    
    
    
    
    

];
