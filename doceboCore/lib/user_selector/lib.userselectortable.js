/* ======================================================================== \
| 	DOCEBO - The E-Learning Suite											|
| 																			|
| 	Copyright (c) 2008 (Docebo)												|
| 	http://www.docebo.com													|
|   License 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt		|
\ ======================================================================== */

//datatable wrapper and configurator

function _oUserDataTable(tableId, containerId, oConfig, oLangs) {
  this._oLangs = oLangs;
  this.init(tableId, containerId, oConfig);
}


_oUserDataTable.prototype = {

  _containerId: null,

  _oLangs: null,

  _useFilter: false,

  _filterString: "",

  _oSelector: null,

  _oDataSource: null,
  
  _oPaginator: null,

  _oDatatable: null,

  _oFilter: null, //yet not used, will probably be with conditional filter

  _oHeadSelectionFields: null,

  _headFields: [0, 0, 0],

  init: function(tableId, containerId, oConfig) {

    this._containerId = containerId;
    this._oHeadSelectionFields = oConfig.user_fields;

    this._oSelector = new ElemSelector(containerId+'_');
    if (oConfig.initial_selection) {
      for (var i=0; i<oConfig.initial_selection.length; i++) {
        this._oSelector.addsel(oConfig.initial_selection[i]);
      }
    }

    var oScope = this;
    
    var formatCheckbox_Sel = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = '';
			var id = oRecord.getData("idst");
			var checkbox 	= document.createElement("input");
			checkbox.type 	= 'checkbox';
			checkbox.id 	= 'user_'+id;
			//checkbox.name 	= 'user['+id+']';
			checkbox.value 	= id;
			if (oScope._oSelector.isset(id)) checkbox.checked = true;
			elCell.appendChild(checkbox);
			YAHOO.util.Event.addListener(checkbox, "click", function(e) {
        var t = oScope._oSelector;
        if (this.checked) t.addsel(this.value); else t.remsel(this.value);
        oScope._refreshSelectorCount();
      });
		};		


    var oColumnDefs = [
			{key:"checkbox_sel", label:"",                            sortable: false, formatter: formatCheckbox_Sel },
			{key:"userid",       label:this._oLangs. def('_USERNAME'),   sortable: true},
			{key:"fullname",     label:this._oLangs. def('_FULLNAME'), sortable: true},
			{key:"_varcol_0",    label:"",                            sortable: false},
			{key:"_varcol_1",    label:"",                            sortable: false},
			{key:"_varcol_2",    label:"",                            sortable: false}
		];
		
		// data source (XHR)
		this._oDataSource = new YAHOO.util.DataSource(oConfig.ajax_url+'&');
		this._oDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		this._oDataSource.connXhrMode = "queueRequests";
		this._oDataSource.responseSchema = {
			resultsList: "records",
			totalRecords: 'totalRecords',
			filteredRecords: 'filteredRecords',
			startIndex: 'startIndex',
			fields: ["idst", "userid", "fullname", "_varcol_0", "_varcol_1", "_varcol_2"]//{key:"name", parser:YAHOO.util.DataSource.parseString}]
		};

		// A custom function to translate the js paging request into a query
		// string sent to the XHR DataSource
		var buildQueryString = function (state, dt) {
			var sortedBy = oScope._oDataTable.get("sortedBy");
			return oScope._getRequestString({
        startIndex: state.pagination.recordOffset,
        results: state.pagination.rowsPerPage,
        sort: sortedBy.key,
        dir: (sortedBy.dir==YAHOO.widget.DataTable.CLASS_ASC ? "asc" : "desc")
      });
			//return 'op=table_get_users'	+ "&startIndex=" + state.pagination.recordOffset + "&results=" + state.pagination.rowsPerPage; //+(this._oFilter ? "&filter="+this._oFilter.toString() : "";
		};



		// Set up the Paginator instance.
		this._oPaginator = new YAHOO.widget.Paginator({
			containers 		: ["table_paginator_head_"+containerId, "table_paginator_foot_"+containerId],
			rowsPerPage 	: oConfig.users_per_page,
			alwaysVisible : true,
			initialPage 	: oConfig.start_page,
			
			pageLinks 		: 7,

			template 				: "{FirstPageLink} {PreviousPageLink}"
				+ " {PageLinks} "
				+ "{NextPageLink} {LastPageLink}"
				+ "&nbsp;&nbsp;<strong>{RangeRecords}</strong> "+this._oLangs. def('_ON')+" <strong>{TotalRecords}</strong>&nbsp;&nbsp;",
			firstPageLinkLabel 		: '&laquo; '+this._oLangs. def('_FIRST'),
			previousPageLinkLabel 	: '&lsaquo; '+this._oLangs. def('_PREV'),
			nextPageLinkLabel 		: this._oLangs. def('_NEXT')+' &rsaquo;',
			lastPageLinkLabel 		: this._oLangs. def('_LAST')+' &raquo;'
		});



    var oInitialRequestParams = {
      startIndex: 0,
      results: oConfig.users_per_page,
      sort: 'userid',
      dir: 'asc'
    };

		var oTableConfig = {
			initialRequest         : oScope._getRequestString(oInitialRequestParams),//'op=table_get_users&startIndex=' +  0 + '&results=' + oConfig.users_per_page,
			generateRequest        : buildQueryString,
			paginator              : this._oPaginator,
			sortedBy               : {key: "userid", dir: YAHOO.widget.DataTable.CLASS_ASC}, // Set up initial column headers UI
			paginationEventHandler : YAHOO.widget.DataTable.handleDataSourcePagination
		};

    this._oDataTable = new YAHOO.widget.DataTable(tableId, oColumnDefs, this._oDataSource, oTableConfig);
		
		//set row highlightening on mouse over event
		this._oDataTable.subscribe("rowMouseoverEvent", this._oDataTable.onEventHighlightRow);
		this._oDataTable.subscribe("rowMouseoutEvent", this._oDataTable.onEventUnhighlightRow);
		
		this._oDataTable.subscribe("initEvent", function(e) {
		  var $E = YAHOO.util.Event, $D = YAHOO.util.Dom;
		  var i, j, t, opt, fields = this._oHeadSelectionFields;
		  
		  //purge previous listeners, if exist
		  if (t = $D.get("_varcol_0_sel")) $E.purgeElement(t, false, "change");
		  if (t = $D.get("_varcol_1_sel")) $E.purgeElement(t, false, "change");
		  if (t = $D.get("_varcol_2_sel")) $E.purgeElement(t, false, "change");
		  		  
      var col = [
        this._oDataTable.getThLinerEl( this._oDataTable.getColumn("_varcol_0") ),//YAHOO.util.Dom.get('yui-dt0-th3-liner'),
        this._oDataTable.getThLinerEl( this._oDataTable.getColumn("_varcol_1") ),//YAHOO.util.Dom.get('yui-dt0-th4-liner'),
        this._oDataTable.getThLinerEl( this._oDataTable.getColumn("_varcol_2") ) //YAHOO.util.Dom.get('yui-dt0-th5-liner')
      ];
      
        
      for(i=0; i<col.length; i++) {

        col[i].innerHTML = '';
        var select = document.createElement("SELECT");
        select.id = "_varcol_"+i+"_sel";
        //select.name = "_varcol_sel["+i+"]";
        
        //if (fields)
          for (j=0; j<fields.length; j++) {
            //opt = new Option(fields[j].name, fields[j].id);
            opt = document.createElement("OPTION");
            opt.value = fields[j].id;
            opt.text = fields[j].name;
            try { select.add(opt, null); } catch(e) { select.add(opt); }
          }
        
        select.selectedIndex = this._headFields[i];
        
        col[i].appendChild(select);
        
      } //end for
      
      $E.addListener("_varcol_0_sel", "change", function(e) { this._headFields[0] = $D.get("_varcol_0_sel").selectedIndex; this.refresh(); }, this, true);
      $E.addListener("_varcol_1_sel", "change", function(e) { this._headFields[1] = $D.get("_varcol_1_sel").selectedIndex; this.refresh(); }, this, true);
      $E.addListener("_varcol_2_sel", "change", function(e) { this._headFields[2] = $D.get("_varcol_2_sel").selectedIndex; this.refresh(); }, this, true);
            
    }, this, true);
    
    
    // Override function for custom server-side sorting 
    this._oDataTable.sortColumn = function(oColumn) {
      // Default ascending 
      var sDir = "asc" 

      // If already sorted, sort in opposite direction 
      if(oColumn.key === this.get("sortedBy").key) { 
        sDir = (this.get("sortedBy").dir === YAHOO.widget.DataTable.CLASS_ASC) ? "desc" : "asc"; 
      }
    
      // Pass in sort values to server request
      var state = this.get("paginator").getState();
      
      var newRequest = oScope._getRequestString({
        startIndex: state.recordOffset,
        results: state.rowsPerPage,
        sort: oColumn.key,
        dir: sDir
      });//"sort=" + oColumn.key + "&dir=" + sDir + "&results=100&startIndex=0"; 
    
      // Create callback for data request 
      var oCallback = { 
        success: this.onDataReturnInitializeTable, 
        failure: function() { alert("failure"); },//this.onDataReturnInitializeTable, 
        scope: this, 
        argument: {
          startIndex: state.recordOffset,
          results: state.rowsPerPage,
          // Pass in sort values so UI can be updated in callback function 
          sorting: { 
            key: oColumn.key, 
            dir: (sDir === "asc") ? YAHOO.widget.DataTable.CLASS_ASC : YAHOO.widget.DataTable.CLASS_DESC 
          } 
        } 
      } 

      // Send the request 
      this.getDataSource().sendRequest(newRequest, oCallback);
      
    }; 
    
    
  },
  
  refresh: function(oRequestData) {
    
    if (!oRequestData) {
    
      var oState = this._oDataTable.get("paginator").getState();
      var oSortedBy = this._oDataTable.get("sortedBy");
      oRequestData = {
        startIndex: oState.recordOffset,
        results: oState.rowsPerPage,
        sort: oSortedBy.key,
        dir: (oSortedBy.dir==YAHOO.widget.DataTable.CLASS_ASC ? "asc" : "desc")
      }
    
    }
    var newRequest = this._getRequestString(oRequestData);
    
    var oCallback = { 
      success: this._oDataTable.onDataReturnInitializeTable,//onDataReturnSetRows 
      failure: function() { alert("failure"); },
      scope: this._oDataTable, 
      argument: {
        startIndex: oRequestData.startIndex,
        results: oRequestData.results,
        // Pass in sort values so UI can be updated in callback function 
        sorting: { 
          key: oRequestData.sort, 
          dir: (oRequestData.dir=="asc" ? YAHOO.widget.DataTable.CLASS_ASC : YAHOO.widget.DataTable.CLASS_DESC)
        } 
      } 
    };
 
    this._oDataTable.getDataSource().sendRequest(newRequest, oCallback);
  },
  
  setFilter: function(oFilter) { this._oFilter = oFilter; },



  _getRequestString: function(oParams) {
    var indexes = this._headFields, t = this._oHeadSelectionFields;
    var req = "op=table_get_users&startIndex="+oParams.startIndex+"&results="+oParams.results+"&sort="+oParams.sort+"&dir="+oParams.dir;
    req += "&extraFields[0]="+t[indexes[0]].id+"&extraFields[1]="+t[indexes[1]].id+"&extraFields[2]="+t[indexes[2]].id;
    if (this._useFilter) req += "&filter="+this._filterString;
    return req;
  },
  
  
  
  _refreshSelectorCount: function() {
    var t;
    if (t = YAHOO.util.Dom.get("selected_count_"+this._containerId)) {
      t.innerHTML = this._oSelector.num_selected;
    }  
  }

}





//main class

function UserSelectorTable(container, oConfig) {
  this.init(container, oConfig);
}




UserSelectorTable.prototype = {

  _oConstants: {},
  
  _oLangs: {
  
    _oKeys : {},
    
    def: function(textKey) { if (this._oKeys[textKey]) return this._oKeys[textKey]; else return textKey; }
  },

  _id: null,
  
  _serverUrl: "",
  
  _oContainerEl: null,

  _oTable: null,
  
  _oInputEl: null,
  
  _oSelectAllDialog: null,
  
  init: function(container, oConfig) {
  
    var t, oScope = this;

    this.id = container;
    this._oContainerEl = YAHOO.util.Dom.get(this.id);
    
    
    if (oConfig.langs) this._oLangs._oKeys = oConfig.langs;
    
    
    //append filter container
    t = document.createElement("DIV");
    t.id = "table_filter_"+this.id;
    this._oContainerEl.appendChild(t);
    
    //this is paginator above table
    t = document.createElement("DIV");
    t.id = "table_paginator_head_"+this.id;
    this._oContainerEl.appendChild(t);
  
    //the table
    t = document.createElement("DIV");
    t.id = "table_container_"+this.id;
    this._oContainerEl.appendChild(t);
    
    //paginator under table
    t = document.createElement("DIV");
    t.id = "table_paginator_foot_"+this.id;
    this._oContainerEl.appendChild(t);
  
    //commands
    t = document.createElement("DIV");
    t.id = "table_commands_"+this.id;
    this._oContainerEl.appendChild(t);
  
  
  
    //add filter textbox
  
    t = document.createElement("DIV");
    t.className = "align_right";
    var filterText = document.createElement("INPUT");
    filterText.type = "text";
    filterText.id = "table_filter_textbox_"+this.id;
    //filterText.className = ...
    
    var filterButton = document.createElement("BUTTON");
    try { filterButton.type = "button"; } catch(e) {}
    filterButton.id = "table_filter_searchbutton_"+this.id;
    filterButton.innerHTML = this._oLangs. def('_SEARCH');
    //filterButton.className = ...
    
    var filterReset = document.createElement("BUTTON");
    try { filterReset.type = "button"; } catch(e) {}
    filterReset.id = "table_filter_resetbutton_"+this.id;
    filterReset.innerHTML = this._oLangs. def('_RESET');
    //filterButton.className = ...
    
    t.appendChild(filterText);
    t.appendChild(filterButton);
    t.appendChild(filterReset);
    YAHOO.util.Dom.get("table_filter_"+this.id).appendChild(t);
    
    YAHOO.util.Event.addListener("table_filter_searchbutton_"+this.id, "click", function(e) { 
      //resetpaginator
      this._oTable._useFilter = true;
      this._oTable._filterString = YAHOO.util.Dom.get("table_filter_textbox_"+this.id).value;
      
      //refreshtable with filter
      var s = this._oTable._oDataTable.get("sortedBy");
      this._oTable.refresh({
        startIndex: 0,
        results: this._oTable._oDataTable.get("paginator").getState().rowsPerPage,
        sort: s.key,
        dir: (s.dir==YAHOO.widget.DataTable.CLASS_ASC ? "asc" : "desc")
      });
    }, this, true);
  
    YAHOO.util.Event.addListener("table_filter_resetbutton_"+this.id, "click", function(e) { 
      //resetpaginator
      this._oTable._useFilter = false;
      this._oTable._filterString = "";
      YAHOO.util.Dom.get("table_filter_textbox_"+this.id).value = "";
      
      //refreshtable with filter
      var s = this._oTable._oDataTable.get("sortedBy");
      this._oTable.refresh({
        startIndex: 0,
        results: this._oTable._oDataTable.get("paginator").getState().rowsPerPage,
        sort: s.key,
        dir: (s.dir==YAHOO.widget.DataTable.CLASS_ASC ? "asc" : "desc")
      });
    }, this, true);
  
    if (oConfig.ajax_url) this._serverUrl = oConfig.ajax_url;
  
    //create the table

    this._oTable = new _oUserDataTable("table_container_"+this.id, this.id, oConfig, this._oLangs);

    this._oTable.setFilter(null);
  
    if (oConfig.use_input) {
      t = YAHOO.util.Dom.get(this.id+"_input");
      this._oContainerEl.appendChild(t);
      this._oInputEl = t;
      YAHOO.util.Event.addListener(this._oInputEl.form, "submit", function(e) {
        this._oInputEl.value = this.getCurrentSelection();
      }, this, true);
    }
  
  
    //set commands buttons
    var commands = YAHOO.util.Dom.get("table_commands_"+this.id);

    var div = document.createElement("DIV");
    div.className = "align_right";
    t = document.createElement("SPAN");
    t.innerHTML = this._oLangs. def('_SELECTED')+":&nbsp;";
    div.appendChild(t);
    t = document.createElement("SPAN");
    t.id = "selected_count_"+this.id;
    t.innerHTML = '0';
    div.appendChild(t);
    commands.appendChild(div);

    
    t = document.createElement("BUTTON");
    try { t.type = "button"; } catch(e) {}
    t.id = "select_all_"+this.id;
    t.innerHTML = this._oLangs. def('_SELECT_ALL');
    commands.appendChild(t);
    
    t = document.createElement("BUTTON");
    try { t.type = "button"; } catch(e) {}
    t.id = "select_page_"+this.id;
    t.innerHTML = this._oLangs. def('_SELECT_PAGE');
    commands.appendChild(t);
    
    t = document.createElement("BUTTON");
    try { t.type = "button"; } catch(e) {}
    t.id = "deselect_all_"+this.id;
    t.innerHTML = this._oLangs. def('_UNSELECT_ALL');
    commands.appendChild(t);
    
    t = document.createElement("BUTTON");
    try { t.type = "button"; } catch(e) {}
    t.id = "deselect_page_"+this.id;
    t.innerHTML = this._oLangs. def('_DESELECT_PAGE');
    commands.appendChild(t);
    
    
    YAHOO.util.Event.addListener(YAHOO.util.Dom.get("select_all_"+this.id), "click", this.selectAll, this, true);
    YAHOO.util.Event.addListener(YAHOO.util.Dom.get("select_page_"+this.id), "click", this.selectPage, this, true);
    YAHOO.util.Event.addListener(YAHOO.util.Dom.get("deselect_all_"+this.id), "click", this.deselectAll, this, true);
    YAHOO.util.Event.addListener(YAHOO.util.Dom.get("deselect_page_"+this.id), "click", this.deselectPage, this, true);
    
    //create Select All confirm dialog popup
    t = document.createElement("DIV");
    t.id = "confirm_select_all_"+this.id;
    document.body.appendChild(t);
    this._oSelectAllDialog = new YAHOO.widget.SimpleDialog("confirm_select_all_"+this.id, 
		  {
        width: "300px",
			  fixedcenter: true,
        visible: false,
        draggable: true,
        close: true,
        constraintoviewport: true,
        buttons: [
          { text: oScope._oLangs. def('_YES'), handler:function() { oScope._selectAll(); this.hide(); }, isDefault:true },
          { text: oScope._oLangs. def('_NO'),  handler:function() { this.hide(); } }
        ]
      } );
    this._oSelectAllDialog.render();
    
  },


  _selectAll: function() {
    if (this._serverUrl != "") {
      var postData = "op=select_all"+(this._oTable._useFilter ? "&filter="+this._oTable._filterString : "");
      
      var oCallback = {
        success: function(o) {
          var i, t = YAHOO.lang.JSON.parse(o.responseText);
          for (i=0; i<t.data.length; i++) { this._oTable._oSelector.addsel(t.data[i]); }
          var nodes = YAHOO.util.Selector.query('input[id^=user_]');
          for (i=0; i<nodes.length; i++) { nodes[i].checked = true; }
          this._oTable._refreshSelectorCount();
        },
        failure: function() { alert("failure"); },
        scope: this
      };
      
      YAHOO.util.Connect.asyncRequest("POST", this._serverUrl, oCallback, postData);
    }
  },

  selectAll: function(e) {
    this._oSelectAllDialog.setBody( this._oLangs. def('_SELECTALL_TEXT')+": "+this._oTable._oDataTable.get("paginator").getTotalRecords() );
    this._oSelectAllDialog.show();
  },



  selectPage: function(e) { 
    var i, nodes = YAHOO.util.Selector.query('input[id^=user_]');
    for (i=0; i<nodes.length; i++) { nodes[i].checked = true; this._oTable._oSelector.addsel(nodes[i].value); }
    this._oTable._refreshSelectorCount();
  },
  
  
  deselectAll: function(e) {
    this._oTable._oSelector.reset();
    this._oTable._refreshSelectorCount();
    var i, nodes = YAHOO.util.Selector.query('input[id^=user_]');
    for (i=0; i<nodes.length; i++) { nodes[i].checked = false; }
  },
  
  
  deselectPage: function(e) {
    var i, nodes = YAHOO.util.Selector.query('input[id^=user_]');
    for (i=0; i<nodes.length; i++) { nodes[i].checked = false; this._oTable._oSelector.remsel(nodes[i].value); }
    this._oTable._refreshSelectorCount();
  },
  
  
  getCurrentSelection: function() {
    return this._oTable._oSelector.toString();
  }
}
