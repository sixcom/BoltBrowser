<?php
require_once('BoltBrowser.class.php');

$browser = new BoltBrowser('http://www.google.com');
$browser->registerCookieManager('BoltFilePersistentCookieManager');
$response = $browser->get('');

var_dump($response);

