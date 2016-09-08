pimcore.registerNS('pimcore.plugin.CsvImport');
pimcore.plugin.CsvImport = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return 'pimcore.plugin.CsvImport';
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },


    activateThePanel: function () {
        var panel = null;
        try {
            panel = pimcore.globalmanager.get('CsvImport.admin');
            panel.activate();
        }
        catch (e) {
            pimcore.globalmanager.add("CsvImport.admin", mainPanel);
            mainPanel.getPanel();
        }
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
            'plugin_bulkpump_user',
            function() {
                this.activateThePanel();
            }.bind(this),
            function() {
                //TODO: in the future, people without rights shouldnt even see the button
            }
        );
    },
    pimcoreReady:    function (params, broker) {
        var menuItems = [];
        menuItems.push({
            text:    t('csv_product_importer'),
            iconCls: 'pimcore_icon_routes',
            handler: this.productImporter.bind(this)
        });

        this.navEl = Ext.get('pimcore_menu_product_importer');
        if (!this.navEl) {
            this.navEl = Ext.get('pimcore_menu_logout').insertSibling('<li id="pimcore_menu_product_importer" class="pimcore_menu_item icon-book">' + t('product_importer') + '</li>');
        }

        var toolbar = pimcore.globalmanager.get('layout_toolbar');
        var menu = new Ext.menu.Menu({
            cls:   'pimcore_navigation_flyout',
            items: [menuItems]
        });
        this.navEl.on('mousedown', toolbar.showSubMenu.bind(menu));

        /** Start the initialisation of the upload window */
        uploadForm.getFileWindow().hide();
    }


});


var CsvImportPlugin = new pimcore.plugin.CsvImport();