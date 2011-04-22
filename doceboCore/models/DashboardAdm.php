<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class DashboardAdm extends Model {

	protected $db;

	//--- init functions ---------------------------------------------------------

	public function __construct() {
		$this->db = DbConn::getInstance();
	}

	public function getPerm()	{
		return array('view' => 'standard/view.png');
	}

	//----------------------------------------------------------------------------
	public function deactivateFeeds() {
		$query = "UPDATE %adm_setting SET param_value = 'off' WHERE param_name = 'welcome_use_feed'";
		$res = $this->db->query($query);
		return $res ? true : false;
	}

	public function activateFeeds() {
		$query = "UPDATE %adm_setting SET param_value = 'on' WHERE param_name = 'welcome_use_feed'";
		$res = $this->db->query($query);
		return $res ? true : false;
	}

	public function getSqlInfo() {
		$query = "SELECT @@GLOBAL.sql_mode";
		$res = $this->db->query($query);
		list($sql_mode) = $this->db->fetch_row($res);

		$info_character = array();
		$info_collation = array();

		//string mysql_client_encoding ([ resource $link_identifier ] )
		$query = "SHOW VARIABLES LIKE 'character_set%'";
		$res = $this->db->query($query);
		while (list($name, $value) = $this->db->fetch_row($res)) {
			$info_character[$name] = $value;
		}

		$query = "SHOW VARIABLES LIKE 'collation%'";
		$res = $this->db->query($query);
		while (list($name, $value) = $this->db->fetch_row($res)) {
			$info_collation[$name] = $value;
		}

		return array(
			'sql_mode' => $sql_mode,
			'character_info' => $info_character,
			'collation_info' => $info_collation
		);
	}

	public function updateVersion($old_version, $new_version) {
		
		if($this->db->query("UPDATE %adm_setting SET param_value = '".$new_version."' WHERE param_name = 'core_version'")) {

			return $new_version;
		} else {

			return $old_version;
		}
	}

	public function getVersionExternalInfo() {
		if (Get::sett('welcome_use_feed') == 'on') {

			$version = array(
				'db_version' => Get::sett('core_version'),
				'file_version' => _file_version_,
				'string' => ''
			);

			// check for differences beetween files and database version
			if(version_compare($version['file_version'], $version['db_version']) == 1) {

				switch($version['db_version']) {
					case "3.6.0.3" :
					case "3.6.0.4" : 
					case "4.0.0" : ;break;
					case "4.0.1" :
					case "4.0.2" : 
					case "4.0.3" : $version['db_version'] = $this->updateVersion($version['db_version'], $version['file_version']);break;
				}
			}
			
			require_once(_base_.'/lib/lib.fsock_wrapper.php');
			$fp = new Fsock();
			$_online_version = $fp->send_request('http://www.docebo.org/release.txt');

			if(!$fp || !$_online_version) {

				$version = array( 'string' => '<b class="red">'.Lang::t('_UNKNOWN_RELEASE', 'dashboard').'</b>' );
			} elseif(version_compare($_online_version, $version['file_version']) == 1) {

				$version['string'] .= '<br/>'
					.'<a href="http://www.docebo.com/?versions" class="red">'.Lang::t('_NEW_RELEASE_AVAILABLE', 'dashboard').': <b>'.$_online_version.'</b></a>';
			}
		}
		return $version;
	}

	/**
	 * various stats and data retrieving to display in the dashboard
	 *
	 * @param boolean $stats_required
	 * @param boolean $arr_users
	 * @return array
	 */
	public function getUsersStats($stats_required = false, $arr_users = false) {
		
		$aclManager = Docebo::user()->getACLManager();
		$users = array();
		if($stats_required == false || empty($stats_required) || !is_array($stats_required)) {
			$stats_required = array('all', 'suspended', 'register_today', 'register_yesterday', 'register_7d',
				'now_online', 'inactive_30d', 'waiting', 'superadmin', 'admin', 'public_admin');
		}
		$stats_required = array_flip($stats_required);

		$data = new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
		if(isset($stats_required['all'])) {
			$users['all'] 	= $data->getTotalRows();
		}
		if(isset($stats_required['suspended'])) {
			$data->addFieldFilter('valid', 0);
			$users['suspended'] = $data->getTotalRows();
			$users['suspended']--; // one is anonymous
		}
		if(isset($stats_required['register_today'])) {
			$data->resetFieldFilter();
			$data->addFieldFilter('register_date', date("Y-m-d").' 00:00:00', '>');
			$users['register_today'] = $data->getTotalRows();
		}
		if(isset($stats_required['register_yesterday'])) {
			$data->resetFieldFilter();
			$yesterday = date("Y-m-d", time() - 86400);
			$data->addFieldFilter('register_date', $yesterday.' 00:00:00', '>');
			$data->addFieldFilter('register_date', $yesterday.' 23:59:59', '<');
			$users['register_yesterday'] = $data->getTotalRows();
		}
		if(isset($stats_required['register_7d'])) {
			$data->resetFieldFilter();
			$sevendaysago = date("Y-m-d", time() - (7 * 86400));
			$data->addFieldFilter('register_date', $sevendaysago.' 00:00:00', '>');
			$users['register_7d'] = $data->getTotalRows();
		}
		if(isset($stats_required['now_online'])) {
			$data->resetFieldFilter();
			$data->addFieldFilter('lastenter', date("Y-m-d H:i:s", time() - REFRESH_LAST_ENTER), '>');
			$users['now_online'] = $data->getTotalRows();
			if (($arr_users !== false) && (is_array($arr_users)) && (count($arr_users) > 0)) {
				$data->setUserFilter($arr_users);
				$users['now_online_filtered'] = $data->getTotalRows();
			}
			else {
				$users['now_online_filtered'] =0;
			}
		}
		if(isset($stats_required['inactive_30d'])) {
			$data->resetFieldFilter();
			$data->addFieldFilter('lastenter', date("Y-m-d", time() - 30 * 86400).' 00:00:00', '<');
			$users['inactive_30d'] = $data->getTotalRows();
		}
		if(isset($stats_required['waiting'])) {
			$users['waiting'] 	= $aclManager->getTempUserNumber();
		}
		if(isset($stats_required['superadmin'])) {
			$idst_sadmin = $aclManager->getGroupST(ADMIN_GROUP_GODADMIN);
			$users['superadmin'] 	= $aclManager->getGroupUMembersNumber($idst_sadmin);
		}
		if(isset($stats_required['admin'])) {
			$idst_admin = $aclManager->getGroupST(ADMIN_GROUP_ADMIN);
			$users['admin'] 		= $aclManager->getGroupUMembersNumber($idst_admin);
		}

        if(isset($stats_required['public_admin'])) {
			$idst_admin = $aclManager->getGroupST(ADMIN_GROUP_PUBLICADMIN);
			$users['public_admin'] 		= $aclManager->getGroupUMembersNumber($idst_admin);
		}
		return $users;
	}



	public function getCoursesStats() {
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');

		$course_man = new AdminCourseManagment();
		return $course_man->getCoursesStats();
	}

	public function getCoursesMonthsStats() {
		$output = array(
			'month_subs_1' => 0,
			'month_subs_2' => 0,
			'month_subs_3' => 0
		);

		//extract subscriptions for the last three months
		for ($i=0; $i<3; $i++) {
			$date = date("Y-m", strtotime("-".$i." months"));
			$query = "SELECT COUNT(*) FROM %lms_courseuser WHERE date_inscr>'".$date."-01' AND date_inscr<'".$date."-31'";
			list($num) = $this->db->fetch_row($this->db->query($query));
			$output['month_subs_'.($i + 1)] = $num;
		}

		return $output;
	}

	public function getUsersChartAccessData($how_many_days) {
		$output = array();
		$dates = array();

		$today = date("Y-m-d");
		for ($i=$how_many_days-1; $i>=0; $i--) {//for ($i=0; $i<$how_many_days; $i++) {
			$date = date("Y-m-d", strtotime("-".(int)$i." days"));
			$dates[$date] = 0;
		}
		$last_date = date("Y-m-d", strtotime("-".((int)$how_many_days - 1)." days"));

		$query = "SELECT MAX(enterTime) FROM %lms_tracksession "
			." WHERE enterTime>'".$last_date." 00:00:00' "
			." AND enterTime<='".$today." 23:59:59' GROUP BY idUser";
		$res = $this->db->query($query);
		while (list($last_access) = $this->db->fetch_row($res)) {
			$date = date("Y-m-d", strtotime($last_access));
			if (isset($dates[$date])) $dates[$date]++;
		}

		foreach ($dates as $date=>$count) {
			$output[] = array('x_axis' => $date, 'y_axis' => $count);
		}

		return $output;
	}

	public function getUsersChartRegisterData($how_many_days) {
		$output = array();
		$dates = array();

		$today = date("Y-m-d");
		for ($i=$how_many_days-1; $i>=0; $i--) {//for ($i=0; $i<$how_many_days; $i++) {
			$date = date("Y-m-d", strtotime("-".(int)$i." days"));
			$dates[$date] = 0;
		}
		$last_date = date("Y-m-d", strtotime("-".((int)$how_many_days - 1)." days"));

		$query = "SELECT register_date FROM %adm_user "
			." WHERE register_date>'".$last_date." 00:00:00' "
			." AND register_date<='".$today." 23:59:59' ORDER BY register_date DESC";
		$res = $this->db->query($query);
		while (list($last_access) = $this->db->fetch_row($res)) {
			$date = date("Y-m-d", strtotime($last_access));
			if (isset($dates[$date])) $dates[$date]++;
		}

		foreach ($dates as $date=>$count) {
			$output[] = array('x_axis' => $date, 'y_axis' => $count);
		}

		return $output;
	}

	public function getCoursesChartSubscriptionData($how_many_days) {
		$output = array();
		$dates = array();

		$today = date("Y-m-d");
		for ($i=$how_many_days-1; $i>=0; $i--) {//for ($i=0; $i<$how_many_days; $i++) {
			$date = date("Y-m-d", strtotime("-".(int)$i." days"));
			$dates[$date] = 0;
		}
		$last_date = date("Y-m-d", strtotime("-".((int)$how_many_days - 1)." days"));

		$query = "SELECT date_inscr FROM %lms_courseuser "
			." WHERE date_inscr>'".$last_date." 00:00:00' AND date_inscr<='".$today." 23:59:59'";
		$res = $this->db->query($query);
		while (list($date_inscr) = $this->db->fetch_row($res)) {
			$date = date("Y-m-d", strtotime($date_inscr));
			if (isset($dates[$date])) $dates[$date]++;
		}

		foreach ($dates as $date=>$count) {
			$output[] = array('x_axis' => $date, 'y_axis' => $count);
		}

		return $output;
	}

	public function getCoursesChartStartAttendingData($how_many_days) {
		$output = array();
		$dates = array();

		$today = date("Y-m-d");
		for ($i=$how_many_days-1; $i>=0; $i--) {//for ($i=0; $i<$how_many_days; $i++) {
			$date = date("Y-m-d", strtotime("-".(int)$i." days"));
			$dates[$date] = 0;
		}
		$last_date = date("Y-m-d", strtotime("-".((int)$how_many_days - 1)." days"));

		$query = "SELECT date_first_access FROM %lms_courseuser "
			." WHERE date_first_access>'".$last_date." 00:00:00' AND date_first_access<='".$today." 23:59:59'";
		$res = $this->db->query($query);
		while (list($date_first) = $this->db->fetch_row($res)) {
			$date = date("Y-m-d", strtotime($date_first));
			if (isset($dates[$date])) $dates[$date]++;
		}

		foreach ($dates as $date=>$count) {
			$output[] = array('x_axis' => $date, 'y_axis' => $count);
		}

		return $output;
	}

	public function getCoursesChartCompletedData($how_many_days) {
		$output = array();
		$dates = array();

		$today = date("Y-m-d");
		for ($i=$how_many_days-1; $i>=0; $i--) {//for ($i=0; $i<$how_many_days; $i++) {
			$date = date("Y-m-d", strtotime("-".(int)$i." days"));
			$dates[$date] = 0;
		}
		$last_date = date("Y-m-d", strtotime("-".((int)$how_many_days - 1)." days"));

		$query = "SELECT date_complete FROM %lms_courseuser "
			." WHERE date_complete>'".$last_date." 00:00:00' AND date_complete<='".$today." 23:59:59'";
		$res = $this->db->query($query);
		while (list($date_first) = $this->db->fetch_row($res)) {
			$date = date("Y-m-d", strtotime($date_first));
			if (isset($dates[$date])) $dates[$date]++;
		}

		foreach ($dates as $date=>$count) {
			$output[] = array('x_axis' => $date, 'y_axis' => $count);
		}

		return $output;
	}

}
