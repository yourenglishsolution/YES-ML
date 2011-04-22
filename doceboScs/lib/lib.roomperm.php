<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class RoomPermissions {

	var $prefix=NULL;
	var $dbconn=NULL;
	var $room_id="";
	var $module="";


	function RoomPermissions($room_id, $module, $prefix=FALSE, $dbconn=NULL) {
		$this->prefix = ($prefix !== FALSE ? $prefix : $GLOBALS["prefix_scs"]);
		$this->dbconn = $dbconn;
		$this->platform = Get::cur_plat();
		$this->room_id = (int)$room_id;
		$this->module = $module;
	}


	function _executeQuery( $query ) {
		if( $this->dbconn === NULL )
			$rs = sql_query( $query );
		else
			$rs = sql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
		if( $this->dbconn === NULL ) {
			if( !sql_query( $query ) )
				return FALSE;
		} else {
			if( !sql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	function _getPermTable() {
		return $this->prefix."_chatperm";
	}


	function getRoomId() {
		return (int)$this->room_id;
	}


	function setRoomId($room_id) {
		$this->room_id=(int)$room_id;
	}

	function getModule() {
		return $this->module;
	}


	function addPerm($perm, $idst_arr) {
		$res=TRUE;

		if (empty($perm))
			return FALSE;

		foreach($idst_arr as $user_idst) {
			$qtxt ="INSERT INTO ".$this->_getPermTable()." (room_id, module, user_idst, perm) ";
			$qtxt.="VALUES ('".$this->getRoomId()."', '".$this->getModule()."', '".$user_idst."', '".$perm."')";

			$q=$this->_executeQuery($qtxt);
			if (!$q)
				$res=FALSE;
		}

		return $res;
	}


	function removePerm($perm, $idst_arr) {
		$res=TRUE;

		if (empty($perm))
			return FALSE;

		if ((is_array($idst_arr)) && (count($idst_arr) > 0)) {

			$qtxt ="DELETE FROM ".$this->_getPermTable()." WHERE room_id='".$this->getRoomId()."' AND ";
			$qtxt.="module='".$this->getModule()."' AND perm='".$perm."' AND ";
			$qtxt.="user_idst IN (".implode(",", $idst_arr).")";

			$q=$this->_executeQuery($qtxt);
			if (!$q)
				$res=FALSE;
		}

		return $res;
	}


	function getAllPerm() {
		$res=array();

		$fields="user_idst, perm";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getPermTable()." WHERE ";
		$qtxt.="room_id='".$this->getRoomId()."' AND module='".$this->getModule()."'";

		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_assoc($q)) {

				$user_idst=$row["user_idst"];
				$perm=$row["perm"];
				$res[$perm][$user_idst]=$user_idst;

			}
		}

		return $res;
	}


}


?>
