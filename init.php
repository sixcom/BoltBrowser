<?php

require_once('BoltBrowser.class.php');

$browser = new BoltBrowser('http://www.baidu.com');

$response = $browser->get('');

