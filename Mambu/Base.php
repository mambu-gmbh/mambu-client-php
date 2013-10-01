<?php

require_once 'Zend/Http/Client.php';
require_once 'Zend/Json.php';

/**
 * Abstract base class providing basic implementations for all Mambu REST 
 * resources.
 *
 * All Mambu REST resources must extend this class and implement the method 
 * _getResourceName().
 *
 * Methods starting with '_' should only be used by tests and PHP classes 
 * extending Mambu_Base, they are only public and not private or protected to 
 * enable easy testing.
 */
abstract class Mambu_Base {

	private static $apiUsername;
	private static $apiPassword;
	private static $domain;

	/**
	 * Setup the Mambu PHP Client with your Mambu API credentials and account 
	 * settings
	 *
	 * @param string $apiUsername
	 * @param string $apiPassword
	 * @param string $domain
	 */
	public static function setupAccount($apiUsername, $apiPassword, $domain){
		self::$apiUsername = $apiUsername;
		self::$apiPassword = $apiPassword;
		self::$domain = $domain;
	}

	/**
	 * Returns the name of the resource, which becomes part of the resource URL.
	 * 
	 * Don't call this method in your client.
	 *
	 * - e.g. 'clients' or 'accounts'
	 *
	 * - must be implemented by all Mambu REST resources
	 *
	 * @return the name of the resource
	 */
	protected abstract function _getResourceName();

	/**
	 * Returns the URL which points to the Mambu REST resource collection, 
	 * including authorization credentials. Don't call this method in your 
	 * client.
	 *
	 * @param string|null $id the id of the resource, not the encoded key, if 
	 * null a resource collection url is returned
	 *
	 * @return the URL which points to the Mambu REST resource collection or 
	 * resource
	 */
	public function _getApiEndpoint($id = null){
		$url = 'https://'.self::$apiUsername.':'.self::$apiPassword.'@'.
					self::$domain.'/api/'.
					$this->_getResourceName().($id != null ? '/'.$id : '');
		return $url;
	}

	/**
	 * Returns a Zend_Http_Client object which contains all information in 
	 * order to submit the request to the Mambu server. Don't call this method 
	 * in your client.
	 *
	 * @param string $url the url of the resource or resource collection
	 * @param string $method Zend_Http_Client::GET or Zend_Http_Client::POST
	 * @param array|null $parameters array with key-value pairs that are posted 
	 * to the resource or resource collection
	 *
	 * @return Zend_Http_Client
	 */
	public function _getHttpClient($url, $method, $parameters = null){
		$httpClient = new Zend_Http_Client($url, array(
	    	'maxredirects' => 0,
	    	'timeout'      => 10));
		$httpClient->setMethod($method);
		if(is_array($parameters)){
			foreach($parameters as $key => $value){
				$httpClient->setParameterGet($key, $value);
			}
		}
		return $httpClient;
	}

	/**
	 * Creates a new resource of this class on Mambu
	 */
	public function post(){
		$response = $this->_getHttpClient($this->_getApiEndpoint(), 
			Zend_Http_Client::POST, get_object_vars($this))->request();
		Mambu_Base::parseResult($this, $response->getBody());
	}

	/**
	 * Retrieves an exisiting resource of this class from Mambu.
	 *
	 * @param string $id the resource identifier (id, not encodedKey)
	 */
	public function get($id){
		if(!empty($id)){
			$response = $this->_getHttpClient($this->_getApiEndpoint($id), 
				Zend_Http_Client::GET)->request();
			Mambu_Base::parseResult($this, $response->getBody());
		} else {
			throw new Exception("Provided 'id' is empty", "1");
		}
	}

	/**
	 * Retrieves an exisiting resource of this class from Mambu, the additional 
	 * parameters can effect the result (e.g. more details, less details). 
	 * Don't call this method in your client.
	 *
	 * @param string $id the resource identifier (id, not encodedKey)
	 * @param array $parameters an array of parameters
	 */
	public function _getWithParameters($id, $parameters){
		if(is_array($parameters) && !empty($id)){
			$response = $this->_getHttpClient($this->_getApiEndpoint($id), 
				Zend_Http_Client::GET, $parameters)->request();
			Mambu_Base::parseResult($this, $response->getBody());
		} else {
			throw new Exception("Provided 'parameters' argument is not an '.
				'array or 'id' is empty", "1");
		}

	}

	/**
	 * Retrieves all exisiting resources of this class from Mambu, filtered by 
	 * a set of criteria. Don't call this method in your client.
	 *
	 * @param array $filter the resource identifier (id, not encodedKey)
	 * @return array with returned objects
	 */
	public function _getAllFiltered($filter){
		if(is_array($filter)){
			$response = $this->_getHttpClient($this->_getApiEndpoint(), 
				Zend_Http_Client::GET, $filter)->request();
			$resourceCollection = array();
			$resourceCollection = Mambu_Base::parseResult($resourceCollection, 
				$response->getBody());
			return $resourceCollection;
		} else {
			throw new Exception("Provided filter argument is not an array", 
				"1");
		}
	}

	/**
	 * Retrieves all exisiting resources of this class from Mambu
	 *
	 * TODO: NOT YET USED, since a list of all clients is not available
	 */
	public function getAll(){
		$response = $this->_getHttpClient($this->_getApiEndpoint(), 
			Zend_Http_Client::GET)->request();
		Mambu_Base::parseResult($this, $response->getBody());
	}

	/**
	 * Takes a JSON string and in case of a provided Mambu_Base object, copies
	 * all key-value pairs to this object as properties with their values and
	 * in case of an array object provided, copies results to the array.
	 *
	 * @param Mambu_Base|array $object the Mambu_Base object or array to copy 
	 * 	the values to
	 * @param string $jsonResult JSON string
	 */
	public static function parseResult($object, $jsonResult){
		$responseArray = Zend_Json::decode($jsonResult);
		if($object instanceof Mambu_Base){
			foreach ($responseArray as $key => $value){
				$object->$key = $value;
			}
		} else if(is_array($object)){
			foreach ($responseArray as $key => $value){
				$object[$key] = $value;
			}
			return $object;
		} else {
			throw new Exception("Provided object argument must be an array or '.
				'instance of Mambu_Base", "1");
		}
	}

	/**
	 * Takes an array and copies it's keys and values to the given object's 
	 * fields
	 *
	 * @param object $object the object which should get populated with the 
	 * key-value pairs if the array
	 * @param array $array array with key-value pairs
	 */
	public static function parseSubResult($object, $array){
		foreach ($array as $key => $value){
			$object->$key = $value;
		}
	}

}