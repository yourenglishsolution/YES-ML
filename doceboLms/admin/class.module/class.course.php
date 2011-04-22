<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @package  DoceboLms
 * @version  $Id: class.course.php 1003 2007-03-31 13:59:46Z fabio $
 * @category Category
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Course extends LmsAdminModule {
	
	function loadBody() {
		
		require_once(dirname(__FILE__).'/../modules/course/course.php');
		courseDispatch($GLOBALS['op']);
	}	
	
	// Function for permission managment
	
	function getAllToken($op) {
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
								'image' => 'standard/delete.png'),
			'subscribe' => array( 'code' => 'subscribe',
								'name' => '_SUBSCRIBE',
								'image' => 'subscribe/add_subscribe.gif'),
			'moderate' => array( 	'code' => 'moderate',
								'name' => '_MODERATE',
								'image' => 'standard/moderate.gif'),
		);
	}
}

?>
