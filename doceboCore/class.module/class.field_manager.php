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
 * @subpackage field
 */
 
require_once(dirname(__FILE__).'/class.definition.php');

class Module_Field_Manager extends Module {
	
	function loadBody() {
		
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		
	}
	
	function getAllToken() {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.png'),
			
			'add' => array( 	'code' => 'add',
								'name' => '_ADD',
								'image' => 'standard/add.png'),
			
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/edit.png'),
			
			'del' => array( 	'code' => 'del',
								'name' => '_DEL',
								'image' => 'standard/delete.png')
		);
	}
}

?>
