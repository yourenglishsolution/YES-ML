<div class="yui-t6">
	<div class="yui-b">
		<?php
		$this->widget('lms_block', array(
			'zone' => 'right',
			'link' => 'elearning/show',
			'block_list' => $block_list
		));
		?>
	</div>
	<div id="yui-main">
		<div class="yui-b">

			<div style="margin:1em;">
				<?php
				$this->widget('lms_tab', array(
					'active' => 'elearning'
				));
				?>
			</div>

		</div>
	</div>
	<div class="nofloat"></div>
</div>
<script type="text/javascript">
	var lb = new LightBox();
	lb.back_url = 'index.php?r=elearning/show&sop=unregistercourse';
    var tabView = new YAHOO.widget.TabView();
	var mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_ALL_OPEN', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=elearning/all&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', lb.init);
	tabView.addTab(mytab, 0);
	<?php if($this->isTabActive('new')): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_NEW', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=elearning/new&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', lb.init);
	tabView.addTab(mytab, 1);
	<?php endif; ?>

	<?php if($this->isTabActive('inprogress')): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_USER_STATUS_BEGIN', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=elearning/inprogress&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', lb.init);
	tabView.addTab(mytab, 2);
	<?php endif; ?>

	<?php if($this->isTabActive('completed')): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_COMPLETED', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=elearning/completed&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', lb.init);
	tabView.addTab(mytab, 3);
	<?php endif; ?>

	<?php if($this->isTabActive('suggested') && false): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_SUGGESTED', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=elearning/suggested&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', lb.init);
	tabView.addTab(mytab, 4);
	<?php endif; ?>
	tabView.appendTo('tab_content');
	tabView.getTab(0).addClass('first');
	tabView.set('activeIndex', 0);
</script>