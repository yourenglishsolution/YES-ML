<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(_adm_.'/lib/user_selector/lib.basetree.php');
require_once(_adm_.'/lib/user_selector/lib.groupselectortable.php');
require_once(_adm_.'/lib/user_selector/lib.userselectortable.php');
require_once(_adm_.'/lib/user_selector/lib.dynamicuserfilter.php');

define("_TAB_ORGCHART",   0);
define("_TAB_USERTABLE",  1);
define("_TAB_GROUPTABLE", 2);
define("_TAB_DYNFILTER",  3);

define("_ORGCHART_ID", "orgchart");
define("_USERTABLE_ID", "usertable");
define("_GROUPTABLE_ID", "grouptable");
define("_DYNFILTER_ID", "dynfilter");

class FullUserSelector {

	private $id = '';

	private $initialTab = _TAB_USERTABLE;

	private $_selectors = false;

	function __construct($id) {
		$this->id = $id;
		$this->_selectors = array(
			_ORGCHART_ID   => new BaseTree($this->id."_"._ORGCHART_ID, false, false, _TREE_COLUMNS_TYPE_RADIO),
			_USERTABLE_ID  => new UserSelectorTable($this->id."_"._USERTABLE_ID),
			_GROUPTABLE_ID => new GroupSelectorTable($this->id."_"._GROUPTABLE_ID),
			_DYNFILTER_ID  => new DynamicUserFilter($this->id."_"._DYNFILTER_ID)
		);
	}

	function &getSelectorObj($which) {
		$output = false;
		switch ($which) {
			case _ORGCHART_ID   : $output = $this->_selectors[_ORGCHART_ID]; break;
			case _USERTABLE_ID  : $output = $this->_selectors[_USERTABLE_ID]; break;
			case _GROUPTABLE_ID : $output = $this->_selectors[_GROUPTABLE_ID]; break;
			case _DYNFILTER_ID  : $output = $this->_selectors[_DYNFILTER_ID]; break;
			default: {}
		}
		return $output;
	}

	function setInitialSelection($which, &$data) {
		$output = true;
		switch ($which) {
			case _ORGCHART_ID   : $this->getSelectorObj(_ORGCHART_ID)->setInitialSelection($data); break;
			case _USERTABLE_ID  : $this->getSelectorObj(_USERTABLE_ID)->setInitialSelection($data); break;
			case _GROUPTABLE_ID : $this->getSelectorObj(_GROUPTABLE_ID)->setInitialSelection($data); break;
			case _DYNFILTER_ID  : $this->getSelectorObj(_DYNFILTER_ID)->setInitialSelection($data); break;
			default: { $output = false; }
		}
		return $output;
	}

	function init() {
		YuiLib::load('base,tabview');
		Util::get_js(Get::rel_path('base').'/lib/lib.elem_selector.js', true,true);
	}

	/**
	 * This function set an initial selection of values, which will be pre-selected in the treeview
	 * @param $data = an array of selected elements
	 * @return void
	 */
	function setInitialTab($tab) {
		switch($tab) {
			case _TAB_ORGCHART:
			case _TAB_USERTABLE:
			case _TAB_GROUPTABLE:
			case _TAB_DYNFILTER: $this->initialTab = $tab; break;
			default: $this->initialTab = _TAB_USERTABLE;
		}
	}

	/**
	 * This function create the initialization data needed to load the selector and the markup
	 * @return array 'js' -> the js used to setup the tree, 'html' => thd html markup needed for the tree
	 */
	function get($domready = true, $tags = true) {

		$lang =& DoceboLanguage::createInstance('standard', 'framework');

		$bt = $this->getSelectorObj(_ORGCHART_ID);
		$bt->init();
		$bt_out = $bt->get(false, false);

		$ust = $this->getSelectorObj(_USERTABLE_ID);;
		$ust->init();
		$ust_out = $ust->get(false, false);

		$gst = $this->getSelectorObj(_GROUPTABLE_ID);;
		$gst->init();
		$gst_out = $gst->get(false, false);

		$duf = $this->getSelectorObj(_DYNFILTER_ID);;
		$duf->init();
		$duf_out = $duf->get(false, false);

		$scripts =
		$bt_out['js']."\n\n".$gst_out['js']."\n\n".$ust_out['js']."\n\n".$duf_out['js'];

		$tab0 = $this->id.'_tab0';
		$tab1 = $this->id.'_tab1';
		$tab2 = $this->id.'_tab2';
		$tab3 = $this->id.'_tab3';

		$output = array();

		$output['html'] =
		'<div id="'.$this->id.'" class="yui-navset">
			<ul class="yui-nav">
				<li'.($this->initialTab == _TAB_ORGCHART   ? ' class="selected"' : '').'><a href="#'.$tab0.'"><em>'.$lang->def('_ORGCHART').'</em></a></li>
				<li'.($this->initialTab == _TAB_USERTABLE  ? ' class="selected"' : '').'><a href="#'.$tab1.'"><em>'.$lang->def('_USERS').'</em></a></li>
				<li'.($this->initialTab == _TAB_GROUPTABLE ? ' class="selected"' : '').'><a href="#'.$tab2.'"><em>'.$lang->def('_GROUPS').'</em></a></li>
				<li'.($this->initialTab == _TAB_DYNFILTER  ? ' class="selected"' : '').'><a href="#'.$tab3.'"><em>'.$lang->def('_RULES').'</em></a></li>
			</ul>
		<div class="yui-content">
				<div id="'.$tab0.'">'.$bt_out['html'].'</div>
				<div id="'.$tab1.'">'.$ust_out['html'].'</div>
				<div id="'.$tab2.'">'.$gst_out['html'].'</div>
				<div id="'.$tab3.'">'.$duf_out['html'].'</div>
			</div>
		</div>';

		$function = 'var tabView_'.$this->id.' = new YAHOO.widget.TabView("'.$this->id.'"); ';
		$output['js'] = ($tags ? '<script type="text/javascript">' : '').
			($domready
				? 'YAHOO.util.Event.onDOMReady(function() { '.$function.' '.$scripts.' });'
				: $function.' '.$scripts
			).($tags ? '</script>' : '');

		return $output;

	}

}

?>