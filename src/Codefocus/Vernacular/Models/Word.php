<?php

namespace Codefocus\Vernacular\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'vernacular_word';
    protected $primaryKey = 'id';

    public $timestamps = false;

    public function __construct()
    {
        //echo 'Codefocus/Vernacular/Word.';
    }
}    //	class Word

