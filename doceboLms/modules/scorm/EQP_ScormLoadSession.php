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
	
	if (!empty($_GET)) {
		$query_aiccdata = "SELECT aicc_data"
				." FROM yes_learning_user_item_answer"
				." WHERE idUser = ".$_GET['idUser']
				." AND idscorm_item = ".$_GET['idscorm_item'];
		
		$result = $db->query($query_aiccdata);
		$row = $db->fetch_row($result);
		$aicc_data = $row[0]; 
	}
			
	echo $aicc_data;

	ob_end_flush();
	
?>