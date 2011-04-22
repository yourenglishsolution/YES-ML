<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package course management
 * @subpackage course catalogue
 * @category ajax server
 * @version $Id:$
 *
 */

if(!defined("IN_DOCEBO") && !defined("IN_AJAX")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

$op = Get::gReq('op', DOTY_ALPHANUM, '');
switch($op) {
	case "getselected" : {

		require_once(_lms_.'/lib/lib.quest_bank.php');
		$qbm = new QuestBankMan();

		$quest_category 	= Get::pReq('quest_category', DOTY_INT);
		$quest_difficult 	= Get::pReq('quest_difficult', DOTY_INT);
		$quest_type 		= Get::pReq('quest_type', DOTY_ALPHANUM);

		$re_quest = $qbm->resQuestList($quest_category, $quest_difficult, $quest_type);

		$value = array();
		while(list($id_q) = $qbm->fetch($re_quest)) {

			$value[] = (int)$id_q;
		}

		$json = new Services_JSON();
		$output = $json->encode($value);
  		aout($output);
	};break;
	case "delquest" : {
		//require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$id_quest = Get::pReq('id_quest', DOTY_INT);
		$row_quest = Get::pReq('row_quest', DOTY_ALPHANUM);

		require_once(_lms_.'/lib/lib.quest_bank.php');
		$qman = new QuestBankMan();
		$result = $qman->delQuest($id_quest);

		$value = array("result"=>$result, "id_quest"=>$id_quest, "row_quest"=>$row_quest, "error"=>$qman->last_error);

		$json = new Services_JSON();
		$output = $json->encode($value);
		aout($output);
	};break;
	default : {

		require_once(_lms_.'/lib/lib.quest_bank.php');
		$qbm = new QuestBankMan();

		$quest_category 	= Get::gReq('quest_category', DOTY_INT);
		$quest_difficult 	= Get::gReq('quest_difficult', DOTY_INT);
		$quest_type 		= Get::gReq('quest_type', DOTY_ALPHANUM);
		$startIndex 		= Get::gReq('startIndex', DOTY_INT, 0);
		$results 			= Get::gReq('results', DOTY_INT, 30);

		$totalRecords = $qbm->totalQuestList($quest_category, $quest_difficult, $quest_type);
		$re_quest = $qbm->resQuestList($quest_category, $quest_difficult, $quest_type, $startIndex, $results);

		$value = array(
			"totalRecords" => (int)$totalRecords,
			"recordsReturned" => (int)$qbm->num_rows($re_quest),
			"startIndex" => (int)$startIndex,
			"records" => array(),
			"qc" => $quest_category,
			"qd" => $quest_difficult,
			"qt" => $quest_type,
			"si" => $startIndex,
			"re" => $results
		);

		while(list($id_q, $id_c, $type, $title, $difficult) = $qbm->fetch($re_quest)) {

			$value['records'][] = array(
				"id_quest" => $id_q,
				"category_quest" => $id_c,
				"type_quest" => $type,
				"title_quest" => $title,
				"difficult" => $difficult
			);
		}

		//require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($value);
		aout($output);
	};break;
}

?>