<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Module_Faq extends LmsModule {

	function hideLateralMenu() {
		
		if(isset($_SESSION['test_assessment'])) return true;
		if(isset($_SESSION['direct_play'])) return true;
		return false;
	}
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		
		switch($GLOBALS['op']) {
			case "insfaqcat" :
			case "newfaq" :
			case "insfaq" : 
			
			case "modfaqcat" :
			case "upfaqcat" :
			case "modfaq" :
			case "upfaq" : {
				loadHeaderHTMLEditor();
			};break;
		}
		return;
	}
	
	function useExtraMenu() {
		return false;
	}
	
	function loadExtraMenu() {
		
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file
		
		switch($GLOBALS['op']) {
			case "play" : {
				$idCategory = importVar('idCategory', true, 0);
				$id_param = importVar('id_param', true, 0);
				$back_url = importVar('back_url');
				
				$object_faq = createLO( 'faq', $idCategory );
				$object_faq->play( $idCategory, $id_param, urldecode( $back_url ) );
			};break;
			default : {
				parent::loadBody();
			}
		}
	}
}

?>