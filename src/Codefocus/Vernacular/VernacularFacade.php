<?php

namespace Codefocus\Vernacular;

use Illuminate\Support\Facades\Facade;

class VernacularFacade extends Facade
{
    /**
     * Get the registered name of the component. This tells $this->app what record to return.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Vernacular';
    }
}
