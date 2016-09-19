/**
 * Created by bas on 12/2/15.
 */
pimcore.registerNS('pimcore.plugin.CsvImport.admin.columns');
pimcore.plugin.CsvImport.admin.columns = Class.create(
    {
        columnsStore: {},
        getColumnsStore: function (id) {

            /*
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
                    url: '/plugin/PimcoreBulkpump/columns'
                }),
                baseParams: {
                    profileId: id
                },
                reader: new Ext.data.JsonReader({
                    successProperty: 'success',
                    messageProperty: 'message'
                })
            });*/
            return Ext.create('Ext.data.Store',{
                autoLoad: true,
                autoSync: true,
                fields: [
                    'id',
                    'profile_id',
                    'csv_field'
                ],
                proxy: {
                    type: 'rest',
                    url: '/plugin/PimcoreBulkpump/columns',
                    reader: {
                        type: 'json',
                        rootProperty: 'fields'
                    },
                    writer: {
                        type: 'json'
                    }
                },
                listeners: {
                    beforeload: function(store){
                        store.getProxy().setExtraParam("profileId", id);
                    },
                    write: function(store, operation){
                        var record = operation.getRecords()[0],
                            name = Ext.String.capitalize(operation.action),
                            verb;

                        if (name == 'Destroy'){
                            verb = 'Destroyed';
                        }else{
                            verb = name + 'd';
                        }
                        Ext.example.msg(name, Ext.String.format("{0} user: {1}", verb,record.getId()));
                    }
                }
            });
        },
        /**
         *
         * @returns {Ext.form.ComboBox}
         */
        getColumnsCombo: function (id) {
            /*return new Ext.form.ComboBox({
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
            });*/

            return Ext.create('Ext.form.ComboBox', {
                ieldLabel: t('object'),
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
                listWidth: 'auto',
                matchFieldWidth: false,
                maxWidth: 410,
                listeners: {
                    select: function (combo, record, index) {
                    }
                }
            });


        }
    }
);
var columns = new pimcore.plugin.CsvImport.admin.columns();