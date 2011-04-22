<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */


/**
 * Here we will process the default tree actions, if you need to add some personalized action you must
 * -> Change the ajax_url called by the php Class BaseTree
 * -> Create your own ajax server file that manage your actions
 * -> Call this file if noone of your action are mathced in order to mantain the normal function of the tree
 * The same method can be used in order to overwrite a tree function
 */

require_once($GLOBALS['where_framework'].'/lib/user_selector/lib.treeuser.php');
require_once(_base_.'/lib/lib.json.php');

$db = DbConn::getInstance();
$tree = new TreeUserManager();

$op = Get::req("op", DOTY_ALPHANUM);

switch($op) {
  case "getaddnodeform": {
    $url = Get::req('server_url', DOTY_ALPHANUM, false);
    $parent_id = Get::req('parent_id', DOTY_ALPHANUM, false);
    $output = array();
    $output['body'] = '<form name="tree_addfolder_form" method="POST" action="'.$url.'">'.
	'<input type="hidden" id="authentic_request_addfolder" name="authentic_request" value="'.Util::getSignature().'" />'.
      '<input type="hidden" name="op" value="add_folder" />'.
      '<input type="hidden" name="parent_id" value="'.$parent_id.'" />'.
      '<label for="newname">'."lang._NEW_FOLDER".':</label><input type="text" name="newname" /></form>';
    $json = new Services_JSON();
    aout( $json->encode($output) );
  } break;

	case "expand" : {
		$json = new Services_JSON();
		$node_id = Get::req('query', DOTY_INT, 0);
		$result = $tree->getNodesById($node_id);
		aout( $json->encode($result) );
	} break;


	case "add_folder": {

		$output = array();

		$output['success'] = true;
		$output['folder_id'] = 666;
		$output['label'] = 'label';
		$output['is_leaf'] = false;

		$json = new Services_JSON();
		aout( $json->encode($output) );
	} break;

	case "add_element": {

	} break;


	case "del_folder": {
		$output = array();
		
		$output['success'] = true;
		$json = new Services_JSON();
		aout( $json->encode($output) );
	} break;

	case "del_element": {

	} break;

	case "rename_folder": {

	} break;

	case "rename_element": {

	} break;
}

?>