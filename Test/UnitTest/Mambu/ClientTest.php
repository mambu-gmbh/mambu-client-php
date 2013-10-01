<?php

require_once '../Mambu/Client.php';

/**
 * Unit test for the Mambu Client API
 */
class UnitTest_Mambu_ClientTest extends PHPUnit_Framework_TestCase {

	public $username;
	public $password;
	public $subdomain;
	public $domain;

	/**
	 * (non-PHPdoc)
	 * 
	 * Sets up the api user credentials and Mambu account settings
	 * 
	 * @see PHPUnit/Framework/PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp(){
		$settings = parse_ini_file("mambu.ini");
		$this->username = $settings['username'];
		$this->password = $settings['password'];
		$this->domain = $settings['domain'];
	}

	/**
	 * Tests URL and request creation
	 *
	 * @covers Mambu_Client::_getApiEndpoint
	 * @covers Mambu_Client::_getHttpClient
	 */
	public function testRequestCreation(){

		// test _getApiEndpoint
		$mambuClient = new Mambu_Client();
		$url = $mambuClient->_getApiEndpoint();
		$this->assertEquals('https://'.$this->username.':'.$this->password.'@'.
		$this->domain.'/api/clients', $url);
			
		// test	_getApiEndpoint(id)
		$url = $mambuClient->_getApiEndpoint('1234');
		$this->assertEquals('https://'.$this->username.':'.$this->password.'@'.
		$this->domain.'/api/clients/1234', $url);

		// test	GET
		$httpClient = $mambuClient->_getHttpClient($url, Zend_Http_Client::GET);
		$this->assertAttributeEquals('GET','method',$httpClient);
		$this->assertEquals('https://'.$this->username.':'.$this->password.'@'.
		$this->domain.':443/api/clients/1234',
		$httpClient->getUri(true));

		// test	POST
		$httpClient = $mambuClient->_getHttpClient($url, Zend_Http_Client::POST);
		$this->assertAttributeEquals('POST','method',$httpClient);

		// test	GET with parameters
		$httpClient = $mambuClient->_getHttpClient($url, Zend_Http_Client::GET, 
			array('fullDetails'=>'true'));
		$this->assertEquals('https://'.$this->username.':'.$this->password.'@'.
		$this->domain.':443/api/clients/1234',
		$httpClient->getUri(true));
		$this->assertAttributeEquals(array('fullDetails'=>'true'),'paramsGet', 
			$httpClient);
	}

	/**
	 * Tests retrieval of a client with full details
	 *
	 * @covers Mambu_Client::getWithFullDetails
	 */
	public function testGetWithFullDetails(){

		try {
			$client = new Mambu_Client();
			$client->getWithFullDetails();
			$this->fail('Exception should be thrown when trying to get client '.
				'without providing an ID');
		} catch (Exception $e){
			// ignore
		}

	}

	/**
	 * Tests retrieval of clients with filters
	 *
	 * @covers Mambu_Client::getAllFiltered
	 */
	public function testGetAllFiltered(){

		// check full details only
		try {
			$client = new Mambu_Client();
			$clients = $client->getAllFiltered();
			$this->fail("Exception should be thrown");
		} catch(Exception $e){
			// ignore
		}

		// check first name only
		try {
			$client = new Mambu_Client();
			$clients = $client->getAllFiltered('Max');
			$this->fail("Exception should be thrown");
		} catch(Exception $e){
			// ignore
		}

		// check last name only
		try {
			$client = new Mambu_Client();
			$clients = $client->getAllFiltered(null,'Mustermann');
			$this->fail("Exception should be thrown");
		} catch(Exception $e){
			// ignore
		}

		// check document id only
		try {
			$client = new Mambu_Client();
			$clients = $client->getAllFiltered(null,null,'123456789ABC');
			$this->fail("Exception should be thrown");
		} catch(Exception $e){
			// ignore
		}

		// check birth date only
		try {
			$client = new Mambu_Client();
			$clients = $client->getAllFiltered(null,null,null,'1990-12-31');
			$this->fail("Exception should be thrown");
		} catch(Exception $e){
			// ignore
		}

	}

}