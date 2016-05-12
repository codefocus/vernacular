<?php

namespace Codefocus\Vernacular\Traits;

use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Observers\ModelObserver;
use App;

trait Learnable
{
    public function __construct()
    {
        static $observedModels = [];
        
        //  Get the full name of the class that implements me.
        $className = static::class;
        
        //  Observe created and updated events for each different Model class.
        if (!isset($observedModels[$className])) {
            $observedModels[$className] = true;
            static::observe(new ModelObserver);
        }
    }
}
