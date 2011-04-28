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
<? if($mode == 'completed') { // Cours terminé ?>
<div class="block<?=($alter%2 == 0 ? ' greyBlock' : '')?><?=($alter == $count ? ' lastBlock' : '')?>">
	<div class="image_block">
		<img src="<?php echo Get::tmpl_path(); ?>/style/images/content_pict3.png" alt="" />
	</div>
	<div class="desc">
		<span class="title title1">Cours n°<? echo $course['increment']; ?></span>
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
		<span class="title title1">Cours n°<? echo $course['increment']; ?></span>
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
		<span class="title title1">Cours n°<? echo $course['increment']; ?></span>
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