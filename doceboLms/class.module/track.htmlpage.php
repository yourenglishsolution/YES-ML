<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once($GLOBALS['where_lms'].'/class.module/track.object.php');

class Track_Htmlpage extends Track_Object {
	
	function Track_Htmlpage( $idTrack ) {
		$this->objectType = 'htmlpage';
		parent::Track_Object($idTrack);
	}

	function getIdTrack( $idReference, $idUser, $idResource, $createOnFail = FALSE ) {
		
		$query = "SELECT idTrack FROM ".$GLOBALS['prefix_lms']."_materials_track"
				." WHERE idReference='".(int)$idReference."'"
				."   AND idUser='".(int)$idUser."'";

		$rs = sql_query( $query )
			or errorCommunication( 'getIdTrack:: '.$query );
		if( mysql_num_rows( $rs )  > 0 ) {
			list( $idTrack ) = sql_fetch_row( $rs );
			return array( TRUE, $idTrack );
		} else if( $createOnFail ) {
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_materials_track"
					."( idResource, idReference, idUser ) VALUES ("
					."'".(int)$idResource."','".(int)$idReference."','".(int)$idUser."')";
			sql_query( $query )
				or errorCommunication( 'getIdTrack' );
			$idTrack = mysql_insert_id();
			return array( FALSE, $idTrack );
		}
		return FALSE;
	}

}

?>
