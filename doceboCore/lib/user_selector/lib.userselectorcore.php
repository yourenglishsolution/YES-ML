<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
|   DOCEBO - The E-Learning Suite                                           |
|                                                                           |
|   Copyright (c) 2008 (Docebo)                                             |
|   http://www.docebo.com                                                   |
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt          |
\ ======================================================================== */


require_once($GLOBALS['where_framework'].'/lib/user_selector/lib.fulluserselector.php');
require_once(_base_.'/lib/lib.json.php');

class UserSelectorCore {


  private $id = "";
  
  private $selection = array();
  
  //$id = the id of the selector
  public function __construct($id = "") { $this->id = $id; }


  private function _encode($mixed) {
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    if (!is_array($mixed)) return $this->_getVoidSelection(true);
    return $json->encode($mixed);
  }
  
  private function _decode($string) {
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    $output = ($string!="" ? $json->decode($string) : "");
    return (!$output || $output==null || $output="" ? $this->_getVoidSelection() : $output);
  }


  private function _getVoidSelection($encode = false, $id = '') {
    $temp = array(
      'id' => $id,
      'orgchart' => array(),
      'usertable'  => array(),
      'grouptable' => array(),
      'dynfilter'  => array('exclusive'=>true, 'filters'=>array()),
      'is_exclusive' => false
    );
    return ($encode ? $this->_encode($temp) : $temp);
  }

  public function getSelectorFilterString() {
    
    $data = array(
      'id' => $this->id,
      'orgchart' => Get::req($this->id."_"._ORGCHART_ID."_input", DOTY_MIXED, ""),
      'usertable'  => Get::req($this->id."_"._USERTABLE_ID."_input", DOTY_MIXED, ""),
      'grouptable' => Get::req($this->id."_"._GROUPTABLE_ID."_input", DOTY_MIXED, ""),
      'dynfilter'  => Get::req($this->id."_"._DYNFILTER_ID."_input", DOTY_MIXED, ""),
      'is_exclusive' => (Get::req($this->id.'_exclusive', DOTY_INT, 0)>0 ? true : false)
    );
    return $this->_encode($data);
  }


  public function getSelectorFilterData($string) {
    return $data = $this->_decode($string);
  }


  /**
   * this funcion read and solve the inputs of all 4 tabs of the selector 
   * and produce a list of user ids
   *
   */
  public function readSelectorInput($filter = false) {
    
    require_once($GLOBALS['where_framework'].'/lib/user_selector/lib.basetree.php');
    require_once($GLOBALS['where_framework'].'/lib/user_selector/lib.dynamicuserfilter.php');
    
    $acl = new DoceboACLManager();
    $org = new BaseTree($this->id."_"._ORGCHART_ID);
    $dyn = new DynamicUserFilter($this->id."_"._DYNFILTER_ID);
    
    if (!$filter) { //if filter string is not specified, use form inputs
    
      //$f_orgchart   = Get::req($this->id."_"._ORGCHART_ID."_input", DOTY_MIXED, "");
      $f_usertable  = Get::req($this->id."_"._USERTABLE_ID."_input", DOTY_MIXED, "");
      $f_grouptable = Get::req($this->id."_"._GROUPTABLE_ID."_input", DOTY_MIXED, "");
      //$f_dynfilter  = Get::req($this->id."_"._DYNFILTER_ID."_input", DOTY_MIXED, "");
   
      //solve the orgchart selection
      $arr_orgchart = $org->getUsers();//$acl->getAllUsersFromIdst( explode(',', $f_orgchart) );
    
      //solve the users selection
      $arr_usertable = explode(',', $f_usertable);
    
      //solve the group selection
      $arr_grouptable = $acl->getAllUsersFromIdst( explode(',', $f_grouptable) );
    
      //solve the dyn. filter selection
      $arr_dynfilter = $dyn->getUsers();
    
      $is_exclusive = (Get::req($this->id.'_exclusive', DOTY_INT, 0)>0 ? true : false);
    } else {
    
      $temp = $this->_decode($filter);
      if (!$temp) { return false; }
      
      //solve the orgchart selection
      $arr_orgchart = $org->getUsers($temp['orgchart']);//$acl->getAllUsersFromIdst( explode(',', $f_orgchart) );
    
      //solve the users selection
      $arr_usertable = explode(',', $temp['usertable']);
    
      //solve the group selection
      $arr_grouptable = $acl->getAllUsersFromIdst( explode(',', $temp['grouptable']) );
    
      //solve the dyn. filter selection
      $arr_dynfilter = $dyn->getUsers($temp['dynfilter']);
    
      $is_exclusive = $temp['is_exclusive'];
    }
    
    if ($is_exclusive) { 
      //intersect arrays
      $_selection = array_intersect($arr_orgchart, $arr_usertable, $arr_grouptable, $arr_dynfilter);
    } else {
      //merge arrays
      $_selection = array_merge($arr_orgchart, $arr_usertable, $arr_grouptable, $arr_dynfilter);
    }
    
    //eliminates duplicates and set the selection in the object's cache
    $this->selection = array_unique($_selection);
    return true;
  }


  public function isVoidSelection($selection) {
    $output = true;
    if ($selection != "" && is_string($selection)) {
      if ( count($this->readSelectorInput($selection))>0 ) $output = false;
    }
    return $output;
  }


  public function &getSelectedUsers() {
    return $this->selection;
  }
  
  public function isUserInSelection($user, $filter=false) {
    if ($filter) $this->readSelectorInput($filter);
	else return true;
    return in_array($user, $this->selection);
  }



}



?>