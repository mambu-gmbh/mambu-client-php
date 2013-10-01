<?php

require_once '../Mambu/Client.php';

/**
 * Integration test for the Base class of the Mambu PHP API wrapper
 */
class IntegrationTest_Mambu_BaseTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tests internal method which is used by extending classes
	 *
	 * @covers Mambu_Base::_getWithParameters
	 */
	public function testGetWithParameters(){
		$client = new Mambu_Client();
		$client->firstName = 'Maxi';
		$client->LastName = 'Musterfrau';
		$client->post();
		$id = $client->id;

		$client = new Mambu_Client();
		$client->_getWithParameters($id, array('fullDetails' => 'true'));
		$this->assertTrue(is_array($client->addresses));

		return $id;
	}

	/**
	 * Tests internal method which is used by extending classes
	 *
	 * @depends testGetWithParameters
	 * @covers Mambu_Base::_getAllFiltered
	 */
	public function testGetAllFiltered($id){
		$client = new Mambu_Client();
		$clients = $client->_getAllFiltered(array('firstName' => 'Maxi', 
			'lastName' => 'Musterfrau'));
		$this->assertEquals('Musterfrau', $clients[0]['lastName']);
	}
}