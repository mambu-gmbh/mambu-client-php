<?php

require_once 'Base.php';
require_once 'Address.php';
require_once 'CustomInformation.php';
require_once 'IdDocument.php';

/**
 * Representation of a client in Mambu
 */
class Mambu_Client extends Mambu_Base {

	// user provided values
	public $firstName;
	public $lastName;
	public $homePhone;
	public $mobilePhone1;
	public $gender;
	public $birthdate;
	public $email;
	public $notes;
	public /* @var Mambu_Address[] */ $addresses;
	public /* @var Mambu_CustomInformation[] */ $customInformation;
	public /* @var Mambu_IdDocument[] */ $idDocuments;
	public /* @var array */ $groupKeys;

	// system generated values
	public $encodedKey;
	public $id;
	public $creationDate;
	public $lastModifiedDate;
	public $loanCycle;
	public $groupLoanCycle;

	/**
	 * (non-PHPdoc)
	 *
	 * Implements the abstract function from Mambu_Base
	 *
	 * @see Mambu_Base::_getResourceName()
	 */
	protected function _getResourceName(){
		return 'clients';
	}

	/**
	 * Reads the client information plus additional information like addresses, 
	 * custom information etc.
	 *
	 * @param $id the ID of the client, not the encodedKey
	 */
	public function getWithFullDetails($id){
		$this->_getWithParameters($id, array('fullDetails' => 'true'));
		$this->_parseFullDetails();
	}

	/**
	 * Parses all additional information that is provided if fullDetails is set 
	 * to true into their matching Mambu objects
	 */
	private function _parseFullDetails(){
		$this->_parseClient();
		$this->_parseAddresses();
		$this->_parseCustomInformations();
		$this->_parseIdDocuments();
	}

	/**
	 * Copies the values of the array from the 'addresses' field to the fields 
	 * of this object and deletes the 'addresses' field
	 */
	private function _parseAddresses(){
		// copy to internal array
		$addressesAsArray = $this->addresses;
		unset($this->addresses);
		$this->addresses = array();
		foreach($addressesAsArray as $addressAsArray){
			$address = new Mambu_Address();
			Mambu_Base::parseSubResult($address, $addressAsArray);
			$this->addresses[] = $address;
		}
	}

	/**
	 * Copies the values of the array from the 'customInformation' field to the 
	 * fields of this object and deletes the 'customInformation' field
	 */
	private function _parseCustomInformations(){
		// copy to internal array
		$customInformationsAsArray = $this->customInformation;
		unset($this->customInformation);
		$this->customInformation = array();
		foreach($customInformationsAsArray as $customInformationAsArray){
			$customInformation = new Mambu_CustomInformation();
			Mambu_Base::parseSubResult($customInformation, 
				$customInformationAsArray);
			$this->customInformation[] = $customInformation;
		}
	}

	/**
	 * Copies the values of the array from the 'idDocuments' field to the 
	 * fields of this object and deletes the 'idDocuments' field
	 */
	private function _parseIdDocuments(){
		// copy to internal array
		$idDocumentsAsArray = $this->idDocuments;
		unset($this->idDocuments);
		$this->idDocuments = array();
		foreach($idDocumentsAsArray as $idDocumentAsArray){
			$idDocument = new Mambu_IdDocument();
			Mambu_Base::parseSubResult($idDocument, $idDocumentAsArray);
			$this->idDocuments[] = $idDocument;
		}
	}

	/**
	 * Copies the values of the array from the 'client' field to the fields of 
	 * this object and deletes the 'client' field
	 */
	private function _parseClient(){
		Mambu_Base::parseSubResult($this, $this->client);
		unset($this->client);
	}

	/**
	 * Returns all clients, that the filters apply to - possible combinations 
	 * for lookup are: First + Last Name, Last Name + BirthDate and Last Name + 
	 * ID Document Number (note: you have to set a filter).
	 *
	 * @param string|null $firstName the exact matching firstname of the 
	 * 	client, if used, lastName also needs to be set
	 * @param string|null $lastName the exact matching lastname of the client, 
	 * 	if used, firstName or birthdate also needs to be set
	 * @param string|null $idDocument the id document number of the client
	 * @param string|null $birthdate the birthdate of the client, if used, 
	 * 	lastName also needs to be set
	 *
	 * @return array an array of Mambu_Client's that match the filter criteria
	 */
	public function getAllFiltered($firstName = null, $lastName = null, 
		$idDocument = null, $birthdate = null){
		$filter = array();
		if(!empty($firstName)){
			if(empty($lastName)){
				throw new Exception("When filtering for first name, last name '.
					'also needs to be set.", 1);
			} else {
				$filter['firstName'] = $firstName;
			}
		}
		if(!empty($lastName)){
			if(empty($firstName) && empty($birthdate) && empty($idDocument)){
				throw new Exception("When filtering for last name, first name '.
					'or birthdate also needs to be set.", 1);
			} else {
				$filter['lastName'] = $lastName;
			}
		}
		if(!empty($idDocument)){
			if(empty($lastName)){
				throw new Exception("When filtering for ID document, last '.
					'name also needs to be set.", 1);
			} else {
				$filter['idDocument'] = $idDocument;
			}
		}
		if(!empty($birthdate)){
			if(empty($lastName)){
				throw new Exception("When filtering for birthdate, last name '.
					'also needs to be set.", 1);
			} else {
				$filter['birthdate'] = $birthdate;
			}
		}
		if(empty($firstName) && empty($lastName) && empty($idDocument) && 
			empty($birthdate)){
			throw new Exception("You have to set at least one of the filters: '.
					'First + Last Name, Last Name + BirthDate or Last Name + '.
					'ID Document Number.", 1);
		}

		$clientsReturn = array();
		$clients = $this->_getAllFiltered($filter);
		foreach($clients as $clientArray){
			$clientObject = new Mambu_Client();
			Mambu_Base::parseSubResult($clientObject, $clientArray);
			$clientsReturn[] = $clientObject;
		}

		return $clientsReturn;
	}
}