<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Module_Scorm extends LmsModule {

	//class constructor
	function Module_Scorm($module_name = '') {
		//EFFECTS: if a module_name is passed use it else use global reference
		global $modname;
		
		parent::LmsModule();
	}
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op;
		//echo '<link href="'.getPathTemplate().'style/base-old-treeview.css" rel="stylesheet" type="text/css" />'."\n";
		switch($op) {
			case "category" : {
				//echo '<link href="'.getPathTemplate().'style/base-old-treeview.css" rel="stylesheet" type="text/css" />'."\n";
			};break;
		}
		return;
	}
	function loadBody() {
		//EFFECTS: include module language and module main file

		//if( version_compare(phpversion(), "5.0.0") == -1 )
			include($GLOBALS['where_lms'].'/modules/scorm/'.$this->module_name.'.php');
		//else
		//	include($GLOBALS['where_lms'].'/modules/scorm5/'.$this->module_name.'.php');
	}
}



?>
