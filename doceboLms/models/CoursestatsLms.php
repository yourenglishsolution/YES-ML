<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class CoursestatsLms extends Model {

	protected $db;
	protected $tables;
	protected $cache;

	public function  __construct() {
		$this->db = DbConn::getInstance();
		$this->tables = array(
			'organization'	=> '%lms_organization',
			'commontrack'		=> '%lms_commontrack',
			'user'					=> '%adm_user',
			'courseuser'		=> '%lms_courseuser',
			'testtrack'			=> '%lms_testtrack',
			'lo_types'			=> '%lms_lo_types'
		);
		$this->cache = array();
	}


	public function getPerm()	{
		return array(
			'view' => 'standard/view.png',
			'mod'  => 'standard/edit.png'
		);
	}

	//...


	public function getTrackId($id_lo, $id_user) {
		$query = "SELECT idTrack FROM ".$this->tables['commontrack']." "
			." WHERE idReference=".(int)$id_lo." AND idUser=".(int)$id_user;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res)>0) {
			list($id_track) = $this->db->fetch_row($res);
			return $id_track;
		}
		return false;
	}


	public function getCourseLOs($id_course) {
		if (isset($this->cache['lo_list'][$id_course]) && is_array($this->cache['lo_list'][$id_course])) {
			return $this->cache['lo_list'][$id_course];
		}
		
		$output = array();
		$query = "SELECT * FROM ".$this->tables['organization']." "
			." WHERE idCourse=".(int)$id_course." ORDER BY title"; //or path?
		$res = $this->db->query($query);
		if ($res) {
			while ($obj = $this->db->fetch_obj($res)) {
				$record = new stdClass();
				$record->id = $obj->idOrg;
				$record->title = $obj->title;
				$record->id_resource = $obj->idResource;
				$record->type = $obj->objectType;
				$output[$obj->idOrg] = $record;
			}
		}
		$this->cache['lo_list'][$id_course] = $output;
		return $output;
	}


	public function getCourseStatsList($pagination, $id_course, $filter = false) {
		if (is_array($pagination)) {
			$startIndex = (isset($pagination['startIndex']) ? $pagination['startIndex'] : 0);
			$results = (isset($pagination['results']) ? $pagination['results'] : Get::sett('visuItem', 25));
		}

		$dir = 'ASC';
		if (is_array($pagination) && isset($pagination['dir'])) {
			switch (strtolower($pagination['dir'])) {
				case 'yui-dt-asc': $dir = 'ASC'; break;
				case 'yui-dt-desc': $dir = 'DESC'; break;
				case 'asc': $dir = 'ASC'; break;
				case 'desc': $dir = 'DESC'; break;
				default: $dir = 'ASC';
			}
		}

		$sort = 'u.userid';
		if (is_array($pagination) && isset($pagination['sort'])) {
			switch ($pagination['sort']) {
				case 'fullname': $sort = 'u.lastname '.$dir.', u.firstname'; break;
				//case 'level': $sort = '?!?'; break;
				case 'level': $sort = 'cu.level'; break;
				case 'status': $sort = 'cu.status'; break;
			}
		}

		$query = "SELECT u.idst, u.userid, u.firstname, u.lastname, cu.status, cu.level "
			." FROM ".$this->tables['courseuser']." as cu "
			." JOIN ".$this->tables['user']." as u "
			." ON (cu.idUser = u.idst AND cu.idCourse=".(int)$id_course.") ";
		if (is_array($filter)) {
			$condition = array();

			if (isset($filter['text']) && $filter['text'] != "") {
				$conditions[] = " (u.userid LIKE '%".$filter."%' OR u.firstname LIKE '%".$filter."%' OR u.lastname LIKE '%".$filter."%') ";
			}
			if (isset($filter['selection']) && $filter['selection']>0) {
				switch ($filter['selection']) {
					//begin only
					case 1: {
						$conditions[] = " AND cu.status = 0 ";
					} break;
					//itinere only
					case 2: {
						$conditions[] = " AND cu.status = 1 ";
					} break;
					//complete only
					case 3: {
						$conditions[] = " AND cu.status = 2 ";
					} break;
				}
			}

			$arr_idst = array();
			if (isset($filter['orgchart']) && $filter['orgchart']>0) {
				$umodel = new UsermanagementAdm();
				$use_desc = (isset($filter['descendants']) && $filter['descendants']);
				$ulist = $umodel->getFolderUsers($filter['orgchart'], $use_desc);
				if (!empty($ulist)) $arr_idst = $ulist;
				unset($ulist);
			}
			if (isset($filter['groups']) && $filter['groups']>0) {
				$gmodel = new GroupmanagementAdm();
				$ulist = $gmodel->getGroupAllUsers($filter['groups']);
				if (!empty($ulist))
					if (!empty($arr_idst)) {
						$arr_idst = array_merge($arr_idst, $ulist);
					} else {
						$arr_idst = $ulist;
					}
			}
			if (!empty($arr_idst)) $conditions[] = " AND u.idst IN (".implode(",", $arr_idst).") ";

			if (!empty($conditions)) $query .= " WHERE ".implode(" AND ", $conditions)." ";
		}

		$query .= " ORDER BY ".$sort." ".$dir." ";
		if (is_array($pagination)) {
			$query .= "LIMIT ".$startIndex.", ".$results;
		}

		$res = $this->db->query($query);

		if ($res) {
			while ($obj = $this->db->fetch_obj($res)) {
				$output[$obj->idst] = $obj;
				$output[$obj->idst]->lo_status = array(); //add property
			}

			//extract LOs status for the user (a subarray in the record)
			$lo_list = $this->getCourseLOs($id_course);
			if (!empty($lo_list) && !empty($output)) {
				$query = "SELECT * FROM ".$this->tables['commontrack']." as c "
					." WHERE idReference IN (".implode(",", array_keys($lo_list)).") "
					." AND idUser IN (".implode(",", array_keys($output)).")";
				$res = $this->db->query($query);
				if ($res) {
					while ($obj = $this->db->fetch_obj($res)) {
						$output[$obj->idUser]->lo_status[$obj->idReference] = $obj->status;
					}
				}
			}

		} else {
			return false;
		}

		return array_values($output);
	}

	public function getCourseStatsTotal($id_course, $filter) {
		$query = "SELECT COUNT(*) "
			." FROM ".$this->tables['courseuser']." as cu "
			." JOIN ".$this->tables['user']." as u "
			." ON (cu.idUser = u.idst AND cu.idCourse=".(int)$id_course.") ";
		if (is_array($filter)) {
			$condition = array();

			if (isset($filter['text']) && $filter['text'] != "") {
				$conditions[] = " (u.userid LIKE '%".$filter."%' OR u.firstname LIKE '%".$filter."%' OR u.lastname LIKE '%".$filter."%') ";
			}
			if (isset($filter['selection']) && $filter['selection']>0) {
				switch ($filter['selection']) {
					//begin only
					case 1: {
						$conditions[] = " AND cu.status = 0 ";
					} break;
					//itinere only
					case 2: {
						$conditions[] = " AND cu.status = 1 ";
					} break;
					//complete only
					case 3: {
						$conditions[] = " AND cu.status = 2 ";
					} break;
				}
			}

			$arr_idst = array();
			if (isset($filter['orgchart']) && $filter['orgchart']>0) {
				$umodel = new UsermanagementAdm();
				$use_desc = (isset($filter['descendants']) && $filter['descendants']);
				$ulist = $umodel->getFolderUsers($filter['orgchart'], $use_desc);
				if (!empty($ulist)) $arr_idst = $ulist;
				unset($ulist);
			}
			if (isset($filter['groups']) && $filter['groups']>0) {
				$gmodel = new GroupmanagementAdm();
				$ulist = $gmodel->getGroupAllUsers($filter['groups']);
				if (!empty($ulist))
					if (!empty($arr_idst)) {
						$arr_idst = array_merge($arr_idst, $ulist);
					} else {
						$arr_idst = $ulist;
					}
			}
			if (!empty($arr_idst)) $conditions[] = " AND u.idst IN (".implode(",", $arr_idst).") ";

			if (!empty($conditions)) $query .= " WHERE ".implode(" AND ", $conditions)." ";
		}

		$res = $this->db->query($query);
		if ($res) {
			list($count) = $this->db->fetch_row($res);
		} else {
			$count = false;
		}

		return $count;
	}


	public function getCourseUserStatsList($pagination, $id_course, $id_user) {
		if (is_array($pagination)) {
			$startIndex = (isset($pagination['startIndex']) ? $pagination['startIndex'] : 0);
			$results = (isset($pagination['results']) ? $pagination['results'] : Get::sett('visuItem', 25));

			$dir = 'ASC';
			if (isset($pagination['dir'])) {
				switch (strtolower($pagination['dir'])) {
					case 'yui-dt-asc': $dir = 'ASC'; break;
					case 'yui-dt-desc': $dir = 'DESC'; break;
					case 'asc': $dir = 'ASC'; break;
					case 'desc': $dir = 'DESC'; break;
					default: $dir = 'ASC';
				}
			}

			$sort = 'o.title';
			if (isset($pagination['sort'])) {
				switch ($pagination['sort']) {
					case 'LO_name': $sort = 'o.title'; break;
					case 'LO_type': $sort = 'o.objectType '.$dir.', o.title'; break;
					case 'LO_status': $sort = 'c.status '.$dir.', o.title'; break;
				}
			}
		}


		$query = "SELECT o.idOrg, o.title, o.objectType, o.idResource, c.status, "
			." c.dateAttempt as last_access, c.firstAttempt as first_access, c.first_complete, c.last_complete "
			." FROM ".$this->tables['organization']." as o "
			." LEFT JOIN ".$this->tables['commontrack']." as c "
			." ON (c.idReference = o.idOrg AND c.idUser=".(int)$id_user.") "
			." WHERE o.idCourse=".(int)$id_course." ";

		if (is_array($pagination)) {
			$query .= " ORDER BY ".$sort." ".$dir." ";
			$query .= "LIMIT ".$startIndex.", ".$results;
		}

		$output = array();
		$res = $this->db->query($query);
		if ($res) {
			$scores = $this->getLOScores($id_course, $id_user); //actually only tests can be scored
			while ($obj = $this->db->fetch_obj($res)) {
				$obj->score = isset($scores[$obj->idOrg]) ? $scores[$obj->idOrg] : "";
				$output[] = $obj;
			}
		} else {
			return false;
		}

		return $output;
	}

	public function getCourseUserStatsTotal($id_course, $id_user) {
		$query = "SELECT COUNT(*) "
			." FROM ".$this->tables['organization']." as o "
			." LEFT JOIN ".$this->tables['commontrack']." as c "
			." ON (c.idReference = o.idOrg AND c.idUser=".(int)$id_user.") "
			." WHERE o.idCourse=".(int)$id_course." ";
		$res = $this->db->query($query);
		if ($res) {
			list($total) = $this->db->fetch_row($res);
			return $total;
		}
		return false;
	}



	public function getLOInfo($id_lo) {
		if ((int)$id_lo <= 0) return false;
		$query = "SELECT * FROM ".$this->tables['organization']." WHERE idOrg=".(int)$id_lo;
		$res = $this->db->query($query);
		$output = $this->db->fetch_obj($res);
		return $output;
	}


	public function getLOScores($id_course, $id_user) {
		$output = array();

		$query = "SELECT * FROM ".$this->tables['testtrack']."";
		$res = $this->db->query($query);
		if ($res) {
			while ($obj = $this->db->fetch_obj($res)) {

			}
		}

		return $output;
	}


	public function getUserCourseInfo($id_course, $id_user) {
		if ($id_course <= 0 || $id_user <= 0) return false;
		$output = false;
		$query = "SELECT status, date_first_access, date_complete FROM ".$this->tables['courseuser']." "
			."WHERE idUser=".(int)$id_user." AND idCourse=".(int)$id_course;
		$res = $this->db->query($query);
		if ($res) {
			if ($this->db->num_rows($res) > 0) {
				$output = $this->db->fetch_obj($res);
			}
		}
		return $output;
	}


	public function getUserTrackInfo($id_user, $id_lo) {
		if ($id_lo <= 0 || $id_user <= 0) return false;
		$output = false;
		$query = "SELECT idTrack, objectType, status, firstAttempt as first_access, "
			." dateAttempt as last_access, first_complete, last_complete "
			." FROM ".$this->tables['commontrack']." "
			."WHERE idUser=".(int)$id_user." AND idReference=".(int)$id_lo;
		$res = $this->db->query($query);
		if ($res) {
			if ($this->db->num_rows($res) > 0) {
				$output = $this->db->fetch_obj($res);
			}
		}
		return $output;
	}




	/*
	 * retrieve a list of alla existent types of LO (and cache it for future retrievements)
	 */
	public function getLOTypes() {
		if (!isset($this->cache['lo_types'])) {
			$this->cache['lo_types'] = array();
			$query = "SELECT * FROM ".$this->tables['lo_types'];
			$res = $this->db->query($query);
			if ($res) {
				while ($obj = $this->db->fetch_obj($res)) {
					$this->cache['lo_types'][$obj->objectType] = $obj;
				}
			}
		}
		return $this->cache['lo_types'];
	}


	/*
	 * get an instance of LO object given ad id organization and/or an objectType
	 */
	public function getLOObject($id_lo, $type = false) {
		$output = false;
		$types = $this->getLOTypes();
		if (!$type) {
			$query = "SELECT objectType FROM ".$this->tables['organization']." WHERE idOrg=".(int)$id_lo;
			$res = $this->db->query($query);
			if (!$res || $this->db->num_rows($res) <= 0) return false;
			list($type) = $this->db->fetch_row($res);
		}
		if (is_array($types) && isset($types[$type])) {
			require_once(_lms_.'/class.module/'.$types[$type]->fileName);
			$classname = $types[$type]->className;
			$output = new $classname($id_lo);
		}
		return $output;
	}


	/*
	 * get an instance of LO track object given ad idTrack and/or an objectType
	 */
	public function getLOTrackObject($id_track, $type = false, $id_lo = false) {
		if ($id_track <= 0) return false;
		$output = false;
		$types = $this->getLOTypes();
		if (!$type) {
			$query = "SELECT objectType FROM ".$this->tables['commontrack']." WHERE idTrack=".(int)$id_track;
			$query.= ($id_lo > 0 ? " AND idReference = ".$id_lo : "");
			$res = $this->db->query($query);
			if (!$res || $this->db->num_rows($res) <= 0) return false;
			list($type) = $this->db->fetch_row($res);
		}
		if (is_array($types) && isset($types[$type])) {
			require_once(_lms_.'/class.module/'.$types[$type]->fileNameTrack);
			$classname = $types[$type]->classNameTrack;
			$output = new $classname($id_track);
		}
		return $output;
	}


	/*
	 * change the status of a user for a given LO
	 */
	public function changeLOUserStatus($id_lo, $id_user, $new_status) {
		if ($id_lo <= 0 || $id_user <= 0) return false;

		$output = false;
		$query = "SELECT * FROM ".$this->tables['organization']." WHERE idOrg=".(int)$id_lo;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res)>0) {
			$query = "SELECT * FROM ".$this->tables['commontrack']." WHERE idReference=".(int)$id_lo." AND idUser=".(int)$id_user;
			$res = $this->db->query($query);
			if ($res) {
				$obj = $this->db->fetch_obj($res);
				$track_obj = $this->getLOTrackObject($obj->idTrack, $obj->objectType);
				if (!$track_obj) return false;

				if ($this->db->num_rows($res)>0) {
					$obj = $this->db->fetch_obj($res);
					$old_status = $obj->status;
					if ($old_status == $new_status) return true;
					$output = $track_obj->updateTrackInfo(array('status' => $new_status));
				} else {
					//$track_obj->setUserStatus(); ?!?
				}
			}
		}

		return $output;
	}


	public function changeLOUserFirstAccess($id_lo, $id_user, $new_status) {
		if ($id_lo <= 0 || $id_user <= 0) return false;

		$output = false;
		$query = "UPDATE ".$this->tables['commontrack']." SET firstAttempt='".$new_status."' "
			." WHERE idReference=".(int)$id_lo." AND idUser=".(int)$id_user;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res)>0) {
			$output = true;
		}

		return $output;
	}

	public function changeLOUserLastAccess($id_lo, $id_user, $new_status) {
		if ($id_lo <= 0 || $id_user <= 0) return false;

		$output = false;
		$query = "UPDATE ".$this->tables['commontrack']." SET dateAttempt='".$new_status."' "
			." WHERE idReference=".(int)$id_lo." AND idUser=".(int)$id_user;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res)>0) {
			$output = true;
		}

		return $output;
	}

	public function changeLOUserFirstComplete($id_lo, $id_user, $new_status) {
		if ($id_lo <= 0 || $id_user <= 0) return false;

		$output = false;
		$query = "UPDATE ".$this->tables['commontrack']." SET first_complete='".$new_status."' "
			." WHERE idReference=".(int)$id_lo." AND idUser=".(int)$id_user;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res)>0) {
			$output = true;
		}

		return $output;
	}

	public function changeLOUserLastComplete($id_lo, $id_user, $new_status) {
		if ($id_lo <= 0 || $id_user <= 0) return false;

		$output = false;
		$query = "UPDATE ".$this->tables['commontrack']." SET last_complete='".$new_status."' "
			." WHERE idReference=".(int)$id_lo." AND idUser=".(int)$id_user;
		$res = $this->db->query($query);
		if ($res && $this->db->num_rows($res)>0) {
			$output = true;
		}

		return $output;
	}


	/*
	 * for every LO of a course with id $id_course, count how many users have completed it
	 */
	public function getLOsTotalCompleted($id_course) {
		$output = array();
		$lo_list = $this->getCourseLOs($id_course);
		if (!empty($lo_list)) {
			$query = "SELECT idReference, COUNT(*) FROM ".$this->tables['commontrack']." "
				." WHERE (status='completed' OR status='passed') AND idReference IN (".implode(",", array_keys($lo_list)).") "
				." GROUP BY idReference";
			$res = $this->db->query($query);
			if ($res) {
				$output = array();
				foreach ($lo_list as $id_lo => $lo_info) $output[$id_lo] = 0;
				while (list($id_lo, $count) = $this->db->fetch_row($res)) {
					$output[$id_lo] = (int)$count;
				}
			}
		}
		return $output;
	}


	/*
	 * delete all tracking info for a LO
	 */
	public function resetTrack($id_lo, $id_user) {
		$id_track = $this->getTrackId($id_lo, $id_user);
		$object_lo = $this->getLOTrackObject($id_track, false, $id_lo);
		if (!$object_lo) return true; //no track for this user and LO, the track is already "reset"
		$res = $object_lo->deleteTrackInfo($id_lo, $id_user);
		return $res;
	}

}

?>
