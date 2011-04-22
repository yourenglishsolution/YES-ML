<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class TablefilterWidget extends Widget {

	public $id = '';
	public $filter_text = "";
	public $auxiliary_filter = "";
	public $common_options = "";
	public $advanced_filter_content = false;
	public $advanced_filter_active = false;
	
	public $js_callback_set = false;
	public $js_callback_reset = false;

	protected $json = null;


	public function __construct() {
		parent::__construct();
		$this->_widget = 'tablefilter';
	}

	public function init() {
		//Util::get_js(Get::rel_path('base').'/lib/js_utils.js', true, true);
	}

	public function run() {
		if (!$this->id) {
			//..
			return false;
		}

		//render view
		$this->render('tablefilter', array(
			'id' => $this->id,
			'filter_text' => (string)$this->filter_text,
			'auxiliary_filter' => $this->auxiliary_filter,
			'common_options' => $this->common_options,
			'advanced_filter_content' =>  $this->advanced_filter_content,
			'advanced_filter_active' => $this->advanced_filter_active,
			'js_callback_set' => $this->js_callback_set,
			'js_callback_reset' => $this->js_callback_reset
		));
	}

}

?>