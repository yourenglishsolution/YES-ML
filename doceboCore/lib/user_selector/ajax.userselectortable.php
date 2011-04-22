<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once($GLOBALS['where_framework'].'/lib/user_selector/lib.treeuser.php');
require_once(_base_.'/lib/lib.aclmanager.php');
require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
require_once(_base_.'/lib/lib.json.php');


$op = Get::req("op", DOTY_ALPHANUM, false);

$db = DbConn::getInstance();
$json = new Services_JSON();


define("_VAR_FIELDS_COUNT", 3);

switch ($op) {

  case "select_all": {
  
    $searchFilter = Get::req("filter", DOTY_MIXED, false); //fast filter
    $useAnonymous = Get::req("useAnonymous", DOTY_INT, false);
    
    
    //compose query
    $query = "SELECT idst FROM ".
      " ".$GLOBALS['prefix_fw']."_user as t1 ".//LEFT JOIN ".$GLOBALS['prefix_fw']."_field_userentry as t2 ".
      " WHERE ".($useAnonymous ? "1=1" : "t1.userid<>'/Anonymous'")." ";
    
    if ($searchFilter) {
      //to do : check $searchFilter for special characters, like '
      $query_filter_block .= " AND (
        t1.userid LIKE '%".$searchFilter."%' OR 
        t1.firstname LIKE '%".$searchFilter."%' OR
        t1.lastname LIKE '%".$searchFilter."%' OR
        t1.email LIKE '%".$searchFilter."%'
        ) ";
    } else {
      $query_filter_block = "";
    }
    $query .= $query_filter_block;
  
    $temp = array();
    $res = $db->query($query);
    while (list($idst) = $db->fetch_row($res)) { $temp[] = $idst; }
  
  
    $output = array(
      'count' => count($temp),
      'data' => $temp
    );
    aout($json->encode($output));
  } break;


  case "table_get_users": {   
    
    // read request params
    $startIndex = Get::req("startIndex", DOTY_INT, false);
    $numResults = Get::req("results", DOTY_INT, false);
    $sortBy = Get::req("sort", DOTY_ALPHANUM, false);
    $sortDir = Get::req("dir", DOTY_ALPHANUM, false);
    $extraFields = Get::req("extraFields", DOTY_MIXED, array()); //array of fields id
    $searchFilter = Get::req("filter", DOTY_MIXED, false); //fast filter
    $useAnonymous = Get::req("useAnonymous", DOTY_INT, false);
    
    
    //compose query
    $query = "SELECT idst, userid, lastname, firstname, email, register_date, lastenter FROM ".
      " ".$GLOBALS['prefix_fw']."_user as t1 ".//LEFT JOIN ".$GLOBALS['prefix_fw']."_field_userentry as t2 ".
      " WHERE ".($useAnonymous ? "1=1" : "t1.userid<>'/Anonymous'")." ";
      
    if ($searchFilter) {
      //to do : check $searchFilter for special characters, like '
      $query_filter_block .= " AND (
        t1.userid LIKE '%".$searchFilter."%' OR 
        t1.firstname LIKE '%".$searchFilter."%' OR
        t1.lastname LIKE '%".$searchFilter."%' OR
        t1.email LIKE '%".$searchFilter."%'
        ) ";
    } else {
      $query_filter_block = "";
    }
    $query .= $query_filter_block;
    
    $_sort = '';
    switch ($sortBy) {
      case "fullname": { $_sort = "firstname ".$sortDir.", t1.lastname"; } break;
      default: { $_sort = "userid"; }
    }
    
    $query .=
      " ORDER BY t1.".$_sort." ".$sortDir." ".
      " LIMIT ".$startIndex.", ".$numResults;
    
    //retrieve records
    $u_arr = array();
    $var_cols = array();
    $fields_selection = array();
    $users_selection = array();
    $res = $db->query($query);
    while ($row = $db->fetch_assoc($res)) {
      
      $users_selection[] = $row['idst'];
      
      for ($i=0; $i<_VAR_FIELDS_COUNT; $i++) {
        list($ftype, $fid) = explode("_", $extraFields[$i]);
        switch ($ftype) {
          case "std": {
          
          } break;
          
          case "cstm": {
            if (!in_array($fid, $fields_selection)) $fields_selection[] = $fid; //$fid = field type id_common
          } break;
        
          default: {}
        } //end switch
        $var_cols[/*$i*/] = array('type'=>$ftype, 'id'=>$fid); 
      }
      
      $u_arr[ $row['idst'] ] = $row;
      
    } //end while
    
    $f_arr = array();
    $query_fields = "SELECT id_common, id_user, user_entry FROM ".$GLOBALS['prefix_fw']."_field_userentry ".
      " WHERE id_common IN (".implode(",", $fields_selection).") ".
      " AND id_user IN (".implode(",", $users_selection).")";
    $fres = $db->query($query_fields);
    while ($frow = $db->fetch_assoc($fres)) {
      $f_arr[ $frow['id_user'] ][ $frow['id_common'] ] = $frow['user_entry'];
    }
    
    $rows = array();
    $acl = new DoceboACLManager();
    foreach ($u_arr as $key=>$val) {
    
      $temp = array();
      for ($i=0; $i<_VAR_FIELDS_COUNT; $i++) {
        
        switch ($var_cols[$i]['type']) {
      
          case "std": {
            $t = '';
            $is_filtered = false;
            switch ($var_cols[$i]['id']) {
              case 0: { $t = $val['userid'];  $is_filtered = true;} break; //userid
              case 1: { $t = $val['firstname'];  $is_filtered = true;} break; //firstname
              case 2: { $t = $val['lastname'];  $is_filtered = true;} break; //lastname
              case 3: { $t = $val['email'];  $is_filtered = true;} break; //email
              case 4: { $t = $val['register_date']; } break; //registration date
            }
            if ($searchFilter && $is_filtered) { $t = str_replace($searchFilter, '<span class="filter_evidence">'.$searchFilter.'</span>', $t); }
            $temp[] = $t;
          } break;
        
          case "cstm": {
            $fman = new FieldList();
            $temp_val = "";
            
            if ( isset( $f_arr[$key][ $var_cols[$i]['id'] ] ) ) {
              switch ($fman->getFieldTypeById($var_cols[$i]['id'])) {
                
                case 'dropdown': 
                case 'country': {
                  $inst = $fman->getFieldInstance($var_cols[$i]['id']);
                  $sons = $inst->getAllSon();
                  $temp_val = $sons[ $f_arr[$key][ $var_cols[$i]['id'] ] ]; 
                } break;
                
                case 'yesno': {
                  switch ( $f_arr[$key][ $var_cols[$i]['id'] ] ) {
                    case 0: $temp_val = Lang::t('_NOT_ASSIGNED'); break;
                    case 1: $temp_val = Lang::t('_YES'); break;
                    case 2: $temp_val = Lang::t('_NO'); break;
                    default: $temp_val = "";
                  }
                } break;
                
                default: $temp_val = $f_arr[$key][ $var_cols[$i]['id'] ];
                
              }
            }
            
            $temp[] = $temp_val;
          } break;
          
          default: { $temp[] = ''; }
        }
        
      } //end for
    
      $r_userid = $acl->relativeId( $val['userid'] );
      $r_fullname = $val['firstname'].' '.$val['lastname'];
    
      if ($searchFilter) {
        $r_userid = str_replace($searchFilter, '<span class="filter_evidence">'.$searchFilter.'</span>', $r_userid);
        $r_fullname = str_replace($searchFilter, '<span class="filter_evidence">'.$searchFilter.'</span>', $r_fullname);
      }
            
    
      $rows[] = array(
        'idst'      => $key,
        'userid'    => $r_userid,
        'fullname'  => $r_fullname,
        '_varcol_0' => $temp[0],
        '_varcol_1' => $temp[1],
        '_varcol_2' => $temp[2]
      );
    }
    
    //retrieve other info
    $total_query = "SELECT COUNT(*) FROM ".$GLOBALS['prefix_fw']."_user".($useAnonymous ? "" : " WHERE userid<>'/Anonymous'");
    $res = $db->query($total_query);
    $row = $db->fetch_row($res);
    $_total = $row[0];
    
    if ($searchFilter) {
      $filtered_query = "SELECT COUNT(*) FROM ".$GLOBALS['prefix_fw']."_user as t1 WHERE ".($useAnonymous ? "1=1" : "t1.userid<>'/Anonymous'")." ".$query_filter_block;
      $res = $db->query($filtered_query);
      $row = $db->fetch_row($res);
      $_filtered = $row[0];
    } else {
      $_filtered = $_total;
    }
    
    $output = array(
      'startIndex' => $startIndex,
      'results' => count($rows),
      'sort' => $sortBy,
      'dir' => $sortDir,
      'totalRecords' => $_filtered, //$_total, //total and filtered distinction is not yet supported
      'filteredRecords' => $_filtered,
      'records' => $rows    
    );
    
    
    aout($json->encode($output));
  } break;
  
  default: { }

}



?>