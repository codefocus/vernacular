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
     * @covers \Codefocus\Vernacular\Traits\Learnable
     * @covers \Codefocus\Vernacular\Observers\ModelObserver
     * @covers \Codefocus\Vernacular\Providers\VernacularServiceProvider
     * @covers \Codefocus\Vernacular\Vernacular
     * @covers \Codefocus\Vernacular\Tokenizers\Whitespace
     * @covers \Codefocus\Vernacular\Models\Source
     * @covers \Codefocus\Vernacular\Models\Document
     * @covers \Codefocus\Vernacular\Models\Word
     * @covers \Codefocus\Vernacular\Models\Bigram
     * @covers \Codefocus\Vernacular\Services\BigramKeyService
     * @return void
     */
    public function testExample()
    {
        //  Generate a random ImaginaryWebsite model instance.
        $website = Factory::create(ImaginaryWebsite::class);
        //  Save.
        $website->save();
        
        //  @NOTE:  Fairly useless assertions currently.
        $this->seeInDatabase(
            'imaginary_website', [
                'id' => 1
            ]
        );
        $this->assertEquals($website->id, 1);
    }
}
