<?php

class Log {
	public static function logQuery($query) {
		$file = fopen(dirname(__FILE__) . '/../logs/queries.log', 'a');
		fwrite($file, $query . "\n\n\n");
		fclose($file);
	}
	
	public static function logAiAction($action) {
		$file = fopen(dirname(__FILE__) . '/../logs/ai_actions.log', 'a');
		fwrite($file, $action . "\n\n\n");
		fclose($file);
	}
	
	public static function command($command) {
		$file = fopen(dirname(__FILE__) . '/../logs/commands.log', 'a');
		fwrite($file, $command . "\n\n\n");
		fclose($file);
	}
}
?>