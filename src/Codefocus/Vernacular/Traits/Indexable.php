<?php

namespace Codefocus\Vernacular\Traits;

use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Observers\ModelObserver;
use App;

trait Indexable
{
    protected static $vernacular;
    protected static $observer;
    
    
    public function __construct() {
        static $observedModels = [];
        
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
        
        //  Observe created and updated events for each different Model class.
        if (!isset($observedModels[$className])) {
            if (!isset(static::$observer)) {
                static::$observer = new ModelObserver;
            }
            $observedModels[$className] = true;
            static::observe(static::$observer);
        }
        
    }
    
}
