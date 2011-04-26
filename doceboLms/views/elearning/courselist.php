<?php

	function wrap($text, $length = 20)
	{
		$text = wordwrap($text, $length, ";");
		$pos = strpos($text, ";");
		if($pos > 0) $text = substr($text, 0, $pos).'...';
		return $text;
	}

	$page = (isset($_GET['page']) ? (int) $_GET['page'] : 1);
	$cpt = 1 + (($page - 1) * 10);
	$alter = 1;
	$count = count($courselist);
	
	$mode = (isset($GLOBALS['r'][1]) && $GLOBALS['r'][1] == 'completed' ? 'completed' : 'inprogress');
	$userCount = Docebo::user()->getCourseCount(($mode == 'inprogress' ? 0 : 1));
	$nbPages = ceil($userCount / 5);
	
?>
<? if($nbPages > 0) { ?>
<div class="pagination">
	<span>Page : </span>
	<select class="selectPage">
		<? for($i=1 ; $i<=$nbPages ; $i++) { ?>
		<option value="<?=$i?>"<?=($i == $page ? ' selected' : '')?>><?=$i?></option>
		<? } ?>
	</select>
</div>

<div class="clear"></div>
<? } ?>

<div class="block firstBlock">
	<div class="image_block">
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict1.png" alt="" width="164" height="166" />
	</div>
	<div class="infos">
		<span class="price">1&euro;/jour</span>
		<a href="index.php?r=elearning/catalogue" class="button"><span><span><span>Je m’abonne !</span></span></span></a>
	</div>
	<div class="desc">
		<span class="title">Je souhaite m’abonner au Microlearning YES</span>
		<ul>
			<li>Une bibliothèque d’exercices à votre niveau.</li>
			<li>Un entretien d’embauche en perspective ?</li>
			<li>Un voyage dans un pays anglophone ?</li>
		</ul>
		<div class="clear"></div>
		<span class="go"><a href="index.php?r=elearning/catalogue">Pratiquez l'anglais 3 minutes par jour !</a></span>
	</div>
	<div class="clear"></div>
</div>

<div class="clear"></div>

<? foreach($courselist as $course ) { ?>

<?php
	/*
	$category = '';
	if($course['idCategory'] > 0)
	{
		$db =& DbConn::getInstance();
		$sql = "SELECT * FROM ".$GLOBALS['prefix_lms']."_category WHERE idCategory = ".$course['idCategory']." LIMIT 0, 1";
		$category = $db->fetch_row($db->query($sql));
	}
	*/
?>

<? if($mode == 'completed') { // Cours terminé ?>
<div class="block<?=($alter%2 == 0 ? ' greyBlock' : '')?><?=($alter == $count ? ' lastBlock' : '')?>">
	<div class="image_block">
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict3.png" alt="" />
	</div>
	<div class="desc">
		<span class="title title1">Cours n°<? echo $course['idCourse']; ?></span>
		<span class="title title2" title="<? echo $course['name']; ?>"><? echo wrap($course['name'], 30); ?></span>
		<span class="help"><a href="index.php?modname=pages&op=help" class="ajaxPopup">Besoin d'aide</a></span>
		<div class="blockLine"></div>
		<div class="desc_l">
			<div class="desc_l_c">
				<p><? echo wrap($course['description'], 150)?></p>
			</div>
		</div>
		<div class="desc_r">
			<span class="paraButton">
				<a href="index.php?modname=course&op=aula&idCourse=<? echo $course['idCourse']?>" target="_blank" class="button">
					<span><span><span>Voir mes résultats</span></span></span>
				</a>
			</span>
		</div>
	</div>
</div>
<? } elseif($cpt == 1) { // Premier cours ?>
<div class="block<?=($alter%2 == 0 ? ' greyBlock' : '')?><?=($alter == $count ? ' lastBlock' : '')?>">
	<div class="image_block">
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict2.png" alt="" />
	</div>
	<div class="desc">
		<span class="title title1">Cours n°<? echo $course['idCourse']; ?></span>
		<span class="title title2" title="<? echo $course['name']; ?>"><? echo wrap($course['name'], 30); ?></span>
		<span class="help"><a href="index.php?modname=pages&op=help" class="ajaxPopup">Besoin d'aide</a></span>
		<div class="blockLine"></div>
		<div class="desc_l">
			<div class="desc_l_c">
				<p><? echo wrap($course['description'], 150)?></p>
			</div>
		</div>
		<div class="desc_r">
			<span class="paraButton">
				<a href="index.php?modname=course&op=aula&idCourse=<? echo $course['idCourse']?>" target="_blank" class="button">
					<span><span><span>Lancer mon cours</span></span></span>
				</a>
			</span>
		</div>
	</div>
</div>
<? } else { // Cours non-accessible ?>
<div class="block disabled<?=($alter%2 == 0 ? ' greyBlock' : '')?><?=($alter == $count ? ' lastBlock' : '')?> waitPopup">
	<div class="image_block">
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict2.png" alt="" />
	</div>
	<div class="desc">
		<span class="title title1">Cours n°<? echo $course['idCourse']; ?></span>
		<span class="title title2" title="<? echo $course['name']; ?>"><? echo wrap($course['name'], 30); ?></span>
		<span class="help"><a>Besoin d'aide</a></span>
		<div class="blockLine"></div>
		<div class="desc_l">
			<div class="desc_l_c">
				<p><? echo wrap($course['description'], 150)?></p>
			</div>
		</div>
		<div class="desc_r">
			<span class="paraButton">
				<a href="index.php?modname=pages&op=wait" class="button">
					<span><span><span>Lancer mon cours</span></span></span>
				</a>
			</span>
		</div>
	</div>
</div>
<? } ?>

<?/*<div class="block<?=($cpt > 1 && $course['user_status'] <= 1 ? ' disabled' : '')?><?=($alter%2 == 0 ? ' greyBlock' : '')?><?=($alter == $count ? ' lastBlock' : '')?>">
	<div class="image_block">
		<? if($course['user_status'] <= 1) { ?>
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict2.png" alt="" />
		<? } else { ?>
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict3.png" alt="" />
		<? } ?>
	</div>
	<div class="desc">
		<span class="title title1">Cours n°<? echo $course['idCourse']; ?></span>
		<span class="title title2" title="<? echo $course['name']; ?>"><? echo wrap($course['name'], 30); ?></span>
		<span class="help"><a href="index.php?modname=pages&op=help" class="ajaxPopup">Besoin d'aide</a></span>
		<div class="blockLine"></div>
		<div class="desc_l">
			<div class="desc_l_c">
				<p>
					<? echo wrap($course['description'], 150)?>
				</p>
			</div>
		</div>
		<div class="desc_r">
			<span class="paraButton">
				<?
				switch (true)
				{
					case ($cpt == 1 || $course['user_status'] > 1) :
					{
						?>
						<a target="_blank" href="index.php?modname=course&op=aula&idCourse=<? echo $course['idCourse']?>" class="button">
							<span><span><span><? echo $btnLabel; ?></span></span></span>
						</a>
						<?
						break;
					}
					case ($cpt > 1 ? ' disabled' : '') :
					{
						?>
						<a class="button waitPopup disabledLink">
							<span><span><span><? echo $btnLabel; ?></span></span></span>
						</a>
						<?
						break;
					}
					default :
					{
						?>
						<a href="index.php?modname=pages&op=wait" class="button waitPopup">
							<span><span><span><? echo $btnLabel; ?></span></span></span>
						</a>
						<?
						break;
					}
				}
				?>
			</span>
		</div>
	</div>
</div>
*/?>

<div class="clear"></div>
<?
		$cpt++;
		$alter++;
	} // Foreach end
?>

<? if($nbPages > 0) { ?>
<div class="pagination">
	<span>Page : </span>
	<select class="selectPage">
		<? for($i=1 ; $i<=$nbPages ; $i++) { ?>
		<option value="<?=$i?>"<?=($i == $page ? ' selected' : '')?>><?=$i?></option>
		<? } ?>
	</select>
</div>

<div class="clear"></div>
<? } ?>

<?/*
<?php $unsubscribe_call_arr = array(); ?>
<?php foreach( $courselist as $course ) : ?>

	<?php echo '<div class="dash-course '.
		($course['user_status'] < 1 ? 'status_subscribed' : 'status_begin').'">'; ?>

		<?php if($course['use_logo_in_courselist'] && $course['img_course']) : ?>
		<div class="logo_container">
			<img class="clogo"
				src="<?php echo $path_course.$course['img_course']; ?>"
				alt="<?php echo Util::purge($course['name']); ?>" />
		</div>
		<?php endif; ?>
		<?php if($course['use_logo_in_courselist'] && !$course['img_course']) : ?>
		<div class="logo_container">
			<img class="clogo cnologo"
				 src="<?php echo Get::tmpl_path().'images/course/course_nologo.png'; ?>"
				alt="<?php echo Util::purge($course['name']); ?>" />
		</div>
		<?php endif; ?>

		<div class="info_container">
		<h2>
			<?php if ($course['can_enter']['can']) { ?>
			<a href="index.php?modname=course&amp;op=aula&amp;idCourse=<?php echo $course['idCourse']; ?>">
				<?php echo ( $course['lang_code'] != 'none' ? Get::img('language/'.strtolower($course['lang_code']).'.png', $course['lang_code']) : '' ); ?>
				<?php echo $course['name']; ?>
			</a>
			<?php } else {
				echo Get::img('standard/locked.png', Lang::t('_'.strtoupper($course['can_enter']['reason']), 'standard'));
				echo ' '.$course['name'];
			}
			?>
		</h2>
		<p class="course_support_info">
			<?php
			echo Lang::t($this->ustatus[ $course['user_status'] ], 'course').''
				.Lang::t('_USER_LVL', 'course', array('[level]' => '<b>'.$this->levels[ $course['level'] ].'</b>'));
			?>
		</p>
		<?php if(!empty($access['expiring_in']) && $access['expiring_in'] < 30) : ?>
			<p class="course_support_info">
				<?php echo Lang::t('_EXPIRING_IN', 'course', array('[expiring_in]' => $access['expiring_in'])); ?>
			</p>
		<?php endif; ?>
		<p class="course_support_info">
			<?php if($course['code']) { ?><i style="font-size:.88em">[<?php echo $course['code']; ?>]</i><?php } ?>
		</p>

		<?php
			if (!empty($display_info) && isset($display_info[$course['idCourse']])) {
				echo '<p class="course_support_info">';
				echo '<ul class="action-list">';
				foreach ($display_info[$course['idCourse']] as $key => $info) {
					$_start_time = $info->start_date != "" && $info->start_date != "0000-00-00 00:00:00" ? Format::date($info->start_date, 'datetime') : "";
					$_end_time = $info->end_date != "" && $info->end_date != "0000-00-00 00:00:00" ? Format::date($info->end_date, 'datetime') : "";
					echo '<li style="width: 98%;">';//.($info->code != "" ? '['.$info->code.'] ' : "").$info->name.' '


					$start_date =$info->date_info['date_begin'];
					$end_date =$info->date_info['date_end'];
					$_start_time = $start_date != "" && $start_date != "0000-00-00 00:00:00" ? Format::date($start_date, 'datetime') : "";
					$_end_time = $end_date != "" && $end_date != "0000-00-00 00:00:00" ? Format::date($end_date, 'datetime') : "";

					echo '<b>'.Lang::t('_COURSE_BEGIN', 'certificate').'</b>: '.($_start_time ? $_start_time : "- ").'; '
						.'<b>'.Lang::t('_COURSE_END', 'certificate').'</b>: '.($_end_time ? $_end_time : "- ")."; ";


					echo ($info->date_info['location'] != "" ? '<b>'.Lang::t('_LOCATION', 'standard').'</b>: '.$info->date_info['location'] : "");


					echo "<br />".Lang::t('_COURSE_INTRO', 'course', array(
						'[course_type]'		=> $course['course_type'],
						'[enrolled]'		=> $info->enrolled,
						'[course_status]'	=> $info->status,
					));

					echo '</li>';
				}
				echo '</ul>';
				echo '</p>';
			}
		?>

		<?php
			$smodel = new SubscriptionAlms();
			if ($smodel->isUserWaitingForSelfUnsubscribe(Docebo::user()->idst, $course['idCourse'])) {
				echo '<p style="padding:.4em">'.Lang::t('_UNSUBSCRIBE_REQUEST_WAITING_FOR_MODERATION', 'course').'</p>';
			} else {

				//auto unsubscribe management: create a link for the user in the course block
				$_can_unsubscribe = ($course['auto_unsubscribe']==1 || $course['auto_unsubscribe']==2);
				$_date_limit = $course['unsubscribe_date_limit'] != "" && $course['unsubscribe_date_limit'] != "0000-00-00 00:00:00"
					? $course['unsubscribe_date_limit']
					: FALSE;
				echo '<!-- '.print_r($course['auto_unsubscribe'], true).' -->';
				echo '<!-- '.print_r($course['unsubscribe_date_limit'], true).' -->';
				if ($_can_unsubscribe):
		?>
		<p class="course_support_info">
			<?php if ($_date_limit !== FALSE && $_date_limit <= date("Y-m-d H:i:s")) {
				echo '';
			} else {

				$unsubscribe_call_arr[]=$course['idCourse'];

				?>

			<?php if ($dm->checkHasValidUnsubscribePeriod($course['idCourse'], Docebo::user()->getIdSt())): ?>
			<a id="self_unsubscribe_link_<?php echo $course['idCourse']; ?> " href="ajax.server.php?r=classroom/self_unsubscribe_dialog&amp;id_course=<?php echo $course['idCourse']; ?>"
				 title="<?php echo Lang::t('_SELF_UNSUBSCRIBE', 'course'); ?>">
				 <?php echo Lang::t('_SELF_UNSUBSCRIBE', 'course'); ?>
			</a>
			<?php
				if ($_date_limit) echo '&nbsp;('.Lang::t('_UNTIL', 'standard').' '.Format::date(substr($_date_limit, 0, 10), 'date').')';
			?>
			<?php endif; ?>
			<?php } //endif ?>
		</p>
		<?php
				endif;
			}
			unset($smodel);
		?>
		</div><!-- info container -->
	</div>

<?php endforeach; ?>

				  */?>