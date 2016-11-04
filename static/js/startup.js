pimcore.registerNS('pimcore.plugin.CsvImport');
pimcore.plugin.CsvImport = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return 'pimcore.plugin.CsvImport';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);

        var plugin = this;
        this.checkRight(
            'plugin_pimcorebulkpump_user',
            function() {
                plugin.navEl = Ext.get('pimcore_menu_product_importer');
                if (!plugin.navEl) {
                    plugin.navEl = Ext.get('pimcore_menu_search').insertSibling('<li id="pimcore_menu_product_importer" class="pimcore_menu_item pimcore_icon-book">' + t('product_importer') + '</li>');
                }
            }
        );
    },


    activateThePanel: function () {
        mainPanel.getPanel();
        pimcore.globalmanager.add("CsvImport.admin", mainPanel);

        var panel = pimcore.globalmanager.get('CsvImport.admin');


    },

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
                    error();
                }
            }.bind(this),
            error : function () { alert('error checking permissions') }
        });
    },

    productImporter: function () {
       this.checkRight(
            'plugin_pimcorebulkpump_user',
            function() {
                this.activateThePanel();
            }.bind(this),
            function() {
                //TODO: in the future, people without rights shouldnt even see the button
            }
        );
    },
    pimcoreReady:    function (params, broker) {

        var toolbar = pimcore.globalmanager.get('layout_toolbar');

        this.navEl.on('mousedown', this.productImporter.bind(this));
    }


});


var CsvImportPlugin = new pimcore.plugin.CsvImport();