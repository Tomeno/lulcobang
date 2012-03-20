<?php

class PageNotFoundBox extends AbstractBox {
	
	protected $template = 'page-not-found.tpl';

	protected $message = '';

	protected function setup() {
		header('HTTP/1.1 404 Not Found');
		MySmarty::assign('baseUrl', BASE_URL);
		MySmarty::assign('message', $this->message);
	}

	public function setMessage($message) {
		$this->message = $message;
	}
}

?>