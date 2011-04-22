<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
|   DOCEBO - The E-Learning Suite                                           |
|                                                                           |
|   Copyright (c) 2008 (Docebo)                                             |
|   http://www.docebo.com                                                   |
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt          |
\ ======================================================================== */

define("CERT_ID",					0);
define("CERT_NAME",					1);
define("CERT_DESCR",				2);
define("CERT_LANG",					3);
define("CERT_STRUCTURE",			4);
define("CERT_CODE",					5);

define("CERT_ID_COURSE",			4);
define("CERT_AV_STATUS",			5);
define("CERT_AV_POINT_REQUIRED", 	6);

define("CERTIFICATE_PATH", '/doceboLms/certificate/');

define("AVS_NOT_ASSIGNED", 					0);
define("AVS_ASSIGN_FOR_ALL_STATUS", 		1);
define("AVS_ASSIGN_FOR_STATUS_INCOURSE", 	2);
define("AVS_ASSIGN_FOR_STATUS_COMPLETED", 	3);

define("ASSIGN_CERT_ID", 		0);
define("ASSIGN_COURSE_ID", 		1);
define("ASSIGN_USER_ID", 		2);
define("ASSIGN_OD_DATE", 		3);
define("ASSIGN_CERT_FILE", 		4);

class Certificate {

	function getCertificateList($name_filter = false, $code_filter = false) {
		
		$cert = array(); 
		$query_certificate = "
		SELECT id_certificate, name, description, base_language, cert_structure, code
		FROM ".$GLOBALS['prefix_lms']."_certificate"
		." WHERE meta = 0";
		
		if ($name_filter && $code_filter)
			$query_certificate .= " AND name LIKE '%".$name_filter."%'" .
									" AND code LIKE '%".$code_filter."%'";
		elseif ($name_filter)
			$query_certificate .= " AND name LIKE '%".$name_filter."%'";
		elseif ($code_filter)
			$query_certificate .= " AND code LIKE '%".$code_filter."%'";
		
		$query_certificate .= " ORDER BY name";
		
		$re_certificate = sql_query($query_certificate);
		
		while($row = sql_fetch_row($re_certificate))
		{
			$cert[$row[CERT_ID]] = $row;
		}
		
		return $cert;
	}

	
	function getCourseCertificate($id_course) {
		
		$cert = array(); 
		$query_certificate = "
		SELECT id_certificate, available_for_status
		FROM %lms_certificate_course
		WHERE id_course = '".$id_course."' "
		." AND point_required = 0";
		$re_certificate = sql_query($query_certificate);
		while(list($id, $available_for_status) = sql_fetch_row($re_certificate)) {
			
			$cert[$id] = $available_for_status;
		}
		return $cert;
	}

	function getCourseExCertificate($id_course) {

		$cert = array();
		$query_certificate = "
		SELECT id_certificate, available_for_status
		FROM %lms_certificate_course
		WHERE id_course = '".$id_course."' "
		." AND point_required > 0";
		$re_certificate = sql_query($query_certificate);
		while(list($id, $available_for_status) = sql_fetch_row($re_certificate)) {

			$cert[$id] = $available_for_status;
		}
		return $cert;
	}

	function getPointRequiredForCourse($id_course)
	{
		$query =	"SELECT MAX(point_required)"
					." FROM %lms_certificate_course"
					." WHERE id_course = ".$id_course;

		list($res) = sql_fetch_row(sql_query($query));

		if($res == NULL)
			$res = '0';

		return $res;
	}
	
	/**
	 * @return array 	idcourse => array( idcert => array( CERT_ID, CERT_NAME, CERT_DESCR, CERT_LANG, CERT_STRUCTURE, CERT_ID_COURSE, CERT_AV_STATUS ) )
	 */
	function certificateForCourses($arr_course = false, $base_language = false) {
		
		$query_certificate = ""
		." SELECT c.id_certificate, c.name, c.description, c.base_language, course.id_course, course.available_for_status, course.point_required "
		." FROM ".$GLOBALS['prefix_lms']."_certificate AS c "
		." 		JOIN ".$GLOBALS['prefix_lms']."_certificate_course AS course"
		." WHERE c.id_certificate = course.id_certificate "
		." 		AND course.available_for_status <> '".AVS_NOT_ASSIGNED."' "
		." AND c.user_release = 1";

		if($arr_course !== false && !empty($arr_course))
			$query_certificate .= " AND course.id_course IN ( ".implode(',', $arr_course)." )";

		if($base_language !== false)
			$query_certificate .= " AND c.base_language = '".$base_language."' ";

		$query_certificate .= " ORDER BY course.available_for_status, c.name";
		
		$re = sql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while($row = sql_fetch_row($re)) {
			
			$list_of[$row[CERT_ID_COURSE]][$row[CERT_ID]] = $row;
		}
		return $list_of;
	}
	
	function numberOfCertificateReleased($id_certificate = false) {
		
		$query_certificate = "
		SELECT id_certificate, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE 1 ";
		if($id_certificate !== false) $query_certificate .= " AND id_certificate = '".$id_certificate."' ";
		
		$re = sql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_c, $number) = sql_fetch_row($re)) {
			$list_of[$id_c] = $number;
		}
		reset($list_of);
		if($id_certificate !== false) return current($list_of);
		return $list_of;
	}
	
	function certificateReleased($id_user, $arr_course = false) {
		
		$query_certificate = "
		SELECT id_course, id_certificate, on_date
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_user = '".$id_user."' ";
		if($arr_course) {
			$query_certificate .= " AND id_course IN ( ".implode(',', $arr_course)."";
		}
		$re = sql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_course, $id_cert, $on_date) = sql_fetch_row($re)) {
			
			$list_of[$id_course][$id_cert] = $on_date;
		}
		return $list_of;
	}

	function certificateStatus($id_user, $id_course) {

		$query_certificate = "
		SELECT ca.id_course, c.id_certificate, c.name
		FROM ".$GLOBALS['prefix_lms']."_certificate AS c
			JOIN ".$GLOBALS['prefix_lms']."_certificate_assign AS ca
			ON (c.id_certificate = ca.id_certificate)
		WHERE ca.id_user = ".(int)$id_user." AND ca.id_course = ".(int)$id_course." ";
		
		$re = sql_query($query_certificate);
		if(!$re) return array();

		$list_of = array();
		while(list($id_course, $id_cert, $name) = sql_fetch_row($re)) {

			$list_of[$id_cert] = $name;
		}
		return $list_of;
	}
	
	function certificateReleasedMultiUser($arr_user = false, $arr_course = false) {
		
		$query_certificate = "
		SELECT id_user, id_certificate, id_course, on_date
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE 1 ";
		if(is_array($arr_user) && !empty($arr_user)) {
			$query_certificate .= " AND id_user IN ( ".implode(',', $arr_user)."";
		}
		if(is_array($arr_course) && !empty($arr_course)) {
			$query_certificate .= " AND id_course IN ( ".implode(',', $arr_course)."";
		}
		$re = sql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_user, $id_course, $id_cert, $on_date) = sql_fetch_row($re)) {
			
			$list_of[$id_user][$id_cert]['on_date'] = $on_date;
			$list_of[$id_user][$id_cert]['id_course'] = $id_course;
		}
		return $list_of;
	}
	
	function numOfCertificateReleasedForCourse($id_course) {
		
		$query_certificate = "
		SELECT id_certificate, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_course = '".$id_course."' 
		GROUP BY id_certificate ";
		$re = sql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_cert, $num_of) = sql_fetch_row($re)) {
			$list_of[$id_cert] = $num_of;
		}
		return $list_of;
	}
	
	function certificateReleasedForCourse($id_course) {
		
		$query_certificate = "
		SELECT id_user, id_certificate, on_date
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_course = '".$id_course."' ";
		$re = sql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_user, $id_cert, $on_date) = sql_fetch_row($re)) {
			$list_of[$id_user][$id_cert] = $on_date;
		}
		return $list_of;
	}
	
	function isReleased($id_certificate, $id_course, $id_user) {
		
		$query_certificate = "
		SELECT cert_file
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_certificate = '".$id_certificate."'
			 AND id_course = '".$id_course."' 
			 AND id_user = '".$id_user."' ";
		
		$re = sql_query($query_certificate);
		if(!$re) return false;
		return (mysql_num_rows($re) > 0);
	}
	
	function canRelease($av_for_status, $user_status) {
		
		require_once(_lms_.'/lib/lib.course.php');
		
		switch($av_for_status) {
			case AVS_NOT_ASSIGNED 				: { return false; };break;
			case AVS_ASSIGN_FOR_ALL_STATUS 		: { return true; };break;
			case AVS_ASSIGN_FOR_STATUS_INCOURSE : { return ($user_status == _CUS_BEGIN); };break;
			case AVS_ASSIGN_FOR_STATUS_COMPLETED : { return ($user_status == _CUS_END); };break;
		}
		return false;
	}
	
	function updateCertificateCourseAssign($id_course, $list_of_assign, $list_of_assign_ex, $point_required)
	{
		$query =	"DELETE FROM ".$GLOBALS['prefix_lms']."_certificate_course"
					." WHERE id_course = ".$id_course;

		if(!sql_query($query))
			return false;

		if(is_array($list_of_assign) && !empty($list_of_assign))
			foreach($list_of_assign as $id_cert => $status)
				if($status != 0)
				{
					$query =	"INSERT INTO %lms_certificate_course"
								." (id_certificate, id_course, available_for_status)"
								." VALUES (".(int)$id_cert.", ".(int)$id_course.", ".(int)$status.")";

					if(!sql_query($query))
						return false;
				}

		if(is_array($list_of_assign_ex) && !empty($list_of_assign_ex) && $point_required > 0)
			foreach($list_of_assign_ex as $id_cert => $status)
				if($status != 0)
				{
					$query =	"INSERT INTO %lms_certificate_course"
								." (id_certificate, id_course, available_for_status, point_required)"
								." VALUES (".(int)$id_cert.", ".(int)$id_course.", ".(int)$status.", ".(int)$point_required.")";

					if(!sql_query($query))
						return false;
				}

		return true;
	}
	
	function getSubstitutionArray($id_user, $id_course) {
		
		$query_certificate = "
		SELECT file_name, class_name
		FROM ".$GLOBALS['prefix_lms']."_certificate_tags ";
		$re = sql_query($query_certificate);
		
		$subst = array();
		while(list($file_name, $class_name) = sql_fetch_row($re)) {
			
			if(file_exists(_lms_.'/lib/certificate/'.$file_name)) {
				
				require_once(_lms_.'/lib/certificate/'.$file_name);
				$instance = new $class_name($id_user, $id_course);
				$this_subs = $instance->getSubstitution();
				$subst = $subst + $this_subs;
			}
		}
		return $subst;	
	}
	
	function send_preview_certificate($id_certificate, $array_substituton = false) {
		
		$query_certificate = "
		SELECT name, cert_structure, base_language, orientation, bgimage
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($name, $cert_structure, $base_language, $orientation, $bgimage) = sql_fetch_row(sql_query($query_certificate));
		
		//require_once($GLOBALS['where_framework'].'/addons/html2pdf/html2fpdf.php');
		
		if($array_substituton !== false) {
			$cert_structure = str_replace(array_keys($array_substituton), $array_substituton, $cert_structure);
		}
		$cert_structure = fillSiteBaseUrlTag($cert_structure);
		
		$name = str_replace(
			array('\\', '/', 	':', 	'\'', 	'\*', 	'?', 	'"', 	'<', 	'>', 	'|'),
			array('', 	'', 	'', 	'', 	'', 	'', 	'', 	'', 	'', 	'' ),
			$name
		);
		
		$name .= '.pdf';
		
		$this->getPdf($cert_structure, $name, $bgimage, $orientation, true, false);
	}
	
	function getPdf($html, $name, $img = false, $orientation = 'P', $download = true, $facs_simile = false, $for_saving = false)
	{
		
		require_once(_base_.'/addons/tcpdf/tcpdf.php');

		$pdf = new PDF($orientation);
			
		if($for_saving)
			return $pdf->getPdf($html, $name, $img, $download, $facs_simile, $for_saving);
		else
			$pdf->getPdf($html, $name, $img, $download, $facs_simile, $for_saving);
	}
	
	function send_facsimile_certificate($id_certificate, $id_user, $id_course, $array_substituton = false)
	{
		$query_certificate = "
		SELECT name, cert_structure, base_language, orientation, bgimage
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($name, $cert_structure, $base_language, $orientation, $bgimage) = sql_fetch_row(sql_query($query_certificate));
		
		if($array_substituton !== false) {
			$cert_structure = str_replace(array_keys($array_substituton), $array_substituton, $cert_structure);
		}
		$cert_structure = fillSiteBaseUrlTag($cert_structure);
		
		$this->getPdf($cert_structure, $name, $bgimage, $orientation, true, true);
	}
	
	function send_certificate($id_certificate, $id_user, $id_course, $array_substituton = false, $download = true, $from_multi = false)
	{
		$id_meta = Get::req('idmeta', DOTY_INT, 0);
		
		if(!isset($_GET['idmeta']))
			$query_certificate = "
			SELECT cert_file
			FROM ".$GLOBALS['prefix_lms']."_certificate_assign 
			WHERE id_certificate = '".$id_certificate."'
				 AND id_course = '".$id_course."' 
				 AND id_user = '".$id_user."' ";
		else
			$query_certificate = "
			SELECT cert_file
			FROM ".$GLOBALS['prefix_lms']."_certificate_meta_assign 
			WHERE idUser = '".$id_user."'
			AND idMetaCertificate = '".$id_meta."'";
		
		$re = sql_query($query_certificate);
		echo mysql_error();
		if((mysql_num_rows($re) > 0)) {
			if(!$download)
				return;
			require_once(_base_.'/lib/lib.download.php' );
			list($cert_file) = sql_fetch_row($re);
			sendFile(CERTIFICATE_PATH, $cert_file);
			return;
		}
		
		$query_certificate = "
		SELECT name, cert_structure, base_language, orientation, bgimage
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($name, $cert_structure, $base_language, $orientation, $bgimage) = sql_fetch_row(sql_query($query_certificate));
		
		require_once(_base_.'/lib/lib.upload.php');
		
		if($array_substituton !== false) {
			$cert_structure = str_replace(array_keys($array_substituton), $array_substituton, $cert_structure);
		}
		$cert_structure = fillSiteBaseUrlTag($cert_structure);
		
		$cert_file = $id_course.'_'.$id_certificate.'_'.$id_user.'_'.time().'_'.$name.'.pdf';
		
		sl_open_fileoperations();
		if(!$fp = sl_fopen(CERTIFICATE_PATH.$cert_file, 'w')) { sl_close_fileoperations(); return false; }
		if(!fwrite($fp, $this->getPdf($cert_structure, $name, $bgimage, $orientation, false, false, true))) { sl_close_fileoperations(); return false; }
		fclose($fp);
		sl_close_fileoperations();

		//save the generated file in database
		if(!isset($_GET['idmeta']))
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_certificate_assign " 
			." ( id_certificate, id_course, id_user, on_date, cert_file ) "
			." VALUES "
			." ( '".$id_certificate."', '".$id_course."', '".$id_user."', '".date("Y-m-d H:i:s")."', '".addslashes($cert_file)."' ) ";
		else
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_certificate_meta_assign "
			." ( idUser, idMetaCertificate, idCertificate, on_date, cert_file ) "
			." VALUES "
			." ('".$id_user."', '".$id_meta."', '".$id_certificate."', '".date("Y-m-d H:i:s")."', '".addslashes($cert_file)."' ) ";
		
		if(!sql_query($query)) return false;

		if($from_multi)
			return;

		$this->getPdf($cert_structure, $name, $bgimage, $orientation, $download, false);
	}
	
	function getCourseForCertificate($id_certificate)
	{
		$id_course = array();
		
		$query_id_course = "SELECT id_course" .
						" FROM ".$GLOBALS['prefix_lms']."_certificate_course" .
						" WHERE id_certificate = '".$id_certificate."'" .
						" AND available_for_status <> '".AVS_NOT_ASSIGNED."'";
		
		$result_id_course = sql_query($query_id_course);
		
		while (list($id_course_find) = sql_fetch_row($result_id_course))
			$id_course[] = $id_course_find;
		
		return $id_course;
	}
	
	function getInfoForCourseCertificate($id_course, $id_certificate, $id_user = false)
	{
		$info = array();
		
		$query = "SELECT *" .
				" FROM ".$GLOBALS['prefix_lms']."_certificate_assign" .
				" WHERE id_certificate = '".$id_certificate."'" .
				" AND id_course = '".$id_course."'";
		if ($id_user)
			$query .= " AND id_user = $id_user";
		
		$result = sql_query($query);
		
		while ($row = sql_fetch_row($result))
			$info[] = $row; 
		
		return $info;
	}
	
	function getCertificateInfo($id_certificate)
	{
		$info = array();
		$query = "SELECT id_certificate, name, description, base_language, cert_structure FROM ".$GLOBALS['prefix_lms']."_certificate ";
		if (is_array($id_certificate) && count($id_certificate)>0) {
			$query .= " WHERE id_certificate IN ('".implode("','", $id_certificate)."')";
		} else {
			$query .= " WHERE id_certificate = '".(int)$id_certificate."'";
		}
		$result = sql_query($query);
		
		while ($row = sql_fetch_row($result))
			$info[$row[CERT_ID]] = $row; 
		
		return $info;
	}
	
	function delCertificateForUserInCourse($id_certificate, $id_user, $id_course)
	{
		$query = "DELETE " .
				" FROM ".$GLOBALS['prefix_lms']."_certificate_assign " .
				" WHERE id_certificate = '".$id_certificate."'" .
				" AND id_course = '".$id_course."'" .
				" AND id_user = '".$id_user."'";
		
		return sql_query($query);
	}
	
	function getNumberOfCertificateForCourse($id_certificate, $id_course)
	{
		$query = "SELECT COUNT(*)" .
				" FROM ".$GLOBALS['prefix_lms']."_certificate_assign" .
				" WHERE id_certificate = '".$id_certificate."'" .
				" AND id_course = '".$id_course."'";
		
		list ($res) = sql_fetch_row(sql_query($query));
		
		return $res;
	}


	function getCertificateQuery($users = false, $id_cert = false, $year = false) {
		$conditions = array();

		if ($users) {
			if (is_numeric($users)) $users = array($users);
			if (is_array($users)) $conditions[] = " t3.idst IN (".implode(',', $users).") ";
		}

		if ($id_cert) {
			if (is_numeric($id_cert)) $conditions[] = " t1.id_certificate = '".(int)$id_cert."' ";
		}

		if ($year) {
			if (is_numeric($year)) $conditions[] = " YEAR(t2.ondate) = '".(int)$year."' ";
		}

		$query = "SELECT t1.code, t1.name, YEAR(t2.on_date) as year, t3.firstname, t3.lastname "
			." FROM ".$GLOBALS['prefix_lms']."_certificate as t1 JOIN ".$GLOBALS['prefix_lms']."_certificate_assign as t2 JOIN ".$GLOBALS['prefix_fw']."_user as t3 "
			." ON (t1.id_certificate = t2.id_certificate AND t2.id_user = t3.idst)  "
			.(count($conditions)>0 ? "WHERE ".implode(" AND ", $conditions) : "")
			." ORDER BY t3.lastname, t3.firstname, t1.name";

		return $query;
	}


	function getCertificateQueryTotal($users = false, $id_cert = false, $year = false) {
		$conditions = array();

		if ($users) {
			if (is_numeric($users)) $users = array($users);
			if (is_array($users)) $conditions[] = " t3.idst IN (".implode(',', $users).") ";
		}

		if ($id_cert) {
			if (is_numeric($id_cert)) $conditions[] = " t1.id_certificate = '".(int)$id_cert."' ";
		}

		if ($year) {
			if (is_numeric($year)) $conditions[] = " YEAR(t2.ondate) = '".(int)$year."' ";
		}

		$query = "SELECT COUNT(*) "
			." FROM ".$GLOBALS['prefix_lms']."_certificate as t1 JOIN ".$GLOBALS['prefix_lms']."_certificate_assign as t2 JOIN ".$GLOBALS['prefix_fw']."_user as t3 "
			." ON (t1.id_certificate = t2.id_certificate AND t2.id_user = t3.idst)  "
			.(count($conditions)>0 ? "WHERE ".implode(" AND ", $conditions) : "");

		list($total) = sql_fetch_row(sql_query($query)); echo mysql_error();
		return $total;
	}

}

function getCertificateQuery($users = false, $id_cert = false, $year = false) {
		$conditions = array();

		if ($users) {
			if (is_numeric($users)) $users = array($users);
			if (is_array($users)) $conditions[] = " t3.idst IN (".implode(',', $users).") ";
		}

		if ($id_cert) {
			if (is_numeric($id_cert)) $conditions[] = " t1.id_certificate = '".(int)$id_cert."' ";
		}

		if ($year) {
			if (is_numeric($year)) $conditions[] = " YEAR(t2.ondate) = '".(int)$year."' ";
		}

		$query = "SELECT t1.code, t1.name, YEAR(t2.on_date) as year, t3.firstname, t3.lastname "
			." FROM ".$GLOBALS['prefix_lms']."_certificate as t1 JOIN ".$GLOBALS['prefix_lms']."_certificate_assign as t2 JOIN ".$GLOBALS['prefix_fw']."_user as t3 "
			." ON (t1.id_certificate = t2.id_certificate AND t2.id_user = t3.idst)  "
			.(count($conditions)>0 ? "WHERE ".implode(" AND ", $conditions) : "")
			." ORDER BY t3.lastname, t3.firstname, t1.name";

		return $query;
	}


	function getCertificateQueryTotal($users = false, $id_cert = false, $year = false) {
		$conditions = array();

		if ($users) {
			if (is_numeric($users)) $users = array($users);
			if (is_array($users)) $conditions[] = " t3.idst IN (".implode(',', $users).") ";
		}

		if ($id_cert) {
			if (is_numeric($id_cert)) $conditions[] = " t1.id_certificate = '".(int)$id_cert."' ";
		}

		if ($year) {
			if (is_numeric($year)) $conditions[] = " YEAR(t2.ondate) = '".(int)$year."' ";
		}

		$query = "SELECT COUNT(*) "
			." FROM ".$GLOBALS['prefix_lms']."_certificate as t1 JOIN ".$GLOBALS['prefix_lms']."_certificate_assign as t2 JOIN ".$GLOBALS['prefix_fw']."_user as t3 "
			." ON (t1.id_certificate = t2.id_certificate AND t2.id_user = t3.idst)  "
			.(count($conditions)>0 ? "WHERE ".implode(" AND ", $conditions) : "");

		list($total) = sql_fetch_row(sql_query($query));
		return $total;
	}
?>