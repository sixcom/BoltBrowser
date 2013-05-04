<?php
require_once('BoltBrowser.class.php');

$browser = new BoltBrowser('http://www.google.com');
$browser->registerCookieManagerClass('BoltFilePersistentCookieManager');
//$browser->registerResponseClass('BoltJSONPResponse');
$response = $browser->get('');

var_dump($response);

