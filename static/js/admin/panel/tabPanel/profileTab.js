/**
 * Created by bas on 11/10/15.
 */
pimcore.registerNS('pimcore.plugin.CsvImport.admin.profileTab');
pimcore.plugin.CsvImport.admin.profileTab = Class.create(
    {
        initialize: function () {

        },
        id: null,
        files: this,
        fileGrid: {},
        rec: {},
        getColumns: function () {
            return [
                {
                    header: t('file_name'),
                    sortable: true,
                    dataIndex: 'name'
                },
                {
                    header: t('last modified'),
                    sortable: true,
                    dataIndex: "lastmod"
                },
                {
                    header: t("created"),
                    sortable: true,
                    dataIndex: "created"
                }
            ]
        },
        getTopToolbar: function (id) {
            return Ext.create('Ext.toolbar.Toolbar',{
                renderTo: document.body,
                //height: 35,
                items: [
                    {
                        text: t('save'),
                        id: 'btn_save_profile_' + id,
                       // iconCls: 'pimcore_icon_publish_medium',
                        scale: 'small',
                        handler: function (button) {
                            var form = Ext.getCmp('csv_import_selected_form_' + id);
                            var values = form.getForm().getFieldValues();
                            profiles.saveRecord(values, id, this.updateProfileCallback);
                        }.bind(this)
                    },
                    {
                        text: t('add_new_file'),
                       // iconCls: 'pimcore_icon_publish_medium',
                        scale: 'small',
                        handler: function (self) {
                            //console.log(id);
                            var window = Ext.getCmp('csv_import_file_upload_window');
                            //console.log(window);
                            window.profileId = id;
                            window.show();
                        }
                    }
                ]
            })
        },
        updateProfileCallback: /**
         * @param {Ext.data.Store} store
         * @param {Ext.data.Record} record
         * @param {String} operation  Ext.data.Record.EDIT || Ext.data.Record.REJECT || Ext.data.Record.COMMIT
         */
            function (store, record, operation) {
            var button = Ext.getCmp('btn_save_profile_' + record.id);
            button.disable();
            var _configTabGrid = Ext.getCmp("csv_import_config_grid" + record.id);

            var _configTabStore = _configTabGrid.getStore();
            _configTabStore.fireEvent('datachanged', _configTabStore);
            _configTabStore.reload({
                callback: function (records, operation, success) {
                    if (success) {
                        // reload csv field selection box
                        var comboBox = _configTabGrid.getColumnModel().getColumnById("csv_field");
                        comboBox.getEditor().getStore().reload({
                            callback: function () {
                                button.findParentByType('panel').doLayout();
                                _configTabGrid.doLayout();
                                profileTab.checkEnabled(record.id);
                                button.enable();
                                pimcore.helpers.showNotification(t('save_notification_title'), t('save_notification'));
                            }
                        });
                    }
                }
            });

        },
        /**
         *
         * @param id
         * @returns {Ext.Panel}
         */
        getPanel: function (id) {
            this.id = id;
            return new Ext.Panel({
                margins: '0 0 0 0',
                title: t('Profile'),
                id: "csv_import_profileTab" + id,
                layout: 'fit',
                region: 'center',
                viewConfig: {
                    forceFit: true
                },
                items: [
                    profileTab.getProfileForm(id)
                ],
                tbar: [
                    profileTab.getTopToolbar(id)
                ]
            });
        },
        getProfileForm: function (id) {
            var define_field_width = 410;
            return new Ext.FormPanel({
                id: "csv_import_selected_form_" + id,
                bodyStyle: 'padding: 10px 10px 10px 10px;',
                labelWidth: 100,
                items: [
                    {
                        xtype: 'fieldset',
                        title: t('selected file'),
                        defaults: {width: define_field_width},
                        defaultType: 'textfield',
                        items: [
                            {
                                id: "profile_name" + id,
                                name: 'profile_name',
                                fieldLabel: t('profile_name'),
                                width: define_field_width
                            },
                            {
                                id: "load_path_field" + id,
                                name: 'load_path',
                                readOnly: true,
                                fieldLabel: t('load_path'),
                                width: define_field_width
                            },
                            object.getObjectsCombo(id),
                            {
                                xtype: "combo",
                                store: new Ext.data.ArrayStore({
                                    id: 0,
                                    fields: [
                                        'import_type_box',
                                        'display_text'
                                    ],
                                    data: [['object', 'Object'], ['attributes', 'Attributes'], ['custom', 'Custom']],
                                }),
                                valueField: 'import_type_box',
                                displayField: 'display_text',
                                typeAhead: true,
                                triggerAction: 'all',
                                lazyRender: true,
                                mode: 'local',
                                allowBlank: false,
                                id: "import_type" + id,
                                name: 'import_type',
                                fieldLabel: t('import_type'),
                                width: define_field_width,
                                listeners: {
                                    select: function (el) {
                                        var value = el.getValue();
                                        this.displaySettingsForImportType(id, value);
                                    }.bind(this)
                                }
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        id: 'csv_import_settings_object_' + id,
                        hidden: true,
                        title: t('settings_for_import_type_object'),
                        defaults: {width: define_field_width},
                        defaultType: 'textfield',
                        items: [
                            {
                                name: 'save_to_path',
                                fieldLabel: t('save_to_path'),
                                width: define_field_width,
                                allowBlank: false,
                                listeners: {}
                            },

                            {
                                fieldLabel: t('store_as_variant'),
                                name: 'store_as_variant',
                                xtype: 'checkbox',
                                listeners: {}
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        id: 'csv_import_settings_attributes_' + id,
                        hidden: true,
                        title: t('settings_for_import_type_attributes'),
                        defaults: {width: define_field_width},
                        defaultType: 'textfield',
                        items: [
                            {
                                name: 'attributes_start_after',
                                fieldLabel: t('attributes_start_after_field'),
                                width: define_field_width,
                                allowBlank: true
                            },
                            {
                                name: 'classification_store_field',
                                fieldLabel: t('classification_store_field'),
                                width: define_field_width,
                                allowBlank: true
                            },
                            {
                                name: 'classification_field',
                                fieldLabel: t('classification_field'),
                                width: define_field_width,
                                allowBlank: true
                            },
                            {
                                name: 'attribute_language_separator',
                                fieldLabel: t('language_separator'),
                                width: define_field_width,
                                allowBlank: true
                            }

                        ]
                    },
                    {
                        xtype: 'fieldset',
                        id: 'csv_import_settings_custom_' + id,
                        hidden: true,
                        title: t('settings_for_import_type_custom'),
                        defaults: {width: define_field_width},
                        defaultType: 'textfield',
                        items: [
                            {
                                name: 'custom_class',
                                fieldLabel: t('custom_class'),
                                width: define_field_width,
                                allowBlank: false
                            },
                            {
                                xtype: "displayfield",
                                hideLabel:true,
                                width: 500,
                                html:'<span class="object_field_setting_warning">' +t('custom_class_help')+'</span>'
                            }
                        ]
                    }
                ],
                listeners: {
                    'beforerender': function (self) {
                        var newPanel = self;
                        var profileStore = profiles.getProfileStore();
                        profileStore.on('load', function (self, records, options) {
                            var rec = profileStore.getById(id);
                            newPanel.getForm().loadRecord(rec);
                            this.displaySettingsForImportType(id, rec.data.import_type);
                            //newPanel.doLayout();
                        }.bind(this));
                        profileStore.load();
                    }.bind(this)
                }
            });
        },
        displaySettingsForImportType: function (id, importType) {
            var object = Ext.getCmp('csv_import_settings_object_' + id),
                attributes = Ext.getCmp('csv_import_settings_attributes_' + id),
                custom = Ext.getCmp('csv_import_settings_custom_' + id);

            object.hide();
            attributes.hide();
            custom.hide();

            switch (importType) {
                case 'object':
                    object.show();
                    break;
                case 'attributes':
                    attributes.show();
                    break;
                case 'custom':
                    object.show();
                    custom.show();
                    break;
            }
        },
        getGrid: function (id) {
            var pageSize = 30;
            var store = profileTab.getFileStore();
            profileTab.fileGrid = new Ext.grid.GridPanel({
                id: 'importer_grid_files' + id,
                store: store,
                layout: 'fit',
                anchor: "100%",
                height: 300,
                columns: profileTab.getColumns(),
                listeners: {
                    render: {
                        fn: function () {
                            store.load({
                                params: {
                                    start: 0,
                                    limit: pageSize
                                }
                            });
                        }
                    }
                },
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                stripeRows: true,
                clicksToEdit: 2,
                region: 'east',
                tbar: [
                    profileTab.getTopToolbar(id)
                ],
                bbar: new Ext.PagingToolbar({
                    store: store,
                    pageSize: pageSize
                })
            });
            profileTab.fileGrid.getSelectionModel().on('rowselect', this.onRowSelect.bind(this));
            profileTab.fileGrid.getStore().reload();
            return profileTab.fileGrid;
        },
        getFileStore: function () {
            var store = new Ext.data.JsonStore({
                restful: true,
                fields: [
                    'name',
                    {
                        name: 'lastmod',
                        type: 'date',
                        dateFormat: 'timestamp'
                    },
                    {
                        name: 'size',
                        type: 'int'
                    },
                    {
                        name: "created",
                        type: "date",
                        dateFormat: "timestamp"
                    }
                ],
                root: 'file',
                writer: new Ext.data.JsonWriter({
                    encode: false
                }),
                proxy: new Ext.data.HttpProxy({
                    url: '/plugin/PimcoreBulkpump/file'
                }),
                baseParams: {},
                reader: new Ext.data.JsonReader({
                    successProperty: 'success',
                    messageProperty: 'message'
                }),
                getRecordByIndex: function (index) {
                    return store.getRange()[index];
                }

            });
            store.load();
            return store;
        },
        onRowSelect: function (sm, rowIdx, rec) {
            profileTab.rec = rec;
        },
        onReload: function (btn, ev) {
            var fileGrid = Ext.getCmp('importer_grid_files' + this.id);
            fileGrid.getStore().reload();
        },

        checkEnabled: function (id) {
            var configForm = Ext.getCmp('importer_config_form_panel' + id);
            var profileForm = Ext.getCmp('csv_import_selected_form_' + id);
            var objectValue = !profileForm.getForm().findField("object").getValue();
            configForm.setDisabled(objectValue);
        }

    }
);

var profileTab = new pimcore.plugin.CsvImport.admin.profileTab();