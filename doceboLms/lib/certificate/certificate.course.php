<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(dirname(__FILE__).'/certificate.base.php');

class CertificateSubs_Course extends CertificateSubstitution {
	
	function getSubstitutionTags() {
		
		$lang =& DoceboLanguage::createInstance('certificate', 'lms');
		
		$subs = array();
		if($_GET['modname'] == 'meta_certificate')
		{

		}
		else
		{
			$subs['[course_code]'] 			= $lang->def('_COURSE_CODE');
			$subs['[course_name]'] 			= $lang->def('_COURSE_NAME');
			$subs['[course_description]'] 	= $lang->def('_COURSE_DESCRIPTION');
			$subs['[date_begin]'] 			= $lang->def('_COURSE_BEGIN');
			$subs['[date_end]'] 			= $lang->def('_COURSE_END');
			$subs['[medium_time]'] 			= $lang->def('_COURSE_MEDIUM_TIME');
			$subs['[ed_date_begin]']		= $lang->def('_ED_DATE_BEGIN');
			$subs['[ed_classroom]'] 		= $lang->def('_ED_CLASSROOM');
			$subs['[teacher_list]'] 		= $lang->def('_TEACHER_LIST');
			$subs['[teacher_list_inverse]'] 	= $lang->def('_TEACHER_LIST_INVERSE');
			$subs['[course_credits]']		= $lang->def('_CREDITS');
		}
	
		return $subs;
	}
	
	function getUserNameInv($idst_user = false, $user_id = false) {
		
		$acl_manager =& Docebo::user()->getAclManager();
		$user_info = $acl_manager->getUser($idst_user, $user_id);

		return ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
			? $user_info[ACL_INFO_FIRSTNAME].' '.$user_info[ACL_INFO_LASTNAME]
			: $acl_manager->relativeId($user_info[ACL_INFO_USERID]) );
	}

	/**
	 * return the list of substitution
	 */
	function getSubstitution() {
		
		$subs = array();
		
		if(isset($_GET['modname']) && $_GET['modname'] == 'meta_certificate') {}
		else {
			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		
			$acl_manager =& Docebo::user()->getAclManager();

			$man_course = new DoceboCourse($this->id_course);

			$query =	"SELECT idUser"
						." FROM ".$GLOBALS['prefix_lms']."_courseuser"
						." WHERE idCourse = '".$this->id_course."'"
						." AND level = '6'";

			$result = sql_query($query);

			$first = true;

			while(list($id_user) = sql_fetch_row($result))
			{
				if($first)
				{
					$subs['[teacher_list]'] = ''.$acl_manager->getUserName($id_user, false);
					$subs['[teacher_list_inverse]'] = ''.$this->getUserNameInv($id_user, false);
					$first = false;
				} else {
					$subs['[teacher_list]'] .= ', '.$acl_manager->getUserName($id_user, false);
					$subs['[teacher_list_inverse]'] .= ', '.$this->getUserNameInv($id_user, false);
				}
			}

			$subs['[course_code]'] 			= $man_course->getValue('code');
			$subs['[course_name]'] 			= $man_course->getValue('name');

			$subs['[date_begin]'] 			= Format::date($man_course->getValue('date_begin'), 'date');
			$subs['[date_end]'] 			= Format::date($man_course->getValue('date_end'), 'date');

			$subs['[course_description]'] 	= html_entity_decode(strip_tags($man_course->getValue('description')), ENT_QUOTES, "UTF-8");

			$subs['[medium_time]'] 			= $man_course->getValue('mediumTime');
			$subs['[course_credits]']		= $man_course->getValue('credits');

			$query =	"SELECT cu.edition_id, ce.date_begin, ce.classrooms"
					." FROM ".$GLOBALS['prefix_lms']."_courseuser AS cu"
					." JOIN ".$GLOBALS['prefix_lms']."_course_edition AS ce ON ce.idCourseEdition = cu.edition_id"
					." WHERE cu.idCourse = ".$this->id_course
					." AND cu.idUser = ".$this->id_user
					." ORDER BY ce.date_begin DESC"
					." LIMIT 0, 1";

			$result = sql_query($query);

			if(sql_num_rows($result) > 0)
			{
				list($edition_id, $date_begin, $classroom) = sql_fetch_row($result);

				if($edition_id !== 0)
				{
					$subs['[ed_date_begin]'] = Format::date($date_begin, 'date');

					$query =	"SELECT name"
							." FROM ".$GLOBALS['prefix_lms']."_classroom"
							." WHERE idClassroom = ".$classroom;

					list($name) = sql_fetch_row(sql_query($query));

					$subs['[ed_classroom]'] = $name;
				}
			}
			else
			{
				$subs['[ed_date_begin]'] = '';
				$subs['[ed_classroom]'] = '';
			}
		}
		
		return $subs;
	}
}

?>