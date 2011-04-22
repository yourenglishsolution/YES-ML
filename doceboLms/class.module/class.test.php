<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Module_Test extends LmsModule {
	
	function hideLateralMenu() {
		
		if(isset($_SESSION['test_assessment'])) return true;
		if(isset($_SESSION['direct_play'])) return true;
		return false;
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file
		
		switch($GLOBALS['op']) {
			case "play" : {
				$idTest = importVar('id_test', true, 0);
				$id_param = importVar('id_param', true, 0);
				$back_url = importVar('back_url');
				
				$object_poll = createLO( 'test', $idTest );
				$object_poll->play( $idTest, $id_param, unserialize(urldecode($back_url)) );
			};break;
			default : {
				parent::loadBody();
			}
		}
	}
}

?>