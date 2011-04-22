<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Docebo {

	private $_current_user = false;

	private $_lang_manager = false;

	private function  __construct() {}

	public static function init() {

		self::$_current_user = false;
		self::$_current_course = false;
	}

	/**
	 * Return an object that describe the current user logged in
	 * @return DoceboUser
	 */
	public static function user() {
		return $GLOBALS['current_user'];
	}

	/**
	 * Return an object that describe the current acl
	 * @return DoceboAcl
	 */
	public static function acl() {
		return $GLOBALS['current_user']->getAcl();
	}

	/**
	 * Return an object that describe the current aclmanager
	 * @return DoceboAclManager
	 */
	public static function aclm() {
		return $GLOBALS['current_user']->getAclManager();
	}

	/**
	 * Return an object that describe the current user logged in
	 * @return DoceboCourse
	 */
	public static function course() {
		return ( isset($GLOBALS['course_descriptor']) ? $GLOBALS['course_descriptor'] : false );
	}

	/**
	 * Return an object that describe the system languages
	 * @return DoceboLangManager
	 */
	public static function langManager() {
		return DoceboLangManager::getInstance();
	}

}