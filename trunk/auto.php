<?php

require_once('classes/Autoload.php');

function __autoload($className) {
	$classes = Autoload::getClasses();

	if ($classes[$className] && file_exists($classes[$className])) {
		require_once($classes[$className]);
	}
}

?>