<?php namespace Codefocus\Vernacular;

use Illuminate\Database\Eloquent\Model;

class Bigram extends Model
{
    protected $table = 'vernacular_bigram';
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
    
}    //	class Bigram

