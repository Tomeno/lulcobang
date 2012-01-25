<?php

class PlayerRepository extends Repository {
	
	protected $table = 'player';
	
	public function getLivePlayersByGame($game) {
		$query = 'SELECT * FROM ' . $this->table . ' WHERE actual_lifes > 0 AND game = ' . intval($game);
		return DB::fetchAll($query, get_class($this));
	}
	
	public function getPlayerByGameAndPosition($game, $position) {
		$query = 'SELECT * FROM ' . $this->table . ' WHERE game = ' . intval($game) . ' AND position = ' . intval($position);
		$players = DB::fetchAll($query, get_class($this));
		if ($players) {
			return $players[0];
		}
		return null;
	}
}

?>