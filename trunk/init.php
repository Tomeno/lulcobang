<?php

$GLOBALS['db'] = new DB(DB_USERNAME, DB_PASSWORD, DB_HOST, DB_NAME);
$GLOBALS['db']->query("SET names UTF8");
MySmarty::init();

?>