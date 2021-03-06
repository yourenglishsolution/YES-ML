<?php defined("IN_DOCEBO") or die("Direct access is forbidden");

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

define("DASH_MAX_RSS_NEWS", 5);
define("_DOCEBO_CORP_BLOG_FEED_ID", 3);

Class DashboardAdmController extends AdmController {

	protected $model;
	protected $json;
	protected $permissions;


	/*
	 * initialize the class
	 */
	public function init() {
		parent::init();
		require_once(_base_.'/lib/lib.json.php');
		$this->json = new Services_JSON();
		$this->model = new DashboardAdm();

		YuiLib::load('autocomplete,tabview');
		Util::get_js(Get::rel_path('adm').'/views/dashboard/dashboard.js', true, true);

		$this->permissions = array(
			'view' => checkPerm('view', true, 'dashboard', 'framework'),
			'view_user' => checkPerm('view', true, 'usermanagement', 'framework'),
			'add_user' => checkPerm('add', true, 'usermanagement', 'framework'),
			'mod_user' => checkPerm('mod', true, 'usermanagement', 'framework'),
			'del_user' => checkPerm('del', true, 'usermanagement', 'framework'),
			'view_course' => checkPerm('view', true, 'course', 'lms'),
			'add_course' => checkPerm('add', true, 'course', 'lms'),
			'mod_course' => checkPerm('mod', true, 'course', 'lms'),
			'del_course' => checkPerm('del', true, 'course', 'lms'),
			'view_communications' => checkPerm('view', true, 'communication', 'lms'),
			'add_communications' => checkPerm('add', true, 'communication', 'lms'),
			'view_games' => checkPerm('view', true, 'games', 'lms'),
			'add_games' => checkPerm('add', true, 'games', 'lms'),
			'subscribe' => checkPerm('subscribe', true, 'course', 'lms'),
		);
	}


	//----------------------------------------------------------------------------


	public function show() {
		//if (!checkPerm('view', true)) return;

		YuiLib::load('tabview,charts');
		$charts_num_days = 7;

		//check if there are any problems with technical configuration of the server
		$php_conf = ini_get_all(); //this
		$problem = false;

		if($php_conf['register_globals']['local_value'])
			$problem = true;

		if (version_compare(phpversion(), "5.2.0", ">"))
			if($php_conf['allow_url_include']['local_value'])
				$problem = true;		


		$user_idst = Docebo::user()->getIdSt();
		$user_level = Docebo::user()->getUserLevelId();
		$acl_man = Docebo::user()->getAclManager();

		$where = "";
		if ($user_level != ADMIN_GROUP_GODADMIN)
			$where .= "AND (author='".$user_idst."' OR is_public>0)";

		$num_reports = 0;
		$query = "SELECT id_filter, filter_name, author, creation_date, is_public "
			." FROM %lms_report_filter "
			." WHERE (author>0 OR is_public>0) ".$where
			." ORDER BY filter_name ASC ".($num_reports > 0 ? " LIMIT 0,".(int)$num_reports : ""); //creation_date DESC
		$res = sql_query($query);

		$arr_report = array();
		while (list($idrep, $name, $author, $creation_date, $is_public) = sql_fetch_row($res)) {

			$arr_report[$idrep] = $name;
		}

		//load date script for user creation and editing mask
		Form::loadDatefieldScript();

		//render view
		$this->render('show', array(
			'diagnostic_problem' => $problem,
			'lang_dir' => Lang::direction(),

			'can_approve' => checkPerm('approve_waiting_user', true, 'directory', 'framework'),
			'version' => $this->model->getVersionExternalInfo(),

			'user_stats' => $this->model->getUsersStats(),

			'course_stats' => $this->model->getCoursesStats(),
			'course_months_stats' => $this->model->getCoursesMonthsStats(),

			'userdata_accesses' => $this->json->encode($this->model->getUsersChartAccessData($charts_num_days)),
			'userdata_registrations' => $this->json->encode($this->model->getUsersChartRegisterData($charts_num_days)),

			'coursedata_subscriptions' => $this->json->encode($this->model->getCoursesChartSubscriptionData($charts_num_days)),
			'coursedata_startattendings' => $this->json->encode($this->model->getCoursesChartStartAttendingData($charts_num_days)),
			'coursedata_completed' => $this->json->encode($this->model->getCoursesChartCompletedData($charts_num_days)),

			'permissions' => $this->permissions,

			'reports' => $arr_report
		));
	}

	public function deactivate() {
		$output = array("success" => $this->model->deactivateFeeds());
		echo $this->json->encode($output);
	}

	public function activate() {
		$output = array("success" => $this->model->activateFeeds());
		echo $this->json->encode($output);
	}



	public function diagnostic_dialogTask(){
		$this->render('diagnostic_dialog', array(
			'title' => Lang::t('_SERVERINFO', 'configuration'),
			'php_conf' => ini_get_all(),
			'sql_server_info' => mysql_get_server_info(),
			'sql_additional_info' => $this->model->getSqlInfo(),
			'json' => $this->json
		));
	}

	public function user_status_dialogTask() {
		$this->render('user_status_dialog', array(
			'title' => Lang::t('_PROFILE', 'profile'),
			'json' => $this->json
		));
	}

	public function certificateTask() {
		$json = new Services_JSON();
		$body = "";

		$body .= Form::openForm('subscr_course_form', "ajax.adm_server.php?r=adm/dashboard/findcertificate");

		$body .= Form::getHidden('subscr_id_user', 'id_user', 0); //init with invalid id: we have to choose it with autocomplete textfield
		$body .= Form::getHidden('subscr_id_course', 'id_course', 0); //init with invalid id: we have to choose it with autocomplete textfield

		$body .= Form::getTextfield(Lang::t('_COURSE', 'standard'), 'certificate_course', 'certificate_course', 255, '');
		$body .= '<div id="certificate_course_container"></div>';

		$body .= Form::getTextfield(Lang::t('_USER', 'standard'), 'certificate_userid', 'certificate_userid', 255, '');
		$body .= '<div id="certificate_userid_container"></div>';

		$body .= Form::closeForm();

		$output['header'] = Lang::t('_CERTIFICATE', 'menu');
		$output['body'] = $body;
		echo $json->encode($output);
	}
	
	public function findcertificateTask() {
		$json = new Services_JSON();

		$c_course = Get::req('certificate_course', DOTY_MIXED, '');
		$id_course = Get::req('id_course', DOTY_INT, 0);
		$c_userid = Get::req('certificate_userid', DOTY_MIXED, '');
		$id_user = Get::req('id_user', DOTY_INT, 0);

		require_once(_lms_.'/lib/lib.course.php');
		$man_course = new Man_Course();
		$acl_man = Docebo::user()->getAclManager();

		if($id_user <= 0) $id_user = $acl_man->getUserST($c_userid);
		if($id_course <= 0) {
			////eliminates che code from the course name
			if ($c_course != "") $c_course = trim(preg_replace('|^\[([^\]]*)\][\s]*|i', '', $c_course));
			$id_course = $man_course->getCourseIdByName($c_course);
		}

		//check if input is correct
		if ($id_user <= 0 || $id_course <= 0) {
			$output['success'] = false;
			$output['message'] = 'Invalid input.';
			echo $this->json->encode($output);
			return;
		}
		
		require_once(_lms_.'/lib/lib.certificate.php');
		$cert = new Certificate();
		$released = $cert->certificateStatus($id_user, $id_course);
		$print = array();
		foreach($released as $id_cert => $name) {
			$print[] = '<a class="ico-wt-sprite subs_pdf" href="index.php?modname=certificate&certificate_id='.$id_cert.'&course_id='.$id_course.'&user_id='.$id_user.'&op=send_certificate&of_platform=lms">'
				.'<span>'.$name.'</span>'
				.'</a>';
		}

		$query = "SELECT cc.id_certificate, ce.name, available_for_status, cu.status "
			." FROM (".$GLOBALS['prefix_lms']."_certificate AS ce "
			." JOIN ".$GLOBALS['prefix_lms']."_certificate_course AS cc "
			."		ON (ce.id_certificate = cc.id_certificate) )"
			." JOIN ".$GLOBALS['prefix_lms']."_courseuser AS cu "
			."		ON (cu.idCourse = cc.id_course)"
			." WHERE cu.idCourse = ".(int)$id_course." "
			."	AND idUser = ".(int)$id_user." ";
		$res = sql_query($query);
		$relesable = array();
		while (list($id_certificate, $name, $available_for_status, $user_status) = sql_fetch_row($res)) {
			if($cert->canRelease( $available_for_status, $user_status ) && !isset($released[$id_certificate])) {
				
				$relesable[] = '<a class="ico-wt-sprite subs_pdf" href="index.php?modname=certificate&certificate_id='.$id_certificate.'&course_id='.$id_course.'&user_id='.$id_user.'&op=print_certificate&of_platform=lms">'
					.'<span>'.$name.'</span>'
					.'</a>';
			}
		}

		$output['success'] = true;
		$output['message'] = '';
		$output['message'] .= '<b>'.Lang::t('_CERTIFICATE_VIEW_CAPTION', 'certificate').':</b> '.( count($print) > 0
			? implode(', ', $print)
			: Lang::t('_NONE', 'standard')
		).'<br /><br />';
		$output['message'] .= '<b>'.Lang::t('_NEW_CERTIFICATE', 'certificate').':</b> '.( count($relesable) > 0
			? implode(', ', $relesable)
			: Lang::t('_NONE', 'standard')
		).'<br /><br />';
		echo $this->json->encode($output);
	}


	public function exportformatTask() {
		$this->render('export_dialog', array(
			'id_report' => Get::req('id_report', DOTY_INT, true),
			'json' => $this->json
		));
	}


}


?>