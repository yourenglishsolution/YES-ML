<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

define("_OTHERFIELD_ID_LANGUAGE", 0);
define("_OTHERFIELD_ID_COURSESTATUS", 1);
define("_OTHERFIELD_ID_ADMINLEVELS", 2);

define("_OTHERFIELD_TYPE_LANGUAGE", 'language');
define("_OTHERFIELD_TYPE_COURSESTATUS", 'coursestatus');
define("_OTHERFIELD_TYPE_ADMINLEVELS", 'adminlevels');

class OtherFieldsTypes {

	protected $db;
	protected $acl_man;

  public function __construct() {
		$this->db = DbConn::getInstance();
		$this->acl_man = Docebo::user()->getAclManager();
	}
  
  
  public function getInitData($js = true) {
    //produces languages' list variable
    $temp1 = array( '{ id: "standard", value: "[ '. Lang::t('_DEFAULT_LANGUAGE').' ]" }' );
    foreach (Docebo::langManager()->getAllLanguages() as $lang) {
			$temp1[] = '{ id: "'.$lang[0].'", value: "'.$lang[1].'" }';
		}
    
    //produce courses' list variable
    $temp2 = array();
    $res = $this->db->query("SELECT idCourse, code, name FROM %lms_course");
    while ( list($idCourse, $code, $name) = $this->db->fetch_row($res) ) {
      $temp2[] ='{ id: '.$idCourse.', value: "'.$name.'" }';
    }

		//produce admin levels list
		$temp3 = array();
		$arr_admin_levels = $this->acl_man->getAdminLevels();
		foreach ($arr_admin_levels as $lev=>$idst) {
			$temp3[] = '{ id: "'.$lev.'", value: "'.Lang::t('_DIRECTORY_'.$lev, 'admin_directory').'" }';
		}

    if (!$js) {
      $output = array( 
				'languages' => $temp1,
				'courses' => $temp2,
				'levels' => $temp3
			);
    } else {
      $js_langs = "[".implode(",", $temp1)."]";
      $js_courses = "[".implode(",", $temp2)."]";
			$js_levels = "[".implode(",", $temp3)."]";
      $output = array(
				'languages' => $js_langs,
				'courses' => $js_courses,
				'levels' => $js_levels
			);
    }
    
    return $output;
  }
  
  
  public function getOtherFieldsList() {
    $list = array(
      array('id'=>'oth_'._OTHERFIELD_ID_LANGUAGE, 'name'=>Lang::t('_LANGUAGE', 'standard'), 'type'=>_OTHERFIELD_TYPE_LANGUAGE, 'standard'=>false),
      //array('id'=>'oth_'._OTHERFIELD_ID_COURSESTATUS, 'name'=>Lang::t('_STATUS', 'profile'), 'type'=>_OTHERFIELD_TYPE_COURSESTATUS, 'standard'=>false),
			array('id'=>'oth_'._OTHERFIELD_ID_ADMINLEVELS, 'name'=>Lang::t('_LEVEL', 'admin_directory'), 'type'=>_OTHERFIELD_TYPE_ADMINLEVELS, 'standard'=>false)
    );
    
    return $list;
  }
  
  
  
  
  
  public function checkUserField($id_field, $id_user, $filter) {    
		$output = false;
  
    switch ($id_field) {
    
      case _OTHERFIELD_ID_LANGUAGE: {
        if ($filter == "standard") {
          $temp = array();
          $query = "SELECT id_user FROM %adm_setting_user WHERE path_name='ui.language'";
          $res = $this->db->query($query);
          while ( list($idst) = $this->db->fetch_row($res) ) { $temp[] = $idst; }
          $output = !in_array($id_user, $temp);          
        } else {
          $query = "SELECT * FROM %adm_setting_user WHERE path_name='ui.language' AND value='$filter' AND id_user='$id_user'";
          $res = $this->db->query($query);
          $output = ($this->db->num_rows($res)>0);
        }
      }break;
      
      case _OTHERFIELD_ID_COURSESTATUS: {
        list($condition, $course) = implode(',', $filter);
        
        //prevents platform's upgrades
        $status = 0;
        switch ($condition) {
          case 0: { $status = 0; } break;
          case 1: { $status = 1; } break;
          case 2: { $status = 2; } break;
          default: {}
        }
        
        $query = "SELECT * ".
		      "FROM  %lms_courseuser ".
		      "WHERE status='$status' AND idCourse='$course' AND idUser='$id_user'";
        $res = $this->db->query($query);
        $output = ($this->db->num_rows($res)>0);
      } break;

			case _OTHERFIELD_ID_ADMINLEVELS: {
				$groupid = $this->acl_man->relativeId($filter);
				$idst_group = $this->acl_man->getGroupST($groupid);
        $query = "SELECT * FROM %adm_group_members WHERE idst=".(int)$idst_group." AND idstMember=".(int)$id_user;
				$res = $this->db->query($query);
        $output = ($this->db->num_rows($res)>0);
      } break;

      default: { }
      
    }
    
    return $output;
	}
  
  
  
  
  public function getFieldQuery($id_field, $filter) {
  
    $output = '';
  
    switch ($id_field) {
    
      case _OTHERFIELD_ID_LANGUAGE: {
        //$filter = lang_code
        if ($filter == "standard") {
          $output = "SELECT idst ".
            "FROM %adm_user ".
            "WHERE idst NOT IN (SELECT id_user as idst FROM %adm_setting_user  WHERE path_name = 'ui.language')";
        } else {
          $output = "SELECT id_user as idst ".
		        "FROM  %adm_setting_user  ".
            "WHERE path_name = 'ui.language' AND value = '".$filter."'";
        }
      } break;
    
      case _OTHERFIELD_ID_COURSESTATUS: {
        //$filter = string like " {condition id} , {course id} "
        list($condition, $course) = implode(',', $filter);
        
        //prevents platform's upgrades
        $status = 0;
        switch ($condition) {
          case 0: { $status = 0; } break;
          case 1: { $status = 1; } break;
          case 2: { $status = 2; } break;
          default: {}
        }
        
        $output = "SELECT idUser as idst ".
		      "FROM  %lms_courseuser ".
		      "WHERE status='$status' AND idCourse='$course'";
		
      } break;

			case _OTHERFIELD_ID_ADMINLEVELS: {
				//$filter = admin_level path
				$groupid = $this->acl_man->relativeId($filter);
				$idst_group = $this->acl_man->getGroupST($groupid);
				$output = "SELECT idstMember as idst "
					."FROM  %adm_group_members  "
					."WHERE idst = '".(int)$idst_group."'";
      } break;

      default: {
        //...
      }

    }
  
		return $output;
  } 

}





?>