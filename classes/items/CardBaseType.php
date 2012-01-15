<?php

class CardBaseType extends Item {

	public function getLocalizedTitle() {
		return Localize::getMessage($this['localize_title_key']);
	}

	public function getLocalizedDescription() {
		return Localize::getMessage($this['localize_description_key']);
	}
}

?>