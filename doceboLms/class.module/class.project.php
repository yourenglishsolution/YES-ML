<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Module_Project extends LmsModule {
	
	function loadBody() {
		require_once($GLOBALS['where_lms'].'/modules/project/project.php');
		projectDispatch($GLOBALS['op']);
	}
	
	function useExtraMenu() {
		return false;
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.png'),
			'add' => array( 	'code' => 'add',
								'name' => '_ALT',
								'image' => 'standard/add.png'),								
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/edit.png'),								
			'del' => array( 	'code' => 'del',
								'name' => '_DEL',
								'image' => 'standard/delete.png')
		);
	}

	function getPermissionsForMenu($op) {
		return array(
			1 => $this->selectPerm($op, 'view'),
			2 => $this->selectPerm($op, 'view'),
			3 => $this->selectPerm($op, 'view'),
			4 => $this->selectPerm($op, 'view'),
			5 => $this->selectPerm($op, 'view,mod'),
			6 => $this->selectPerm($op, 'view,add,mod,del'),
			7 => $this->selectPerm($op, 'view,add,mod,del')
		);
	}
	
}

?>