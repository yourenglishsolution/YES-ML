<div class="yui-g">
	<div class="yui-u first">
		<div class="inline_block_big">
			<h2 class="heading"><?php echo Lang::t('_QUICK_LINKS', 'dashboard'); ?></h2>
			<div class="content">
				<div class="yui-u first">
					<?php
						$_can_view_block = (bool)($permissions['view_user'] && ($permissions['view_user'] || $permissions['add_user'] || $permissions['mod_user']));
						if ($_can_view_block):
					?>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_USERS', 'standard'); ?></h3>
						<ul class="link_list">
							<?php if ($permissions['view_user']): ?>
							<li>
								<a id="quicklinks_users_status" href="ajax.adm_server.php?r=adm/dashboard/user_status_dialog"><?php echo Lang::t('_PROFILE', 'profile'); ?></a>
							</li>
							<?php endif; ?>
							<?php if ($permissions['mod_user']): ?>
							<li>
								<a id="quicklinks_users_chgpwd" href="ajax.adm_server.php?r=adm/usermanagement/changepwd"><?php echo Lang::t('_CHANGEPASSWORD', 'profile'); ?></a>
							</li>
							<?php endif; ?>
							<?php if ($permissions['add_user']): ?>
							<li>
								<a id="quicklinks_users_create" href="ajax.adm_server.php?r=adm/usermanagement/create"><?php echo Lang::t('_NEW_USER', 'admin_directory'); ?></a>
							</li>
							<li>
								<a id="quicklinks_users_import" href="index.php?r=adm/usermanagement/importusers&id=0"><?php echo Lang::t('_ORG_CHART_IMPORT_USERS', 'organization_chart'); ?></a>
							</li>
							<?php endif; ?>
						</ul>
						<?php
							if ($permissions['view_user']) {
								$this->widget('dialog', array(
									'id' => 'status_user_dialog',
									'width' => "700px",
									'dynamicContent' => true,
									'ajaxUrl' => 'this.href',
									'dynamicAjaxUrl' => true,
									'constrainToViewport' => false,
									'callback' => 'Dashboard.userStatusCallback',
									'renderEvent' => 'Dashboard.userStatusRenderEvent',
									'confirmOnly' => true,
									'fixedCenter' => false,
									'callEvents' => array(
										array('caller' => 'quicklinks_users_status', 'event' => 'click')
									)
								));
							}

							if ($permissions['mod_user']) {
								$this->widget('dialog', array(
									'id' => 'chgpwd_user_dialog',
									'dynamicContent' => true,
									'constrainToViewport' => false, //this dialog may be too big to be constrained
									'ajaxUrl' => 'this.href',
									'dynamicAjaxUrl' => true,
									'fixedCenter' => false,
									'callback' => 'Dashboard.changePasswordCallback',
									'renderEvent' => 'Dashboard.changePasswordRenderEvent',
									'callEvents' => array(
										array('caller' => 'quicklinks_users_chgpwd', 'event' => 'click')
									)
								));
							}

							if ($permissions['add_user']) {
								$this->widget('dialog', array(
									'id' => 'create_user_dialog',
									'dynamicContent' => true,
									'ajaxUrl' => 'this.href',
									'dynamicAjaxUrl' => true,
									'fixedCenter' => false,
									'constrainToViewport' => false, //this dialog may be too big to be constrained
									'renderEvent' => 'Dashboard.createUserRenderEvent',
									'callback' => 'Dashboard.createUserCallback',
									'callEvents' => array(
										array('caller' => 'quicklinks_users_create', 'event' => 'click')
									)
								));

								//orgchart tree in the user creation popup, to be activated at runtime
								$this->widget('tree', array(
									'id' => 'createuser_orgchart_tree',
									'ajaxUrl' => 'ajax.adm_server.php?r=adm/usermanagement/gettreedata_create',
									'treeClass' => 'DialogOrgFolderTree',
									'treeFile' => Get::rel_path('adm').'/views/usermanagement/orgchartfoldertree.js',
									'initialSelectedNode' => 0,
									'show' => 'tree',
									'useCheckboxes' => 'true',
									'initialSelectorData' => array(0),
									'setSelectedNodeOnServer' => false,
									'hiddenSelection' => 'orgchart_hidden_selection',
									'runtime' => true,
									'languages' => array(
										'_ROOT' => Get::sett('title_organigram_chart', Lang::t('_ORG_CHART', 'organization_chart') ),
										'_LOADING' => Lang::t('_LOADING', 'standard')
									)
								));
							}
						?>
					</div>
					<?php
						endif;
					?>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_CERTIFICATE', 'menu'); ?></h3>
						<ul class="link_list">
							<li>
								<a id="find_certificate" href="ajax.adm_server.php?r=adm/dashboard/certificate"><?php echo Lang::t('_MONITOR_PRINT_CERTIFICATE_STATUS', 'dashboard'); ?></a>
							</li>
						</ul>
						<?php
							$this->widget('dialog', array(
								'id' => 'certificate_dialog',
								'width' => "700px",
								'dynamicContent' => true,
								'ajaxUrl' => 'this.href',
								'dynamicAjaxUrl' => true,
								'fixedCenter' => false,
								'constrainToViewport' => true,
								'callback' => 'Dashboard.certificateCallback',
								'renderEvent' => 'Dashboard.certificateRenderEvent',
								'callEvents' => array(
									array('caller' => 'find_certificate', 'event' => 'click')
								)
							));
						?>
					</div>
					<?php if (count($reports) > 0) : ?>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_REPORT', 'report'); ?></h3>
						<?php

						echo Form::openForm('show_report_created_form', 'index.php?modname=report&amp;op=show_results&amp;of_platform=lms');
						echo '<p><label for="report_created_sel">'.Lang::t('_SELECT', 'report').'</label></p>';
						echo Form::getInputDropdown('dropdown', 'report_created_sel', 'idrep', $reports, false, '').'<br />';
						echo Form::getButton('show_report_created', false, Lang::t('_VIEW', 'standard'));
						echo Form::getButton('export_report_created', false, Lang::t('_EXPORT', 'standard'), false, '', true, false);
						echo Form::closeform();

						$this->widget('dialog', array(
							'id' => 'export_report_dialog',
							'dynamicContent' => true,
							'ajaxUrl' => 'function() { return "ajax.adm_server.php?r=adm/dashboard/exportformat&id_report="+YAHOO.util.Dom.get("report_created_sel").value; }',
							'dynamicAjaxUrl' => true,
							'fixedCenter' => false,
							'directSubmit' => true,
							'hideAfterSubmit' => true,
							'callback' => 'Dashboard.exportCallback',
							'callEvents' => array(
								array('caller' => 'export_report_created', 'event' => 'click')
							)
						));

						?>
					</div>
					<?php endif; ?>
				</div>
				<div class="yui-u">
					<?php
						$_can_view_block = (bool)($permissions['view_course'] && ($permissions['add_course'] || $permissions['subscribe']));;
						if ($_can_view_block):
					?>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_COURSES', 'course'); ?></h3>
						<ul class="link_list">
							<?php if ($permissions['subscribe']): ?>
							<li>
								<a id="quicklinks_courses_subscr" href="ajax.adm_server.php?r=alms/subscription/fast_subscribe_dialog"><?php echo Lang::t('_SUBSCRIBE', 'dashboard'); ?></a>
							</li>
							<?php endif; ?>
							<?php if ($permissions['add_course']): ?>
							<li>
								<a id="quicklinks_courses_create" href="index.php?r=alms/course/newcourse"><?php echo Lang::t('_NEW_COURSE', 'dashboard'); ?></a>
							</li>
							<?php endif; ?>
						</ul>
						<?php
							if ($permissions['subscribe']) {
								$this->widget('dialog', array(
									'id' => 'subscr_course_dialog',
									'dynamicContent' => true,
									'ajaxUrl' => 'this.href',
									'dynamicAjaxUrl' => true,
									'fixedCenter' => false,
									'callback' => 'Dashboard.subscribeToCourseCallback',
									'renderEvent' => 'Dashboard.subscribeToCourseRenderEvent',
									'callEvents' => array(
										array('caller' => 'quicklinks_courses_subscr', 'event' => 'click')
									)
								));
							}
						?>
					</div>
					<?php endif; ?>
					<?php
						$_can_view_block = (bool)(($permissions['view_communications'] && ($permissions['add_communications']) || ($permissions['view_games'] && $permissions['add_games'])));
						if ($_can_view_block):
					?>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_CONTENTS', 'dashboard'); ?></h3>
						<ul class="link_list">
							<?php if ($permissions['add_communications']): ?>
							<li>
								<a href="index.php?r=alms/communication/add"><?php echo Lang::t('_COMMUNICATIONS', 'communication'); ?></a>
							</li>
							<?php endif; ?>
							<?php if ($permissions['add_games']): ?>
							<li>
								<a href="index.php?r=alms/games/add"><?php echo Lang::t('_CONTEST', 'games'); ?></a>
							</li>
							<?php endif; ?>
						</ul>
					</div>
					<?php endif; ?>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_DETAILS', 'dashboard'); ?></h3>
						<ul class="link_list">
							<li>
								<?php
									echo Lang::t('_VERSION', 'dashboard').': <b>'.$version['db_version'].'</b>'
										.$version['string'];
								?>
							</li>
							<li>
								<a id="quicklinks_diagnostic" href="ajax.adm_server.php?r=adm/dashboard/diagnostic_dialog">
									<?php
										if ($diagnostic_problem) echo '<span class="ico-sprite fd_notice"><span>'.Lang::t('_WARNING', 'standard').'</span></span>&nbsp;';
										echo Lang::t('_SERVERINFO', 'configuration');
									?>
								</a>
							</li>
						</ul>
						<?php
						$this->widget('dialog', array(
							'id' => 'tech_info_dialog',
							'dynamicContent' => true,
							'ajaxUrl' => 'this.href',
							'width' => '700px',
							'dynamicAjaxUrl' => true,
							'fixedCenter' => false,
							'constrainToViewport' => false, //this dialog may be too big
							'callback' => 'Dashboard.diagnosticCallback',
							'renderEvent' => 'Dashboard.diagnosticRenderEvent',
							'confirmOnly' => true,
							'callEvents' => array(
								array('caller' => 'quicklinks_diagnostic', 'event' => 'click')
							)
						));
						?>
					</div>
					<div class="block_spacer">
						<h3><?php echo Lang::t('_SUPPORT_SITE', 'dashboard'); ?></h3>
						<ul class="link_list">
							<li>
								<a href="http://www.docebo.com/"
									 onclick="window.open(this.href); return false;"
									 onkeypress="window.open(this.href); return false;"
									 title="Docebo <?php echo Lang::t('_OPEN_IN_NEW_WINDOW', 'dashboard'); ?>">
									<?php echo Lang::t('_URL_COMPANY', 'dashboard'); ?>
								</a>
							</li>
							<li>
								<a href="http://www.docebo.org/"
									 onclick="window.open(this.href); return false;"
									 onkeypress="window.open(this.href); return false;"
									 title="Docebo Community <?php echo Lang::t('_OPEN_IN_NEW_WINDOW', 'dashboard'); ?>">
									<?php echo Lang::t('_URL_SUPPORTLMS', 'dashboard'); ?>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="nofloat"></div>
				<!-- Social list -->
				<div class="block_spacer">
					<h3><?php echo Lang::t('_FOLLOW_US', 'dashboard'); ?></h3>
					<ul class="link_list_inline align-center">
						<li>
							<a href="http://www.facebook.com/pages/Docebo/204396855124?v=info"
								 onclick="window.open(this.href); return false;"
								 title="Facebook">
								<?php echo Get::img('social/facebook.png', Lang::t('_FACEBOOK', 'standard')).' '.Lang::t('_FACEBOOK', 'standard'); ?>
							</a>
						</li>
						<li>
							<a href="http://www.linkedin.com/groupInvitation?groupID=35592&sharedKey=46E02D0F9DBB"
								 onclick="window.open(this.href); return false;"
								 title="Linkedin">
								<?php echo Get::img('social/linkedin.png', Lang::t('_LINKEDIN', 'standard')).' '.Lang::t('_LINKEDIN', 'standard'); ?>
							</a>
						</li>
						<li>
							<a href="http://www.twitter.com/@docebo"
								 onclick="window.open(this.href); return false;"
								 title="Twitter">
								<?php echo Get::img('social/twitter.png', Lang::t('_TWITTER', 'standard')).' '.Lang::t('_TWITTER', 'standard'); ?>
							</a>
						</li>
						<li>
							<a href="http://docebo-elearning-<?php echo Lang::get() == 'italian' ? 'ita' : 'eng'; ?>.blogspot.com/"
								 onclick="window.open(this.href); return false;"
								 title="Company Blog">
								<?php echo Get::img('social/blogspot.png', Lang::t('_COMPANY_BLOG', 'standard')).' '.Lang::t('_COMPANY_BLOG', 'standard'); ?>
							</a>
						</li>
						<li>
							<a href="http://docebo-org-<?php echo Lang::get() == 'italian' ? 'ita' : 'eng'; ?>.blogspot.com/"
								 onclick="window.open(this.href); return false;"
								 title="Community Blog">
								<?php echo Get::img('social/blogspot.png', Lang::t('_COMMUNITY_BLOG', 'standard')).' '.Lang::t('_COMMUNITY_BLOG', 'standard'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="yui-u">
		<div class="inline_block_big">
			<h2 class="heading"><?php echo Lang::t('_USERS', 'dashboard'); ?></h2>
			<div class="content">
				<div class="yui-g">
					<div class="yui-u first">
						<ul class="link_list">
							<li><?php echo Lang::t('_TOTAL_USER', 'dashboard').': <b id="total_users_count">'.($user_stats['all'] - 1).'</b>;'; ?></li>
							<li><?php echo Lang::t('_SUSPENDED', 'dashboard').': <b>'.$user_stats['suspended'].'</b>;'; ?></li>
							<?php echo ($can_approve ? '<li>'.Lang::t('_WAITING_USERS', 'dashboard').': <b>'.$user_stats['waiting'].'</b>;</li>' : '' ); ?>
							<li><?php echo Lang::t('_REG_LASTSEVENDAYS', 'dashboard').':<b>'.$user_stats['register_7d'].'</b>;'; ?></li>
							<li><?php echo Lang::t('_INACTIVE_USER', 'dashboard').': <b>'.$user_stats['inactive_30d'].'</b>;'; ?></li>
							<li><?php echo Lang::t('_ONLINE_USER', 'dashboard').': <b>'.$user_stats['now_online'].'</b>;'; ?></li>
						</ul>
					</div>
					<div class="yui-u">
						<ul class="link_list">
							<li><?php echo Lang::t('_SUPERADMIN_USER', 'dashboard').': <b>'.$user_stats['superadmin'].'</b>;'; ?></li>
							<li><?php echo Lang::t('_ADMIN_USER', 'dashboard').': <b>'.$user_stats['admin'].'</b>;'; ?></li>
							<li><?php echo Lang::t('_PUBLIC_ADMIN_USER', 'dashboard').': <b>'.$user_stats['public_admin'].'</b>;'; ?></li>
						</ul>
					</div>
				</div>
				<div class="nofloat"></div>
				<!-- <div style="text-align:center;margin:1em;padding:1em;">
					<p>Statistics: <span id="users_chart_buttons"></span></p>
					<div id="users_chart_display"></div>
				</div> --><br />
				<div id="users_tabview">
				</div>
			</div>
		</div>
		<div class="inline_block_big">
			<h2 class="heading"><?php echo Lang::t('_COURSES', 'dashboard'); ?></h2>
			<div class="content">
				<div class="yui-g">
					<div class="yui-u first">
						<ul class="link_list">
							<li><?php echo Lang::t('_TOTAL_COURSE', 'dashboard').': <b>'.$course_stats['total'].'</b>;'; ?></li>
							<li><?php echo Lang::t('_ACTIVE_COURSE', 'dashboard').': <b>'.$course_stats['active'].'</b>;'; ?></li>
							<li><?php echo Lang::t('_ACTIVE_SEVEN_COURSE', 'dashboard').': <b>'.$course_stats['active_seven'].'</b>;'; ?></li>
						</ul>
					</div>
					<div class="yui-u">
						<ul class="link_list">
							<li>
								<?php echo Lang::t('_TOTAL_SUBSCRIPTION', 'dashboard').': <b>'.$course_stats['user_subscription'].'</b>;'; ?>
							</li>
							<?php
								echo (checkPerm('moderate', true, 'course', 'lms') ? '<li>'.Lang::t('_WAITING_SUBSCRIPTION', 'dashboard').': <b>'.$course_stats['user_waiting'].'</b>;</li>' : '');
								$month_1 = (int)date("m");
								$month_2 = (($month_1 + 12 - 2) % 12) + 1;
								$month_3 = (($month_1 + 12 - 3) % 12) + 1;
							?>
							<li>
								<?php echo Lang::t('_SUBSCRIPTION', 'course').'&nbsp;'.Lang::t('_MONTH_'.((int)$month_1<10 ? '0' : '').(int)$month_1).': <b>'.$course_months_stats['month_subs_1'].'</b>;'; ?>
							</li>
							<li>
								<?php echo Lang::t('_SUBSCRIPTION', 'course').'&nbsp;'.Lang::t('_MONTH_'.((int)$month_2<10 ? '0' : '').(int)$month_2).': <b>'.$course_months_stats['month_subs_2'].'</b>;'; ?>
							</li>
							<li>
								<?php echo Lang::t('_SUBSCRIPTION', 'course').'&nbsp;'.Lang::t('_MONTH_'.((int)$month_3<10 ? '0' : '').(int)$month_3).': <b>'.$course_months_stats['month_subs_3'].'</b>;'; ?>
							</li>
						</ul>
					</div>
				</div>
				<div class="nofloat"></div>
				<!-- <div style="text-align:center;margin:1em;padding:1em;">
					<p>Statistics:&nbsp;<span id="courses_chart_buttons"></span></p>
					<div id="users_chart_display"></div>
				</div> --><br />
				<div id="courses_tabview"></div>
			</div>
		</div>
	</div>
</div>
<div class="nofloat"></div>
<script type="text/javascript">
YAHOO.util.Event.onDOMReady( function() {
	Dashboard.drawTabView("users_tabview", [
		{label: "<?php echo Lang::t('_ACCESSES', 'dashboard'); ?>", content: "user_accesses_chart", active: true},
		{label: "<?php echo Lang::t('_TOTAL_USER', 'dashboard'); ?>", content: "user_registrations_chart", active: false}
	]);
	Dashboard.drawChart("user_accesses_chart", <?php echo $userdata_accesses; ?>, "<?php echo Lang::t('_ACCESSES', 'dashboard'); ?>", '<?php echo $lang_dir; ?>');
	Dashboard.drawChart("user_registrations_chart", <?php echo $userdata_registrations; ?>, "<?php echo Lang::t('_TOTAL_USER', 'dashboard'); ?>", '<?php echo $lang_dir; ?>');
});

YAHOO.util.Event.onDOMReady( function() {
	Dashboard.drawTabView("courses_tabview", [
		{label: "<?php echo Lang::t('_USER_STATUS_SUBS', 'dashboard'); ?>", content: "courses_subscriptions_chart", active: false},
		{label: "<?php echo Lang::t('_USER_STATUS_BEGIN', 'dashboard'); ?>", content: "courses_startattendings_chart", active: false},
		{label: "<?php echo Lang::t('_USER_STATUS_END', 'dashboard'); ?>", content: "courses_completed_chart", active: true}
	]);
	Dashboard.drawChart("courses_subscriptions_chart", <?php echo $coursedata_subscriptions; ?>, "<?php echo Lang::t('_USER_STATUS_SUBS', 'dashboard'); ?>", '<?php echo $lang_dir; ?>');
	Dashboard.drawChart("courses_startattendings_chart", <?php echo $coursedata_startattendings; ?>, "<?php echo Lang::t('_USER_STATUS_BEGIN', 'dashboard'); ?>", '<?php echo $lang_dir; ?>');
	Dashboard.drawChart("courses_completed_chart", <?php echo $coursedata_completed; ?>, "<?php echo Lang::t('_USER_STATUS_END', 'dashboard'); ?>", '<?php echo $lang_dir; ?>');
});
</script>