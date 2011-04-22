<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

//use single user-code authentication
define('_AUTH_UCODE', 0);
//use generated token authentication
define('_AUTH_TOKEN', 1); 

define('_AUTH_UCODE_DESC', 'SINGLE_CODE');
define('_AUTH_TOKEN_DESC', 'GENERATED_TOKEN');

define('_REST_OUTPUT_XML', 'xml');
define('_REST_OUTPUT_JSON', 'json');

// xml export function
define('_XML_VERSION', '1.0');
define('_XML_ENCODING', 'UTF-8');
define('_GENERIC_ELEMENT', 'element');

class API {

	protected $db;
	protected $aclManager;

	protected $needAuthentication = true;
	protected $authenticated = false;
	protected $buffer = "";

	public function __construct() {
		$this->db = DbConn::getInstance();
		$this->aclManager = Docebo::user()->getAclManager();
	}

	/**
	 * Returns the buffer
	 * @return <return>
	 */
	public function get() { return $this->_buffer; }

	/**
	 * Writes in the buffer
	 * @param <string> $string
	 */
	protected function _write( $string ) { $this->_buffer .= $string; }

	/**
	 * Empty the buffer
	 */
	public function flush() { $this->_buffer = ""; }

	public function authenticateUser($username, $password) {
		$acl_man = Docebo::user()->getAclManager();
		$query="SELECT * FROM %adm_user "
			."WHERE userid='".$this->aclManager->absoluteId($username)."' AND pass='".$this->aclManager->encrypt($password)."'";
		$res = $this->db->query($query);
		return ($this->db->num_rows($res) > 0);
	}

	/*
	 * Check user authentication
	 */
	final public function checkAuthentication($code) {
		//eliminates old token
		$query = "DELETE FROM %adm_rest_authentication WHERE expiry_date < NOW()";
		$res = $this->db->query($query);

		if (!$this->needAuthentication) {
			//no authentication needed for this module
			return true;
		}

		//load auth setting
		$auth_method = Get::sett('rest_auth_method', 'none');

		$result = false;
		switch ($auth_method) {

			// use application's pre-set authentication code
			case _AUTH_UCODE: {
				$auth_code = Get::sett('rest_auth_code', false);
				$result = ($code == $auth_code);
			} break;

			// search the token in  authentications DB table
			case _AUTH_TOKEN: {
				$query = "SELECT * FROM %adm_rest_authentication WHERE token='$code'";
				$res = $this->db->query($query);
				if($this->db->num_rows($res) > 0) {

					$now = time();
					$result = true;
					$query = "UPDATE %adm_rest_authentication SET last_enter_date='".date("Y-m-d H:i:s", $now)."' ";
					if (Get::sett('rest_auth_update', false)) {
						$lifetime = Get::sett('rest_auth_lifetime', 1) * 60;
						$query .= " , expiry_date='".date("Y-m-d H:i:s", $now + $lifetime)."' ";
					}
					$query .= " WHERE token='$code'";
					$this->db->query($query);

				} else {
					$result = false;
				}
			} break;

			default: {}

		}
		return $result;
	}


	public function call($name, $params) {

		return $this->$name($params);
	}

	static public function Execute($auth_code, $module, $function, $params) {
		
		$class_name = $module.'_API';

		require_once(_base_.'/api/lib/api.'.$module.'.php');
		$api_obj = new $class_name();

		$result = false;
		if ($api_obj->checkAuthentication($auth_code)) {
			$result = $api_obj->call($function, $params);
		}

		return $result;
	}

	/**
	 * Return the array of nested element into an xml format
	 * @param array $arr
	 * @return string the xml formatted output
	 */
	static public function getXML($arr) {

		$output='';
		if (is_array($arr)) {
			$output.='<?xml version="'._XML_VERSION.'" encoding="'._XML_ENCODING.'"?>';
			$output.='<XMLoutput>';
			self::convert($output, $arr);
			$output.='</XMLoutput>';
		}
		return $output;
	}

	static protected function getopentag($tagkey) {

		$output  = '<';
		if (is_numeric($tagkey)) $output.=_GENERIC_ELEMENT; else $output.=$tagkey;
		$output .= '>';
		return $output;
	}

	static protected function getclosetag($tagkey) {

		$output  = '</';
		if (is_numeric($tagkey)) $output.=_GENERIC_ELEMENT; else $output.=$tagkey;
		$output .= '>';
		return $output;
	}

	static protected function getstringval(&$value) {

		$output='';
		if (is_bool($value)) {
			switch ($value) {
				case true:  $output.='true';  break;
				case false: $output.='false'; break;
			}
		} else {
			$output.=$value;
		}
		return $output;
	}

	static protected function convert(&$out, &$data) {

		if (!is_array($data)) return;
		foreach ($data as $key => $val) {
			$out .= self::getopentag($key);
			if(is_array($val)) self::convert($out, $val);
			else $out.= self::getstringval($val);
			$out .= self::getclosetag($key);
		}
	}


	/**
	 * Check if the params array contains information about the external user;
	 * if found the idst value of the array will be overwritten with the
	 * data found.
	 * @param <array> $params
	 * @return <array>
	 */
	public function checkExternalUser($params, $data) {

		if (!empty($data['ext_user']) && !empty($data['ext_user_type'])) {
			$pref_path ='ext.user.'.$data['ext_user_type'];
			$pref_val ='ext_user_'.$data['ext_user_type']."_".(int)$data['ext_user'];

			$res =$this->aclManager->getUsersBySetting($pref_path, $pref_val);

			if (count($res) > 0) {
				$params[0]=$res[0];
				$params['idst']=$res[0];				
			}
			else {
				$params[0]=0;
				$params['ext_not_found']=true;

				// this will be useful for example for the createUser method..
				$params['ext_user']=$data['ext_user'];
				$params['ext_user_type']=$data['ext_user_type'];
			}
		}

		return $params;
	}

}
