pimcore.registerNS('pimcore.plugin.CsvImport');
pimcore.plugin.CsvImport = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return 'pimcore.plugin.CsvImport';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },


    /**
     * Opening the tab in main panel
     */
    activateThePanel: function () {
        mainPanel.getPanel();
        pimcore.globalmanager.add("CsvImport.admin", mainPanel);

        var panel = pimcore.globalmanager.get('CsvImport.admin');
    },

    pimcoreReady: function (params, broker) {


        var user = pimcore.globalmanager.get("user");
        if(user.isAllowed("plugin_pimcorebulkpump_user")) {

            var toolbar = pimcore.globalmanager.get('layout_toolbar');

            //When the user has the right permission then add menu item in left menu
            this.navEl = Ext.get('pimcore_menu_product_importer');
            if (!this.navEl) {
                this.navEl = Ext.get('pimcore_menu_search')
                    .insertSibling('<li id="pimcore_menu_product_importer" class="pimcore_menu_item pimcore_icon-book">' + t('product_importer') + '</li>');

                this.navEl.on('mousedown', this.activateThePanel.bind(this));
            }
        }
    }


});


var CsvImportPlugin = new pimcore.plugin.CsvImport();