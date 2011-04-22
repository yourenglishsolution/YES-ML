<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @package		Docebo
 * @subpackage	ImportExport
 * @version 	$Id: import.org_chart.php 977 2007-02-23 10:40:19Z fabio $
 * @author		Emanuele Sandri <emanuele (@) docebo (.) com>
**/
require_once($GLOBALS['where_framework'].'/lib/lib.import.php');
class ImportUser extends DoceboImport_Destination {

	var $last_error = NULL;
	var $mandatory_cols = array('userid');
	var $default_cols = array(	'firstname'=>'','lastname'=>'','pass'=>'',
								'email'=>'','avatar'=>'',
								'signature'=>'');
	var $ignore_cols = array( 'idst', 'avatar', 'lastenter', 'valid', 'pwd_expire_at', 'level', 'register_date', 'force_change',
		'facebook_id', 'twitter_id', 'linkedin_id', 'google_id', 'signature', 'privacy_policy' );
	var $valid_filed_type = array( 'textfield', 'date', 'dropdown', 'yesno', 'freetext', 'country',	'gmail', 'icq', 'msn', 'skype', 'yahoo', 'codicefiscale', 'country');
	var $cols_descriptor = NULL;
	var $dbconn = NULL;
	var $tree = 0;
	var $charset = '';

	var $idst_imported = array();

	/**
	 * constructor for docebo users destination connection
	 * @param array $params
	 *			- 'dbconn' => connection to database (required)
	 *			- 'tree' => The id of the destination folder on tree (required)
	**/
	function ImportUser( $params ) {
		$this->dbconn = $params['dbconn'];
		$this->tree = (int)$params['tree'];
	}

	function connect() {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		// load language for fields names
		$lang_dir =& DoceboLanguage::createInstance('admin_directory', 'framework');
		$acl =& Docebo::user()->getACL();
		$fl = new FieldList();
		$acl_manager = Docebo::user()->getAclManager();
		$this->idst_group = $acl_manager->getGroupST('oc_'.(int)$this->tree);
		$this->idst_desc = $acl_manager->getGroupST('ocd_'.(int)$this->tree);

		//$idst_group = 1;//$this->tree->tdb->getGroupST($this->tree->getSelectedFolderId());
		//$idst_desc = 2;//$this->tree->tdb->getGroupDescendantsST($this->tree->getSelectedFolderId());
		$arr_idst_all = $acl->getArrSTGroupsST(array($this->idst_group,$this->idst_desc));
		$arr_fields = $fl->getFieldsFromIdst($arr_idst_all);

		$this->cols_descriptor = NULL;
		if( $this->dbconn === NULL ) {
			$this->last_error = Lang::t('_ORG_IMPORT_ERR_DBCONNISNULL');
			return FALSE;
		}
		$query = "SHOW FIELDS FROM ".$GLOBALS['prefix_fw']."_user";
		$rs = sql_query( $query, $this->dbconn );
		if( $rs === FALSE ) {
			$this->last_error = Lang::t('_ORG_IMPORT_ERR_ERRORONQUERY').$query.' ['.mysql_error($this->dbconn).']';
			return FALSE;
		}
		$this->cols_descriptor = array();
		while( $field_info = mysql_fetch_array($rs) ) {
			if( !in_array($field_info['Field'],$this->ignore_cols) ) {
				$mandatory = in_array($field_info['Field'],$this->mandatory_cols);
				if( isset($this->default_cols[$field_info['Field']])) {
					$this->cols_descriptor[] =
								array(  DOCEBOIMPORT_COLNAME => $lang_dir->def('_DIRECTORY_FILTER_'.$field_info['Field']),
										DOCEBOIMPORT_COLID => $field_info['Field'],
										DOCEBOIMPORT_COLMANDATORY => $mandatory,
										DOCEBOIMPORT_DATATYPE => $field_info['Type'],
										DOCEBOIMPORT_DEFAULT => $this->default_cols[$field_info['Field']]
										);
				} else {
					$this->cols_descriptor[] =
								array(  DOCEBOIMPORT_COLNAME => $lang_dir->def('_DIRECTORY_FILTER_'.$field_info['Field']),
										DOCEBOIMPORT_COLID => $field_info['Field'],
										DOCEBOIMPORT_COLMANDATORY => $mandatory,
										DOCEBOIMPORT_DATATYPE => $field_info['Type']
										);
				}
			}
		}

		mysql_free_result( $rs );

		foreach($arr_fields as $field_id => $field_info) {
			if( in_array($field_info[FIELD_INFO_TYPE],$this->valid_filed_type) ) {
				$this->cols_descriptor[] =
							array(  DOCEBOIMPORT_COLNAME => $field_info[FIELD_INFO_TRANSLATION],
									DOCEBOIMPORT_COLID => $field_id,
									DOCEBOIMPORT_COLMANDATORY => FALSE,
									DOCEBOIMPORT_DATATYPE => 'text',
									);
			}
		}

		return TRUE;

	}

	function close() {}

	function get_tot_cols(){
		return count( $this->cols_descriptor );
	}

	function get_cols_descripor() {
		return $this->cols_descriptor;
	}

	/**
	 * @return integer the number of mandatory columns to import
	**/
	function get_tot_mandatory_cols() {
		$result = array();
		foreach( $this->cols_descriptor as $col ) {
			if( $col[DOCEBOIMPORT_COLMANDATORY] )
				$result[] = $col;
		}
		return count($result);
	}

	function _convert_char( $text ) {
		if( function_exists('mb_convert_encoding') ) {
			return mb_convert_encoding($text, 'UTF-8', $this->charset);
		} else {
			return utf8_encode($text);
		}
	}

	/**
	 * @param array data to insert; is an array with keys the names of cols and
	 *				values the data
	 * @return TRUE if the row was succesfully inserted, FALSE otherwise
	**/
	function add_row( $row ) {
		$acl =& Docebo::user()->getACL();
		$acl_manager =& Docebo::user()->getAclManager();

		$userid = addslashes($this->_convert_char($row['userid']));
		
		$firstname = addslashes($this->_convert_char($row['firstname']));
		$lastname = addslashes($this->_convert_char($row['lastname'])); 

		$pass = addslashes($this->_convert_char($row['pass']));
		$email = addslashes($this->_convert_char($row['email']));
		$idst = $acl_manager->registerUser( 	$userid, $firstname, $lastname,
														$pass, $email, '',
														'');
		if($idst !== false) {
			$this->idst_imported[$idst] = $idst;
			//  -- Add user to registered users group if not importing into root ---

			$idst_oc 			= $acl_manager->getGroup(false, '/oc_0');
			$idst_oc 			= $idst_oc[ACL_INFO_IDST];

			$idst_ocd 			= $acl_manager->getGroup(false, '/ocd_0');
			$idst_ocd 			= $idst_ocd[ACL_INFO_IDST];

			if ($this->idst_group != $idst_oc)
				$acl_manager->addToGroup($idst_oc, $idst);

			if ($this->idst_desc != $idst_ocd)
				$acl_manager->addToGroup($idst_ocd, $idst);

			//  -------------------------------------------------------------------|

			$result = TRUE;
			$acl_manager->addToGroup($this->idst_group,$idst );
			$acl_manager->addToGroup($this->idst_desc,$idst );

			// add to group level
			$userlevel = $acl_manager->getGroupST(ADMIN_GROUP_USER);
			$acl_manager->addToGroup($userlevel,$idst );

			//-save extra field------------------------------------------
			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
			$fl = new FieldList();
			$arr_idst_all = $acl->getArrSTGroupsST(array($this->idst_group,$this->idst_desc));
			$arr_fields = $fl->getFieldsFromIdst($arr_idst_all);
			$arr_fields_toset = array();
			foreach( $arr_fields as $field_id => $field_info) {
				if( isset($row[$field_id]) ) {
					$arr_fields_toset[$field_id] = $this->_convert_char($row[$field_id]);
				}
			}
			if( count($arr_fields_toset) > 0 )
				$result = $fl->storeDirectFieldsForUser($idst, $arr_fields_toset, false);
			//-----------------------------------------------------------
			if( !$result ) {
				$this->last_error = Lang::t('_ORG_IMPORT_ERR_STORECUSTOMFIELDS');
			}
			return $result;
		} else {
			$this->last_error = Lang::t('_OPERATION_FAILURE');
			return FALSE;
		}
	}

	function getNewImportedIdst() {

		return $this->idst_imported;
	}

	function set_charset( $charset ) { $this->charset = $charset; }


	function get_error() {
		return $this->last_error;
	}
}

class ImportGroupUser extends DoceboImport_Destination {

	var $last_error = NULL;
	var $cols_id			= array('userid', 'groupid');
	var $cols_default		= array();
	var $cols_mandatory		= array('userid', 'groupid');
	var $cols_type			= array('userid' => 'text', 'groupid' => 'text');
	var $cols_descriptor 	= array();
	var $dbconn = NULL;
	var $charset = '';
	
	var $acl_man;
	
	var $group_cache = array();
	var $user_cache = array();

	/**
	 * constructor for docebo users destination connection
	 * @param array $params
	 *			- 'dbconn' => connection to database (required)
	 *			- 'tree' => The id of the destination folder on tree (required)
	**/
	function ImportGroupUser( $params ) {
		$this->dbconn = $params['dbconn'];
		$this->acl_man 	=& Docebo::user()->getAclManager();
	}

	function connect() {
		
		$this->cols_descriptor = array();
		foreach($this->cols_id as $k => $field_id) {

			$mandatory = in_array($field_id, $this->cols_mandatory);
				
			if( in_array($field_id, $this->cols_default)) {
				
				$this->cols_descriptor[] = array(  
					DOCEBOIMPORT_COLNAME 		=> Lang::t('_GROUPUSER_'.$field_id, 'organization_chart', 'framework'),
					DOCEBOIMPORT_COLID 			=> $field_id,
					DOCEBOIMPORT_COLMANDATORY 	=> in_array($field_id, $this->cols_mandatory),
					DOCEBOIMPORT_DATATYPE 		=> $this->cols_type[$field_id],
					DOCEBOIMPORT_DEFAULT => $this->default_cols[$field_id]
				);
			} else {
				
				$this->cols_descriptor[] = array(  
					DOCEBOIMPORT_COLNAME 		=> Lang::t('_GROUPUSER_'.$field_id, 'organization_chart', 'framework'),
					DOCEBOIMPORT_COLID 			=> $field_id,
					DOCEBOIMPORT_COLMANDATORY 	=> in_array($field_id, $this->cols_mandatory),
					DOCEBOIMPORT_DATATYPE 		=> $this->cols_type[$field_id]
				);
			}
		}
		return TRUE;
	}

	function close() {}

	function get_tot_cols(){
		return count( $this->cols_descriptor );
	}

	function get_cols_descripor() {
		return $this->cols_descriptor;
	}

	/**
	 * @return integer the number of mandatory columns to import
	**/
	function get_tot_mandatory_cols() {
		
		return count( $this->cols_mandatory );
	}

	function _convert_char( $text ) {
		if( function_exists('mb_convert_encoding') ) {
			return mb_convert_encoding($text, 'UTF-8', $this->charset);
		} else {
			return utf8_encode($text);
		}
	}

	/**
	 * @param array data to insert; is an array with keys the names of cols and
	 *				values the data
	 * @return TRUE if the row was succesfully inserted, FALSE otherwise
	**/
	function add_row( $row ) {

		while(list($k, $v) = each($row)) {
			
			$row[$k] = mysql_escape_string($v);
		}
		reset($row);
		// find the group idst
		$group_idst = array_search($row['groupid'], $this->group_cache);
		if($group_idst === NULL || $group_idst === false) {
			
			$group = $this->acl_man->getGroup(false, $row['groupid']);
			$this->group_cache[$group[ACL_INFO_IDST]] = $row['groupid'];
			$group_idst = $group[ACL_INFO_IDST];
		}
		if($group_idst == false) {
			// the group doesn't exist
			$this->last_error = Lang::t('_GROUP_IMPORT_ERR_GROUP_DOESNT_EXIST', 'org_chart', 'framework');
			return false;
		}
		// find the user idst
		$user_idst = array_search($row['userid'], $this->user_cache);
		if($user_idst === NULL || $user_idst === false) {
			
			$user = $this->acl_man->getUser(false, $row['userid']);
			$this->user_cache[$user[ACL_INFO_IDST]] = $row['userid'];
			$user_idst = $user[ACL_INFO_IDST];
		}
		if($user_idst == false) {
			// the user doesn't exist
			$this->last_error = Lang::t('_GROUP_IMPORT_ERR_USER_DOESNT_EXIST', 'org_chart', 'framework');
			return false;
		}
		
		$result = $this->acl_man->addToGroup( $group_idst, $user_idst );
		if( !$result ) {
			$this->last_error = Lang::t('_GROUP_IMPORT_ERR_SUBSCRIPTION', 'org_chart', 'framework');
		}
		return true;
	}

	function set_charset( $charset ) { $this->charset = $charset; }


	function get_error() {
		return $this->last_error;
	}
}

?>