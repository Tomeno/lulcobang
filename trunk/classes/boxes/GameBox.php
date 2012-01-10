<?php

class GameBox extends AbstractBox {
	
	protected $template = 'game.tpl';
	
	protected $game = NULL;

	protected function setup() {
		if ($this->game !== NULL) {
			MySmarty::assign('game', $this->game);
		}
	}

	public function setGame(Game $game) {
		$this->game = $game;
	}
}

?>