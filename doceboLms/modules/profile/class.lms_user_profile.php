<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(_base_.'/lib/lib.user_profile.php');

/**
 * @category library
 * @package user_management
 * @subpackage profile
 *
 * This class will manage the action with performed by the profile (data access, view, etc.)
 */
class LmsUserProfile extends UserProfile {

	/**
	 * class constructor
	 */
	function LmsUserProfile($id_user, $edit_mode = false) {

		parent::UserProfile($id_user, $edit_mode);
	}

	// initialize functions ===========================================================

	/**
	 * instance the viewer class of the profile
	 */
	function initViewer($varname_action) {

		$this->_up_viewer = new LmsUserProfileViewer($this, $varname_action);
	}

}

// ========================================================================================================== //
// ========================================================================================================== //
// ========================================================================================================== //

/**
 * @category library
 * @package user_management
 * @subpackage profile
 *
 * This class will manage the display of the data readed by the
 */
class LmsUserProfileViewer extends UserProfileViewer {

	/**
	 * class constructor
	 */
	function LmsUserProfileViewer(&$user_profile, $varname_action) {

		parent::UserProfileViewer($user_profile, $varname_action);
	}

	/**
	 * print the title of the page
	 * @param mixed $text the title of the area, or the array with zone path and name
	 * @param string $image the image to load before the title
	 *
	 * @return string the html code for space open
	 */
	function getTitleArea($text = '', $image = '') {

		return '<div class="up_main">'
		 .'<h1>'.( $text != '' ? $this->_lang->def($text).': ' : '' ).$this->resolveUsername().'</h1>';
	 }

	/**
	 * Print the head of the module space after the getTitle area
	 * @return string the html code for space open
	 */
	function getHead() {

		return ''."\n";
	}

	/**
	 * Print the footer of the module space
	 * @return string the html code for space close
	 */
	function getFooter() {

		return '</div>'."\n";
	}

}


?>