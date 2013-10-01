<?php

/**
 * Representation of a client's address in Mambu
 */
class Mambu_Address {

	// user provided values
	public $line1;
	public $line2;
	public $city;
	public $region;
	public $postcode;
	public $country;

	// system generated values
	public $encodedKey;
	public $parentKey;
	public $indexInList;

}