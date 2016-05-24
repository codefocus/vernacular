<?php

namespace Codefocus\Vernacular\Models;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    //  Implementing the "Learnable" trait triggers Vernacular to
    //  index the content of the attributes specified in $vernacularAttributes.
    use \Codefocus\Vernacular\Traits\Learnable;
    public $vernacularAttributes = ['content'];
    //public $vernacularTags = ['positive'];

    protected $table = 'vernacular_url';
    protected $primaryKey = 'id';

    public $timestamps = false;
}    //	class Url

