Mambu PHP Client
================

The Mambu PHP Client library is an open source library to interact with Mambu's APIs from your PHP project. 

The library interacts with Mambu's REST API.


Usage
-----

In order to use the Mambu PHP Client you need to

* Zend Framework (version >= 1.11) on your PHP include_path (http://framework.zend.com/)

* Mambu PHP Client on your PHP include_path

* provide your Mambu API credentials as follows:
Mambu_Base::setupAccount('apiUsername', 'apiPassword', 'subdomain.mambu.com');

* simple sample usage scenario: see demo.php

Contributing to the Mambu PHP Client
------------------------------------

If you would like to contribute to the Mambu PHP Client, please consider the following rules:

* add php unit tests to verify that your code works and is of high quality

* document all methods using PHPDoc style


Running Tests
-------------
In order to run the unit and integration tests you need to:

* PHPUnit installed (http://www.phpunit.de/manual/current/en/index.html), requires pear (http://pear.php.net/)

* if you want to generate code coverage reports, you also need to install XDebug (http://xdebug.org/)

* create a Custom Field with the label 'Education' through the Mambu user interface

* create a client through the Mambu user interface with the following data:

    First Name: John
    
    Last Name: Doe
    
    Date of Birth: January 1, 1990
    
    Gender: Male
    
    Street Address - Line 1: Teststreet 1
    
    Street Address - Line 2: East
    
    City: Testtown
    
    State/Province/Region: Testregion
    
    Zip Postal Code: 12345
    
    Country: Testcountry
    
    Custom Fields (Education): High School
    
    Document Type: Driver's License
    
    Issuing Authority: Testcountry
    
    Document ID: 123456789ABC
    
    Valid Until: November 1, 2013
    
    Profile Notes: test note


* save the Client ID for later usage

* add John Doe to a group called 'Test Group' through the Mambu user interface

* enter your Mambu API user credentials and Mambu account information in the mambu.ini in the mambu-client-php/Test directory.

    [account_setup]
    
    username = 'your-api-user's-username';
    
    password = 'your-api-user's password';
    
    domain = 'your-mambu-subodomain.mambu.com';
    
    [test_data_setup]
    
    client_id = 'your-john-doe-client-id';
    

* open your shell and go to the mambu-client-php/Test directory, then enter 'phpunit' and make sure that all tests run through without any problems; if you just want to run the unit tests and not the integration tests, enter 'phpunit UnitTest'
