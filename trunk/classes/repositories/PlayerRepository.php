<?php

class PlayerRepository extends Repository {
	
	protected $table = 'player';
	
	public function getLivePlayersByGame($game) {
		$query = 'SELECT * FROM ' . $this->table . ' WHERE actual_lifes > 0 AND game = ' . intval($game);
		return $GLOBALS['db']->fetchAll($query, get_class($this));
	}
}

?>