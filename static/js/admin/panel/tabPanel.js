pimcore.registerNS('pimcore.plugin.CsvImport.admin.panel.tabPanel');
pimcore.plugin.CsvImport.admin.mainPanel.tabPanel = Class.create(
    {
        subPanel: {},
        /**
         *
         * @returns {Ext.TabPanel}
         */
        getTabPanel: function () {
            return new Ext.TabPanel({
                id: 'csv_importer_profile_config_tabs',
                autoTabs: true,
                autoScroll: true,
                layoutOnTabChange: true,
                renderTo: document.body,
                region: "center",
                items: []
            })
        },
        /**
         *
         * @param id
         */
        openTab: function (id) {
            var tabComponent = Ext.getCmp("csv_importer_profile_config_tabs");

            var panelToActivate = tabComponent.getItem('csv_import_config_panel_' + id);

            if(panelToActivate === undefined){
                var subPanel = tabPanel.getSubPanel(id);
                tabComponent.add( subPanel);
                tabComponent.activate(subPanel);

            }else{
                tabComponent.activate(panelToActivate);

            }
        },
        /**
         *
         * @param id
         * @returns {Ext.Panel}
         */
        getSubPanel: function (id) {

            return new Ext.Panel({
                id: "csv_import_config_panel_" + id,
                title: t('configuration') + " id: " + id,
                border: false,
                autoScroll: true,
                layout: 'fit',
                region: "center",
                iconCls: 'pimcore_icon_tab_dependencies',
                closable: true,
                items: [
                    tabPanel.getSubTabs(id)
                ],
                tbar: [
                ],
                listeners: {
                    close: function(p) {
                        p.hide();
                    }
                }
            });
        },
        /**
         *
         * @param id
         * @returns {*[]}
         */
        getSubTabs: function (id)
        {
            return new Ext.TabPanel({
                margins: '0 0 0 0',
                id: 'csv_importer_sub_tabs' + id,
                autoTabs: true,
                autoScroll: true,
                layoutOnTabChange: true,
                renderTo: document.body,
                region: "center",
                layoutConfig: {
                    padding: '0 0 0 0',
                    margin: '0 0 0 0'
                },
                items: [
                    profileTab.getPanel(id),
                    configTab.getPanel(id),
                    logsTab.getPanel(id),
                    dataTab.getPanel(id)
                ],
               listeners: {
                    render: function(self){
                        self.activate(self.items.items[0]);
                        profileTab.checkEnabled(id);
                    }
               }
           });

        },
        /**
         *
         * @returns {Ext.Toolbar}
         */
        getTopToolbar: function (id) {
            return new Ext.Toolbar({
                id: "csv_import_config_tab_panel_toolbar_top_" + id,
                renderTo: document.body,
                items: [
                    {
                        text: t('save_profile'),
                        iconCls: 'pimcore_icon_publish_medium',
                        scale: 'medium',
                        handler: {}
                    }, {
                        text: t('remove_profile'),
                        iconCls: 'pimcore_icon_delete_medium',
                        scale: 'medium',
                        handler: {}
                    }, {
                        text: t('reload profile'),
                        iconCls: 'pimcore_icon_reload_medium',
                        scale: 'medium',
                        handler:{}
                    },
                    {
                        text: t('run import'),
                        iconCls: 'pimcore_icon_upload_medium',
                        scale: 'medium',
                        handler: {}
                    }
                ]
            });
        }
    });
var tabPanel = new pimcore.plugin.CsvImport.admin.mainPanel.tabPanel();