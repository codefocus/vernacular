<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LearnTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseMigrations;
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        //  Simple test to ensure the unit testing environment is set up correctly.
        $tag = new \Codefocus\Vernacular\Models\Tag;
        $tag->name = 'Save in test database';
        $tag->save();
        
        $this->seeInDatabase(
            'vernacular_tag', [
                'id' => 1
            ]
        );
        
    }
}
