pimcore.registerNS('pimcore.plugin.CsvImport.admin.paramsPanel');
pimcore.plugin.CsvImport.admin.paramsPanel = Class.create({

    formPanel: null,
    parentPanel: null,

    createFilterParamPanel: function (parent, config_id, filter_id) {
        this.parentPanel = parent;

        Ext.Ajax.request({
            url: '/plugin/BulkPump/Params/get?config_id=' + config_id + '&filter_id=' + filter_id,
            success: function(res) {
                
                this.formPanel = new Ext.FormPanel({
                    border: false,
                    autoDestroy: true,
                    bodyStyle: 'margin-top: 5px;',
                    items: [],
                    buttons: [
                        new Ext.Button({
                            text:  'save',
                            listeners: {
                                click: function() {
                                    this.saveForm();
                                }.bind(this)
                            }
                        }),
                        new Ext.Button({
                            text:  'cancel',
                            listeners: {
                                click: function() {
                                    this.parentPanel.removeAll();
                                }.bind(this)
                            }
                        })
                    ]
                });
        
                var data = Ext.decode(res.responseText);
                console.log(data);

                // data.fields.parameters
                var t = this.generateDynamicForm(config_id, filter_id, data.fields.parameters);
                t.setTitle(data.fields.name);
                this.formPanel.add(t);

                this.parentPanel.removeAll();
                this.parentPanel.add(this.formPanel);
                this.parentPanel.doLayout();

            }.bind(this),
            failure: function() {
                alert('error occured while retrieving data');
            }.bind(this)
        });

    },
    
    saveForm: function () {
        this.formPanel.getForm().submit({
            url : '/plugin/BulkPump/Params/save',
            success: function () {
                this.parentPanel.removeAll();
                pimcore.helpers.showNotification(t("success"), "Filter parameters saved", "success");
            }.bind(this),
            failure: function (form, action) {
                if(action.failureType == 'client')
                {
                    Ext.Msg.alert('Status', 'Error: Some mandatory fields are missing');
                }
                else
                {
                    Ext.Msg.alert('Status', 'Error while trying to save');
                }
            }.bind(this)
        });
    },

    generateDynamicForm: function (config_id, filter_id, propsdata) {
        /*
         fields: Object
             class: "BulkPump\ImportFilter\Native\stringToLower"
             description: "Converts a string to lower case"
             id: "ext-gen1631"
             name: "String to lowercase"
             parameters: Array (1)
                 0Object
                 default: false
                 id: "only_first"
                 name: "Only the first character"
                 type: "boolean"
                 Object Prototype
        */

        var fieldset = new Ext.form.FieldSet({
            border: false,
            title: ' '
        });

        fieldset.add(new Ext.form.TextField({
            value     : config_id,
            name      : 'config_id',
            fieldLabel: 'Config ID',
            hidden    : true
        }));
        
        fieldset.add(new Ext.form.TextField({
            value     : filter_id,
            name      : 'filter_id',
            fieldLabel: 'Filter ID',
            hidden    : true
        }));

        var index;
        for (index = 0; index < propsdata.length; ++index) {
            var propdata = propsdata[index];
            var newfield;
            switch(propdata.type) {
                case 'integer':
                    newfield = this.makeIntegerField(propdata);
                    break;
                case 'string':
                    newfield = this.makeStringField(propdata);
                    break;
                case 'boolean':
                    newfield = this.makeBooleanField(propdata);
                    break;
                case 'select':
                    newfield = this.makeSelectField(propdata);
                    break;
                default:
                    alert('unknown field type: ' + propdata.type);
            }
            fieldset.add(newfield);
        }

        return fieldset;
    },

    makeIntegerField: function (pd) {
        return new Ext.form.TextField({
            name            : pd.id,
            fieldLabel      : pd.name,
            //allowBlank      : !pd.mandatory,
            value           : pd.value
        });
    },

    makeStringField: function (pd) {
        return new Ext.form.TextField({
            name            : pd.id,
            fieldLabel      : pd.name,
            //allowBlank      : !pd.mandatory,
            value           : pd.value
        });
    },

    makeBooleanField: function (pd) {
        return new Ext.form.RadioGroup({
            fieldLabel: pd.name,
            columns: [44, 44],
            vertical: true,
            items: [{
                boxLabel    : 'Yes',
                name        : pd.id,
                inputValue  : 1,
                checked     : pd.value,
                style: {
                    marginLeft: '1px',
                    background: 'red'
                }
            },{
                boxLabel    : 'No',
                name        : pd.id,
                inputValue  : 0,
                checked     : !pd.value,
                style: {
                    marginLeft: '1px',
                    background: 'red'
                }
            }]
        });

        // ! dont use checkbox ! - values will not be sent when not checked
        // ---> replaced by radio buttons
        //
        //return new Ext.form.Checkbox({
        //    name            : 'pee_' + pd.id,
        //    fieldLabel      : pd.name,
        //    checked         : pd.value
        //});
    },

    makeSelectField: function (pd) {
        var selected_value = false;
        var data = [];
        var i;
        for (i = 0; i < pd.options.length; ++i) {
            var o = pd.options[i];
            data.push({
                "key": o,
                "value": o
            });
        }

        var mystore = new Ext.data.JsonStore({
            fields: [
                {type: 'string', name: 'key'},
                {type: 'string', name: 'value'}
            ]
        });
        mystore.loadData(data);

        return new Ext.form.ComboBox({
            fieldLabel      : pd.name,
            value           : pd.value,
            name            : pd.id + '_temp',
            hiddenName      : pd.id,
            displayField    : 'value',
            valueField      : 'key',
            mode            : 'local',
            triggerAction   : 'all',
            listClass       : 'comboalign',
            typeAhead       : true,
            forceSelection  : true,
            selectOnFocus   : true,
            store           : mystore
        });
    }

});

var paramsPanel = new pimcore.plugin.CsvImport.admin.paramsPanel();