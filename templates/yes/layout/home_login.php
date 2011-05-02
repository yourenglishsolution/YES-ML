<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo Layout::lang_code(); ?>">
	<head>
		<title><?php echo Layout::title(); ?></title>
		<link rel="shortcut icon" href="<?php echo Layout::path(); ?>/style/images/favicon.ico" type="image/x-con" />
		<link rel="icon" href="<?php echo Layout::path(); ?>/style/images/favicon.ico" type="image/x-con" />
		<link rel="stylesheet" type="text/css" href="<?php echo Layout::path(); ?>/style/login.css" />
		
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/mootools.js" /></script>
		<script type="text/javascript" src="<?php echo Layout::path(); ?>/js/mootools-more.js" /></script>
		
		<?php echo Layout::zone('meta'); ?>
		<?php echo Layout::rtl(); ?>
		<?php echo Layout::accessibility(); ?>
		
		<script type="text/javascript">
			window.addEvent('domready', function()
			{
				// Gestion des évènements sur les champs du formulaire
				var login = document.id('loginField');
				var passw = document.id('passwordField');
				login.addEvent('focus', function() { this.select(); });
				login.addEvent('blur', function() { if(this.value == '') this.value = 'Identifiant'; });
				passw.addEvent('focus', function() { this.select(); });
				passw.addEvent('blur', function() { if(this.value == '') this.value = 'mot de passe'; });
				login.focus();
			});
		</script>
	</head>
	<body>
		<div class="contener">
			<div class="header">
				<h1><span></span></h1>
			</div>
			<div class="content">
				<h2>ACCÉDER À MON COMPTE</h2>
				<p class="acceder">Pour accéder à votre compte YES Microlearning, veuillez entrez votre identifiant et votre mot de passe.</p>
				<?php echo LoginLayout::service_msg(); ?>
				<?php echo LoginLayout::login_form(); ?>
				<div class="footer_c">
					Copyright © 2011 YES SAS All rights reserved.
				</div>
			</div>
		</div>
		<?php echo Layout::analytics(); ?>
	</body>
</html>