/**
 * Created by bas on 12/3/15.
 */
pimcore.registerNS('pimcore.plugin.CsvImport.admin.object');
pimcore.plugin.CsvImport.admin.object = Class.create({

    /**
     *
     * @returns {Ext.form.ComboBox}
     */
    getObjectsCombo: function (id) {

        var profileStore = gridlist.profileGrid.getStore();
        var rec = profileStore.getById(id);
        return new Ext.form.ComboBox({
            fieldLabel: t('object'),
            dataIndex: 'object',
            name: 'object',
            store: object.getClassStore(),
            displayField: 'text',
            typeAhead: true,
            forceSelection: true,
            triggerAction: 'all',
            emptyText: t('select_an_object'),
            selectOnFocus: true,
            value: rec.get('object'),
            allowBlank: false,
            listeners: {
                select: function( combo, record, index ) {
                    console.log(configGrid);
                     configGrid.addGrid(record.id);
                }
            }
        });
    },
    /**
     *
     * @returns {Ext.data.JsonStore}
     */
    getClassStore: function () {
        /*
        return new Ext.data.JsonStore({
            url: '/admin/class/get-tree',
            restful: false,
            autoDestroy: true,
            rootVisible: false,
            fields: [
                'id',
                'text'
            ]
        });*/
        return Ext.create('Ext.data.Store', {
            proxy: {
                type: 'ajax',
                url : '/admin/class/get-tree',
                reader: {
                    type: 'json',
                    root: 'object'
                }
            },
            autoLoad: true
        });
    }

});

var object = new pimcore.plugin.CsvImport.admin.object();