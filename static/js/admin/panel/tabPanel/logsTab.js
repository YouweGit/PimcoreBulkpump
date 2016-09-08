pimcore.registerNS('pimcore.plugin.CsvImport.admin.logsTab');
pimcore.plugin.CsvImport.admin.logsTab = Class.create(
    {
        /**
         *
         * @param id
         * @returns {Ext.Panel}
         */
        getPanel: function (id) {
            return new Ext.Panel({
                title: t('log'),
                id: "csv_import_config_logs",
                iconCls: 'pimcore_icon_tab_versions',
                border: false,
                hidden: true,
                disabled: true,
                items: [
                    logsTab.getProfileLogGrid(id),
                    logsTab.getLogRecordsGrid()
                ]
            });
        },
       getProfileLogGrid: function (id) {
            var columns = [
                {
                    header: t('status'),
                    dataIndex: 'status',
                    width: 10,
                    renderer: function (value) {
                        if (value == 'ok') {
                            return '<span style="color:green;">' + value + '</span>';
                        } else if (value == 'error') {
                            return '<span style="color:red;">' + value + '</span>';
                        }
                        return value;
                    },
                    sortable: false
                },
                {
                    header: t('time'),
                    type: 'date',
                    renderer: function (value) {
                        var datetime = new Date(parseInt(value) * 1000);
                        return Ext.util.Format.date(datetime, 'Y-m-d H:i:s P');
                    },
                    dataIndex: 'time',
                    sortable: false
                }
            ];

            var profileLogGrid = new Ext.grid.GridPanel({
                id: 'importer_grid_profile_logs',
                region: 'north',
                height: 300,
                split: true,
                store: logsTab.getProfileLogsStore(),
                sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
                columns: columns,
                stripeRows: true,
                autoScroll: true,
                title: t('entries'),
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    render: function (grid) {
                        grid.store.on('load', function (store, records, options) {
                            // If empty just empty the logs content as well
                            if (store.getCount() == 0) {
                                var logRecordsGrid = Ext.getCmp('importer_grid_logs_records');
                                logRecordsGrid.getStore().loadData([]);
                            } else {
                                grid.getSelectionModel().selectFirstRow();
                            }
                        });
                    }.bind(this)
                },
                tbar: logsTab.getToolbar(id)
            });
            profileLogGrid.getSelectionModel().on('rowselect', function (sm, rowIdx, record) {
                var logRecordsGrid = Ext.getCmp('importer_grid_logs_records');
                logRecordsGrid.getStore().loadData(record.data.content);
            });
            return profileLogGrid;
        },
        getToolbar: function(id) {
            return new Ext.Toolbar({
                height: 35,
                items: [
                    //{
                    //    text:       t('start import'),
                    //    iconCls:    'pimcore_icon_upload_medium',
                    //    height:     35,
                    //    handler: function(button) {
                    //        log.triggerImport(id);
                    //    }
                    //}
                ]
            });
        },
        getProfileLogsStore: function () {
            return new Ext.data.JsonStore({
                url: '/plugin/PimcoreBulkpump/profile/get-logs',
                restful: false,
                autoDestroy: true,
                autoLoad: false,
                root: 'logs',
                fields: [
                    'status',
                    'time',
                    'content'
                ]
            });
        },
        getLogRecordsGrid: function () {
            return new Ext.grid.GridPanel({
                id: 'importer_grid_logs_records',
                title: t('log_content'),
                region: 'center',
                autoScroll: true,
                autoHeight: true,
                store: this.getLogRecordsStore(),
                cm: new Ext.grid.ColumnModel({
                    defaults: {
                        sortable: false
                    },
                    columns: [
                        {
                            id: 'time', header: t('time'), width: 120, dataIndex: 'time'
                        },
                        {
                            id: 'status', header: t('status'), width: 95, dataIndex: 'status'
                        },
                        {
                            id: 'text', header: t('text'), dataIndex: 'text',
                            renderer: function (value, metadata, record, rowIndex, colIndex, store) {
                                var html = Ext.util.Format.htmlEncode(value);
                                metadata.attr = 'ext:qtip="' + html + '"';
                                return value;
                            }
                        }
                    ]
                }),
                autoExpandColumn: 'text',
                stripeRows: true
            });
        },
        getLogRecordsStore: function () {
            return new Ext.data.ArrayStore({
                autoLoad: false,
                local: true,
                fields: ['time', 'status', 'text']
            });
        }
    }
);

var logsTab = new pimcore.plugin.CsvImport.admin.logsTab();
