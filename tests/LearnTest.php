<?php

//use Codefocus\Vernacular;

class LearnTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	public function setUp()
	{
		//	Set up
	}

	/**
	 * Clean up the testing environment before the next test.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		//	Tear down
	}

	/**
	 * Test whether I am learning the words in a document correctly.
	 *
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
