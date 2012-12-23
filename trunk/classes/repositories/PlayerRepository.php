<?php

class PlayerRepository extends Repository {
	
	protected $table = 'player';
	
	public function getLivePlayersByGame($game) {
		$query = 'SELECT * FROM ' . $this->getTable() . ' WHERE actual_lifes > 0 AND game = ' . intval($game);
		return DB::fetchAll($query, get_class($this));
	}
	
	public function getPlayerByGameAndPosition($game, $position) {
		$query = 'SELECT * FROM ' . $this->getTable() . ' WHERE game = ' . intval($game) . ' AND position = ' . intval($position);
		$players = DB::fetchAll($query, get_class($this));
		if ($players) {
			return $players[0];
		}
		return null;
	}

	public function getCountLivePlayersWithRoles($game, $roles = array()) {
		$roleWhere = '';
		if (!is_array($roles)) {
			$roles = array($roles);
		}
		if (!empty ($roles)) {
			$roleWhere = ' AND role IN (' . implode(', ', $roles) . ')';
		}
		$query = 'SELECT count(*) AS pocet FROM ' . $this->getTable() . ' WHERE game = ' . intval($game) . ' AND actual_lifes > 0' . $roleWhere;
		$res = DB::fetchFirst($query);
		return $res['pocet'];
	}
}

?>