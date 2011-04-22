<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class CoursepathLms extends Model {

	protected $_t_order = false;

	public function  __construct() {}

	public function getUserStartedCoursepath($id_user) {
		
		$query = "SELECT cp.id_path, cp.path_code, cp.path_name, cp.path_descr, cpu.course_completed"
			." FROM %lms_coursepath AS cp"
			." JOIN %lms_coursepath_user AS cpu ON cpu.id_path = cp.id_path"
			." WHERE idUser = ".(int)$id_user
			." ORDER BY cp.path_name";
		$result = sql_query($query);

		$res = array();
		while($row = sql_fetch_assoc($result)) {
			
			$res[$row['id_path']] = $row;
		}

		$query_num_coursepath =	"SELECT id_path, COUNT(*) as courses"
			." FROM %lms_coursepath_courses"
			." WHERE id_path IN (".  implode(',', array_keys($res)).")"
			." GROUP BY id_path";
		$result = sql_query($query_num_coursepath);
		while($o = sql_fetch_object($result)) {
			
			if($o->courses <= $res[$o->id_path]['course_completed']) unset($res[$o->id_path]);
			else {
				$res[$o->id_path]['coursepath_courses'] = $o->courses;
				$res[$o->id_path]['percentage'] = ($res[$o->id_path]['course_completed'] == 0 ? 0 : round(($res[$o->id_path]['course_completed'] / $o->courses) * 100, 0));
			}
		}
		return $res;
	}

	public function getUserFinishedCoursepath($id_user) {
		
		$query = "SELECT cp.id_path, cp.path_code, cp.path_name, cp.path_descr, cpu.course_completed"
			." FROM %lms_coursepath AS cp"
			." JOIN %lms_coursepath_user AS cpu ON cpu.id_path = cp.id_path"
			." WHERE idUser = ".(int)$id_user
			." ORDER BY cp.path_name";
		$result = sql_query($query);

		$res = array();
		while($row = sql_fetch_assoc($result)) {

			$res[$row['id_path']] = $row;
		}
		
		$query_num_coursepath =	"SELECT id_path, COUNT(*) as courses"
			." FROM %lms_coursepath_courses"
			." WHERE id_path IN (".  implode(',', array_keys($res)).")"
			." GROUP BY id_path";
		$result = sql_query($query_num_coursepath);
		while($o = sql_fetch_object($result)) {

			if($o->courses > $res[$o->id_path]['course_completed']) unset($res[$o->id_path]);
			else {
				$res[$o->id_path]['coursepath_courses'] = $o->courses;
				$res[$o->id_path]['percentage'] = ($res[$o->id_path]['course_completed'] == 0 ? 0 : round(($res[$o->id_path]['course_completed'] / $o->courses) * 100, 0));
			}
		}
		return $res;
	}

	public function getCoursepathPercentage($array_coursepath = array())
	{
		$res = array();

		if(is_array($array_coursepath) && !empty($array_coursepath))
		{
			foreach($array_coursepath as $id_path)
			{
				$query_completed =	"SELECT COUNT(*)"
									." FROM %lms_courseuser AS cu"
									." JOIN %lms_coursepath_courses AS cpc ON cu.idCourse = cpc.id_item"
									." WHERE cu.status = "._CUS_END
									." AND cpc.id_path = ".$id_path;

				list($completed_number) = sql_fetch_row(sql_query($query_completed));

				$query_num_coursepath =	"SELECT COUNT(*)"
										." FROM %lms_coursepath_courses"
										." WHERE id_path = ".$id_path;

				list($num_course_in_path) = sql_fetch_row(sql_query($query_num_coursepath));

				$res[$id_path]['percentage'] = ($completed_number == 0 ? 0 : round(($completed_number / $num_course_in_path) * 100, 0));
				$res[$id_path]['total'] = $num_course_in_path;
				$res[$id_path]['done'] = $completed_number;
			}
		}

		return $res;
	}

	public function getCoursepathCourseDetails($array_coursepath = array())
	{
		$query =	"SELECT c.idCourse, c.name, c.course_type, c.course_edition, cu.status, cpc.prerequisites, cpc.id_path, cpc.sequence"
					." FROM %lms_course AS c"
					." JOIN %lms_courseuser AS cu ON c.idCourse = cu.idCourse"
					." JOIN %lms_coursepath_courses AS cpc ON c.idCourse = cpc.id_item"
					.(is_array($array_coursepath) && !empty($array_coursepath) ? " WHERE cpc.id_path IN (".implode(',',$array_coursepath).")" : " WHERE 0")
					." GROUP BY cpc.id_path, c.idCourse"
					." ORDER BY cpc.id_path, cpc.sequence";

		$result = sql_query($query);
		$res = array();

		while($row = sql_fetch_assoc($result))
			$res[$row['id_path']][$row['idCourse']] = $row;

		return $res;
	}
}