<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class PrecompileLms extends Model {

	protected $db;
	protected $error;
	protected $pmodel;

	public function __construct() {
		$this->db = DbConn::getInstance();
		$this->error = false;
		$this->pmodel = new PrivacypolicyAdm();
	}

	public function compileRequired() {
		$check_privacy_policy = Get::sett('request_mandatory_fields_compilation', 'off') != 'off';
		$check_mandatory_fields = Get::sett('request_mandatory_fields_compilation', 'off') != 'off';

		$id_user = Docebo::user()->getIdSt();
		$policy_checked = $this->getAcceptingPolicy($id_user);

		require_once(_adm_.'/lib/lib.field.php');
		$fieldlist = new FieldList();

		$fields_checked = $fieldlist->checkUserMandatoryFields($id_user);
		
		return (!$policy_checked || !$fields_checked);
	}

	/**
	 * Retrieve the privacy policy text for the current user, given a specific language code
	 * @param string $language the language code to use, current language by default
	 * @return string 
	 */
	public function getPrivacyPolicyText($language = FALSE) {
		//initialize output
		$output = FALSE;
		if (!$language) $language = Lang::get();
		$id_user = Docebo::user()->getIdSt();

		//retrieve the translation from DB
		$pmodel = new PrivacypolicyAdm();
		$policies = $pmodel->getUserPolicy($id_user);
		if (!empty($policies)) {
			$output = "";
			$id_policy = $policies[0]; //the user may have more than one policy, get the first one
			$pinfo = $pmodel->getPolicyInfo($id_policy);
			if (isset($pinfo->translations[Lang::get()]))
				$output = $pinfo->translations[Lang::get()];
		}

		return $output;
	}

	/**
	 * Set if the user has accepted the privacy policy or not
	 * @param integer $id_user the idst of the user who is accepting/refusing privacy policy
	 * @param boolean $accepted true if the policy has been accepted by the user, false otherwise
	 * @return boolean
	 */
	public function setAcceptingPolicy($id_user, $accepted = TRUE) {
		//check input values
		if ((int)$id_user <= 0) return FALSE;

		//set value in DB
		$query = "UPDATE %adm_user SET privacy_policy = ".($accepted ? "1" : "0")
			." WHERE idst = ".(int)$id_user;
		$res = $this->db->query($query);

		return $res ? TRUE : FALSE;
	}


	/**
	 * Check if the user has accepted the privacy policy or not yet
	 * @param integer $id_user the idst of the user to check
	 * @return boolean
	 */
	public function getAcceptingPolicy($id_user) {
		//check input values
		if ((int)$id_user <= 0) return FALSE;

		//read value in DB
		$output = FALSE;
		$query = "SELECT privacy_policy FROM %adm_user WHERE idst = ".(int)$id_user;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res) > 0) {
			list($value) = $this->db->fetch_row($res);
			if ($value > 0) $output = TRUE;
		}

		return $output;
	}


}