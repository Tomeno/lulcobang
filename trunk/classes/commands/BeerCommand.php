<?php

class BeerCommand extends Command {
	
	protected function check() {
		// TODO checker ci uz nema dosiahnuty max lifes
	}

	protected function run() {
		// Tequila Joe ma myslim 2 zivoty za kazde pivo, preto by sa mohlo stat ze sa max_lifes presvihne
		$newLifes = min($this->actualPlayer['actual_lifes'] + 1, $this->actualPlayer['max_lifes']);
	}

	protected function generateMessages() {
	}

	protected function createResponse() {
		;
	}
}

?>