<?php

require_once '../Mambu/Client.php';

/**
 * Integration test with a Mambu account
 */
class IntegrationTest_Mambu_ClientTest extends PHPUnit_Framework_TestCase {

	public $clientId;

	/**
	 * (non-PHPdoc)
	 * 
	 * Sets up the an existing client's client ID
	 * 
	 * @see PHPUnit/Framework/PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp(){
		$settings = parse_ini_file("mambu.ini");
		$this->clientId = $settings['client_id'];
	}

	/**
	 * Tests creation of a client
	 *
	 * @covers Mambu_Client::post
	 */
	public function testPost(){

		// create client
		$client = new Mambu_Client();
		$client->firstName = 'Max';
		$client->lastName = 'Mustermann';
		$client->birthdate = '1950-12-20';
		$client->post();

		// check returned values
		$this->assertEquals('Max',$client->firstName);
		$this->assertEquals('Mustermann',$client->lastName);
		$this->assertNotNull($client->lastModifiedDate);
		$this->assertEquals(0,$client->loanCycle);

		// pass client id to next test
		return $client->id;

	}

	/**
	 * Tests retrieval of a client
	 *
	 * @depends testPost
	 * @covers Mambu_Client::get
	 */
	public function testGet($id){

		// retrieve client
		$client = new Mambu_Client();
		$client->get($id);

		// check retrieved values
		$this->assertEquals('Max',$client->firstName);
		$this->assertEquals('Mustermann',$client->lastName);
		$this->assertNotNull($client->lastModifiedDate);
		$this->assertEquals(0,$client->loanCycle);
		$this->assertEquals($id, $client->id);

	}

	/**
	 * Tests retrieval of a client with full details
	 *
	 * @depends testPost
	 * @covers Mambu_Client::getWithFullDetails
	 * @covers Mambu_Client::_parseFullDetails
	 * @covers Mambu_Client::_parseAddresses
	 * @covers Mambu_Client::_parseCustomInformations
	 * @covers Mambu_Client::_parseIdDocuments
	 * @covers Mambu_Client::_parseClient
	 * @covers Mambu_Client::_getResourceName
	 *
	 */
	public function testGetWithFullDetails($id){

		// check full details, if a client with full details was created with 
		// the UI
		if($this->clientId != null){
			// retrieve client
			$client = new Mambu_Client();
			$client->getWithFullDetails($this->clientId);
			// check retrieved values
			$this->assertEquals('John', $client->firstName);
			$this->assertEquals('Doe', $client->lastName);
			$this->assertEquals(1, count($client->addresses));
			$this->assertInstanceOf('Mambu_Address', $client->addresses[0]);
			$this->assertEquals('Testtown', $client->addresses[0]->city);
			$this->assertEquals(1, count($client->customInformation));
			$this->assertInstanceOf('Mambu_CustomInformation', 
				$client->customInformation[0]);
			$this->assertEquals('Education', 
				$client->customInformation[0]->name);
			$this->assertEquals('High School', 
				$client->customInformation[0]->value);
			$this->assertEquals(1,count($client->idDocuments));
			$this->assertInstanceOf('Mambu_IdDocument', 
				$client->idDocuments[0]);
			$this->assertEquals('Testcountry', 
				$client->idDocuments[0]->issuingAuthority);
			$this->assertNotNull($client->lastModifiedDate);
			$this->assertEquals(0, $client->loanCycle);
			$this->assertEquals($this->clientId, $client->id);
		}
		// otherwise check reasonable data with just created client (via 
		// testPost())
		else {
			// retrieve client
			$client = new Mambu_Client();
			$client->getWithFullDetails($id);

			// check retrieved values
			$this->assertEquals('Max', $client->firstName);
			$this->assertEquals('Mustermann', $client->lastName);

			$this->assertEquals(0, count($client->addresses));

			$this->assertNotNull($client->lastModifiedDate);
			$this->assertEquals(0, $client->loanCycle);
			$this->assertEquals($id, $client->id);
		}

	}

	/**
	 * Tests retrieval of clients with filters
	 *
	 * @depends testPost
	 * @covers Mambu_Client::getAllFiltered
	 */
	public function testGetAllFiltered($id){

		// retrieve all clients
		$client = new Mambu_Client();
		$clients = $client->getAllFiltered('Max', 'Mustermann');

		// check retrieved values
		$this->assertEquals('Max', $clients[0]->firstName);
		$this->assertEquals('Mustermann', $clients[0]->lastName);
		$this->assertNotNull($clients[0]->lastModifiedDate);
		$this->assertEquals(0, $clients[0]->loanCycle);
		$foundId = false;
		foreach($clients as $testClient){
			if($testClient->id == $id){
				$foundId = true;
			}
		}
		$this->assertTrue($foundId);
		$this->assertGreaterThanOrEqual(1, count($clients));

		// check first + last name
		$client = new Mambu_Client();
		$clients = $client->getAllFiltered('Max', 'Mustermann');
		$this->assertNull($clients[0]->addresses);
		$this->assertGreaterThanOrEqual(1, count($clients));

		// check birthdate and last name
		$client = new Mambu_Client();
		$clients = $client->getAllFiltered(null, 'Mustermann', null, 
			'1950-12-20');
		$this->assertNull($clients[0]->addresses);
		$this->assertGreaterThanOrEqual(1, count($clients));

		// check document id
		if($this->clientId != null){
			$client = new Mambu_Client();
			$clients = $client->getAllFiltered(null, 'Mustermann', 
				'123456789ABC');
			$this->assertEquals(0, count($clients[0]->addresses));
			$this->assertEquals(1, count($clients));
			$this->assertEquals(0, count($clients[0]->idDocuments));
		}

	}

}