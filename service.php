<?php

require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/auto.php');
require_once(dirname(__FILE__) . '/init.php');

$serviceName = $_GET['service'];
$service = new $serviceName();
$service->main();

?>