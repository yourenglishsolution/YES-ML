<?php

include('bootstrap.php');
require('../config.php');

$db = mysql_connect($cfg['db_host'], $cfg['db_user'], $cfg['db_pass']);
mysql_select_db($cfg['db_name']);

mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");

$step =Get::gReq('step', DOTY_INT);


switch($step) {
	case "1": { // --- Upgrade db structure --------------------------------------
		$fn = _upgrader_.'/data/sql/pre_upgrade.sql';
		importSqlFile($fn);
	} break;
	case "2": { // --- Upgrade learning_module content ---------------------------
		updateLearningModule();
	} break;
	case "3": { // --- Upgrade some db data --------------------------------------
		$fn = _upgrader_.'/data/sql/upgrade01.sql';
		importSqlFile($fn);

		$re = mysql_query("SELECT idCourse, idUser, COUNT(*)
		FROM learning_courseuser
		GROUP BY idCourse, idUser
		HAVING COUNT(*) >= 2");

		while(list($idc, $idu, $occurency) = mysql_fetch_row($re)) {

			$query = "DELETE FROM learning_courseuser WHERE idCourse = ".$idc." AND idUser = ".$idu." LIMIT ".($occurency-1);
			if(!mysql_query($query)) {

				$GLOBALS['debug'] .= $query.' - '.mysql_error();
			}
		}

	} break;
	case "4": { // --- Upgrade trees (ileft / iright) ----------------------------
		$GLOBALS['tree_st'] = '';
		$tables = array(
			'core_org_chart_tree' => 'idOrg',
			'learning_category' => 'idCategory',
		);
		foreach ($tables as $tab=>$p_key) {
			populate($tab, $p_key);
		}
	} break;
	case "5": { // --- Upgrade settings table ------------------------------------
		updateSettings();
	} break;
	case "6": { // --- Adding god admins and all users roles ---------------------
		addUpgraderRoles();
	} break;
	case "7": { // --- Remove old photo ------------------------------------------
		$db = mysql_connect($_SESSION['db_info']['db_host'], $_SESSION['db_info']['db_user'], $_SESSION['db_info']['db_pass']);
		mysql_select_db($_SESSION['db_info']['db_name']);

		$query =	"SELECT photo"
					." FROM core_user"
					." WHERE avatar <> ''"
					." AND photo <> ''";

		$result = mysql_query($query);

		while(list($photo) = mysql_fetch_row($result)) {
			@unlink('../files/doceboCore/photo/'.$photo);
		}
	} break;
	case "8": { // --- Kb --------------------------------------------------------
		kbUpgrade();
	} break;
	case "9": { // --- Post upgrade queries --------------------------------------
		$fn = _upgrader_.'/data/sql/post_upgrade.sql';
		importSqlFile($fn);
	} break;
}


echo $GLOBALS['debug'];
mysql_close($db);




// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------




function populate($table_name, $field_id) {

	$search_query = "
	SELECT ".$field_id.", idParent, path, lev, iLeft, iRight
	FROM ".$table_name."
	ORDER BY path";
	$q = mysql_query($search_query);
	if (!$q) { $GLOBALS['debug'].=mysql_error()."\n"; }

	if(!$q) return false;

	$table = array();
	$GLOBALS['tree_st'] = array(
		0 =>  array(
			'id' => 0,
			'id_parent' => 0,
			'path' => '/root/',
			'sons' => array(),
			'left' => 0,
			'right' => 0,
			'iLeft' => 1,
			'iRight' => mysql_num_rows($q) * 2
		)
	);
	while(list($id, $idParent, $path, $deep, $il, $ir) = mysql_fetch_row($q)) {

		$GLOBALS['tree_st'][$id] = array(
			'id' => $id,
			'id_parent' => $idParent,
			'path' => $path,
			'sons' => array(),
			'left' => 0,
			'right' => 0,
			'iLeft' => $il,
			'iRight' => $ir
		);

		if(isset($GLOBALS['tree_st'][$idParent]) && $id != 0) {
			$GLOBALS['tree_st'][$idParent]['sons'][$id] = $id;
		}
		$table[$deep][$id] = end(explode("/", $path));
	}

	$GLOBALS['count'] = 1;

	navigate(0);
	if($table_name == 'core_org_chart_tree') {
		// we need to update also idst_oc and idst_ocd
		$idst_oc = array();
		$qtxt ="SELECT idst, groupid FROM core_group WHERE groupid LIKE '/oc%' ";
		$q = mysql_query($qtxt);
		while($row=mysql_fetch_object($q)) {

			$idst_oc[$row->groupid] = $row->idst;
		}
	}
	foreach($GLOBALS['tree_st'] as $id => $node) {
		$qtxt ="
		UPDATE ".$table_name."
		SET iLeft = '".$node['left']."', iRight = '".$node['right']."'"
		.( $table_name == 'core_org_chart_tree' ? ", idst_oc = '".$idst_oc['/oc_'.$node['id']]."', idst_ocd = '".$idst_oc['/ocd_'.$node['id']]."' " : "" )
		."WHERE ".$field_id." = ".$node['id']."";

		$q2 =mysql_query($qtxt);
		if (!$q2) { $GLOBALS['debug'].=mysql_error()."\n"; }
	}
}


function navigate($nodeid) {

	$GLOBALS['tree_st'][$nodeid]['left'] = $GLOBALS['count'];
	$GLOBALS['count']++;

	if(empty($GLOBALS['tree_st'][$nodeid]['sons'])) {

		$GLOBALS['tree_st'][$nodeid]['right'] = $GLOBALS['count'];
		$GLOBALS['count']++;
		return;

	} else {
		foreach($GLOBALS['tree_st'][$nodeid]['sons'] as $id) {
			navigate($id);
		}

		$GLOBALS['tree_st'][$nodeid]['right'] = $GLOBALS['count'];
		$GLOBALS['count']++;
	}

}



// -----------------------------------------------------------------------------


function updateLearningModule() {

	$fn = _upgrader_."/data/sql/learning_module_new.sql";
	importSqlFile($fn);

	$fields ="t1.module_name, t1.default_op, t1.default_name, t1.token_associated,
		t1.file_name, t1.class_name, t1.module_info, t1.mvc_path, t2.idModule as old_id";
	$qtxt ="SELECT ".$fields." FROM learning_module_new as t1
		LEFT JOIN learning_module as t2 ON
		(t1.module_name=t2.module_name AND
		t1.default_name = t2.default_name)
		WHERE t2.module_name IS NULL OR t1.mvc_path != ''";
	$q =mysql_query($qtxt);
	if (!$q) { $GLOBALS['debug'].=mysql_error()."\n"; }

	if ($q) {
		while($row=mysql_fetch_assoc($q)) {

			if ($row['old_id'] > 0) { // update (t1.mvc_path != '')
				$qtxt ="UPDATE learning_module SET module_name = '".$row['module_name']."',
					default_op = '".$row['default_op']."', default_name = '".$row['default_name']."',
					token_associated = '".$row['token_associated']."',
					file_name = '".$row['file_name']."',class_name = '".$row['class_name']."',
					module_info = '".$row['module_info']."',mvc_path = '".$row['mvc_path']."'
					WHERE learning_module.idModule ='".$row['old_id']."' LIMIT 1";
				$q2 =mysql_query($qtxt);
				if (!$q2) { $GLOBALS['debug'].=mysql_error()."\n"; }
			}
			else { // insert missing
				$qtxt ="INSERT INTO learning_module (module_name, default_op,
					default_name, token_associated, file_name, class_name, module_info,
					mvc_path) VALUES
					('".$row['module_name']."', '".$row['default_op']."',
					'".$row['default_name']."', '".$row['token_associated']."',
					'".$row['file_name']."', '".$row['class_name']."',
					'".$row['module_info']."', '".$row['mvc_path']."');";
				$q2 =mysql_query($qtxt);
				if (!$q2) { $GLOBALS['debug'].=mysql_error()."\n"; }
			}
		}
	}

	$qtxt ="DROP TABLE IF EXISTS `learning_module_new`;";
	mysql_query($qtxt);
}


// -----------------------------------------------------------------------------


function addUpgraderRoles() {
	require_once(_installer_.'/lib/lib.role.php');

	$godadmin =getGroupIdst('/framework/level/godadmin');
	$oc0 =getGroupIdst('/oc_0');

	$fn = _installer_."/data/role/rolelist_godadmin.txt";
	$roles=explode("\n", file_get_contents($fn));
	addRoles($roles, $godadmin);

	$fn = _installer_."/data/role/rolelist_oc0.txt";
	$roles=explode("\n", file_get_contents($fn));
	addRoles($roles, $oc0);
}


// -----------------------------------------------------------------------------


function updateSettings() {
	$fn = _upgrader_."/data/sql/core_setting_default.sql";
	importSqlFile($fn);

	$new_setting	= getSettingsArr('core_setting_default');

	// Unset Old settings
	unset($core_cfg['core_version']);
	unset($learning_cfg['lms_version']);

	$core_cfg		= getSettingsArr('core_setting');
	$learning_cfg	= getSettingsArr('learning_setting');
	$conference_cfg = getSettingsArr('conference_setting');
	$old_cfg		= array_merge($core_cfg, $learning_cfg, $conference_cfg);
	
	// Update the platform url
	$https=(isset($_SERVER["HTTPS"]) ? $_SERVER["HTTPS"] : FALSE);
	$base_url=($https ? "https://" : "http://").$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF'])."/";
	$base_url=preg_replace("/upgrade\\/$/", "", $base_url);	
	$default_cfg['url']['param_value']=$base_url;


	// empty the core_setting
	$qtxt = "TRUNCATE TABLE core_setting";
	$q=mysql_query($qtxt);
	if (!$q) { $GLOBALS['debug'].=mysql_error()."\n"; }

	// Store config (we'll keep only the core_setting table)
	foreach($new_setting as $key=>$val) {
		$fields = array();
		foreach ($val as $fk => $fv) {

			if($fk == 'param_value') $fields[] = $fk."='".( isset($old_cfg[$fk]) ? $old_cfg[$fk][$fv] : $fv )."'";
			else $fields[] = $fk."='".$fv."'";
		}
		$fields_qtxt =implode(', ', $fields);
		$qtxt ="INSERT INTO core_setting SET ".$fields_qtxt;
		$q=mysql_query($qtxt);
		if (!$q) { $GLOBALS['debug'].=mysql_error()."\n"; }
	}

	$qtxt ="DROP TABLE IF EXISTS `core_setting_default`;";
	mysql_query($qtxt);
}


function getSettingsArr($table) {
	$res = array();

	$qtxt = "SELECT * FROM ".$table." ORDER BY param_name";
	$q=mysql_query($qtxt);
	if (!$q) { $GLOBALS['debug'].=mysql_error()."\n"; }

	if ($q) {
		while($row=mysql_fetch_assoc($q)) {
			$key = $row['param_name'];
			$res[$key] = $row;
		}
	}

	return $res;
}


function updateConfVal($param_name, & $from_arr, & $to_arr) {

	if (!isset($to_arr[$param_name]) && (isset($from_arr[$param_name]))) {
		$to_arr[$param_name]=$from_arr[$param_name];
	}
	else if (isset($to_arr[$param_name])) {
		$to_arr[$param_name]['param_value']=$from_arr[$param_name]['param_value'];
	}
	
}

function kbUpgrade() {
	
	$qtxt = "INSERT INTO learning_kb_res (r_name, original_name, r_item_id, r_type, r_env)"
		."SELECT title as title1, title as title2, idOrg, objectType, 'course_lo' FROM learning_organization WHERE objectType <> '' ";
	$q=mysql_query($qtxt);
}


// -----------------------------------------------------------------------------


?>