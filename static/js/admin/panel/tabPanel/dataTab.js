pimcore.registerNS('pimcore.plugin.CsvImport.admin.dataTab');
pimcore.plugin.CsvImport.admin.dataTab = Class.create(
    {

        grid: {},
        /**
         * @param id
         * @returns {Ext.Panel}
         */
        getPanel: function (id) {
            return new Ext.Panel({
                id: 'importer_data_panel' + id,
                profile_id: id,
                title: t('data'),
                margins: '0 0 0 0',
                region: 'center',
                border: false,
                layout: 'fit',
                hidden: true,
                disabled: true,
                items: [
                    dataTab.getGrid(id)
                ]
            });
        },
        getGrid: function (id) {
            var store = this.getDataStore(id);

            dataTab.grid = Ext.create('Ext.grid.Panel', {
                id: 'csv_import_data_grid' + id,
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
                            header: 'id2',
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
                            //editor: //columns.getColumnsCombo(id)
                        },
                        {
                            id: 'language',
                            header: 'Language',
                            dataIndex: 'language',
                            sortable: true,
                            width: 30
                        },
                        {
                            id: 'overwrite_empty',
                            header: 'Overwrite with empty',
                            dataIndex: 'overwrite_empty',
                            sortable: false,
                            hidden: false,
                            width: 30,
                            xtype: 'checkcolumn'
                        }
                    ]
                }),
                listeners: {
                    render: function (self) {
                        self.getStore().load();
                    }
                },
                tbar: dataTab.getToolbar(id)
            });
            return dataTab.grid;
        },
        getDataStore: function (id) {
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
                    url: '/PimcoreBulkpump/config'     // @TODO: fix data proxy !!!!!
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
            });
        },
        getToolbar: function (id) {
            return new Ext.Toolbar({
                items: [
                    {
                        text: t('reload'),
                        iconCls: 'pimcore_icon_reload_medium',
                        scale: 'medium',
                        handler: function (button) {
                            var grid = button.findParentByType('grid');
                            var store = grid.getStore();
                            store.reload({
                                callback: function () {
                                    var comboBox = grid.getColumnModel().getColumnById("csv_field");
                                    comboBox.getEditor().getStore().reload({
                                        callback: function () {
                                            pimcore.helpers.showNotification(t('data'), t('data_reloaded_confirmation'));
                                        }
                                    });
                                }
                            });
                            grid.doLayout();
                        }
                    }
                ]
            });
        }
    }
);

var dataTab = new pimcore.plugin.CsvImport.admin.dataTab();
