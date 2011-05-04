<?php defined("IN_DOCEBO") or die('Direct access is forbidden.');

/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

/**
 * @module scorm.php
 * Impor module for scorm content packages
 * @version $Id: scorm.php 1002 2007-03-24 11:55:51Z fabio $
 * @copyright 2004 
 * @author Emanuele Sandri
 **/

define( "STRPOSTCONTENT", '_content');
 
function additem($object_item) {
	//checkPerm( 'view', FALSE, 'storage' );
	
	$lang =& DoceboLanguage::createInstance('scorm', 'lms');
	require_once(_base_.'/lib/lib.form.php');
	$form = new Form();
	
	//area title
	$GLOBALS['page']->add( getTitleArea(
							$lang->getLangText('_SCORMIMGSECTION'),
							'scorm',
							$lang->getLangText('_SCORMSECTIONNAME'))
			);
			
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=insitem", 
								false, 
								false, 
								'multipart/form-data')
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->getFilefield( $lang->getLangText('_CONTENTPACKAGE'), "attach", "attach" ) );

	$GLOBALS['page']->add( $form->getCheckbox( 	$lang->getLangText('_SCORMIMPORTRESOURCES'), 
												"lesson_resources", 
												"lesson_resources", 
												"import" ) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												$lang->getLangText('_SCORMLOAD') ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Rocky SIGNAVONG
	 * Created Date : 08/03/11
	 * Modified Date : 08/03/11
	 * Version : 1.0
	 * Function : Ajouter un formulaire
	 * Description : Ajouter un formulaire avec un bouton pour lancer le script de multichargement sur Docebo
	 * 
	 **********************************/
	$form = new Form();
	
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=insallitem", 
								false, 
								false, 
								false)
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												$lang->getLangText('_SCORMLOAD') ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Polo
	 * Created Date : 22/03/11
	 * Modified Date : 22/03/11
	 * Version : 1.0
	 * Function : Ajouter un formulaire
	 * Description : Ajouter un formulaire avec un bouton pour la liaison entre les cours et les contenu
	 * 
	 **********************************/
	$form = new Form();
	
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=synchroallitem", 
								false, 
								false, 
								false)
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												$lang->getLangText('_SCORMSYNCHRO') ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Polo
	 * Created Date : 24/03/11
	 * Modified Date : 24/03/11
	 * Version : 1.0
	 * Function : Ajouter un formulaire
	 * Description : Ajouter un formulaire avec un bouton pour la récupération de la description
	 * 
	 **********************************/
	$form = new Form();
	
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=infoallitem", 
								false, 
								false, 
								false)
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												$lang->getLangText('_SCORMINFO') ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Polo
	 * Created Date : 04/04/11
	 * Modified Date : 04/04/11
	 * Version : 1.0
	 * Function : Ajouter un formulaire
	 * Description : Bouton de création des groupes de code / règles d'inscription
	 * 
	 **********************************/
	$form = new Form();
	
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=createcontent", 
								false, 
								false, 
								false)
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												"CONTENT" ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Polo
	 * Created Date : 04/04/11
	 * Modified Date : 04/04/11
	 * Version : 1.0
	 * Function : Ajouter un formulaire
	 * Description : Bouton de clean
	 * 
	 **********************************/
	$form = new Form();
	
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=cleanitems", 
								false, 
								false, 
								false)
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												"CLEAN" ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
	
	/**********************************
	 * 
	 * YES SAS - Your English Solution
	 * Author : Polo
	 * Created Date : 04/04/11
	 * Modified Date : 04/04/11
	 * Version : 1.0
	 * Function : Ajouter un formulaire
	 * Description : Bouton d'ajout des course à certains users
	 * 
	 **********************************/
	$form = new Form();
	
	$GLOBALS['page']->add( 	
				'<div class="std_block">'
				.getBackUi( Util::str_replace_once('&', '&amp;', $object_item->back_url).'&amp;create_result=0', 
							$lang->getLangText('_BACK_TOLIST' ))							
			);
	
	$GLOBALS['page']->add( Form::getFormHeader($lang->def('_SCORM_ADD_FORM') ) );
	
	$GLOBALS['page']->add( 
				$form->openForm("scormform", 
								"index.php?modname=scorm&amp;op=joinitems", 
								false, 
								false, 
								false)
			);
	$GLOBALS['page']->add( $form->openElementSpace() );
	
	$GLOBALS['page']->add( $form->getHidden("back_url","back_url",htmlentities(urlencode($object_item->back_url))) );
	$GLOBALS['page']->add( $form->closeElementSpace() );
	$GLOBALS['page']->add( $form->openButtonSpace() );
	$GLOBALS['page']->add( $form->getButton( 	"scorm_add_submit", 
												"scorm_add_submit", 
												"USERS" ) );
	$GLOBALS['page']->add( $form->closeButtonSpace() );
	$GLOBALS['page']->add( $form->closeForm().'</div>' );
}
 
function insitem() {
	//checkPerm( 'view', FALSE, 'storage' );
	
	require_once(_base_.'/lib/lib.upload.php');
	require_once(_base_.'/addons/pclzip/pclzip.lib.php');
	require_once(dirname(__FILE__).'/RendererDb.php');
	require_once(dirname(__FILE__).'/CPManager.php');
	
	$back_url = urldecode($_POST['back_url']);
	
	// there is a file?
	if($_FILES['attach']['name'] == '') {
		$_SESSION['last_error'] = _FILEUNSPECIFIED;
		Util::jump_to( ''.$back_url.'&create_result=0' );
	}
	$path = str_replace ( '\\', '/', '/doceboLms/'.Get::sett('pathscorm'));
	$savefile = getLogUserId().'_'.rand(0,100).'_'.time().'_'.$_FILES['attach']['name'];
	if(!file_exists ($GLOBALS['where_files_relative'].$path.$savefile)) {
		sl_open_fileoperations();
		if(!sl_upload($_FILES['attach']['tmp_name'], $path.$savefile)) {
		//if( !move_uploaded_file($_FILES['attach']['tmp_name'], $GLOBALS['where_files_relative'].$path.$savefile ) ) {
			sl_close_fileoperations();				
			$_SESSION['last_error'] = _ERROR_UPLOAD;
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
	} else {
		sl_close_fileoperations();
		$_SESSION['last_error'] = _ERROR_UPLOAD;
		Util::jump_to( ''.$back_url.'&create_result=0' );
	}
	
	// compute filepath
	$filepath = $path.$savefile.STRPOSTCONTENT;
	// extract zip file
	$zip = new PclZip($path.$savefile);
	
	// check disk quota --------------------------------------------------
	if(isset($_SESSION['idCourse']) && defined("LMS")) {

		$zip_content = $zip->listContent();
		$zip_extracted_size = 0;
		while(list(, $file_info) = each($zip_content)) {

			$zip_extracted_size += $file_info['size'];
		}

		$quota = $GLOBALS['course_descriptor']->getQuotaLimit();
		$used = $GLOBALS['course_descriptor']->getUsedSpace();

		if(Util::exceed_quota(false, $quota, $used, $zip_extracted_size)) {

			sl_unlink($path.$savefile);
			$_SESSION['last_error'] = Lang::t('_QUOTA_EXCEDED');
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
		$GLOBALS['course_descriptor']->addFileToUsedSpace(false, $zip_extracted_size);
	}
	// extract zip ------------------------------------------------------
	
	$zip->extract(PCLZIP_OPT_PATH, $filepath );
	if( $zip->errorCode() != PCLZIP_ERR_NO_ERROR && $zip->errorCode() != 1 ) {
		sl_unlink($path.$savefile);
		$_SESSION['last_error'] = _ERROR_UPLOAD;
		sl_close_fileoperations();

		Util::jump_to( ''.$back_url.'&create_result=0' );
	}

	/* remove zip file */
	sl_unlink($path.$savefile);
	sl_close_fileoperations();

	
	$cpm = new CPManager();
	// try to open content package
	if( !$cpm->Open( $GLOBALS['where_files_relative'].$filepath ) ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		Util::jump_to( ''.$back_url.'&create_result=0' );
	}
	// and parse the manifest
	if( !$cpm->ParseManifest() ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		Util::jump_to( ''.$back_url.'&create_result=0' );
	}

	// create entry in content package table
	$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package"
			." (idpackage,idProg,path,defaultOrg,idUser,scormVersion) VALUES"
			." ('".addslashes($cpm->identifier)
			."','0','".$savefile.STRPOSTCONTENT
			."','".addslashes($cpm->defaultOrg)
			."','".(int)getLogUserId()
			."','".$cpm->scorm_version
			."')";
	if( !($result = sql_query($query)) ) {
		$_SESSION['last_error'] = _OPERATION_FAILURE;
		Util::jump_to( ''.$back_url.'&create_result=0' );
	}
	
	$idscorm_package = mysql_insert_id();
	
	// create the n entries in resources table
	for( $i = 0; $i < $cpm->GetResourceNumber(); $i++ ) {
		$info = $cpm->GetResourceInfo( $cpm->GetResourceIdentifier($i) );
		$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources (idsco,idscorm_package,scormtype,href)"
				." VALUES ('".addslashes($info['identifier'])."','"
				.(int)$idscorm_package."','"
				.$info['scormtype']."','"
				.addslashes($info['href']) ."')";

		$result = sql_query( $query );

		if(!$result){
			$_SESSION['last_error'] = _OPERATION_FAILURE;
			Util::jump_to( ''.$back_url.'&create_result=0' );
		} else if(mysql_affected_rows() == 0)  {
			$_SESSION['last_error'] = _OPERATION_FAILURE;
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
	}

	$rdb = new RendererDb($GLOBALS['dbConn'], $GLOBALS['prefix_lms'], $idscorm_package);
	$orgElems = $cpm->orgElems;
	// save all organizations
	for( $iOrg = 0; $iOrg < $orgElems->getLength(); $iOrg++ ) {
		$org = $orgElems->item($iOrg);
		$cpm->RenderOrganization( $org->getAttribute('identifier'), $rdb );
	}
	
	if( $_POST['lesson_resources'] == 'import' || $cpm->defaultOrg == '-resource-' ) {
		// save flat organization with resources
		$cpm->RenderOrganization( '-resource-', $rdb );
	}
	
	$so = new Scorm_Organization( $cpm->defaultOrg, $idscorm_package, $GLOBALS['dbConn'] );
	if( $so->err_code > 0 ) {
		$_SESSION['last_error'] = 'Error: '. $so->getErrorText() . ' [' . $so->getErrorCode() .']';
		Util::jump_to( ''.$back_url.'&create_result=0' );
	} else {
		//Util::jump_to( ''.$back_url.'&id_lo='.$so->idscorm_organization.'&create_result=1' );
		Util::jump_to( ''.$back_url.'&id_lo='.$idscorm_package.'&create_result=2' );
	}
}

/**********************************
 * 
 * YES SAS - Your English Solution
 * Author : Rocky SIGNAVONG
 * Created Date : 08/03/11
 * Modified Date : 08/03/11
 * Version : 1.0
 * Function : insallitem
 * Description : La fonction va recuperer tous les zip du repertoire de depot de zip et les charger dans docebo
 * 
 ***********************************/

function insallitem() {
	//checkPerm( 'view', FALSE, 'storage' );
	set_time_limit(0);
	
	require_once(_base_.'/lib/lib.upload.php');
	require_once(_base_.'/addons/pclzip/pclzip.lib.php');
	require_once(dirname(__FILE__).'/RendererDb.php');
	require_once(dirname(__FILE__).'/CPManager.php');
	
	//back_url is not necessary for us
	$back_url = urldecode($_POST['back_url']);
	$zipdir = str_replace ( '\\', '/', '/doceboLms/scorm_ftp_zip/');
	foreach ( scandir($GLOBALS['where_files_relative'].$zipdir) as $file) {
		
		$info = pathinfo($file);
		if ($file == '.' || $file == '..' || is_dir($file) || $info['extension'] != 'zip')
			continue;
			
		$zipfile = $GLOBALS['where_files_relative'].$zipdir.$file;
		
		// there is a file?
		if(!file_exists($zipfile)) {
			$_SESSION['last_error'] = _FILEUNSPECIFIED;
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
		$path = str_replace ( '\\', '/', '/doceboLms/'.Get::sett('pathscorm'));
		$savefile = getLogUserId().'_'.rand(0,100).'_'.time().'_'.$file;
		if(!file_exists ($GLOBALS['where_files_relative'].$path.$savefile)) {
			/*sl_open_fileoperations();
			if(!sl_upload($_FILES['attach']['tmp_name'], $path.$savefile)) {
			//if( !move_uploaded_file($_FILES['attach']['tmp_name'], $GLOBALS['where_files_relative'].$path.$savefile ) ) {
				sl_close_fileoperations();
				*/
			if (!copy($zipfile, $GLOBALS['where_files_relative'].$path.$savefile)) {				
				$_SESSION['last_error'] = _ERROR_UPLOAD;
				echo "Copy zip error: " . $zipfile . " to " . $GLOBALS['where_files_relative'].$path.$savefile;
				//Util::jump_to( ''.$back_url.'&create_result=0' );
				exit();
			} else {
				if (md5_file($zipfile) != md5_file($GLOBALS['where_files_relative'].$path.$savefile)) {
					echo "MD5 Error: " . $GLOBALS['where_files_relative'].$path.$savefile . " & filename: " . $file;
					exit();
				}
			}
			
		} else {
			sl_close_fileoperations();
			$_SESSION['last_error'] = _ERROR_UPLOAD;
			echo "file exists: ". $savefile;
			exit();
			//Util::jump_to( ''.$back_url.'&create_result=0' );
		}
		
		// compute filepath
		$filepath = $path.$savefile.STRPOSTCONTENT;
		// extract zip file
		$zip = new PclZip($path.$savefile);
		
		// check disk quota --------------------------------------------------
		if(isset($_SESSION['idCourse']) && defined("LMS")) {
	
			$zip_content = $zip->listContent();
			$zip_extracted_size = 0;
			while(list(, $file_info) = each($zip_content)) {
	
				$zip_extracted_size += $file_info['size'];
			}
	
			$quota = $GLOBALS['course_descriptor']->getQuotaLimit();
			$used = $GLOBALS['course_descriptor']->getUsedSpace();
	
			if(Util::exceed_quota(false, $quota, $used, $zip_extracted_size)) {
	
				sl_unlink($path.$savefile);
				$_SESSION['last_error'] = Lang::t('_QUOTA_EXCEDED');
				Util::jump_to( ''.$back_url.'&create_result=0' );
			}
			$GLOBALS['course_descriptor']->addFileToUsedSpace(false, $zip_extracted_size);
		}
		// extract zip ------------------------------------------------------
		
		usleep(100);
		$test = $zip->extract(PCLZIP_OPT_PATH, $filepath );
		if( $zip->errorCode() != PCLZIP_ERR_NO_ERROR && $zip->errorCode() != 1 ) {
			sl_unlink($path.$savefile);
			$_SESSION['last_error'] = _ERROR_UPLOAD;
			sl_close_fileoperations();
			echo "Extract zip error: ". $filepath;
			exit();
			//Util::jump_to( ''.$back_url.'&create_result=0' );
		}
	
		/* remove zip file */
		sl_unlink($path.$savefile);
		sl_close_fileoperations();
	
		
		$cpm = new CPManager();
		// try to open content package
		if( !$cpm->Open( $GLOBALS['where_files_relative'].$filepath ) ) {
			$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
		// and parse the manifest
		if( !$cpm->ParseManifest() ) {
			$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
	
		// create entry in content package table
		$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package"
				." (idpackage,idProg,path,defaultOrg,idUser,scormVersion) VALUES"
				." ('".addslashes($cpm->identifier)
				."','0','".$savefile.STRPOSTCONTENT
				."','".addslashes($cpm->defaultOrg)
				."','".(int)getLogUserId()
				."','".$cpm->scorm_version
				."')";
		if( !($result = sql_query($query)) ) {
			$_SESSION['last_error'] = _OPERATION_FAILURE;
			Util::jump_to( ''.$back_url.'&create_result=0' );
		}
		
		$idscorm_package = mysql_insert_id();
		
		// create the n entries in resources table
		for( $i = 0; $i < $cpm->GetResourceNumber(); $i++ ) {
			$info = $cpm->GetResourceInfo( $cpm->GetResourceIdentifier($i) );
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources (idsco,idscorm_package,scormtype,href)"
					." VALUES ('".addslashes($info['identifier'])."','"
					.(int)$idscorm_package."','"
					.$info['scormtype']."','"
					.addslashes($info['href']) ."')";
	
			$result = sql_query( $query );
	
			if(!$result){
				$_SESSION['last_error'] = _OPERATION_FAILURE;
				Util::jump_to( ''.$back_url.'&create_result=0' );
			} else if(mysql_affected_rows() == 0)  {
				$_SESSION['last_error'] = _OPERATION_FAILURE;
				Util::jump_to( ''.$back_url.'&create_result=0' );
			}
		}
	
		$rdb = new RendererDb($GLOBALS['dbConn'], $GLOBALS['prefix_lms'], $idscorm_package);
		$orgElems = $cpm->orgElems;
		// save all organizations
		for( $iOrg = 0; $iOrg < $orgElems->getLength(); $iOrg++ ) {
			$org = $orgElems->item($iOrg);
			$cpm->RenderOrganization( $org->getAttribute('identifier'), $rdb );
		}
		
		if( $_POST['lesson_resources'] == 'import' || $cpm->defaultOrg == '-resource-' ) {
			// save flat organization with resources
			$cpm->RenderOrganization( '-resource-', $rdb );
		}
		
		$so = new Scorm_Organization( $cpm->defaultOrg, $idscorm_package, $GLOBALS['dbConn'] );
		if( $so->err_code > 0 ) {
			$_SESSION['last_error'] = 'Error: '. $so->getErrorText() . ' [' . $so->getErrorCode() .']';
			Util::jump_to( ''.$back_url.'&create_result=0' );
		} else {
			//Util::jump_to( ''.$back_url.'&id_lo='.$so->idscorm_organization.'&create_result=1' );
			//Util::jump_to( ''.$back_url.'&id_lo='.$idscorm_package.'&create_result=2' );
			
			// get title of the content
			//area title
			$query = "SELECT title ".
				" FROM ".$GLOBALS['prefix_lms']."_scorm_organizations ".
				" WHERE idscorm_package = ".(int)$idscorm_package." AND org_identifier = 'OrgName' ";
			$scorm_title = mysql_fetch_row(mysql_query($query));
			
			// create entry in content package table
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_repo"
					." (idRepo, idParent, path, lev, title, objectType, idResource, idCategory, idUser, idAuthor, version, difficult, description, language, resource, objective, dateInsert) VALUES"
					." ('NULL','0','/root/".$scorm_title[0]
					."','1','".$scorm_title[0]
					."','scormorg','".(int)$idscorm_package
					."','0','','".(int)getLogUserId()
					."','".$cpm->scorm_version
					."','','','','', '',NOW())";
			if( !($result = sql_query($query)) ) {
				$_SESSION['last_error'] = _OPERATION_FAILURE;
				Util::jump_to( ''.$back_url.'&create_result=0' );
			}
			
		}
	}
	
	Util::jump_to( ''.$back_url.'&create_result=2' );
}

/**********************************
 * 
 * YES SAS - Your English Solution
 * Author : Polo
 * Created Date : 22/03/11
 * Modified Date : 22/03/11
 * Version : 1.0
 * Function : synchroallitem
 * Description : La fonction lié chaque paquet scorm à un cours
 * 
 ***********************************/

function synchroallitem()
{
	set_time_limit(0);
	
	$corresp = array();
	$increments = array();
	
	// On construit le tableau de correspondance code <=> idCategory + gestion de l'incrément
	$query = "SELECT idCategory, code FROM %adm_group_category";
	$result = sql_query($query);
	while($row = mysql_fetch_object($result))
	{
	    $corresp[$row->idCategory] = $row->code;
	    if(!isset($increments[$row->idCategory])) $increments[$row->idCategory] = 0;
	}
	
	$query = "SELECT * FROM ".$GLOBALS['prefix_lms']."_repo repo WHERE idResource NOT IN (SELECT idResource FROM ".$GLOBALS['prefix_lms']."_organization)";
	$result = sql_query($query);
	
	$man_course = new Man_Course();
	
	while($row = mysql_fetch_object($result))
	{
		$course_info = array(
			'code' 			=> $row->title,
			'name' 			=> $row->title,
			'description' 	=> '',
			'lang_code'		=> getLanguage(),
			'course_type' 	=> 'elearning',
			'show_rules' 	=> 0,
			'status' 		=> 2,
			'idCategory' 	=> 0,
			'direct_play' => 1
		);
		
		if($row->idResource > 0)
		{
			$s = "SELECT path FROM ".$GLOBALS['prefix_lms']."_scorm_package WHERE idscorm_package=".$row->idResource;
			$line = mysql_fetch_object(sql_query($s));
			
			$path = $line->path;
			foreach($corresp as $idCategory => $name)
			{
				if(stripos($path, $name) !== false)
				{
				    // On assigne la catégorie
					$course_info['idCategory'] = $idCategory;
					
					// On assigne le numéro d'incrément
					if(isset($increments[$idCategory]))
					{
					    $increments[$idCategory]++;
					    $course_info['increment'] = $increments[$idCategory];
					}
				}
			}
		}
		
		$id_course = $man_course->addCourse($course_info);
		if($id_course == false) continue;
		
		$level_idst =& DoceboCourse::createCourseLevel($id_course);
		if($level_idst == false) continue;
		
		$id_main = $man_course->addMainToCourse($id_course, Lang::t('_PREASSESSMENT_MENU', 'preassessment', 'framework'));
		
		$re = true;
		$perm = array();
		$perm['7'] = array('view');
		$perm['6'] = array('view');
		$perm['3'] = array('view');
		$re &= $man_course->addModuleToCourse($id_course, $level_idst, $id_main, false, 'organization', 'organization', $perm );

		$perm = array();
		$perm['7'] = array('view', 'home', 'lesson', 'public');
		$perm['6'] = array('view', 'home', 'lesson', 'public');
		$re &= $man_course->addModuleToCourse($id_course, $level_idst, $id_main, false, 'storage', 'display', $perm);
		
		$perm = array();
		$perm['7'] = array('view', 'mod');
		$perm['6'] = array('view', 'mod');
		$re &= $man_course->addModuleToCourse($id_course, $level_idst, $id_main, false, 'coursereport', 'coursereport', $perm );
		
		if($re)
		{
			$query = "INSERT INTO %lms_test ( author, title, description ) VALUES "."( '".$row->idAuthor."', '".$row->title."', '' )";
			
			if(!sql_query($query)) return false;
			$id_test = sql_insert_id();
			
			if($id_test)
			{
				require_once(_lms_.'/modules/organization/orglib.php');
				$odb= new OrgDirDb($id_course);
				
				// modif du 31/03/2011 par polo pour corriger le décalage dans les ID
				$odb->addItem(0, $row->title, 'scormorg', $row->idResource, '0', '0', $row->idAuthor, $row->version, '_DIFFICULT_MEDIUM', '', '', '', '', date('Y-m-d H:i:s'));
				//$odb->addItem(0, $row->title, 'scormorg', $id_test, '0', '0', $row->idAuthor, $row->version, '_DIFFICULT_MEDIUM', '', '', '', '', date('Y-m-d H:i:s'));
			}
			else return false;
		}
	}
}

/**********************************
 * 
 * YES SAS - Your English Solution
 * Author : Polo
 * Created Date : 22/03/11
 * Modified Date : 22/03/11
 * Version : 1.0
 * Function : createcontent
 * Description : La fonction crée les groupes de code et règles d'inscription
 * 
 ***********************************/

function createcontent()
{
	set_time_limit(0);
	
	// On supprime les règles existantes
	$sql = "DELETE FROM %adm_rules WHERE rule_type='group'";
	sql_query($sql);
	
	// On vide les tables qui doivent être vidées
	$truncates = array(
		'%adm_rules_entity',
		'%adm_code_groups',
		'%adm_code_course',
	    '%adm_code',
	    '%lms_product'
		);
	
	foreach($truncates as $table)
	{
		$sql = "TRUNCATE ".$table;
		sql_query($sql);
	}
	
	// On récupère la liste des catégories (Basics, Essential... etc)
	$sql = "SELECT * FROM %adm_group_category ORDER BY idCategory";
	$rows = sql_query($sql);
	
	while($row = mysql_fetch_object($rows))
	{
		// Pour chaque règle d'inscription, on récupère les 5 premiers cours
		$sql = "SELECT idCourse FROM %lms_course WHERE idCategory=".$row->idCategory." ORDER BY idCourse LIMIT 5";
		$result = sql_query($sql);
		
		$courseList = array();
		while($r = mysql_fetch_object($result))
		{
			$courseList[] = $r->idCourse;
		}
		
		// On crée la règle d'inscription pour cette catégorie
		$query = "INSERT INTO %adm_rules (title, lang_code, rule_type, creation_date, rule_active, course_list) VALUES ( '".$row->code."', 'all', 'group', '".date('Y-m-d H:i:s')."', 1, '[".implode(',', $courseList)."]') ";
		sql_query($query);
		
		$rule_id = mysql_insert_id();
		
		// Pour chaque règle, on associe le bon groupe d'utilisateur
		$sql = "INSERT INTO %adm_rules_entity (id_rule, id_entity, course_list) VALUES ($rule_id, ".$row->idGroup.", '[".implode(',', $courseList)."]')";
		sql_query($sql);
		
		// On crée les groupes de codes (1 / mois / catégorie)
		for($i=0 ; $i<=8 ; $i++)
		{
			// On crée le groupe de code
			$sql = "INSERT INTO %adm_code_groups VALUES ('', '".$row->code." (mois ".($i+1).")', '".$row->code.($i+1)."')";
			sql_query($sql);
			
			$group_id = mysql_insert_id();
			
			// Pour chaque groupe de code on associe 20 cours
			$begin = ($i*20) + 5; // On oublie les 5 cours gratuits de chaque groupe
			$sql = "SELECT idCourse FROM %lms_course WHERE idCategory=".$row->idCategory." ORDER BY idCourse LIMIT $begin, 20";
			$result = sql_query($sql);
			
			while($r = mysql_fetch_object($result))
			{
				$sql = "INSERT INTO %adm_code_course (idCodeGroup, idCourse) VALUES ($group_id, ".$r->idCourse.")";
				sql_query($sql);
			}
		}
		
		$productData = array(
		    1 => array(
		    	'amount_ht' => 16.7224,
		        'abo_months' => 3,
		        'course_count' => 60,
		        'discount_rate' => 0,
		        'offer_text' => '1&euro; / jour',
		        'description' => "Un entretien à préparer ? un voyage en perspective ? Cette option vous permet de progresser rapidement",
		    ),
		    
		    2 => array(
		    	'amount_ht' => 15.0502,
		        'abo_months' => 6,
		        'course_count' => 120,
		        'discount_rate' => 0.10,
		        'offer_text' => '0.90&euro; / jour',
		        'description' => "Vous prévoyez une évolution professionnelle ? N'hésitez pas à pratiquer pendant 6 mois ou plus pour une progression durable.",
		    ),
		    
		    3 => array(
		    	'amount_ht' => 12.5418,
		        'abo_months' => 9,
		        'course_count' => 180,
				'discount_rate' => 0.10,
		        'offer_text' => '0.75&euro; / jour',
		        'description' => "Travaillez votre anglais à un rythme mesuré, en fonction de vos projets à court et moyen terme.",
		    ),
		);
		
		$corresp = array();
		
		for($i=1 ; $i<=3 ; $i++)
		{
		    $query = "INSERT INTO %lms_product (idCategory, code, title, description, amount_ht, abo_months, course_count, offer_text, discount_rate, crea) VALUES (".$row->idCategory.", '".$row->code."', '".$row->code." : ".$productData[$i]['abo_months']." mois de Microlearning', \"".$productData[$i]['description']."\", ".$productData[$i]['amount_ht'].", ".$productData[$i]['abo_months'].", ".$productData[$i]['course_count'].", '".$productData[$i]['offer_text']."', '".$productData[$i]['discount_rate']."', UNIX_TIMESTAMP())";
		    sql_query($query);
		}
	}
}

/**********************************
 * 
 * YES SAS - Your English Solution
 * Author : Polo
 * Created Date : 22/03/11
 * Modified Date : 22/03/11
 * Version : 1.0
 * Function : cleanitems
 * Description : La fonction supprime tous les paquets et cours correspondant
 * 
 ***********************************/

function cleanitems()
{
    set_time_limit(0);
	$model = new CourseAlms();
	
	$courses = sql_query("SELECT idCourse FROM %lms_course");
	while($row = mysql_fetch_object($courses))
	{
		$model->delCourse($row->idCourse);
	}
	
	$items = sql_query("SELECT idscorm_organization, idscorm_package FROM ".$GLOBALS['prefix_lms']."_scorm_organizations");
	while($row = sql_fetch_object($items))
	{
		_scorm_deleteitem($row->idscorm_package, $row->idscorm_organization, true);
	}
	
	$tables = array(
		'learning_course',
		'learning_courseuser',
		'learning_organization',
		'learning_repo',
		'learning_scorm_items',
		'learning_scorm_items_track',
		'learning_scorm_organizations',
		'learning_scorm_package',
		'learning_scorm_resources',
		'learning_scorm_tracking',
		'learning_scorm_tracking_history',
		'learning_test',
		);
	
	foreach($tables as $table)
	{
		sql_query("TRUNCATE ".$table);
	}
	
	return true;
}

function joinitems()
{
    set_time_limit(0);
    $users = array('polo', 'test');
    
    require_once(_lms_.'/lib/lib.subscribe.php');
    $subscribe = new CourseSubscribe_Management();
    
    foreach($users as $userid)
    {
        $sql = "SELECT idst FROM %adm_user WHERE userid LIKE '/".$userid."' LIMIT 1";
        $row = sql_fetch_object(sql_query($sql));
        
        if($row !== false)
        {
            $idst = (int) $row->idst;
            
            $sql = "SELECT idCourse FROM %lms_course ORDER BY idCourse";
            $result = sql_query($sql);
            
            while($row = sql_fetch_object($result))
        	{
        		$query_control = "SELECT COUNT(*) FROM %lms_courseuser WHERE idCourse=".$row->idCourse." AND idUser=".$idst;
        
        		list($control) = sql_fetch_row(sql_query($query_control));
        
        		if($control == 0)
        		{
        			$subscribe->subscribeUser($idst, $row->idCourse, '3');
        		}
        	}
        }
    }
}

function infoallitem()
{
	set_time_limit(0);
	
	$path = '../files/doceboLms/'.Get::sett('pathscorm');
	$sql = 'SELECT idscorm_package, path FROM '.$GLOBALS['prefix_lms'].'_scorm_package';
	$packages = mysql_query($sql);
	
	while($row = mysql_fetch_object($packages))
	{
		if(is_dir($path.$row->path))
		{
			require($path.$row->path.'/manifest.php');
			if(isset($Description) && strlen($Description) > 0)
			{
				$Title = addslashes($Title);
				$Description = addslashes($Description);
				
				$s = 'UPDATE '.$GLOBALS['prefix_lms'].'_course SET description="'.$Description.'", name="'.$Title.'" WHERE idCourse = (SELECT idCourse FROM '.$GLOBALS['prefix_lms'].'_organization WHERE idResource="'.$row->idscorm_package.'")';
				mysql_query($s);
			}
		}
	}
}

function moditem($object_item) {
	checkPerm( 'view', FALSE, 'storage' );

	$lang =& DoceboLanguage::createInstance('scorm', 'lms');

	//area title
	$query = "SELECT idOrg ".
		" FROM ".$GLOBALS['prefix_lms']."_organization ".
		" WHERE idResource = ".(int)$object_item->id." AND objectType = 'scormorg' ";
	list($id_reference) = mysql_fetch_row(mysql_query($query));

	require_once(_lib_.'/lib.table.php');
	$tb = new Table();
	$h_type = array('', '');
	$h_content = array(
		$lang->def('_NAME'),
		$lang->def('_LINK')
	);

	$tb->setColsStyle($h_type);
	$tb->addHead($h_content);

	$qry = "SELECT item_identifier, idscorm_resource, title ".
		" FROM ".$GLOBALS['prefix_lms']."_scorm_items ".
		" WHERE idscorm_organization = ".(int)$object_item->id."".
		" ORDER BY idscorm_item ";

	$res = mysql_query($qry);
	while($row = mysql_fetch_row($res)) {

		$line = array();
		$line[] = $row[2];
		$line[] = ( $row[1] != 0
			? Get::abs_path('lms').'/index.php?id_course='.$_SESSION['idCourse'].'&amp;act=playsco&amp;&courseid='.$_SESSION['idCourse'].'&amp;id_item='.$id_reference.'&amp;chapter='.$row[0].''
			: '' );
		$tb->addBody($line);

	}

	cout( getTitleArea($lang->getLangText('_SCORMIMGSECTION'), 'scorm')
		.'<div class="std_block">'
		.getBackUi($object_item->back_url.'&amp;edit_result=0', $lang->getLangText('_BACK_TOLIST' ))
		.$tb->getTable()
		.'</div>' );
}

function play($aidResource, $aidReference, $aback_url, $aautoplay, $aplayertemplate, $environment = 'course_lo' ) {
	
	$GLOBALS['idReference'] = $aidReference;
	$GLOBALS['idResource'] = $aidResource;
	$GLOBALS['back_url'] = $aback_url;
	$GLOBALS['autoplay'] = $aautoplay;
	$GLOBALS['playertemplate'] = $aplayertemplate;
	$GLOBALS['environment'] = $environment;
	require( dirname(__FILE__) . '/scorm_frameset.php' );
}

function _scorm_deleteitem( $idscorm_package, $idscorm_organization, $erasetrackcontent = FALSE ) {
	
	/* remove items: based on organizations */
	//$rs = sql_query("SELECT idscorm_organization FROM ".$prefix."_scorm_organizations WHERE idscorm_package=".$idscorm_package);			
	//while(list($idscorm_organization) = sql_fetch_row($rs)) {
	if( $erasetrackcontent ) { // selected tracking remove
		$rsItems = sql_query( "SELECT idscorm_item FROM ".$GLOBALS['prefix_lms']."_scorm_items WHERE idscorm_organization=".$idscorm_organization );
		while(list($idscorm_item) = sql_fetch_row($rsItems)) {
			sql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_tracking WHERE idscorm_resource=".$idscorm_item);
		}
	}
	sql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_items WHERE idscorm_organization=".$idscorm_organization);

	//}
	
	/* remove organizations */
	sql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_organizations WHERE idscorm_organization=".$idscorm_organization);

	// detect if there are other organization in package
	$rs = sql_query("SELECT idscorm_organization FROM ".$GLOBALS['prefix_lms']."_scorm_organizations WHERE idscorm_package=".$idscorm_package);

	if( mysql_num_rows( $rs ) == 0 ) {
		$rs = sql_query("SELECT path FROM ".$GLOBALS['prefix_lms']."_scorm_package WHERE idscorm_package='".(int)$idscorm_package."'")
			or die(mysql_error());
	
		list($path) = sql_fetch_row($rs);
		$scopath = str_replace ( '\\', '/', $GLOBALS['where_files_relative'].'/doceboLms/'.Get::sett('pathscorm'));
		/* remove all zip directory */
		if(file_exists($scopath.$path)) {
			
			/* if is the only occurrence of path in db delete files */
			$rs = sql_query(	"SELECT idscorm_package FROM ".$GLOBALS['prefix_lms']."_scorm_package"
								." WHERE path = '".$path."'");
			if( mysql_num_rows( $rs ) == 1 ) {
				
				$size = Get::dir_size($scopath.$path);
			
				require_once( dirname(__FILE__). '/scorm_utils.php'); // for del tree
				delDirTree($scopath.$path);

				if(isset($_SESSION['idCourse']) && defined("LMS")) {
					$GLOBALS['course_descriptor']->subFileToUsedSpace(false, $size);
				}
			}
		}
	
		/* remove resources */
		sql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_resources WHERE idscorm_package=".$idscorm_package);
	
		
		/* remove packages */
		sql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_scorm_package WHERE idscorm_package=".$idscorm_package);
	}
	
}

function _scorm_copyitem( $idscorm_package, $idscorm_organization ) {
	funAccess( 'additem','NEW', false, 'scorm' );
	
	require_once(_base_.'/lib/lib.upload.php');
	require_once(dirname(__FILE__) .'/RendererDb.php');
	require_once(dirname(__FILE__) .'/CPManager.php');
	
	if( ($rs = sql_query("SELECT path FROM ".$GLOBALS['prefix_lms']."_scorm_package "
							."WHERE idscorm_package='"
							.(int)$idscorm_package."'")) === FALSE ) {
		$_SESSION['last_error'] = _OPERATION_FAILURE.': '.mysql_error();
		return FALSE;
	}

	list($path) = sql_fetch_row($rs);
	$scopath = str_replace ( '\\', '/', $GLOBALS['where_files_relative'].'/doceboLms/'.Get::sett('pathscorm'));
	
	/* copy all zip directory */
	/* remove copy - use same files 
	$fname = explode ( '_', $path, 4);
	$savefile = $_SESSION['sesUser'].'_'.rand(0,100).'_'.time().'_'.$fname[3];
	$filepath = $pathscorm.$savefile;

	if(file_exists($path)) {
		if( !sl_copyr($path, $filepath ) ) {
			$_SESSION['last_error'] = _ERRORCOPYFILE;
			return FALSE;
		}
	}
	*/
	/* copy package record */
	$rs_package = sql_query(	"SELECT idpackage,idProg,'".$path."',defaultOrg,idUser "
								." FROM ".$GLOBALS['prefix_lms']."_scorm_package "
								." WHERE idscorm_package='".(int)$idscorm_package."'");
	
	$arr_package = sql_fetch_row($rs_package);
	for( $i = 0; $i < count($arr_package); $i++)
		$arr_package[$i] = addslashes($arr_package[$i]);
	sql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package "
				." (idpackage,idProg,path,defaultOrg,idUser) VALUES "
				."('".implode("','", $arr_package)."')");
		
	
/*	sql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_package "
				." (idpackage,idProg,path,defaultOrg,idUser) " 
				." SELECT idpackage,idProg,'".$path."',defaultOrg,idUser "
				."   FROM ".$GLOBALS['prefix_lms']."_scorm_package "
				."  WHERE idscorm_package='".(int)$idscorm_package."'");*/
	
	$new_idscorm_package = mysql_insert_id();
	
	/* copy resources */
	$rs_resources = sql_query(" SELECT idsco,'".$new_idscorm_package."',scormtype,href "
								."  FROM ".$GLOBALS['prefix_lms']."_scorm_resources "
								." WHERE idscorm_package='".(int)$idscorm_package."'");
								
	while( $arr_resource = sql_fetch_row($rs_resources) ) {
		for( $i = 0; $i < count($arr_resource); $i++)
			$arr_resource[$i] = addslashes($arr_resource[$i]);
		sql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources "
					." (idsco,idscorm_package,scormtype,href) VALUES "
					."('".implode("','", $arr_resource)."')");
	}
	/*sql_query("INSERT INTO ".$GLOBALS['prefix_lms']."_scorm_resources "
				." (idsco,idscorm_package,scormtype,href) "
				." SELECT idsco,'".$new_idscorm_package."',scormtype,href "
				."   FROM ".$GLOBALS['prefix_lms']."_scorm_resources "
				."  WHERE idscorm_package='".(int)$idscorm_package."'");*/

	$cpm = new CPManager();
	// try to open content package
	if( !$cpm->Open( $scopath.$path ) ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		return FALSE;
	}
	
	// and parse the manifest
	if( !$cpm->ParseManifest() ) {
		$_SESSION['last_error'] = 'Error: '. $cpm->errText . ' [' . $cpm->errCode .']';
		return FALSE;
	}


	$rdb = new RendererDb($GLOBALS['dbConn'], $GLOBALS['prefix_lms'], $new_idscorm_package);
	/*$orgElems = $cpm->orgElems;
	// save all organizations
	foreach( $orgElems as $org )
		$cpm->RenderOrganization( $org->get_attribute('identifier'), $rdb );*/
	
	list($org_identifier) = sql_fetch_row(sql_query(
				"SELECT org_identifier FROM ".$GLOBALS['prefix_lms']."_scorm_organizations "
				." WHERE idscorm_organization='".(int)$idscorm_organization."'"));
	
	$cpm->RenderOrganization( $org_identifier, $rdb );
	
	// save flat organization with resources
	//$cpm->RenderOrganization( '-resource-', $rdb );
	
	$so = new Scorm_Organization( addslashes($org_identifier), $new_idscorm_package, $GLOBALS['dbConn'] );
	if( $so->err_code > 0 ) {
		$_SESSION['last_error'] = 'Error: '. $so->getErrorText() . ' [' . $so->getErrorCode() .']';
		return FALSE;
	} else {
		return $so->idscorm_organization;
	}
}


if( isset( $GLOBALS['op'] ) ) {
	
	switch($GLOBALS['op']) {
		/*case "display": {
			display();
		}; break;*/
		case "additem" : {
			additem();
		};break;
		case "insitem" : {
			insitem();
		};break;
		case "insallitem" : {
			insallitem();
		};break;
		case "infoallitem" : {
			infoallitem();
		};break;
		case "createcontent" : {
			createcontent();
		};break;
		case "cleanitems" : {
			cleanitems();
		};break;
		case "synchroallitem" : {
			synchroallitem();
		};break;
		case "joinitems" : {
			joinitems();
		};break;
		case "deleteitem": {
			deleteitem();
		}; break;
		case "dodelete": {
			dodelete();
		}; break;
		case "category" : {
			category();
		};break;
		case "categorysave": {
			categorysave();
		};break;
		case "play": {
			play();
		};break;
		case "tree": {
			require( dirname(__FILE__) . '/scorm_page_tree.php');
		};break;
		case "head": {
			require( dirname(__FILE__) . '/scorm_page_head.php');
		};break;
		case "body": {
			require( dirname(__FILE__) . '/scorm_page_body.php');
		};break;
		case "scoload": {
			require( dirname(__FILE__) . '/soaplms.php');
		};break;
	}
}
 
?>
