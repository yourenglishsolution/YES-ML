<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */


class PrecompileLmsController extends LmsController {
	public $name = 'precompile';

	protected $model;
	protected $json;
	protected $jump_url;

	public function init() {
		$this->model = new PrecompileLms();

		require_once(_base_.'/lib/lib.json.php');
		$this->json = new Services_JSON();

		$this->jump_url = 'index.php?r=lms/catalog/show';//'index.php?r=elearning/show';
	}


	/**
	 * The main screen, show the policy text if it has not been acccepted yet,
	 * otherwise show the mandatory fields to compile (if set) then jump to Lms
	 */
	public function showTask() {
		require_once(_adm_.'/lib/lib.field.php');
		$fieldlist = new FieldList();
		
		$result_message = "";
		$res = Get::req('res', DOTY_ALPHANUM, "");
		switch ($res) {
			case "err": $result_message .= UIFeedback::notice(Lang::t('_OPERATION_FAILURE', 'standard'), true);
		}

		//$policy_checked = $this->model->getAcceptingPolicy($id_user);
		$fields_checked = $fieldlist->checkUserMandatoryFields(false);

		if ($this->model->compileRequired()) {
			$this->render('show', array(
				'result_message' => $result_message,
				//'policy_checked' => $policy_checked,
				'fields_checked' => $fields_checked,
				'policy_text' => $this->model->getPrivacyPolicyText(),
				'id_user' => Docebo::user()->getIdSt(),
				'fieldlist' => $fieldlist
			));
		} else {
			Util::jump_to($this->jump_url);
		}
	}


	/**
	 * Set the new fields and policy acceptance, than jump to the proper page
	 */
	public function set() {
		$id_user = Docebo::user()->getIdst();

		require_once(_adm_.'/lib/lib.field.php');
		$fl = new FieldList();
		$fl->storeFieldsForUser($id_user);

		$accept_policy = Get::req('accept_policy', DOTY_INT, 0) > 0;
		$this->model->setAcceptingPolicy($id_user, $accept_policy);

		$policy_checked = $this->model->getAcceptingPolicy($id_user);
		$fields_checked = $fl->checkUserMandatoryFields($id_user);

		if ($fields_checked && $policy_checked) {
			//send alert
			Util::jump_to($this->jump_url);
		} else {
			//send alert
			Util::jump_to('index.php?r=precompile/show&res=err');
		}
	}

}

?>