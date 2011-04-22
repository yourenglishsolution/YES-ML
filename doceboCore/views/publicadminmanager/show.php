<?php echo getTitleArea(Lang::t('_PUBLIC_ADMIN_MANAGER', 'menu')); ?>
<div class="std_block">
<?php

//--- SEARCH FILTER -------

$this->widget('tablefilter', array(
	'id' => 'adminmanager',
	'filter_text' => $filter_text,
	'js_callback_set' => 'PublicAdminManagement.setFilter',
	'js_callback_reset' => 'PublicAdminManagement.resetFilter'
));


//--- TABLE -------

$_columns = array(
	array('key' => 'userid', 'label' => Lang::t('_USERNAME', 'adminmanager'), 'sortable' => true),
	array('key' => 'firstname', 'label' => Lang::t('_FIRSTNAME', 'adminmanager'), 'sortable' => true),
	array('key' => 'lastname', 'label' => Lang::t('_LASTNAME', 'adminmanager'), 'sortable' => true),
	array('key' => 'user_profile', 'label' => Lang::t('_ADMIN_RULES', 'adminrules'), 'sortable' => true, 'formatter' => 'PublicAdminManagement.formatUserProfile')
);

$_img_profile = Get::sprite('subs_user', Lang::t('_ADMIN_RULES', 'adminrules'));
$_img_users = Get::sprite('subs_users', Lang::t('_ASSIGN_USERS', 'adminmanager'));
$_img_courses = Get::sprite('subs_elem', Lang::t('_COURSES', 'adminmanager'));
$_img_classlocations = Get::sprite('subs_location', Lang::t('_LOCATION', 'adminmanager'));

if ($permissions['assign_profile']) $_columns[] = array('key' => 'profile', 'label' => $_img_profile, 'className' => 'img-cell', 'formatter' => 'stdDialog');
if ($permissions['assign_users']) $_columns[] = array('key' => 'users', 'label' => $_img_users, 'className' => 'img-cell', 'formatter' => 'PublicAdminManagement.formatUsers');
if ($permissions['assign_courses']) $_columns[] = array('key' => 'courses', 'label' => $_img_courses, 'className' => 'img-cell', 'formatter' => 'PublicAdminManagement.formatCourses');
if ($permissions['assign_courses']) $_columns[] = array('key' => 'classlocations', 'label' => $_img_classlocations, 'className' => 'img-cell', 'formatter' => 'PublicAdminManagement.formatClasslocations');

$this->widget('table', array(
	'id'			=> 'public_admin_manager_table',
	'ajaxUrl'		=> 'ajax.adm_server.php?r=adm/publicadminmanager/getAdmin&',
	'rowsPerPage'	=> Get::sett('visuItem', 25),
	'startIndex'	=> 0,
	'results'		=> Get::sett('visuItem', 25),
	'sort'			=> 'userid',
	'dir'			=> 'asc',
	'columns'		=> $_columns,
	'fields'		=> array('id_user', 'userid', 'firstname', 'lastname', 'user_profile', 'has_users', 'has_courses', 'has_classlocations', 'profile'),
	'generateRequest' => 'PublicAdminManagement.requestBuilder',
	'stdDialogIcons' => array('profile' => 'subs_user')
));

?>
</div>
<script type="text/javascript">

var PublicAdminManagement = {

	oLangs: new LanguageManager(),
	filterText: "",

	init: function(oConfig) {
		if (oConfig.filterText) this.filterText = oConfig.filterText;
		if (oConfig.langs) this.oLangs.set(oConfig.langs);
	},

	formatUserProfile: function(elLiner, oRecord, oColumn, oData) {
		if (oData === false) {
			elLiner.innerHTML = '<span class="ico-sprite fd_notice"><span>'+PublicAdminManagement.oLangs.get('_NONE')+'</span></span>';
		} else {
			elLiner.innerHTML = oData;
		}
	},

	formatUsers: function(elLiner, oRecord, oColumn, oData) {
		var url ='index.php?r=adm/publicadminmanager/users&amp;id_user='+oRecord.getData("id_user")+'&amp;load=1';
		var style = oRecord.getData("has_users") > 0 ? 'subs_users' : 'fd_notice';
		var title = PublicAdminManagement.oLangs.get('_USERS_ASSOCIATION');
		elLiner.innerHTML = '<a href="'+url+'" class="ico-sprite '+style+'" title="'+title+'"><span>'+title+'</span></a>';
	},

	formatCourses: function(elLiner, oRecord, oColumn, oData) {
		var url ='index.php?r=adm/publicadminmanager/courses&amp;id_user='+oRecord.getData("id_user")+'&amp;load=1';
		var style = oRecord.getData("has_courses") > 0 ? 'subs_elem' : 'fd_notice';
		var title = PublicAdminManagement.oLangs.get('_COURSES');
		elLiner.innerHTML = '<a href="'+url+'" class="ico-sprite '+style+'" title="'+title+'"><span>'+title+'</span></a>';
	},

	formatClasslocations: function(elLiner, oRecord, oColumn, oData) {
		var url ='index.php?r=adm/publicadminmanager/classlocations&amp;id_user='+oRecord.getData("id_user")+'&amp;load=1';
		var style = oRecord.getData("has_classlocations") > 0 ? 'subs_location' : 'fd_notice';
		var title = PublicAdminManagement.oLangs.get('_LOCATION');
		elLiner.innerHTML = '<a href="'+url+'" class="ico-sprite '+style+'" title="'+title+'"><span>'+title+'</span></a>';
	},

	setFilter: function() {
		PublicAdminManagement.filterText = this.value;
		DataTable_public_admin_manager_table.refresh();
	},

	resetFilter: function() {
		this.value = "";
		PublicAdminManagement.filterText = "";
		DataTable_public_admin_manager_table.refresh();
	},

	requestBuilder: function (oState, oSelf) {
		var sort, dir, startIndex, results;
		oState = oState || {pagination: null, sortedBy: null};
		startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
		results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
		sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
		dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
		return "&results=" + results +
			"&startIndex=" + startIndex +
			"&sort=" + sort +
			"&dir=" + dir+
			"&filter_text=" + PublicAdminManagement.filterText;
	}

}

PublicAdminManagement.init({
	filterText: "<?php echo $filter_text; ?>",
	langs: {
		_NONE: "<?php echo Lang::t('_NONE', 'adminrules'); ?>",
		_USERS_ASSOCIATION: "<?php echo Lang::t('_ASSIGN_USERS', 'adminmanager'); ?>",
		_COURSES: "<?php echo Lang::t('_COURSES', 'adminmanager'); ?>",
		_LOCATION: "<?php echo Lang::t('_LOCATION', 'adminmanager'); ?>"
	}
});

</script>