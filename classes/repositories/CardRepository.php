<?php

class CardRepository extends Repository {
	
	protected $table = 'card';

	public static function instance() {
		return parent::instance(get_class());
	}
}

?>