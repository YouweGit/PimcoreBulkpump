pimcore.registerNS('pimcore.plugin.CsvImport.admin.mainPanel');
pimcore.plugin.CsvImport.admin.mainPanel = Class.create(
    {
        tabPanel:     {},
        getClassName: function () {
            return 'pimcore.plugin.CsvImport.admin.mainPanel';
        },
        initialize:   function () {
        },
        getPanel:     function () {
            var panel = new Ext.Panel({
                id:       "csv_product_import_tab",
                title:    t('csv_product_importer_mapper'),
                iconCls:  'pimcore_icon_routes',
                layout:   'border',
                closable: true,
                items:    [
                    {
                        region:      'west',
                        margins:     '0 0 0 0',
                        layout:      'fit',
                        title:       t("import_profiles"),
                        split:       true,
                        minSize:     150,
                        collapsible: true,
                        width:       300,
                        items:       [
                            gridlist.getGridList()
                        ]
                    }, {
                        title:   t("import_configuration"),
                        region:  'center',     // center region is required, no width/height specified
                        margins: '0 0 0 0',
                        layout:  'fit',
                        minSize: 150,
                        items:   [
                            tabPanel.getTabPanel()
                        ]
                    }

                ]
            });

            var pimcoreTabs = Ext.getCmp('pimcore_panel_tabs');
            pimcoreTabs.add(panel);
          //  pimcoreTabs.activate('csv_product_import_tab');

            panel.on('destroy', function () {
                pimcore.globalmanager.remove('CsvImport.admin');
            }.bind(this));

            pimcore.layout.refresh();
            return panel;
        },
        activate:     function () {
            var pimcoreTabs = Ext.getCmp('pimcore_panel_tabs');
            pimcoreTabs.activate('csv_product_import_tab');
        }
    }
);
var mainPanel = new pimcore.plugin.CsvImport.admin.mainPanel();