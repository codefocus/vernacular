<?php

namespace Codefocus\Vernacular\Observers;

use Illuminate\Database\Eloquent\Model;
use Codefocus\Vernacular\Vernacular;


class ModelObserver
{
    
    /**
     * One ModelObserver instance is created for each different
     * Model class that is observed.
     * 
     */
    public function __construct() {
        dump('ModelObserver created.');
    }
    
    
    public function created(Model $model) {
        dump('ModelObserver::created '.get_class($model));
        
    }
    
    
    public function updated(Model $model) {
        dump('ModelObserver::updated '.get_class($model));
        
    }
    
    
}

