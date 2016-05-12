<?php

namespace Codefocus\Vernacular\Traits;

use Codefocus\Vernacular\Exceptions\VernacularException;
use App;

trait Indexable
{
    protected static $vernacular;
    
    public function __construct() {
        static $listeningToModels = [];
        
        //  Ensure all required attributes are set and valid.
        if (!isset($this->indexableAttributes)) {
            throw new VernacularException('Required $indexableAttributes not defined.');
        }
        if (!is_array($this->indexableAttributes)) {
            throw new VernacularException('$indexableAttributes should be an array.');
        }
        
        //  Get the full name of the class that implements me.
        $className = static::class;
        
        //  Instantiate a Vernacular singleton.
        if (!static::$vernacular) {
            static::$vernacular = App::make('vernacular');
        }
        
        if (!isset($listeningToModels[$className])) {
            $listeningToModels[$className] = true;
            //  Listen to database events for this Model.
            static::created(function($model) {
                
                dump('TRAIT: created a '.class_basename($model));
                $model::$vernacular->learn($model);
                //dump($this->indexableAttributes);
            });
            static::updated(function($model) {
                dump('TRAIT: updated a '.class_basename($model));
            });
        }
        
    }
    
    
    
}

//  $t = new \Codefocus\Vernacular\Models\Tag;