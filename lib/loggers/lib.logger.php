<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Log {
	
	private static $_arr_log = array();
	
	public static function add($str) {
		
		self::$_arr_log[] = $str;
	}
	
	public static function get_log() {
		
		return self::$_arr_log;
	}

	public static function debug() {

		while(list($n, $entry) = each(self::$_arr_log)) {
			echo $n.') '.$entry."<br>\n";
		}
		die();
	}
}

?>