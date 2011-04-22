<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Man_Advice {
	
	function getCountUnreaded($id_user, $courses, &$last_access) {
		
		if(empty($courses)) return array();
		
		$unreaded = array();
		$query_unreaded = "
		SELECT idCourse, UNIX_TIMESTAMP(posted) 
		FROM ".$GLOBALS['prefix_lms']."_advice 
		WHERE author <> '".$id_user."' AND idCourse IN ( ".implode(',', $courses)." ) ";
		$re_advice = sql_query($query_unreaded);
		if(!mysql_num_rows($re_advice)) return array();
		
		while(list($id_c, $posted) = sql_fetch_row($re_advice)) {
			
			if(!isset($last_access[$id_c])) {
				
				if(isset($unreaded[$id_c])) $unreaded[$id_c]++;
				else $unreaded[$id_c] = 1;
			} elseif($posted > $last_access[$id_c]) {
				
				if(isset($unreaded[$id_c])) $unreaded[$id_c]++;
				else $unreaded[$id_c] = 1;
			}
		}
		return $unreaded;
	}
	
}

?>