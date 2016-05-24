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
     * Test learning a model.
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
    public function testLearnModel()
    {
        //  Generate a random ImaginaryWebsite model instance.
        $website = Factory::create(ImaginaryWebsite::class);
        //  Save.
        $website->save();
        
        //  Test that the Source was created.
        $this->seeInDatabase(
            'vernacular_source', [
                'id' => 1
            ]
        );
        //  Test that the Document was created.
        $this->seeInDatabase(
            'vernacular_document', [
                'id' => 1
            ]
        );
        //  Test that at least one Bigram was created.
        $this->seeInDatabase(
            'vernacular_bigram', [
                'id' => 1
            ]
        );
        //  Test that at least one Bigram was linked to the Document.
        $this->seeInDatabase(
            'vernacular_document_bigram', [
                'bigram_id' => 1
            ]
        );
    }
    
    
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
        //  Generate random ImaginaryWebsite model instances.
        $numWebsites = 5;
        $websites = [];
        for ($iWebsite = 0; $iWebsite < $numWebsites; ++$iWebsite) {
            $websites[] = Factory::create(ImaginaryWebsite::class);
        }
        
        //  Save.
        foreach ($websites as $website) {
            $website->save();
        }
        
        
        //  @NOTE:  Fairly useless assertions currently.
        $this->seeInDatabase(
            'imaginary_website', [
                'id' => 1
            ]
        );
    }
}
