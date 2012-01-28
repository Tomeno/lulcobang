<?php

class PageNotFoundBox extends AbstractBox {
	
	protected $template = 'page-not-found.tpl';

	protected function setup() {
		MySmarty::assign('baseUrl', BASE_URL);
	}
}

?>