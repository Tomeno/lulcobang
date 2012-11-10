<?php

class Log {
	public static function logQuery($query) {
		$file = fopen(dirname(__FILE__) . '/../logs/queries.log', 'a');
		fwrite($file, $query . "\n\n\n");
		fclose($file);
	}
}
?>