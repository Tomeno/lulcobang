<?php

abstract class AbstractBox {

	protected $template = '';

	public function render() {
		$this->setup();
		return MySmarty::fetch($this->template);
	}

	abstract protected function setup();
}

?>