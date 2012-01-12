<?php

require_once('config.php');
require_once('auto.php');
require_once('init.php');

$language = Utils::get('language');

if (!$language) {
	// TODO check user and country, then default en
	$url = PageActionMap::createUrl(array(), 'en');
	Utils::redirect($url);
}

$action = PageActionMap::getActionByPageAndLanguage(Utils::get('action'));
$actionClassName = ucfirst($action) . 'Action';

if (LoggedUser::whoIsLogged() === NULL && $actionClassName != 'LoginAction') {
	$page = PageActionMap::getPageByTypeAndLanguage('login', $language);
	$url = PageActionMap::createUrl($page['alias']);
	Utils::redirect($url);
}

$actionClass = new $actionClassName();
MySmarty::assign('content', $actionClass->getContent());
MySmarty::assign('baseUrl', BASE_URL);
echo MySmarty::fetch('index.tpl');

?>