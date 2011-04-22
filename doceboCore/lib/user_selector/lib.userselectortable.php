<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
|   DOCEBO - The E-Learning Suite                                           |
|                                                                           |
|   Copyright (c) 2008 (Docebo)                                             |
|   http://www.docebo.com                                                   |
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt        |
\ ======================================================================== */

class UserSelectorTable {

	public $id = '';
	
	public $use_form_input = false;

	public $initialSelection = array();

	public function __construct($id) {
		
		$this->id = $id;
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	}

	public function init() {
		YuiLib::load('container,menu,button,table,resize,selector');
		Util::get_js(Get::rel_path('base').'/lib/lib.elem_selector.js', true, true);
		Util::get_js(Get::rel_path('adm').'/lib/user_selector/lib.common.js', true, true);
		Util::get_js(Get::rel_path('adm').'/lib/user_selector/lib.userselectortable.js',true, true);
	}


  public function setInitialSelection($users) {
    if (is_array($users))
      $this->initialSelection = $users;
    elseif (is_string($users))
      $this->initialSelection = explode(",", $users);
	}
	

	private function getStandardFieldsList() {
		$fields = array(
			array('id'=>_STANDARD_FIELDS_PREFIX.'_0', 'name'=>Lang::t('_USERNAME', 'standard'),      'type'=>_FIELD_TYPE_TEXT, 'standard'=>true),
			array('id'=>_STANDARD_FIELDS_PREFIX.'_1', 'name'=>Lang::t('_FIRSTNAME', 'standard'),     'type'=>_FIELD_TYPE_TEXT, 'standard'=>true),
			array('id'=>_STANDARD_FIELDS_PREFIX.'_2', 'name'=>Lang::t('_LASTNAME', 'standard'),      'type'=>_FIELD_TYPE_TEXT, 'standard'=>true),
			array('id'=>_STANDARD_FIELDS_PREFIX.'_3', 'name'=>Lang::t('_EMAIL', 'standard'),         'type'=>_FIELD_TYPE_TEXT, 'standard'=>true),
			array('id'=>_STANDARD_FIELDS_PREFIX.'_4', 'name'=>Lang::t('_REGISTER_DATE', 'standard'), 'type'=>_FIELD_TYPE_DATE, 'standard'=>true)
		);
		return $fields;
	}
	
	public function get($domready = true, $tags = true) {
  
		$output = array();
		
		//to do: ufilter should not be global, this is now just for debug pourpose
		$fman = new FieldList();
		$fields = $fman->getFlatAllFields();
		
		$lang =& DoceboLanguage::createInstance('standard', 'framework');
		$temp=array(
      '{ id: "std_3", name: "'.Lang::t('_EMAIL').'" }',
      '{ id: "std_4", name: "'.Lang::t('_REGISTER_DATE').'" }'
    );
		foreach ($fields as $key=>$val) {
			$temp[] = '{ id: "cstm_'.$key.'", name: "'.$val.'"}';
		}
		
		$js_lang = 
		'var usertable_langs = {'.
		' _YES: "'.Lang::t('_YES', 'standard').'",'.
		' _NO: "'.Lang::t('_NO', 'standard').'"'.','.
		' _SELECTALL_TEXT: "'.Lang::t('_SELECT_ALL', 'standard').'",'.
		' _SELECTED: "'.Lang::t('_SELECTED_ELEMENT', 'standard').'",'.
		' _SEARCH: "'.Lang::t('_SEARCH', 'standard').'",'.
		' _RESET: "'.Lang::t('_RESET', 'standard').'",'.
		' _USERID: "'.Lang::t('_USERNAME', 'standard').'",'.
		' _FULLNAME: "'.Lang::t('_NAME', 'standard').'",'.
		' _SELECT_ALL: "'.Lang::t('_CMD_SELECT_ALL', 'standard').'",'.
		' _SELECT_PAGE: "'.Lang::t('_CMD_SELECT_PAGE', 'standard').'",'.
		' _UNSELECT_ALL: "'.Lang::t('_UNSELECT_ALL', 'standard').'",'.
		' _DESELECT_PAGE: "'.Lang::t('_CMD_DESELECT_PAGE').'",'.
		' _FIRST: "'.Lang::t('_START', 'standard').'",'.
		' _LAST: "'.Lang::t('_END', 'standard').'",'.
		' _PREV: "'.Lang::t('_PREV_STEP', 'standard').'",'.
		' _NEXT: "'.Lang::t('_NEXT_STEP', 'standard').'",'.
		' _ON: "'.Lang::t('_ON', 'standard').'"'.
		'};';
		
		$js_initsel = '';
		if (count($this->initialSelection)>0) {
		  $js_initsel = '['.implode(',', $this->initialSelection).']';
		}
		
		$output['js'] = ($tags ? '<script type="text/javascript">' : '').$js_lang
			
			.($domready ? '	YAHOO.util.Event.onDOMReady(function(e) { ' : '')
      .'    var ufilter = new UserSelectorTable("'.$this->id.'", {'."\n"
			.'		id: "'.$this->id.'",'."\n"
			.'		use_input: true,'."\n"
            .'		users_per_page: '.Get::sett('visuItem').','."\n"
            .'		user_fields: ['.implode(',', $temp).'],'."\n"
            .(count($this->initialSelection)>0 ? 'initial_selection: '.$js_initsel.','."\n" : '')
            .'    langs: usertable_langs,'."\n"
            .'		ajax_url: "'.$GLOBALS['where_framework_relative'].'/ajax.adm_server.php?plf=framework&file=userselectortable&sf=user_selector'.'"'."\n"
			.'	}); '
      .($domready ? '});'."\n" : '')
			.($tags ? '</script>' : '');
		
		$output['html'] = '<div id="'.$this->id.'"></div><input type="hidden" id="'.$this->id.'_input" name="'.$this->id.'_input" value="" />';
  
		return $output;
  
	}


}