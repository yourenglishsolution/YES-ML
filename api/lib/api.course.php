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

	/***********************************
	 * YES SAS - Your English Solution
	 * Author: Rocky SIGNAVONG
	 * Created Date: 16/03/2011
	 * Modified Date: 16/03/2011
	 * Function: generateCode
	 * Description: api for generate new call and return it
	 ************************************/
	
	public function generateCode($req) {
		require_once($GLOBALS['where_framework'].'/lib/lib.code.php');

		$code_man = new CodeManager();
		
		$step = 2;
		$id_code_group = 2;//$req['groupe'];
		$code_number = 1;
		$use_number = 1;
		$use_low_letter = 1;
		$use_high_letter = 1;
		$unlimited_use = 0;
		$generate = 'generate';
		$modname = 'code';
		$op = 'generate_code';

		if ($unlimited_use == 0)
			$unlimited_use = false;
		else
			$unlimited_use = true;

		if ($use_number == 0)
			$use_number = false;
		else
			$use_number = true;

		if ($use_low_letter == 0)
			$use_low_letter = false;
		else
			$use_low_letter = true;

		if ($use_high_letter == 0)
			$use_high_letter = false;
		else
			$use_high_letter = true;
			
		$all_code = $code_man->getAllCode();

		//for ($i = 1; $i <= $code_number; $i++) {
			$control = true;

			while ($control) {
				$new_code = '';

				if ($use_number && $use_low_letter && $use_high_letter) {
					for ($a = 0; $a < 10; $a++) {
						$seed = mt_rand(0, 15);

						if ($seed > 10)
							$new_code .= mt_rand(0, 9);
						elseif ($seed > 5)
							$new_code .= chr(mt_rand(65, 90));
						else
							$new_code .= chr(mt_rand(97, 122));
					}
				}
				elseif ($use_number && $use_low_letter) {
					for ($a = 0; $a < 10; $a++) {
						$seed = mt_rand(0, 10);

						if ($seed > 5)
							$new_code .= mt_rand(0, 9);
						else
							$new_code .= chr(mt_rand(65, 90));
					}
				}
				elseif ($use_number && $use_high_letter) {
					for ($a = 0; $a < 10; $a++) {
						$seed = mt_rand(0, 10);

						if ($seed > 5)
							$new_code .= mt_rand(0, 9);
						else
							$new_code .= chr(mt_rand(97, 122));
					}
				}
				elseif ($use_low_letter && $use_high_letter) {
					for ($a = 0; $a < 10; $a++) {
						$seed = mt_rand(0, 10);

						if ($seed > 5)
							$new_code .= chr(mt_rand(65, 90));
						else
							$new_code .= chr(mt_rand(97, 122));
					}
				}
				else {
					if ($use_number)
						for ($a = 0; $a < 10; $a++)
							$new_code .= mt_rand(0, 9);

					if ($use_low_letter)
						for ($a = 0; $a < 10; $a++)
							$new_code .= chr(mt_rand(65, 90));

					if ($use_high_letter)
						for ($a = 0; $a < 10; $a++)
							$new_code .= chr(mt_rand(97, 122));
				}

				if (array_search($new_code, $all_code) === false) {
					$all_code[] = $new_code;
					$code_man->addCode($new_code, $id_code_group, $unlimited_use);
					$control = false;
				}
			}
		//}
		if (!$control)
			$result = array('success'=>true,'code'=>$new_code);
		else
			$result = array('success'=>false);
		
		return $result;
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

			
			case 'generatecode': {
				$output = $this->generateCode($_POST);
			} break;

			
			default: $output = parent::call($name, $_POST);
		}
		return $output;
	}

}
