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
	    //
	}
	
	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function register()
	{
		echo 'VernacularServiceProvider.Register';
		$this->app->bind('Vernacular', function($app)
		{
			return new Vernacular;
		});
	}
	
}	//	class VernacularServiceProvider
