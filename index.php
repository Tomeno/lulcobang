<?php

require_once('config.php');
require_once('auto.php');
require_once('init.php');

try {
	BangSeo::addTitlePart('Bang!');

	$language = Utils::get('language');

	if (!$language) {
		// TODO check user and country, then default en
		$url = PageActionMap::createUrl(array(), 'sk');
		Utils::redirect($url);
	}

	$actionAlias = Utils::get('action');
	$pageRepository = new PageRepository(TRUE);
	$page = $pageRepository->getOneByAlias($actionAlias);

	$pageTypeRepository = new PageTypeRepository(TRUE);
	$pageType = $pageTypeRepository->getOneById($page['page_type']);

	$action = PageActionMap::getActionByPageAndLanguage(Utils::get('action'));
	$actionClassName = ucfirst($action) . 'Action';

	// TODO caching

	if (LoggedUser::whoIsLogged() === NULL && $pageType['needs_login'] == 1) {
		if ($pageType['action'] != 'logout') {
			setcookie('ref_url', Utils::getActualUrl(), NULL, '/');
		}
		$page = PageActionMap::getPageByTypeAndLanguage('login', $language);
		$url = PageActionMap::createUrl($page['alias']);
		Utils::redirect($url);
	}

	$actionClass = new $actionClassName();
	MySmarty::assign('content', $actionClass->getContent());

} catch (Exception $e) {
	// TODO vsetky exceptions lokalizovat a hadzat uz lokalizovane aby sa tu mohli vypisat
	$pageNotFoundBox = new PageNotFoundBox();
	$pageNotFoundBox->setMessage($e->getMessage());
	$content = $pageNotFoundBox->render();
	MySmarty::assign('content', $content);
}

$upperPartBox = new UpperPartBox();
MySmarty::assign('upperPart', $upperPartBox->render());

// nacachujeme si menu
$memcache = BangMemcache::instance();
$key = 'main_menu_' . $language . '_' . $actionAlias;
$menu = $memcache->get($key);

if (!$menu) {
	$menuBox = new MenuBox();
	$menuBox->setActualAction($action);
	$menu = $menuBox->render();
	$memcache->set($key, $menu, NULL, '+2 hours');
}
MySmarty::assign('menu', $menu);

MySmarty::assign('title', BangSeo::getTitle());
MySmarty::assign('description', BangSeo::getDescription());
MySmarty::assign('keywords', BangSeo::getKeywords());
MySmarty::assign('actualYear', date('Y'));
MySmarty::assign('baseUrl', BASE_URL);
echo MySmarty::fetch('index.tpl');

?>