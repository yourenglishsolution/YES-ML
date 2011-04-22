<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(_base_.'/api/lib/lib.api.php');

class Course_API extends API {


	public function getCourses($params) {
		require_once(_lms_.'/lib/lib.course.php');
		$output =array();		

		$output['success']=true;

		$id_category =(isset($params['category']) ? (int)$params['category'] : false);

		$course_man =new Man_Course();
		$course_list =$course_man->getAllCoursesWithMoreInfo($id_category);

		
		foreach($course_list as $key=>$course_info) {
			$output[]['course_info']=array(
				'course_id'=>$course_info['idCourse'],
				'code'=>str_replace('&', '&amp;', $course_info['code']),
				'course_name'=>str_replace('&', '&amp;', $course_info['name']),
				'course_description'=>str_replace('&', '&amp;', $course_info['description']),
				'status'=>$course_info['status'],
				'selling'=>$course_info['selling'],
				'price'=>$course_info['prize'],
				'subscribe_method'=>$course_info['subscribe_method'],
				'course_edition'=>$course_info['course_edition'],
				'sub_start_date'=>$course_info['sub_start_date'],
				'sub_end_date'=>$course_info['sub_end_date'],
				'date_begin'=>$course_info['date_begin'],
				'date_end'=>$course_info['date_end'],
				'course_link'=>Get::sett('url')._folder_lms_.'/index.php?modname=course&amp;op=aula&amp;idCourse='.$course_info['idCourse'],
			);
		}

		//$output['debug']=print_r($course_list, true);

		return $output;
	}


	public function getEditions($params) {
		require_once(_lms_.'/lib/lib.course.php');
		require_once(_lms_.'/lib/lib.edition.php');
		$output =array();

		$output['success']=true;

		$course_id =(int)$params['course_id'];

		$edition_man = new EditionManager();
		$course_list =$edition_man->getEditionsInfoByCourses($course_id);

		$course_man =new Man_Course();
		$course =$course_man->getCourseInfo($course_id);

		foreach($course_list[$course_id] as $key=>$course_info) {
			$output[]['course_info']=array(
				'course_id'=>$course['idCourse'],
				'edition_id'=>$course_info['id_edition'],
				'code'=>str_replace('&', '&amp;', $course_info['code']),
				'course_name'=>str_replace('&', '&amp;', $course_info['name']),
				'course_description'=>str_replace('&', '&amp;', $course_info['description']),
				'status'=>$course_info['status'],
				'selling'=>$course['selling'],
				'price'=>$course_info['price'],
				'subscribe_method'=>$course['subscribe_method'],
				'sub_start_date'=>$course_info['sub_date_begin'],
				'sub_end_date'=>$course_info['sub_date_end'],
				'date_begin'=>$course_info['date_begin'],
				'date_end'=>$course_info['date_end'],
				'course_link'=>Get::sett('url')._folder_lms_.'/index.php?modname=course&amp;op=aula&amp;idCourse='.$course['idCourse'],
			);
		}

		//$output['debug']=print_r($course_list, true).print_r($course, true);

		return $output;
	}


	public function getClassrooms($params) {
		require_once(_lms_.'/lib/lib.course.php');
		require_once(_lms_.'/lib/lib.date.php');
		$output =array();

		$output['success']=true;

		$course_id =(int)$params['course_id'];

		$classroom_man = new DateManager();
		$course_list =$classroom_man->getCourseDate($course_id);

		$course_man =new Man_Course();
		$course =$course_man->getCourseInfo($course_id);

		foreach($course_list as $key=>$course_info) {
			$output[]['course_info']=array(
				'course_id'=>$course['idCourse'],
				'date_id'=>$course_info['id_date'],
				'code'=>str_replace('&', '&amp;', $course_info['code']),
				'course_name'=>str_replace('&', '&amp;', $course_info['name']),
				'course_description'=>str_replace('&', '&amp;', $course_info['description']),
				'status'=>$course_info['status'],
				'selling'=>$course['selling'],
				'price'=>$course_info['price'],
				'subscribe_method'=>$course['subscribe_method'],
				'sub_start_date'=>$course_info['sub_start_date'],
				'sub_end_date'=>$course_info['sub_end_date'],
				'date_begin'=>$course_info['date_begin'],
				'date_end'=>$course_info['date_end'],
				'num_day'=>$course_info['num_day'],
				'classroom'=>$course_info['classroom'],
				'course_link'=>Get::sett('url')._folder_lms_.'/index.php?modname=course&amp;op=aula&amp;idCourse='.$course['idCourse'],
			);
		}

		//$output['debug']=print_r($course_list, true).print_r($course, true);

		return $output;
	}


	
	public function call($name, $params) {
		$output = false;

		
		switch ($name) {

			case 'courses': {
				$output = $this->getCourses($_POST);
			} break;


			case 'editions': {
				$output = $this->getEditions($_POST);
			} break;


			case 'classrooms': {
				$output = $this->getClassrooms($_POST);
			} break;


			default: $output = parent::call($name, $_POST);
		}
		return $output;
	}

}
