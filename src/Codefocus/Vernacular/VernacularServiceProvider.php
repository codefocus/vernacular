<?php namespace Codefocus\Vernacular;

class VernacularServiceProvider extends \Illuminate\Support\ServiceProvider {
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;
	
	
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
