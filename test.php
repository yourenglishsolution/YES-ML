<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

define("IN_DOCEBO", true);
define("_deeppath_", '');
require(dirname(__FILE__).'/base.php');


require(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_USER);

// mise en place des répertoires et chargement des classes
set_include_path('.'
. PATH_SEPARATOR . _base_.'/lib/'
. PATH_SEPARATOR . _base_.'/lib/Zend/'
. PATH_SEPARATOR . get_include_path());

// On insère manuellement l'autoloader
include_once _base_."/lib/Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();


require(_base_.'/lib/tools/MyPdf.php');
$pdf = new MyPdf();

Zend_Debug::dump($pdf);