<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @package admin-core
 * @subpackage language
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Lang extends Module {
	
	function useExtraMenu() {
		return true;
	}
	
	function loadExtraMenu() {
		loadAdminModuleLanguage($this->module_name);
		
	}

	function loadBody() {
		global $op, $modname, $prefix;
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		langDispatch( $op );
	}
	
	// Function for permission managment
	function getAllToken($op) {
		
		switch($op) {
			case "lang" : {
				return array( 
					'view' => array( 	'code' => 'view',
										'name' => '_VIEW',
										'image' => 'standard/view.png')
				);
			};break;
			case "importexport" : {
				return array( 
					'view' => array( 	'code' => 'view',
										'name' => '_VIEW',
										'image' => 'standard/view.png')
				);
			};break;
		}
	}
}

?>
