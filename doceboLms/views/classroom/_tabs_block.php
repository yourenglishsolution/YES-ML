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
					'active' => 'classroom'
				));
				?>
			</div>

		</div>
	</div>
	<div class="nofloat"></div>
</div>

<?php
$prop =array(
	'id' => 'self_unsubscribe_dialog',
	'dynamicContent' => true,
	'ajaxUrl' => 'this.href',
	'dynamicAjaxUrl' => true,
	'callEvents' => array()
);
$this->widget('dialog', $prop);
?>

<script type="text/javascript">
  var tabView = new YAHOO.widget.TabView();

	function unsubscribeClick() {
		var nodes = YAHOO.util.Selector.query('a[id^=self_unsubscribe_link_]');
		YAHOO.util.Event.on(nodes, 'click', function (e) { 
			YAHOO.util.Event.preventDefault(e);
			CreateDialog("self_unsubscribe_dialog", {
				width: "700px",
				modal: true,
				close: true,
				visible: false,
				fixedcenter: false,
				constraintoviewport: false,
				draggable: true,
				hideaftersubmit: false,
				isDynamic: true,
				ajaxUrl: this.href,
				confirmOnly: true,
				callback: function() {
					this.destroy();
				}
			}).call(this, e);
		});
	}

	var mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_ALL_OPEN', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=classroom/all&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addClass('first');
	mytab.addListener('contentChange', unsubscribeClick);
	tabView.addTab(mytab);

	<?php if($this->isTabActive('new')): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_NEW', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=classroom/new&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', unsubscribeClick);
	tabView.addTab(mytab);
	<?php endif; ?>

	<?php if($this->isTabActive('inprogress')): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_USER_STATUS_BEGIN', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=classroom/inprogress&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', unsubscribeClick);
	tabView.addTab(mytab);
	<?php endif; ?>

	<?php if($this->isTabActive('completed')): ?>
	mytab = new YAHOO.widget.Tab({
	    label: '<?php echo Lang::t('_COMPLETED', 'course'); ?>',
	    dataSrc: 'ajax.server.php?r=classroom/completed&rnd=<?php echo time(); ?>',
	    cacheData: true
	});
	mytab.addListener('contentChange', unsubscribeClick);
	tabView.addTab(mytab);
	<?php endif; ?>

	tabView.appendTo('tab_content');
	tabView.set('activeIndex', 0);
</script>