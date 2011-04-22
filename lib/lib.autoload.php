<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * Definition of php magic __autoload() method
 * @param <string> $classname the classname that php are tring to istanciate
 * @return not used
 */
function docebo_autoload($classname) {

	// purify the request
	$classname = preg_replace('/[^a-zA-Z0-9\-\_]+/', '', $classname);

	// fixed bases classes
	$fixed = array(
		// Layout
		'Layout'			=> _lib_.'/layout/lib.layout.php',
		'CmsLayout'			=> _lib_.'/layout/lib.cmslayout.php',
		'LoginLayout'		=> _lib_.'/layout/lib.loginlayout.php',

		// mvc
		'Model'				=> _lib_.'/mvc/lib.model.php',
		'TreeModel'			=> _lib_.'/mvc/lib.treemodel.php',

		'Controller'		=> _lib_.'/mvc/lib.controller.php',
		'LmsController'		=> _lib_.'/mvc/lib.lmscontroller.php',
		'CmsController'		=> _lib_.'/mvc/lib.cmscontroller.php',
		'AdmController'		=> _lib_.'/mvc/lib.admcontroller.php',
		'AlmsController'	=> _lib_.'/mvc/lib.almscontroller.php',
		'AcmsController'	=> _lib_.'/mvc/lib.acmscontroller.php',

		// db
		'DbConn'			=> _base_.'/db/lib.docebodb.php',
		'Mysql_DbConn'		=> _base_.'/db/drivers/docebodb.mysql.php',
		'Mysqli_DbConn'		=> _base_.'/db/drivers/docebodb.mysqli.php',

		// i18n
		'Lang'				=> _i18n_.'/lib.lang.php',
		'DoceboLanguage'	=> _i18n_.'/lib.lang.php',
		'Format'			=> _i18n_.'/lib.format.php',

		// form file
		'Form'				=> _lib_.'/lib.form.php',
		'DForm'				=> _lib_.'/forms/lib.dform.php',

		// lib files
		'Acl'				=> _lib_.'/lib.acl.php',
		'AclManager'		=> _lib_.'/lib.aclmanager.php',

		// widget
		'Widget'			=> _base_.'/widget/lib.widget.php',
	);
	//search for a base class and include the file if found
	if(isset($fixed[$classname])) {
		if(file_exists($fixed[$classname])) include_once($fixed[$classname]);
		return;
	}

	//possibile path for autoloading classes
	$path = array(
		'adm' => array(
			_adm_.'/models',
			_adm_.'/controllers'
		),
		'alms' => array(
			_lms_.'/admin/models',
			_lms_.'/admin/controllers'
		),
		'lms' => array(
			_lms_.'/models',
			_lms_.'/controllers'
		),
		'acms' => array(
			_cms_.'/admin/models',
			_cms_.'/admin/controllers'
		),
		'cms' => array(
			_cms_.'/models',
			_cms_.'/controllers'
		)
	);

	//parse classname for info and path
	$location = array();
	if(preg_match ('/(Adm|Alms|Lms|Acms|Cms)/', $classname, $location)) {
		$loc = 'adm';
		if(isset($location[1])){
			$loc = strtolower($location[1]);
			if(!isset($path[$loc])) $loc = 'adm';
		}
		if(strpos($classname, 'Controller') !== false) {
			// include controller file
			$c_file = $path[$loc][1].'/'.$classname.'.php';
			if(file_exists($c_file)) include_once($c_file);
			return;
		} else {
			// include model file
			$c_file = $path[$loc][0].'/'.$classname.'.php';
			if(file_exists($c_file)) include_once($c_file);
			return;
		}
	}
	// manage widgets classnames
	if(preg_match ('/(Widget)/', $classname, $location)) {
		$loc = _base_.'/widget/'.strtolower(str_replace(array('WidgetController', 'Widget'), array('', ''), $classname));
		if(strpos($classname, 'Controller') !== false) {
			// include controller file
			$c_file = $loc.'/controller/'.$classname.'.php';
			if(file_exists($c_file)) include_once($c_file);
			return;
		} else { //if(strpos($classname, 'Model') !== false) {
			// include model file
			$c_file = $loc.'/model/'.$classname.'.php';
			if(file_exists($c_file)) include_once($c_file);
			return;
		}
	}
	// search for a standard filename in the library
	if(file_exists(_lib_.'/lib.'.strtolower($classname).'.php')) {
		include_once(_lib_.'/lib.'.strtolower($classname).'.php');
		return;
	}

	// unable to autoload
}

spl_autoload_register('docebo_autoload');
