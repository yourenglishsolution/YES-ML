<?php
// start buffer
ob_start();

include('bootstrap.php');

StepManager::checkStep();
Lang::setLanguage();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Docebo installer</title> 
		<!-- 
		<link rel="stylesheet" type="text/css" href="../templates/standard/yui-skin/button.css" />
		<script type="text/javascript" src=".././lib/js_utils.js"></script> 
		-->
		<link rel="stylesheet" type="text/css" href="docebo_40/templates/standard/style/reset-fonts-grids.css" />
		<link rel="stylesheet" type="text/css" href="docebo_40/templates/standard/yui-skin/button.css" />
		<?php echo getZoneContent('page_head'); ?>
	</head> 
	<body class="yui-skin-docebo yui-skin-sam">
		<?php echo Form::openForm('main_form', 'index.php'); ?>
		<div class="shadow">
			<div class="install_container">
				<div class="install_hd">
					<div class="ibd">
						<h1><?php echo Lang::t('_INSTALLER_TITLE'); ?></h1>
						<b><?php echo StepManager::getCurrentStep().' / '.StepManager::getTotalStep(); ?></b>
					</div>
				</div>
				<div class="install_bd">
					<div class="install_shoulder">
						<img class="logo" src="../templates/standard/images/company_logo.png" alt="Docebo e-learning logo" />
						<img class="flux" src="./templates/standard/images/left_shoulder.jpg" alt="Flux" />
					</div>
					<div class="install_content">
						<div class="ibd">
						<?php echo getZoneContent('main'); ?>
						<?php StepManager::loadCurrentStep(); ?>
						</div>
					</div>
				</div>
				<div class="install_ft" style="position: relative;">
					<div id="loading_box" style="visibility: hidden; position: absolute; left: 240px; top: 6px; text-align: left; padding: 0.3em;">
					<img src="templates/standard/images/loading.gif" alt="loading" style="vertical-align: middle;" />
					<?php echo Lang::t('_LOADING'); ?>...
					</div>
					<div id="warn_msg_box" style="visibility: hidden; position: absolute; right: 160px; top: 6px; min-width: 400px; text-align: left; padding: 0.3em; background:#ffc;border:1px solid #fc3;">
					<img src="templates/standard/images/warning.png" alt="warning" style="vertical-align: middle;" />
					<span id="warn_txt"></span>
					</div>
					<div class="ibd">
						<span id="my_button" class="yui-button">
							<span class="first-child">
								<button id="btn_next" type="submit" style="vertical-align:middle;"><?php echo Lang::t('_NEXT'); ?> &raquo;</button>
							</span>
						</span>
					</div>
				</div>
			</div>
		</div>
		<?php echo Form::getHidden('submit_form', 'submit_form', 1); ?>
		<?php echo Form::getHidden('current_step', 'current_step', StepManager::getCurrentStep()); ?>
		<?php echo Form::closeForm(); ?>
	</body> 
</html>


<?php

// flush buffer
$contents =ob_get_contents();
ob_clean();
echo $GLOBALS['page']->render($contents);
?>