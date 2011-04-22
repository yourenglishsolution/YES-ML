<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

function load_categories() {
  $res = sql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_report WHERE enabled=1");
  $GLOBALS['report_categories'] = array();
  while ($row = mysql_fetch_assoc($res)) {
    $GLOBALS['report_categories'][ $row['id_report'] ] = $row['report_name'];
  }
}


function report_save($report_id, $filter_name, &$filter_data) {
	$data = serialize($filter_data); //put serialized data in DB
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_filter ".
		"(id_report, author, creation_date, filter_data, filter_name) VALUES ".
		"($report_id, ".getLogUserId().", NOW(), '$data', '$filter_name')";
		
	if (!sql_query($query)) {
		return false;
	} else {
		$row = sql_fetch_row(sql_query("SELECT LAST_INSERT_ID()"));
		return $row[0];
	}
}

/*
function report_update($report_id, $filter_name, &$filter_data) {
	$data = serialize($filter_data); //put serialized data in DB
	$query = "UPDATE ".$GLOBALS['prefix_lms']."_report_filter SET ".
		"id_report=$report_id, author=".getLogUserId().", ".
		"creation_date=NOW(), filter_data='$data', filter_name='$filter_name' ".
		"WHERE id_filter=$report_id";
		
	return sql_query($query);
}
*/
function report_update($report_id, $filter_name, &$filter_data, $is_public = false) {
	$data = serialize($filter_data); //put serialized data in DB
	$query = "UPDATE ".$GLOBALS['prefix_lms']."_report_filter SET ".
		//"id_report=$report_id, author=".getLogUserId().", ".
		"creation_date=NOW(), filter_data='$data', filter_name='$filter_name', is_public=".($is_public ? '1' : '0')." ".
		"WHERE id_filter=$report_id";

	return mysql_query($query);
}

function report_save_schedulation($id_rep, $name, $period, $time, &$recipients) {
	//TO DO : try to use transation for this
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_schedule ".
		"(id_report_filter, id_creator, name, period, time, creation_date) VALUES ".
		"($id_rep, ".getLogUserId().",'".trim($name)."', '$period', '$time', NOW())";
	
	if (!sql_query($query)) {
		return false;
	} else {
		$row = sql_fetch_row(sql_query("SELECT LAST_INSERT_ID()"));
		$id_sched = $row[0];
	}
	
	$temp = array();
	foreach ($recipients as $value) {
		$temp[] = '('.$id_sched.', '.$value.')';
	}
	
	//TO DO : handle void recipients case
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_schedule_recipient ".
		"(id_report_schedule, id_user) VALUES ".implode(',', $temp);
		
	if (!sql_query($query))
		return false;
	else
		return $id_sched;
}


function getReportNameById($id) {
	$qry = "SELECT filter_name, author FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$id";
	$row = sql_fetch_row( sql_query($qry) );
	
	if($row[1])
		return $row[0];
	else
	{
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		return $lang->def($row[0]);
	}
}

function getScheduleNameById($id) {
	$qry = "SELECT name FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_report_schedule=$id";
	$row = sql_fetch_row( sql_query($qry) );	
	return $row[0];
}

function report_delete_filter($id_filter) {
	$qry = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$id_filter"; 
	$output = sql_query($qry);
	if ($output) {
		//delete schedulations connected to this filter
		$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_filter=$id_filter";
		$res = sql_query($qry);
		while ($row = mysql_fetch_assoc($res)) {
			$output = report_delete_scheduletion($row['id_report_schedule']);
		}
	}
	return $output;
}

function report_delete_schedulation($id_sched) {
	//delete row from report_schedule table and recipients row
	$output = false;
	$qry = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE id_report_schedule=$id_sched";
	if ($output = sql_query($qry)) {
		$qry2 = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipients WHERE id_report_schedule=$id_sched";
		$output = sql_query($qry2);		
	}
	return $output;
}


function report_update_schedulation($id_sched, $name, $period, $time, &$recipients) {
	$output = true;
	$qry = "UPDATE ".$GLOBALS['prefix_lms']."_report_schedule ".
		"SET name='$name', period='$period' ".
		"WHERE id_report_schedule=$id_sched";

	if ($output = sql_query($qry)) {
		$qry2 = "DELETE FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient WHERE id_report_schedule=$id_sched";
		if ($output = sql_query($qry2)) {
			//delete old recipients and replace with new ones
			$temp = array();
			foreach ($recipients as $value) {
				$temp[] = '('.$id_sched.', '.$value.')';
			}		
			$qry3 = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_schedule_recipient ".
				"(id_report_schedule, id_user) VALUES ".implode(',', $temp);
			$output &= sql_query($qry3);
			echo $qry3.'<br/>';
		} else echo($qry2); //return false;
	} else echo($qry); //return false;

	return $output;
}


function get_schedule_recipients($id_sched, $names=false) {
	$acl_man =& Docebo::user()->getACLManager();
	$qry = "SELECT t2.userid, t2.firstname, t2.lastname ".
		"FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient as t1, ".
		$GLOBALS['prefix_fw']."_user as t2 WHERE t2.idst=t1.id_user AND ".
		"t1.id_report_schedule=$id_sched ORDER BY userid";
	if ($res = sql_query($qry)) {
		$output = array();
		while ($row = mysql_fetch_assoc($res)) {
			if ($names) {
				$row['userid'] = $acl_man->relativeId($row['userid']);
				$temp = $row;
			} else {
				$temp = $acl_man->relativeId($row['userid']);
			}
			$output[] = $temp;
		}
		return $output;
	} else return false;
}


//------------------------------------------------------------------------------

/*
 * This returns an array $objectType => {translation}
 */
function _getLOtranslations() {
	$output = array();
	$query = "SELECT objectType FROM ".$GLOBALS['prefix_lms']."_lo_types";
	$db = DbConn::getInstance();
	$res = $db->query($query);
	while (list($objectType) = $db->fetch_row($res)) {
		switch ($objectType) {
			case "scormorg": $text = Lang::t('_SCORMSECTIONNAME', 'scorm'); break;
			case "item": $text = Lang::t('_FILE', 'standard'); break;
			default: $text = Lang::t('_LONAME_'.$objectType, 'storage'); break;
		}
		$output[$objectType] = $text;
	}
	return $output;
}

function getCommunicationsTable($selected = false) {
		require_once(_base_.'/lib/lib.table.php');
		$table = new Table();

		$lang_type = array(
			'none' => Lang::t('_NONE', 'communication'),
			'file' => Lang::t('_LONAME_item', 'storage'),
			'scorm' => Lang::t('_LONAME_scormorg', 'storage')
		);

		$col_type = array('image','','','align_center','align_center','align_center');
		$col_content = array(
			Lang::t(''),
			Lang::t('_TITLE'),
			Lang::t('_DESCRIPTION'),
			Lang::t('_DATE'),
			Lang::t('_TYPE'),
			//Lang::t('_COUNT_ACCESSIBILITY')
		);

		$table->setColsStyle($col_type);
		$table->addHead($col_content);

		if (!is_array($selected)) $selected = array();
		$query = "SELECT c.id_comm, c.title, c.description, c.publish_date, c.type_of, id_resource, COUNT(ca.id_comm) as access_entity "
			." FROM %lms_communication AS c "
			." LEFT JOIN %lms_communication_access AS ca ON (c.id_comm = ca.id_comm)"
			." GROUP BY c.id_comm"
			." ORDER BY c.publish_date DESC, c.title ASC";
		$db = DbConn::getInstance();
		$res = $db->query($query);
		while ($obj = $db->fetch_obj($res)) {
			$line = array();

			$line[] = Form::getInputCheckbox(
				'comm_selection_'.$obj->id_comm,    //id
				'comm_selection[]',                 //name
				$obj->id_comm,                      //value
				in_array($obj->id_comm, $selected), //is_checked
				''                                  //other param
			);
			$line[] = $obj->title;
			$line[] = $obj->description;
			$line[] = Format::date($obj->publish_date, 'date');
			$line[] = isset($lang_type[$obj->type_of]) ? $lang_type[$obj->type_of] : '';
			//$line[] = $obj->access_entity;

			$table->addBody($line);
		}

		return $table->getTable();
}


function getGamesTable($selected = false) {
		require_once(_base_.'/lib/lib.table.php');
		$table = new Table();

		$lang_type= _getLOtranslations();

		$col_type = array('image','','', '','align_center','align_center');
		$col_content = array(
			Lang::t(''),
			Lang::t('_TITLE'),
			Lang::t('_DESCRIPTION'),
			Lang::t('_FROM'),
			Lang::t('_TO'),
			Lang::t('_TYPE'),
			//Lang::t('_COUNT_ACCESSIBILITY')
		);

		$table->setColsStyle($col_type);
		$table->addHead($col_content);

		if (!is_array($selected)) $selected = array();
		$query = "SELECT c.id_game, c.title, c.description, c.start_date, c.end_date, "
			." c.type_of, id_resource, COUNT(ca.id_game) as access_entity "
			." FROM %lms_games AS c "
			." LEFT JOIN %lms_games_access AS ca ON (c.id_game = ca.id_game)"
			." GROUP BY c.id_game"
			." ORDER BY c.title";
		$db = DbConn::getInstance();
		$res = $db->query($query);
		while ($obj = $db->fetch_obj($res)) {
			$line = array();

			$line[] = Form::getInputCheckbox(
				'comp_selection_'.$obj->id_game,    //id
				'comp_selection[]',                 //name
				$obj->id_game,                      //value
				in_array($obj->id_game, $selected), //is_checked
				''                                  //other param
			);
			$line[] = $obj->title;
			$line[] = $obj->description;
			$line[] = Format::date($obj->start_date, 'date');
			$line[] = Format::date($obj->end_date, 'date');
			$line[] = isset($lang_type[$obj->type_of]) ? $lang_type[$obj->type_of] : '';
			//$line[] = $obj->access_entity;

			$table->addBody($line);
		}

		return $table->getTable();
}

?>