<?php
if(!$id_date) {
	Get::title(array(
		'index.php?r='.$this->link_course.'/show' => Lang::t('_COURSES', 'course'),
		Lang::t('_SUBSCRIBE', 'subscribe').' : '.$course_name
	));
} else {
	Get::title(array(
		'index.php?r='.$this->link_course.'/show' => Lang::t('_COURSE', 'course'),
		'index.php?r='.$this->link_classroom.'/classroom&amp;id_course='.$id_course.'' => Lang::t('_CLASSROOM', 'course'),
		Lang::t('_SUBSCRIBE', 'subscribe').' : '.$course_name
	));
}
?>
<div class="std_block">
<p id="fast_subscribe_result" class="container-feedback" style="visibility:hidden;"><span class="ico-wt-sprite fd_info"></span></p>
<?php echo $back_link; ?>
<div class="quick_search_form qsf_left">
	<div>
		<?php
			echo '<label for="fast_subscribe">'.Lang::t('_SUBSCRIBE', 'subscribe').'</label>:&nbsp;';
			echo Form::getInputTextfield("search_t", 'fast_subscribe', 'fast_subscribe', '', '', 255, '');
			echo Form::getButton("fast_subscribe_b", "fast_subscribe_b", Lang::t('_SUBSCRIBE', 'standard'), "plus_b");
			echo '<div id="fast_subscribe_container"></div>';
			echo Form::getHidden('fast_subscribe_idst', 'fast_subscribe_idst', '0');
		?>
	</div>
</div>
<div class="quick_search_form">
	<div>
		<?php
			echo Form::getInputTextfield("search_t", "filter_text", "filter_text", $filter_text, '', 255, '' );
			echo Form::getButton("filter_set", "filter_set", Lang::t('_SEARCH', 'standard'), "search_b");
			echo Form::getButton("filter_reset", "filter_reset", Lang::t('_RESET', 'standard'), "reset_b");
		?>
	</div>
	<a id="advanced_search" class="advanced_search" href="javascript:;"><?php echo Lang::t("_ADVANCED_SEARCH", 'standard'); ?></a>
	<div id="advanced_search_options" class="advanced_search_options" style="display: <?php echo $is_active_advanced_filter ? 'block' : 'none'; ?>">
		<?php
			//filter inputs

			$_orgchart_after = '<br />'.Form::getInputCheckbox('filter_descendants', 'filter_descendants', 1, $filter_descendants ? true : false, "")
				.'&nbsp;<label for="filter_descendants">'.Lang::t('_ORG_CHART_INHERIT', 'organization_chart').'</label>';
			echo Form::getDropdown(Lang::t('_DIRECTORY_MEMBERTYPETREE', 'admin_directory'), 'filter_orgchart', 'filter_orgchart', $orgchart_list, (int)$filter_orgchart, $_orgchart_after);
			echo Form::getDatefield(Lang::t('_VALID_AT_DATE', 'subscribe'), 'filter_date_valid', 'filter_date_valid', $filter_date_valid);

			$arr_filter = array(
				0 => Lang::t('_ALL', 'standard'),
				1 => Lang::t('_ONLY_EXPIRED', 'subscribe'),
				2 => Lang::t('_NOT_EXPIRED_WITH_DATE', 'subscribe'),
				3 => Lang::t('_NOT_EXPIRED_WITHOUT_DATE', 'subscribe')
			);
			echo Form::getDropdown(Lang::t('_SHOW_ONLY', 'subscribe'), 'filter_show', 'filter_show', $arr_filter, $filter_show);

			//buttons
			echo Form::openButtonSpace();
			echo Form::getButton('set_advanced_filter', false, Lang::t('_SEARCH', 'standard'));
			echo Form::getButton('reset_advanced_filter', false, Lang::t('_UNDO', 'standard'));
			echo Form::closeButtonSpace();
		?>
	</div>
</div>
<div class="nofloat"></div>
<?php

$add_url = 'index.php?r='.$this->link.'/add&amp;load=1&amp;id_course='.$id_course.'&amp;id_edition='.$id_edition.'&amp;id_date='.$id_date.'';
$mod_url = 'ajax.adm_server.php?r='.$this->link.'/multimod_dialog&amp;id_course='.$id_course.'&amp;id_edition='.$id_edition.'&amp;id_date='.$id_date.'';
$del_url = 'ajax.adm_server.php?r='.$this->link.'/multidel&amp;id_course='.$id_course.'&amp;id_edition='.$id_edition.'&amp;id_date='.$id_date.'';
$imp_csv = 'index.php?r='.$this->link.'/import_csv&amp;id_course='.$id_course.'&amp;id_edition='.$id_edition.'&amp;id_date='.$id_date.'';
$imp_course = 'index.php?r='.$this->link.'/import_course&amp;load=1&amp;id_course='.$id_course.'&amp;id_edition='.$id_edition.'&amp;id_date='.$id_date.'';

$rel_action = '<a class="ico-wt-sprite subs_add" href="'.$add_url.'"><span>'.Lang::t('_ADD', 'subscribe').'</span></a>'
	.'<a class="ico-wt-sprite subs_mod" href="'.$mod_url.'"><span>'.Lang::t('_MOD_SELECTED', 'subscribe').'</span></a>'
	.'<a class="ico-wt-sprite subs_del" href="'.$del_url.'"><span>'.Lang::t('_DEL_SELECTED', 'subscribe').'</span></a>'
	.($id_edition != 0 || $id_date != 0 ? '' : '<a class="ico-wt-sprite subs_dup" href="'.$imp_course.'"><span>'.Lang::t('_IMPORT_FROM_COURSE', 'subscribe').'</span></a>')
	.'<a class="ico-wt-sprite subs_import" href="'.$imp_csv.'"><span>'.Lang::t('_IMPORT', 'subscribe').'</span></a>'
	.'&nbsp;&nbsp;&nbsp;&nbsp;';

$count_selected_over = '<span>'
	.'<b id="num_users_selected_top">'.(int)(isset($num_users_selected) ? $num_users_selected : '0').'</b> '.Lang::t('_SELECTED', 'admin_directory')
	.'</span>';

$count_selected_bottom = '<span>'
	.'<b id="num_users_selected_bottom">'.(int)(isset($num_users_selected) ? $num_users_selected : '0').'</b> '.Lang::t('_SELECTED', 'admin_directory')
	.'</span>';


$icon_unset = '<span class="ico-sprite subs_cancel" title="'.Lang::t('_RESET_VALIDITY_DATES', 'subscribe').'"><span>'.Lang::t('_RESET_VALIDITY_DATES', 'subscribe').'</span></span>';
$icon_delete = '';

$columns = array(
		array('key' => 'userid', 'label' => Lang::t('_USERNAME', 'subscribe'), 'sortable' => true, 'formatter' => 'Subscription.labelFormatter'),
		array('key' => 'fullname', 'label' => Lang::t('_FULLNAME', 'subscribe'), 'sortable' => true, 'formatter' => 'Subscription.labelFormatter'),
		array('key' => 'level', 'label' => Lang::t('_LEVEL', 'subscribe'), 'sortable' => true,
			'formatter' => 'Subscription.levelFormatter',
			'editor' => 'new YAHOO.widget.DropdownCellEditor({dropdownOptions:'.$level_list_js.'})'),
		array('key' => 'status', 'label' => Lang::t('_STATUS', 'subscribe'), 'sortable' => true,
			'formatter' => 'Subscription.statusFormatter',
			'editor' => 'new YAHOO.widget.DropdownCellEditor({dropdownOptions:'.$status_list_js.'})')
);

$columns[] = array("key"=>"date_begin", "label"=>Lang::t("_DATE_BEGIN_VALIDITY", 'subscribe'), "sortable"=>true, "formatter" => 'Subscription.dateFormatter',
	"editor"=>'new YAHOO.widget.DateCellEditor({asyncSubmitter: Subscription.asyncSubmitter})', "className"=>'img-cell');
$columns[] = array("key"=>"date_expire", "label"=>Lang::t("_DATE_EXPIRE_VALIDITY", 'subscribe'), "sortable"=>true, "formatter" => 'Subscription.dateFormatter',
	"editor"=>'new YAHOO.widget.DateCellEditor({asyncSubmitter: Subscription.asyncSubmitter})', "className"=>'img-cell');
$columns[] = array("key"=>"date_unset", "label"=>$icon_unset, 'formatter' => 'Subscription.resetDatesFormatter', 'className' => 'img-cell');

$columns[] = array('key' => 'del', 'label' => Get::img('standard/delete.png', Lang::t('_DEL', 'subscribe')), 'formatter'=>'doceboDelete', 'className' => 'img-cell');

$this->widget('table', array(
	'id'			=> 'subscribed_table',
	'ajaxUrl'		=> 'ajax.adm_server.php?r='.$this->link.'/getlist&id_course='.$id_course.'&id_edition='.$id_edition.'&id_date='.$id_date.'&',
	'rowsPerPage'	=> Get::sett('visuItem', 25),
	'startIndex'	=> 0,
	'results'		=> Get::sett('visuItem', 25),
	'sort'			=> 'userid',
	'dir'			=> 'asc',
	'columns'		=> $columns,
	'fields'		=> array('id', 'userid', 'fullname', 'level', 'status', 'date_begin', 'date_expire', 'date_begin_timestamp', 'date_expire_timestamp', 'del'),
	'stdSelection' => true,
	//'stdSelectionField' => '_checked',
	'selectAllAdditionalFilter' => 'Subscription.selectAllAdditionalFilter()',
	'rel_actions' => array($rel_action.$count_selected_over, $rel_action.$count_selected_bottom),
	'delDisplayField' => 'userid',
	'generateRequest' => 'Subscription.requestBuilder',
	'editorSaveEvent' => 'YAHOO.fastSubscribe.editorSaveEvent',
	'events' => array(
		'initEvent' => 'Subscription.initEvent',
		'beforeRenderEvent' => 'Subscription.beforeRenderEvent',
		'postRenderEvent' => 'Subscription.postRenderEvent'
	)
));

echo $back_link;
?>
	
</div>
<script type="text/javascript">
YAHOO.namespace("fastSubscribe");

YAHOO.fastSubscribe.filterText = "<?php echo $filter_text; ?>";
YAHOO.fastSubscribe.levelList = <?php echo $level_list_js; ?>;
YAHOO.fastSubscribe.statusList = <?php echo $status_list_js; ?>;
YAHOO.fastSubscribe.setNumUserSelected = function(num) {
	var prefix = "num_users_selected_", D = YAHOO.util.Dom;
	D.get(prefix+"top").innerHTML = num;
	D.get(prefix+"bottom").innerHTML = num;
};

YAHOO.util.Event.onDOMReady(function() {

	var refreshTable = function() {
		DataTable_subscribed_table.refresh();
	};

	//autocomplete
	var url = "ajax.adm_server.php?r=<?php echo $this->link; ?>/fastadd"
		+"&id_course=<?php echo (int)$id_course; ?>"
		+"&id_edition=<?php echo (int)$id_edition; ?>"
		+"&id_date=<?php echo (int)$id_date; ?>"
		+"&filter=" + YAHOO.util.Dom.get('fast_subscribe').value;

	var oDS = new YAHOO.util.XHRDataSource(url);
	oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
	oDS.responseSchema = {
		resultsList : "users",
		fields: ["userid", "id", "name"]
	};

	var oAC = new YAHOO.widget.AutoComplete("fast_subscribe", "fast_subscribe_container", oDS);
	oAC.useShadow = true;
	oAC.resultTypeList = false;
	oAC.minQueryLength = 3;
	oAC.maxResultsDisplayed = 15;
	oAC.formatResult = function(oResultData, sQuery, sResultMatch) { return oResultData.name; };
	oAC.itemSelectEvent.subscribe(function(sType, oArgs) { YAHOO.util.Dom.get('fast_subscribe_idst').value = oArgs[2].id; });

	YAHOO.fastSubscribe.autoComplete = {oDS: oDS, oAC: oAC};

	var fastSubscribeSendRequest = function() {
		var id_input = YAHOO.util.Dom.get('fast_subscribe_idst'), userid_input = YAHOO.util.Dom.get('fast_subscribe');
		var postdata = "idst="+id_input.value
			+"&userid="+userid_input.value
			+"&id_course=<?php echo (int)$id_course; ?>"
			+"&id_edition=<?php echo (int)$id_edition; ?>"
			+"&id_date=<?php echo (int)$id_date; ?>";

		YAHOO.util.Connect.asyncRequest("POST", "ajax.adm_server.php?r=<?php echo $this->link; ?>/fastsubscribe", {
			success: function(o) {
				var res = YAHOO.lang.JSON.parse(o.responseText), res_el = YAHOO.util.Dom.get('fast_subscribe_result');
				if (res.success) {
					res_el.firstChild.innerHTML = res.message ? res.message : "<?php echo Lang::t('_OPERATION_SUCCESSFUL', 'subscribe'); ?>";
					res_el.style.visibility = 'visible';
					refreshTable();
					id_input.value = "";
					userid_input.value = "";
				} else {
					res_el.firstChild.innerHTML = res.message ? res.message : "<?php echo Lang::t('_OPERATION_FAILURE', 'subscribe'); ?>";
					res_el.style.visibility = 'visible';
				}
			},
			failure: function() {}
		}, postdata);
	}

	YAHOO.util.Event.addListener('fast_subscribe', "keypress", function(e) {
		switch (YAHOO.util.Event.getCharCode(e)) {
			case 13: {
				YAHOO.util.Event.preventDefault(e);
				if (YAHOO.util.Dom.get('fast_subscribe') != "")	fastSubscribeSendRequest();
			} break;
		}
	});

	YAHOO.util.Event.addListener('fast_subscribe_b', "click", function(e) {
		YAHOO.util.Event.preventDefault(e);
		fastSubscribeSendRequest();
	});


	//multi delete
	var multidel_links = YAHOO.util.Dom.getElementsByClassName('ico-wt-sprite subs_del');
	YAHOO.util.Event.addListener(multidel_links, "click", function(e) {
		YAHOO.util.Event.preventDefault(e);

		var confirm = function() { this.submit(); };
		var undo = function() { this.destroy(); };

		var setDialogErrorMessage = function(message) {
			var el = YAHOO.util.Dom.get("subscribe_table_multidel_dialog_message");
			if (el) el.innerHTML = message;
		}

		var buttons = [], body = '', count_sel = DataTableSelector_subscribed_table.num_selected;
		if (count_sel > 0) buttons.push({text:"<?php echo Lang::t('_CONFIRM', 'standard'); ?>", handler: confirm, isDefault: true});
		buttons.push({text:"<?php echo Lang::t('_UNDO', 'standard'); ?>", handler: undo});

		var delDialog = new YAHOO.widget.Dialog("subscribe_table_multidelDialog", {
			width: "500px",
			modal: true,
			close: true,
			visible: false,
			fixedcenter: true,
			constraintoviewport: true,
			draggable: true,
			hideaftersubmit: false,
			buttons: buttons
		});

		delDialog.hideEvent.subscribe(function(e, args) {
			YAHOO.util.Event.stopEvent(args[0]);
			this.destroy();
		}, delDialog);

		delDialog.callback = {
			success: function(oResponse) {
				var x, o = YAHOO.lang.JSON.parse(oResponse.responseText);
				if (o.success) {
					this.destroy();
					//refreshTable();
					var oDt = DataTable_subscribed_table;
					var oState = oDt.getState();
					var request = oDt.get("generateRequest")(oState, oDt);
					var oCallback = {
						success : oDt.onDataReturnSetRows,
						failure : oDt.onDataReturnSetRows,
						argument : oState,
						scope : oDt
					};
					oDt.getDataSource().sendRequest(request, oCallback);
				} else {
					setDialogErrorMessage(o.message ? o.message : "<?php echo Lang::t('_OPERATION_FAILURE', 'standard'); ?>");
				}
			},
			failure: function() { setDialogErrorMessage("<?php echo Lang::t('_CONNECTION_ERROR', 'standard'); ?>"); },
			scope: delDialog
		};


		if (count_sel > 0) {
			body += '<form method="POST" id="subscribe_table_multidel_dialog_form" action="'+this.href+'">'
				+'<p><?php echo Lang::t('_DEL', 'standard'); ?>: '+count_sel+' <?php echo Lang::t('_USERS', 'standard'); ?></p>'
				+'<input type="hidden" name="users" value="'+DataTableSelector_subscribed_table.toString()+'" />'
				+'</form>';
		} else {
			body += '<p><?php echo Lang::t('_EMPTY_SELECTION', 'admin_directory'); ?></p>';
		}
		delDialog.setHeader("<?php echo Lang::t('_AREYOUSURE', 'standard'); ?>");
		delDialog.setBody('<div id="subscribe_table_multidel_dialog_message"></div>'+body);

		delDialog.render(document.body);
		delDialog.show();
	});

});

YAHOO.fastSubscribe.editorSaveEvent = function(oArgs) {
	var oEditor = oArgs.editor;
	var new_value = oArgs.newData;
	var old_value = oArgs.oldData;
	var id_user = oEditor.getRecord().getData("id");
	var col = oEditor.getColumn().getKey();
	var callback = {
		success: function(o) {
			var res = YAHOO.lang.JSON.parse(o.responseText);
			if (res.success) {
				//oEditor.getRecord().setData(col+"_id");
			}
		},
		failure: function() {}
	};

	var url = "ajax.adm_server.php?r=<?php echo $this->link; ?>/update";
	var post = "id_course=<?php echo $id_course; ?>&id_edition=<?php echo $id_edition; ?>&id_date=<?php echo $id_date; ?>"
				+"&id_user=" + id_user
				+"&col=" + col
				+"&new_value=" + new_value
				+"&old_value=" + old_value;

	YAHOO.util.Connect.asyncRequest("POST", url, callback, post);
}


var Subscription = {

	idCourse: 0,
	idEdition: 0,

	filterText: "",
	filterOrgChart: 0,
	filterDescendants: false,
	filterDateValid: "",
	filterShow: 0,

	statusList: null,
	levelList: null,

	oLangs: new LanguageManager(),

	init: function(idCourse, idEdition, oConfig) {
		this.idCourse = idCourse;
		if (idEdition) this.idEdition = idEdition;
		this.levelList = oConfig.levelList || [];
		this.statusList = oConfig.statusList || [];
		if (oConfig.langs) this.oLangs.set(oConfig.langs);
		if (oConfig.filterText) S.filterText = oConfig.filterText;
		if (oConfig.filterOrgchart) S.filterOrgChart = oConfig.filterOrgchart;
		if (oConfig.filterDescendants) S.filterDescendants = oConfig.filterDescendants;
		if (oConfig.filterDateValid) S.filterDateValid = oConfig.filterDateValid;
		if (oConfig.filterShow) S.filterShow = oConfig.filterShow;

		YAHOO.util.Event.onDOMReady(function(e) {
			var E = YAHOO.util.Event, D = YAHOO.util.Dom, S = Subscription, oDt = DataTable_subscribed_table;

			E.addListener('filter_text', "keypress", function(e) {
				switch (E.getCharCode(e)) {
					case 13: {
						E.preventDefault(e);
						S.filterText = this.value;
						oDt.refresh();
					} break;
				}
			});

			E.addListener("filter_set", "click", function(e) {
				E.preventDefault(e);
				S.filterText = D.get("filter_text").value;
				oDt.refresh();
			});

			E.addListener("filter_reset", "click", function(e) {
				E.preventDefault(e);
				D.get("filter_text").value = "";
				S.filterText = "";
				oDt.refresh();
			});

			E.addListener("advanced_search", "click", function(e){
				var el = D.get("advanced_search_options");
				if (el.style.display != 'block') {
					el.style.display = 'block'
				} else {
					el.style.display = 'none'
				}
			});

			E.addListener("set_advanced_filter", "click", function(e) {
				S.filterOrgChart = D.get("filter_orgchart").value;
				S.filterDescendants = D.get("filter_descendants").checked;
				S.filterDateValid = D.get("filter_date_valid").value;
				S.filterShow = D.get("filter_show").value;
				oDt.refresh();
			});

			E.addListener("reset_advanced_filter", "click", function(e) {
				D.get("filter_orgchart").value = 0;
				D.get("filter_descendants").checked = false;
				D.get("filter_date_valid").value = 0;
				D.get("filter_show").selectedIndex = 0;

				S.filterOrgChart = 0;
				S.filterDescendants = false;
				S.filterDateValid = 0;
				S.filterShow = 0;
				oDt.refresh();
			});

			//multi mod
			var multimod_links = D.getElementsByClassName('ico-wt-sprite subs_mod');
			E.addListener(multimod_links, "click", function(e) {
				var oDs = DataTableSelector_subscribed_table;
				var count_sel = oDs.num_selected;
				CreateDialog("subscribe_table_multimodDialog", {
					//width: "700px",
					modal: true,
					close: true,
					visible: false,
					fixedcenter: false,
					constraintoviewport: false,
					draggable: true,
					hideaftersubmit: false,
					isDynamic: true, //count_sel > 0 ...
					ajaxUrl: this.href + "&count_sel=" + count_sel,
					confirmOnly: count_sel > 0 ? false : true,
					renderEvent: function() {
						E.onAvailable("mod_dialog_users", function() {
							D.get("mod_dialog_users").value = oDs.toString();
						});
						E.onAvailable("multimod_date_begin_set", function() {
							E.addListener(this, "click", function() {
								if (this.checked) {
									var el = D.get("multimod_date_begin_reset");
									if (el) el.checked = false;
								}
							});
						});
						E.onAvailable("multimod_date_expire_set", function() {
							E.addListener(this, "click", function() {
								if (this.checked) {
									var el = D.get("multimod_date_expire_reset");
									if (el) el.checked = false;
								}
							});
						});
						E.onAvailable("multimod_date_begin_reset", function() {
							E.addListener(this, "click", function() {
								if (this.checked) {
									var el = D.get("multimod_date_begin_set");
									if (el) el.checked = false;
								}
							});
						});
						E.onAvailable("multimod_date_expire_reset", function() {
							E.addListener(this, "click", function() {
								if (this.checked) {
									var el = D.get("multimod_date_expire_set");
									if (el) el.checked = false;
								}
							});
						});
					},
					destroyEvent: function() {
						//purge events
					},
					callback: function(o) { try { 
						if (o.success) {
							this.destroy();
							DataTable_subscribed_table.refresh();
						} else {
							WriteDialogMessage(this, o.message ? o.message : Subscription.oLanguage.get('_OPERATION_FAILURE'));
						} } catch(e) {alert(e)}
					}
				}).call(this, e);
			});
		});
	},

	initEvent: function() {
		var updateSelected = function() {
			YAHOO.fastSubscribe.setNumUserSelected(this.num_selected);
		};
		var ds = DataTableSelector_subscribed_table;
		ds.subscribe("add", updateSelected);
		ds.subscribe("remove", updateSelected);
		ds.subscribe("reset", updateSelected);

		this.doBeforeShowCellEditor = function(oEditor) {
			var key = oEditor.getColumn().getKey();
			switch (key) {

				case "date_begin": {
					oEditor.value = new Date( oEditor.getRecord().getData("date_begin_timestamp") );
				}break;

				case "date_expire": {
					oEditor.value = new Date( oEditor.getRecord().getData("date_expire_timestamp") );
				}break;

			}
			return true;
		};
	},


	beforeRenderEvent: function() {
		var elList = YAHOO.util.Selector.query("a[id^=_reset_dates_]");
		YAHOO.util.Event.purgeElement(elList);
	},

	postRenderEvent: function() {
		var elList = YAHOO.util.Selector.query("a[id^=_reset_dates_]");
		YAHOO.util.Event.addListener(elList, "click", function(e) {
			YAHOO.util.Event.preventDefault(e);
			var oDt = DataTable_subscribed_table;
			oDt.showTableMessage(oDt.get("MSG_LOADING"), YAHOO.widget.DataTable.CLASS_LOADING);
			var oRecord = oDt.getRecord(this);
			YAHOO.util.Connect.asyncRequest("POST", this.href, {
				success: function(o) {
					var res;
					try { res = YAHOO.lang.JSON.parse(o.responseText); } catch(e) { res = {success: false} };
					if (res.success) {
						oDt.hideTableMessage();
						oDt.updateCell(oRecord, "date_begin", "-");
						oDt.updateCell(oRecord, "date_expire", "-");
					} else {
						oDt.showTableMessage(oDt.get("MSG_ERROR"), YAHOO.widget.DataTable.CLASS_LOADING);
					}
				},
				failure: function() {
					oDt.showTableMessage(oDt.get("MSG_ERROR"), YAHOO.widget.DataTable.CLASS_LOADING);
				}
			});
		});
	},


	selectAllAdditionalFilter: function() {
		return "&filter_text=" + Subscription.filterText
				"&filter_orgchart=" + Subscription.filterOrgChart +
				"&filter_descendants=" + (Subscription.filterDescendants ? '1' : '0') +
				"&filter_date_valid=" + Subscription.filterDateValid;
	},

	requestBuilder: function (oState, oSelf) {
		var sort, dir, startIndex, results;
		oState = oState || {pagination: null, sortedBy: null};
		startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
		results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
		sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
		dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
		return "&results=" 	+ results +
				"&startIndex=" 	+ startIndex +
				"&sort="		+ sort +
				"&dir="			+ dir +
				Subscription.selectAllAdditionalFilter();
	},


	labelFormatter: function(elLiner, oRecord, oColumn, oData) {
		elLiner.innerHTML = '<label for="subscribed_table_sel_'+oRecord.getData("id")+'">'+oData+'</label>';
	},

	levelFormatter: function(elLiner, oRecord, oColumn, oData) {
		var i, valid = false, list = Subscription.levelList;
		for (i=0; i<list.length; i++) {
			if (list[i].value == oData) {
				elLiner.innerHTML = list[i].label;
				valid = true;
				break;
			}
		}
		if (!valid) elLiner.innerHTML = '&nbsp;';
		//elLiner.innerHTML = (YAHOO.lang.isNumber(parseInt(oData)) ? oRecord.getData("level_tr") : oData);
	},

	statusFormatter: function(elLiner, oRecord, oColumn, oData) {
		var i, valid = false, list = Subscription.statusList;
		for (i=0; i<list.length; i++) {
			if (list[i].value == oData) {
				elLiner.innerHTML = list[i].label;
				valid = true;
				break;
			}
		}
		if (!valid) elLiner.innerHTML = '&nbsp;';
		//elLiner.innerHTML = (YAHOO.lang.isNumber(parseInt(oData)) ? oRecord.getData("status_tr") : oData);//oRecord.getData("status_tr");
	},

	dateFormatter: function(elLiner, oRecord, oColumn, oData) {
		if (!oData || oData == "00-00-00" || oData == "00-00-0000") {
			elLiner.innerHTML = '-';
		} else {
			elLiner.innerHTML = oData;
		}
	},

	resetDatesFormatter: function(elLiner, oRecord, oColumn, oData) {
		var url = 'ajax.adm_server.php?r=<?php echo $this->link; ?>/reset_validity_dates&id_course='
			+Subscription.idCourse+(Subscription.idEdition>0 ? "&id_edition="+Subscription.idEdition : "")
			+"&id_user="+oRecord.getData("id");
		var id = "_reset_dates_"+oRecord.getData("id");
		elLiner.innerHTML = '<a href="'+url+'" id="'+id+'" class="ico-sprite subs_cancel" title="'+Subscription.oLangs.get('_RESET_VALIDITY_DATES')+'"><span>'
			+Subscription.oLangs.get('_RESET_VALIDITY_DATES')+'</span></a>';
	},

	asyncSubmitter: function (callback, newData) {
		var new_value = newData;
		var col = this.getColumn().key;
		var old_value =  "";
		var id_user = this.getRecord().getData("id");

		switch (col) {
			case "date_begin": {
				var date = this.calendar.getSelectedDates();
				old_value = this.getRecord().getData("date_begin_timestamp");
				new_value = parseInt(date[0].getTime() / 1000); //we need time in seconds, not milliseconds
			}break;

			case "date_expire": {
				var date = this.calendar.getSelectedDates();
				old_value = this.getRecord().getData("date_expire_timestamp");
				new_value = parseInt(date[0].getTime() / 1000); //we need time in seconds, not milliseconds
			}break;

			default: {
				old_value = this.value;
			}break;
		}

		//var datatable = DataTable_subscribed_table;

		var editorCallback = {
			success: function(o) {
				var r = YAHOO.lang.JSON.parse(o.responseText);
				if (r.success) {
					callback(true, r.new_value ? r.new_value : old_value);
				} else {
					callback(false);
				}
			},
			failure: {}
		}

		var _post = "id_course=" + Subscription.idCourse;
		if (Subscription.idEdition) _post += "&id_edition=" + Subscription.idEdition;
		var post =	_post+"&id_user=" + id_user + "&col=" + col + "&new_value=" + new_value + "&old_value=" + old_value;
		var url = "ajax.adm_server.php?r=<?php echo $this->link; ?>/show_inline_editor";
		YAHOO.util.Connect.asyncRequest("POST", url, editorCallback, post);
	}
}

Subscription.init(<?php echo (int)$id_course;?>, <?php echo (int)$id_edition; ?>, {
	levelList: <?php echo $level_list_js; ?>,
	statusList: <?php echo $status_list_js; ?>,
	filterText: "<?php echo $filter_text; ?>",
	filterOrgChart: <?php echo (int)$filter_orgchart; ?>,
	filterDescendants: <?php echo $filter_descendants ? 'true' : 'false'; ?>,
	filterDateValid: '<?php echo $filter_date_valid; ?>',
	filterShow: <?php echo (int)$filter_show; ?>,
	langs: {
		_RESET_VALIDITY_DATES: "<?php echo Lang::t('_RESET_VALIDITY_DATES', 'subscribe'); ?>",
		_ERROR: "<?php echo Lang::t('_OPERATION_FAILURE', 'standard'); ?>"
	}
});

</script>