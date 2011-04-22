<?php defined("IN_DOCEBO") or die("Direct access is forbidden");

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

Class CourseAlmsController extends AlmsController
{
	protected $json;
	protected $acl_man;
	protected $model;

	protected $data;

	protected $permissions;

	protected $base_link_course;
	protected $base_link_classroom;
	protected $base_link_edition;
	protected $base_link_subscription;
	protected $base_link_competence;

	public function init()
	{
		parent::init();
		require_once(_base_.'/lib/lib.json.php');
		$this->json = new Services_JSON();
		$this->acl_man =& Docebo::user()->getAclManager();
		$this->model = new CourseAlms();

		$this->base_link_course = 'alms/course';
		$this->base_link_classroom = 'alms/classroom';
		$this->base_link_edition = 'alms/edition';
		$this->base_link_subscription = 'alms/subscription';
		$this->base_link_competence = 'adm/competences';

		$this->permissions = array(
			'view'			=> checkPerm('view', true, 'course', 'lms') || true,
			'add'			=> checkPerm('add', true, 'course', 'lms') || true,
			'mod'			=> checkPerm('mod', true, 'course', 'lms') || true,
			'del'			=> checkPerm('del', true, 'course', 'lms') || true,
			'moderate'		=> checkPerm('moderate', true, 'course', 'lms') || true,
			'subscribe'		=> checkPerm('subscribe', true, 'course', 'lms') || true,
			'add_category'	=> checkPerm('add', true, 'course', 'lms') || true,
			'mod_category'	=> checkPerm('mod', true, 'course', 'lms') || true,
			'del_category'	=> checkPerm('del', true, 'course', 'lms') || true
		);
	}

	protected function _getMessage($code) {
		$message = "";
		switch ($code) {
			case "no permission": $message = ""; break;
			case "": $message = ""; break;
		}
		return $message;
	}

	public function show()
	{
		if(isset($_GET['res']) && $_GET['res'] !== '')
			UIFeedback::info(Lang::t('_OPERATION_SUCCESSFUL', 'standard'));

		if(isset($_GET['err']) && $_GET['err'] !== '')
			UIFeedback::error(Lang::t('_OPERATION_FAILURE', 'standard'));

		$params = array();

		if(!isset($_SESSION['course_filter']))
		{
			$_SESSION['course_filter']['text'] = '';
			$_SESSION['course_filter']['classroom'] = false;
			$_SESSION['course_filter']['descendants'] = false;
			$_SESSION['course_filter']['waiting'] = false;
		}

		if(isset($_POST['c_filter_set']))
		{
			$classroom = (bool)Get::req('classroom', DOTY_INT, false);
			$descendants = (bool)Get::req('descendants', DOTY_INT, false);
			$waiting = (bool)Get::req('waiting', DOTY_INT, false);
			$filter_text = Get::req('text', DOTY_STRING, '');
		}
		else
		{
			$classroom = $_SESSION['course_filter']['classroom'];
			$descendants = $_SESSION['course_filter']['descendants'];
			$waiting = $_SESSION['course_filter']['waiting'];
			$filter_text = $_SESSION['course_filter']['text'];
		}

		$filter_open = false;

		if($descendants || $waiting)
			$filter_open = true;

		$filter = array(
			'classroom' => $classroom,
			'descendants' => $descendants,
			'waiting' => $waiting,
			'text' => $filter_text,
			'open' => $filter_open,
			'id_category' => $this->_getSessionTreeData('id_category', 0));

		$_SESSION['course_filter']['text'] = $filter_text;
		$_SESSION['course_filter']['classroom'] = $classroom;
		$_SESSION['course_filter']['descendants'] = $descendants;
		$_SESSION['course_filter']['waiting'] = $waiting;

		$params['initial_selected_node'] = $this->_getSessionTreeData('id_category', 0);
		$params['filter'] = $filter;
		$params['root_name'] = Lang::t('_CATEGORY', 'admin_course_managment');
		$params['permissions'] = $this->permissions;

		$params['base_link_course'] = $this->base_link_course;
		$params['base_link_classroom'] = $this->base_link_classroom;
		$params['base_link_edition'] = $this->base_link_edition;
		$params['base_link_subscription'] = $this->base_link_subscription;

		$smodel = new SubscriptionAlms();
		$params['unsubscribe_requests'] = $smodel->countPendingUnsubscribeRequests();

		$this->render('show', $params);
	}

	protected function _getSessionTreeData($index, $default = false)
	{
		if (!$index || !is_string($index)) return false;
		if (!isset($_SESSION['course_category']['filter_status'][$index]))
			$_SESSION['course_category']['filter_status'][$index] = $default;
		return $_SESSION['course_category']['filter_status'][$index];
	}

	protected function _setSessionTreeData($index, $value)
	{
		$_SESSION['course_category']['filter_status'][$index] = $value;
	}

	public function filterevent()
	{
		$_SESSION['course_filter']['classroom'] = Get::req('classroom', DOTY_MIXED, false);
		$_SESSION['course_filter']['descendants'] = Get::req('descendants', DOTY_MIXED, false);
		$_SESSION['course_filter']['waiting'] = Get::req('waiting', DOTY_MIXED, false);
		$_SESSION['course_filter']['text'] = Get::req('text', DOTY_STRING, '');

		if($_SESSION['course_filter']['classroom'] === 'false')
			$_SESSION['course_filter']['classroom'] = false;
		else
			$_SESSION['course_filter']['classroom'] = true;

		if($_SESSION['course_filter']['descendants'] === 'false')
			$_SESSION['course_filter']['descendants'] = false;
		else
			$_SESSION['course_filter']['descendants'] = true;

		if($_SESSION['course_filter']['waiting'] === 'false')
			$_SESSION['course_filter']['waiting'] = false;
		else
			$_SESSION['course_filter']['waiting'] = true;

		echo $this->json->encode(array('success' => true));
	}

	public function resetevent()
	{
		$_SESSION['course_filter']['text'] = '';
		$_SESSION['course_filter']['classroom'] = false;
		$_SESSION['course_filter']['descendants'] = false;
		$_SESSION['course_filter']['waiting'] = false;
	}

	protected function _getNodeActions($id_category, $is_leaf, $associated_courses = 0)
	{
		$node_options = array();

		//modify category action
		if ($this->permissions['mod_category']) {
			$node_options[] = array(
				'id' => 'mod_'.$id_category,
				'command' => 'modify',
				'icon' => 'standard/edit.png',
				'alt' => Lang::t('_MOD')
			);
		}

		//delete category action
		if ($this->permissions['del_category']) {
			if ($is_leaf && $associated_courses == 0)
			{
				$node_options[] = array(
					'id' => 'del_'.$id_category,
					'command' => 'delete',
					'icon' => 'standard/delete.png',
					'alt' => Lang::t('_DEL'));
			}
			else
			{
				$node_options[] = array(
					'id' => 'del_'.$id_category,
					'command' => false,
					'icon' => 'blank.png');
			}
		}

		return $node_options;
	}

	public function gettreedata()
	{
		require_once(_lms_.'/lib/category/class.categorytree.php');
		$treecat = new Categorytree();

		$command = Get::req('command', DOTY_ALPHANUM, "");
		switch ($command)
		{
			case "expand":
				$node_id = Get::req('node_id', DOTY_INT, 0);
				$initial = Get::req('initial', DOTY_INT, 0);

				$db = DbConn::getInstance();
				$result = array();
				if ($initial==1)
				{
					$treestatus = $this->_getSessionTreeData('id_category', 0);
					$folders = $treecat->getOpenedFolders( $treestatus );
					$result = array();

					$ref =& $result;
					foreach ($folders as $folder)
					{
						if ($folder > 0)
						{
							for ($i=0; $i<count($ref); $i++)
							{
								if ($ref[$i]['node']['id'] == $folder)
								{
									$ref[$i]['children'] = array();
									$ref =& $ref[$i]['children'];
									break;
								}
							}
						}

						$childrens = $treecat->getJoinedChildrensById($folder);
						while (list($id_category, $idParent, $path, $lev, $left, $right, $associated_courses) = $db->fetch_row($childrens))
						{
							$is_leaf = ($right-$left) == 1;
							$node_options = $this->_getNodeActions($id_category, $is_leaf, $associated_courses);
							$ref[] = array(
								'node' => array(
									'id' => $id_category,
									'label' => end(explode('/', $path)),
									'is_leaf' => $is_leaf,
									'count_content' => (int)(($right-$left-1)/2),
									'options' => $node_options));
						}
					}

				}
				else
				{ //not initial selection, just an opened folder
					$re = $treecat->getJoinedChildrensById($node_id);
					while (list($id_category, $idParent, $path, $lev, $left, $right, $associated_courses) = $db->fetch_row($re))
					{
						$is_leaf = ($right-$left) == 1;

						$node_options = $this->_getNodeActions($id_category, $is_leaf, $associated_courses);

						$result[] = array(
							'id' => $id_category,
							'label' => end(explode('/', $path)),
							'is_leaf' => $is_leaf,
							'count_content' => (int)(($right-$left-1)/2),
							'options' => $node_options); //change this
					}
				}

				$output = array('success'=>true, 'nodes'=>$result, 'initial'=>($initial==1));
				echo $this->json->encode($output);
			break;

			case "set_selected_node":
				$id_node = Get::req('node_id', DOTY_INT, -1);
				if ($id_node >= 0) $this->_setSessionTreeData('id_category', $id_node);
			break;

			case "modify":
				if (!$this->permissions['mod_category']) {
					$output = array('success' => false, 'message' => $this->_getMessage("no permission"));
					echo $this->json->encode($output);
					return;
				}

				$node_id = Get::req('node_id', DOTY_INT, 0);
				$new_name = Get::req('name', DOTY_STRING, false);

				$result = array('success'=>false);
				if ($new_name !== false) $result['success'] = $treecat->renameFolderById($node_id, $new_name);
				if ($result['success']) $result['new_name'] = stripslashes($new_name);

				echo $this->json->encode($result);
			break;


			case "create":
				if (!$this->permissions['add_category']) {
					$output = array('success' => false, 'message' => $this->_getMessage("no permission"));
					echo $this->json->encode($output);
					return;
				}

				$node_id = Get::req('node_id', DOTY_INT, false);
				$node_name = Get::req('name', DOTY_STRING, false); //no multilang required for categories

				$result = array();
				if ($node_id === false)
					$result['success'] = false;
				else
				{
					$success = false;
					$new_node_id = $treecat->addFolderById($node_id, $node_name);
					if ($new_node_id != false && $new_node_id>0) $success = true;

					$result['success'] = $success;
					if ($success)
						$result['node'] = array(
							'id' => $new_node_id,
							'label' => $node_name,
							'is_leaf' => true,
							'count_content' => 0,
							'options' => $this->_getNodeActions($new_node_id, true));
				}
				echo $this->json->encode($result);
			break;

			case "delete":
				if (!$this->permissions['del_category']) {
					$output = array('success' => false, 'message' => $this->_getMessage("no permission"));
					echo $this->json->encode($output);
					return;
				}

				$node_id = Get::req('node_id', DOTY_INT, 0);
				$result = array('success' => $treecat->deleteTreeById($node_id));
				echo $this->json->encode($result);
			break;

			case "move":
				if (!$this->permissions['mod_category']) {
					$output = array('success' => false, 'message' => $this->_getMessage("no permission"));
					echo $this->json->encode($output);
					return;
				}
				
				$node_id = Get::req('src', DOTY_INT, 0);
				$node_dest = Get::req('dest', DOTY_INT, 0);
				$model = new CoursecategoryAlms();
				$result = array('success'=>$model->moveFolder($node_id, $node_dest));

				echo $this->json->encode($result);
			break;

			case "options":
				$node_id = Get::req('node_id', DOTY_INT, 0);

				//get properties from DB
				$count = $treecat->getChildrenCount($node_id);
				$is_leaf = true;
				if ($count>0) $is_leaf = false;
				$node_options = $this->_getNodeActions($node_id, $is_leaf);

				$result = array('success'=>true, 'options'=>$node_options, '_debug'=>$count);
				echo $this->json->encode($result);
			break;
			//invalid command
			default: {}
		}
	}

	public function getcourselist()
	{
		//Datatable info
		$start_index = Get::req('startIndex', DOTY_INT, 0);
		$results = Get::req('results', DOTY_MIXED, Get::sett('visuItem', 25));
		$sort = Get::req('sort', DOTY_MIXED, 'userid');
		$dir = Get::req('dir', DOTY_MIXED, 'asc');

		$id_category = Get::req('node_id', DOTY_INT, (int)$this->_getSessionTreeData('id_category', 0));
		$filter_text = $_SESSION['course_filter']['text'];
		$classroom = $_SESSION['course_filter']['classroom'];
		$descendants = $_SESSION['course_filter']['descendants'];
		$waiting = $_SESSION['course_filter']['waiting'];

		$filter_open = false;

		if($descendants || $waiting)
			$filter_open = true;

		$filter = array(
			'id_category' => $id_category,
			'classroom' => $classroom,
			'descendants' => $descendants,
			'waiting' => $waiting,
			'text' => $filter_text,
			'open' => $filter_open
		);

		$total_course = $this->model->getCourseNumber($filter);
		if ($start_index >= $total_course) {
			if ($total_course<$results) {
				$start_index = 0;
			} else {
				$start_index = $total_course - $results;
			}
		}
		$course_res = $this->model->loadCourse($start_index, $results, $sort, $dir, $filter);
		$course_with_cert = $this->model->getCourseWithCertificate();
		$course_with_competence = $this->model->getCourseWithCompetence();

		$list = array();
		
		while($row = sql_fetch_assoc($course_res))
			$list[] = array(
				'id' => $row['idCourse'],
				'code' => $row['code'],
				'name' => $row['name'],
				'type' => Lang::t('_'.strtoupper($row['course_type'])),
				
				'wait' => (/*$row['course_type'] !== 'classroom' && */$row['course_edition'] != 1 && $row['pending'] != 0
						? '<a href="index.php?r='.$this->base_link_subscription.'/waitinguser&id_course='.$row['idCourse'].'" title="'.Lang::t('_WAITING', 'course').'">'.$row['pending'].'</a>'
						: '' ),
				'user' => ($row['course_type'] !== 'classroom' && $row['course_edition'] != 1 
						? '<a class="nounder" href="index.php?r='.$this->base_link_subscription.'/show&amp;id_course='.$row['idCourse'].'" title="'.Lang::t('_SUBSCRIPTION', 'course').'">'.$row['subscriptions'].' '.Get::img('standard/moduser.png', Lang::t('_SUBSCRIPTION', 'course')).'</a>'
						: ''),
				'edition' => ($row['course_type'] === 'classroom' 
						? '<a href="index.php?r='.$this->base_link_classroom.'/classroom&amp;id_course='.$row['idCourse'].'" title="'.Lang::t('_CLASSROOM_EDITION', 'course').'">'.$this->model->classroom_man->getDateNumber($row['idCourse'], true).'</a>' : ($row['course_edition'] == 1 ? '<a href="index.php?r='.$this->base_link_edition.'/show&amp;id_course='.$row['idCourse'].'" title="'.Lang::t('_EDITIONS', 'course').'">'.$this->model->edition_man->getEditionNumber($row['idCourse']).'</a>'
						: '')),
				'certificate' => '<a href="index.php?r='.$this->base_link_course.'/certificate&amp;id_course='.$row['idCourse'].'">'.Get::sprite('subs_pdf'.(!isset($course_with_cert[$row['idCourse']]) ? '_grey' : ''), Lang::t('_CERTIFICATE_ASSIGN_STATUS', 'course')).'</a>',
				'competences' => '<a href="index.php?r='.$this->base_link_competence.'/man_course&amp;id_course='.$row['idCourse'].'">'.Get::sprite('subs_competence'.(!isset($course_with_competence[$row['idCourse']]) ? '_grey' : ''), Lang::t('_COMPETENCES', 'course')).'</a>',
				'menu' => '<a href="index.php?r='.$this->base_link_course.'/menu&amp;id_course='.$row['idCourse'].'">'.Get::sprite('subs_menu', Lang::t('_ASSIGN_MENU', 'course')).'</a>',
				'dup' => 'ajax.adm_server.php?r='.$this->base_link_course.'/dupcourse&id_course='.$row['idCourse'],
				'mod' => '<a href="index.php?r='.$this->base_link_course.'/modcourse&amp;id_course='.$row['idCourse'].'">'.Get::sprite('subs_mod', Lang::t('_MOD', 'standard')).'</a>',
				'del' => 'ajax.adm_server.php?r='.$this->base_link_course.'/delcourse&id_course='.$row['idCourse'].'&confirm=1');

		$result = array(
			'totalRecords' => $total_course,
			'startIndex' => $start_index,
			'sort' => $sort,
			'dir' => $dir,
			'rowsPerPage' => $results,
			'results' => count($list),
			'records' => $list
		);

		echo $this->json->encode($result);
	}

	public function dupcourse()
	{
		if (!$this->permissions['add']) {
			$output = array('success' => false, 'message' => $this->_getMessage("no permission"));
			echo $this->json->encode($output);
			return;
		}

		if(isset($_POST['confirm']))
		{
			$id_dupcourse = Get::req('id_course', DOTY_INT, 0);
			$array_new_object = array();

			// read the old course info
			$query_sel = "SELECT *
			FROM ".$GLOBALS['prefix_lms']."_course
			WHERE idCourse = '".$id_dupcourse."' ";
			$result_sel = sql_query($query_sel);
			$list_sel = sql_fetch_array($result_sel);

			foreach($list_sel as $k=>$v)
				$list_sel[$k] = mysql_escape_string($v);

			$new_course_dup = 0;

			$new_file_array = array();

			if($list_sel['imgSponsor'] !== '')
			{
				$new_name_array = explode('_', str_replace('course_sponsor_logo_', '', $list_sel['imgSponsor']));
				$filename = 'course_sponsor_logo_'.mt_rand(0, 100).'_'.time().'_'.str_replace('course_sponsor_logo_'.$new_name_array[0].'_'.$new_name_array[1].'_', '',$list_sel['imgSponsor']);

				$new_file_array[0]['old'] = $list_sel['imgSponsor'];
				$new_file_array[0]['new'] = $filename;
				$list_sel['imgSponsor'] = $filename;
			}

			if($list_sel['img_course'] !== '')
			{
				$new_name_array = explode('_', str_replace('course_logo_', '', $list_sel['img_course']));
				$filename = 'course_logo_'.mt_rand(0, 100).'_'.time().'_'.str_replace('course_logo_'.$new_name_array[0].'_'.$new_name_array[1].'_', '',$list_sel['img_course']);

				$new_file_array[1]['old'] = $list_sel['img_course'];
				$new_file_array[1]['new'] = $filename;
				$list_sel['img_course'] = $filename;
			}

			if($list_sel['img_material'] !== '')
			{
				$new_name_array = explode('_', str_replace('course_user_material_', '', $list_sel['img_material']));
				$filename = 'course_user_material_'.mt_rand(0, 100).'_'.time().'_'.str_replace('course_user_material_'.$new_name_array[0].'_'.$new_name_array[1].'_', '',$list_sel['img_material']);

				$new_file_array[2]['old'] = $list_sel['img_material'];
				$new_file_array[2]['new'] = $filename;
				$list_sel['img_material'] = $filename;
			}

			if($list_sel['img_othermaterial'] !== '')
			{
				$new_name_array = explode('_', str_replace('course_otheruser_material_', '', $list_sel['img_othermaterial']));
				$filename = 'course_otheruser_material_'.mt_rand(0, 100).'_'.time().'_'.str_replace('course_otheruser_material_'.$new_name_array[0].'_'.$new_name_array[1].'_', '',$list_sel['img_othermaterial']);

				$new_file_array[3]['old'] = $list_sel['img_othermaterial'];
				$new_file_array[3]['new'] = $filename;
				$list_sel['img_othermaterial'] = $filename;
			}

			if($list_sel['course_demo'] !== '')
			{
				$new_name_array = explode('_', str_replace('course_demo_', '', $list_sel['course_demo']));
				$filename = 'course_demo_'.mt_rand(0, 100).'_'.time().'_'.str_replace('course_demo_'.$new_name_array[0].'_'.$new_name_array[1].'_', '',$list_sel['course_demo']);

				$new_file_array[4]['old'] = $list_sel['course_demo'];
				$new_file_array[4]['new'] = $filename;
				$list_sel['course_demo'] = $filename;
			}

			// duplicate the entry of learning_course
			$query_ins = "INSERT INTO ".$GLOBALS['prefix_lms']."_course
				( idCategory, code, name, description, lang_code, status, level_show_user,
				subscribe_method, linkSponsor, imgSponsor, img_course, img_material, img_othermaterial,
				course_demo, mediumTime, permCloseLO, userStatusOp, difficult, show_progress, show_time, show_extra_info,
				show_rules, valid_time, max_num_subscribe, min_num_subscribe,
				max_sms_budget, selling, prize, course_type, policy_point, point_to_all, course_edition, classrooms, certificates,
				create_date, security_code, imported_from_connection, course_quota, used_space, course_vote, allow_overbooking, can_subscribe,
				sub_start_date, sub_end_date, advance, show_who_online, direct_play, autoregistration_code, use_logo_in_courselist )
			VALUES
				( '".$list_sel['idCategory']."', '".$list_sel['code']."', '"."Copia di ".$list_sel['name']."', '".$list_sel['description']."', '".$list_sel['lang_code']."', '".$list_sel['status']."', '".$list_sel['level_show_user']."',
				'".$list_sel['subscribe_method']."', '".$list_sel['linkSponsor']."', '".$list_sel['imgSponsor']."', '".$list_sel['img_course']."', '".$list_sel['img_material']."', '".$list_sel['img_othermaterial']."',
				'".$list_sel['course_demo']."', '".$list_sel['mediumTime']."', '".$list_sel['permCloseLO']."', '".$list_sel['userStatusOp']."', '".$list_sel['difficult']."', '".$list_sel['show_progress']."', '".$list_sel['show_time']."', '".$list_sel['show_extra_info']."',
				'".$list_sel['show_rules']."', '".$list_sel['valid_time']."', '".$list_sel['max_num_subscribe']."', '".$list_sel['min_num_subscribe']."',
				'".$list_sel['max_sms_budget']."', '".$list_sel['selling']."', '".$list_sel['prize']."', '".$list_sel['course_type']."', '".$list_sel['policy_point']."', '".$list_sel['point_to_all']."', '".$list_sel['course_edition']."', '".$list_sel['classrooms']."', '".$list_sel['certificates']."',
				'".date('Y-m-d H:i:s')."', '".$list_sel['security_code']."', '".$list_sel['imported_from_connection']."', '".$list_sel['course_quota']."', '".$list_sel['used_space']."', '".$list_sel['course_vote']."', '".$list_sel['allow_overbooking']."', '".$list_sel['can_subscribe']."',
				'".$list_sel['sub_start_date']."', '".$list_sel['sub_end_date']."', '".$list_sel['advance']."', '".$list_sel['show_who_online']."', '".$list_sel['direct_play']."', '".$list_sel['autoregistration_code']."', '".$list_sel['use_logo_in_courselist']."' )";
			$result_ins = sql_query($query_ins);

			if(!$result_ins)
			{
				ob_clean();
				ob_start();
				echo $this->json->encode(array('success' => false));
				die();
			}

			// the id of the new course created
			$new_course_dup = sql_insert_id();

			//Create the new course file
			if(isset($_POST['image']))
			{
				$path = Get::sett('pathcourse');
				$path = '/doceboLms/'.Get::sett('pathcourse').( substr($path, -1) != '/' && substr($path, -1) != '\\' ? '/' : '');

				require_once(_base_.'/lib/lib.upload.php');

				sl_open_fileoperations();

				foreach($new_file_array as $file_info)
					sl_copy($path.$file_info['old'], $path.$file_info['new']);

				sl_close_fileoperations();
			}

			// copy the old course menu into the new one
			$query_selmen = "SELECT *
			FROM ".$GLOBALS['prefix_lms']."_menucourse_main
			WHERE idCourse = '".$id_dupcourse."' ";
			$result_selmen = sql_query($query_selmen);
			while($list_selmen = sql_fetch_array($result_selmen))
			{
				$query_dupmen = "INSERT INTO ".$GLOBALS['prefix_lms']."_menucourse_main ".
					" (idCourse, sequence, name, image) ".
					" VALUES ".
					" ( '".$new_course_dup."', '".$list_selmen['sequence']."', '".$list_selmen['name']."', '".$list_selmen['image']."' )";
				$result_dupmen = sql_query($query_dupmen);
				$array_seq[$list_selmen['idMain']] = sql_insert_id();
			}

			$query_selmenun = "SELECT *
			FROM ".$GLOBALS['prefix_lms']."_menucourse_under
			WHERE idCourse = '".$id_dupcourse."' ";
			$result_selmenun = sql_query($query_selmenun);
			while($list_selmenun = sql_fetch_array($result_selmenun)) {
				$valore_idn = $list_selmenun['idMain'];
				$_idMain = $array_seq[$valore_idn];
				$query_dupmen = "INSERT INTO ".$GLOBALS['prefix_lms']."_menucourse_under
				(idMain, idCourse, sequence, idModule, my_name)
				VALUES
				('".$_idMain."', '".$new_course_dup."', '".$list_selmenun['sequence']."', '".$list_selmenun['idModule']."', '".$list_selmenun['my_name']."')";
				$result_dupmen = sql_query($query_dupmen);
			}
			function &getCourseLevelSt($id_course) {

				$map 		= array();
				$levels 	= CourseLevel::getLevels();
				$acl_man	=& $GLOBALS['current_user']->getAclManager();

				// find all the group created for this menu custom for permission management
				foreach($levels as $lv => $name_level) {

					$group_info = $acl_man->getGroup(FALSE, '/lms/course/'.$id_course.'/subscribed/'.$lv);
					$map[$lv] 	= $group_info[ACL_INFO_IDST];
				}
				return $map;
			}
			function funAccess($functionname, $mode, $returnValue = false, $custom_mod_name = false) {

				return true;
			}
			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
			require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
			require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');

			$docebo_course = new DoceboCourse($id_dupcourse);
			$subscribe_man = new CourseSubscribe_Manager();

			$group_idst =& $docebo_course->createCourseLevel($new_course_dup);
			$group_of_from  =& $docebo_course->getCourseLevel($id_dupcourse);
			$perm_form   =& createPermForCoursebis($group_of_from, $new_course_dup, $id_dupcourse);
			$levels    =  $subscribe_man->getUserLevel();

			foreach($levels as $lv => $name_level) {

				foreach($perm_form[$lv] as $idrole => $v) {

					if($group_idst[$lv] != 0 && $idrole != 0) {
						$acl_man =& $GLOBALS['current_user']->getAclManager();
						$acl_man->addToRole( $idrole, $group_idst[$lv] );
					}
				}
			}

			// duplicate the certificate assigned
			$query_selmenun = "SELECT *
			FROM ".$GLOBALS['prefix_lms']."_certificate_course
			WHERE id_course = '".$id_dupcourse."' ";
			$result_selmenun = sql_query($query_selmenun);
			while($list_selmenun = sql_fetch_array($result_selmenun)) {
				$query_dupmen = "INSERT INTO ".$GLOBALS['prefix_lms']."_certificate_course
					(id_certificate, id_course, available_for_status)
					VALUES
					('".$list_selmenun['id_certificate']."', '".$new_course_dup."', '".$list_selmenun['available_for_status']."' )";
				$result_dupmen = sql_query($query_dupmen);
			}

			require_once($GLOBALS['where_lms'].'/modules/organization/orglib.php' );
			require_once($GLOBALS['where_lms'].'/lib/lib.param.php');
			require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
			require_once($GLOBALS['where_lms'].'/class.module/learning.object.php' );

			function createLO( $objectType, $idResource = NULL ) {

				$query = "SELECT className, fileName FROM ".$GLOBALS['prefix_lms']."_lo_types WHERE objectType='".$objectType."'";
				$rs = sql_query( $query );
				list( $className, $fileName ) = sql_fetch_row( $rs );
					require_once($GLOBALS['where_lms'].'/class.module/'.$fileName );
				$lo =  new $className ( $idResource );
				return $lo;
			}

			$nullVal = NULL;
			$array_cor = array();
			$map_org = array();
			
			if(isset($_POST['lo']))
			{
				$tree_course = new OrgDirDb($id_dupcourse);
				$coll = $tree_course->getFoldersCollection( $nullVal );
				while($folder = $coll->getNext())
				{
					if( !empty($folder->otherValues[REPOFIELDOBJECTTYPE]) ) {

						$lo = createLO($folder->otherValues[REPOFIELDOBJECTTYPE]);
						$new_id = $lo->copy($folder->otherValues[REPOFIELDIDRESOURCE]);

						$old_id = $folder->otherValues[REPOFIELDIDRESOURCE];

						$query_selmenun = "SELECT * FROM
						".$GLOBALS['prefix_lms']."_organization
						WHERE idCourse = '".$id_dupcourse."'
						AND idResource = '".$old_id."' ";
						$result_selmenun = sql_query($query_selmenun);

						while($list_selmenun = mysql_fetch_array($result_selmenun)) {

							$query_dupmen = "INSERT INTO ".$GLOBALS['prefix_lms']."_organization
							(idParent, path, lev, title,
							objectType, idResource, idCategory, idUser, idAuthor,
							version, difficult, description, language, resource,
							objective, dateInsert, idCourse, prerequisites, isTerminator,
							idParam, visible, milestone)
							VALUES
							('".( isset($map_org[$list_selmenun['idParent']]) ? $map_org[$list_selmenun['idParent']] : 0 ) ."', '".$list_selmenun['path']."', '".$list_selmenun['lev']."', '".$list_selmenun['title']."',
							'".$list_selmenun['objectType']."', '".$new_id."', '".$list_selmenun['idCategory']."', '".$list_selmenun['idUser']."', '".$list_selmenun['idAuthor']."',
							'".$list_selmenun['version']."', '".$list_selmenun['difficult']."', '".$list_selmenun['description']."', '".$list_selmenun['language']."', '".$list_selmenun['resource']."',
							'".$list_selmenun['objective']."', '".$list_selmenun['dateInsert']."', '".$new_course_dup."', '".$list_selmenun['prerequisites']."', '".$list_selmenun['isTerminator']."',
							'".$list_selmenun['idParam']."', '".$list_selmenun['visible']."', '".$list_selmenun['milestone']."')";
							$result_dupmen = sql_query($query_dupmen);
							$id_org = $list_selmenun['idOrg'];
							$id_last = sql_insert_id();
							$array_cor[$id_org] = $id_last;
							$array_new_object[$id_org] = $id_last;

							$query_lo_par  = "INSERT INTO ".$GLOBALS['prefix_lms']."_lo_param
							(param_name, param_value)
							VALUES
							('idReference', '".$id_last."') ";
							$result_lo_par = sql_query($query_lo_par);
							$id_lo_par = sql_insert_id();

							$query_up_lo = "UPDATE ".$GLOBALS['prefix_lms']."_lo_param
							SET idParam = '".$id_lo_par."'
							WHERE id = '".$id_lo_par."' ";
							$result_up_lo = sql_query($query_up_lo);

							$query_up_or = "UPDATE ".$GLOBALS['prefix_lms']."_organization
							SET	idParam = '".$id_lo_par."'
							WHERE idOrg = '".$id_last."' ";
							$result_up_or = sql_query($query_up_or);
						}
					} else {
						// copy folder
						//echo $old_id = $folder->id;

						$query_selmenun = "SELECT * FROM
						".$GLOBALS['prefix_lms']."_organization
						WHERE idCourse = '".$id_dupcourse."'
						AND idOrg = '".$old_id."' ";
						$result_selmenun = sql_query($query_selmenun);

						$list_selmenun = mysql_fetch_array($result_selmenun);

						$query_dupmen = " INSERT INTO ".$GLOBALS['prefix_lms']."_organization
						(idParent, path, lev, title,
						objectType, idResource, idCategory, idUser, idAuthor,
						version, difficult, description, language, resource,
						objective, dateInsert, idCourse, prerequisites, isTerminator,
						idParam, visible, milestone)
						VALUES
						('".( isset($map_org[$list_selmenun['idParent']]) ? $map_org[$list_selmenun['idParent']] : 0 ) ."', '".$list_selmenun['path']."', '".$list_selmenun['lev']."', '".$list_selmenun['title']."',
						'".$list_selmenun['objectType']."', '".$new_id."', '".$list_selmenun['idCategory']."', '".$list_selmenun['idUser']."', '".$list_selmenun['idAuthor']."',
						'".$list_selmenun['version']."', '".$list_selmenun['difficult']."', '".$list_selmenun['description']."', '".$list_selmenun['language']."', '".$list_selmenun['resource']."',
						'".$list_selmenun['objective']."', '".$list_selmenun['dateInsert']."', '".$new_course_dup."', '".$list_selmenun['prerequisites']."', '".$list_selmenun['isTerminator']."',
						'".$list_selmenun['idParam']."', '".$list_selmenun['visible']."', '".$list_selmenun['milestone']."')";
						$result_dupmen = sql_query($query_dupmen);
						$map_org[$old_id] = sql_insert_id();

					}
				}
				$query_cor = "SELECT *
				FROM ".$GLOBALS['prefix_lms']."_organization
				WHERE
				idCourse = '".$new_course_dup."'
				AND prerequisites !='' ";
				$result_cor = sql_query($query_cor);
				while($list_cor = sql_fetch_array($result_cor))
				{
					$id_orgup = $list_cor['prerequisites'];
					$arr_pre = explode(",",$id_orgup);

					for($i=0;$i<sizeof($arr_pre);$i++)
						$arr_pre[$i]=str_replace(intval($arr_pre[$i]),$array_cor[intval($arr_pre[$i])],$arr_pre[$i]);

					$query_updcor = "UPDATE ".$GLOBALS['prefix_lms']."_organization
						SET prerequisites = '";

					for($i=0;$i<sizeof($arr_pre);$i++)
					{
						if($i!=0)
							$query_updcor.=",";
						$query_updcor.=$arr_pre[$i];
					}

					$query_updcor.= "' WHERE idOrg = '".$list_cor['idOrg']."' ";
					$result_upcor = sql_query($query_updcor);
				}

				$query_selmenun = "SELECT * FROM
				".$GLOBALS['prefix_lms']."_forum
				WHERE idCourse = '".$id_dupcourse."' ";
				$result_selmenun = sql_query($query_selmenun);
				while($list_selmenun = sql_fetch_array($result_selmenun)) {

					$query_dupmen = "INSERT INTO
					".$GLOBALS['prefix_lms']."_forum
					(idCourse, title, description, locked, sequence, emoticons)
					VALUES
					('".$new_course_dup."', '".$list_selmenun['title']."', '".$list_selmenun['description']."',
					'".$list_selmenun['locked']."', '".$list_selmenun['sequence']."', '".$list_selmenun['emoticons']."')";
					$result_dupmen = sql_query($query_dupmen);
				}

				$query_selmenun = "SELECT * FROM
				".$GLOBALS['prefix_lms']."_coursereport
				WHERE id_course = '".$id_dupcourse."' ";
				$sql2=$query_selmenun;
				$result_selmenun = sql_query($query_selmenun);
				while($list_selmenun = sql_fetch_array($result_selmenun)) {

				if(!isset($array_organization[$list_selmenun['id_source']]) or $array_organization[$list_selmenun['id_source']]=="")
					$array_organization[$list_selmenun['id_source']]=0;
					$query_dupmen = "INSERT INTO
					".$GLOBALS['prefix_lms']."_coursereport
					(id_course,title,max_score,required_score,weight,show_to_user,use_for_final,sequence,source_of,id_source)
					VALUES
					('".$new_course_dup."', '".$list_selmenun['title']."', '".$list_selmenun['max_score']."',
					'".$list_selmenun['required_score']."', '".$list_selmenun['weight']."', '".$list_selmenun['show_to_user']."', '".$list_selmenun['use_for_final']."', '".$list_selmenun['sequence']."', '".$list_selmenun['source_of']."', '".$array_organization[$list_selmenun['id_source']]."')";
					$sql2=$query_dupmen;
					$result_dupmen = sql_query($query_dupmen);
				}

				$query_selmenun = "SELECT *
				FROM ".$GLOBALS['prefix_lms']."_htmlfront
				WHERE id_course = '".$id_dupcourse."' ";
				$result_selmenun = sql_query($query_selmenun);
				while($list_selmenun = sql_fetch_array($result_selmenun)){

					$query_dupmen = "INSERT INTO ".$GLOBALS['prefix_lms']."_htmlfront
					(id_course, textof)
					VALUES
					('".$new_course_dup."', '".mysql_escape_string($list_selmenun['textof'])."')";
					$result_dupmen = sql_query($query_dupmen);
				}
			}

			if(isset($_POST['advice']))
			{
				$query =	"SELECT *"
							." FROM ".$GLOBALS['prefix_lms']."_advice"
							." WHERE idCourse = ".(int)$id_dupcourse;

				$result = sql_query($query);

				if(sql_num_rows($result) > 0)
				{
					$array_sub = array();
					$array_replace = array();

					foreach($array_new_object as $id_old_obj => $id_new_obj)
					{
						$array_sub[] = 'id_org='. $id_old_obj;
						$array_replace[] = 'id_org='.$id_new_obj;
					}

					while($row = sql_fetch_row($result))
					{
						$query =	"INSERT INTO ".$GLOBALS['prefix_lms']."_advice"
									." (idAdvice, idCourse, posted, author, title, description, important)"
									." VALUES (NULL, ".(int)$new_course_dup.", '".$row['posted']."', ".(int)$row['author'].", '".$row['title']."', '".str_replace($array_sub, $array_replace, $row['description'])."', ".(int)$row['important'].")";

						sql_query($query);
					}
				}
			}

			ob_clean();
			ob_start();
			echo $this->json->encode(array('success' => true));
		}
	}

	public function certificate()
	{
		if (!$this->permissions['mod']) {
			$this->render('invalid', array(
				'message' => $this->_getErrorMessage('no permission'),
				'back_url' => 'index.php?r='.$this->base_link_course.'/show'
			));
			return;
		}
		
		if(isset($_POST['undo']))
			Util::jump_to('index.php?r='.$this->base_link_course.'/show');

		require_once(_lms_.'/lib/lib.certificate.php');
		$cert = new Certificate();

		$id_course = Get::req('id_course', DOTY_INT, 0);

		if(isset($_POST['assign']))
		{
			$point_required = Get::req('point_required', DOTY_INT, 0);
			
			if(!$cert->updateCertificateCourseAssign($id_course, $_POST['certificate_assign'], $_POST['certificate_ex_assign'], $point_required))
				Util::jump_to('index.php?r='.$this->base_link_course.'/show&err=_up_cert_err');
			Util::jump_to('index.php?r='.$this->base_link_course.'/show&res=_up_cert_ok');
		}
		else
		{
			require_once(_base_.'/lib/lib.table.php');

			$all_languages 	= Docebo::langManager()->getAllLanguages(true);
			$languages = array();
			foreach($all_languages as $k => $v)
				$languages[$v['code']] = $v['description'];

			$query =	"SELECT code, name, course_type"
						." FROM %lms_course WHERE idCourse = '".$id_course."'";
			$course = sql_fetch_array(sql_query($query));

			$tb	= new Table(false, Lang::t('_TITLE_CERTIFICATE_TO_COURSE', 'course'), Lang::t('_TITLE_CERTIFICATE_TO_COURSE', 'course'));

			$certificate_list = $cert->getCertificateList();
			$course_cert = $cert->getCourseCertificate($id_course);
			$course_ex_cert = $cert->getCourseExCertificate($id_course);
			$released = $cert->numOfcertificateReleasedForCourse($id_course);
			$point_required = $cert->getPointRequiredForCourse($id_course);

			$possible_status = array(
				AVS_NOT_ASSIGNED 					=> Lang::t('_NOT_ASSIGNED', 'course'),
				AVS_ASSIGN_FOR_ALL_STATUS 			=> Lang::t('_ASSIGN_FOR_ALL_STATUS', 'course'),
				AVS_ASSIGN_FOR_STATUS_INCOURSE 		=> Lang::t('_ASSIGN_FOR_STATUS_INCOURSE', 'course'),
				AVS_ASSIGN_FOR_STATUS_COMPLETED 	=> Lang::t('_ASSIGN_FOR_STATUS_COMPLETED', 'course')
			);

			$type_h = array('nowrap', 'nowrap', '', '', 'image');
			$cont_h	= array(
				Lang::t('_TITLE', 'course'),
				Lang::t('_CERTIFICATE_LANGUAGE', 'course'),
				Lang::t('_CERTIFICATE_ASSIGN_STATUS', 'course'),
				Lang::t('_CERTIFICATE_EX_ASSIGN_STATUS', 'course'),
				Lang::t('_CERTIFICATE_RELEASED', 'course')
			);
			$tb->setColsStyle($type_h);
			$tb->addHead($cont_h);

			$view_cert = false;
			if(Docebo::user()->getUserLevelId() != ADMIN_GROUP_GODADMIN)
			{
				if(checkPerm('view', true, 'certificate', 'lms') || checkPerm('view', true, 'pcertificate', 'lms'))
					$view_cert = true;
			}
			else
				$view_cert = true;

			while(list($id_cert, $cert) = each($certificate_list))
			{
				$cont = array();
				$cont[] = '<label for="certificate_assign_'.$id_cert.'">'.$cert[CERT_NAME].'</label>';
				$cont[] = (isset($languages[$cert[CERT_LANG]]) ? $languages[$cert[CERT_LANG]] : $cert[CERT_LANG]); //lang description?
				$cont[] = Form::getInputDropdown(	'dropdown_nowh',
													'certificate_assign_'.$id_cert,
													'certificate_assign['.$id_cert.']',
													$possible_status,
													( isset($course_cert[$id_cert]) ? $course_cert[$id_cert] : 0 ),
													'' );
				$cont[] = Form::getInputDropdown(	'dropdown_nowh',
													'certificate_ex_assign_'.$id_cert,
													'certificate_ex_assign['.$id_cert.']',
													$possible_status,
													( isset($course_ex_cert[$id_cert]) ? $course_ex_cert[$id_cert] : 0 ),
													'' );
				$cont[] = (isset($course_cert[$id_cert]) && $course_cert[$id_cert] != 0 && $view_cert ? '<a href="index.php?modname='.(Docebo::user()->getUserLevelId() == ADMIN_GROUP_PUBLICADMIN ? 'p' : '').'certificate&amp;op=view_report_certificate&amp;id_certificate='.$id_cert.'&amp;id_course='.$id_course.'&amp;from=course&amp;of_platform=lms">' : '').( isset($released[$id_cert]) ? $released[$id_cert] : '0' ).(isset($course_cert[$id_cert]) && $course_cert[$id_cert] != 0  ? '</a>' : '');
				$tb->addBody($cont);
			}

			$course_info = $this->model->getInfo($id_course);
			$course_name = ($course_info['code'] !== '' ? '['.$course_info['code'].'] ' : '').$course_info['name'];

			$this->render(
					'certificate', array(
					'id_course' => $id_course,
					'tb' => $tb,
					'point_required' => $point_required,
					'base_link_course' => $this->base_link_course,
					'course_name' => $course_name
			));
		}
	}

	public function menu()
	{
		if (!$this->permissions['mod']) {
			$this->render('invalid', array(
				'message' => $this->_getErrorMessage('no permission'),
				'back_url' => 'index.php?r='.$this->base_link_course.'/show'
			));
			return;
		}

		if(isset($_POST['undo']))
			Util::jump_to('index.php?r='.$this->base_link_course.'/show');

		$id_course = Get::req('id_course', DOTY_INT, 0);

		if(isset($_POST['assign']))
		{
			$id_custom = Get::req('selected_menu', DOTY_INT, 0);

			require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

			$acl_man =& Docebo::user()->getAclManager();
			$course_man = new Man_Course();

			$levels =& $course_man->getCourseIdstGroupLevel($id_course);
			if(empty($levels) || implode('', $levels) == '')
				$levels =& DoceboCourse::createCourseLevel($id_course);

			$course_man->removeCourseRole($id_course);
			$course_man->removeCourseMenu($id_course);
			$course_idst =& $course_man->getCourseIdstGroupLevel($id_course);

			$result = cerateCourseMenuFromCustom($id_custom, $id_course, $course_idst);

			if($_SESSION['idCourse'] == $id_course)
			{
				$query =	"SELECT module.idModule, main.idMain
							FROM ( ".$GLOBALS['prefix_lms']."_menucourse_main AS main JOIN
							".$GLOBALS['prefix_lms']."_menucourse_under AS un ) JOIN
							".$GLOBALS['prefix_lms']."_module AS module
							WHERE main.idMain = un.idMain AND un.idModule = module.idModule
							AND main.idCourse = '".(int)$_SESSION['idCourse']."'
							AND un.idCourse = '".(int)$_SESSION['idCourse']."'
							ORDER BY main.sequence, un.sequence
							LIMIT 0,1";

				list($id_module, $id_main) = sql_fetch_row(sql_query($query));

				$_SESSION['current_main_menu'] = $id_main;
				$_SESSION['sel_module_id'] = $id_module;

				//loading related ST
				Docebo::user()->loadUserSectionST('/lms/course/public/');
				Docebo::user()->SaveInSession();
			}

			if($result)
				Util::jump_to('index.php?r='.$this->base_link_course.'/show&res=_up_menu_ok');
			Util::jump_to('index.php?r='.$this->base_link_course.'/show&res=_up_menu_err');
		}
		else
		{
			require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
			$menu_custom = getAllCustom();
			$menu_custom = array(0 => Lang::t('_SELECT', 'standard').' ...' ) + $menu_custom;
			reset($menu_custom);

			$course_info = $this->model->getInfo($id_course);
			$course_name = ($course_info['code'] !== '' ? '['.$course_info['code'].'] ' : '').$course_info['name'];

			$this->render('menu', array(
				'menu_custom' => $menu_custom,
				'sel_custom' => 0,
				'id_course' => $id_course,
				'base_link_course' => $this->base_link_course,
				'course_name' => $course_name
			));
		}
	}

	public function newcourse()
	{
		if (!$this->permissions['add']) {
			$this->render('invalid', array(
				'message' => $this->_getErrorMessage('no permission'),
				'back_url' => 'index.php?r='.$this->base_link_course.'/show'
			));
			return;
		}

		if(isset($_POST['undo']))
			Util::jump_to('index.php?r='.$this->base_link_course.'/show');

		if(isset($_POST['save']))
		{
			//resolve course type
			if($_POST['course_type'] == 'edition') {

				$_POST['course_type'] = 'elearning';
				$_POST['course_edition'] = 1;
			} else {

				$_POST['course_edition'] = 0;
			}

			$result = $this->model->insCourse();
			$url = 'index.php?r='.$this->base_link_course.'/show';
			foreach($result as $key => $value)
				$url .= '&'.$key.'='.$value;
			Util::jump_to($url);
		}
		else
			$this->coursemask();
	}

	public function modcourse()
	{
		if (!$this->permissions['mod']) {
			$this->render('invalid', array(
				'message' => $this->_getErrorMessage('no permission'),
				'back_url' => 'index.php?r='.$this->base_link_course.'/show'
			));
			return;
		}

		if(isset($_POST['undo']))
			Util::jump_to('index.php?r='.$this->base_link_course.'/show');

		$id_course = Get::req('id_course', DOTY_INT, 0);

		if(isset($_POST['save']))
		{
			//resolve course type
			if($_POST['course_type'] == 'edition') {

				$_POST['course_type'] = 'elearning';
				$_POST['course_edition'] = 1;
			} else {

				$_POST['course_edition'] = 0;
			}

			$result = $this->model->upCourse();
			$url = 'index.php?r='.$this->base_link_course.'/show';
			foreach($result as $key => $value)
				$url .= '&'.$key.'='.$value;
			Util::jump_to($url);
		}
		else
			$this->coursemask($id_course);
	}

	public function delcourse()
	{
		if (!$this->permissions['del']) {
			$output = array('success' => false, 'message' => $this->_getMessage("no permission"));
			echo $this->json->encode($output);
			return;
		}

		if(Get::cfg('demo_mode'))
			die('Cannot del course during demo mode.');

		if(isset($_GET['confirm']))
		{
			$id_course = Get::req('id_course', DOTY_INT, 0);

			$res = array('success' => $this->model->delCourse($id_course));

			echo $this->json->encode($res);
		}
	}

	public function coursemask($id_course = false)
	{
		$perm_requested = $id_course ? 'mod' : 'add';
		if (!$this->permissions[$perm_requested]) {
			$this->render('invalid', array(
				'message' => $this->_getErrorMessage('no permission'),
				'back_url' => 'index.php?r='.$this->base_link_course.'/show'
			));
			return;
		}

		YuiLib::load();

		require_once(_lms_.'/lib/lib.levels.php');
		require_once(_lms_.'/admin/models/LabelAlms.php');
		$levels = CourseLevel::getLevels();
		$label_model = new LabelAlms();

		$array_lang = Docebo::langManager()->getAllLangCode();
		$array_lang[] = 'none';

		//status of course -----------------------------------------------------
		$status = array(
			CST_PREPARATION => Lang::t('_CST_PREPARATION', 'course'),
			CST_AVAILABLE 	=> Lang::t('_CST_AVAILABLE', 'course'),
			CST_EFFECTIVE 	=> Lang::t('_CST_CONFIRMED', 'course'),
			CST_CONCLUDED 	=> Lang::t('_CST_CONCLUDED', 'course'),
			CST_CANCELLED 	=> Lang::t('_CST_CANCELLED', 'course'));
		//difficult ------------------------------------------------------------
		$difficult_lang = array(
			'veryeasy' 		=> Lang::t('_DIFFICULT_VERYEASY', 'course'),
			'easy' 			=> Lang::t('_DIFFICULT_EASY', 'course'),
			'medium' 		=> Lang::t('_DIFFICULT_MEDIUM', 'course'),
			'difficult' 	=> Lang::t('_DIFFICULT_DIFFICULT', 'course'),
			'verydifficult' => Lang::t('_DIFFICULT_VERYDIFFICULT', 'course'));
		//type of course -------------------------------------------------------
		$course_type= array (
			'classroom' 	=> Lang::t('_CLASSROOM', 'course'),
			'elearning' 	=> Lang::t('_COURSE_TYPE_ELEARNING', 'course'),
			'edition'		=> Lang::t('_COURSE_TYPE_EDITION', 'course')
		);
			
		$show_who_online = array(
			0				=> Lang::t('_DONT_SHOW', 'course'),
			_SHOW_COUNT 	=> Lang::t('_SHOW_COUNT', 'course'),
			_SHOW_INSTMSG 	=> Lang::t('_SHOW_INSTMSG', 'course'));

		$hours = array('-1' => '- -', '0' =>'00', '01', '02', '03', '04', '05', '06', '07', '08', '09',
					'10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
					'20', '21', '22', '23' );
		$quarter = array('-1' => '- -', '00' => '00', '15' => '15', '30' => '30', '45' => '45');

		$params = array(
			'id_course' => $id_course,
			'levels' => $levels,
			'array_lang' => $array_lang,
			'label_model' => $label_model,
			'status' => $status,
			'difficult_lang' => $difficult_lang,
			'course_type' => $course_type,
			'show_who_online' => $show_who_online,
			'hours' => $hours,
			'quarter' => $quarter,
			'model' => $this->model
		);

		if($id_course === false)
		{
			require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
			$menu_custom = getAllCustom();
			list($sel_custom) = current($menu_custom);
			reset($menu_custom);

			$params['menu_custom'] = $menu_custom;
			$params['sel_custom'] = $sel_custom;

			$params['name_category'] = $this->model->getCategoryName($this->_getSessionTreeData('id_category', 0));
		}

		$params['course'] = $this->model->getCourseModDetails($id_course);
		//resolve edition flag into type
		if($params['course']['course_edition'] == 1) $params['course']['course_type'] = 'edition';

		if($id_course == false) $params['has_classroom'] = false;
		else $params['has_classroom'] = $this->model->hasEdionOrClassroom($id_course);

		if($params['course']['hour_begin'] != '-1') {
			$hb_sel = (int)substr($params['course']['hour_begin'], 0, 2);
			$qb_sel = substr($params['course']['hour_begin'], 3, 2);
		} else {
			$hb_sel = $qb_sel = '-1';
		}
		if($params['course']['hour_end'] != '-1')
		{
			$he_sel = (int)substr($params['course']['hour_end'], 0, 2);
			$qe_sel = substr($params['course']['hour_end'], 3, 2);
		} else {
			$he_sel = $qe_sel = '-1';
		}
		$params['hb_sel'] = $hb_sel;
		$params['qb_sel'] = $qb_sel;
		$params['he_sel'] = $he_sel;
		$params['qe_sel'] = $qe_sel;
		$params['base_link_course'] = $this->base_link_course;

		$params['use_unsubscribe_date_limit'] = (bool)($params['course']['unsubscribe_date_limit'] != '');
		$params['unsubscribe_date_limit'] = $params['course']['unsubscribe_date_limit'] != '' && $params['course']['unsubscribe_date_limit'] != "0000-00-00 00:00:00" 
			? Format::date($params['course']['unsubscribe_date_limit'], 'date')
			: "";

		$this->render('maskcourse', $params);
	}
}
?>