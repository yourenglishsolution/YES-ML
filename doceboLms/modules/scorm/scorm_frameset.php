<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */
/*
define("LMS", true);
define("IN_DOCEBO", true);
define("_deeppath_", '../../../');
//require(dirname(__FILE__).'/../base.php');

// start buffer
ob_start();

// initialize
require_once(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_TEMPLATE);
*/
if(Docebo::user()->isLoggedIn()) {

require_once(dirname(__FILE__) . '/config.scorm.php');
require_once(dirname(__FILE__) . '/scorm_utils.php');
require_once(dirname(__FILE__) . '/scorm_items_track.php');

$idReference 	= $GLOBALS['idReference']; 
$idResource 	= $GLOBALS['idResource'];
$back_url 		= $GLOBALS['back_url'];
$autoplay 		= $GLOBALS['autoplay'];
$playertemplate = $GLOBALS['playertemplate'];
$environment	= $GLOBALS['environment'];
if(!empty($GLOBALS['chapter'])) {
	$start_from_chapter = $GLOBALS['chapter'];
} else {
	$start_from_chapter = Get::req('start_from_chapter', DOTY_MIXED, false);
}

if($autoplay == '') $autoplay = '1';
if($playertemplate == '') $playertemplate = 'default';
if($environment == false) $environment = 'course_lo';

if($playertemplate != '') {
	if(!file_exists(getPathTemplate().'player_scorm/'.$playertemplate.'/def_style.css')) {
		$playertemplate = 'default';
	}
} else {
	$playertemplate = 'default';
}

$idscorm_organization = $idResource;
$idUser = (int)getLogUserId();

$sql = 'SELECT idCourse FROM '.$GLOBALS['prefix_lms'].'_organization WHERE idResource="'.$idResource.'"';
$row = sql_fetch_object(sql_query($sql));
$idCourse = $row->idCourse;

/*Start database connection***********************************************/

/* get scorm version */
$scormVersion = getScormVersion( 'idscorm_organization', $idscorm_organization);

/* get object title */
list($lo_title) = sql_fetch_row(sql_query(	"SELECT title"
												." FROM ".$GLOBALS['prefix_lms']."_organization"
											  	." WHERE idResource = '$idResource'" 
											  	."   AND objectType = 'scormorg'"));

$itemtrack = new Scorm_ItemsTrack(null, $GLOBALS['prefix_lms']);
$rsItemTrack = $itemtrack->getItemTrack($idUser,$idReference, NULL, $idscorm_organization);
if( $rsItemTrack === FALSE ) {
	// The first time for this user in this organization
	$itemtrack->createItemsTrack( $idUser, $idReference, $idscorm_organization );
	// Now should be present
	$rsItemTrack = $itemtrack->getItemTrack( $idUser, $idReference, NULL, $idscorm_organization );
}

$arrItemTrack = mysql_fetch_assoc( $rsItemTrack );
// with id_item_track of organization|user|reference create an entry in commontrack table
require_once( dirname(__FILE__) . '/../../class.module/track.object.php' );
require_once( dirname(__FILE__) . '/../../class.module/track.scorm.php' );
$track_so = new Track_ScormOrg( $arrItemTrack['idscorm_item_track'], false, false, NULL, $environment );
if( $track_so->idReference === NULL )
	$track_so->createTrack( $idReference, $arrItemTrack['idscorm_item_track'], $idUser, date("Y-m-d H:i:s"), 'ab-initio', 'scormorg' );


/* info on number of items and setting of variables for tree hide/show */
$nItem = $arrItemTrack['nDescendant'];
if(!empty($GLOBALS['chapter'])) {
	$isshow_tree = 'false';
	$class_extension = '_hiddentree';
} else {
	$isshow_tree = ($nItem > 1) ? 'true':'false';
	$class_extension = ($nItem > 1) ? '':'_hiddentree';
}

//$lms_base_url = Get::rel_path('lms').'/';
//$lms_base_url = preg_replace("/:\/\/([A-Za-z0-9_:.]+)\//","://".$_SERVER['HTTP_HOST']."/",Get::sett('url'));
$lms_base_url = preg_replace("/http[s]*:\/\/([A-Za-z0-9_:.]+)\//", 'http' . ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '' )."://".$_SERVER['HTTP_HOST']."/",Get::sett('url'));
$lms_base_url .= 'doceboLms/';
$lms_base_url = Get::rel_path('lms').'/';

$lms_base_url = 'http' . ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '' ).'://'.$_SERVER['HTTP_HOST']
	    	.( strlen(dirname($_SERVER['SCRIPT_NAME'])) != 1 ? dirname($_SERVER['SCRIPT_NAME']) : '' ).'/';
/*
 lms_url: 'http://localhost/docebo_36/doceboLms/modules/scorm/soaplms.php',
 lms_base_url: 'http://localhost/docebo_36/doceboLms/',
*/
$lms_url = $lms_base_url.$scormws;
$xmlTreeUrl = $lms_base_url.$scormxmltree.'?idscorm_organization='.$idscorm_organization.'&idReference='.$idReference.'&environment='.$environment;
$imagesPath = getPathImage().'treeview/';

header("Content-Type: text/html; charset=utf-8");

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN"'."\n";
echo '    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml">'."\n";
echo '<head>';
echo '	<title>'.$lo_title.'</title>';
echo '	<link href="'.Get::tmpl_path().'/style/lms-scormplayer.css" rel="stylesheet" type="text/css" />';

if(trim($playertemplate) != '') echo '	<link href="'.Get::tmpl_path().'/player_scorm/'.$playertemplate.'/def_style.css" rel="stylesheet" type="text/css" />';

	echo '<SCRIPT type="text/javascript" src="'.Get::rel_path('lms').'/modules/scorm/prototype.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.Get::rel_path('lms').'/modules/scorm/ScormTypes.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.Get::rel_path('lms').'/modules/scorm/ScormCache.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.Get::rel_path('lms').'/modules/scorm/ScormApi.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.Get::rel_path('lms').'/modules/scorm/player.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" src="'.Get::rel_path('lms').'/modules/scorm/StdPlayer.js"></SCRIPT>'."\n";
	echo '<SCRIPT type="text/javascript" >'."\n";
	echo '<!--'."\n";

	echo "var playerConfig = {\n";
	echo " autoplay: '$autoplay',\n";
	echo " backurl: '".str_replace('custom_enditem', 'custom_playitem', $back_url)."',\n";
	echo " xmlTreeUrl: '$xmlTreeUrl',\n";
	echo " host: '{$_SERVER['HTTP_HOST']}',\n";
	echo " lms_url: '$lms_url',\n";
	echo " lms_base_url: '$lms_base_url',\n";
	echo " scormserviceid: '$scormserviceid',\n";
	echo " scormVersion: '$scormVersion',\n";
	echo " idUser: '$idUser',\n";
	echo " idReference: '$idReference',\n";
	echo " idscorm_organization: '$idscorm_organization',\n";
	echo " imagesPath: '$imagesPath',\n";
	echo " idElemTree: 'treecontent',\n";
	echo " idElemSco: 'scormbody',\n";
	echo " idElemScoContent: 'scocontent',\n";	
	echo " idElemSeparator: 'separator',\n ";
	echo " showTree: '$isshow_tree',\n ";
	echo " playertemplate: '$playertemplate',\n";
	echo " auth_request: '".Util::getSignature()."',\n";

	echo " environment: '$environment',\n";
	echo " useWaitDialog: '". Get::sett('use_wait_dialog', "off") ."',\n";

	echo " startFromChapter: ".( $start_from_chapter ? "'".$start_from_chapter."'" : "false" )."\n";

	echo "};\n";
		
	echo 'window.onload = StdUIPlayer.initialize;'."\n";	
	echo ' // -->'."\n";
	echo '</SCRIPT>'."\n";
	
	
	echo '
		<script type="text/javascript" src=".././addons/yui/utilities/utilities.js"></script>
		<script type="text/javascript" src=".././addons/yui/json/json-min.js"></script>
		<script type="text/javascript" src=".././addons/yui/animation/animation-min.js"></script>
		<script type="text/javascript" src=".././addons/yui/logger/logger-min.js"></script>
		<script type="text/javascript">
		function opendico()
		{
			window.open("http://www.wordreference.com/enfr/"+document.getElementById("dico").value);
			return false;
		}
		</script>

		<link rel="stylesheet" type="text/css" href="../templates/standard/yui-skin/logger.css" />
		<style type="text/css">
			html {overflow-y: auto; overflow-x: hidden;}
			iframe {width: 100%; height: 100%; position: absolute; top: 0; left: 0; overflow: auto;}
	
			#iframe {width: 100%; height: 48px; position: relative; top: 0px; left: 0px; z-index: 20;}
				#iframe_c {width: 100%; height: 48px; background: url(../templates/yes/images/scorm/bg_iframe_c.png) 0 0 repeat-x; float: left; font: 20px \'Droid Sans\'; text-shadow: 0px 1px 0px #5881a6; color: white;}
					#iframe_c_l 		{float: left; line-height: 48px; margin-left: 43px;}
					#iframe_c_dico 	{float: left; line-height: 48px; height: 48px; margin-left: 100px; padding: 0 20px; background: transparent url(../templates/yes/images/scorm/bg_dico.png) 0 50% repeat-x;}
						#iframe_dico_l {float: left;}
							#lbl_dico		{text-shadow: none;}
							#dico				{width: 194px; height: 30px; padding: 0; border: 0; line-height: 35px;}	
						#btn_dico		{display: block; float: left; height: 24px; width: 76px; margin-top: 5px; margin-left: 5px;}
					#iframe_c_r 		{float: right; line-height: 48px; margin-right: 43px; font-size: 12px;}
		</style>';
	
echo '</head>'."\n";

echo '<body class="yui-skin-sam" id="page_head" class="'.$playertemplate.'" onunload="trackUnloadOnLms()">
	<div id="treecontent" class="treecontent'.$class_extension.' '.$playertemplate.'_menu" style="z-index: 4000;">
		<div class="menubox">Menu</div>
		<br />
	</div>
	<div id="separator" class="separator'.$class_extension.'" >
		<a id="sep_command" href="#" onclick="showhidetree();">
			<img src="'.$imagesPath.'../scorm/'.( ($nItem > 1) ? 'bt_sx' : 'bt_dx' ).'.png" alt="Expand/Collapse" />
		</a>
	</div>
	<div id="iframe">
		<div id="iframe_c">
			<div id="iframe_c_l">
				<span id="lvl">Microlearning '.Docebo::user()->getMainGroupLabel().'</span> | <span id="lssn_nb">Cours n°'.$idCourse.'</span>
			</div>
			<div id="iframe_c_dico">
				<div id="iframe_dico_l">
					<label for="dico" id="lbl_dico">Aa</label>
					<input type="text" id="dico" />
				</div>
				<a id="btn_dico" onclick="javascript:opendico();"><img src="../templates/yes/images/scorm/btn_ok.png" alt="" /></a>
			</div>
			<div id="iframe_c_r">
				Copyright © 2011 YES SAS All rights reserved.
			</div>
		</div>
	</div>
	<div id="scocontent" class="scocontent'.$class_extension.'">
		<iframe id="scormbody" name="scormbody" frameborder="0" marginwidth="0" marginheight="0" framespacing="0" width="100%" height="100%">
		</iframe>
	</div>
	<div id="log_reader" style="position:absolute;background:#fff;"></div>';

/*
echo '<script type="text/javascript">
	var yl_debug = true;
	var yl_reset_timeout;
	if (!yl_debug) {
		var yl_debug =false;
	}
	// Put a LogReader on your page
	yuiLogReader = new YAHOO.widget.LogReader("log_reader", {
		verboseOutput:false,
		top:\'2px\',
		right:\'2px\',
		width:\'80%\',
		height:\'500px\',
		footerEnabled: false
	});
	yuiLogReader.collapse();
	//yuiLogReader.hide();

	function yuiLogAutoReset() {
		yuiLogReader.show();
		yuiLogReader.expand();
		//clearTimeout(yl_reset_timeout);
		//yl_reset_timeout =setTimeout(\'yuiLogReader.collapse(); yuiLogReader.hide(); yuiLogReader.clearConsole();\', 30000);
	}
	function yuiLogMsg(msg, type) {
		if (!yl_debug) { return false; }
		if (yuiLogReader.isCollapsed) {
			//yuiLogAutoReset();
		}
		if (type == \'\') {
			type = \'info\';
		}
		YAHOO.log(msg, type);
	}
</script>';
*/

echo '</body>
</html>';



ob_end_flush();
exit;	// to avoid index.php to add additional and unuseful html
} 
?>
