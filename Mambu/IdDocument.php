<?php

/**
 * Representation of a client's ID document in Mambu
 */
class Mambu_IdDocument {

	// user provided values
	public $documentType;
	public $documentId;
	public $issuingAuthority;
	public $validUntil;

	// system generated values
	public $encodedKey;
	public $clientKey;
	public $indexInList;

}