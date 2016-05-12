<?php

namespace Codefocus\Vernacular\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'vernacular_document';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'source_id',
        'model_id',
        'word_count',
    ];

}    //	class Document

