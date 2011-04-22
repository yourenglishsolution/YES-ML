<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
|   DOCEBO - The E-Learning Suite                                           |
|                                                                           |
|   Copyright (c) 2008 (Docebo)                                             |
|   http://www.docebo.com                                                   |
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt          |
\ ======================================================================== */

class GroupSelectorTable {

	public $id = '';
	
	public $use_form_input = false;

  public $initialSelection = array();

	public function __construct($id) {
		
		$this->id = $id;
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	}

	public function init() {
		YuiLib::load(/*array(
			'json'=>'json-min.js',
			'container'=>'container_core-min.js', //menu
			'menu'=>'menu-min.js', //menu
			'button'=>'button-min.js', //dialog
			'container'=>'container-min.js', //dialog
			'button'=>'button-min.js', //dialog
			'datasource'=>'datasource-beta-min.js',
			'datatable'=>'datatable-beta-min.js',
			'resize'=>'resize-beta-min.js',
			'selector'=>'selector-beta-min.js'),
			array(
			'assets/skins/sam' => 'skin.css'
			)*/
			'base,container,menu,button,table,resize,selector'
		);
		
		//addJs($GLOBALS['where_framework_relative'].'/lib/', 'lib.elem_selector.js');

		Util::get_js(Get::rel_path('base').'/lib/lib.elem_selector.js', true,true);
		addJs($GLOBALS['where_framework_relative'].'/lib/user_selector/', 'lib.common.js');
		addJs($GLOBALS['where_framework_relative'].'/lib/user_selector/', 'lib.groupselectortable.js');		
	}

	
	public function setInitialSelection($groups) {
    if (is_array($groups))
      $this->initialSelection = $groups;
    elseif (is_string($groups))
      $this->initialSelection = explode(",", $groups);	
	}
	
	
	public function get($domready = true, $tags = true) {
  
    $lang =& DoceboLanguage::createInstance('standard', 'framework');
		$output = array();
		
		$js_lang = 
		'var grouptable_langs = {'.
		' _YES: "'.$lang->def('_YES').'",'.
		' _NO: "'.$lang->def('_NO').'",'.
		' _SELECTALL_TEXT: "'.$lang->def('_SELECT_ALL').'",'.
		' _SELECTED: "'.$lang->def('_SELECTED_ELEMENT').'",'.
		' _SEARCH: "'.$lang->def('_SEARCH').'",'.
		' _RESET: "'.$lang->def('_RESET').'",'.
		' _NAME: "'.$lang->def('_NAME').'",'.
		' _DESCRIPTION: "'.$lang->def('_DESCRIPTION').'",'.
		' _SELECT_ALL: "'.$lang->def('_SELECT_ALL').'",'.
		' _SELECT_PAGE: "'.$lang->def('_CMD_SELECT_PAGE').'",'.
		' _DESELECT_ALL: "'.$lang->def('_UNSELECT_ALL').'",'.
		' _DESELECT_PAGE: "'.$lang->def('_CMD_DESELECT_PAGE').'",'.
		' _FIRST: "'.$lang->def('_START').'",'.
		' _LAST: "'.$lang->def('_END').'",'.
		' _PREV: "'.$lang->def('_PREV_STEP').'",'.
		' _NEXT: "'.$lang->def('_NEXT_STEP').'",'.
		' _ON: "'.$lang->def('_ON').'"'.
		'};';
		
		$js_initsel = '';
		if (count($this->initialSelection)>0) {
		  $js_initsel = '['.implode(',', $this->initialSelection).']';
		}
		
		$output['js'] = ($tags ? '<script type="text/javascript">' : '').$js_lang
			.($domready ? '	YAHOO.util.Event.onDOMReady(function(e) { ' : '')
      .'    var gfilter = new GroupSelectorTable("'.$this->id.'", {'."\n"
			.'		id: "'.$this->id.'",'."\n"
			.'		use_input: true,'."\n"
            .'		groups_per_page: '.Get::sett('visuItem').','."\n"
            .'		langs: grouptable_langs,'."\n"
            .(count($this->initialSelection)>0 ? 'initial_selection: '.$js_initsel.','."\n" : '')
            .'		ajax_url: "'.$GLOBALS['where_framework_relative'].'/ajax.adm_server.php?plf=framework&file=groupselectortable&sf=user_selector'.'"'."\n"
			.'	}); '
      .($domready ? '});'."\n" : '')
			.($tags ? '</script>' : '');
		
		$output['html'] = '<div id="'.$this->id.'"></div><input type="hidden" id="'.$this->id.'_input" name="'.$this->id.'_input" value="" />';
  
		return $output;
  
	}


}