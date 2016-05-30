<?php

namespace Codefocus\Vernacular\Traits;

use Codefocus\Vernacular\Exceptions\VernacularException;
use Codefocus\Vernacular\Observers\ModelObserver;
use Codefocus\Vernacular\Models\Document;
use App;

trait Learnable
{
    
    private $vernacularDocument;
    
    
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
    
    
    public function getVernacularAttribute() {
        return $this->vernacularDocument;
    }

    public function setVernacularAttribute(Document $document) {
        $this->vernacularDocument = $document;
    }
    
    
    
}
