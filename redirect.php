<?php

define("IN_DOCEBO", true);
define("_deeppath_", '');
require(dirname(__FILE__).'/base.php');

require(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_USER);

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

require_once(_base_.'/lib/lib.platform.php');

$email = isset($_GET['email']) ? addslashes($_GET['email']) : false;
$pass = isset($_GET['code']) ? addslashes($_GET['code']) : false;
$back = isset($_GET['back']) ? $_GET['back'] : false;

if($email !== false && $pass !== false)
{
    $sql = 'SELECT * FROM %adm_user WHERE email LIKE "'.$email.'" AND pass="'.$pass.'"';
    $res = sql_query($sql);
    
    if(sql_num_rows($res) > 0)
    {
        $infos = sql_fetch_object($res);
        
        $user = new DoceboUser($infos->userid, 'public_area');
    	$user->loadUserSectionST('/lms/course/public/');
		$pm =& PlatformManager::createInstance();
		$pm->doCommonOperations("login");
		$user->SaveInSession();
    	
		if($back !== false)
		{
		    $url = '/';
		    
    		switch($back)
    		{
    		    case 'catalogue': $url = '/doceboLms/index.php?r=elearning/catalogue'; break;
    		}
    		
    		header('Location:'.$url);
		}
    	else header('Location:/');
    }
    else header('Location:/');
}