<?php

class DB {
	
	protected $link = NULL;
	
	protected $dbName = NULL;
	
	public function __construct($username, $password, $server, $database) {
		$this->link = mysql_connect($server, $username, $password, TRUE) or die("Cannot connect to '$server'\n");
		mysql_select_db($database, $this->link);
		$this->dbName = $database;
	}
	
	public function query($query) {
		$res = mysql_query($query, $this->link);
		if (mysql_error($this->link)) {
			echo $query;
			throw new Exception('MYSQL ERROR: ' . mysql_errno($this->link) .': ' . mysql_error($this->link));
		}
		return $res;
	}
	
	public function fetchAll($query, $repository = null) {
		$res = $this->query($query);
		$result = array();
		while ($row = mysql_fetch_assoc($res)) {
			$class = $repository ? str_replace('Repository', '', $repository) : '';
			$result[] = $class ? new $class($row) : $row;
		}
		return $result;
	}
	
	public function fetchFirst($query, $repository = null) {
		$rows = $this->fetchAll($query, $repository);
		if (count($rows)) {
			return $rows[0];
		}
		return null;
	}
	
	public function insert($table, $data) {
		$params = array();
		foreach ($data as $key => $value) {
			$value = "'".addslashes($value)."'";
			$params[$key] = $value;
		}
		$query = "INSERT INTO $table (".implode(',', array_keys($params)).") VALUES (".implode(',', array_values($params)).")";
		$this->query($query);
		$id = mysql_insert_id($this->link);
		return $id;
	}
	
	public function update($table, $params, $where) {
		$parts = array();
		foreach ($params as $key => $value) {
			$parts[] = "$key='" . addslashes($value) . "'";
		}
		$set = implode(',', $parts);
		$query = "UPDATE $table SET $set WHERE $where";
		return $this->query($query);
	}
	
	public function delete($table, $where) {
		$query = "DELETE FROM $table WHERE $where";
		return $this->query($query);
	}
	
	public function begin() {
		$this->query('BEGIN');
	}
	
	public function rollback() {
		$this->query('ROLLBACK');
	}
	
	public function commit() {
		$this->query('COMMIT');
	}
}

?>