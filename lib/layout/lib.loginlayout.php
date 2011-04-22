<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

class LoginLayout {

	/**
	 * Return the menu for the pre-login pages
	 * @return <string>
	 */
	public static function menu() {

		$db = DbConn::getInstance();

		$li = '';
		$ul = '<ul id="main_menu">';
		if(Get::sett('course_block') == 'on') {
			$li .= '<li class="first"><a href="index.php?modname=login&amp;op=courselist">'.Lang::t('_COURSE_LIST', 'course').'</a></li>';
		}
		$query = "
		SELECT idPages, title
		FROM %lms_webpages
		WHERE publish = '1' AND language = '".getLanguage()."'
		ORDER BY sequence ";
		$result = $db->query( $query);

		$numof = $db->num_rows($result);
		$numof--;

		$i = 0;
		while(list($id_pages, $title) = sql_fetch_row($result)) {
			$li .= '<li'.( $i == $numof ? ' class="last"' : '' ).'><a href="index.php?modname=login&amp;op=readwebpages&amp;idPages='.$id_pages.'">'
				.$title.'</a></li>';
			$i++;
		}
		return ( $li != '' ? $ul.$li.'</ul>' : '' );
	}

	/**
	 * Return the complete code for the login page
	 * @return <string>
	 */
	public static function login_form() {

		$user_manager = new UserManager();
		$user_manager->_render->hideLoginLanguageSelection();
		$user_manager->setLoginStyle(false);

		$html = Form::openForm('login_confirm', Get::rel_path('lms').'/index.php?modname=login&amp;op=confirm')
			.$user_manager->getExtLoginMask(Get::rel_path('lms').'/index.php?modname=login&amp;op=login', '')
			.Form::closeForm();

		return $html;
	}

	/**
	 * Return the links for auto-register and lost password
	 * @return <html>
	 */
	public static function links() {

		$user_manager = new UserManager();
		$html = '<div id="link">';
		if($user_manager->_option->getOption('register_type') == 'self' || $user_manager->_option->getOption('register_type') == 'moderate') {

			$html .= '<a class="first" href="index.php?modname=login&amp;op=register">'.Lang::t('_REGISTER', 'login').'</a> ';
		}
		$html .= '<a href="index.php?modname=login&amp;op=lostpwd">'.Lang::t('_LOG_LOSTPWD', 'login').'</a>';
		$html .= '</div>';
		return $html;
	}

	/**
	 * The news link for the home pages
	 * @return <html>
	 */
	public static function news($hnumber = 2) {

		$html = '<div id="news">';

		$textQuery = "
		SELECT idNews, publish_date, title, short_desc
		FROM ".$GLOBALS['prefix_lms']."_news
		WHERE language = '".getLanguage()."'
		ORDER BY important DESC, publish_date DESC
		LIMIT 0,".Get::sett('visuNewsHomePage');

		//do query
		$result = sql_query($textQuery);
		if(sql_num_rows($hnumber)) $html .= '<p>'.Lang::set('_NO_CONTENT', 'login').'</p>';
		while( list($idNews, $publish_date, $title, $short_desc) = sql_fetch_row($result)) {

			$html .= '<h'.$hnumber.'>'
				.'<a href="index.php?modname=login&amp;op=readnews&amp;idNews='.$idNews.'">'.$title.'</a>'
				.'</h'.$hnumber.'>'
				.'<p class="news_textof">'
				.'<span class="news_data">'
					.Format::date($publish_date).' - </span>'
					.$short_desc
				.'</p>';
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * Service message for logout, and wrong password
	 * @return <html>
	 */
	public static function service_msg() {

		$html = '';
		if(isset($_GET['access_fail']) || isset($_GET['logout']) || isset($_GET['msg'])) {

			$html .= '<div id="service_msg">';
			if(isset($_GET['logout'])) {
				$html .= '<b class="logout">'.Lang::t('_UNLOGGED', 'login').'</b>';
			}
			if(isset($_GET['access_fail'])) {
				$html .= '<b class="login_failed">'.Lang::t('_NOACCESS', 'login').'</b>';
			}
			if(isset($_GET['msg'])) {
				$class ="login_failed";
				switch((int)$_GET['msg']) {
					case 101: { // Security issue, the request seem invalid ! (failed checkSignature)
						$msg =Lang::t('_INVALID_REQUEST', 'login');
					} break;
					case 102: { // Two user logged at the same time with the same username
						$msg =Lang::t('_TWO_USERS_LOGGED_WITH_SAME_USERNAME', 'login');
					} break;
				}
				$html .= '<b class="'.$class.'">'.$msg.'</b>';
			}
			$html .= '</div>';
		}
		return $html;
	}

	public function isSocialActive() {
		$res ='';

		$social =new Social();
		if($social->enabled()) {
			return true;
		}
		return false;
	}

	public function social_login() {
		$res ='';

		$social =new Social();
		if (!$social->enabled()) {
			// we don't show the box if there is nothing enabled..
			return $res;
		}

		$res.='<div id="social_login">';

		$res.= Form::openForm('social_form', Get::rel_path('lms').'/index.php?modname=login&amp;op=social')
			.'<span>'.Lang::t('_LOGIN_WITH', 'login').' </span>';
		
		if ($social->isActive('facebook')) {
			$social->includeFacebookLib();
			$facebook =$social->getFacebookObj();
			$_SESSION['fb_from']='login';
			$loginUrl = $facebook->getLoginUrl(array(
					/* 'req_perms'=>'email', */
					'next'=>Get::sett('url').'index.php?modname=login&op=facebook_login',
				));
			$res.='<a href="'.$loginUrl.'">'.Get::img('social/facebook-24.png').'</a>';
		}

		if ($social->isActive('twitter')) {
			$res.='<a href="index.php?modname=login&amp;op=twitter_login">'.Get::img('social/twitter-24.png').'</a>';
		}

		if ($social->isActive('linkedin')) {
			$res.='<a href="index.php?modname=login&amp;op=linkedin_login">'.Get::img('social/linkedin-24.png').'</a>';
		}

		if ($social->isActive('google')) {
			$res.='<a href="index.php?modname=login&amp;op=google_login">'.Get::img('social/google-24.png').'</a>';
		}

		$res.=Form::closeForm();
		
		$res.='</div>';
		return $res;
	}

}
