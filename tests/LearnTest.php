<?php


class LearnTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        echo "Setting up database connection...\n";
        $capsule = new Illuminate\Database\Capsule\Manager();

        $capsule->addConnection(array(
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'test',
            'username' => 'test',
            'password' => 'l4m3p455w0rd!',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ));

        $capsule->bootEloquent();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    public function tearDown()
    {
        if ($this->app) {
            $this->app->flush();
        }
    }

    /**
     * Test whether I am learning the words in a document correctly.
     */
    public function testLearnDocument()
    {
        //$stringy = new Stringy\Stringy();
        //echo '<pre style="text-align: left; background-color: #fff; color: #444; border: 1px solid #ccc;">'.print_r($stringy, true).'</pre>';
        $word = new Codefocus\Vernacular\Word();
        $word->save();

/*
        $loggedInUserId = 1;
        Auth::loginUsingId($loggedInUserId);
        $response = $this->call('GET', '/user/');
        $this->assertRedirectedTo('/user/'.$loggedInUserId);
*/
    }
}
