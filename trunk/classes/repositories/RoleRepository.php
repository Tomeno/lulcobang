<?php

class RoleRepository extends Repository {
	
	protected $table = 'role';
	
	public function getRoles($playerCount) {
		$query = 'SELECT * FROM ' . $this->table . ' LIMIT ' . $playerCount;
		return $GLOBALS['db']->fetchAll($query);
	}
}

?>