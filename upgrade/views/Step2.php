<h2><?php echo Lang::t('_TITLE_STEP2'); ?></h2>

<?php $php_conf = ini_get_all(); ?>
<?php $cl = $this->checkRequirements(); ?>

<h3><?php echo Lang::t('_VERSION'); ?></h3>
<p class="microform">
	<b><label for="start_version"><?php echo Lang::t('_START'); ?> : </label></b><?php echo $this->versionList(); ?><br />
	<b><?php echo Lang::t('_END'); ?> : </b><?php echo $GLOBALS['cfg']['endversion']; ?>
</p>

<?php if ($cl['config'] == 'err'): ?>
<script type="text/javascript">
	YAHOO.util.Event.onDOMReady(function() {
		disableBtnNext(true);
	});
</script>
<ul class="info">
	<li class="err"><span><?php echo Lang::t('_INVALID_CONFIG_FILE'); ?></span></li>
</ul>
<?php endif; ?>

<br/>
<h3><?php echo Lang::t('_SERVERINFO'); ?></h3>
<ul class="info">
	<li><?php echo Lang::t('_SERVER_SOFTWARE'); ?>: <span><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span></li>
	<li class="<?php echo $cl['php']; ?>"><?php echo Lang::t('_PHPVERSION'); ?>: <span><?php echo phpversion(); ?></span></li>
	<li class="<?php echo $cl['mysql']; ?>"><?php echo Lang::t('_MYSQLCLIENT_VERSION'); ?>: <span><?php echo mysql_get_client_info(); ?></span></li>
	<li class="<?php echo $cl['ldap']; ?>"><?php echo Lang::t('_LDAP'); ?>: <span><?php echo (extension_loaded('ldap') ? _ON : _OFF.' '._ONLY_IF_YU_WANT_TO_USE_IT); ?></span></li>
</ul>


<h3><?php echo Lang::t('_PHPINFO'); ?></h3>
<ul class="info">
	<li><?php echo Lang::t('_MAGIC_QUOTES_GPC'); ?>: <?php echo ($php_conf['magic_quotes_gpc']['local_value'] != '' ? _ON : _OFF); ?></li>
	<li><?php echo Lang::t('_SAFEMODE'); ?>: <?php echo ($php_conf['safe_mode']['local_value'] != '' ? _ON : _OFF); ?></li>
	<li><?php echo Lang::t('_REGISTER_GLOBALS'); ?>: <?php echo ($php_conf['register_globals']['local_value'] != '' ? _ON : _OFF); ?></li>
	<li><?php echo Lang::t('_UPLOAD_MAX_FILESIZE'); ?>: <?php echo $php_conf['upload_max_filesize']['local_value']; ?></li>
	<li><?php echo Lang::t('_POST_MAX_SIZE'); ?>: <?php echo $php_conf['post_max_size']['local_value']; ?></li>
	<li><?php echo Lang::t('_MAX_EXECUTION_TIME'); ?>: <?php echo $php_conf['max_execution_time']['local_value'].'s'; ?></li>
</ul>

<?php echo $this->checkFolderPerm(); ?>