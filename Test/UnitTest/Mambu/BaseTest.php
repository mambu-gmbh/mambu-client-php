<?php

require_once '../Mambu/Client.php';

/**
 * Unit test for the Base class of the Mambu PHP API wrapper
 */
class UnitTest_Mambu_BaseTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tests retrieval of a resource
	 *
	 * @covers Mambu_Base::get
	 */
	public function testGet(){
		
		try {
			$client = new Mambu_Client();
			$client->get();
			$this->fail('Exception should be thrown when trying to get client '.
				'without providing an ID');
		} catch (Exception $e){
			// ignore
		}

		try {
			$client = new Mambu_Client();
			$client->get(null);
			$this->fail('Exception should be thrown when trying to get client '.
				'without providing an ID');
		} catch (Exception $e){
			// ignore
		}
	}

	/**
	 * Tests parsing of JSON strings (single resource)
	 *
	 * @covers Mambu_Base::parseResult
	 */
	public function testParseResult(){
		$client = new Mambu_Client();
		$jsonResult = '{"encodedKey":"8a56f203337f480e013388c697db040e",'.
			'"id":"502848759","creationDate":"2011-11-09T14:41:16+0000",'.
			'"lastModifiedDate":"2011-11-09T14:41:54+0000",'.
			'"firstName":"Claudia","lastName":"Mustermann",'.
			'"homePhone":"987 654321","mobilePhone1":"123 456789",'.
			'"birthDate":"1990-11-01T00:00:00+0000","gender":"FEMALE",'.
			'"loanCycle":0,"groupLoanCycle":0}';
		Mambu_Base::parseResult($client, $jsonResult);
		$this->assertEquals('8a56f203337f480e013388c697db040e',
			$client->encodedKey);

		$clients[] = array();
		$jsonResult = '['.
						'{"encodedKey":"8a56f203337f480e013388c697db040e",'.
							'"id":"502848759",'.
							'"creationDate":"2011-11-09T14:41:16+0000",'.
							'"lastModifiedDate":"2011-11-09T14:41:54+0000",'.
							'"firstName":"Claudia","lastName":"Mustermann",'.
							'"homePhone":"987 654321",'.
							'"mobilePhone1":"123 456789",'.
							'"birthDate":"1990-11-01T00:00:00+0000",'.
							'"gender":"FEMALE","loanCycle":0,'.
							'"groupLoanCycle":0},'.
						'{"encodedKey":"8a56f203337f480e013388c697db040f",'.
							'"id":"502848759",'.
							'"creationDate":"2011-11-09T14:41:16+0000",'.
							'"lastModifiedDate":"2011-11-09T14:41:54+0000",'.
							'"firstName":"Claudia","lastName":"Mustermann",'.
							'"homePhone":"987 654321",'.
							'"mobilePhone1":"123 456789",'.
							'"birthDate":"1990-11-01T00:00:00+0000",'.
							'"gender":"FEMALE","loanCycle":0,'.
							'"groupLoanCycle":0}'.
						']';
		$clientsResult = Mambu_Base::parseResult($clients, $jsonResult);
		$this->assertEquals(2, count($clientsResult));

		try{
			Mambu_Base::parseResult(new stdClass, $jsonResult);
			$this->fail('Exception should be thrown, because first parameter '.
				'is not an array or object of Mambu_Base');
		} catch (Exception $e){
			// ignore
		}
	}

	/**
	 * Tests parsing of JSON strings (resource collection)
	 *
	 * @covers Mambu_Base::parseSubResult
	 */
	public function testParseSubResult(){
		$clients[] = array();
		$jsonResult = '['.
						'{"encodedKey":"8a56f203337f480e013388c697db040e",'.
							'"id":"502848759",'.
							'"creationDate":"2011-11-09T14:41:16+0000",'.
							'"lastModifiedDate":"2011-11-09T14:41:54+0000",'.
							'"firstName":"Claudia","lastName":"Mustermann",'.
							'"homePhone":"987 654321",'.
							'"mobilePhone1":"123 456789",'.
							'"birthDate":"1990-11-01T00:00:00+0000",'.
							'"gender":"FEMALE","loanCycle":0,'.
							'"groupLoanCycle":0},'.
						'{"encodedKey":"8a56f203337f480e013388c697db040f",'.
							'"id":"502848759",'.
							'"creationDate":"2011-11-09T14:41:16+0000",'.
							'"lastModifiedDate":"2011-11-09T14:41:54+0000",'.
							'"firstName":"Claudia","lastName":"Mustermann",'.
							'"homePhone":"987 654321",'.
							'"mobilePhone1":"123 456789",'.
							'"birthDate":"1990-11-01T00:00:00+0000",'.
							'"gender":"FEMALE","loanCycle":0,'.
							'"groupLoanCycle":0}'.
						']';
		$clientsResult = Mambu_Base::parseResult($clients, $jsonResult);
		$clientsReturn = array();
		$clientTemplate = new Mambu_Client();
		foreach($clientsResult as $clientArray){
			$clientObject = new Mambu_Client();
			Mambu_Base::parseSubResult($clientObject, $clientArray);
			$clientsReturn[] = $clientObject;
		}
		$this->assertEquals('8a56f203337f480e013388c697db040e', 
			$clientsReturn[0]->encodedKey);
		$this->assertEquals('8a56f203337f480e013388c697db040f', 
			$clientsReturn[1]->encodedKey);
	}

	/**
	 * Tests retrieval of filtered resource collections
	 *
	 * @covers Mambu_Base::_getAllFiltered
	 */
	public function testGetAllFiltered(){
		$client = new Mambu_Client();
		try {
			$client->_getAllFiltered();
			$this->fail('Exception should be thrown, because first parameter '.
				'is not provided');
		} catch (Exception $e){
			// ignore
		}

		try {
			$client->_getAllFiltered('foo');
			$this->fail('Exception should be thrown, because first parameter '.
				'is no array');
		} catch (Exception $e){
			// ignore
		}
	}

	/**
	 * Tests retrieval of resource with parameter
	 *
	 * @covers Mambu_Base::_getWithParameters
	 */
	public function testGetWithParameters(){

		$client = new Mambu_Client();

		try {
			$client->_getWithParameters();
			$this->fail('Exception should be thrown, because no parameters '.
				'are provided');
		} catch (Exception $e){
			// ignore
		}

		try {
			$client->_getWithParameters(null, null);
			$this->fail('Exception should be thrown, because no parameters '.
				'are provided');
		} catch (Exception $e){
			// ignore
		}
	}

}