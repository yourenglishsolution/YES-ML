<br />

<script type="text/javascript">
	YAHOO.util.Event.onDOMReady(function()
	{
		var refreshTable = function()
		{
			var oDt = DataTable_admin_rules_table;
			var oState = oDt.getState();
			var request = oDt.get("generateRequest")(oState, oDt);
			var oCallback = {
				success : oDt.onDataReturnSetRows,
				failure : oDt.onDataReturnSetRows,
				argument : oState,
				scope : oDt
			};
			oDt.getDataSource().sendRequest(request, oCallback);
		};

		//add group event
		var add = YAHOO.util.Dom.getElementsByClassName('ico-wt-sprite subs_add');
		YAHOO.util.Event.addListener(add, "click", function(e)
		{
			YAHOO.util.Event.preventDefault(e);

			var confirm = function() { this.submit(); };
			var undo = function() { this.destroy(); };

			var buttons = [], body = '';
			buttons.push({text:"<?php echo Lang::t('_CONFIRM', 'standard'); ?>", handler: confirm, isDefault: true});
			buttons.push({text:"<?php echo Lang::t('_UNDO', 'standard'); ?>", handler: undo});

			var addDialog = new YAHOO.widget.Dialog("admin_rules_table_addDialog",
			{
				modal: true,
				close: true,
				visible: false,
				fixedcenter: true,
				constraintoviewport: true,
				draggable: true,
				hideaftersubmit: false,
				buttons: buttons
			});

			addDialog.hideEvent.subscribe(function(e, args)
			{
				YAHOO.util.Event.stopEvent(args[0]);
				this.destroy();
			}, addDialog);

			addDialog.callback =
			{
				success: function(oResponse)
				{
					var o = YAHOO.lang.JSON.parse(oResponse.responseText);
					if (o.success) {
						this.destroy();
						refreshTable();
					} else {
						setDialogErrorMessage(o.message ? o.message : "<?php echo Lang::t('_OPERATION_FAILURE', 'standard'); ?>");
					}
				},
				failure: function() { setDialogErrorMessage("<?php echo Lang::t('_CONNECTION_ERROR', 'standard'); ?>"); },
				scope: addDialog
			};

			addDialog.setHeader("<?php echo Lang::t('_LOADING', 'subscribe'); ?>");
			addDialog.setBody('<div id="admin_rules_table_addDialog"></div>'
				+'<div class="align_center"><img src="<?php echo Get::tmpl_path().'images/standard/loadbar.gif'; ?>" /></div>');
			addDialog.render(document.body);
			addDialog.show();
			var postdata = "";
			YAHOO.util.Connect.asyncRequest("POST", this.href, {
				success: function(o) {
					var res = YAHOO.lang.JSON.parse(o.responseText);
					if (res.success) {
						addDialog.setHeader(res.header);
						addDialog.setBody('<div id="admin_rules_table_addDialog"></div>'+res.body);
						addDialog.center();
						eval(res.script);
					} else {
						setDialogErrorMessage(res.message ? res.message : "<?php echo Lang::t('_OPERATION_FAILURE', 'standard'); ?>");
					}
				},
				failure: function() { setDialogErrorMessage("<?php echo Lang::t('_CONNECTION_ERROR', 'standard'); ?>"); }
			}, postdata);
		})
	})

function saveData(callback, newData)
{
	var new_value = newData;
	var old_value =  this.value;
	var datatable = this.getDataTable();
	var idst = this.getRecord().getData("idst");

	var myCallback =
	{
		success: function(o)
		{
			var r = YAHOO.lang.JSON.parse(o.responseText);
			if (r.success)
			{
				callback(true, stripSlashes(r.new_value));
			}
			else
			{
				callback(true, stripSlashes(r.old_value));
			}
		},
		failure:
		{
		}
	}

	var post =	"idst=" + idst
				+"&new_value=" + new_value
				+"&old_value=" + old_value;

	var url = "ajax.adm_server.php?r=adm/publicadminrules/saveData&";

	YAHOO.util.Connect.asyncRequest("POST", url, myCallback, post);
}
YAHOO.namespace("PublicAdminProfile");

var PublicAdminProfile = {
	beforeRenderEvent: function()
	{
		var special = YAHOO.util.Selector.query('a[id^=special_]');
		var lang = YAHOO.util.Selector.query('a[id^=lang_]');

		YAHOO.util.Event.purgeElement(special);
		YAHOO.util.Event.purgeElement(lang);
	},

	postRenderEvent: function()
	{
		var special = YAHOO.util.Selector.query('a[id^=special_]');

		YAHOO.util.Event.addListener(special, "click", function(e) {
			var oDialog = CreateDialog("special_dialog", {
				width: "700px",
				modal: true,
				close: true,
				visible: false,
				fixedcenter: true,
				constraintoviewport: false,
				draggable: true,
				hideaftersubmit: false,
				isDynamic: true,
				ajaxUrl: this.href,
				confirmOnly: false,
				renderEvent: function() {},
				destroyEvent: function() {},
				callback: function() {
					this.destroy();
				}
			});
			oDialog.call(this, e);
		});
	}
}
</script>
<?php

echo	getTitleArea(Lang::t('_PUBLIC_ADMIN_RULES', 'menu'))
		.'<div class="std_block">';

echo $result_message;

$add_url = 'ajax.adm_server.php?r=adm/publicadminrules/addGroup';

$rel_action = '<a class="ico-wt-sprite subs_add" href="'.$add_url.'"><span>'.Lang::t('_ADD', 'adminrules').'</span></a>';


$this->widget('table', array(
	'id'			=> 'admin_rules_table',
	'ajaxUrl'		=> 'ajax.adm_server.php?r=adm/publicadminrules/getGroups&',
	'rowsPerPage'	=> Get::sett('visuItem', 25),
	'startIndex'	=> 0,
	'results'		=> Get::sett('visuItem', 25),
	'sort'			=> 'groupid',
	'dir'			=> 'asc',
	'columns'		=> array(
		array('key' => 'groupid', 'label' => Lang::t('_NAME', 'adminrules'), 'sortable' => true, 'editor' => 'new YAHOO.widget.TextboxCellEditor({asyncSubmitter: saveData})'),
		array('key' => 'special', 'label' => '<span class="ico-sprite subs_conf"><span>'.Lang::t('_SPECIAL_SETTING', 'adminrules').'</span></span>', 'className' => 'img-cell'),
		array('key' => 'menu', 'label' => '<span class="ico-sprite subs_elem"><span>'.Lang::t('_EDIT_SETTINGS', 'adminrules').'</span></span>', 'className' => 'img-cell'),
		//array('key' => 'admin_manage', 'label' => '<span class="ico-sprite subs_users"><span>'.Lang::t('_MANAGE_SUBSCRIPTION', 'adminrules').'</span></span>', 'className' => 'img-cell'),
		array('key' => 'del', 'label' => Get::img('standard/delete.png', Lang::t('_DEL', 'adminrules')), 'formatter'=>'doceboDelete', 'className' => 'img-cell')
	),
	'fields'		=> array('idst', 'groupid', 'special', 'menu', 'admin_manage', 'del'),
	'stdSelection' => false,
	'rel_actions' => $rel_action,
	'delDisplayField' => 'groupid',
	'events' => array(	'beforeRenderEvent' => 'PublicAdminProfile.beforeRenderEvent',
						'postRenderEvent' => 'PublicAdminProfile.postRenderEvent')
));

?>

</div>