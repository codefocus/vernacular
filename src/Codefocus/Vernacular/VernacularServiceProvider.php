<?php

namespace Codefocus\Vernacular;

class VernacularServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        //  @TODO:  issue #6: Extend default config.
        $this->publishes([
            __DIR__.'/../publish/config/vernacular.php' => config_path('vernacular.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../publish/migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        App::bind('vernacular', function () {
            return new \Codefocus\Vernacular\Vernacular(config('vernacular'));
        });
    }
}    //	class VernacularServiceProvider

