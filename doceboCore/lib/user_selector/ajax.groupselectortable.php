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
require_once(_base_.'/lib/lib.json.php');


$op = Get::req("op", DOTY_ALPHANUM, false);

$db = DbConn::getInstance();
$json = new Services_JSON();


switch ($op) {

  case "select_all": {
    
    $searchFilter = Get::req("filter", DOTY_MIXED, false); //fast filter
    
    //compose query
    $query = "SELECT idst FROM ".
      " ".$GLOBALS['prefix_fw']."_group as t1 ".//LEFT JOIN ".$GLOBALS['prefix_fw']."_field_userentry as t2 ".
      " WHERE hidden='false' AND groupid NOT LIKE '/lms/%' ";
    
    if ($searchFilter) {
      //to do : check $searchFilter for special characters, like '
      $query_filter_block .= " AND t1.groupid LIKE '%".$searchFilter."%' ";
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
  
  
  case "table_get_groups": {   
    
  
    // read request params
    $startIndex = Get::req("startIndex", DOTY_INT, false);
    $numResults = Get::req("results", DOTY_INT, false);
    $sortBy = Get::req("sort", DOTY_ALPHANUM, false);
    $sortDir = Get::req("dir", DOTY_ALPHANUM, false);
    $searchFilter = Get::req("filter", DOTY_MIXED, false); //fast filter
    
    //compose query
    $query = "SELECT idst, groupid, description, hidden, type FROM ".
      " ".$GLOBALS['prefix_fw']."_group as t1 ".
      " WHERE hidden='false' AND groupid NOT LIKE '/lms/%' ";
      
    if ($searchFilter) {
      //to do : check $searchFilter for special characters, like '
      $query_filter_block .= " AND t1.groupid LIKE '%".$searchFilter."%' ";
    } else {
      $query_filter_block = "";
    }
    $query .= $query_filter_block;
    
    $_sort = '';
    switch ($sortBy) {
      //...
      default: { $_sort = "groupid"; }
    }
    
    $query .=
      " ORDER BY t1.".$_sort." ".$sortDir." ".
      " LIMIT ".$startIndex.", ".$numResults;
  
    $temp = array();
    $acl = new DoceboACLManager();
    $res = $db->query($query);
    while ($row = $db->fetch_assoc($res)) {
      $str = $acl->relativeId( $row['groupid'] );
      if ($searchFilter) { $str = str_replace($searchFilter, '<span class="filter_evidence">'.$searchFilter.'</span>', $str); }
      $temp[] = array(
        'idst' => $row['idst'],
        'groupid' => $str,
        'description' => $row['description']
      );
    }
    
    //retrieve other info
    $total_query = "SELECT COUNT(*) FROM ".$GLOBALS['prefix_fw']."_group WHERE hidden='false' AND groupid NOT LIKE '/lms/%' ";
    $res = $db->query($total_query);
    $row = $db->fetch_row($res);
    $_total = $row[0];
    
    if ($searchFilter) {
      $filtered_query = "SELECT COUNT(*) FROM ".$GLOBALS['prefix_fw']."_group as t1 WHERE hidden='false'  AND groupid NOT LIKE '/lms/%' ".$query_filter_block;
      $res = $db->query($filtered_query);
      $row = $db->fetch_row($res);
      $_filtered = $row[0];
    } else {
      $_filtered = $_total;
    }
    
    $output = array(
      'startIndex' => $startIndex,
      'results' => count($temp),
      'sort' => $sortBy,
      'dir' => $sortDir,
      'totalRecords' => $_filtered, //$_total, //total and filtered distinction is not yet supported
      'filteredRecords' => $_filtered,
      'records' => $temp    
    );

    aout($json->encode($output));
  } break;


  default: {
  
  }
  
}