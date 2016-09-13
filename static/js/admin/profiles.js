pimcore.registerNS('pimcore.plugin.CsvImport.admin.profiles');
Ext.define('profiles', {
    extend: 'Ext.data.Model',
    fields: [
        'id',
        'profile_name',
        'load_path',
        'save_to_path',
        'object',
        'key_field',
        'store_as_variant',
        'creationDate',
        'modificationDate',
        'import_type',
        'attributes_start_after',
        'classification_store_field',
        'classification_field',
        'attribute_language_separator',
        'custom_class'
    ]
});
pimcore.plugin.CsvImport.admin.profiles = Class.create({
        profiles: this,
        getProfileStore6: function(){
            return Ext.create('Ext.data.Store',{
                autoLoad: true,
                autoSync: true,
                model: 'profiles',
                proxy: {
                    type: 'rest',
                    url: '/PimcoreBulkpump/profile',
                    reader: {
                        type: 'json',
                        rootProperty: 'profiles'
                    },
                    writer: {
                        type: 'json'
                    }
                },
                listeners: {
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
        getProfileStore: function () {
            return new Ext.data.JsonStore({
                root: 'profiles',
                restful: true,
                fields: [
                    'id',
                    'profile_name',
                    'load_path',
                    'save_to_path',
                    'object',
                    'key_field',
                    'store_as_variant',
                    'creationDate',
                    'modificationDate',
                    'import_type',
                    'attributes_start_after',
                    'classification_store_field',
                    'classification_field',
                    'attribute_language_separator',
                    'custom_class'
                ],
                writer: new Ext.data.JsonWriter({
                    encode: false,
                    type: 'json',
                    writeAllFields: true,
                    root: 'profiles'

                }),
                proxy: new Ext.data.HttpProxy({
                    url: '/PimcoreBulkpump/profile'
                }),
                reader: new Ext.data.JsonReader({
                    successProperty: 'success',
                    idProperty: 'id',
                    messageProperty: 'message',
                    totalProperty: 'totalCount'
                })
            });
        },
        saveRecord: function (values, id, updateCallback) {
            var store = Ext.getCmp('importer_grid_profiles').getStore();
            var rec = store.getById(id);

            try {
                rec.beginEdit();
                rec.markDirty();
                for (var key in values) {
                    rec.set(key, values[key]);
                }
                rec.endEdit();
                rec.commit();
            } catch (e) {
                console.log(e);
            }

            store.on('update', updateCallback, this, {single: true}
            );

        }
    }
);

var profiles = new pimcore.plugin.CsvImport.admin.profiles();