<?php

require_once('config.php');
require_once('auto.php');
require_once('init.php');

$action = PageActionMap::getActionByPage(Utils::get('action'));
$actionClassName = ucfirst($action) . 'Action';

if (LoggedUser::whoIsLogged() === NULL && $actionClassName != 'LoginAction') {
	Utils::redirect('prihlasenie.html');
}

$actionClass = new $actionClassName();
MySmarty::assign('content', $actionClass->getContent());
MySmarty::assign('baseUrl', BASE_URL);
echo MySmarty::fetch('index.tpl');

?>