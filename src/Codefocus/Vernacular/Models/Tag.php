<?php

namespace Codefocus\Vernacular\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    
    //  @TODO @NOTE For testing only
    use \Codefocus\Vernacular\Traits\Indexable;
    protected $indexableAttributes = ['name'];
    
    
    protected $table = 'vernacular_tag';
    protected $primaryKey = 'id';

    public $timestamps = false;
    
    
    
}    //	class Tag
