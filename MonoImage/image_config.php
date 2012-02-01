<?php

// standart require
require_once(dirname(__FILE__).'/class.MonoImage.php');
require_once(dirname(__FILE__).'/class.ImageAppManager.php');

// require transform factories you would like to use
//require_once(dirname(__FILE__).'/MonoImage/transforms/class.DummyTransformFactory.php');
require_once(dirname(__FILE__).'/transforms/class.MaxTransformFactory.php');
require_once(dirname(__FILE__).'/transforms/class.BoxTransformFactory.php');
require_once(dirname(__FILE__).'/transforms/class.TrimTransformFactory.php');
//require_once(dirname(__FILE__).'/MonoImage/transforms/class.WatermarkTransformFactory.php');

// require save factory what you would like to use
require_once(dirname(__FILE__).'/saves/class.DirectoryKeySaveFactory.php');
//require_once(dirname(__FILE__).'/MonoImage/saves/class.AlphabetSaveFactory.php');

/**
 * Premenna nastavuje ci sa budu pouzivat loklane obrazky alebo obtrazky z nod
 */
$GLOBALS['use_local_images'] = true;


$GLOBALS['image_manager'] = new ImageAppManager(new DirectoryKeySaveFactory());

?>
