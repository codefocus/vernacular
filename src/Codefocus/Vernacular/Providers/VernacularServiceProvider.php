<?php

namespace Codefocus\Vernacular\Providers;

use Codefocus\Vernacular\Vernacular;
use App;

class VernacularServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../../../publish/config/vernacular.php' => config_path('vernacular.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../../../../publish/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        App::singleton('vernacular', function () {
            return new Vernacular(config('vernacular'));
        });
    }
}    //	class VernacularServiceProvider

