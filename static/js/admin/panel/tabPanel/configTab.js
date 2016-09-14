pimcore.registerNS('pimcore.plugin.CsvImport.admin.configTab');
Ext.define('Config', {
    extend: 'Ext.data.Model',
    fields: [
        'id',
        'pimcore_field',
        'fieldtype',
        'language',
        'csv_field',
        'filters',
        'overwrite_empty'
    ],

});
pimcore.plugin.CsvImport.admin.configTab = Class.create(
    {

        grid: {},
        /**
         * @param id
         * @returns {Ext.Panel}
         */
        getPanel: function (id) {
            /*
            return new Ext.Panel({
                id: 'importer_config_form_panel' + id,
                profile_id: id,
                title: t('configuration'),
                margins: '0 0 0 0',
                region: 'center',
                border: false,
                layout: 'fit',
                disabled: true,
                items: [
                    configTab.getGrid(id)
                ]
            });*/
            return Ext.create('Ext.panel.Panel',{
                id: 'importer_config_form_panel' + id,
                profile_id: id,
                title: t('configuration'),
                margins: '0 0 0 0',
                region: 'center',
                anchor: "100% 100%",
                border: false,
                layout: 'fit',
                disabled: false,
                items: [
                    configTab.getGrid(id)
                ]
            });
        },
        getGrid: function (id) {
            var store = this.getConfigStore(id);
            var storeColumns = this.getColumnStore(id);

            configTab.grid = Ext.create('Ext.grid.Panel', {
                id: 'csv_import_config_grid' + id,
                store: store,
                layout: 'fit',
                region: 'north',
                anchor: "100% 100%",
                iconCls: 'icon-grid',
                stripeRows: true,
                tbar: configTab.getToolbar(id),
                viewConfig: {
                    forceFit: true
                },
                plugins: [
                    Ext.create('Ext.grid.plugin.CellEditing', {
                        clicksToEdit: 1
                    })
                ],
                columns: [
                    {
                        id: 'id',
                        header: 'id',
                        dataIndex: 'id',
                        sortable: true,
                        hidden: true
                    },
                    {
                        id: 'pimcore_field',
                        header: 'Pimcore field',
                        dataIndex: 'pimcore_field',
                        sortable: true
                    },
                    {
                        id: 'fieldtype',
                        header: 'Field type',
                        dataIndex: 'fieldtype',
                        sortable: true
                    },
                    {
                        id: 'csv_field',
                        header: 'CSV field',
                        dataIndex: 'csv_field',
                        sortable: true,
                        editor: {
                            xtype: 'combo',
                            store: storeColumns
                        }
                    },
                    {
                        id: 'csv_field',
                        header: 'CSV field b',
                        dataIndex: 'csv_field',
                        sortable: true,
                        editor: columns.getColumnsCombo(id)
                    },
                    {
                        id: 'language',
                        header: 'Language',
                        dataIndex: 'language',
                        sortable: true,
                        width: 30
                    },
                    {
                        id: 'filters',
                        header: 'Filters',
                        dataIndex: 'filters',
                        sortable: true,
                        hidden: false,
                        width: 50,
                        renderer: function (val) {
                            var data = Ext.decode(val);
                            if(data)
                            {
                                if(data.length > 1)
                                {
                                    return '(' + data.length + ' chained)';
                                }
                                else if(data.length == 1)
                                {
                                    return data[0].class.split('\\').pop();
                                }
                                else {
                                    return '';
                                }
                            }
                        }
                    },
                    {
                        id: 'overwrite_empty',
                        header: 'Overwrite with empty',
                        dataIndex: 'overwrite_empty',
                        sortable: false,
                        hidden: false,
                        width: 30,
                        xtype: 'checkcolumn'
                    },
                    {
                        xtype: 'actioncolumn',
                        id: 'action',
                        header: 'Action',
                        dataIndex: 'action',
                        width: 30,
                        hidden: false,
                        items: [
                            {
                                icon: '/plugins/PimcoreBulkpump/static/img/filter.png',
                                tooltip: 'Filter configuration',
                                handler: function (grid, rowIndex) {

                                    var row = store.getAt(rowIndex);
                                    var config_id = row.get('id');
                                    var filterWindow = filterWindow2.getWindow(config_id, '[csv] ' + (row.get('csv_field')?row.get('csv_field'):'<i>unknown</i>') + ' to [pim] ' + row.get('pimcore_field'));
                                    filterWindow.on('destroy', function() {
                                        store.reload();
                                    });

                                }.bind(this)
                            }
                        ]
                    }
                ],
            });
            /*
            configTab.grid = new Ext.grid.EditorGridPanel({
                id: 'csv_import_config_grid' + id,
                store: store,
                layout: 'fit',
                region: 'east',
                anchor: "100% 100%",
                iconCls: 'icon-grid',
                stripeRows: true,
                viewConfig: {
                    forceFit: true
                },
                colModel: new Ext.grid.ColumnModel({
                    defaults: {
                        sortable: true
                    },
                    columns: [
                        {
                            id: 'id',
                            header: 'id',
                            dataIndex: 'id',
                            sortable: true,
                            hidden: true
                        },
                        {
                            id: 'pimcore_field',
                            header: 'Pimcore field',
                            dataIndex: 'pimcore_field',
                            sortable: true
                        },
                        {
                            id: 'fieldtype',
                            header: 'Field type',
                            dataIndex: 'fieldtype',
                            sortable: true
                        },
                        {
                            id: 'csv_field',
                            header: 'CSV field',
                            dataIndex: 'csv_field',
                            sortable: true,
                            editor: columns.getColumnsCombo(id)
                        },
                        {
                            id: 'language',
                            header: 'Language',
                            dataIndex: 'language',
                            sortable: true,
                            width: 30
                        },
                        {
                            id: 'filters',
                            header: 'Filters',
                            dataIndex: 'filters',
                            sortable: true,
                            hidden: false,
                            width: 50,
                            renderer: function (val) {
                                var data = Ext.decode(val);
                                if(data)
                                {
                                    if(data.length > 1)
                                    {
                                        return '(' + data.length + ' chained)';
                                    }
                                    else if(data.length == 1)
                                    {
                                        return data[0].class.split('\\').pop();
                                    }
                                    else {
                                        return '';
                                    }
                                    console.log(data);
                                }
                            }
                        },
                        {
                            id: 'overwrite_empty',
                            header: 'Overwrite with empty',
                            dataIndex: 'overwrite_empty',
                            sortable: false,
                            hidden: false,
                            width: 30,
                            xtype: 'checkcolumn'
                        },
                        {
                            xtype: 'actioncolumn',
                            id: 'action',
                            header: 'Action',
                            dataIndex: 'action',
                            width: 30,
                            hidden: false,
                            items: [
                                {
                                    icon: '/plugins/PimcoreBulkpump/static/img/filter.png',
                                    tooltip: 'Filter configuration',
                                    handler: function (grid, rowIndex) {
                                        
                                        var row = store.getAt(rowIndex);
                                        var config_id = row.get('id');
                                        var filterWindow = filterWindow2.getWindow(config_id, '[csv] ' + (row.get('csv_field')?row.get('csv_field'):'<i>unknown</i>') + ' to [pim] ' + row.get('pimcore_field'));
                                        filterWindow.on('destroy', function() {
                                            store.reload();
                                        });

                                    }.bind(this)
                                }
                            ]
                        }
                    ]
                }),
                listeners: {
                    render: function (self) {
                        self.getStore().load();
                    }
                },
                tbar: configTab.getToolbar(id)
            });*/
            return configTab.grid;
        },
        getColumnStore: function(id){
            return Ext.create('Ext.data.Store',{

            });
        },
        getConfigStore: function (id) {
            /*
            return new Ext.data.JsonStore({
                root: 'fields',
                restful: true,
                // autoSave: true,
                // autoSet: true,
                fields: [
                    'id',
                    'pimcore_field',
                    'fieldtype',
                    'language',
                    'csv_field',
                    'filters',
                    'overwrite_empty'
                ],
                writer: new Ext.data.JsonWriter({
                    encode: false,
                    type: 'json',
                    writeAllFields: true,
                    root: 'fields'
                }),
                proxy: new Ext.data.HttpProxy({
                    url: '/PimcoreBulkpump/config'
                }),
                baseParams: {
                    profileId: id
                },
                reader: new Ext.data.JsonReader({
                    successProperty: 'success',
                    idProperty: 'id',
                    messageProperty: 'message',
                    totalProperty: 'totalCount'
                })
            });*/

            return Ext.create('Ext.data.Store',{
                autoLoad: true,
                autoSync: true,
                model: 'Config',
                proxy: {
                    type: 'rest',
                    url: '/PimcoreBulkpump/config',
                    reader: {
                        type: 'json',
                        rootProperty: 'fields'
                    },
                    writer: {
                        type: 'json',
                        rootProperty: 'fields'
                    }
                },
                listeners: {
                    beforeload: function(store){
                        store.getProxy().setExtraParam("profileId", id);
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
        getToolbar: function (id) {
            return new Ext.Toolbar({
                items: [
                    {
                        text: t('reload'),
                        //iconCls: 'pimcore_icon_reload_medium',
                        scale: 'small',
                        handler: function (button) {
                            /*
                            var grid = button.findParentByType('grid');
                            var store = grid.getStore();
                            store.reload({
                                callback: function () {
                                    var comboBox = grid.getColumnModel().getColumnById("csv_field");
                                    comboBox.getEditor().getStore().reload({
                                        callback: function () {
                                            pimcore.helpers.showNotification(t('configuration'), t('configuration_reloaded_confirmation'));
                                        }
                                    });
                                }
                            });
                            grid.doLayout();*/
                        }
                    },
                    {
                        text: t('start import'),
                        //iconCls: 'pimcore_icon_upload_medium',
                        scale: 'small',
                        handler: function (button) {
                            logClass.triggerImport(id);
                        }
                    }
                ]
            });
        }
    }
);
var configTab = new pimcore.plugin.CsvImport.admin.configTab();



Ext.onReady(function(){

    //this.propsGrid = new Ext.grid.PropertyGrid({
    //    renderTo: 'prop-grid',
    //    width: 300,
    //    autoHeight: true,
    //    propertyNames: {
    //        tested: 'QA',
    //        borderWidth: 'Border Width'
    //    },
    //    source: {
    //        '(name)': 'Properties Grid',
    //        grouping: false,
    //        autoFitColumns: true,
    //        productionQuality: false,
    //        created: new Date(Date.parse('10/15/2006')),
    //        tested: false,
    //        version: 0.01,
    //        borderWidth: 1
    //    },
    //    viewConfig : {
    //        forceFit: true,
    //        scrollOffset: 2 // the grid will never have scrollbars
    //    }
    //});

    // simulate updating the grid data via a button click
    //new Ext.Button({
    //    renderTo: 'button-container',
    //    text: 'Update source',
    //    handler: function(){
    //        //this.propsGrid.setSource({
    //        //    '(name)': 'Property Grid',
    //        //    grouping: false,
    //        //    autoFitColumns: true,
    //        //    productionQuality: true,
    //        //    created: new Date(),
    //        //    tested: false,
    //        //    version: 0.8,
    //        //    borderWidth: 2
    //        //});
    //    }.bind(this)
    //});
});