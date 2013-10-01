<?php
require_once 'Mambu/Base.php';
require_once 'Mambu/Client.php';
Mambu_Base::setupAccount('apiUsername', 'apiPassword', 'subdomain.mambu.com');
$client = new Mambu_Client();
$client->get('existing-client-id');
if($client->encodedKey != null){
	echo $client->firstName.' '.$client->lastName;
} else {
	echo 'Warning: Could not find a client with the provided client ID!';
}
