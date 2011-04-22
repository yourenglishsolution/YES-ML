<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class CatalogLmsController extends LmsController {

	public $name = 'catalog';

	private $path_course = '';

	protected $_default_action = 'show';

	var $model;
	var $json;
	var $acl_man;

	public function isTabActive($tab_name)
	{
		return true;
	}

	public function init()
	{
		YuiLib::load('base,tabview');
		Lang::init('course');
		$this->path_course = $GLOBALS['where_files_relative'].'/doceboLms/'.Get::sett('pathcourse').'/';
		$this->model = new CatalogLms();

		require_once(_base_.'/lib/lib.json.php');
		$this->json = new Services_JSON();

		$this->acl_man =& Docebo::user()->getAclManager();
	}

	public function show()
	{
		$this->allCourse();
	}

	public function allCourse()
	{
		require_once(_base_.'/lib/lib.navbar.php');
		require_once(_lms_.'/lib/lib.middlearea.php');
		$active_tab = 'all';
		$action = Get::req('action', DOTY_STRING, '');

		$page = Get::req('page', DOTY_INT, 1);
		$id_cat = Get::req('id_cat', DOTY_INT, 0);

		$nav_bar = new NavBar('page', Get::sett('visuItem'), $this->model->getTotalCourseNumber($active_tab), 'link');

		$nav_bar->setLink('index.php?r=catalog/allCourse'.($id_cat > 1 ? '&amp;id_cat='.$id_cat : ''));

		$html = $this->model->getCourseList($active_tab, $page);
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		$ma = new Man_MiddleArea();

		echo '<div style="margin:1em;">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab,
											'user_coursepath' => $user_coursepath,
											'std_link' => 'index.php?r=catalog/allCourse'.($page > 1 ? '&amp;page='.$page : ''),
											'model' => $this->model,
											'ma' => $ma));
		$this->render('courselist', array(	'html' => $html,
											'nav_bar' => $nav_bar));
		$this->render('tab_end', array(	'std_link' => 'index.php?r=catalog/allCourse'.($page > 1 ? '&amp;page='.$page : ''),
										'model' => $this->model));
		$lmstab->endWidget();

		echo '</div>';
	}

	public function newCourse()
	{
		require_once(_base_.'/lib/lib.navbar.php');
		$active_tab = 'new';

		$page = Get::req('page', DOTY_INT, 1);
		$id_cat = Get::req('id_cat', DOTY_INT, 0);

		$nav_bar = new NavBar('page', Get::sett('visuItem'), $this->model->getTotalCourseNumber($active_tab), 'link');

		$nav_bar->setLink('index.php?r=catalog/newCourse'.($id_cat > 1 ? '&amp;id_cat='.$id_cat : ''));

		$html = $this->model->getCourseList($active_tab, $page);
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		echo '<div class="layout_colum_container"><div style="margin:1em">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab,
											'user_coursepath' => $user_coursepath,
											'std_link' => 'index.php?r=catalog/newCourse'.($page > 1 ? '&amp;page='.$page : ''),
											'model' => $this->model));
		$this->render('courselist', array(	'html' => $html,
											'nav_bar' => $nav_bar));
		$this->render('tab_end', array(	'std_link' => 'index.php?r=catalog/newCourse'.($page > 1 ? '&amp;page='.$page : ''),
										'model' => $this->model));
		$lmstab->endWidget();

		echo '</div>';
	}

	public function elearningCourse()
	{
		require_once(_base_.'/lib/lib.navbar.php');
		$active_tab = 'elearning';

		$page = Get::req('page', DOTY_INT, 1);
		$id_cat = Get::req('id_cat', DOTY_INT, 0);

		$nav_bar = new NavBar('page', Get::sett('visuItem'), $this->model->getTotalCourseNumber($active_tab), 'link');

		$nav_bar->setLink('index.php?r=catalog/elearningCourse'.($id_cat > 1 ? '&amp;id_cat='.$id_cat : ''));

		$html = $this->model->getCourseList($active_tab, $page);
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		echo '<div class="layout_colum_container"><div style="margin:1em">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab,
											'user_coursepath' => $user_coursepath,
											'std_link' => 'index.php?r=catalog/elearningCourse'.($page > 1 ? '&amp;page='.$page : ''),
											'model' => $this->model));
		$this->render('courselist', array(	'html' => $html,
											'nav_bar' => $nav_bar));
		$this->render('tab_end', array(	'std_link' => 'index.php?r=catalog/elearningCourse'.($page > 1 ? '&amp;page='.$page : ''),
										'model' => $this->model));
		$lmstab->endWidget();

		echo '</div>';
	}

	public function classroomCourse()
	{
		require_once(_base_.'/lib/lib.navbar.php');
		$active_tab = 'classroom';

		$page = Get::req('page', DOTY_INT, 1);
		$id_cat = Get::req('id_cat', DOTY_INT, 0);

		$nav_bar = new NavBar('page', Get::sett('visuItem'), $this->model->getTotalCourseNumber($active_tab), 'link');

		$nav_bar->setLink('index.php?r=catalog/classroomCourse'.($id_cat > 1 ? '&amp;id_cat='.$id_cat : ''));

		$html = $this->model->getCourseList($active_tab, $page);
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		echo '<div class="layout_colum_container"><div style="margin:1em">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab,
											'user_coursepath' => $user_coursepath,
											'std_link' => 'index.php?r=catalog/classroomCourse'.($page > 1 ? '&amp;page='.$page : ''),
											'model' => $this->model));
		$this->render('courselist', array(	'html' => $html,
											'nav_bar' => $nav_bar));
		$this->render('tab_end', array(	'std_link' => 'index.php?r=catalog/classroomCourse'.($page > 1 ? '&amp;page='.$page : ''),
										'model' => $this->model));
		$lmstab->endWidget();

		echo '</div>';
	}

	public function catalogueCourse()
	{
		require_once(_base_.'/lib/lib.navbar.php');
		$id_cata = Get::req('id_cata', DOTY_INT, 0);
		$active_tab = 'catalogue';

		$page = Get::req('page', DOTY_INT, 1);
		$id_cat = Get::req('id_cat', DOTY_INT, 0);

		$nav_bar = new NavBar('page', Get::sett('visuItem'), $this->model->getTotalCourseNumber($active_tab), 'link');

		$nav_bar->setLink('index.php?r=catalog/catalogueCourse&amp;id_cata='.$id_cata.($id_cat > 1 ? '&amp;id_cat='.$id_cat : ''));

		$html = $this->model->getCourseList($active_tab, $page);
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		echo '<div class="layout_colum_container"><div style="margin:1em">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab.'_'.$id_cat,
											'user_coursepath' => $user_coursepath,
											'std_link' => 'index.php?r=catalog/catalogueCourse&amp;id_cata='.$id_cata.($page > 1 ? '&amp;page='.$page : ''),
											'model' => $this->model));
		$this->render('courselist', array(	'html' => $html,
											'nav_bar' => $nav_bar));
		$this->render('tab_end', array(	'std_link' => 'index.php?r=catalog/catalogueCourse&amp;id_cata='.$id_cata.($page > 1 ? '&amp;page='.$page : ''),
										'model' => $this->model));
		$lmstab->endWidget();

		echo '</div>';
	}

	public function coursepathCourse()
	{
		require_once(_base_.'/lib/lib.navbar.php');
		$active_tab = 'coursepath';

		$nav_bar = new NavBar('page', Get::sett('visuItem'), count($this->model->getUserCoursepath(Docebo::user()->getIdSt())), 'link');

		$nav_bar->setLink('index.php?r=catalog/coursepathCourse');

		$page = Get::req('page', DOTY_INT, 1);

		$html = $this->model->getCoursepathList(Docebo::user()->getIdSt(), $page);
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		echo '<div class="layout_colum_container"><div style="margin:1em">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab,
											'user_coursepath' => $user_coursepath));
		$this->render('courselist', array(	'html' => $html,
											'nav_bar' => $nav_bar));
		$this->render('tab_end', array());
		$lmstab->endWidget();

		echo '</div>';
	}

	public function calendarCourse()
	{
		$active_tab = 'calendar';
		$user_catalogue = $this->model->getUserCatalogue(Docebo::user()->getIdSt());
		$user_coursepath = $this->model->getUserCoursepath(Docebo::user()->getIdSt());

		echo '<div class="layout_colum_container"><div style="margin:1em">';

		$lmstab = $this->widget('lms_tab', array(
								'active' => 'catalog',
								'close' => false));

		$this->render('tab_start', array(	'user_catalogue' => $user_catalogue,
											'active_tab' => $active_tab,
											'user_coursepath' => $user_coursepath));
		$this->render('calendar', array());
		$this->render('tab_end', array());
		$lmstab->endWidget();

		echo '</div>';
	}

	public function subscribeInfo()
	{
		$id_course = Get::req('id_course', DOTY_INT, 0);
		$id_date = Get::req('id_date', DOTY_INT, 0);
		$id_edition = Get::req('id_edition', DOTY_INT, 0);
		$selling = Get::req('selling', DOTY_INT, 0);

		$res = $this->model->subscribeInfo($id_course, $id_date, $id_edition, $selling);

		echo $this->json->encode($res);
	}

	public function courseSelection()
	{
		$id_course = Get::req('id_course', DOTY_INT, 0);
		$selling = Get::req('selling', DOTY_INT, 0);

		$res = $this->model->courseSelectionInfo($id_course, $selling);

		echo $this->json->encode($res);
	}

	public function subscribeToCourse()
	{
		$id_course = Get::req('id_course', DOTY_INT, 0);
		$id_date = Get::req('id_date', DOTY_INT, 0);
		$id_edition = Get::req('id_edition', DOTY_INT, 0);

		$id_user = Docebo::user()->getIdSt();

		$docebo_course = new DoceboCourse($id_course);

		require_once(_lms_.'/admin/models/SubscriptionAlms.php');
		$model = new SubscriptionAlms($id_course, $id_edition, $id_date);

		$course_info = $model->getCourseInfoForSubscription();

		$level_idst =& $docebo_course->getCourseLevel($id_course);

		if(count($level_idst) == 0 || $level_idst[1] == '')
			$level_idst =& $docebo_course->createCourseLevel($id_course);

		$waiting = 0;

		if($course_info['subscribe_method'] != 2)
			$waiting = 1;

		$this->acl_man->addToGroup($level_idst[3], $id_user);

		if($model->subscribeUser($id_user, 3, $waiting))
		{
			$res['success'] = true;
			if($id_edition != 0 || $id_date != 0)
			{
				$must_change_status = $this->model->controlSubscriptionRemaining($id_course);
				$res['new_status'] = '';

				if(!$must_change_status)
					$res['new_status'] = '<p class="cannot_subscribe">'.Lang::t('_NO_EDITIONS', 'catalogue').'</p>';
			}
			else
			{
				if($waiting == 1)
					$res['new_status'] = '<p class="cannot_subscribe">'.Lang::t('_WAITING', 'catalogue').'</p>';
				else
					$res['new_status'] = '<p class="cannot_subscribe">'.Lang::t('_USER_STATUS_SUBS', 'catalogue').'</p>';
			}

			$res['message'] = UIFeedback::info(Lang::t('_SUBSCRIPTION_CORRECT', 'catalogue'), true);
		}
		else
		{
			$this->acl_man->removeFromGroup($level_idst[3], $id_user);
			$res['success'] = false;

			$res['message'] = UIFeedback::error(Lang::t('_SUBSCRIPTION_ERROR', 'catalogue'), true);
		}

		echo $this->json->encode($res);
	}

	public function addToCart()
	{
		$id_course = Get::req('id_course', DOTY_INT, 0);
		$id_date = Get::req('id_date', DOTY_INT, 0);
		$id_edition = Get::req('id_edition', DOTY_INT, 0);

		if($id_edition != 0)
			$_SESSION['lms_cart'][$id_course]['edition'][$id_edition] = $id_edition;
		elseif($id_date != 0)
			$_SESSION['lms_cart'][$id_course]['classroom'][$id_date] = $id_date;
		else
			$_SESSION['lms_cart'][$id_course] = $id_course;

		$res['success'] = true;
		$res['message'] = UIFeedback::info(Lang::t('_COURSE_ADDED_IN_CART', 'catalogue'), true);

		if($id_edition != 0 || $id_date != 0)
		{
			$must_change_status = $this->model->controlSubscriptionRemaining($id_course);
			$res['new_status'] = '';

			if(!$must_change_status)
				$res['new_status'] = '<p class="cannot_subscribe">'.Lang::t('_ALL_EDITION_BUYED', 'catalogue').'</p>';
		}
		else
			$res['new_status'] = '<p class="cannot_subscribe">'.Lang::t('_IN_CART', 'catalogue').'</p>';

		require_once(_lms_.'/lib/lib.cart.php');

		$res['cart_element'] = ''.Learning_Cart::cartItemCount().'';
		$res['num_element'] = Learning_Cart::cartItemCount();
		$res['cart_message'] = Lang::t('_COURSE_ADDED_IN_CART', 'catalogue');

		echo $this->json->encode($res);
	}
}
