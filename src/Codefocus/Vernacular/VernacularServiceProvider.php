<?php namespace Codefocus\Vernacular;

class VernacularServiceProvider extends \Illuminate\Support\ServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;
    
    
    /**
    * Perform post-registration booting of services.
    *
    * @return void
    */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../publish/config/vernacular.php' => config_path('vernacular.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../publish/migrations/' => database_path('migrations')
        ], 'migrations');
    }
    
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        App::bind('vernacular', function()
        {
            return new Vernacular;
        });
    }
    
}	//	class VernacularServiceProvider
