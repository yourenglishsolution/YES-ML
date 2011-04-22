<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

require_once(_i18n_.'/lib.lang.php');

function canEditLang($lang_code) {
	
	if(Docebo::user()->getUserLevelId() == ADMIN_GROUP_GODADMIN) return true;

	require_once(_base_.'/lib/lib.preference.php');
	$adminManager = new AdminPreference();

	$admin_preference = $adminManager->getAdminRules(Docebo::user()->getIdSt());

	if(array_search($lang_code, $admin_preference[_RULES_LANG]) !== false) return true;
	return false;
}

function lang_lang() {
	checkPerm('view', false, 'lang', 'framework');
	
	global $globLangManager, $visuItem;

	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	
	$newPerm = true;
	$modPerm = true;
	$remPerm = true;

	require_once(_base_."/lib/lib.table.php");
	
	$arrLang = Docebo::langManager()->getAllLanguages();
	sort($arrLang);
	$stats = Docebo::langManager()->getLangStat();
	
	//getTitleArea(testo, nomeimmagine , alt immagine)
	//loadAdminTitleArea('lang');
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">', 'content');
	$tableLang = new Table( $visuItem );
	
	$tableLang->initNavBar('ini');
	$tableLang->setLink('index.php?modname=lang&amp;op=lang');
	$ini = $tableLang->getSelectedElement();
	
	
	//$GLOBALS['page']->add($tableLang->OpenTable( '' ), 'content');
	
	$img_up = '<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def( '_UP' ).'"/>';
	$img_down = '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def( '_DOWN' ).'"/>';
	
	$contentH = array( 	$lang->def( '_LANGUAGE' ), 
						$lang->def( '_DESCRIPTION' ),
						$lang->def( '_ORIENTATION' ),
						'<img src="'.getPathImage().'standard/modelem.png" alt="'.$lang->def( '_TRANSLATELANG' ).'" title="'.$lang->def( '_TRANSLATELANGG' ).'" />');
	$typeH = array( '', '', '', 'image');
	
	if($modPerm) {
		$contentH[] = '<img src="'.getPathImage().'standard/edit.png" alt="'.$lang->def( '_SAVE' ).'" title="'.$lang->def( '_SAVE' ).'" />';
		$typeH[] = 'image';
	}
	if($remPerm) {
		$contentH[] = '<img src="'.getPathImage().'standard/delete.png" alt="'.$lang->def( '_DEL' ).'" title="'.$lang->def( '_DEL' ).'" />';
		$typeH[] = 'image';
	}
	/*
	$contentH[] = '<img src="'.getPathImage().'standard/export.gif" alt="'.$lang->def( '_EXPORT' ).'" title="'.$lang->def( '_EXPORTG' ).'"/>';
	$typeH[] = 'img';
	*/
	
	$contentH[] = $lang->def( '_PERC_TRAD' );
	$typeH[] = '';
	
	$contentH[] = $lang->def( '_NUM_TRAD' );
	$typeH[] = 'nowrap';
	
	$tableLang->addHead($contentH, $typeH);

	$maxItem = count($arrLang);
	$GLOBALS['page']->add( "<!-- beee: ".count($arrLang).", $maxItem, $ini, $visuItem -->", 'content');
	for( $index = $ini; $index < $maxItem; $index++ ) {
		$rowContent = $arrLang[$index];
		$rowContent[] = '<a href="index.php?modname=lang&amp;op=translator&amp;tranm='
						.str_replace(' ', '_', $rowContent[0]).'&amp;modulef=FF">'
						.'<img src="'.getPathImage().'standard/modelem.png" alt="'
						.$lang->def( '_TRANSLATELANG' ).'" /></a>';
		if($modPerm) {
			if(canEditLang($arrLang[$index][0])) {
			
				$rowContent[] = '<a href="index.php?modname=lang&amp;op=modlang&amp;lang_code='.str_replace(' ', '_', $rowContent[0]).'">'
								.'<img src="'.getPathImage().'standard/edit.png" alt="'.$lang->def( '_SAVE' ).'" /></a>';
			} else $rowContent[] = '';
		}
		if($remPerm) {
			if(canEditLang($arrLang[$index][0])) {
				$_title = $lang->def('_DEL').' : '.$arrLang[$index][0];
				$rowContent[] = '<a href="index.php?modname=lang&amp;op=dellang&amp;lang_code='.str_replace(' ', '_', $rowContent[0]).'">
							<img src="'.getPathImage().'standard/delete.png" alt="'.$lang->def( '_DEL' ).'" 
							title="'.$_title.'" /></a>';
			} else $rowContent[] = '';
		}
		/*
		$rowContent[] = '<a href="index.php?modname=lang&amp;op=exportXML&amp;lang_code='.str_replace(' ', '_', $rowContent[0]).'">
						<img src="'.getPathImage().'standard/export.gif" alt="'.$lang->def( '_EXPORT' ).'" /></a>';
		*/
		if(!isset($stats[$arrLang[$index][0]])) {			

			$rowContent[] = Util::draw_progress_bar(0, true, FALSE, FALSE,FALSE, FALSE);
			$rowContent[] = '0';

		} else {

			$percent = (int)(($stats[$arrLang[$index][0]] / $stats['tot_lang'])*100);
			$rowContent[] = Util::draw_progress_bar(	$percent, true, FALSE, FALSE,FALSE, FALSE);
			$rowContent[] = $stats[$arrLang[$index][0]].'/'.$stats['tot_lang'];
		}
		
		$tableLang->addBody($rowContent);
	}

	if($newPerm) {
		$tableLang->addActionAdd('<a href="index.php?modname=lang&amp;op=addlang">'
			.'<img src="'.getPathImage().'standard/add.png" title="'.$lang->def( '_ADD' ).'" alt="'.$lang->def( '_ADD' ).'" /> '.$lang->def( '_ADD' ).'</a>');
	}
	
	if($remPerm) {
		//add delete confirm popup
		require_once(_base_.'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=dellang]');
	}
	
	//$GLOBALS['page']->add($tableLang->CloseTable(),'content');
	
	$GLOBALS['page']->add(
		$tableLang->getTable()
		.'</div>'
	, 'content' );
}

function lang_editlang( $lang_code = FALSE ) {
	checkPerm('view', false, 'lang', 'framework');
	
	global $globLangManager;
	require_once(_base_. "/lib/lib.form.php");
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	
	if( $lang_code === FALSE ) {
		
		$isInsert = TRUE;
		$lang_code = "";
		$lang_description = "";
		$lang_charset = "";
		$lang_browsercode = "";
		$lang_direction = 'ltr';
	} else {
		
		$isInsert = FALSE;
		$lang_description = $globLangManager->getLanguageDescription($lang_code);
		$lang_charset = $globLangManager->getLanguageCharset($lang_code);
		$lang_browsercode = $globLangManager->getLanguageBrowsercode($lang_code);
		$lang_direction = $globLangManager->getLanguageDirection($lang_code);
	}

	//loadAdminTitleArea('lang');
	$GLOBALS['page']->add( getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">', 'content' );
	$GLOBALS['page']->add( getBackUi( "index.php?modname=lang&amp;op=lang", $lang->def( '_BACK' ) ), 'content');
	
	$GLOBALS['page']->add( 
		Form::getFormHeader($lang->def("_LANG_INFO"))
		.Form::openForm("addlangform", "index.php?modname=lang&amp;op=inslang"), 'content');
	$GLOBALS['page']->add( Form::openElementSpace(), 'content');
	
	//$GLOBALS['page']->add( '<span class="fontRed">*</span>', content );
	if($isInsert) {
		$GLOBALS['page']->add( Form::getTextfield( $lang->def( '_LANGUAGE' ), "lang_code", "lang_code", 50, $lang_code) , 'content' );
	} else {
		$GLOBALS['page']->add(
			Form::getHidden('lang_code', 'lang_code', $lang_code)
			.Form::getLineBox($lang->def( '_LANGUAGE' ), $lang_code) , 'content' );
	}
	$GLOBALS['page']->add( Form::getSimpleTextarea( $lang->def( '_DESCRIPTION' ), 
							'lang_description', 
							'lang_description',
							$lang_description),
							'content');
	$GLOBALS['page']->add( Form::getTextfield( $lang->def( '_LANG_CHARSET' ), "lang_charset", "lang_charset", 50, $lang_charset) , 'content' );
	$GLOBALS['page']->add( Form::getTextfield( $lang->def( '_LANG_BROWSERCODE' ), "lang_browsercode", "lang_browsercode", 50, $lang_browsercode) , 'content' );
	
	$GLOBALS['page']->add( Form::getRadioSet( $lang->def( '_LANG_DIRECTION' ), 
								"lang_direction", 
								"lang_direction", 
								array($lang->def('_DIRECTION_LTR') => 'ltr',
									$lang->def('_DIRECTION_RTL') => 'rtl'), 
								( $lang_direction ? $lang_direction : 'ltr' ) 
							) , 'content' );
	
	$GLOBALS['page']->add( Form::closeElementSpace(), 'content' );

	$GLOBALS['page']->add( Form::openButtonSpace(), 'content' );
	$GLOBALS['page']->add( Form::getButton(	"editlangsave",
											"editlangsave",
											($isInsert?$lang->def( '_INSERT' ):$lang->def( '_SAVE' ))), 
							'content' );
	$GLOBALS['page']->add( Form::getButton("editkeycancel","editkeycancel",$lang->def( '_CANCEL' )), 'content' );
	$GLOBALS['page']->add( Form::closeButtonSpace(), 'content' );
	$GLOBALS['page']->add( Form::closeForm(), 'content' );
	$GLOBALS['page']->add( '</div>', 'content');

}

function lang_uplang() {
	checkPerm('view', false, 'lang', 'framework');
	
	global $globLangManager;
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	
	if(isset($_POST['editkeycancel'])) Util::jump_to( 'index.php?modname=lang&op=lang' );
	
	$lang_code = str_replace('_', ' ', $_POST['lang_code']);
	if( trim($lang_code) == "" ) {
		$GLOBALS['page']->add( getErrorUi( $lang->def( '_BLANKLANG_CODE' )), 'content' );
		return;
	}
	
	if( $globLangManager->existLanguage($lang_code) ) {
		
		if( !$globLangManager->updateLanguage(	$lang_code, 
												$_POST['lang_description'],
												$_POST['lang_charset'],
												$_POST['lang_browsercode'],
												$_POST['lang_direction']) )
			$GLOBALS['page']->add( getErrorUi( $lang->def( '_OPERATION_FAILURE' ) ), 'content' );
	} else {
		
		if( !$globLangManager->insertLanguage(	$lang_code, 
												$_POST['lang_description'],
												$_POST['lang_charset'],
												$_POST['lang_browsercode'],
												$_POST['lang_direction']) )
			$GLOBALS['page']->add( getErrorUi( $lang->def( '_OPERATION_FAILURE' ) ), 'content' );
	}
	Util::jump_to( 'index.php?modname=lang&op=lang' );
}

function lang_dellang() {
	checkPerm('view', false, 'lang', 'framework');
	
	global $globLangManager;
	
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	$lang_code = str_replace('_', ' ', $_GET['lang_code']);
		
	if( isset($_GET['confirm']) && ($_GET['confirm'] == '1')) {
		//delete lang
		if(!$globLangManager->deleteLanguage($lang_code)) {
			$GLOBALS['page']->add( getErrorUi( $lang->def( '_OPERATION_FAILURE' ) ), 'content' );
			return;
		}
		Util::jump_to( 'index.php?modname=lang&op=lang' );
	}
	else {
		// loadAdminTitleArea('lang');
		
		$GLOBALS['page']->add( getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">'
			.'<div class="boxinfo_title">'.$lang->def( '_AREYOUSURE' ).'</div>'
			.'<div class="boxinfo_container">'
			.'<span class="text_bold">'.$lang_code.'</span> [ '
				.$globLangManager->getLanguageDescription($lang_code).']'
			.'</div>', 'content');
		$GLOBALS['page']->add( '<div class="del_container">'
			.'<a href="index.php?modname=lang&amp;op=dellang&amp;lang_code='.$lang_code.'&amp;confirm=1">'
				.'<img src="'.getPathImage().'standard/delete.png" alt="'.$lang->def( '_CONFIRM' ).'" />'
				.$lang->def( '_CONFIRM' ).'</a>&nbsp;&nbsp;'
			.'<a href="index.php?modname=lang&amp;op=lang">'
				.'<img src="'.getPathImage().'standard/cancel.png" alt="'.$lang->def( '_UNDO' ).'" />'
				.$lang->def( '_UNDO' ).'</a>'
			.'</div>', 'content');
	
		$GLOBALS['page']->add( '</div>', 'content');
	}
	
}

/* IDEA: function lang_translator */
function lang_translator() {
	checkPerm('view', false, 'lang', 'framework');
	
	/*  param:
				platformf,modulef, key, tranm, tranc, full
	*/
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	
	global $globLangManager;
	// loadAdminTitleArea('lang');

	$platformf			= importVar('platformf',FALSE, 	Get::cur_plat() );
	$modulef			= importVar('modulef',	FALSE, 	'' );
	$key				= importVar('key', 		FALSE, 	'' );
	$tranm				= str_replace('_', ' ', importVar('tranm', 	FALSE, 	'' ) );
	$tranc				= importVar('tranc', 	FALSE, 	'' );
	$full				= importVar('full', 	TRUE, 	'' );
	$onlyempty			= importVar('onlyempty',FALSE, 	'' );
	$trans_contains		= importVar('trans_contains',FALSE, 	'' );
	$order_by			= importVar('order_by', FALSE, 	'' );
	
	if( $tranm != '' & isset($_POST['saveall']) ) {
		foreach($_POST as $post_key => $post_val) {
			if( strncmp($post_key,'mk_',3) == 0 ) {
				$globLangManager->updateTranslationC(substr($post_key,3),$post_val,$tranm);
			}
		}
	}
	if( isset($_POST['editkeysave']) ) {
		if( strlen(trim($_POST['newmodule'])) > 0 )
			$modulef = $_POST['newmodule'];
		else
			$modulef = $_POST['modulef'];
			
		$arr_attributes = array();
		if( isset( $_POST['accessibility'] ) )
			$arr_attributes[] = 'accessibility';
		if( isset( $_POST['sms'] ) )
			$arr_attributes[] = 'sms';
		if( isset( $_POST['email'] ) )
			$arr_attributes[] = 'email';
		//print_r( $arr_attributes );
		
		$globLangManager->updateKey($_POST['newkey'], 
									$modulef, 
									$_POST['platformf'], 
									$_POST['description'],
									join( ',', $arr_attributes));
		foreach($_POST as $post_key => $post_val) {
			if( strncmp($post_key,'lc_',3) == 0 ) {
				if(canEditLang(substr($post_key,3))){
					$globLangManager->updateTranslation($_POST['newkey'], 
														$modulef,
														$_POST['platformf'],
														$post_val,
														str_replace('_', ' ', substr($post_key,3) )
														);
				}
			}
		}
	}
	if( isset($_POST['editkeydel']) ) {
		$globLangManager->deleteKey($_POST['keytodel'], $_POST['modulef'],$_POST['platformf']);
	}
	
	foreach($_POST as $post_key => $post_val) {
		if( strncmp( $post_key, 'editkey_', 8 ) == 0 ) {
			$composed_key = substr($post_key,8);
			$other_filter = array();
			if(isset($_POST['filter_attributes']['accessibility'])) $other_filter['filter_attributes[accessibility]'] = $_POST['filter_attributes']['accessibility'];
			if(isset($_POST['filter_attributes']['sms'])) $other_filter['filter_attributes[sms]'] = $_POST['filter_attributes']['sms'];
			if(isset($_POST['filter_attributes']['email'])) $other_filter['filter_attributes[email]'] = $_POST['filter_attributes']['email'];
			
			
			list($key,$modulef,$platformf) = DoceboLangManager::decomposeKey( $composed_key );
			lang_edit_key($key, $modulef, $platformf, $tranm, $tranc, $onlyempty, $other_filter, $order_by);
			return;
		} elseif ( strncmp( $post_key, 'delkey_', 7 ) == 0 ) {
			$composed_key = substr($post_key,7);
			list($key,$modulef,$platformf) = DoceboLangManager::decomposeKey( $composed_key );
			lang_del_key($key, $modulef, $platformf, $tranm, $tranc, $onlyempty, $trans_contains);
			return;			
		}
	}
	
	if( isset($_POST['addkey']) ) {
		lang_edit_key('', $modulef, $platformf, $tranm, $tranc, $onlyempty, $trans_contains, $order_by);
	} elseif( isset($_GET['fastadd']) ) {
		lang_edit_key($key, $modulef, $platformf, $tranm, $tranc, $onlyempty, $trans_contains, $order_by);
	} elseif( $key != '' ) {
		if( $modulef == '' ) {
			$GLOBALS['page']->add( getErrorUi( $lang->def( '_OPERATION_FAILURE' ) ), 'content' );
			return;			
		}
		lang_translator_key($key, $modulef, $platformf);
	} elseif( $full == '1' ) {
		lang_translator_full();
	} else {
		if( $tranm === FALSE ) {
			$GLOBALS['page']->add( getErrorUi( $lang->def( '_OPERATION_FAILURE' ) ), 'content' );
			return;			
		}
		lang_translator_listKey($modulef, $platformf, $tranm, $tranc, $onlyempty, $trans_contains, $order_by);
	}
}

function lang_translator_listKey($modulef, $platformf, $tranm, $tranc, $onlyempty, $trans_contains, $order_by = false) {
	checkPerm('view', false, 'lang', 'framework');
	
	/*
		table display
		y: module+key
		x: [module], key, translation_lang_modify, [translation_lang_compare], list_shared
	*/
	
	require_once(_base_.'/lib/lib.table.php');
	require_once(_base_.'/lib/lib.form.php');
	require_once(_base_.'/lib/lib.platform.php');
	
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	$can_edit = canEditLang($tranm);
	
	$GLOBALS['page']->add( 
		getTitleArea(array('index.php?modname=lang&amp;op=lang' => $lang->def('_LANGUAGE_MANAGMENT'),
						$tranm), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">'
		.Form::openForm("langtranslator", "index.php?modname=lang&amp;op=translator&amp;tranm=".str_replace(' ', '_', $tranm))
		.getBackUi('index.php?modname=lang&amp;op=lang', Lang::t('_BACK', 'standard'))
	, 'content');
	
	$plt_man =& PlatformManager::createInstance();
	$arrPlatform =& $plt_man->getPlatformList();
	
	$arrModules = Docebo::langManager()->getAllModules($platformf);
	$all_module = array();
	if($modulef == 'FF') $modulef = $arrModules[0];
	$all_module[''] = $lang->def( '_LANG_ALLMODULES' );
	foreach($arrModules as $opt_module) $all_module[$opt_module] = $opt_module; 
	
	$arrLanguages = Docebo::langManager()->getAllLanguages();
	$all_value = array( '' => $lang->def( '_LANG_COMPARE_NONE') );
	foreach($arrLanguages as $opt_lang_comp) $all_value[$opt_lang_comp[0]] = $opt_lang_comp[0]; 
	
	if($tranc != '') {
		$browser_code = Docebo::langManager()->getLanguageBrowsercode($tranc);
		$pos = strpos($browser_code, ';');
		if($pos !== false) $browser_code = substr($browser_code, 0, $pos);
	}
	if($onlyempty == 1) $trans_contains = '';
	if($trans_contains != '') $tranc = $tranm;
	
	// show filter ------------------------------------------------------------------
	$GLOBALS['page']->add(
		Form::getHidden('order_by', 'order_by', $order_by)
		.Form::getDropdown( $lang->def( '_LANG_PLATFORM' ), "platformf", "platformf", $arrPlatform, $platformf )
		.Form::getDropdown($lang->def( '_LANG_MODULE' ), "modulef", "modulef", $all_module, $modulef)
		.Form::getDropdown($lang->def( '_LANG_COMPARE' ), "tranc", "tranc", $all_value, $tranc)
		.Form::getTextfield($lang->def('_TRANSLATION_COTAINS'), 'trans_contains', 'trans_contains', 255, 
			( ($onlyempty == '1') ? '' : $trans_contains ) )
		
		// attributes -----------------------------------------------------------------
		.Form::getOpenCombo($lang->def( '_LANG_SPECIAL' ))
		.Form::getCheckbox($lang->def('_ONLY_EMPTY'), 'onlyempty', 'onlyempty', '1', ($onlyempty == '1') )
		.Form::getCheckbox( $lang->def( '_LANG_ACCESSIBILITY' ), 
										'filter_attributes_accessibility', 
										'filter_attributes[accessibility]', 
										'accessibility' , 
										isset($_POST['filter_attributes']['accessibility']),
										'')
		.Form::getCheckbox( $lang->def( '_LANG_SMS' ), 
										'filter_attributes_sms', 
										'filter_attributes[sms]', 
										'sms' , 
										isset($_POST['filter_attributes']['sms']),
										'')
		.Form::getCheckbox( $lang->def( '_EMAIL' ), 
										'filter_attributes_email', 
										'filter_attributes[email]', 
										'email' , 
										isset($_POST['filter_attributes']['email']),
										'')
		.Form::getCloseCombo()
		.Form::openButtonSpace()
		.Form::getButton( "applyfilter", "applyfilter", $lang->def( '_LANG_APPLY' ) )
		.Form::closeButtonSpace()
		.'<br />'
	, 'content');
	
	
	// script for dynamic modules dropdown filter
	$GLOBALS['page']->add( '<script type="text/javascript">'."\n", 'page_head');
	$GLOBALS['page']->add( 'var strAllModules = "'.$lang->def( '_LANG_ALLMODULES' ).'";', 'page_head' );
	
	$GLOBALS['page']->add( 'var arrFrameworkModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('framework')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrLmsModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('lms')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrCmsModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('cms')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrScsModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('scs')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrEcomModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('ecom')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrCrmModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('crm')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	
	
	$GLOBALS['page']->add( 
	'window.onload = function(){
		var fieldPlatform = document.getElementById("platformf");
		fieldPlatform.onchange = filterModules;
	}'."\n", 'page_head');
	
	$GLOBALS['page']->add( 
	'function filterModules() {
		var fieldPlatform = document.getElementById("platformf");
		var fieldModules = document.getElementById("modulef");
		// delete all
		for( var i = fieldModules.options.length-1; i >= 0 ; i-- ) {
			fieldModules.options[i] = null;
		}
		var optModule = new Option(strAllModules,"",true,true);
		fieldModules.options[0] = optModule;
		for( i = 0; i < fieldPlatform.options.length; i++ ) {
			if( fieldPlatform.options[i].selected ) {
				var platform = fieldPlatform.options[i].value;
				if( platform == "framework" ) {
					
					reinitModules(arrFrameworkModules, platform);
				} else if( platform == "lms" ) {
					
					reinitModules(arrLmsModules, platform);
				} else if( platform == "cms" ) {
					
					reinitModules(arrCmsModules, platform);
				} else if( platform == "scs" ) {
					
					reinitModules(arrScsModules, platform);
				} else if( platform == "ecom" ) {
					
					reinitModules(arrEcomModules, platform);
				} else if( platform == "crm" ) {
					
					reinitModules(arrCrmModules, platform);
				} 
			}
		}
	}'."\n", 'page_head');
	
	$GLOBALS['page']->add( 
	'function reinitModules(arrModules, platform) {
		var fieldModules = document.getElementById("modulef");
		// delete all
		//for( var i = fieldModules.options.length-1; i >= 0 ; i-- ) {
		//	fieldModules.options[i] = null;
		//}
		// populate
		var optModule = null;
		for( var i = 0; i < arrModules.length ; i++ ) {
			optModule = new Option(arrModules[i],arrModules[i]);
			fieldModules.options[fieldModules.length] = optModule;
		}
	}'."\n", 'page_head');
	
	$GLOBALS['page']->add( '</script>', 'page_head');
	
	
	//Util::get_css(Get::tmpl_path('base').'yui-skin/datatable.css', true, true);
	
	// show all the key selected -----------------------------------------------------------
	
	$url = 'index.php?modname=lang&amp;op=translator&amp;tranm='.str_replace(' ', '_', $tranm).'&amp;platformf='.$platformf.'&amp;modulef='.$modulef
		.'&amp;tranm='.str_replace(' ', '_', $tranm).'&amp;tranc='.$tranc.'&amp;order_by';
	$order_symbol = '<img src="'.getPathImage('fw').'standard/down.gif" alt="'.$lang->def('_ORDER_BY').'" /> ';
	
	//$tableLang = new Table(0);
	//$GLOBALS['page']->add( '<div class="yui-dt">', 'content');
	//$GLOBALS['page']->add( $tableLang->OpenTable( preg_replace('/%module%/', $modulef, $lang->def( '_TRANSALTIONTABLEFOR' )) ), 'content');
	$tableLang = new Table(0/*, preg_replace('/%module%/', $modulef, $lang->def( '_TRANSALTIONTABLEFOR' ))*/);

	$contentH = array(
		$lang->def( '_LANG_MODULE' ),
		$lang->def( '_LANG_KEY' ), 
		$lang->def( '_LANG_ATTRIBUTES' ),
		$lang->def( '_LANG_TRANSLATION' ).': <span xml:lang="en">'.$tranm.'</span>' 
	);
	
	$typeH = array( '', '', 'img-cellp', 'text_translation');
	if($tranc != '') {
		
		$contentH[] = $lang->def( '_LANG_TRAN_COMPARE' ).': <span xml:lang="en">'.$tranc.'</span>';
		$typeH[] = '';
	}
	$contentH[] = '<img src="'.getPathImage().'standard/edit.png" alt="'.$lang->def( '_TRANSLATELANG' ).'" title="'.$lang->def( '_TRANSLATELANGG' ).'" />';
	$typeH[] = 'img-cell';
	if($can_edit) {
		$contentH[] = '<img src="'.getPathImage().'standard/delete.png" alt="'.$lang->def( '_DEL' ).'" title="'.$lang->def( '_DEL' ).'" />';
		$typeH[] = 'img-cell';
	}
	//$GLOBALS['page']->add( $tableLang->WriteHeader($contentH, $typeH), 'content' );
	$tableLang->addHead($contentH, $typeH);
	
	$module_param = ($modulef == '') ? FALSE : $modulef;
	$attributes = isset($_POST['filter_attributes']) ? $_POST['filter_attributes'] : array() ;
	
	$arrTranslations = Docebo::langManager()->getModuleLangTranslations($platformf,$module_param,$tranm, $trans_contains, $attributes, $order_by );
	if( $tranc != '' ) {
		$arrCompare = Docebo::langManager()->getModuleLangTranslations($platformf,$module_param,$tranc, $trans_contains, $attributes, 'translation');
	}
	$t = $trans_contains != '';
	$e = $onlyempty == '1';
	
	if($order_by != 'tranc') $primary =& $arrTranslations;
	else $primary =& $arrCompare;
	
	while(list($elem_key, $elem) = each($primary)) {
		
		$elem_translation =& $arrTranslations[$elem_key];
		
		$r = $elem_translation[2] != '';
		
		/*           00 | 01 | 11 | 10
		 *  r\te    -------------------
		 *        0 | 1 |  1 |    |    |
		 *          -------------------
		 *        1 | 1 |    |    |  1 |
		 *          -------------------
		 *		f = ^r ^t + r ^e
		 */
		if( ( $r && !$e ) || ( !$r && !$t )) {
			$composed_key = DoceboLangManager::composeKey($elem_translation[1],$elem_translation[0],$platformf);
			$rowContent = array();
			$rowContent[] = $elem_translation[0];
			$rowContent[] = '<label for="mk_'.$composed_key.'">'.$elem_translation[1].'</label>';
			$arr_attributes = split( ',', $elem_translation[3]);
			$str_attributes = "";
			if( in_array( 'accessibility', $arr_attributes ) )
				$str_attributes = '<img src="'.getPathImage().'standard/accessibility.gif"'
								.' alt="'.$lang->def( '_LANG_ACCESSIBILITY' ).'"'
								.' title="'.$lang->def( '_LANG_ACCESSIBILITY' ).'" >';
			if( in_array( 'sms', $arr_attributes ) )
				$str_attributes .= '<img src="'.getPathImage().'standard/sms.gif"'
								.' alt="'.$lang->def( '_LANG_SMS' ).'"'
								.' title="'.$lang->def( '_LANG_SMSTITLE' ).'" >';
			if( in_array( 'email', $arr_attributes ) )
				$str_attributes .= '<img src="'.getPathImage().'standard/email.gif"'
								.' alt="'.$lang->def( '_EMAIL' ).'"'
								.' title="'.$lang->def( '_LANG_EMAILTITLE' ).'" >';
			$rowContent[] = $str_attributes;
			
			$display = ( isset($_POST['mk_'.$composed_key]) ? stripslashes($_POST['mk_'.$composed_key]) : $elem_translation[2]);
			if($can_edit) {
				
				$rowContent[] = Form::getInputTextarea('mk_'.$composed_key,
														'mk_'.$composed_key,
														$display,
														'textarea_wh_full',
														6 );
			} else {
				
				$rowContent[] = $display;
			}
			if( $tranc != '' ) {
				
				$translation = '<span xml:lang="'.$browser_code.'">';
				if($trans_contains != '') $translation .= eregi_replace($trans_contains, '<em class="filter_evidence">'.$trans_contains.'</em>', $arrCompare[$elem_key][2]);
				else $translation .= $arrCompare[$elem_key][2];
				$rowContent[] = $translation.'</span>';
			}
			$rowContent[] = /*'<input type="submit"
								class="graphic_submit22"
								style="background-image: url( \''.getPathImage().'standard/edit.png\' )"
								id="editkey_'.$composed_key.'" 
								name="editkey_'.$composed_key.'" 
								value="" 
								alt="edti key"/>';*/
			'<input type="image" id="editKey_'.$composed_key.'" src="'.getPathImage().'standard/edit.png" '
			.'name="editkey_'.$composed_key.'" value="" alt="'.Lang::t('_MOD', 'standard').'" title="'.Lang::t('_MOD', 'standard').'" />';
			if($can_edit) {
					
				$rowContent[] = /*'<input type="submit"
									class="graphic_submit22"
									style="background-image: url( \''.getPathImage().'standard/delete.png\' )"
									id="delkey_'.$composed_key.'" 
									name="delkey_'.$composed_key.'" 
									value="" 
									alt="edti key"/>';*/
				'<input type="image" id="delKey_'.$composed_key.'" src="'.getPathImage().'standard/delete.png" '
					.'name="delkey_'.$composed_key.'" value="" alt="'.Lang::t('_DEL', 'standard').'" title="'.Lang::t('_DEL', 'standard').'" />';
			}			
			//$GLOBALS['page']->add( $tableLang->WriteRow($rowContent), 'content');
			$tableLang->addBody($rowContent);
		}
	}
	/*$GLOBALS['page']->add( $tableLang->WriteAddRow(
								Form::getButton( "addkey", "addkey", $lang->def( '_LANG_ADDKEY' ), 'transparent_add_button' )
							), 'content' );*/
	$tableLang->addActionAdd(Form::getButton( "addkey", "addkey", $lang->def( '_LANG_ADDKEY' ), 'yui-button', '', false ));
	$GLOBALS['page']->add($tableLang->getTable(), 'content');
	//$GLOBALS['page']->add( $tableLang->CloseTable(), 'content' );
	//$GLOBALS['page']->add( '</div>', 'content' ); //close yui-dt block

	$GLOBALS['page']->add( Form::openButtonSpace()
							.Form::getButton( "saveall", "saveall", $lang->def( '_SAVE' ) )
							.Form::getButton( "undo", "undo", $lang->def( '_UNDO' ) )
							.Form::closeButtonSpace(), 'content' );
	$GLOBALS['page']->add( 
		getBackUi('index.php?modname=lang&amp;op=lang', $lang->def('_BACK'))
		.Form::closeForm()
		.'</div>', 'content');
}

function lang_edit_key($key, $modulef, $platformf, $tranm, $tranc, $onlyempty, $other_filter = false, $order_by = '') {
	checkPerm('view', false, 'lang', 'framework');
	
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	require_once(_base_.'/lib/lib.form.php');
	
	$GLOBALS['page']->add( 
		getTitleArea(array('index.php?modname=lang&amp;amp;op=lang' => $lang->def('_LANGUAGE_MANAGMENT'),
			'index.php?modname=lang&amp;op=translator&amp;tranm='.str_replace(' ', '_', $tranm).'&amp;platformf='.$platformf.'&amp;modulef='.$modulef.'&amp;tranm='.str_replace(' ', '_', $tranm).'&amp;tranc='.$tranc => $tranm,
			$key), 'manlanguage')
	, 'content');
	
	$GLOBALS['page']->add('<div class="std_block">', 'content');
	$GLOBALS['page']->add( Form::getFormHeader( $lang->def( '_LANG_ADDKEY' ) ), 'content' );
	$GLOBALS['page']->add( Form::openForm( 	'langnewkey', 
							'index.php?modname=lang&amp;op=translator'
							.'&amp;modulef='.$modulef
							.'&amp;tranm='.str_replace(' ', '_', $tranm)
							.'&amp;tranc='.$tranc ), 'content' );
	$GLOBALS['page']->add( Form::openElementSpace(), 'content' );
	
	$GLOBALS['page']->add( Form::getOpenFieldset( $lang->def( '_LANG_KEY' ).' - '.$lang->def( '_LANG_MODULE' ) ), 'content' );
	
	$GLOBALS['page']->add( Form::getTextfield( $lang->def( '_LANG_KEY' ), "newkey", "newkey", 50, $key), 'content' );
	$GLOBALS['page']->add( Form::getHidden('platformf', 'platformf', $platformf), 'content' );
	$GLOBALS['page']->add( Form::getHidden('modulef_h', 'modulef', $modulef), 'content' );
	$GLOBALS['page']->add( Form::getHidden('tranm', 'tranm', $tranm), 'content' );
	$GLOBALS['page']->add( Form::getHidden('tranc', 'tranc', $tranc), 'content' );
	$GLOBALS['page']->add( Form::getHidden('order_by', 'order_by', $order_by), 'content' );
	
	if($other_filter !== false && is_array($other_filter)) {
		
		foreach($other_filter as $name => $value) {
			$GLOBALS['page']->add( Form::getHidden(str_replace(']', '', str_replace('[', '_', $name)), $name, $value), 'content' );
		}
	}
	
	$GLOBALS['page']->add( Form::getHidden('onlyempty', 'onlyempty', $onlyempty), 'content' );
	// filter on module
	$arrModules = Docebo::langManager()->getAllModules($platformf);

	$allData = array();
	foreach( $arrModules as $arrElem ) 
		$allData[$arrElem] = $arrElem;
	$GLOBALS['page']->add( Form::getDropdown(	$lang->def( '_LANG_MODULE' ),
							"modulef", 
							"modulef", 
							array_merge( array(''=>$lang->def( '_LANG_ALLMODULES' )),$allData), 
							$modulef), 'content' );
	$GLOBALS['page']->add( Form::getTextfield( $lang->def( '_LANG_NEWMODULE' ), "newmodule", "newmodule", 50, ''), 'content' );
	
	$GLOBALS['page']->add( Form::getSimpleTextarea( $lang->def( '_DESCRIPTION' ), 
							"description", 
							"description",
							($key != '')?Docebo::langManager()->getKeyDescription($key,$modulef,$platformf):''), 'content' );
							
	$arr_attributes = split( ',', Docebo::langManager()->getKeyAttributes($key,$modulef,$platformf) );
	
	// lang attributes
	$GLOBALS['page']->add( 
		Form::getOpenCombo($lang->def( '_LANG_ATTRIBUTES' ))
		
		.Form::getCheckbox( $lang->def( '_LANG_ACCESSIBILITY' ), 
										'accessibility', 
										'accessibility', 
										'accessibility' , 
										in_array('accessibility',$arr_attributes),
										'')
		
		.Form::getCheckbox( $lang->def( '_LANG_SMS' ), 
										'sms', 
										'sms', 
										'sms' , 
										in_array('sms',$arr_attributes),
										'')
										
		.Form::getCheckbox( $lang->def( '_EMAIL' ), 
										'email', 
										'email', 
										'email' , 
										in_array('email',$arr_attributes),
										'')
		.Form::getCloseCombo()
	);
	
	$GLOBALS['page']->add( Form::getCloseFieldset(), 'content' );
	
	$GLOBALS['page']->add( Form::getOpenFieldset( $lang->def( '_TRANSLATELANG' ) ), 'content' );
	
	$arrLanguages = Docebo::langManager()->getAllLanguages();
	foreach( $arrLanguages as $langElem) {
		
		$translation_text = Docebo::langManager()->getLangTranslationText($langElem[0], $key, $modulef,$platformf);
		if(canEditLang($langElem[0])) {
			$GLOBALS['page']->add( Form::getSimpleTextarea( $langElem[0], 'lc_'.str_replace(' ', '_', $langElem[0]), 'lc_'.str_replace(' ', '_', $langElem[0]), $translation_text), 'content' );
		} else {
			$GLOBALS['page']->add(
				Form::openFormLine()
				.Form::getLabel('not_assigned_lc_'.$langElem[0], $langElem[0])
				.Form::getInputTextarea( 'not_assigned_lc_'.$langElem[0], 'not_assigned_lc_'.$langElem[0], $translation_text, false, 5, 22, ' disabled="disabled"')
				.Form::closeFormLine()
			, 'content');
		}
	}
	
	$GLOBALS['page']->add( Form::getCloseFieldset(), 'content' );
	$GLOBALS['page']->add( Form::closeElementSpace(), 'content' );
	$GLOBALS['page']->add( Form::openButtonSpace(), 'content' );
	$GLOBALS['page']->add( Form::getButton("editkeysave","editkeysave",$lang->def( '_SAVE' )), 'content' );
	$GLOBALS['page']->add( Form::getButton("editkeycancel","editkeycancel",$lang->def( '_CANCEL' )), 'content' );
	$GLOBALS['page']->add( Form::closeButtonSpace(), 'content' );
	$GLOBALS['page']->add( Form::closeForm(), 'content' );
	$GLOBALS['page']->add( '</div>', 'content');
}

function lang_del_key($key, $modulef, $platformf, $tranm, $tranc, $onlyempty) {
	
	checkPerm('view', false, 'lang', 'framework');
	
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	require_once(_base_.'/lib/lib.form.php');
	
	$GLOBALS['page']->add( getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">', 'content');
	//$GLOBALS['page']->add( Form::getFormHeader( $lang->def( '_DEL' ) ), 'content' );
	$GLOBALS['page']->add( Form::openForm( 	'langdelkey', 
							'index.php?modname=lang&amp;op=translator'
							.'&amp;platformf='.$platformf
							.'&amp;modulef='.$modulef
							.'&amp;tranm='.str_replace(' ', '_', $tranm)
							.'&amp;tranc='.$tranc ), 'content' );
	$GLOBALS['page']->add( Form::getHidden('keytodel', 'keytodel', $key), 'content' );
	$GLOBALS['page']->add( Form::getHidden('platformf', 'platformf', $platformf), 'content' );
	$GLOBALS['page']->add( Form::getHidden('modulef', 'modulef', $modulef), 'content' );
	$GLOBALS['page']->add( Form::getHidden('tranm', 'tranm', $tranm), 'content' );
	$GLOBALS['page']->add( Form::getHidden('tranc', 'tranc', $tranc), 'content' );
	$GLOBALS['page']->add( Form::getHidden('onlyempty', 'onlyempty', $onlyempty), 'content' );

	$GLOBALS['page']->add( '<div class="boxinfo_title">'
		.$lang->def( '_AREYOUSURE' )
		.'</div>'
		.'<div class="boxinfo_container">'
		.'<span class="text_bold">'.$key.'</span> ['. Lang::t($key, $modulef, $platformf, $tranm).' ]'
		.'</div>', 'content');
	$GLOBALS['page']->add( 
		'<div class="del_container">'
		.Form::getButton("editkeydel","editkeydel",$lang->def( '_CONFIRM' ), 'transparent_del_button' )
		.Form::getButton("editkeycancel","editkeycancel",$lang->def( '_UNDO' ), 'transparent_undo_button' )
		.'</div>', 'content' );
	
	$GLOBALS['page']->add( Form::closeForm(), 'content' );
	$GLOBALS['page']->add( '</div>', 'content');

	
}

function lang_inport() {
	global $globLangManager;
	
	checkPerm('view', false, 'lang', 'framework');
	
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	// loadAdminTitleArea('lang');
	if( isset($_GET['confirm']) && ($_GET['confirm'] == '1')) {
			include(dirname(__FILE__).'/ImportOld.php');
	} else {
		
		$GLOBALS['page']->add( getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
			.'<div class="std_block">'
			.'<div class="evidenceBlock">'
			.'<b>'.$lang->def( '_AREYOUSURE' ).'</b>', 'content');
		$GLOBALS['page']->add( '</div>'
			.'[ <a href="index.php?modname=lang&amp;op=import&amp;&amp;confirm=1">'.$lang->def( '_YESIMPORT' ).'</a> | '
			.'<a href="index.php?modname=lang&amp;op=lang">'.$lang->def( '_NOIMPORT' ).'</a> ]<br />', 'content');
	
		$GLOBALS['page']->add( '</div>', 'content');
	}	
}

function lang_importXML() {
	global $globLangManager;
	require_once(_lib_.'/lib.domxml5.php');
	$overwrite = isset($_POST['overwrite']);
	$no_add_miss = isset($_POST['no_add_miss']);
	
	$modules = 0;
	$definitions = 0;
	$doc = new DoceboDOMDocument();
	$doc->load( $_FILES['attach']['tmp_name'] );
	
	$query_new_lang_default = "INSERT INTO ".$globLangManager->_getTableText()
							." (text_key, text_module, text_platform, text_description, text_attributes ) VALUES ";
	
	$query_new_lang = $query_new_lang_default;
	
	$counter = 0;
	$array_for_update = array();
	
	$context = new DoceboDOMXPath( $doc );
	$root = $doc->documentElement;
	
	$arrLang = $context->query('//LANGUAGES/LANG');
	for( $iLang = 0; $iLang < $arrLang->length; $iLang++ ) {
		$lang =& $arrLang->item($iLang);
		$elem = $context->query('lang_code/text()',$lang);
		$elemNode = $elem->item(0);
		$lang_code = $elemNode->textContent;
		
		if(canEditLang($lang_code)) {
		
			$elem = $context->query('lang_description/text()',$lang);
			$elemNode = $elem->item(0);
			$lang_description = addslashes(urldecode($elemNode->textContent));
			$elem = $context->query('lang_charset/text()',$lang);
			$elemNode = $elem->item(0);
			$lang_charset = $elemNode->textContent;
			$elem = $context->query('lang_browsercode/text()',$lang);
			$elemNode = $elem->item(0);
			$lang_browsercode = $elemNode->textContent;
			
			$globLangManager->setLanguage($lang_code, $lang_description, $lang_charset, $lang_browsercode );
			$arrPlatforms = $context->query('platform',$lang);
			for( $iPlatform = 0; $iPlatform < $arrPlatforms->length; $iPlatform++ ) {
				
				$elem_platform =& $arrPlatforms->item($iPlatform);
				$platform = $elem_platform->getAttribute( 'id' );
				$arrModules = $context->query('module', $elem_platform);
				
				for( $iModule = 0; $iModule < $arrModules->length; $iModule++ ) {
					
					$modules++;
					$elem_module =& $arrModules->item($iModule);
					$module = $elem_module->getAttribute( 'id' );
					$arrKey = $context->query('key', $elem_module);
					
					for( $iKey = 0; $iKey < $arrKey->length; $iKey++ ) {
						
						$elem_key = $arrKey->item($iKey);
						$definitions++;
						$content = $elem_key->firstChild;
						
						if ($counter == 100) {
							
							$globLangManager->_executeQuery($query_new_lang);
							
							$query_new_lang = $query_new_lang_default;
							
							for ($i = 0; $i < $counter; $i++) {
								
								list($key,$module,$platform) = $globLangManager->decomposeKey($array_for_update[$i]['id']);
								if( $platform === FALSE ) list($module,$platform) = $globLangManager->decomposeKey( $module );
								
								$query = "SELECT id_text FROM ".$globLangManager->_getTableText()
										." WHERE text_platform = '".$platform."'"
										."   AND text_module = '".$module."'"
										."	 AND text_key = '".$key."'";
								$rs = $globLangManager->_executeQuery( $query );
								
								if( $rs != FALSE ) {
									
									list($id_text) = sql_fetch_row($rs);
									$query = "INSERT INTO ".$globLangManager->_getTableTranslation()
											." (id_text, translation_text,lang_code,save_date) VALUES "
											." ('".$id_text."',"
											."	'".$array_for_update[$i]['translation']."',"
											."	'".$array_for_update[$i]['lang_code']."',"
											."	'".$array_for_update[$i]['save_date']."' ) ";
									$globLangManager->_executeQuery($query);
									/*
									if($id_translation != FALSE ) {
										
										$query = "INSERT INTO ".$globLangManager->_getTableTextTranslation()
												." (id_text,id_translation) VALUES "
												." ('".$id_text."','".$id_translation."') ";
										$globLangManager->_executeQuery( $query );
									}*/
								}
							}
							
							$array_for_update = array();
							$counter = 0;
						} // end of cahced insert to do
						
						list($key,$module/*,$platform*/) = $globLangManager->decomposeKey($elem_key->getAttribute( 'id' ));
						//if( $platform === FALSE ) list($module,$platform) = $globLangManager->decomposeKey( $module );
						
						if($elem_key->hasAttribute('save_date')) $save_date = $elem_key->getAttribute('save_date');
						else $save_date = date('Y-m-d H:i:s'); 				
									
						$query = "SELECT id_text FROM ".$globLangManager->_getTableText()
								." WHERE text_key = '".$key."' "
								."   AND text_module = '".$module."'"
								."   AND text_platform = '".$platform."'";
						$rs = $globLangManager->_executeQuery( $query );
						if( mysql_num_rows($rs) == 0) {
							
							// a completly new key -------------------------------------------
							
							if($no_add_miss === false) {
								
								if ($counter) {
									$query_new_lang .= ", ('".$key."','".$module."','".$platform."','','".$elem_key->getAttribute('attributes')."') ";
								} else {
									$query_new_lang .= " ('".$key."','".$module."','".$platform."','','".$elem_key->getAttribute('attributes')."') ";
								}
								if($content != NULL) {
									
									if (preg_match("/^<!\\[CDATA\\[/i", $content->nodeValue))
										$str_value = trim(preg_replace("/<!\\[CDATA\\[(.*?)\\]\\]>/si", "\$1", $content->nodeValue));
									else
										$str_value = trim(urldecode($content->nodeValue));
									
									$array_for_update[$counter]['id'] = $elem_key->getAttribute( 'id' );
									$array_for_update[$counter]['translation'] = addslashes($str_value);
									$array_for_update[$counter]['lang_code'] = $lang_code;
									$array_for_update[$counter]['save_date'] = $save_date;
								} else {
									
									$array_for_update[$counter]['id'] = $elem_key->getAttribute( 'id' );
									$array_for_update[$counter]['translation'] = '';
									$array_for_update[$counter]['lang_code'] = $lang_code;
									$array_for_update[$counter]['save_date'] = $save_date;
								}
								$counter++;
							}
						} else {
							
							// the key alredy exists, now we must check if the translation exists
							
                            list($id_text) = sql_fetch_row($rs);
                            
							$query = "SELECT translation_text "
									." FROM  ".$globLangManager->_getTableTranslation()." "
									." WHERE lang_code = '".$lang_code."'"
									." AND id_text = '".$id_text."'";
							$re = sql_query($query);
							list($translation_text) = sql_fetch_row($re);
							
							if( $content != NULL ) {
								
								if (preg_match("/^<!\\[CDATA\\[/i", $content->nodeValue))
									$str_value = trim(preg_replace("/<!\\[CDATA\\[(.*?)\\]\\]>/si", "\$1", $content->nodeValue));
								else
									$str_value = trim(urldecode($content->nodeValue));
							} else {
								$str_value = '';
							} 
							if(mysql_num_rows($re)) {
								
								if($overwrite == true || trim($translation_text) == '') {
									
									$query = "UPDATE ".$globLangManager->_getTableTranslation()
											."   SET translation_text='".addslashes($str_value)."', "
											." save_date  = '".$save_date."'"
											." WHERE lang_code = '".$lang_code."'"
											." AND id_text = '".$id_text."'";
									$globLangManager->_executeQuery( $query );
								}
							} else {
								
								$array_for_update[$counter]['id'] = $elem_key->getAttribute( 'id' );
								$array_for_update[$counter]['translation'] = addslashes($str_value);
								$array_for_update[$counter]['lang_code'] = $lang_code;
								$array_for_update[$counter]['save_date'] = $save_date;
						
								$counter++;
							}
							// --------------------------------
						}
						
					} // end for on arrKey
					
				} // end for on modules
				
			} // end for on platforms
			
		} // end if
		
		$globLangManager->_executeQuery($query_new_lang);
		
		$query_new_lang = $query_new_lang_default;
		
		for ($i = 0; $i < $counter; $i++) {
			
			list($key,$module,$platform) = $globLangManager->decomposeKey($array_for_update[$i]['id']);
			if( $platform === FALSE ) list($module,$platform) = $globLangManager->decomposeKey( $module );
								
			$query = "SELECT id_text FROM ".$globLangManager->_getTableText()
					." WHERE text_platform = '".$platform."'"
					."   AND text_module = '".$module."'"
					."	 AND text_key = '".$key."'";
			$rs = $globLangManager->_executeQuery( $query );
			if( $rs != FALSE )
			{
				list($id_text) = sql_fetch_row($rs);
				
				if($array_for_update[$i]['translation'] == '') $save_date = '0000-00-00 00:00:00';
				$query = "INSERT INTO ".$globLangManager->_getTableTranslation()
						." (id_text, translation_text,lang_code,save_date) VALUES "
						." ('".$id_text."',"
						."	'".$array_for_update[$i]['translation']."',"
						."	'".$array_for_update[$i]['lang_code']."',"
						."	'".$array_for_update[$i]['save_date']."' ) ";
				$globLangManager->_executeQuery($query);
			}
		}
		
		$array_for_update = array();
		$counter = 0;
		
	}
	
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">'
		.getBackUi( "index.php?modname=lang&amp;op=importexport", $lang->def( '_BACK' ) )
		.'<span class="text_bold">'.$lang->def('_MODULES_UPDATED').' : </span>'.$modules.'<br />'
		.'<span class="text_bold">'.$lang->def('_TOTAL_LANGUAGE_UPDATED').' : </span>'.$definitions
		.'</div>'
	, 'content');
}

function lang_exportXML() {
	require_once(_lib_.'/lib.domxml5.php');
	$lang_exported 		= array();
	$platform_exported 	= array();
	
	$doc = new DoceboDOMDocument('1.0');
	$root = $doc->createElement("LANGUAGES");
	$doc->appendChild($root);
	
	$elem = $doc->createElement("DATE");
	$elemText = $doc->createTextNode(date("Ymd"));
	$elem->appendChild($elemText);
	$root->appendChild($elem);
	
	if( isset($_POST['lang_code']) && isset($_POST['platform']) ) {
		if( $_POST['lang_code'][0] == '' ) {
			$arrLanguages = Docebo::langManager()->getAllLanguages();
			$_POST['lang_code'] = array();
			foreach( $arrLanguages as $arrLangElem ) {
				$_POST['lang_code'][] = $arrLangElem[0];
			}
		}
		foreach( $_POST['lang_code'] as $lang_code ) {
			
			$lang_code = str_replace('_', ' ', $lang_code);
			$lang_exported[$lang_code] = $lang_code;
			
			$lang = $doc->createElement("LANG");
			$root->appendChild($lang);
			$elem = $doc->createElement("lang_code");
			$elemText = $doc->createTextNode($lang_code);
			$elem->appendChild($elemText);
			$lang->appendChild($elem);
			$lang->setAttribute('id', $lang_code );
			
			$elem = $doc->createElement("lang_description");
			$elemText = $doc->createTextNode(urlencode(Docebo::langManager()->getLanguageDescription($lang_code)));
			$elem->appendChild($elemText);
			$lang->appendChild($elem);
			
			$elem = $doc->createElement("lang_charset");
			$elemText = $doc->createTextNode(Docebo::langManager()->getLanguageCharset($lang_code));
			$elem->appendChild($elemText);
			$lang->appendChild($elem);	
			
			$elem = $doc->createElement("lang_browsercode");
			$elemText = $doc->createTextNode(Docebo::langManager()->getLanguageBrowsercode($lang_code));
			$elem->appendChild($elemText);
			$lang->appendChild($elem);
			
			foreach( $_POST['platform'] as $platform ) {
				$platform_exported[$platform] = $platform;
				
				$elemPlatform = $doc->createElement("platform");
				$elemPlatform->setAttribute( "id", $platform );
				$lang->appendChild( $elemPlatform );
				if( !isset($_POST['modules']) || $_POST['modules'][0] == '-' )
					$arrModules = Docebo::langManager()->getAllModules($platform);
				else {
					$arrModules = array();
					foreach( $_POST['modules'] as $module ) {
						$mod_plat = substr($module, 0, strpos($module, "-"));
						$mod_mod = substr($module, strpos($module, "-")+1);
						if( $mod_plat == $platform ) {						 
							$arrModules[] = $mod_mod;
						}
					}
				}
								
				foreach( $arrModules as $module ) {
					$elemModule = $doc->createElement("module");
					$elemModule->setAttribute( "id", $module );
					$elemPlatform->appendChild( $elemModule );
					
					$arrTranslations = Docebo::langManager()->getModuleLangTranslations($platform,$module,$lang_code, '', false, false, true);
					foreach( $arrTranslations as $tran ) {
						$elem = $doc->createElement("key");
						$elem->setAttribute('id',Docebo::langManager()->composeKey( $tran[1], $module, $platform) );
						$elem->setAttribute('attributes', $tran[3]);
						$elem->setAttribute('save_date', $tran[4]);
						$elemText = $doc->createTextNode('<![CDATA['.$tran[2].']]>');
						$elem->appendChild($elemText);
						$elemModule->appendChild($elem);
					}
				}
			}
		}
	}
	$out = $doc->saveXML();
	
	$filename = 'platform['.implode('-', $platform_exported).']_lang['.implode('-', $lang_exported).']';
	$filename = substr($filename, 0, 200);
	
	ob_end_clean();
	//Download file
	//send file length info
	header('Content-Length:'. strlen($out));
	//content type forcing dowlad
	header("Content-type: application/download\n");
	//cache control
	header("Cache-control: private");
	//sending creation time
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	//content type
	header('Content-Disposition: attachment; filename="'.$filename.'.xml"');
	//sending file
	echo $out;
	//and now exit	
	exit();
}

function lang_exportXML_missing() {
	
	$lang_exported 		= array();
	$platform_exported 	= array();
	
	$doc = new DoceboDOMDocument("1.0");
	$root = $doc->createElement("LANGUAGES");
	$doc->appendChild($root);
	
	$elem = $doc->createElement("DATE");
	$elemText = $doc->createTextNode(date("Ymd"));
	$elem->appendChild($elemText);
	$root->appendChild($elem);
	
	if( isset($_POST['lang_code']) && isset($_POST['platform']) ) {
		if( $_POST['lang_code'][0] == '' ) {
			$arrLanguages = Docebo::langManager()->getAllLanguages();
			$_POST['lang_code'] = array();
			foreach( $arrLanguages as $arrLangElem ) {
				$_POST['lang_code'][] = $arrLangElem[0];
			}
		}
		foreach( $_POST['lang_code'] as $lang_code ) {
			
			$lang_code = str_replace('_', ' ', $lang_code);
			$lang_exported[$lang_code] = $lang_code;
			
			$lang = $doc->createElement("LANG");
			$root->appendChild($lang);
			$elem = $doc->createElement("lang_code");
			$elemText = $doc->createTextNode($lang_code);
			$elem->appendChild($elemText);
			$lang->appendChild($elem);
			$lang->setAttribute('id', $lang_code );
			
			$elem = $doc->createElement("lang_description");
			$elemText = $doc->createTextNode(urlencode(Docebo::langManager()->getLanguageDescription($lang_code)));
			$elem->appendChild($elemText);
			$lang->appendChild($elem);
			
			$elem = $doc->createElement("lang_charset");
			$elemText = $doc->createTextNode(Docebo::langManager()->getLanguageCharset($lang_code));
			$elem->appendChild($elemText);
			$lang->appendChild($elem);	
			
			$elem = $doc->createElement("lang_browsercode");
			$elemText = $doc->createTextNode(Docebo::langManager()->getLanguageBrowsercode($lang_code));
			$elem->appendChild($elemText);
			$lang->appendChild($elem);
			
			foreach( $_POST['platform'] as $platform ) {
				$platform_exported[$platform] = $platform;
				
				$elemPlatform = $doc->createElement("platform");
				$elemPlatform->setAttribute( "id", $platform );
				$lang->appendChild( $elemPlatform );
				if( !isset($_POST['modules']) || $_POST['modules'][0] == '-' )
					$arrModules = Docebo::langManager()->getAllModules($platform);
				else {
					$arrModules = array();
					foreach( $_POST['modules'] as $module ) {
						$mod_plat = substr($module, 0, strpos($module, "-"));
						$mod_mod = substr($module, strpos($module, "-")+1);
						if( $mod_plat == $platform ) {						 
							$arrModules[] = $mod_mod;
						}
					}
				}
								
				foreach( $arrModules as $module ) {
					$elemModule = $doc->createElement("module");
					$elemModule->setAttribute( "id", $module );
					$elemPlatform->appendChild( $elemModule );
					
					$arrTranslationsEng = Docebo::langManager()->getModuleLangTranslations($platform,$module,'english', '', false, false, true);
					$arrTranslationsOrg = Docebo::langManager()->getModuleLangTranslations($platform,$module,$lang_code, '', false, false, true);
					
					foreach( $arrTranslationsOrg as $id_text => $tran ) {
						
						if(trim($tran[2]) == '') {
							
							$tran = $arrTranslationsEng[$id_text];
							
							$elem = $doc->createElement("key");
							$elem->setAttribute('id',Docebo::langManager()->composeKey( $tran[1], $module, $platform) );
							$elem->setAttribute('attributes', $tran[3]);
							$elem->setAttribute('save_date', $tran[4]);
							$elemText = $doc->createTextNode('<![CDATA['.$tran[2].']]>');
							$elem->appendChild($elemText);
							$elemModule->appendChild($elem);
						}
							
						unset($arrTranslationsEng[$id_text]);
					}
					if(!empty($arrTranslationsEng)) {
						
						foreach( $arrTranslationsEng as $id_text => $tran ) {
							
							$tran = $arrTranslationsEng[$id_text];
							
							$elem = $doc->createElement("key");
							$elem->setAttribute('id',Docebo::langManager()->composeKey( $tran[1], $module, $platform) );
							$elem->setAttribute('attributes', $tran[3]);
							$elem->setAttribute('save_date', $tran[4]);
							$elemText = $doc->createTextNode('<![CDATA['.$tran[2].']]>');
							$elem->appendChild($elemText);
							$elemModule->appendChild($elem);
						}
					}
				}
			}
		}
	}
	$out = $doc->saveXML();
	
	$filename = 'platform['.implode('-', $platform_exported).']_lang['.implode('-', $lang_exported).']';
	$filename = substr($filename, 0, 200);
	
	ob_end_clean();
	//Download file
	//send file length info
	header('Content-Length:'. strlen($out));
	//content type forcing dowlad
	header("Content-type: application/download\n");
	//cache control
	header("Cache-control: private");
	//sending creation time
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	//content type
	header('Content-Disposition: attachment; filename="'.$filename.'.xml"');
	//sending file
	echo $out;
	//and now exit	
	exit();
}

function lang_importexport() {
	
	checkPerm('view', false, 'lang', 'framework');
	
	require_once(_base_.'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	
	$GLOBALS['page']->setWorkingZone( 'content' );
	$GLOBALS['page']->add( getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">' );
	
	// import Old
	/*
	$GLOBALS['page']->add( Form::openForm( 	'importForm', 
											'index.php?modname=lang&amp;op=import')
						   );
	$GLOBALS['page']->add( Form::getOpenFieldset( $lang->def( '_IMPORT' ) )
		.$lang->def( '_IMPORT_DESCR' ) );
	
	$GLOBALS['page']->add( Form::openButtonSpace() );
	$GLOBALS['page']->add( Form::getButton("import","import",$lang->def( '_IMPORT' )) );
	$GLOBALS['page']->add( Form::closeButtonSpace() );
	$GLOBALS['page']->add( Form::getCloseFieldset() );
	$GLOBALS['page']->add( Form::closeForm() );
	*/
	// import XML
	$GLOBALS['page']->add( Form::openForm( 	'importXMLForm', 
											'index.php?modname=lang&amp;op=importXML',
											'std_form',
											'post',
											'multipart/form-data') 
						    );
	$GLOBALS['page']->add( Form::getOpenFieldset( $lang->def( '_IMPORT_FROM_XML' ) ) );
	/*$GLOBALS['page']->add(	'<label class="floating" for="file">'.$lang->def( '_FILE' ).'</label>' 
							.'<input class="fileupload" id="file" type="file" name="attach" maxlength="255" alt="'.$lang->def( '_FILE' ).'" />'
							);*/
	
	$GLOBALS['page']->add( Form::getFilefield(	$lang->def( '_FILE' ), 
													'file', 
													'attach', 
													'',
													$lang->def( '_FILE' )) 
							.Form::getCheckbox(	$lang->def('_OVERWRITE_EXISTENT'),
												'overwrite',
												'overwrite',
												'1')
							.Form::getCheckbox(	$lang->def('_DO_NOT_ADD_MISS'),
												'no_add_miss',
												'no_add_miss',
												'1')
						);
					   
	$GLOBALS['page']->add( Form::openButtonSpace() );
	$GLOBALS['page']->add( Form::getButton("importXML","importXML",$lang->def( '_IMPORT_FROM_XML' )) );
	$GLOBALS['page']->add( Form::closeButtonSpace() );
	$GLOBALS['page']->add( Form::getCloseFieldset() );
	$GLOBALS['page']->add( Form::closeForm() );

	// export
	$GLOBALS['page']->add( Form::openForm( 	'exportXMLform', 
											'index.php?modname=lang&amp;op=exportXML') );
	$GLOBALS['page']->add( Form::getOpenFieldset( $lang->def( '_EXPORT_XML' ) ));
	
	$plt_man =& PlatformManager::createInstance();
	$plt_list = $plt_man->getPlatformList();
	$GLOBALS['page']->add( Form::getListbox($lang->def( '_LANG_PLATFORM' ), 
											'platform', 
											'platform[]', 
											$plt_list) );
											
	$arr_modules = array();
	$arr_tmp = Docebo::langManager()->getAllModules('framework');
	$arr_modules['-'] = '---all modules---';
	foreach( $arr_tmp as $module ) {
		$arr_modules['framework-'.$module] = 'framework-'.$module;
	}
	$arr_tmp = Docebo::langManager()->getAllModules('lms');
	foreach( $arr_tmp as $module ) {
		$arr_modules['lms-'.$module] = 'lms-'.$module;
	}
	$arr_tmp = Docebo::langManager()->getAllModules('cms');
	foreach( $arr_tmp as $module ) {
		$arr_modules['cms-'.$module] = 'cms-'.$module;
	}
											
	$GLOBALS['page']->add( Form::getListbox($lang->def( '_LANG_MODULES' ), 
											'modules', 
											'modules[]', 
											$arr_modules,
											array('-')));
	
	$GLOBALS['page']->add( '<script type="text/javascript">'."\n", 'page_head');
	$GLOBALS['page']->add( 'var strAllModules = "---all modules---";', 'page_head' );
	
	$GLOBALS['page']->add( 'var arrFrameworkModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('framework')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrLmsModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('lms')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrCmsModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('cms')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrScsModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('scs')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrEcomModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('ecom')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	$GLOBALS['page']->add( 'var arrCrmModules = ["', 'page_head' );
	$GLOBALS['page']->add( join('","', Docebo::langManager()->getAllModules('crm')), 'page_head' );
	$GLOBALS['page']->add( '"];'."\n", 'page_head');
	
	
	$GLOBALS['page']->add( 
	'window.onload = function(){
		var fieldPlatform = document.getElementById("platform");
		fieldPlatform.onchange = filterModules;
	}'."\n", 'page_head');
	
	$GLOBALS['page']->add( 
	'function filterModules() {
		var fieldPlatform = document.getElementById("platform");
		var fieldModules = document.getElementById("modules");
		// delete all
		for( var i = fieldModules.options.length-1; i >= 0 ; i-- ) {
			fieldModules.options[i] = null;
		}
		var optModule = new Option(strAllModules,"-",true,true);
		fieldModules.options[0] = optModule;
		for( i = 0; i < fieldPlatform.options.length; i++ ) {
			if( fieldPlatform.options[i].selected ) {
				var platform = fieldPlatform.options[i].value;
				if( platform == "framework" ) {
					
					reinitModules(arrFrameworkModules, platform);
				} else if( platform == "lms" ) {
					
					reinitModules(arrLmsModules, platform);
				} else if( platform == "cms" ) {
					
					reinitModules(arrCmsModules, platform);
				} else if( platform == "scs" ) {
					
					reinitModules(arrScsModules, platform);
				} else if( platform == "ecom" ) {
					
					reinitModules(arrEcomModules, platform);
				} else if( platform == "crm" ) {
					
					reinitModules(arrCrmModules, platform);
				} 
			}
		}
	}'."\n", 'page_head');
	
	$GLOBALS['page']->add( 
	'function reinitModules(arrModules, platform) {
		var fieldModules = document.getElementById("modules");
		// delete all
		//for( var i = fieldModules.options.length-1; i >= 0 ; i-- ) {
		//	fieldModules.options[i] = null;
		//}
		// populate
		var optModule = null;
		for( var i = 0; i < arrModules.length ; i++ ) {
			optModule = new Option(platform+"-"+arrModules[i],platform+"-"+arrModules[i]);
			fieldModules.options[fieldModules.length] = optModule;
		}
	}'."\n", 'page_head');
	
	$GLOBALS['page']->add( '</script>', 'page_head');

											
	$arrLanguages = Docebo::langManager()->getAllLanguages();
	$all_value = array();
	$all_value[''] = $lang->def( '_LANG_ALL' );
	foreach( $arrLanguages as $opt_lang_comp ) 
		$all_value[$opt_lang_comp[0]] = $opt_lang_comp[0]; 

	$GLOBALS['page']->add( Form::getListbox($lang->def( '_LANGUAGE' ), 
											'lang_code',
											'lang_code[]',
											$all_value) );
											
											
	$GLOBALS['page']->add( Form::getCheckbox(	$lang->def('_EXPORT_DIFF'),
												'export_diff',
												'export_diff',
												'1') );
											
	$GLOBALS['page']->add( Form::openButtonSpace() );
	$GLOBALS['page']->add( Form::getButton("exportXML","exportXML",$lang->def( '_EXPORT_XML' )) );
	$GLOBALS['page']->add( Form::closeButtonSpace() );
	$GLOBALS['page']->add( Form::getCloseFieldset() );
	$GLOBALS['page']->add( Form::closeForm() );
	/*
	$GLOBALS['page']->add( Form::openForm( 	'importForm', 
											'index.php?modname=lang&amp;op=clean_lang')
						   );
	$GLOBALS['page']->add( Form::getOpenFieldset( $lang->def( '_CLEAN_LANG' ) )
		.$lang->def( '_LANG_LEAN_DESCR' ) );
	
	$GLOBALS['page']->add( Form::openButtonSpace() );
	$GLOBALS['page']->add( Form::getButton("save","save",$lang->def( '_CLEAR' )) );
	$GLOBALS['page']->add( Form::closeButtonSpace() );
	$GLOBALS['page']->add( Form::getCloseFieldset() );
	$GLOBALS['page']->add( Form::closeForm() );
	*/
	$GLOBALS['page']->add( '</div>');
}

/** Clean languages **/

function parse($file_path) {
	
	$string_lang = array();
	$file_to_parse = file_get_contents($file_path);
	$founded = preg_match_all("/_[A-Z0-9\_]{2,}/", $file_to_parse, $string_lang);
	$re = true;
	$lang = '';
	$i = 0;
	if($founded) {
		
		while(list(, $lang_def) = each($string_lang[0])) {
			
			if($lang_def != '_SESSION' && $lang_def != '_GET'  && $lang_def != '_POST' && 
				$lang_def != '__FILE__' && !strpos($lang_def, '___')) {
				++$i;
				$GLOBALS['parsed_define'][$lang_def] = $lang_def;
			}
		}
	}
	return $i;
}

function scan_dir($dir_path) {
	
	checkPerm('view', false, 'lang', 'framework');
	
	$scan_dir = opendir($dir_path);
	
	$GLOBALS['page']->add( '<ul>', 'content');
	while ($file = readdir($scan_dir)) {
		if($file != '..' && $file !='.' && $file !=''){ 
			
			if(is_dir($dir_path.$file)) {
				
				if($file != 'SQL' && $file != 'CVS' && $file != 'addons' && $file != 'templates' && $file != 'files') {
					$GLOBALS['page']->add( '<li>'
						.'<span style="color: #0cc;">'.$file.'</span>', 'content');
					scan_dir($dir_path.$file.'/');
					$GLOBALS['page']->add( '</li>', 'content');
				}
			} else {
				
				$GLOBALS['page']->add( '<li>', 'content');
				if(strpos($file, '.php') !== false) {
					$GLOBALS['page']->add( '<span style="color: #369;">'.$file.'</span>', 'content');
					$re = parse($dir_path.$file);
					$GLOBALS['page']->add( '<span style="color: #06f;"> parse complete </span><span style="color: #f20;">'.$re.'</span>', 'content');
				}
				$GLOBALS['page']->add( '</li>', 'content');
			}
		}
	}
	$GLOBALS['page']->add( '</ul>', 'content');
	closedir($scan_dir);
	clearstatcache();
}

function clean_lang() {
	checkPerm('view', false, 'lang', 'framework');
	
	require_once(_base_.'/lib/lib.platform.php');
	$lang =& DoceboLanguage::createInstance('admin_lang', 'framework');
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_LANGUAGE_MANAGMENT'), 'manlanguage', $lang->def('_LANGUAGE_MANAGMENT'))
		.'<div class="std_block">'
	, 'content');
		
	$GLOBALS['parsed_define'] = array();
	scan_dir($GLOBALS['where_framework'].'/');
	scan_dir($GLOBALS['where_lms'].'/');
	
	//$standard_def = Docebo::langManager()->getModuleLangTranslations('framework', 'standard', 'italian');
	
	$plat_man = new PlatformManager();
	$all_platform = $plat_man->getPlatformList();
	
	$i = 0;
	echo '<table cellspacing=0">';
	foreach( $all_platform as $platform => $name ) {
		
		$all_modules = Docebo::langManager()->getAllModules($platform);
		
		foreach( $all_modules as $module ) {
			
			$all_translations = Docebo::langManager()->getModuleLangTranslations($platform, $module, 'italian');
			foreach( $all_translations as $tran ) {
				
				if(!isset($GLOBALS['parsed_define'][$tran[1]])) {
					
					//echo '<tr><td>'.$i++.'</td><td>'.$platform.'</td><td>'.$module.'</td><td>'.$tran[1].'</td></tr>';
					Docebo::langManager()->deleteKey($tran[1], $module,$platform);
				} else {
					//echo '<tr><td>'.$i++.'</td><td>conserve'.$platform.'</td><td>'.$module.'</td><td>'.$tran[1].'</td></tr>';
				}
				//Docebo::langManager()->composeKey( $tran[1], $module, $platform);
			}
		}
	}

	$GLOBALS['page']->add(
		'</div>'
	, 'content');
}

function langDispatch( $op ) {
	switch($op) {
		case "lang" : {
			lang_lang();
		};break;
		
		//new lang
		case "addlang" : {
			lang_editlang();
		};break;
		case "inslang" : {
			lang_uplang();
		};break;
		
		//mod lang
		case "modlang" : {
			lang_editlang($_GET['lang_code']);
		};break;
		case "uplang" : {
			lang_uplang();
		};break;
		
		//rem lang
		case "dellang" : {
			lang_dellang();
		};break;
		
		//translator
		case "translator" : {
			lang_translator();
		};break;
		
		//import export
		case "importexport": {
			lang_importexport();
		};break;
		
		//special import
		case "import": {
			lang_inport();
		};break;
		
		// importXML
		case "importXML": {
			lang_importXML();
		};break;
		
		// exportXML
		case "exportXML": {
			if(isset($_POST['export_diff'])) lang_exportXML_missing();
			else lang_exportXML();
		};break;
		
		case "clean_lang" : {
			clean_lang();
		};break;
	}
}


?>
