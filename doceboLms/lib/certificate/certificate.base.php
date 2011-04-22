<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class CertificateSubstitution {

	var $id_user;
	
	var $id_course;

	function CertificateSubstitution($id_user, $id_course) {
		
		$this->id_user = $id_user;
		$this->id_course = $id_course;
	}
	
	function getSubstitution() {
		
		return array();
	}
	
	function getSubstitutionTags() {
		
		return array();
	}
	
}

?>