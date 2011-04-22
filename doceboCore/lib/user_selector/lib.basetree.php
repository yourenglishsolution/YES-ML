<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */



define("_TREE_COLUMNS_TYPE_CHECK", "checkbox");
define("_TREE_COLUMNS_TYPE_RADIO", "radiobutton");

/**
 * Ajax Tree base class
 */


class BaseTree {

	public $id = 'base_tree';

	public $ajax_url = '';

	public $preload_tree = false;

	public $root_name = false;

	public $sel_columns = "";

	public $initial_selection = array();

	public $use_form_input = true;

	public $draggable_nodes = false;

	public $use_context_menu = false;


	function __construct($id, $url = false, $preload = false, $cols_type = "") {

		$this->id = $id;

		$this->ajax_url = ( $url
			? $url
			: 'ajax.adm_server.php?plf=framework&file=basetree&sf=user_selector' );

		$this->preload_tree = $preload;
		$this->sel_columns = $cols_type;
	}

	function init() {

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
			'tabview'=>'tabview-min.js',
			'treeview'=>'treeview-min.js'),
			array(
			'assets/skins/sam' => 'skin.css'
			)*/
			'base,container,menu,button,table,tree'
		);
		addCss('folder_tree', 'framework');
		addJs($GLOBALS['where_framework_relative'].'/lib/user_selector/', 'lib.basetree.js');
		//addJs($GLOBALS['where_framework_relative'].'/lib/', 'lib.elem_selector.js');
		Util::get_js(Get::rel_path('base').'/lib/lib.elem_selector.js', true,true);
	}

	/**
	 * This function set an initial selection of values, which will be pre-selected in the treeview
	 * @param $data = an array of selected elements
	 * @return void
	 */
	function setInitialSelection($data=array()) {
		if (is_array($data)) {
			$this->initial_selection = $data;
		} elseif (is_string($data)) {
		  $this->initial_selection = explode(",", $data);
		} elseif (is_int($data)) {
		  $this->initial_selection = array($data);
		}
	}

	//format ids or whatever is being used as strings
	private function formatInitialSelection() {
		$output = array();
		foreach ($this->initial_selection as $val) {
			$output[] = '"'.$val.'"';
		}
		return implode(',', $output);
	}


	/**
	 * This function create the initialization data needed to load the tree and the markup
	 * @return array 'js' -> the js used to setup the tree, 'html' => thd html markup needed for the tree
	 */
	function get($domready = true, $tags = true) {

		require_once($GLOBALS['where_framework'].'/lib/user_selector/lib.treeuser.php');
		$tree = new TreeUserManager();

		$lang =& DoceboLanguage::createInstance('organization_chart', 'framework');
		$lang =& DoceboLanguage::createInstance('treeview', 'framework');

		/*
		$first_level = $tree->getNodesById(ROOT_NODE_ID);


		$temp = array();
		foreach ($first_level as $node) {
			$temp[] = '{id:"'.$node['id'].'", label:"'.$node['label'].'", is_leaf:'.($node['is_leaf'] ? 'true' : 'false').', count_content:0}';
		}
		$tree_data = implode(',', $temp);
		*/

		if(!$this->root_name) $this->root_name = $lang->def('_ROOT');
		
		$out = array();
		$out['js'] = ($tags ? '<script type="text/javascript">'."\n" : '').

			//init the language
			'var _lang_tree = {'.
			'_SAVE: "'.$lang->def('_SAVE').'",'.
			'_CONFIRM: "'.$lang->def('_CONFIRM').'",'.
			'_UNDO: "'.$lang->def('_UNDO').'",'.
			'_AREYOUSURE: "'.$lang->def('_AREYOUSURE').'",'.
			'_NAME: "'.$lang->def('_NAME').'",'.
			'_ADD: "'.$lang->def('_ADD').'",'.
			'_MOD: "'.$lang->def('_MOD').'",'.
			'_DEL: "'.$lang->def('_DEL').'",'.
		
			'_YES: "'.$lang->def('_YES').'",'.
			'_NO: "'.$lang->def('_NO').'",'.
			'_INHERIT: "'.$lang->def('_ORG_CHART_INHERIT', 'organization_chart').'",'.

			'_ROOT: "'.$this->root_name.'",'.
			'_NEW_FOLDER: "'.$lang->def('_NEW_FOLDER').'",'.
			'_DEL: "'.$lang->def('_DEL').'",'.

			'_AJAX_FAILURE: "'.$lang->def('_DEL').'"'.
			' };'.
			"\n\n".
			// init the tree
			($domready ? ' YAHOO.util.Event.onDOMReady(function(e) {' : '').
			'var BaseTree = new SelectorTree("'.$this->id.'", {'."\n".
			'     tree_id: "'.$this->id.'",'."\n".
			'     request_url: "'.$this->ajax_url.'",'."\n".
			//'     tree_data: ['.$tree_data.'],'."\n".
			'     selector_columns_type: "'.$this->sel_columns.'",'."\n".
			'     use_form_input: '.($this->use_form_input ? 'true' : 'false').','."\n".
			'     draggable_nodes: '.($this->draggable_nodes ? 'true' : 'false').','."\n".
			'     use_context_menu: '.($this->use_context_menu ? 'true' : 'false').','."\n".
			'     langs: _lang_tree,'."\n".
			'     initial_selection: ['.$this->formatInitialSelection().']'."\n".
			'   }'."\n".
			'); '.($domready ? '} );' : '').
			($tags ? '</script>' : '');

		$out['html'] = '<div class="tree_container">'.
			/*'<div id="'.$this->id.'_root" class="ygtvroot">'.
				'<span>'.( $this->root_name ? $this->root_name : $lang->def("_ROOT") ).'</span>'.
			'</div>'.*/
			'<div class="folder_status_message"><div id="'.$this->id.'_status"></div></div>'.
			'<div class="folder_tree" id="'.$this->id.'"></div>'.
			($this->use_form_input ? '<input type="hidden" id="'.$this->id.'_input" name="'.$this->id.'_input" />' : '').
			'</div>';

		return $out;
	}



  function getUsers($param = false) {
  
    $temp = ($param ? $param : Get::req($this->id."_input", DOTY_MIXED, false) );
    $nodes = explode(',', $temp);
    
    $output = array();
    $branches = array();
    foreach ($nodes as $node) {
      
      if (stristr($node, "d")) {
        $branches[] = '/ocd_'.str_replace('d', '', $node);
      } else {
        $branches[] = '/oc_'.$node;
      }
    
    }
    
    $acl = new DoceboACLManager();
    $groups = $acl->getGroupsIdByPaths( $branches );
    $output = $acl->getAllUsersFromIdst( $groups );
    return $output;
  }

}


?>