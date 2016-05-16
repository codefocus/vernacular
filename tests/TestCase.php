<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $app->register('Codefocus\Vernacular\Providers\VernacularServiceProvider');
        return $app;
    }
    
    /**
     * Setup DB before each test.
     *
     * @return void  
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default','sqlite'); 
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->migrate();
    }
    
    /**
     * run package database migrations
     *
     * @return void
     */
    public function migrate()
    {
        Artisan::call('migrate', [
            '--path' => '../../../publish/migrations'
        ]);
    }

}
