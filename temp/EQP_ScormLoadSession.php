<?php

	ini_set('display_errors', 1);
	define("LMS", true);
	define("IN_DOCEBO", true);
	define("_deeppath_", '../');
	require(dirname(__FILE__).'/../../../base.php');
	
	// start buffer
	ob_start();

	// initialize
	require(_base_.'/lib/lib.bootstrap.php');
	Boot::init(BOOT_USER);

	// connect to the database
	$db =& DbConn::getInstance();
	
	$idUser = $current_user->getId();
	
	var_dump($_SESSION);
	
	$query_scormitem_id = "SELECT idResource"
			." FROM ".$GLOBALS['prefix_lms']."_organization"
			." WHERE idCourse = ".$_SESSION['idCourse']
			." AND objectType like 'scormorg'";
	
	
	$result = $db->query($query_scormitem_id) or die( "Error on load sco: ". mysql_error() . "[ $query_scormitem_id ]");
	$row = $db->fetch_row($result);
	$idscorm_item = $row[0];
	
	if (!empty($idUser) && !empty($idscorm_item)) {
		$query_aiccdata = "SELECT aicc_data"
				." FROM yes_learning_user_item_score"
				." WHERE idUser = ".$idUser
				." AND idscorm_item = ".$idscorm_item;
		
		$result = $db->query($query_aiccdata);
		$row = $db->fetch_row($result);
		$aicc_data = $row[0]; 
	}
			
			
	echo "idUser=".$idUser."&idscorm_item=".$idscorm_item."&aicc_data=".$aicc_data;
	
	
	ob_end_flush();
	
?>