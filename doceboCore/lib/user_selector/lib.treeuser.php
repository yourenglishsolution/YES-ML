<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

define('ROOT_NODE_ID', 0);

class TreeUserManager {

	//private $acl = new DoceboACLManager();
	private $orgchartTable;
	private $orgchartLangTable;

	private $db;

	public function __construct() {
		$this->db = DbConn::getInstance();
		$this->orgchartTable = $GLOBALS['prefix_fw'].'_orgchart_tree';
		$this->orgchartLangTable = $GLOBALS['prefix_fw'].'_orgchart';
	}

	public function getNodesById($node_id) {
		$search_query = "
		SELECT
			t1.idOrg, t1.path, t2.translation, 1
		FROM
			core_org_chart_tree AS t1 LEFT JOIN	core_org_chart AS t2
			ON (t1.idOrg = t2.id_dir AND t2.lang_code = '".getLanguage()."' )
		WHERE
			t1.idParent = '".$node_id."'
		ORDER BY
			t2.translation";
		$re = $this->db->query($search_query);

		$output = array();
		while(list($id, $path, $translation, $sons) = $this->db->fetch_row($re)) {
			$label = $translation;//end(explode('/', $path));
			$output[$id] = array('id'=>$id,'label'=> $label,'is_leaf'=>($sons <= 0), 'count_content' => '');
		}

		$search_query = "
		SELECT t1.idOrg, COUNT(t2.idParent)
		FROM core_org_chart_tree AS t1 LEFT JOIN core_org_chart_tree AS t2 ON (t1.idOrg = t2.idParent)
		WHERE t1.idParent = '".$node_id."'
		GROUP BY t1.idOrg";
		$re = $this->db->query($search_query);
		while(list($id, $sons) = $this->db->fetch_row($re)) {
			$output[$id]['is_leaf'] = ($sons == 0);
		}

		return array_values($output);
	}



	public function deleteFolder($node_id) {

		$query = "DELETE FROM ". $this->orgchartTable
		." WHERE idOrg = '".(int)$node_id ."'";

		return $re = $this->db->query( $query );
	}


	protected function _getFolderById($node_id) {

	}

	public function addFolder($parent_id, $langs) {
		$parent = $this->_getFolderById( $parent_id );
		$path = mysql_escape_string($parent->path). "/" .$folderName;
		$level = $parent->level + 1;
		$query = "INSERT into ". $this->orgchartTable
		."('idParent', 'path', 'level') VALUES ("
		. (int)$parent_id ."','". $path. "','". (int)$level ."')";
		$re = $this->db->query( $query );
		//first check if langs is array and with length>0
		if ($re) {
			//now insert folder name (in different languages) in db
			$lang_codes = getAllLanguages();
			foreach ($lang_codes as $lang) {

			}
			if (count($temp)>0) {
				$query = "INSERT into ". $this->orgchartLangTable ." ()";
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}
?>