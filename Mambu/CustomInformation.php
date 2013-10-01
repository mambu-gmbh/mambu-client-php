<?php

/**
 * Representation of a client's custom information in Mambu
 */
class Mambu_CustomInformation {

	// user provided values
	public $name;
	public $value;

	// system generated values
	public $encodedKey;
	public $parentKey;
	public $indexInList;
	public $customFieldKey;

}