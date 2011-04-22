<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

if(Docebo::user()->isAnonymous())  die("You can't access");

function retriveTrack($id_reference, $id_test, $id_user, $do_not_create = false) {
	
	$id_track = false;
	if(isset($_POST['idTrack']) || isset($_GET['idTrack'])) {
		return importVar('idTrack', true, 0);
	}
	if($id_reference !== FALSE) {
		
		if(Track_Test::isTrack(getLogUserId(), $id_test, $id_reference)) {
			
			// Load existing info track
			$track_info 	= Track_Test::getTrackInfo($id_user, $id_test, $id_reference);
			$id_track 		= $track_info['idTrack'];
		} elseif($do_not_create == false) {
			
			$id_track = Track_Test::createNewTrack($id_user, $id_test, $id_reference);
			if($id_track) {
				Track_Test::createTrack(	$id_reference, 
											$id_track, 
											$id_user, 
											date('Y-m-d H:i:s'), 
											'attempted', 
											'test' );
			} else $id_track = false;
		} 
	} else {
		
		// try to retrive by user, test
		$id_track = Track_Test::getTrack($id_test, $id_user);
		if(!$id_track) {
			// create a new one
			$id_track = Track_Test::createNewTrack($id_user, $id_test, 0);
		}
	}
	return $id_track;
}

function intro( $object_test, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once(_base_.'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php' );
	
	$lang 			=& DoceboLanguage::createInstance('test');
	$id_test 		= $object_test->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_test->back_url));
	$id_track 		= retriveTrack($id_reference, $id_test, getLogUserId());
	
	if($id_track === false) {
		
		$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_TRACK_FAILURE')
			.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))), 'content');
	}
	
	$track_info = Track_Test::getTrackInfoById($id_track);
	
	$test_man 	= new TestManagement($id_test);
	$play_man 	= new PlayTestManagement($id_test, getLogUserId(), $id_track, $test_man);
	$test_info 	= $test_man->getTestAllInfo();
	
	$prerequisite = $test_man->getPrerequisite();
	
	$group_test_man = new GroupTestManagement();
	$tests_score =& $group_test_man->getTestsScores(array($id_test), array(getLogUserId()));
	
	if($test_info['time_dependent'] && $test_info['time_assigned']) {
		
		$minute_assigned 	= (int)($test_info['time_assigned'] / 60);
		$second_assigned 	= (int)($test_info['time_assigned'] % 60);
		if(strlen($second_assigned) == 1) $second_assigned = '0'.$second_assigned;
		$time_readable 	= str_replace('[time_assigned]', $minute_assigned.':'.$second_assigned.'', 
						$lang->def('_TEST_TIME_ASSIGNED'));
		$time_readable 	= str_replace('[second_assigned]', ''.$second_assigned, 
						str_replace('[minute_assigned]', ''.$minute_assigned, $time_readable));
	}
	
	$page_title = array(
		Util::str_replace_once('&', '&amp;', $object_test->back_url) => $lang->def('_TITLE'), 
		$test_info['title']
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'test', $lang->def('_TEST_INFO'))
		.'<div class="std_block">'
		.getBackUi( Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))
		
		.'<span class="text_bold">'.$lang->def('_TITLE').' : </span>'.$test_info['title'].'<br /><br />'
		.( $test_info['description'] != '' 
			? '<span class="text_bold">'.$lang->def('_DESCRIPTION').' : </span>'.$test_info['description'].'<br /><br />' 
			: '' )
		.(isset($track_info['score']) && $track_info['score'] >= $test_info['point_required'] ? '<span class="text_bold">'.str_replace('[score]', $track_info['score'], $lang->def('_RESTART_INFO')).'</span><br /><br />' : '')
			, 'content');

	
	if($test_info['hide_info'] == 0)
	{
		$GLOBALS['page']->add('<span class="text_bold">'.$lang->def('_TEST_INFO').' : </span><br />'
			.'<ul class="test_info_list">', 'content');
		
		if($test_info['order_type'] != 2) {
			
			$GLOBALS['page']->add('<li>'.str_replace('[max_score]', ''.($test_info['point_type'] != 1 ? $test_man->getMaxScore() : 100 ) , $lang->def('_TEST_MAXSCORE')).'</li>', 'content');
		}
		
		$GLOBALS['page']->add('<li>'.str_replace('[question_number]', ''.$test_man->getNumberOfQuestion() , $lang->def('_TEST_QUESTION_NUMBER')).'</li>', 'content');
		
		if($test_info['point_required'] != 0) {
			
			$GLOBALS['page']->add('<li>'.str_replace('[score_req]', ''.$test_info['point_required'], $lang->def('_TEST_REQUIREDSCORE')).'</li>', 'content');
		}
		$GLOBALS['page']->add(
			'<li>'.( $test_info['save_keep'] ? $lang->def('_TEST_SAVEKEEP') 
						: $lang->def('_TEST_SAVEKEEP_NO') ).'</li>'
			.'<li>'.( $test_info['mod_doanswer'] ? $lang->def('_TEST_MOD_DOANSWER') 
						: $lang->def('_TEST_MOD_DOANSWER_NO') ).'</li>'
			.'<li>'.( $test_info['can_travel'] ? $lang->def('_TEST_CAN_TRAVEL') 
						: $lang->def('_TEST_CAN_TRAVEL_NO') ).'</li>'
			.'<li>'.( ($test_info['show_score'] || $test_info['show_score_cat']) ? $lang->def('_TEST_SHOW_SCORE') 
						: $lang->def('_TEST_SHOW_SCORE_NO') ).'</li>'
			.'<li>'.( $test_info['show_solution'] ? $lang->def('_TEST_SHOW_SOLUTION') 
						: $lang->def('_TEST_SHOW_SOLUTION_NO') ).'</li>'
			.'<li>'
		, 'content');
		switch($test_info['time_dependent']) {
			
			case 0 : $GLOBALS['page']->add($lang->def('_TEST_TIME_ASSIGNED_NO'), 'content');;break;
			case 1 : $GLOBALS['page']->add($time_readable, 'content');;break;
			case 2 : $GLOBALS['page']->add($lang->def('_TEST_TIME_ASSIGNED_QUEST'), 'content');;break;
		}
		
		if($test_info['max_attempt'] > 0) {
			$GLOBALS['page']->add(
				'<li>'
					.str_replace('[remaining_attempt]', ($test_info['max_attempt'] - $track_info['number_of_attempt']), $lang->def('_NUMBER_OF_ATTEMPT'))
				.'</li>'
			, 'content');
		}
		$GLOBALS['page']->add('</ul>'
			.'<br />', 'content');
	}
	
	if ($tests_score[$id_test][getLogUserId()]['comment'] !== '')
        $GLOBALS['page']->add('<span class="text_bold">'.$lang->def('_COMMENTS').' : </span>'.$tests_score[$id_test][getLogUserId()]['comment'].'<br /><br />', 'content');
	
	
	// Actions
	$score_status = $play_man->getScoreStatus();
	$show_result = $test_info['show_score'] || $test_info['show_score_cat'] || $test_info['show_solution'];
	$is_end = $score_status == 'valid' || $score_status == 'not_checked' || 
					$score_status == 'passed' || $score_status == 'not_passed';
	
	
	$GLOBALS['page']->add(
		Form::openForm('test_intro', 'index.php?modname=test&amp;op=play')
		.Form::getHidden('id_test', 'id_test', $id_test)
		.Form::getHidden('id_param', 'id_param', $id_param)
		.Form::getHidden('idTrack', 'idTrack', $id_track)
		.Form::getHidden('back_url', 'back_url', $url_coded)
		.Form::getHidden('next_step', 'next_step', 'play')
	, 'content');
	
	if($test_info['max_attempt'] > 0) {
		
		if($test_info['max_attempt'] - $track_info['number_of_attempt'] <= 0) {
			
			$GLOBALS['page']->add($lang->def('_MAX_ATTEMPT_REACH'), 'content');
			if($show_result) {
				
				$GLOBALS['page']->add(
					'<div class="align_right">'
					.Form::getHidden('show_result', 'show_result', 1)
					.Form::getButton('show_review', 'show_review', $lang->def('_TEST_SHOW_REVIEW'))
					.'</div>'
				, 'content');
			}
			$GLOBALS['page']->add(
				Form::closeForm()
				.'</div>', 'content');
			return;
		}
		
		if($is_end && ($track_info['score'] >= $test_info['point_required'])) {
			
			$GLOBALS['page']->add($lang->def('_YOU_HAVE_PASS_THIS_TEST'), 'content');
			if($show_result) {
				
				$GLOBALS['page']->add(
					'<div class="align_right">'
					.Form::getHidden('show_result', 'show_result', 1)
					.Form::getButton('show_review', 'show_review', $lang->def('_TEST_SHOW_REVIEW'))
					.'</div>'
				, 'content');
			}
			$GLOBALS['page']->add(
				Form::closeForm()
				.'</div>', 'content');
			return;
		}
	}
	

	//--- check for suspension condition -------------------------------------------
//die('<pre>'.print_r($track_info, true).'</pre>');
	if ($test_info['use_suspension']) {
		$attempts_until_now = $track_info['attempts_for_suspension'];
		$last_suspension_date = $track_info['suspended_until'];
		if ($last_suspension_date == "") $last_suspension_date = '0000-00-00 00:00:00';

		//check if last suspension date is prior to now
		$now = date("Y-m-d H:i:s");
		if ($last_suspension_date > $now) { //user is suspended for this test until "last_suspension_date"
			cout('<span class="text_bold">'.$lang->def('_TEST_SUSPENDED').' : </span>'.$GLOBALS['regset']->databaseToRegional($last_suspension_date, "datetime").'<br /><br />', 'content');
			cout(Form::closeForm().'</div>', 'content');
			return;
		}

		//check if the user should re-play LO in prerequisites
		if ($test_info['suspension_prerequisites']) {

			if ($prerequisite != "") {
				//check all prerequisites conditions
				$query = "SELECT idOrg FROM ".$GLOBALS['prefix_lms']."_organization WHERE objectType='test' AND idResource=".(int)$test_info['idTest'];
				list($idOrg) = sql_fetch_row( sql_query( $query ) );

				$conditions = explode(",", $prerequisite);
				$req_arr = array();
				foreach ($conditions as $condition) {
					if (is_numeric($condition) && (int)$condition != $idOrg) $req_arr[] = $condition;
				}

				if (count($req_arr)>0) {
					$query = "SELECT * FROM ".$GLOBALS['prefix_lms']."_commontrack WHERE idReference IN (".implode(",", $req_arr).") "
						." AND dateAttempt>'".$last_suspension_date."' AND status IN ('completed','passed')";
					$res = sql_query($query);
					if (sql_num_rows($res) < count($req_arr)) {
						cout('<span class="text_bold">'.$lang->def('_UNREACHED_PREREQUISITES').'</span><br /><br />', 'content');
						cout(Form::closeForm().'</div>', 'content');
						return;
					}
				}

			}

		}

		//check remaining attempts
		$diff_attempts = $test_info['suspension_num_attempts'] - $attempts_until_now;
		if ($diff_attempts > 0) {
			//warning: $diff_attempts remaining before suspesion
			cout('<span class="text_bold">'.$lang->def('_ATTEMPTS_REMAINING_BEFORE_SUSPENSION').' : </span>'.$diff_attempts.'<br /><br />', 'content');
		} else {
			//(this branch should never have been reached by alghoritm)
			return;
		}

	}

//--- end suspension check -----------------------------------------------------


	if ($score_status == 'passed') $incomplete = FALSE;
	elseif ($score_status == 'valid') {
		$track_info = $play_man->getTrackAllInfo();
		
		if ($track_info['score'] >= $test_info['point_required'])
			$incomplete = FALSE;
		else
			$incomplete = TRUE;
	} else {
		$incomplete = TRUE;
	}
	if($score_status == 'not_complete') {
		$GLOBALS['page']->add(Form::getHidden('page_continue', 'page_continue', $play_man->getLastPageSeen()), 'content');
	}
	if($is_end) {
		$GLOBALS['page']->add(Form::getHidden('show_result', 'show_result', 1), 'content');
	}
	if($test_info['save_keep'] && $score_status == 'not_complete') {
		$GLOBALS['page']->add('<span class="text_bold">'.$lang->def('_TEST_SAVED').'</span><br /><br />', 'content');
	}
	$GLOBALS['page']->add('<div class="align_right">', 'content');
	if($is_end && $show_result) {
		$GLOBALS['page']->add(Form::getButton('show_review', 'show_review', $lang->def('_TEST_SHOW_REVIEW')), 'content');
	}
	elseif($test_info['save_keep'] && $score_status == 'not_complete') {
		$GLOBALS['page']->add(Form::getButton('continue', 'continue', $lang->def('_TEST_CONTINUE')), 'content');
	}
	if($score_status == 'not_complete') {
			$GLOBALS['page']->add(Form::getButton('restart', 'restart', $lang->def('_TEST_BEGIN')), 'content');
	}
	elseif ($is_end)
	{
		if($_SESSION['levelCourse'] > '3') {
			$GLOBALS['page']->add(Form::getButton('restart', 'restart', $lang->def('_TEST_RESTART')), 'content');
		} elseif (str_replace('incomplete', '', $prerequisite) !== $prerequisite)
			($incomplete 
				? $GLOBALS['page']->add(Form::getButton('restart', 'restart', $lang->def('_TEST_RESTART')), 'content') 
				: $GLOBALS['page']->add($lang->def('_TEST_COMPLETED'), 'content'));
		elseif (str_replace('NULL', '', $prerequisite) !== $prerequisite)
			($score_status !== 'valid' && $score_status !== 'passed' 
				? $GLOBALS['page']->add(Form::getButton('restart', 'restart', $lang->def('_TEST_RESTART')), 'content') 
				: $GLOBALS['page']->add($lang->def('_TEST_COMPLETED'), 'content'));
		else
			$GLOBALS['page']->add(Form::getButton('restart', 'restart', $lang->def('_TEST_RESTART')), 'content');
	} else {
		
		resetTrack($id_test, $id_track);
		$GLOBALS['page']->add(Form::getButton('begin', 'begin', $lang->def('_TEST_BEGIN')), 'content');
	}
	$GLOBALS['page']->add(
		'</div>'
		.Form::closeForm()
		.'</div>', 'content');
}

function resetTrack($id_test, $id_track) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once(_base_.'/lib/lib.upload.php');
	
	$query_question = "
	SELECT q.idQuest, q.type_quest, t.type_file, t.type_class 
	FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
	WHERE q.idTest = '".$id_test."' AND q.type_quest = t.type_quest 
	ORDER BY q.sequence";
	$re_quest = sql_query($query_question);
	while(list($idQuest, $type_quest, $type_file, $type_class) = sql_fetch_row($re_quest)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
		$quest_obj = eval("return new $type_class( $idQuest );");
		
		$quest_obj->deleteAnswer($id_track);
	}
	
	$query_page = "
	DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_page 
	WHERE idTrack = '".$id_track."'";
	$query_quest = "
	DELETE FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
	WHERE idTrack = '".$id_track."'";
	
	sql_query($query_page);
	sql_query($query_quest);
	
	$now = date("Y-m-d H:i:s");
	$new_info = array(
		'date_attempt' => $now,
		'date_end_attempt' => $now,
		'date_attempt_mod' => NULL,
		'last_page_seen' => 0,
		'last_page_saved' => 0,
		'score' => 0,
		'bonus_score' => 0,
		'score_status' => 'not_complete' );
	$re_update = Track_Test::updateTrack($id_track, $new_info);
	
	return $re_update;
}

function playTestDispatch( $object_test, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once(_base_.'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php' );

	$lang 			=& DoceboLanguage::createInstance('test');
	$id_test 		= $object_test->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_test->back_url));
	$id_track 		= retriveTrack($id_reference, $id_test, getLogUserId());
	
	if(isset($_POST['restart'])) {
		
		//delete existing track and begin the test
		$test_man 	= new TestManagement($id_test);
		$play_man 	= new PlayTestManagement($id_test, getLogUserId(), $id_track, $test_man);
		$score_status = $play_man->getScoreStatus();

		
		$max_attempt 	= $test_man->getTestInfo('max_attempt');

		if($max_attempt > 0) {
			
			$track_info = Track_Test::getTrackInfoById($id_track);
			if($max_attempt - $track_info['number_of_attempt'] <= 0) {				
				$GLOBALS['page']->add($lang->def('_MAX_ATTEMPT_REACH'), 'content');

				$GLOBALS['page']->add(
					Form::closeForm()
					.'</div>', 'content');
				return;
			}
		}

		$is_end = $score_status == 'valid' || $score_status == 'not_checked' || 
					$score_status == 'passed' || $score_status == 'not_passed';
		
		if($score_status == 'not_complete' || $is_end) {
			
			resetTrack($object_test->getId(), importVar('idTrack', true, 0));
		}
		play($object_test, $id_param);
	} elseif(isset($_POST['test_save_keep'])) {
		
		// continue a test completed, show the result
		saveAndExit($object_test, $id_param);
	} elseif(isset($_POST['show_result'])) {
		
		// continue a test completed, show the result
		showResult($object_test, $id_param);
	} elseif(isset($_POST['time_elapsed']) && $_POST['time_elapsed'] == '1') {
		
		// continue a test completed, show the result
		showResult($object_test, $id_param);
	} else {
		
		// play test
		play($object_test, $id_param);
	}
}

function play($object_test, $id_param) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once(_base_.'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php' );

	if(!isset($_SESSION['test_date_begin']))
		$_SESSION['test_date_begin'] = date('Y-m-d H:i:s');

	$lang 			=& DoceboLanguage::createInstance('test');
	$id_test 		= $object_test->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_test->back_url));
	$id_track 		= retriveTrack($id_reference, $id_test, getLogUserId());
	
	if($id_track === false) {
		
		$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_TRACK_FAILURE')
			.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))), 'content');
	}
	$test_man 	= new TestManagement($id_test);
	$play_man 	= new PlayTestManagement($id_test, getLogUserId(), $id_track, $test_man);
	$test_info 		= $test_man->getTestAllInfo();
	$track_info 	= $play_man->getTrackAllInfo();
	
	// cast display to one quest at time if the time is by quest
	if($test_info['time_dependent'] == 2) $test_info['display_type'] = 1;
	
	//number of test pages-------------------------------------------
	$tot_page = $test_man->getTotalPageNumber();
	
	// find the page to display 
	$previous_page = importVar('previous_page', false, false);
	if($previous_page === false) {
		
		if(isset($_POST['page_continue']) && isset($_POST['continue'])) $page_to_display = $_POST['page_continue'];
		else $page_to_display = 1;
	} else {
		$page_to_display = $previous_page;
		if(isset($_POST['next_page'])) ++$page_to_display;
		if(isset($_POST['prev_page']) && $test_info['can_travel']) --$page_to_display;
	}
	if(($page_to_display < $track_info['last_page_seen']) && !$test_info['can_travel']) {
		
		//the page request is alredy displayed, but the user cannot travel trought page
		$GLOBALS['page']->add(getErrorUi($lang->def('_ERR_INCOERENCY_WITH_PAGE_NUMBER'))
			.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK')), 'content');
		return;
	} //print_r($track_info['score_status']); die();
	if($track_info['score_status'] != 'not_complete' && $track_info['score_status'] != 'doing') {

		$GLOBALS['page']->add(getErrorUi($lang->def('_ERR_INCOERENCY_WITH_PAGE_NUMBER'))
			.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK')), 'content');
		return;
	}
	$new_info = array(
		'last_page_seen' => $page_to_display,
		'score_status' => 'doing' );
	if(isset($_POST['page_to_save'])) {
		
		if($test_info['mod_doanswer']) {
			
			$new_info['last_page_saved'] = $_POST['page_to_save'];
			$play_man->storePage($_POST['page_to_save'], $test_info['mod_doanswer']);
			$play_man->closeTrackPageSession($_POST['page_to_save']);
		} else {
			
			if($_POST['page_to_save'] > $track_info['last_page_saved']) {
				
				$new_info['last_page_saved'] = $_POST['page_to_save'];
				$play_man->storePage($_POST['page_to_save'], $test_info['mod_doanswer']);
				$play_man->closeTrackPageSession($_POST['page_to_save']);
			}
		}
	}
	$re_update = Track_Test::updateTrack($id_track, $new_info);
	
	// save page track info
	$play_man->updateTrackForPage($page_to_display);
	
	$quest_sequence_number = $test_man->getInitQuestSequenceNumberForPage($page_to_display);
	$query_question			= $play_man->getQuestionsForPage($page_to_display);
	$time_in_test 			= $play_man->userTimeInTheTest();
	
	$lock_edit = false;
	$time_string = '';
	if($test_info['time_dependent'] == 1 || $test_info['time_dependent'] == 2) {
		
		if($test_info['time_dependent'] == 1) {
			
			// time is for test
			$start_time = $test_info['time_assigned'] - $time_in_test;
			if($start_time <= 0) {
				
				showResult($object_test, $id_param);
				return;
			}
		} elseif($test_info['time_dependent'] == 2) {
			
			// time is for quest
			$re_question = sql_query($query_question);
			list($idQuest, $type_quest, $type_file, $type_class, $start_time) = sql_fetch_row($re_question);
			
			$time_in_quest = $play_man->userTimeInThePage($page_to_display);
			$start_time = $start_time - $time_in_quest;
			if($start_time <= 0) {
				
				$lock_edit = true;
			}
		}
		$time_string .= '<div class="test_time_left">'.$lang->def('_TIME_LEFT').' : '
			.'<span id="time_left">'.(int)($start_time/60).' m '.($start_time%60).' s</span>'
			.'</div>';
		
		// Js for time counter
		$time_string .= 
			"<script type=\"text/javascript\">
			<!--
			
			var start_count_from = ".$start_time.";
			var step = 1;
			var time_elapsed = 0;
			
			var id_interval;
			var id_timeout;
			
			if( window.document.getElementById == null ) {
				window.document.getElementById = function( id ) {
					return document.all[id];
			  }
			}
			
			function counter() {
				
				time_elapsed += step;
				
				var display = start_count_from - time_elapsed;
				var elem = document.getElementById('time_left');
				
				if(display <  0) return;
				
				var value = display/60;
				var minute = Math.floor(value).toString(10);
				if( minute.length <= 1 ) minute = '0' + minute;
				value = display%60;
				var second = Math.floor(value).toString(10);
				if( second.length <= 1 ) second = '0' + second;
				elem.innerHTML = minute + 'm ' + second  + ' s';
			}
			
			function whenTimeElapsed() {
				
				 window.clearInterval(id_interval);
				 window.clearTimeout(id_timeout);
				 
				var submit_to_end = document.getElementById('test_play');
				var time_elapsed = document.getElementById('time_elapsed');
				time_elapsed.value = 1;
				alert('".$lang->def('_TIME_ELAPSED')."');
				submit_to_end.submit();
			}
			
			function activateCounter() {
				
				counter();
				id_interval 	= window.setInterval(\"counter()\", step * 1000);
				id_timeout 		= window.setTimeout(\"whenTimeElapsed()\", (start_count_from - 1) * 1000);
			}
			
			activateCounter();
			// -->
			</script>";
		$time_string .= Form::getHidden('time_elapsed', 'time_elapsed', '0')
			.'<br />';
	}
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE').' : '.$test_info['title'], 'test', $lang->def('_TEST_INFO'))
		.'<div class="std_block">'
		
		.Form::openForm('test_play', 'index.php?modname=test&amp;op=play', 'std_form', 'post', 'multipart/form-data')
		// Standard info
		.Form::getHidden('next_step', 'next_step', 'play')
		.Form::getHidden('id_test', 'id_test', $id_test)
		.Form::getHidden('id_param', 'id_param', $id_param)
		.Form::getHidden('back_url', 'back_url', $url_coded)
		.Form::getHidden('idTrack', 'idTrack', $id_track)
		.$time_string, 'content');
	
	
	if($tot_page > 1) {
		$GLOBALS['page']->add(
			'<div class="align_center">'.$lang->def('_TEST_PAGES').' : '.$page_to_display.' / '.$tot_page.'</div><br />'
		, 'content');
	}
	
	// Page info
	$GLOBALS['page']->add(
		Form::getHidden('page_to_save', 'page_to_save', $page_to_display)
		.Form::getHidden('previous_page', 'previous_page', $page_to_display), 'content');
	
	// Get question from database
	$re_question = sql_query($query_question);
	
	// Page display
	$GLOBALS['page']->add('<div class="test_answer_space">', 'content');

	$array_answer = array();
	$tot_question = 0;

	while(list($idQuest, $type_quest, $type_file, $type_class, $time_assigned) = sql_fetch_row($re_question)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
		$quest_obj = eval("return new $type_class( $idQuest );");
		
		$GLOBALS['page']->add($quest_obj->play( 	$quest_sequence_number, 
								$test_info['shuffle_answer'], 
								$id_track,
								!$test_info['mod_doanswer'] && !$lock_edit ), 'content');

		switch($type_quest)
		{
			case 'choice_multiple':
			case 'choice':
			case 'associate':
			case 'text_entry':
				$query =	"SELECT idAnswer, is_correct"
							." FROM ".$GLOBALS['prefix_lms']."_testquestanswer"
							." WHERE idQuest = ".(int)$idQuest;
				$result = mysql_query($query);
				while(list($id_answer, $is_correct) = mysql_fetch_row($result))
					$array_answer[$idQuest][$id_answer] = $is_correct;
				$array_answer[$idQuest]['type'] = $type_quest;
				$tot_question++;
			break;
			default:
			break;
		}

		// Save question visualization sequence 
		sql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_testtrack_quest
		(idTrack, idQuest, page) VALUES 
		('".(int)$id_track."', '".(int)$idQuest."', '".$page_to_display."')");
		
		if(($type_quest != 'break_page') && ($type_quest != 'title')) {
			++$quest_sequence_number;
		}
	}
	if($test_info['mandatory_answer'] == 1)
	{
		YuiLib::load();
		Util::get_js(Get::rel_path('lms').'/modules/question/question.js', true,true);
		cout('<script type="text/javascript">', 'content');

		$tot_correct_array = array();

		foreach($array_answer as $id_quest => $quest_info)
		{
			switch($quest_info['type'])
			{
				case 'choice_multiple':
					$tot_correct = 0;
					foreach($quest_info as $id_answer => $is_correct)
						if($id_answer !== 'type')
							$tot_correct += $is_correct;
					if($tot_correct == 0)
						$tot_question--;
					else
					{
						cout('YAHOO.util.Event.onDOMReady(configureMultiC, \'\', \''.(int)$id_quest.'\');'."\n", 'content');
						$tot_correct_array[$id_quest] = $tot_correct;
					}
				break;
				case 'choice':
					cout('YAHOO.util.Event.onDOMReady(configureSingleC, \'\', \''.(int)$id_quest.'\');'."\n", 'content');
				break;
				case 'text_entry':
					cout('YAHOO.util.Event.onDOMReady(configureTextE, \'\', \''.(int)$id_quest.'\');'."\n", 'content');
				break;
				case 'associate':
					cout('YAHOO.util.Event.onDOMReady(configureAss, \'\', \''.(int)$id_quest.'\');'."\n", 'content');
				break;
			}
		}

		$js_array = '{';
		$first = true;

		foreach($tot_correct_array as $id_quest => $num_correct)
			if($first)
			{
				$js_array .= '\'_'.$id_quest.'\':'.$num_correct;
				$first = false;
			}
			else
				$js_array .= ',\'_'.$id_quest.'\':'.$num_correct;

		$js_array .= '}';

		cout(	'var num_answer_control = '.$js_array.';'."\n"
				.'var tot_question = '.(int)$tot_question.';'."\n"
				.'</script>', 'content');
	}

	$GLOBALS['page']->add(	'</div>'
							.'<span id="answer_info" style="color:#FF0000;width:97%;float:right;margin-bottom:5px;text-align:right;padding-right:30px;'.($tot_question > 0 && $test_info['mandatory_answer'] == 1 ? 'display:block;' : 'display:none;').'"><b>'.$lang->def('_NEED_ANSWER').'</b></span>'
							.'<div class="test_button_space">', 'content');

	if($test_info['save_keep'] == 1) {
		//save and exit
		$GLOBALS['page']->add(Form::getButton('test_save_keep', 'test_save_keep', $lang->def('_TEST_SAVE_KEEP')), 'content');
	}
	if($test_info['can_travel'] && ($page_to_display != 1)) {
		//back to the next page
		$GLOBALS['page']->add(Form::getButton('prev_page', 'prev_page', $lang->def('_TEST_PREV_PAGE')), 'content');
	}
	if($page_to_display != $tot_page) {
		//button to the next page
		$GLOBALS['page']->add(Form::getButton('next_page', 'next_page', $lang->def('_TEST_NEXT_PAGE'), '', ($tot_question > 0 && $test_info['mandatory_answer'] == 1 ? ' disabled="disabled"' : '')), 'content');
	} else {
		//button to the result page
		$GLOBALS['page']->add(Form::getButton('show_result', 'show_result', $lang->def('_TEST_END_PAGE'), '', ($tot_question > 0 && $test_info['mandatory_answer'] == 1 ? ' disabled="disabled"' : '')), 'content');
	}
	$GLOBALS['page']->add('</div>'
		.Form::closeForm()
		.'</div>', 'content');

}

function saveAndExit($object_test, $id_param) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once(_base_.'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php' );
	
	$lang 			=& DoceboLanguage::createInstance('test');
	$id_test 		= $object_test->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_test->back_url));
	$id_track 		= retriveTrack($id_reference, $id_test, getLogUserId());
	
	if($id_track === false) {
		
		$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_TRACK_FAILURE')
			.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))), 'content');
	}
	$test_man 		= new TestManagement($id_test);
	$play_man 		= new PlayTestManagement($id_test, getLogUserId(), $id_track, $test_man);
	$test_info 		= $test_man->getTestAllInfo();
	$track_info 	= $play_man->getTrackAllInfo();
	
	$GLOBALS['page']->add(
			getTitleArea($lang->def('_TITLE').' : '.$test_info['title'], 'test', $lang->def('_TEST_INFO'))
			.'<div class="std_block">', 'content');
	
	// find the page to display 
	$previous_page = importVar('previous_page', false, false);
	
	if($test_info['save_keep']) {
		$new_info = array(
			'last_page_seen' => $previous_page,
			'score_status' => 'not_complete' );
		if(isset($_POST['page_to_save'])) {
			
			if($test_info['mod_doanswer']) {
				
				$new_info['last_page_saved'] = $_POST['page_to_save'];
				$play_man->storePage($_POST['page_to_save'], $test_info['mod_doanswer']);
				$play_man->closeTrackPageSession($_POST['page_to_save']);
			} else {
				
				if($_POST['page_to_save'] > $track_info['last_page_saved']) {
					
					$new_info['last_page_saved'] = $_POST['page_to_save'];
					$play_man->storePage($_POST['page_to_save'], $test_info['mod_doanswer']);
					$play_man->closeTrackPageSession($_POST['page_to_save']);
				}
			}
		}
		$re_update = Track_Test::updateTrack($id_track, $new_info);
		
		if($re_update)  {
			
			$GLOBALS['page']->add(
				$lang->def('_OPERATION_SUCCESSFUL')
				.Form::openForm('test_savekeep', Util::str_replace_once('&', '&amp;', $object_test->back_url))
				.Form::openButtonSpace()
				.Form::getButton('test', 'test', $lang->def('_TEST_SAVEKEEP_BACK'))
				.Form::closeButtonSpace()
				.Form::closeForm(), 'content');
		} else {
			
			$GLOBALS['page']->add($lang->def('_OPERATION_FAILURE')
				.Form::openForm('test_savekeep','index.php?modname=test&amp;op=play')
			//-standard info
				.Form::getHidden('next_step', 'next_step', 'play')
				.Form::getHidden('id_test', 'id_test', $id_test)
				.Form::getHidden('id_param', 'id_param', $id_param)
				.Form::getHidden('back_url', 'back_url', $url_coded)
				.Form::getHidden('idTrack', 'idTrack', $id_track), 'content');
			//page info
			$GLOBALS['page']->add(
				Form::getHidden('previous_page', 'previous_page', $previous_page)
				.Form::openButtonSpace()
				.Form::getButton('test', 'test', $lang->def('_TEST_SAVEKEEP_FAILURE_BACK'))
				.Form::closeButtonSpace()
				.Form::closeForm(), 'content');
		}
	} else {
		
		//this test doesn't support save and keep
		$GLOBALS['page']->add($lang->def('_TEST_YOUCANNOT_SAVEKEEP')
			.Form::openForm('test_savekeep','index.php?modname=test&amp;op=play')
			//-standard info
			.Form::getHidden('next_step', 'next_step', 'play')
			.Form::getHidden('id_test', 'id_test', $id_test)
			.Form::getHidden('id_param', 'id_param', $id_param)
			.Form::getHidden('back_url', 'back_url', $url_coded)
			.Form::getHidden('idTrack', 'idTrack', $id_track), 'content');
			//page info
		$GLOBALS['page']->add(
			Form::getHidden('previous_page', 'previous_page', $previous_page)
			.Form::openButtonSpace()
			.Form::getButton('test', 'test', $lang->def('_TEST_SAVEKEEP_FAILURE_BACK'))
			.Form::closeButtonSpace()
			.Form::closeForm(), 'content');
	}
	$GLOBALS['page']->add('</div>', 'content');
}

function showResult( $object_test, $id_param ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	
	require_once(_base_.'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php' );
	
	$lang 			=& DoceboLanguage::createInstance('test');
	$id_test 		= $object_test->getId();
	$id_reference 	= getLoParam($id_param, 'idReference');
	$url_coded 		= urlencode(serialize($object_test->back_url));
	$id_track 		= retriveTrack($id_reference, $id_test, getLogUserId());
	
	if($id_track === false) {
		
		$GLOBALS['page']->add(getErrorUi($lang->def('_TEST_TRACK_FAILURE')
			.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))), 'content');
	}
	$test_man 		= new TestManagement($id_test);
	$play_man 		= new PlayTestManagement($id_test, getLogUserId(), $id_track, $test_man);
	$test_info 		= $test_man->getTestAllInfo();
	$track_info 	= $play_man->getTrackAllInfo();
	
	$previous_page = importVar('previous_page', false, false);
	
	$new_info = array(
		'last_page_seen' => $previous_page,
		'score_status' => 'doing' );
	
	if(isset($_POST['page_to_save']) && (($_POST['page_to_save'] > $track_info['last_page_saved']) || $test_info['mod_doanswer'])) {
		
		$play_man->storePage($_POST['page_to_save'], $test_info['mod_doanswer']);
		$play_man->closeTrackPageSession($_POST['page_to_save']);
	}
	
	$now = date('Y-m-d H:i:s');
	
	$point_do 		= 0;
	$max_score 		= 0;
	$num_manual 	= 0;
	$manual_score 	= 0;
	$point_do_cat 	= array();
	
	$re_visu_quest = sql_query("SELECT idQuest 
	FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
	WHERE idTrack = '".$id_track."' ");
		
	while(list($id_q) = sql_fetch_row($re_visu_quest)) $quest_see[] = $id_q;
	
	$reQuest = sql_query("
	SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
	FROM %lms_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t
	WHERE q.idTest = '".$id_test."' AND q.type_quest = t.type_quest AND q.idQuest IN (".implode($quest_see, ',').") 
	ORDER BY q.sequence");
	
	while(list($id_quest, $type_quest, $type_file, $type_class, $id_cat) = sql_fetch_row($reQuest)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
		
		$quest_point_do = 0;
		
		$quest_obj = new $type_class( $id_quest );
		$quest_point_do 	= $quest_obj->userScore($id_track);
		$quest_max_score 	= $quest_obj->getMaxScore();
		if($quest_obj->getScoreSetType() == 'manual') {
			++$num_manual;
			$manual_score = round($manual_score + $quest_max_score, 2);
		}
		$point_do = round($point_do + $quest_point_do, 2);
		$max_score = round($max_score + $quest_max_score, 2);
		if(isset($point_do_cat[$id_cat])) {
			$point_do_cat[$id_cat] = round($quest_point_do + $point_do_cat[$id_cat], 2);
		} else {
			$point_do_cat[$id_cat] = round($quest_point_do, 2);
		}
	}

	if($test_info['point_type'] == '1') { // percentage score (%)
		// x:100=$point_do:$max_score
		$point_do =round(100*$point_do/$max_score);
	}
	$save_score = $point_do;

	// save new status in track
	if($point_do >= $test_info['point_required']) {
		$next_status = 'passed';
		if($test_info['show_only_status']) $score_status = 'passed';
	} else {
		$next_status = 'failed';
		if($test_info['show_only_status']) $score_status = 'not_passed';
	}
	if(!$test_info['show_only_status']) {
		if($num_manual != 0) $score_status = 'not_checked';
		else $score_status = 'valid';
	}
	$test_track = new Track_Test($id_track);
	$test_track->setDate($now);
	$test_track->status = $next_status;
	$test_track->update();
	
	// --
	require_once(_lms_.'/lib/lib.assessment_rule.php');
	$score_arr =array();
	$i =0;
	foreach($point_do_cat as $cat_id=>$score) {
		$score_arr[$i]['score']=$score;
		$score_arr[$i]['category_id']=$cat_id;
		$i++;
	}
	// final score:
	$score_arr[$i]['score']=$point_do;
	$score_arr[$i]['category_id']=0;
	$asrule =new AssessmentRuleManager($id_test);
	$feedback_txt = $asrule->setRulesFromScore($score_arr);
	$asrule->loadJs();
	// --
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE').' : '.$test_info['title'], 'test', $lang->def('_TEST_INFO'))
		.'<div class="std_block">'
		.( $next_status == 'failed' 
			? '<b>'.$lang->def('_TEST_FAILED').'</b>' 
			: $lang->def('_TEST_COMPLETED') )
		.'<br />', 'content');
	
	if($test_info['point_type'] != '1') {
		$save_score = $point_do;
	} else {
		$save_score = $point_do;//round(round($point_do / $max_score, 2) * 100, 2);
	}
	
	$track_info = Track_Test::getTrackInfo( getLogUserId(), $id_test, $id_reference );
	if($score_status == 'valid' || $score_status == 'not_checked' || $score_status == 'passed' || $score_status == 'not_passed') {
		$new_info['date_end_attempt'] 	= $now;
		$new_info['number_of_save'] 	= $track_info['number_of_save'] + 1;
		$new_info['score'] 				= $save_score;
		$new_info['score_status'] 		= $score_status;
		$new_info['number_of_attempt'] 	= $track_info['number_of_attempt'] + 1;
		
		$re_update = Track_Test::updateTrack($id_track, $new_info);
		if (!isset($_POST['show_review'])) {

            $time = fromDatetimeToTimestamp(date('Y-m-d H:i:s')) - fromDatetimeToTimestamp($_SESSION['test_date_begin']);

            sql_query("
            INSERT INTO ".$GLOBALS['prefix_lms']."_testtrack_times
            (idTrack, idReference, idTest, date_attempt, number_time, score, score_status, date_begin, date_end, time) VALUES
            ('".$id_track."', '".$id_reference."', '".$id_test."', now(), '".$new_info['number_of_save']."', '".$new_info['score']."', '".$new_info['score_status']."', '".$_SESSION['test_date_begin']."', '".date('Y-m-d H:i:s')."', '".$time."')");

	        unset($_SESSION['test_date_begin']);
        }
	}

	//--- check suspension conditions ----------------------------------------------

	if ($test_info['use_suspension']) {
		$suspend_info = array();
		if ($next_status == 'failed') {
			$suspend_info['attempts_for_suspension'] = $track_info['attempts_for_suspension'] + 1;
			if ($suspend_info['attempts_for_suspension'] >= $test_info['suspension_num_attempts']) {
				//should we reset learning_test.suspension_num_attempts ??
				$suspend_info['attempts_for_suspension'] = 0; //from now on, it uses the suspended_until parameter, so only the date is needed, we can reset the attempts count
				$suspend_info['suspended_until'] = date("Y-m-d H:i:s", time()+$test_info['suspension_num_hours']*3600);
			}
			$re = Track_Test::updateTrack($id_track, $suspend_info);
		} else {
			if ($next_status == 'completed' || $next_status == 'passed') {
				$suspend_info['attempts_for_suspension'] = 0;
				$re = Track_Test::updateTrack($id_track, $suspend_info);
			}
		}
	}

//--- end suspensions check ----------------------------------------------------

	list($bonus_score, $score_status) = sql_fetch_row( sql_query("
	SELECT bonus_score, score_status
	FROM ".$GLOBALS['prefix_lms']."_testtrack 
	WHERE idTrack = '".(int)$id_track."'"));
	
	if($test_info['show_score'] && $test_info['point_type'] != '1') {
		
		$GLOBALS['page']->add('<span class="test_score_note">'.$lang->def('_TEST_TOTAL_SCORE').'</span> '.($point_do + $bonus_score).' / '.$max_score.'<br />', 'content');
		if($num_manual != 0 && $score_status != 'valid') {
			$GLOBALS['page']->add('<br />'
				.'<span class="test_score_note">'.$lang->def('_TEST_MANUAL_SCORE').'</span> '.$manual_score.' '.$lang->def('_TEST_SCORES').'<br />', 'content');
		}
		if($test_info['point_required'] != 0) {
			$GLOBALS['page']->add('<br />'
				.'<span class="test_score_note">'.$lang->def('_TEST_REQUIREDSCORE_RESULT').'</span> '.$test_info['point_required'].'<br />', 'content');
		}
	}
	if($test_info['show_score'] && $test_info['point_type'] == '1') {
		
		$GLOBALS['page']->add('<span class="test_score_note">'.$lang->def('_TEST_TOTAL_SCORE').'</span> '.$save_score.' %'.'<br />', 'content');
		if($num_manual != 0) {
			$GLOBALS['page']->add('<br />'
				.'<span class="test_score_note">'.$lang->def('_TEST_MANUAL_SCORE').'</span> '.$manual_score.' '.$lang->def('_TEST_SCORES').'<br />', 'content');
		}
	}
	if($test_info['show_score_cat']) {
		
		$re_category = sql_query("
		SELECT c.idCategory, c.name, COUNT(q.idQuest)
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q 
			JOIN ".$GLOBALS['prefix_lms']."_quest_category AS c
		WHERE c.idCategory = q.idCategory AND q.idTest = '".$id_test."' AND q.idCategory != 0 
		GROUP BY c.idCategory 
		ORDER BY c.name");
		
		if(sql_num_rows($re_category)) {
			
			$GLOBALS['page']->add('<br />'
				.'<table summary="'.$lang->def('_TEST_CATEGORY_SCORE').'" class="category_score">'
				.'<caption>'.$lang->def('_TEST_CATEGORY_SCORE').'</caption>'
				.'<thead>'
					.'<tr>'
						.'<th>'.$lang->def('_TEST_QUEST_CATEGORY').'</th>'
						.'<th class="number">'.$lang->def('_TEST_QUEST_NUMBER').'</th'
						.'<th class="number">'.$lang->def('_TEST_TOTAL_SCORE').'</th>'
					.'</tr>'
				.'</thead>'
				.'<tbody>', 'content');
			while(list($id_cat, $name_cat, $quest_number) = sql_fetch_row($re_category)) {
				
				$GLOBALS['page']->add('<tr><td>'.$name_cat.'</td>'
					.'<td class="number">'.$quest_number.'</td>'
					.'<td class="number">'.( isset($point_do_cat[$id_cat]) ? $point_do_cat[$id_cat] : 0 ).'</td></tr>'
				, 'content');
			}
			/*
			$GLOBALS['page']->add('<br />'
				.'<span class="test_score_note">'.$lang->def('_TEST_CATEGORY_SCORE').'</span><br />', 'content');
			while(list($id_cat, $name_cat, $quest_number) = sql_fetch_row($re_category)) {
				
				$GLOBALS['page']->add($name_cat.', '.$lang->def('_TEST_SCORES').': '
					.( isset($point_do_cat[$id_cat]) ? $point_do_cat[$id_cat] : 0 ).'<br />', 'content');
			}
			*/
			$GLOBALS['page']->add('</tbody></table>', 'content');
		}
	}
	$GLOBALS['page']->add('<br /><br />', 'content');

	//--- if chart visualization enabled, then show it ---------------------------

	require_once(_base_.'/lib/lib.json.php');
	$json = new Services_JSON();
	if ($test_info['chart_options'] !== "")
		$chart_options = $json->decode($test_info['chart_options']);
	else
		$chart_options = new stdClass();
	if (!property_exists($chart_options, 'use_charts')) $chart_options->use_charts = false;
	if (!property_exists($chart_options, 'selected_chart')) $chart_options->selected_chart = 'column';
	if (!property_exists($chart_options, 'show_chart')) $chart_options->show_chart = 'teacher';

	if ($chart_options->use_charts && $chart_options->show_chart=='course') {
		cout('<div class="align-center">', 'content');
		$chart = new Test_Charts($test_info['idTest'], Docebo::user()->getIdSt());
		$chart->render($chart_options->selected_chart, true);
		cout('</div><br /><br />', 'content');
	}

	//--- end show chart ---------------------------------------------------------

	if($feedback_txt) cout('<p>'.$feedback_txt.'</p><br />', 'content');


	$points = $point_do + $bonus_score;
	if($test_info['show_solution'] == 2 && $points >= $test_info['point_required'])
	{
		$GLOBALS['page']->add(Form::openForm('test_show', 'index.php?modname=test&amp;op=play')
			.Form::getHidden('next_step', 'next_step', 'test_review')
			.Form::getHidden('id_test', 'id_test', $id_test)
			.Form::getHidden('id_param', 'id_param', $id_param)
			.Form::getHidden('back_url', 'back_url', $url_coded)
			.Form::getHidden('idTrack', 'idTrack', $id_track)
			.Form::getButton('review', 'review', $lang->def('_TEST_REVIEW_ANSWER'))
			.Form::closeForm(), 'content');
	}
	elseif($test_info['show_doanswer'] == 2 && $points >= $test_info['point_required'])
	{
		$GLOBALS['page']->add(Form::openForm('test_show', 'index.php?modname=test&amp;op=play')
			.Form::getHidden('next_step', 'next_step', 'test_review')
			.Form::getHidden('id_test', 'id_test', $id_test)
			.Form::getHidden('id_param', 'id_param', $id_param)
			.Form::getHidden('back_url', 'back_url', $url_coded)
			.Form::getHidden('idTrack', 'idTrack', $id_track)
			.Form::getButton('review', 'review', $lang->def('_TEST_REVIEW_ANSWER'))
			.Form::closeForm(), 'content');
	}
	elseif($test_info['show_solution'] != 2 && $test_info['show_doanswer'] != 2)
		if($test_info['show_solution'] || $test_info['show_doanswer'])
		{
			$GLOBALS['page']->add(Form::openForm('test_show', 'index.php?modname=test&amp;op=play')
				.Form::getHidden('next_step', 'next_step', 'test_review')
				.Form::getHidden('id_test', 'id_test', $id_test)
				.Form::getHidden('id_param', 'id_param', $id_param)
				.Form::getHidden('back_url', 'back_url', $url_coded)
				.Form::getHidden('idTrack', 'idTrack', $id_track)
				.Form::getButton('review', 'review', $lang->def('_TEST_REVIEW_ANSWER'))
				.Form::closeForm(), 'content');
		}
	
	$GLOBALS['page']->add(Form::openForm('test_show', Util::str_replace_once('&', '&amp;', $object_test->back_url))
		.'<div class="align_right">'
		.Form::getButton('end_test', 'end_test', $lang->def('_TEST_END_BACKTOLESSON'))
		.'</div>'
		.Form::closeForm(), 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function review($object_test, $id_param) {
	$lang =& DoceboLanguage::createInstance('test');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php' );
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	
	$idTest 		= $object_test->getId();
	$idTrack 		= importVar('idTrack', true, 0);
	$idReference 	= getLOParam( $id_param, 'idReference' );
	
	//test info---------------------------------------------------------
	list($title, $show_solution, $question_random_number, $order_type) = sql_fetch_row( sql_query("
	SELECT  title, show_solution, question_random_number, order_type
	FROM ".$GLOBALS['prefix_lms']."_test 
	WHERE idTest = '".(int)$idTest."'"));
	
	list($score, $bonus_score, $date_attempt, $date_attempt_mod) = sql_fetch_row( sql_query("
	SELECT score, bonus_score, date_attempt, date_attempt_mod 
	FROM ".$GLOBALS['prefix_lms']."_testtrack 
	WHERE idTrack = '".(int)$idTrack."'"));
	
	//questions------------------------------------------------------
	if($order_type >= 2) {
		$re_visu_quest = sql_query("SELECT idQuest 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
		WHERE idTrack = '".(int)$idTrack."' ");
		
		while(list($id_q) = sql_fetch_row($re_visu_quest)) $quest_see[] = $id_q;
		
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class  
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$idTest."' AND q.type_quest = t.type_quest AND q.idQuest IN (".implode($quest_see, ',').") 
			 AND q.type_quest <> 'break_page' AND q.type_quest <> 'title' 
		ORDER BY q.sequence";
	} else {
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$idTest."' AND q.type_quest = t.type_quest 
			 AND q.type_quest <> 'break_page' 
		ORDER BY q.sequence";
	}
	$reQuest = sql_query($query_question);
	
	//display-----------------------------------------------------------
	$GLOBALS['page']->add('<div class="std_block">'
		.'<div class="test_title_play">'.$lang->def('_TITLE').' : '.$title.'</div>'
		.getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))
		.'<br />', 'content');
	
	//page display---------------------------------------------------
	$GLOBALS['page']->add('<div class="test_answer_space">', 'content');
	$quest_sequence_number = 1;
	while(list($idQuest, $type_quest, $type_file, $type_class) = sql_fetch_row($reQuest)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
		$quest_obj = eval("return new $type_class( $idQuest );");
		
		$review = $quest_obj->displayUserResult( 	$idTrack, 
													( $type_quest != 'title' ? $quest_sequence_number++ : $quest_sequence_number ), 
													$show_solution );
		
		$GLOBALS['page']->add('<div class="test_quest_review_container">'
			.$review['quest'], 'content');
		
		if($review['score'] !== false) {
			$GLOBALS['page']->add(
				'<div class="test_answer_comment">'
				.'<div class="test_score_note">'.$lang->def('_SCORE').' : ', 'content');
			if($quest_obj->getScoreSetType() == 'manual' && !$review['manual_assigned'] ) {
				$GLOBALS['page']->add($lang->def('_NOT_ASSIGNED'), 'content');
			} else {
				if($review['score'] > 0) {
					$GLOBALS['page']->add('<span class="test_score_positive">'.$review['score'].'</span>', 'content');
				} else {
					$GLOBALS['page']->add('<span class="test_score_negative">'.$review['score'].'</span>', 'content');
				}
			}
			$GLOBALS['page']->add(
				'</div>'
				.( $review['comment'] != '' ? $review['comment'] : '' )
				.'</div>', 'content');
		}
		$GLOBALS['page']->add( 
			'</div>', 'content');
	}
	$GLOBALS['page']->add('</div>', 'content');
	$GLOBALS['page']->add(getBackUi(Util::str_replace_once('&', '&amp;', $object_test->back_url), $lang->def('_BACK'))
		.'</div>', 'content');
}




function user_report($idUser, $idTest, $id_param = false, $id_track = false, $mvc = false) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	$lang =& DoceboLanguage::createInstance('test');
	
	if($id_param !== false) {
		require_once(_lms_.'/lib/lib.param.php' );
		
		$idReference 	= getLOParam( $id_param, 'idReference' );
		
		if(!Track_Test::isTrack($idUser, $idTest, $idReference)) return;
		
		//load existing info track
		$track_info = Track_Test::getTrackInfo($idUser, $idTest, $idReference);
		$idTrack = $track_info['idTrack'];
	} else {
		
		$idTrack = $id_track;
	}
	//test info---------------------------------------------------------
	list( $title, $mod_doanswer, $point_type, $point_required, $question_random_number, 
		$show_score, $show_score_cat, $show_doanswer, 
		$show_solution, $order_type) = sql_fetch_row( sql_query("
	SELECT  title, mod_doanswer, point_type, point_required, question_random_number, 
			show_score, show_score_cat, show_doanswer, 
			show_solution, order_type
	FROM %lms_test
	WHERE idTest = '".(int)$idTest."'"));
	
	list($score, $bonus_score, $date_attempt, $date_attempt_mod) = sql_fetch_row( sql_query("
	SELECT score, bonus_score, date_attempt, date_attempt_mod 
	FROM %lms_testtrack
	WHERE idTrack = '".(int)$idTrack."'"));
	
	$point_do 				= $bonus_score;
	$max_score 				= 0;
	$num_manual 			= 0;
	$manual_score 			= 0;
	$quest_sequence_number 	= 1;
	$report_test			= '';
	$point_do_cat 			= array();
	/*
	$reQuest = sql_query("
	SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
	FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
	WHERE q.idTest = '".$idTest."' AND q.type_quest = t.type_quest 
	ORDER BY q.sequence");*/
	if($order_type >= 2) {
		$re_visu_quest = sql_query("SELECT idQuest 
		FROM %lms_testtrack_quest
		WHERE idTrack = '".(int)$idTrack."' ");
		
		while(list($id_q) = sql_fetch_row($re_visu_quest)) $quest_see[] = $id_q;
		
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
		FROM %lms_testquest AS q JOIN %lms_quest_type AS t
		WHERE q.idTest = '".$idTest."' AND q.type_quest = t.type_quest AND  q.idQuest IN (".implode($quest_see, ',').") 
		ORDER BY q.sequence";
	} else {
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
		FROM %lms_testquest AS q JOIN %lms_quest_type AS t
		WHERE q.idTest = '".$idTest."' AND q.type_quest = t.type_quest 
		ORDER BY q.sequence";
	}
	$reQuest = sql_query($query_question);
	while(list($id_quest, $type_quest, $type_file, $type_class, $id_cat) = sql_fetch_row($reQuest)) {
		
		require_once(_lms_.'/modules/question/'.$type_file);
		
		$quest_point_do = 0;
		
		$quest_obj = eval("return new $type_class( $id_quest );");
		$quest_point_do = $quest_obj->userScore($idTrack);
		$quest_max_score = $quest_obj->getMaxScore();
		if(($type_quest != 'title') && ($type_quest != 'break_page')) {
			$review = $quest_obj->displayUserResult( 	$idTrack, 
														( $type_quest != 'title' ? $quest_sequence_number++ : $quest_sequence_number ), 
														$show_solution );
			
			$report_test .= '<div class="test_quest_review_container">'
				.$review['quest'];
			
			if($review['score'] !== false) {
				$report_test .= '<div class="test_answer_comment">'
					.'<div class="test_score_note">'.$lang->def('_SCORE').' : ';
				if($quest_obj->getScoreSetType() == 'manual' && !$review['manual_assigned'] ) {
					$report_test .= $lang->def('_NOT_ASSIGNED');
				} else {
					
					if($review['score'] > 0) {
						$report_test .= '<span class="test_score_positive">'.$review['score'].'</span>';
					} else {
						$report_test .= '<span class="test_score_negative">'.$review['score'].'</span>';
					}
				}
				$report_test .= '</div>'
								.'</div>';
			}
			
			$report_test .= 
				//.( $review['comment'] != '' ? $review['comment'] : '' )
				'</div>'."\n";
		}
		if($quest_obj->getScoreSetType() == 'manual') {
			++$num_manual;
			$manual_score = round($manual_score + $quest_max_score, 2);
		}
		
		$point_do = round($point_do + $quest_point_do, 2);
		$max_score = round($max_score + $quest_max_score, 2);
		if(isset($point_do_cat[$id_cat])) {
			$point_do_cat[$id_cat] = round($point_do + $point_do_cat[$id_cat], 2);
		}
		else {
			$point_do_cat[$id_cat] = $point_do;
		}
	}
	
	//output variable, used in mvc mode
	$output = "";

	$str = "";
	if (!$mvc) $str .= '<div class="std_block">';
	$str .= '<div class="title">'.$lang->def('_TITLE').' : '.$title.'</div><br />';
	if ($mvc) {
		$output .= $str;
	} else {
		$GLOBALS['page']->add($str, 'content');
	}

	if($point_type != '1') $save_score = $point_do;
	else $save_score = round(round($point_do / $max_score, 2) * 100, 2);
	
	if($show_score && $point_type != '1') {

		$str = '<span class="test_score_note">'.$lang->def('_TEST_TOTAL_SCORE').'</span> '.$point_do.' / '.$max_score.'<br />';
		if ($mvc) {
			$output .= $str;
		} else {
			$GLOBALS['page']->add($str, 'content');
		}
		if($num_manual != 0) {
			$str = '<br /><span class="test_score_note">'.$lang->def(/*'_TEST_MANUAL_SCORE_REPORT'*/'_TEST_MANUAL_SCORE').'</span> '
				.$manual_score.' '.$lang->def('_TEST_SCORES').'<br />';
			if ($mvc) {
				$output .= $str;
			} else {
				$GLOBALS['page']->add($str, 'content');
			}
		}
	}
	if($show_score && $point_type == '1') {
		
		$str = '<span class="test_score_note">'.$lang->def('_TEST_TOTAL_SCORE').'</span> '.$save_score.' %'.'<br />';
		if ($mvc) {
			$output .= $str;
		} else {
			$GLOBALS['page']->add($str, 'content');
		}
		if($num_manual != 0) {
			$str = '<br /><span class="test_score_note">'.$lang->def(/*'_TEST_MANUAL_SCORE_REPORT'*/'_TEST_MANUAL_SCORE').'</span> '
				.$manual_score.' '.$lang->def('_TEST_SCORES').'<br />';
			if ($mvc) {
				$output .= $str;
			} else {
				$GLOBALS['page']->add($str, 'content');
			}
		}
	}
	if($show_score_cat) {
		
		$category = array();
		$reQuestCat = sql_query("
		SELECT idCategory 
		FROM %lms_testquest
		WHERE idTest = '".$idTest."' AND idCategory != 0");
		while(list($id_cat) = sql_fetch_row($reQuestCat)) $category[] = $id_cat;
		
		if(!empty($category)) {
			
			require_once(_lms_.'/lib/lib.questcategory.php');
			
			$categories = Questcategory::getInfoAboutCategory($category);
			$str = '<br /><span class="test_score_note">'.$lang->def('_TEST_CATEGORY_SCORE').'</span><br />';
			if ($mvc) {
				$output .= $str;
			} else {
				$GLOBALS['page']->add($str, 'content');
			}

			while(list($id_cat, $name_cat) = each($categories)) {
				
				$str = $name_cat.', '.$lang->def('_TEST_SCORES').': '
					.( isset($point_do_cat[$id_cat]) ? $point_do_cat[$id_cat] : 0 ).'<br />';
				if ($mvc) {
					$output .= $str;
				} else {
					$GLOBALS['page']->add($str, 'content');
				}
			}
		}
	}
	$str = '<br /><br /><div class="test_answer_space">'.$report_test.'</div>';
	if (!$mvc) $str .= '</div>'; //end stdblock div
	if ($mvc) {
		return $output;
	} else {
		$GLOBALS['page']->add($str, 'content');
	}
}


function editUserReport($id_user, $id_test, $id_track) {
	
	$lang =& DoceboLanguage::createInstance('test');
	
	//test info---------------------------------------------------------
	list( $title, $mod_doanswer, $point_type, $point_required, $question_random_number, 
		$show_score, $show_score_cat, $show_doanswer, 
		$show_solution, $order_type) = sql_fetch_row( sql_query("
	SELECT  title, mod_doanswer, point_type, point_required, question_random_number, 
			show_score, show_score_cat, show_doanswer, 
			show_solution, order_type
	FROM ".$GLOBALS['prefix_lms']."_test 
	WHERE idTest = '".(int)$id_test."'"));
	
	list($score, $bonus_score, $date_attempt, $date_attempt_mod, $date_end_attempt) = sql_fetch_row( sql_query("
	SELECT score, bonus_score, date_attempt, date_attempt_mod, date_end_attempt
	FROM ".$GLOBALS['prefix_lms']."_testtrack 
	WHERE idTrack = '".(int)$id_track."'"));
	
	$point_do 				= 0;
	$max_score 				= 0;
	$num_manual 			= 0;
	$manual_score 			= 0;
	$quest_sequence_number 	= 1;
	$report_test			= '';
	$point_do_cat 			= array();
	
	if($order_type >= 2) {
		$re_visu_quest = sql_query("SELECT idQuest 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
		WHERE idTrack = '".(int)$id_track."' ");
		
		while(list($id_q) = sql_fetch_row($re_visu_quest)) $quest_see[] = $id_q;
		
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$id_test."' AND q.type_quest = t.type_quest AND  q.idQuest IN (".implode($quest_see, ',').") 
		ORDER BY q.sequence";
	} else {
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$id_test."' AND q.type_quest = t.type_quest 
		ORDER BY q.sequence";
	}
	$reQuest = sql_query($query_question);
	while(list($id_quest, $type_quest, $type_file, $type_class, $id_cat) = sql_fetch_row($reQuest)) {
		
		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
		
		$quest_point_do = 0;
		
		$quest_obj = eval("return new $type_class( $id_quest );");
		$quest_point_do = $quest_obj->userScore($id_track);
		$quest_max_score = $quest_obj->getMaxScore();
		if(($type_quest != 'title') && ($type_quest != 'break_page')) {
			$review = $quest_obj->displayUserResult( 	$id_track, 
														( $type_quest != 'title' ? $quest_sequence_number++ : $quest_sequence_number ), 
														$quest_sequence_number );
			
			$report_test .= '<div class="test_quest_review_container">'
				.$review['quest'];
			
			if($review['score'] !== false) {
				$report_test .= '<div class="test_answer_comment_nomargin">'
					.'<div class="test_score_note">'.$lang->def('_SCORE').' : ';
				if($quest_obj->getScoreSetType() == 'manual' && !$review['manual_assigned'] ) {
					$report_test .= $lang->def('_NOT_ASSIGNED');
				} else {
				if($review['score'] > 0) {
					$report_test .= '<span class="test_score_positive">'.$review['score'].'</span>';
				} else {
					$report_test .= '<span class="test_score_negative">'.$review['score'].'</span>';
				}
			}
				$report_test .= '</div>'
					.( $review['comment'] != '' ? $review['comment'] : '' )
					.'</div>';
			}
			$report_test .= 
				'<div class="test_edit_scores">'
				.Form::getTextfield(	$lang->def('_NEW_SCORE_FOR_QUESTION'),
										'new_user_score_'.$id_quest, 
										'new_user_score['.$id_quest.']', 
										8, 
										'' )
				.'</div>'."\n"
				.'</div>'."\n";
		}
	}

	$total_time = 0;
	$total_time = fromDatetimeToTimestamp($date_end_attempt) - fromDatetimeToTimestamp($date_attempt);
	if($total_time > 0)
	{
		$seconds = $total_time % 60;
		$total_time -= $seconds;
		$minutes = $total_time / 60;
	}
	
	$GLOBALS['page']->add(
		'<div class="title">'.$lang->def('_TITLE').' : '.$title.'</div>', 'content');
	
	$GLOBALS['page']->add('<br />'
		.Form::getTextfield(	$lang->def('_BONUS_SCORE_FOR_TEST'),
										'bonus_score', 
										'bonus_score', 
										8, 
										$bonus_score )
		.'<br />'
		.($total_time > 0 ? '<b>'.Lang::t('_DATE_BEGIN', 'standard').'</b> : '.Format::date($date_attempt, 'datetime')
		.'<br />'
		.'<b>'.Lang::t('_DATE_END', 'standard').'</b> : '.Format::date($date_end_attempt, 'datetime')
		.'<br />'
		.'<b>'.Lang::t('_TOTAL_TIME', 'test').'</b> : '.$minutes.':'.$seconds
		.'<br />'
		.'<br />' : '')
		.'<div class="test_answer_space">'
		.$report_test
		.'</div>', 'content');
}

function saveManualUserReport($id_user, $id_test, $id_track) {
	
	require_once($GLOBALS['where_lms'].'/class.module/track.test.php');
	
	list( $title, $mod_doanswer, $point_type, $point_required, $question_random_number, 
		$show_score, $show_score_cat, $show_doanswer, 
		$show_solution, $show_only_status, $order_type) = sql_fetch_row( sql_query("
	SELECT  title, mod_doanswer, point_type, point_required, question_random_number, 
			show_score, show_score_cat, show_doanswer, 
			show_solution, show_only_status, order_type
	FROM ".$GLOBALS['prefix_lms']."_test 
	WHERE idTest = '".(int)$id_test."'"));
	
	list($score, $bonus_score, $date_attempt, $date_attempt_mod, $score_status) = sql_fetch_row( sql_query("
	SELECT score, bonus_score, date_attempt, date_attempt_mod, score_status 
	FROM ".$GLOBALS['prefix_lms']."_testtrack 
	WHERE idTrack = '".(int)$id_track."'"));
	
	if($order_type >= 2) {
		$re_visu_quest = sql_query("SELECT idQuest 
		FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
		WHERE idTrack = '".(int)$id_track."' ");
		
		while(list($id_q) = sql_fetch_row($re_visu_quest)) $quest_see[] = $id_q;
		
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$id_test."' AND q.type_quest = t.type_quest AND  q.idQuest IN (".implode($quest_see, ',').") 
		ORDER BY q.sequence";
	} else {
		$query_question = "
		SELECT q.idQuest, q.type_quest, t.type_file, t.type_class, q.idCategory 
		FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
		WHERE q.idTest = '".$id_test."' AND q.type_quest = t.type_quest 
		ORDER BY q.sequence";
	}
	
	$point_do = 0 ;
	$reQuest = sql_query($query_question);
	while(list($id_quest, $type_quest, $type_file, $type_class, $id_cat) = sql_fetch_row($reQuest)) {
		
		// instance question class
		require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
		$quest_obj = eval("return new $type_class( $id_quest );");
		
		// check score
		if(($type_quest != 'title') && ($type_quest != 'break_page')) {
			
			$quest_max_score = $quest_obj->getMaxScore();
			if(isset($_POST['new_user_score'][$id_quest]) && $_POST['new_user_score'][$id_quest] != '') {
				
				if(!$quest_obj->setUserScore($id_track, $id_quest, $_POST['new_user_score'][$id_quest])) {
						
					$quest_point_do = $quest_obj->userScore($id_track);
				} else {
					
					$quest_point_do = $_POST['new_user_score'][$id_quest];
				}
			} else {
				
				$quest_point_do = $quest_obj->userScore($id_track);
			} // end else
			
			$point_do = round($point_do + $quest_point_do, 2);
			$max_score = round($max_score + $quest_max_score, 2);
		} // end if
	}
	if($point_type != '1') $save_score = $point_do;
	else $save_score = round(round($point_do / $max_score, 2) * 100, 2);
	
	//if($score_status == 'valid') {
		
	$query_scores = "
	UPDATE ".$GLOBALS['prefix_lms']."_testtrack
	SET score = '".$save_score."',
		bonus_score = '".$_POST['bonus_score']."'
	WHERE idTest = '".$id_test."' AND idUser = '".$id_user."'";
	$re &= sql_query($query_scores);
	
	// update status in lesson
	if($point_do >= $point_required) {
		$next_status = 'passed';
	} else {
		$next_status = 'failed';
	}
	
	$test_track = new Track_Test($id_track);
	$test_track->setDate(date('Y-m-d H:i:s'));
	$test_track->status = $score_status;
	$test_track->update();

	//}
	
	
}

?>
