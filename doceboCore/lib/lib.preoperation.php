<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2010 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

if( Docebo::user()->isAnonymous() && $GLOBALS['modname'] != 'login') {
    
    Util::jump_to('index.php?modname=login&op=login');
}

if(isset($_GET['of_platform']) || isset($_POST['of_platform'])) {
	$_SESSION['current_action_platform'] = Get::req('of_platform');
}

// NOTE: some special function
switch($GLOBALS['op']) {
	case "confirm" : {

		if($GLOBALS['modname'] == 'login') {

			require_once(_base_.'/lib/lib.usermanager.php');
			$manager = new UserManager();
			$login_data = $manager->getLoginInfo();
			$manager->saveUserLoginData();

			$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromLogin( 	$login_data['userid'],
																				$login_data['password'],
																				( Get::sett('common_admin_session', 'on') == 'on' ? "public_area" : "admin_area" ),
																				$login_data['lang'] );

			if( $GLOBALS['current_user'] === FALSE ) {
				$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession(( Get::sett('common_admin_session', 'on') == 'on' ? "public_area" : "admin_area" ));
				$GLOBALS['access_fail'] = true;
				$GLOBALS['op'] = 'login';
			} else {
				$GLOBALS['r'] = 'adm/dashboard/show';
				$GLOBALS['modname'] = '';

				//loading related ST
				$GLOBALS['current_user']->loadUserSectionST();
				$GLOBALS['current_user']->SaveInSession();

				// perform other platforms login operation
				require_once(_base_.'/lib/lib.platform.php');
				$pm =& PlatformManager::createInstance();
				$pm->doCommonOperations("login");

			}
			
		}
	};break;
	case "logout" : {

		$_SESSION = array();
		session_destroy();

 		// Recreate Anonymous user
 		$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession(( (Get::sett('common_admin_session') == 'on') ? "public_area" : "admin_area" ));

		$GLOBALS['op'] 		= 'login';
		$GLOBALS['modname'] = 'login';
		$GLOBALS['logout'] 	= true;

		$pm=& PlatformManager::createInstance();
		$pm->doCommonOperations("logout");
		Util::jump_to(Get::rel_path('base').'/index.php?modname=login&op=login');
	};break;
}

if(isset($_GET['close_over'])) {
	$_SESSION['menu_over']['p_sel'] = '';
	$_SESSION['menu_over']['main_sel'] = 0;
}

?>