/**
 * Created by bas on 12/2/15.
 */
pimcore.registerNS('pimcore.plugin.CsvImport.admin.columns');
pimcore.plugin.CsvImport.admin.columns = Class.create(
    {
        columnsStore: {},
        getColumnsStore: function (id) {

            return new Ext.data.JsonStore({
                root: 'fields',
                fields: [
                    'id',
                    'profile_id',
                    'csv_field'
                ],
                writer: new Ext.data.JsonWriter({
                    encode: false
                }),
                proxy: new Ext.data.HttpProxy({
                    url: '/plugin/BulkPump/columns'
                }),
                baseParams: {
                    profileId: id
                },
                reader: new Ext.data.JsonReader({
                    successProperty: 'success',
                    messageProperty: 'message'
                })
            });
        },
        /**
         *
         * @returns {Ext.form.ComboBox}
         */
        getColumnsCombo: function (id) {

            return new Ext.form.ComboBox({
                fieldLabel: t('object'),
                dataIndex: 'csv_field',
                mode: 'remote',
                minChars: 0,
                queryDelay: 200,
                name: 'csv_field',
                store: this.getColumnsStore(id),
                displayField: 'csv_field',
                valueField: 'csv_field',
                typeAhead: false,
                forceSelection: true,
                triggerAction: 'all',
                emptyText: t('select_an_object'),
                selectOnFocus: true,
                listeners: {
                    select: function (combo, record, index) {
                    }
                }
            });
        }
    }
);
var columns = new pimcore.plugin.CsvImport.admin.columns();