pimcore.registerNS('pimcore.plugin.CsvImport');
pimcore.plugin.CsvImport = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return 'pimcore.plugin.CsvImport';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        //Create a variable to prevent scope problems
        var plugin = this;

        this.checkRight(
            'plugin_pimcorebulkpump_user',
            function() {

                //When the user has the right permission then add menu item in left menu
                plugin.navEl = Ext.get('pimcore_menu_product_importer');
                if (!plugin.navEl) {
                    plugin.navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_product_importer" class="pimcore_menu_item pimcore_icon-book">' + t('product_importer') + '</li>');
                }
            }
        );
    },


    /**
     * Opening the tab in main panel
     */
    activateThePanel: function () {
        mainPanel.getPanel();
        pimcore.globalmanager.add("CsvImport.admin", mainPanel);

        var panel = pimcore.globalmanager.get('CsvImport.admin');
    },

    /**
     * Recieve and check if user has the correct permissions
     *
     * @param permission
     * @param success
     * @param error
     */
    checkRight : function(permission, success, error) {
        Ext.Ajax.request({
            url : "/plugin/PimcoreBulkpump/user/permission",
            params : { permission : permission },
            success : function (result) {
                var res = Ext.decode(result.responseText);
                if(res.success) {
                    success();
                }
                else {
                    if(typeof error != 'undefined') {
                        error();
                    }
                }
            }.bind(this),
            error : function () { alert('error checking permissions') }
        });
    },

    /**
     * Method to open the importer panel
     */
    productImporter: function () {
        this.checkRight(
            'plugin_pimcorebulkpump_user',
            function() {
                //Activate the panel
                this.activateThePanel();
            }.bind(this),
            function() {
                //TODO: in the future, people without rights shouldnt even see the button
            }
        );
    },

    pimcoreReady: function (params, broker) {

        var toolbar = pimcore.globalmanager.get('layout_toolbar');

        //When you didnt had the right's there is no element to bind to
        if(typeof this.navEl != 'undefined') {

            //Bind the navigation to the button, after the click to open the tab
            this.navEl.on('mousedown', this.productImporter.bind(this));
        }
    }


});


var CsvImportPlugin = new pimcore.plugin.CsvImport();