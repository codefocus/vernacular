<?php

namespace Codefocus\Vernacular\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'vernacular_source';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'model_class',
        'attribute',
    ];
}    //	class Source
