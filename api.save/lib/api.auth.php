<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(_base_.'/api/lib/lib.api.php');

/**
 * Manage token authentication
 */
class Auth_API extends API {

	public function __construct() {
		//do not request auth code or won't log user at beginning
		parent::__construct();
		$this->needAuthentication = false;
	}

	/**
	 * Get information about the authetication mode
	 */
	final protected function getAuthenticationMethod() {
		$result = '';
		switch (Get::sett('rest_auth_method')) {
			case _AUTH_UCODE: { $result = _AUTH_UCODE_DESC; } break;
			case _AUTH_TOKEN: { $result = _AUTH_TOKEN_DESC; } break;
		}
		$mode = array( 'success'=>($result != ''), 'auth_mode'=>$result );
		return $mode;
	}

	/**
	 * Log user and generate a token
	 * @param <string> $username username of the user that we want to authenticate
	 * @param <string> $password password of the user that we want to authenticate
	 * @return <array> the auth token for the session
	 */
	protected function generateToken($username, $password, $third_party =false) {

		$query = "SELECT * FROM %adm_user WHERE "
			." userid = '".$this->aclManager->absoluteId($username)."' AND "
			." pass = '".$this->aclManager->encrypt($password)."'";
		$res = $this->db->query($query);

		$result = false;
		if($this->db->num_rows($res) > 0) {
			
			if($third_party != false) {
				
				$query = "SELECT * FROM %adm_user WHERE "
					." userid = '".$this->aclManager->absoluteId($third_party)."'";
				$res = $this->db->query($query);
				
			}
			
			$row = $this->db->fetch_obj($res);
			
			$timenow = time();
			$now = date("Y-m-d H:i:s", $timenow);
			$level = $this->aclManager->getUserLevelId($row->idst);
			$token = md5(uniqid(rand(), true) + $username);

			$lifetime = Get::sett('rest_auth_lifetime', 1) * 60;
			$expire = date("Y-m-d H:i:s", $timenow + $lifetime) ;

			// check if the user is already authenticate
			$query = "SELECT * FROM %adm_rest_authentication WHERE id_user = ".$row->idst;
			$res = $this->db->query($query);
			if ($this->db->num_rows($res) > 0) {

				// update log table, if so, than re-authenticate it
				$query = "UPDATE %adm_rest_authentication ".
					" SET token='$token', generation_date='".$now."', last_enter_date=NULL, expiry_date='".$expire."' ".
					" WHERE id_user=".$row->idst;
				$res = $this->db->query($query);
			} else {

				// set authentication in DB
				$query = "INSERT INTO %adm_rest_authentication ".
					"(id_user,user_level, token, generation_date, last_enter_date, expiry_date) VALUES ".
					"('".$row->idst."', '".$level."', '".$token."', '".$now."', NULL, '".$expire."')";
				$res = $this->db->query($query);
			}
			$result = array('success'=>true, 'token'=>$token, 'expire_at'=>$expire);
		} else {
			$result = array('success'=>false, 'message'=>'Error: invalid auth.');
		}
		return $result;
	}


	public function getauthmethod($params) {
		return $this->getAuthenticationMethod();
	}

	public function authenticate($params) {
		
		$auth_method = Get::sett('rest_auth_method', 'none');
		if($auth_method != _REST_AUTH_TOKEN) {
		
			return array('success'=>false, 'message'=>'Error: Tokens are not used on this installation.');
		}
		$username = Get::req('username', DOTY_STRING, false);
		$password = Get::req('password', DOTY_STRING, false);
		$third_party = Get::req('third_party', DOTY_STRING, false);


		if ($username == false || $password === false) {
			//error: no login data provided
			return array('success'=>false, 'message'=>'Error: invalid login data provided.');
		} else {

			$res = $this->generateToken($username, $password, $third_party);
			if ($res['success']) {
				$output = array(
					'success'=>true,
					'message'=>'You are authenticated.',
					'token'=>$res['token'],
					'expire_at'=>$res['expire_at']
				);
			} else {
				$output = array('success'=>false, 'message'=>'Error: invalid user.');
			}
			return $output;
		}
	}

}
