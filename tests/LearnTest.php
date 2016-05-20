<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laracasts\TestDummy\Factory;

class LearnTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseMigrations;
    
    /**
     * Test test, verifying that the factory works as intended.
     *
     * @return void
     */
    public function testExample()
    {
        //  Generate a random ImaginaryWebsite model instance.
        $website = Factory::create(ImaginaryWebsite::class);
        //  Save.
        $website->save();
        
        $this->seeInDatabase(
            'imaginary_website', [
                'id' => 1
            ]
        );
    }
}
