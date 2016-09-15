pimcore.registerNS('pimcore.plugin.CsvImport.admin.filterConfigWindow2');
Ext.define('FilterChain', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'int', persist: false},
        'sort_order',
        'class',
        'name',
        'description'
    ],

});
Ext.define('Filters', {
    extend: 'Ext.data.Model',
    fields: [
        'class',
        'name',
        'description'
    ],
});
pimcore.plugin.CsvImport.admin.filterConfigWindow2 = Class.create({

    filterGrid: null,
    filterGridStore: null,
    filterChainGrid: null,
    filterChainGridStore: null,
    filterParamPanel: null,
    colsFilterChain: null,

    getFilterGridStore: function () {
       /* return new Ext.data.JsonStore({
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
        });*/
        return Ext.create('Ext.data.Store',{
            autoLoad: true,
            autoSync: true,
            model: 'Filters',
            proxy: {
                type: 'rest',
                url: '/PimcoreBulkpump/filters',
                reader: {
                    type: 'json',
                    rootProperty: 'fields',

                }
            },
            listeners: {

            }
        });
    },

    getFilterChainGridStore: function (config_id) {
        /*
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
        });*/
        return Ext.create('Ext.data.Store',{
            autoLoad: true,
            autoSync: true,
            model: 'FilterChain',
            proxy: {
                type: 'rest',
                url: '/PimcoreBulkpump/filterchain',
                reader: {
                    type: 'json',
                    rootProperty: 'fields',
                    idProperty: 'id',
                },
                writer: {
                    type: 'json',
                    rootProperty: 'fields',
                    idProperty: 'id',
                }
            },
            listeners: {
                beforeload: function(store){
                    store.getProxy().setExtraParam("configId", config_id);
                },
                write: function(store, operation){
                    var record = operation.getRecords()[0],
                        name = Ext.String.capitalize(operation.action),
                        verb;

                    if (name == 'Destroy'){
                        verb = 'Destroyed';
                    }else{
                        verb = name + 'd';
                    }
                    // Ext.example.msg(name, Ext.String.format("{0} user: {1}", verb,record.getId()));
                }
            }
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
        /*this.filterGrid = new Ext.grid.GridPanel({
            flex             : 2,
            ddGroup          : 'filterChainGridDDGroup',
            store            : this.filterGridStore,
            columns          : colsFilter,
            enableDragDrop   : true,
            stripeRows       : true,
            title            : 'Available filters',
            //selModel         : new Ext.grid.RowSelectionModel({singleSelect:true}),
            selModel         : Ext.create('Ext.grid.Panel', {
                selType : 'rowmodel', //can be cellmodel too
                 }),
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
        });*/

        this.filterGrid = Ext.create('Ext.grid.Panel',{
            flex             : 2,
            ddGroup          : 'filterChainGridDDGroup',
            store            : this.filterGridStore,
            columns          : colsFilter,
            enableDragDrop   : true,
            stripeRows       : true,
            title            : 'Available filters',
            //selModel         : new Ext.grid.RowSelectionModel({singleSelect:true}),
            sm         : Ext.create('Ext.grid.Panel', {
                selType : 'rowmodel', //can be cellmodel too
            }),
            listeners: {
                'rowdblclick': function (grid, rowIndex,columnIndex, e) {
                    var row = grid.getStore().getAt(e);
                    var sortOrder = this.filterChainGrid.store.getCount();
                    var newRecord = new FilterChain();
                    console.log(rowIndex);
                    console.log(row);
                    console.log(e);
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
        this.filterChainGrid = Ext.create('Ext.grid.Panel',{
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

        this.filterParamPanel = Ext.create('Ext.panel.Panel',{
            flex: 2,
            title: 'Parameters'
        });

        var bgpanel = Ext.create('Ext.panel.Panel',{
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
       // this.initDragAndDrop();
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



pimcore.registerNS('pimcore.plugin.CsvImport.admin.filterConfigWindow3');
Ext.define('FilterChain', {
    extend: 'Ext.data.Model',
    fields: [
        'sort_order',
        'id',
        'class',
        'name',
        'description'
    ],

});
Ext.define('Filters', {
    extend: 'Ext.data.Model',
    fields: [
        'class',
        'name',
        'description'
    ],
});
Ext.define('DataObject', {
    extend: 'Ext.data.Model',
    fields: ['name', 'column1', 'column2']
});
pimcore.plugin.CsvImport.admin.filterConfigWindow3 = Class.create({

    getSecondGrid: function(){
        var secondGrid = Ext.create('Ext.grid.Panel', {
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragGroup: 'secondGridDDGroup',
                    dropGroup: 'firstGridDDGroup'
                },
                listeners: {
                    drop: function(node, data, dropRec, dropPosition) {
                        var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('name') : ' on empty view';
                        Ext.example.msg("Drag from left to right", 'Dropped ' + data.records[0].get('name') + dropOn);
                    }
                }
            },
            store            : this.getSecondGridStore(),
            columns          : this.getColumns(),
            stripeRows       : true,
            title            : 'Second Grid',
            margins          : '0 0 0 3'
        });
        return secondGrid;
    },
    getFirstGrid: function() {
        var firstGrid = Ext.create('Ext.grid.Panel', {
            multiSelect: true,
            viewConfig: {
                plugins: {
                    ptype: 'gridviewdragdrop',
                    dragGroup: 'firstGridDDGroup',
                    dropGroup: 'secondGridDDGroup'
                },
                listeners: {
                    drop: function (node, data, dropRec, dropPosition) {
                        var dropOn = dropRec ? ' ' + dropPosition + ' ' + dropRec.get('name') : ' on empty view';
                        Ext.example.msg("Drag from right to left", 'Dropped ' + data.records[0].get('name') + dropOn);
                    }
                }
            },
            store: this.getFirstGridStore(),
            columns: this.getColumns(),
            stripeRows: true,
            title: 'First Grid',
            margins: '0 2 0 0'
        });

        return firstGrid;
    },
    getSecondGridStore: function() {
        var secondGridStore = Ext.create('Ext.data.Store', {
            model: 'DataObject'
        });
        return secondGridStore;
    },
    getMyData: function() {
        var myData = [
            {name: "Rec 0", column1: "0", column2: "0"},
            {name: "Rec 1", column1: "1", column2: "1"},
            {name: "Rec 2", column1: "2", column2: "2"},
            {name: "Rec 3", column1: "3", column2: "3"},
            {name: "Rec 4", column1: "4", column2: "4"},
            {name: "Rec 5", column1: "5", column2: "5"},
            {name: "Rec 6", column1: "6", column2: "6"},
            {name: "Rec 7", column1: "7", column2: "7"},
            {name: "Rec 8", column1: "8", column2: "8"},
            {name: "Rec 9", column1: "9", column2: "9"}
        ];
        return myData;
    },
    // create the data store
    getFirstGridStore: function() {
        var firstGridStore = Ext.create('Ext.data.Store', {
            model: 'DataObject',
            data: this.getMyData()
        });
        return firstGridStore;
    },

            // Column Model shortcut array
    getColumns: function() {
        var columns = [
            {text: "Record Name", flex: 1, sortable: true, dataIndex: 'name'},
            {text: "column1", width: 70, sortable: true, dataIndex: 'column1'},
            {text: "column2", width: 70, sortable: true, dataIndex: 'column2'}
        ];

        return columns;
    },

    getWindow: function (config_id, title) {

        //var filtersPanel = this.getFiltersPanel(config_id);
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
                //filtersPanel
                this.getSecondGrid(),
                this.getFirstGrid(),

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
        return popup;
    },
});

var filterWindow3 = new pimcore.plugin.CsvImport.admin.filterConfigWindow3();


