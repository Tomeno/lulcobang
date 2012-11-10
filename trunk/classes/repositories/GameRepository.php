<?php

class GameRepository extends Repository {
	
	protected $table = 'game';
	
	public function getGamesByRooms($room) {
		$query = 'SELECT id, room FROM game WHERE room IN (' . implode(',', $room) . ') AND
			status IN (' . Game::GAME_STATUS_CREATED . ', ' . Game::GAME_STATUS_INITIALIZED . ', ' . Game::GAME_STATUS_STARTED . ')';
		$res = DB::fetchAll($query);
		return $res;
	}
}

?>