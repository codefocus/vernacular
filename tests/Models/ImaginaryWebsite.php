<?php

use Illuminate\Database\Eloquent\Model;

class ImaginaryWebsite extends Model
{
    //  Implementing the "Learnable" trait triggers Vernacular to
    //  index the content of the attributes specified in $vernacularAttributes.
    use \Codefocus\Vernacular\Traits\Learnable;
    public $vernacularAttributes = ['content'];
    //public $vernacularTags = ['positive'];

    
    protected $table = 'imaginary_website';
    protected $primaryKey = 'id';

    public $timestamps = false;
}    //	class ImaginaryWebsite

