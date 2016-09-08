pimcore.registerNS('pimcore.plugin.CsvImport.admin.filterConfigWindow2');
pimcore.plugin.CsvImport.admin.filterConfigWindow2 = Class.create({

    filterGrid: null,
    filterGridStore: null,
    filterChainGrid: null,
    filterChainGridStore: null,
    filterParamPanel: null,
    colsFilterChain: null,

    getFilterGridStore: function () {
        return new Ext.data.JsonStore({
            autoLoad: true,
            restful: true,
            root: 'fields',
            fields: [
                'class',
                'name',
                'description'
            ],

            proxy: new Ext.data.HttpProxy({
                url: '/PimcoreBulkpump/filters'
            })
        });
    },

    getFilterChainGridStore: function (config_id) {
        return new Ext.data.JsonStore({
            autoLoad: true,
            autoSave: true,
            restful: true,
            root: 'fields',
            fields: [
                'sort_order',
                'id',
                'class',
                'name',
                'description'
            ],

            writer: new Ext.data.JsonWriter({
                encode: false,
                writeAllFields: true,
                listful: true
            }),

            proxy: new Ext.data.HttpProxy({
                url: '/PimcoreBulkpump/filterchain'
            }),

            baseParams: {
                configId: config_id
            },

            reader: new Ext.data.JsonReader({
                idProperty: 'id',
                successProperty: 'success',
                messageProperty: 'message'
            })
        });
    },

    getFiltersPanel: function (config_id) {

        var colsFilter = [
            {
                id: 'class',
                header: "Class",
                width: 30,
                sortable: true,
                dataIndex: 'class',
                hidden: true
            },
            {
                header: "Name",
                width: 140,
                sortable: true,
                dataIndex: 'name'
            },
            {
                header: "Description",
                width: 260,
                sortable: true,
                dataIndex: 'description'
            }
        ];

        // Column Model shortcut array
        this.colsFilterChain = [
            { header: "SortOrder", width: 20, sortable: true, dataIndex: 'sort_order', hidden: true },
            { id: 'id', header: "Id", width: 30, sortable: false, dataIndex: 'id', hidden: true },
            { header: "Class", width: 30, sortable: false, dataIndex: 'class', hidden: true },
            { header: "Name", width: 160, sortable: false, dataIndex: 'name' },
            { header: "Description", width: 260, sortable: true, dataIndex: 'description' },
            {
                xtype: 'actioncolumn',
                width: 45,
                id: 'action',
                header: 'action',
                dataIndex: 'action',
                hidden: false,
                items: [
                    {
                        icon: '/plugins/PimcoreBulkpump/static/img/arrow_up.png',
                        tooltip: 'Move up',
                        handler: function (grid, rowIndex) {
                            if(rowIndex > 0)
                            {
                                var row = grid.store.getAt(rowIndex);
                                var sort_order = row.get('sort_order');
                                var other_row = grid.store.getAt(rowIndex - 1);
                                var other_sort_order = other_row.get('sort_order');

                                row.beginEdit();
                                row.set('sort_order', other_sort_order);
                                row.markDirty();
                                row.endEdit();
                                other_row.beginEdit();
                                other_row.set('sort_order', sort_order);
                                other_row.markDirty();
                                other_row.endEdit();
                            }
                        }.bind(this)
                    }, {
                        icon: '/plugins/PimcoreBulkpump/static/img/arrow_down.png',
                        tooltip: 'Move down',
                        handler: function (grid, rowIndex) {
                            if(rowIndex < (grid.store.getCount() - 1))
                            {
                                var row = grid.store.getAt(rowIndex);
                                var sort_order = row.get('sort_order');
                                var other_row = grid.store.getAt(rowIndex + 1);
                                var other_sort_order = other_row.get('sort_order');

                                row.beginEdit();
                                row.set('sort_order', other_sort_order);
                                row.markDirty();
                                row.endEdit();
                                other_row.beginEdit();
                                other_row.set('sort_order', sort_order);
                                other_row.markDirty();
                                other_row.endEdit();
                            }
                        }.bind(this)
                    }
                ]
            }
        ];

        this.filterGridStore = this.getFilterGridStore();
        this.filterChainGridStore = this.getFilterChainGridStore(config_id);

        // declare the source Grid
        this.filterGrid = new Ext.grid.GridPanel({
            flex             : 2,
            ddGroup          : 'filterChainGridDDGroup',
            store            : this.filterGridStore,
            columns          : colsFilter,
            enableDragDrop   : true,
            stripeRows       : true,
            title            : 'Available filters',
            selModel         : new Ext.grid.RowSelectionModel({singleSelect:true}),

            listeners: {
                'rowdblclick': function (grid, rowIndex) {
                    var row = grid.getStore().getAt(rowIndex);
                    var sortOrder = this.filterChainGrid.store.getCount();
                    var newRecord = new DropRecord();

                    newRecord.set('sort_order', ++sortOrder);
                    newRecord.set('id', Ext.id());
                    newRecord.set('class', row.get('class'));
                    newRecord.set('name', row.get('name'));
                    newRecord.set('description', row.get('description'));

                    this.filterChainGrid.store.add(newRecord);
                }.bind(this)
            }
        });


        // create the destination Grid
        this.filterChainGrid = new Ext.grid.GridPanel({
            flex             : 3,
            ddGroup          : 'filterGridDDGroup',
            store            : this.filterChainGridStore,
            columns          : this.colsFilterChain,
            enableDragDrop   : true,
            stripeRows       : true,
            title            : 'Current filter chain',

            listeners: {
                'rowdblclick': function (grid, rowIndex, colIndex) {
                    var row = grid.getStore().getAt(rowIndex);
                    var filter_id = row.get('id');

                    var formPanel = paramsPanel.createFilterParamPanel(this.filterParamPanel, config_id, filter_id);
                }.bind(this)
            }
        });

        this.filterParamPanel = new Ext.Panel({
            flex: 2,
            title: 'Parameters'
        });

        var bgpanel = new Ext.Panel({
            id           : 'backgroundPanel',
            layout: {
                type: 'hbox',
                pack: 'start',
                align: 'stretch'
            },
            items: [
                this.filterGrid,
                this.filterChainGrid,
                this.filterParamPanel
            ]
        });

        return bgpanel;
    },

    getWindow: function (config_id, title) {

        var filtersPanel = this.getFiltersPanel(config_id);
        var popup = new Ext.Window({
            modal:          true,
            id:             'filterwindow',
            config_id:      config_id,
            layout :        'fit',
            floating:       true,
            centered:       true,
            title:          'Filter Configuration: ' + title + '',
            width:          1300,
            height:         760,
            closeAction:    'destroy',
            items: [
                filtersPanel
            ],
            bbar: [],
            buttons: [
                {
                    icon: '/plugins/PimcoreBulkpump/static/img/close.png',
                    text: 'Close',
                    handler: function () {
                        popup.close();
                    }
                }
            ]
        }).show();

        // used to add records to the destination stores
        this.initDragAndDrop();
        return popup;
    },

    initDragAndDrop: function() {
        /****
         * Setup Drop Targets
         ***/
        // delete items
        var filterGridDropTargetEl =  this.filterGrid.getView().scroller.dom;
        var filterGridDropTarget = new Ext.dd.DropTarget(filterGridDropTargetEl, {
            ddGroup    : 'filterGridDDGroup',
            notifyDrop : function(ddSource, e, data) {
                var records = ddSource.dragData.selections;
                Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
                //this.filterChainGrid.store.save();
                return true;
            }.bind(this)
        });

        // add items
        var filterChainGridDropTargetEl = this.filterChainGrid.getView().scroller.dom;
        var filterChainGridDropTarget = new Ext.dd.DropTarget(filterChainGridDropTargetEl, {
            ddGroup    : 'filterChainGridDDGroup',
            notifyDrop : function(ddSource, e, data) {
                var records = ddSource.dragData.selections;
                var arrayLength = records.length;
                var newRecords = [];

                var sort_order = this.filterChainGrid.store.getCount();

                for (var i = 0; i < arrayLength; i++) {
                    var blankRecord = new DropRecord();
                    blankRecord.set('sort_order',   ++sort_order);
                    blankRecord.set('id',           Ext.id());
                    blankRecord.set('class',        records[i].get('class'));
                    blankRecord.set('name',         records[i].get('name'));
                    blankRecord.set('description',  records[i].get('description'));
                    newRecords.push(blankRecord);
                }

                this.filterChainGrid.store.add(newRecords);
                return true;
            }.bind(this)
        });
    },

});

var filterWindow2 = new pimcore.plugin.CsvImport.admin.filterConfigWindow2();


// create a Record constructor for the drop fields
var fields = [
    { name: 'sort_order'     , mapping : 'sort_order' },
    { name: 'id'             , mapping : 'id' },
    { name: 'class'          , mapping : 'class' },
    { name: 'name'           , mapping : 'name' },
    { name: 'description'    , mapping : 'description' }
];
DropRecord = Ext.data.Record.create(fields);
