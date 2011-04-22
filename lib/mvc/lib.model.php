<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class Model {

	public $_record = array();
	protected $_table = '';
	protected $db = null;

	public function  __construct()
	{
		$this->db = DbConn::getInstance();
	}

	public function  __get($name) {

		if(!isset($this->_record[$name])) return NULL;
		return $this->$this->_record[$name];
	}

	public function   __set($name,  $value) {

		$this->_record[$name] = $value;
	}

	/**
	 * This method is usefull if you need to convert or verify the dir recived by a ajax request
	 * @param string $dir the sort direction
	 * @return string the cleaned direction
	 */
	public function clean_dir($dir) {
		switch($dir) {
			case 'desc' :
			case 'DESC' :
			case 'yui-dt-desc' : {
				$dir = 'desc';
			};break;
			case 'asc' :
			case 'ASC' :
			case 'yui-dt-asc' :
			default: {
				$dir = 'asc';
			};break;
		}
		return $dir;
	}

	/**
	 * This method will check if the sort recived from the ajax request is valid checking it's value with a whitelist of possibile value.
	 * If a dirty value is passed the default value will be returned or the first sortable_list if the default value is missing
	 * @param string $sort the sort column
	 * @param array $sortable_list the sort values whitelist
	 * @param string $default the default sort direction
	 * @return string the cleaned sort value
	 */
	public function clean_sort($sort, $sortable_list, $default = false) {

		if(in_array($sort, $sortable_list)) return $sort;
		if(!$default) return array_shift ($sortable_list);
		return $default;
	}
	
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Polo
	 * Created Date : 06/04/11
	 * Modified Date : 06/04/11
	 * Version : 1.0
	 * Function : dbCount, dbSearch
	 * Description : Recherche facile dans la DB
	 * 
	 **********************************/
	public function dbCount($search = array())
	{
		return $this->getList($search, true);
	}
	
	public function dbSearch($search=array(), $count = false)
	{
		$result = array();
		$champs = array();
		$searchOptions = array('order', 'offset', 'limit');
		
		if(!$count) $sql = "SELECT * FROM ".$this->_table.' WHERE 1';
		else $sql = "SELECT count(*) as nb FROM ".$this->_table.' WHERE 1';
		
		foreach($search as $key => $ch)
		{
			// On gère les options de recherche ici
			if(!in_array($key, $searchOptions))
			{
				if(!is_array($ch)) $sql .= ' AND '.$key.' = "'.$ch.'"';
				else
				{
					switch(strtoupper($ch['type']))
					{
						case 'IN': case 'NOT IN':
							if(is_array($ch['value'])) $sql .= ' AND '.$key.' '.$ch['type'].' ('.implode(',', $ch['value']).')';
							else $sql .= ' AND '.$key.' '.$ch['type'].' ('.$ch['value'].')';
							break;
						case 'LIKE': $sql .= ' AND '.$key.' LIKE "'.$ch['value'].'"'; break;
						default:
							if(is_numeric($ch['value'])) $sql->where($key.$ch['type'].((int) $ch['value']));
							else $sql .= ' AND '.$key.$ch['type'].' '.$ch['value'];
							break;
					}
				}
			}
		}
		
		if(isset($search['order'])) $sql .= ' ORDER BY '.$search['order'];
		if(isset($search['limit']))
		{
			if(isset($search['offset'])) $sql .= ' LIMIT '.$search['limit'].','.$search['offset'];
			else $sql .= ' LIMIT '.$search['limit'];
		}
		
		$res = $this->db->query($sql);
		
		if(!$count)
		{
			while($line = $this->db->fetch_obj($res))
			{
				$result[] = $line;
			}
		}
		else
		{
			$line = $this->db->fetch_obj($res);
			$result = (int) $line->nb;
		}
		
		return $result;
	}
}
