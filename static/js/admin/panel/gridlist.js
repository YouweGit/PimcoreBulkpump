/**
 * Created by bas on 12/3/15.
 */
pimcore.registerNS('pimcore.plugin.CsvImport.admin.panel.gridlist');
pimcore.plugin.CsvImport.admin.panel.gridlist = Class.create({

    initialize: function () {

    },
    getTopToolbar: function () {
        return new Ext.Toolbar({
            renderTo: document.body,
            items: [
                {
                    text: t('add_new_profile'),
                    //iconCls: 'pimcore_icon_publish_medium',
                    scale: "small",
                    handler: this.onAdd.bind(this)
                }, {
                    text: t('remove_profile'),
                    //iconCls: 'pimcore_icon_delete_medium',
                    scale: "small",
                    handler: this.onDelete.bind(this)
                }, {
                    text: t('import_profile'),
                    scale: "small",
                    handler: this.onRun.bind(this)
                }
            ]
        })
    },
    getGridList: function () {

        var pageSize = 30;

       // var store = profiles.getProfileStore();
        var store = profiles.getProfileStore();

        /*

        gridlist.profileGrid = new Ext.grid.GridPanel({
            id: 'importer_grid_profiles',
            store: store,
            columns: gridlist.getColumns(profiles.profileGrid),
            listeners: {
                rowcontextmenu: this.showMenu.bind(this),
                rowclick: this.onRowClick.bind(this),
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
            sm: new Ext.selection.RowModel({singleSelect: true}),
            stripeRows: true,
            //width: 329,
            region: 'west',
            viewConfig: {
                forceFit: true,
                scrollOffset: 5,
            },
            tbar: [
                gridlist.getTopToolbar()
            ],
            bbar: new Ext.PagingToolbar({
                store: store,
                pageSize: pageSize
            })
        });
        //gridlist.profileGrid.getSelectionModel().on('rowclick', this.onCellClick.bind(this));
        gridlist.profileGrid.getStore().reload();*/
        gridlist.profileGrid = Ext.create('Ext.grid.Panel', {
            bufferedRenderer: false,
            id: 'importer_grid_profiles',
            store: store,
            columns: gridlist.getColumns(profiles.profileGrid),
            forceFit: true,
            height:210,
            split: true,
            region: 'west',
            listeners: {
                rowclick: function(searchgrid, rowIndex, e) {
                   // var rec = grid.getStore().getAt(rowIndex);
                    tabPanel.openTab(rowIndex.id);
                }

            },
            tbar: [
                gridlist.getTopToolbar()
            ],

        });

        return gridlist.profileGrid;
    },
    getColumns: function (self) {
        return [
            {
                header: t('id'),
                dataIndex: 'id',
                width: 30,
                sortable: true
            },
            {
                header: t('profile_name'),
                dataIndex: 'profile_name',
                sortable: true
            },
            {
                header: t('last modified'),
                dataIndex: "modificationDate",
                sortable: true
            },
            {
                header: t('created'),
                dataIndex: "creationDate",
                sortable: true,
                hidden: true
            }
        ]
    },
    showMenu: function (grid, index, event) {
        event.stopEvent();
        var record = grid.getStore().getAt(index);
        new Ext.menu.Menu({
            items: [{
                text: t("clone_profile"),
                handler: function () {
                    this.duplicateProfile(grid, record.id);
                }.bind(this),
                iconCls: 'pimcore_icon_clone'
            }]
        }).showAt(event.xy);
    },
    duplicateProfile: function (grid, profileId) {
        Ext.Ajax.request({
            url: "/plugin/PimcoreBulkpump/profile/duplicate",
            method: 'POST',
            params: {
                profileId: profileId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success === false && data.message) {
                    Ext.MessageBox.alert(t('error'), data.message);
                    return;
                } else if (!data.message) {
                    Ext.MessageBox.alert(t('error'), t('something_went_wrong_please_retry'));
                    return;
                }

                grid.getStore().reload();
            }.bind(this)
        });
    },
    onRowClick: function (grid, rowIndex, e) {
        var rec = grid.getStore().getAt(rowIndex);
        tabPanel.openTab(rec.id);
    },
    onAdd: function (btn, ev) {
        var profileGrid = Ext.getCmp('importer_grid_profiles');
        var profileStore = profileGrid.store;

        var model = new Profile({
            profile_name: null,
            attribute_language_separator: '_'});

        profileStore.add(model);
        //profileStore.sync();
        /*
        var newProfile = profileStore.recordType;
        var profile = new newProfile({
                profile_name: null,
                attribute_language_separator: '_'
            });
        profileGrid.stopEditing();
        profileStore.insert(profileStore.getCount(), profile);*/
    },
    onDelete: function (btn, ev) {
        var profileGrid = Ext.getCmp('importer_grid_profiles'),
            profileStore = profileGrid.getStore(),
            selectedItem = profileGrid.getView().getSelectionModel().getSelection()[0];
        if (selectedItem == undefined) {
            Ext.MessageBox.alert(t('error'), t('please_select_a_profile_to_delete'));
            return;
        }
        Ext.MessageBox.confirm(t('confirm'), t('are_you_sure_you_want_to_delete_this_profile'), function (value) {
            if (value == 'yes') {
                profileStore.remove(selectedItem);

                var tabComponent = Ext.getCmp("csv_importer_profile_config_tabs");

            }
        });
    },
    onRun:function(){
        var profileGrid = Ext.getCmp('importer_grid_profiles'),
            profileStore = profileGrid.getStore(),
            selectedItem = profileGrid.getView().getSelectionModel().getSelection()[0];
        if (selectedItem == undefined) {
            Ext.MessageBox.alert(t('error'), t('please_select_a_profile_to_run'));
            return;
        }
        Ext.MessageBox.confirm(t('confirm'), t('are_you_sure_you_want_to_run_this_profile'), function (value) {
            if (value == 'yes') {
                logClass.triggerImport(selectedItem.id);
            }
        });
    }
});

var gridlist = new pimcore.plugin.CsvImport.admin.panel.gridlist();