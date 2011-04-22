<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo Layout::title(); ?></title>
		
		<?php echo Layout::zone('meta'); ?>
		<?php echo Layout::accessibility(); ?>
		
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="fr" />
		
		<link rel="stylesheet" type="text/css" href="<?php echo Layout::path(); ?>/style/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Layout::path(); ?>/style/styles.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Layout::path(); ?>/style/slide.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Layout::path(); ?>/style/command.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Layout::path(); ?>/style/formalize.css" />
		
		<!-- MOOTOOLS -->
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/mootools.js" /></script>
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/mootools-more.js" /></script>
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/yesPopup.js" /></script>
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/noobslide.js" /></script>
		<script type="text/javascript" src="<?php echo Get::rel_path('base'); ?>/lib/js_utils.js"></script>
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/mootools.formalize.min.js" /></script>
		
		<?php echo Layout::zone('page_head'); ?>
		<?php echo Layout::rtl(); ?>
		
		<script type="text/javascript">
			window.addEvent('domready', function()
			{
				// Popup des pages de contenu
				var contentPopup = new yesPopup('.contentPopup', { popupClass:'popupContent' });
				var supportPopup = new yesPopup('.supportPopup', { popupClass:'popupSupport' });
			});
		</script>
	</head>
	<body>
		<?
			$action = $GLOBALS['modname'];
			if($action == '' && isset($_GET['r'])) $action = $_GET['r'];
		?>
		<div class="contener" id="<?=str_replace('/', '', $action)?>">
			<div class="header">
				<h1><a href="index.php?r=elearning/show&sop=unregistercourse"><span></span></a></h1>
				<a class="lang" href="index.php?modname=login&amp;op=logout"> 
					<div class="logout" >
						<?php echo Lang::t('_LOGOUT', 'standard'); ?>
					</div>
				</a>
				<div id="test" class="welcome">
					Welcome <?php echo ucfirst(Docebo::user()->getUserName()); ?>
				</div>
				<?/*<div class="lang">
					<span class="select-language"><?php echo Layout::change_lang(); ?></span>
					<!--<img src="./images/flag_fr.png" alt="" /> Francais-->
				</div>*/?>
			</div>
			
			<div class="clear"></div>
			
			<div class="menu">
				
				<ul>
					<?/*<li class="block"><a class="<?=($action == 'course_autoregistration' ? 'activ' : '')?>" href="index.php?modname=course_autoregistration&op=course_autoregistration&sop=unregistercourse">Activation</a></li>*/?>
					<li><a class="<?=($action == 'elearning/show' || $action == '' ? 'activ' : '')?>" href="index.php?r=elearning/show&sop=unregistercourse">Parcours YES</a></li>
					<li><a class="<?=($action == 'elearning/catalogue' ? 'activ' : '')?>" href="index.php?r=elearning/catalogue">Catalogue</a></li>
					<li><a class="contentPopup" href="index.php?modname=pages&op=account">Mon compte</a></li>
					<li><a class="supportPopup" href="index.php?modname=pages&op=support">Support</a></li>
				</ul>
			</div>
			
			<div class="clear"></div>
			
			<div class="content">
				<div class="left">
					<div class="left_c">
						<?php echo Layout::zone('content'); ?>
					</div>
				</div>
				<? if($action != 'elearning/catalogue') { ?>
				<? require($GLOBALS['where_lms'].'/../templates/yes/layout/rss.php'); ?>
				<div class="clear"></div>
				<? } ?>
			</div>
			
			<div class="footer">
				Copyright &copy; 2011 YES SAS All rights reserved.
				- <a href="">CGV</a>
				<? if(Docebo::user()->isAdmin()) { ?>- <a href="/doceboCore">Administration</a><? } ?>
			</div>
		</div>
		
		<!-- scripts -->
		<?php echo Layout::zone('scripts'); ?>
		<!-- debug -->
		<?php echo Layout::zone('debug'); ?>
		<!-- def_lang -->
		<?php echo Layout::zone('def_lang'); ?>
		<?php echo Layout::analytics(); ?>
	</body>
</html>