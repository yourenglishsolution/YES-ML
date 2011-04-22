<?php

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

define("LMS", true);
define("IN_DOCEBO", true);
define("_deeppath_", '../');
require(dirname(__FILE__).'/../base.php');

// start buffer
ob_start();

// initialize
require(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_PAGE_WR);

// mise en place des répertoires et chargement des classes
set_include_path('.'
. PATH_SEPARATOR . _base_.'/lib/'
. PATH_SEPARATOR . _base_.'/lib/Zend/'
. PATH_SEPARATOR . get_include_path());

// On insère manuellement l'autoloader
include_once _base_."/lib/Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();

// connect to the database
$db =& DbConn::getInstance();

// some specific lib to load
require_once(_lms_.'/lib/lib.istance.php');
require_once(_lms_.'/lib/lib.permission.php');
require_once(_lms_.'/lib/lib.track_user.php');
require_once(_lms_.'/class.module/class.definition.php');

// -----------------------------------------------------------------------------

$module_cfg = false;
$GLOBALS['modname'] = Get::req('modname', DOTY_ALPHANUM, '');
$GLOBALS['op']		= Get::req('op', DOTY_ALPHANUM, '');
$GLOBALS['req']		= Get::req('r', DOTY_MIXED, '');

if(empty($GLOBALS['modname']) && empty($GLOBALS['op']) && empty($GLOBALS['req'])) $GLOBALS['req'] = 'elearning/show';
YuiLib::activateConnectLoadingBox();

// instanciate the page-writer that we want (maybe we can rewrite it in a
// different way with the introduction of the layout manager)
if(isset($_GET['no_redirect']) || isset($_POST['no_redirect'])) {

	onecolPageWriter::createInstance();
} elseif(!isset($_SESSION['idCourse']) && !Docebo::user()->isAnonymous()) {

	onecolPageWriter::createInstance();
} elseif($module_cfg !== false && $module_cfg->hideLateralMenu()) {

	onecolPageWriter::createInstance();
} else {

	require_once(_lms_.'/lib/lib.lmspagewriter.php');
	LmsPageWriter::createInstance();
}

require_once(_lms_.'/lib/lib.preoperation.php');
require_once(_lms_.'/lib/lib.module.php');

// create the class for management the called module
if(!empty($GLOBALS['modname'])) {
	$module_cfg =& createModule($GLOBALS['modname']);
	if(method_exists($module_cfg, 'beforeLoad')) $module_cfg->beforeLoad();
}

// header
if($module_cfg !== false && $module_cfg->hideLateralMenu()) {

	require(_lms_.'/menu/menu_over.php');
} else {
	if(!Docebo::user()->isAnonymous()) {

		require(_lms_.'/menu/menu_over.php');
		if(isset($_SESSION['idCourse'])) {
			require(_lms_.'/menu/menu_lat.php');
		}
	} else {

		require(_lms_.'/menu/menu_login.php');
	}
}

// New MVC structure
if(!empty($GLOBALS['req'])) {

	$GLOBALS['req'] = preg_replace('/[^a-zA-Z0-9\-\_\/]+/', '', $GLOBALS['req']);
	$r = explode('/', $GLOBALS['req']);
	if(count($r) == 3) {
		// Position, class and method defined in the path requested
		$mvc_class = ucfirst(strtolower($r[1])). ucfirst(strtolower($r[0])).'Controller';
		$mvc_name = $r[1];
		$task = $r[2];
	} else {
		// Only class and method defined in the path requested
		$mvc_class = ''.ucfirst(strtolower($r[0])).'LmsController';
		$mvc_name = $r[0];
		$task = $r[1];
	}
	ob_clean();
	$controller = new $mvc_class( $mvc_name );
	$controller->request($task);

	$GLOBALS['page']->add(ob_get_contents(), 'content');
	ob_clean();

} else {
 	// load module body
	if(!empty($GLOBALS['modname'])) {
		$module_cfg->loadBody();
	}
	
}
// -----------------------------------------------------------------------------

// finalize
Boot::finalize();

// remove all the echo and put them in the debug zone
$GLOBALS['page']->add(ob_get_contents(), 'debug');
ob_clean();

// layout
/* Modifié le 18/03/2011 par polo (ajout du IF render none) + modif du else */
/**********************************
* 
* YES SAS - Your English Solution
* Author : Polo
* Created Date : 18/03/11
* Modified Date : 18/03/11
* Version : 1.0
* Description : Modification du IF (ajout du none et du non Admin)
* 
***********************************/
if($GLOBALS['modname'] == 'pages') Layout::render('none');
elseif(Docebo::user()->isAdmin()) Layout::render( ( isset($_SESSION['idCourse']) ? 'lms' : 'lms_user' ) );
else Layout::render( 'lms_user' );

// flush buffer
ob_end_flush();

?>