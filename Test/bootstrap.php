<?php
require_once '../Mambu/Base.php';

date_default_timezone_set('Europe/Berlin');

$settings = parse_ini_file("mambu.ini");
Mambu_Base::setupAccount($settings['username'], $settings['password'], $settings['domain']);
