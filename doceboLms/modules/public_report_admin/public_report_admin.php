<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @version  $Id: public_report_admin.php 573 2006-08-23 09:38:54Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

if(Docebo::user()->isAnonymous()) die("You can't access");

define('_REP_KEY_NAME',     'name');
define('_REP_KEY_CREATOR',  'creator');
define('_REP_KEY_CREATION', 'creation');
define('_REP_KEY_PUBLIC',   'public');
define('_REP_KEY_OPEN',     'open');
define('_REP_KEY_MOD',      'mod');
define('_REP_KEY_SCHED',    'sched');
define('_REP_KEY_REM',      'rem');

require_once($GLOBALS['where_lms'].'/lib/lib.report.php');

function reportList()
{
	checkPerm('view');
	
	require_once(_base_.'/lib/lib.table.php');

	$acl_man =& Docebo::user()->getACLManager();
	
	$query =	"SELECT t1.*, t2.userid FROM ".
				$GLOBALS['prefix_lms']."_report_filter as t1 LEFT JOIN ".$GLOBALS['prefix_fw']."_user as t2 ON t1.author=t2.idst"
				." WHERE t1.is_public=1";
	
	$lang =& DoceboLanguage::createInstance('report', 'framework');
	$output = '';
	
	$tb = new Table(Get::sett('visu_course'));
	$tb->initNavBar('ini', 'button');
	$col_type = array('','align_center','align_center','image');
	$col_content = array(
		$lang->def('_NAME'),
		$lang->def('_TAB_REP_CREATOR', 'report', 'framework'),
		$lang->def('_CREATION_DATE'),
		'<img src="'.getPathImage().'standard/view.png" alt="'.$lang->def('_REP_TITLE_OPEN', 'report', 'framework').'" title="'.$lang->def('_REP_TITLE_OPEN', 'report', 'framework').'" />',	
	);
	
	$tb->setColsStyle($col_type);
	$tb->addHead($col_content);
	
	if ($res = sql_query($query))
	{
		while ($row = mysql_fetch_assoc($res)) {
			$id = $row['id_filter'];
			$opn_link = 
				'<a href="index.php?modname=public_report_admin&amp;op=view_report&amp;idrep='.$id.'" '.
				' title="'.$lang->def('_REP_TITLE_OPEN', 'report', 'framework').'">'.
				'<img src="'.getPathImage().'standard/view.png" alt="'.$lang->def('_REP_TITLE_OPEN', 'report', 'framework').'" />'.
				'</a>';
			$tb_content = array(
				_REP_KEY_NAME     => ($row['author'] == 0 ? $lang->def($row['filter_name']) : $row['filter_name']),
				_REP_KEY_CREATOR  => ($row['author'] == 0 ? '<div class="align_center">-</div>' : $acl_man->relativeId($row['userid'])),
				_REP_KEY_CREATION => Format::date($row['creation_date']),
				_REP_KEY_OPEN     => $opn_link
			);		
			$tb->addBody($tb_content);		
		}
	}
	
	$GLOBALS['page']->add(	getTitleArea($lang->def('_REPORT'))
							.'<div class="std_block">'
							.$tb->getTable()
							.'</div>', 'content');
}

function viewReport()
{
	checkPerm('view');
	
	$idrep = Get::req('idrep', DOTY_INT, 0);
	
	$out = &$GLOBALS['page'];
	$out->setWorkingZone('content');
	
	load_filter($idrep, true);
	
	require_once(_base_.'/lib/lib.form.php');
	require_once(_base_.'/lib/lib.download.php' );
	
	$lang =& DoceboLanguage::createInstance('report', 'framework');

	$obj_report = openreport($idrep);
	
	$obj_report->back_url = 'index.php?modname=public_report_admin&op=reportlist';
	$obj_report->jump_url = 'index.php?modname=public_report_admin&op=view_report&idrep='.$idrep;
	$start_url = 'index.php?modname=public_report_admin&op=reportlist';

	if ($temp=Get::req('dl', DOTY_STRING, false)) {
		if (!isset($_SESSION['report_tempdata'])) $ref =& $_SESSION['report']; else $ref =& $_SESSION['report_tempdata'];
		$filter_name = $ref['report_name'];
		$filename = 'report_'.$filter_name.'_'.date("d_m_Y");
		switch ($temp) {
			case 'htm': { sendStrAsFile($obj_report->getHTML(), $filename.'.html'); } break;
			case 'csv': { sendStrAsFile($obj_report->getCSV(), $filename.'.csv'); } break;
			case 'xls': { sendStrAsFile($obj_report->getXLS(), $filename.'.xls'); } break;
		}
	}
	
	cout(
  	getTitleArea(	$lang->def('REPORT_SHOW_RESULTS', 'report', 'framework'), 'report', $lang->def('_REPORT_PRINTTABLE', 'report', 'framework'))
  					.'<div class="std_block">');
  	
  	if (Get::req('no_show_repdownload', DOTY_INT, 0) <= 0)
	{
		cout(	getBackUi($obj_report->back_url, $lang->def('_BACK', 'report', 'framework'), 'content') );
  			
		cout(
			'<div>'.
			'<div><a class="export_cvs" href="index.php?modname=public_report_admin&amp;op=view_report&amp;dl=htm&amp;idrep='.$idrep.'">'.$lang->def('_EXPORT_HTML', 'report', 'framework').'</a></div>'.
			'<div><a class="export_cvs" href="index.php?modname=public_report_admin&amp;op=view_report&amp;dl=csv&amp;idrep='.$idrep.'">'.$lang->def('_EXPORT_CSV', 'report', 'framework').'</a></div>'.
			'<div><a class="export_cvs" href="index.php?modname=public_report_admin&amp;op=view_report&amp;dl=xls&amp;idrep='.$idrep.'">'.$lang->def('_EXPORT_XLS', 'report', 'framework').'</a></div>'.
			'</div>');
	}
	
	cout(Form::openForm('user_report_columns_courses', $obj_report->jump_url));
	// css -----------------------------------------------------------
	cout(	"\n".'<link href="'.getPathTemplate('lms').'style/report/style_report_user.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');

	$query_update = "UPDATE ".$GLOBALS['prefix_lms']."_report_filter SET views = views+1 WHERE id_filter = '".$idrep."'";
	$re_update = sql_query($query_update);

	$obj_report->show_results();//$_SESSION['report']['columns_filter_category']);
	
	$out->add(Form::closeForm());
	$out->add('</div>');
}

function load_filter($id)
{
	require_once(_base_.'/lib/lib.preference.php');

	$adminManager = new AdminPreference();
	$acl_manager = new DoceboACLManager();
	
	$row = mysql_fetch_assoc(sql_query("SELECT * FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$id"));
	$temp = unserialize($row['filter_data']);
	
	//Retrive user associated to admin
	$idst_associated = $adminManager->getAdminTree(getLogUserId());
	
	$array_user =& $acl_manager->getAllUsersFromIdst($idst_associated);
			
	$array_user = array_unique($array_user);
	
	//Retrive course associated to admin
	require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
	$course_man = new AdminCourseManagment();
	$courses_can_view =& $course_man->getUserAllCourses( getLogUserId() );
	
	$query =	"SELECT report_name"
				." FROM ".$GLOBALS['prefix_lms']."_report"
				." WHERE id_report = '".$temp['id_report']."'";
	
	list($report_name) = sql_fetch_row(sql_query($query));
	
	switch($report_name)
	{
		case 'user_report':
			if(isset($temp['rows_filter']['all_user']) && $temp['rows_filter']['all_user'])
			{
				$temp['rows_filter']['all_user'] = false;
				$temp['rows_filter']['users'] = $array_user;
			}
			else
			{
				$temp['rows_filter']['users'] =& $acl_manager->getAllUsersFromIdst($temp['rows_filter']['users']);
				$temp['rows_filter']['users'] = array_unique($temp['rows_filter']['users']);
				$temp['rows_filter']['users'] = array_intersect($temp['rows_filter']['users'], $array_user);
			}
		break;
		
		case 'course_report':
			if(isset($temp['rows_filter']['all_course']) && $temp['rows_filter']['all_course'] == 1)
				$temp['rows_filter'] = $courses_can_view;
			else
				$temp['rows_filter'] = array_intersect($temp['rows_filter'], $courses_can_view);
		break;
	}
	
	if($temp['columns_filter_category'] == 'courses')
	{
		if($temp['columns_filter']['all_courses'])
		{
			$temp['columns_filter']['all_courses'] = 0;
			$temp['columns_filter']['selected_courses'] = $courses_can_view;
		}
		else
			$temp['columns_filter']['selected_courses'] = array_intersect($temp['columns_filter']['selected_courses'], $courses_can_view);
	}
	elseif($temp['columns_filter_category'] == 'user')
	{
		if($temp['columns_filter']['all_user'])
		{
			$temp['columns_filter']['all_user'] = 0;
			$temp['columns_filter']['users'] = $array_user;
		}
		else
		{
			$temp['columns_filter']['users'] =& $acl_manager->getAllUsersFromIdst($temp['columns_filter']['users']);
			$temp['columns_filter']['users'] = array_unique($temp['columns_filter']['users']);
			$temp['columns_filter']['users'] = array_intersect($temp['columns_filter']['users'], $array_user);
		}
	}
	
	$_SESSION['report_tempdata'] = $temp;
	$_SESSION['report'] =& $_SESSION['report_tempdata'];
}

function openreport($idrep) {

	//$lang =& DoceboLanguage::createInstance('report', 'framework');

	$id_report = $_SESSION['report']['id_report'];

	$query_report = "
	SELECT class_name, file_name, report_name
	FROM ".$GLOBALS['prefix_lms']."_report
	WHERE id_report = '".$id_report."'";
	$re_report = sql_query($query_report);

	if(mysql_num_rows($re_report) == 0) {
		reportList();
		return;
	}
	list($class_name, $file_name, $report_name) = sql_fetch_row($re_report);

	require_once($GLOBALS['where_lms'].'/admin/modules/report/'.$file_name);
	$obj_report = new $class_name($id_report);
	
	return $obj_report;
}

function publicReportAdminDispatch($op) {
	
	switch($op) {
		case "reportlist" : {
			reportList();
		};break;
		case "view_report" : {
			viewReport();
		};break;
	}
	
}
?>