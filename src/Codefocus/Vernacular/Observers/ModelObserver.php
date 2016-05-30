<?php

namespace Codefocus\Vernacular\Observers;

use Illuminate\Database\Eloquent\Model;
use Codefocus\Vernacular\Vernacular;
use App;

class ModelObserver
{
    protected static $vernacular;
    
    
    public function __construct()
    {
        //  Instantiate a Vernacular singleton.
        if (!static::$vernacular) {
            static::$vernacular = App::make('vernacular');
        }
    }
    
    
    public function created(Model $model)
    {
        $model->vernacular = static::$vernacular->learnModel($model);
    }
    
    
    public function updated(Model $model)
    {
        $model->vernacular = static::$vernacular->updateLearnedModel($model);
    }
}
