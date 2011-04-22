<?php

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @package course management
 * @subpackage course catalogue
 * @category ajax server
 * @author Giovanni Derks
 * @version $Id:$
 *
 */

if(isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

if(!defined("IN_DOCEBO")) define("IN_DOCEBO", true);

$path_to_root = '..';

// prepare refer ------------------------------------------------------------------


@error_reporting(E_ALL);
@ini_set('display_errors', 1);

require_once(dirname(__FILE__).'/'.$path_to_root.'/config.php');
require_once(dirname(__FILE__).'/'.$path_to_root.'/doceboLms/config.php');

ob_start();
// connect to database -------------------------------------------------------------------

$GLOBALS['dbConn'] = mysql_connect($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass']);
if( !$GLOBALS['dbConn'] )
	die( "Can't connect to db. Check configurations" );

if( !mysql_select_db($dbname, $GLOBALS['dbConn']) )
	die( "Database not found. Check configurations" );

@sql_query("SET NAMES '".$GLOBALS['db_conn_names']."'", $GLOBALS['dbConn']);
@sql_query("SET CHARACTER SET '".$GLOBALS['db_conn_char_set']."'", $GLOBALS['dbConn']);

// load lms setting ------------------------------------------------------------------
session_name("docebo_session");
session_start();

// load regional setting --------------------------------------------------------------

// load current user from session -----------------------------------------------------
require_once(_base_.'/lib/lib.user.php');
$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');

require_once(_i18n_.'/lib.lang.php');
require_once(_base_.'/lib/lib.template.php');
require_once(_base_.'/lib/lib.utils.php');

require_once(_base_.'/lib/lib.upload.php');

function checkPerm($token, $return_value = false, $use_custom_name = false, $use_custom_platform = false) {
	if($token == 'view') return true;
	else return false;
}

// security check --------------------------------------------------------------------

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

$GLOBALS['operation_result'] = '';

function aout($string)
{
	$GLOBALS['operation_result'] .= $string;
}

// here all the specific code ==========================================================


setLanguage('english');
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();

define('_NEWLINE', "\r\n");

$GLOBALS['report_log'] = '';

function reportLog($string) { $GLOBALS['report_log'] .= '['.date("d/m/Y h:i:s").']'.$string._NEWLINE; }
function reportLog_break() { $GLOBALS['report_log'] .= _NEWLINE._NEWLINE.'**********'._NEWLINE._NEWLINE; }




function getReportRecipients($id_rep) {
	//get month, day
	$arr_days = array();
	$arr_months = array();

	$output = array();

	//check for daily

	$recipients = array();

	$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE period LIKE '%day%' AND id_report_filter=$id_rep AND enabled = 1";
	$res = sql_query($qry);

	while ($row = mysql_fetch_assoc($res)) {

		$qry2 = "SELECT id_user FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient WHERE id_report_schedule=".$row['id_report_schedule'];
		$res2 = sql_query($qry2);

		while(list($recipient) = sql_fetch_row($res2)) {
			$recipients[] = $recipient; //idst of the recipients
		}

		$qry3 = "SELECT email FROM ".$GLOBALS['prefix_fw']."_user WHERE idst IN (".implode(',', $recipients).") AND email<>''";
		$res3 = sql_query($qry3);
		while ($row3 = sql_fetch_row($res3))
			$output[] = $row3[0];
	}


	//check for weekly
	$daynumber = date('w');
	if($daynumber == 0) $daynumber = 6;
	else $daynumber--;
	$recipients = array();

	$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE period LIKE '%week,$daynumber%' AND id_report_filter=$id_rep AND enabled = 1";
	$res = sql_query($qry);

	while ($row = mysql_fetch_assoc($res)) {

		$qry2 = "SELECT id_user FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient WHERE id_report_schedule=".$row['id_report_schedule'];
		$res2 = sql_query($qry2);

		while(list($recipient) = sql_fetch_row($res2)) {
			$recipients[] = $recipient;
		}

		$qry3 = "SELECT email FROM ".$GLOBALS['prefix_fw']."_user WHERE idst IN (".implode(',', $recipients).") AND email<>''";
		$res3 = sql_query($qry3);
		while ($row3 = sql_fetch_row($res3))
			$output[] = $row3[0];
	}

	//check for monthly
	$monthdaynumber = date('j'); //today's day of the month, 1-31
	$monthdays = date('t'); //amount of days in current month 28-31
	$recipients = array();

	$options = array();
	if ($monthdays<31 && $monthdaynumber==$monthdays) { //if it's the last day of tehe month
		for ($i=31; $i>=$monthdays; $i--) {
			$options[] = "'month,$i'";
		}
	} else {
		$options[] = "'month,$monthdaynumber'";
	}

	$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_schedule WHERE period IN (".implode(',', $options).") AND id_report_filter=$id_rep AND enabled = 1";
	$res = sql_query($qry);


	while ($row = mysql_fetch_assoc($res)) {

		$qry2 = "SELECT id_user FROM ".$GLOBALS['prefix_lms']."_report_schedule_recipient WHERE id_report_schedule=".$row['id_report_schedule'];
		$res2 = sql_query($qry2);

		while(list($recipient) = sql_fetch_row($res2)) {
			$recipients[] = $recipient;
		}

		$qry3 = "SELECT email FROM ".$GLOBALS['prefix_fw']."_user WHERE idst IN (".implode(',', $recipients).") AND email<>''";
		$res3 = sql_query($qry3);
		while ($row3 = sql_fetch_row($res3))
			$output[] = $row3[0];
	}

	//die(print_r($output, true));
	//prepare output
	return array_unique($output);

}
//******************************************************************************

require_once($GLOBALS['where_framework'].'/addons/phpmailer/class.phpmailer.php');
$mailer = new PHPMailer();
$mailer->IsMail();
$mailer->IsHTML(true);
$mailer->language = 'en';
$mailer->Charset = 'utf-8';
$mailer->SingleTo = false;
$mailer->From = Get::sett('sender_event');
$mailer->FromName = 'Training Center';

require_once(_base_.'/lib/lib.json.php');
$json = new Services_JSON();


$path = dirname(__FILE__).'/'.$path_to_root.'/files/tmp/';
$qry = "SELECT * FROM ".$GLOBALS['prefix_lms']."_report_filter";
$res = sql_query($qry);
sl_open_fileoperations();
while ($row = mysql_fetch_assoc($res)) {

	$recipients = getReportRecipients($row['id_filter']);

	if (count($recipients)>0) {
		//reset mailer
		$mailer->ClearAddresses();
		$mailer->ClearAttachments();
		//create temporary file for attachment
		//require_once($GLOBALS['where_lms'].'/admin/modules/report/report.php');

		//$temp = openreport($row['id_report']);

		$data = unserialize( $row['filter_data'] ) ;

		$query_report = "
		SELECT class_name, file_name, report_name
		FROM ".$GLOBALS['prefix_lms']."_report
		WHERE id_report = '".$data['id_report']."'";
		$re_report = sql_query($query_report);
		if($re_report && mysql_num_rows($re_report)) {

			list($class_name, $file_name, $report_name) = sql_fetch_row($re_report);

			require_once($GLOBALS['where_lms'].'/admin/modules/report/'.$file_name);
			$temp = new $class_name( $data['id_report'] );


			$tmpfile = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).'';

			$file = sl_fopen('/tmp/'.$tmpfile, "w");
			fwrite($file, $temp->getXLS($data['columns_filter_category'], $data));
			fclose($file);

			$mailer->addAttachment($path.$tmpfile, $row['filter_name'].'.xls', 'base64', 'application/x-excel');

			//set recipients
			foreach ($recipients as $recipient) $mailer->addAddress($recipient);

			$mailer->Subject = 'Sending scheduled report : '.$row['filter_name'];
			//$mailer->Body = 'Sending scheduled report : '.$row['filter_name'];

			if (!$mailer->Send()) {
				echo($row['filter_name'].' Error while sending mail.'.$mailer->ErrorInfo ); //: '.$mailer->getError?
			} else {
				echo($row['filter_name'].' Mail sent to : '.implode(',', $recipients));
			}
			$mailer->ClearAllRecipients();
			$mailer->ClearAttachments();

			//delete temp file
			unlink($path.$tmpfile.'');
		} else {
			echo '"'.$row['id_report'].'" ';
		}
	}


}
sl_close_fileoperations();
//output log data

// =====================================================================================

// close database connection

mysql_close($GLOBALS['dbConn']);

//ob_clean();
echo $GLOBALS['operation_result'];
ob_end_flush();

?>
