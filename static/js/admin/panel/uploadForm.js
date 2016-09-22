pimcore.registerNS('pimcore.plugin.CsvImport.admin.uploadForm');
pimcore.plugin.CsvImport.admin.uploadForm = Class.create({
    self: this,
    uploadForm: {},
    getUploadForm: function () {
        /*
        return new Ext.FormPanel({
            id: "csv_import_upload_form_panel",
            fileUpload: true,
            width: 500,
            autoHeight: true,
            bodyStyle: 'padding: 10px 10px 10px 10px;',
            labelWidth: 100,
            defaults: {
                anchor: '80%',
                allowBlank: false,
                msgTarget: 'side'
            },
            items: [
                {
                    xtype: 'fileuploadfield',
                    id: 'filedata',
                    emptyText: 'Select a csv to upload...',
                    fieldLabel: 'File',
                    buttonText: 'Browse',
                    validator: function (v) {
                        if (!/\.csv$/.test(v)) {
                            return 'Only csv files allowed';
                        }
                        return true;
                    }
                }
            ],
            buttons: [
                {
                    text: 'Upload',
                    handler: function (self) {
                        // find the containers and the id
                        var form = self.findParentByType('form');
                        var mainWindow = self.findParentByType('window');
                        var id = mainWindow.profileId;

                        // get the form data
                        var values = form.getForm().getFieldValues();
                        console.log(values);
                        var file = values['filedata-inputEl'].split('\\').last();

                        var window = form.findParentByType('window');
                        if (form.getForm().isValid()) {
                            window.hide();
                            form.getForm().submit({
                                fileUpload: true,
                                clientValidation: true,
                                method: 'POST',
                                url: '/plugin/PimcoreBulkpump/file/post',
                                waitMsg: 'Uploading file...',
                                success: function (form, action) {
                                    // update the form field
                                    document.getElementById("load_path_field" + id).value = file;
                                    // reload the config list
                                    Ext.getCmp('csv_import_config_grid' + id).doLayout();

                                    pimcore.helpers.showNotification(t('file_uploaded_confirmation_title'), t('file_uploaded_confirmation'));
                                },
                                failure: function (form, action) {
                                    switch (action.failureType) {
                                        case Ext.form.Action.CLIENT_INVALID:
                                            Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                            break;
                                        case Ext.form.Action.CONNECT_FAILURE:
                                            Ext.Msg.alert('Failure', 'Ajax communication failed');
                                            break;
                                        case Ext.form.Action.SERVER_INVALID:
                                            Ext.Msg.alert('Failure', action.result.message);
                                    }
                                }
                            });
                        }
                    }
                }
            ]
        });*/
        return Ext.create('Ext.form.Panel', {
            bodyPadding: 10,
            frame: false,
            renderTo: Ext.getBody(),
            items: [{
                xtype: 'filefield',
                name: 'filedata',
                fieldLabel: 'File',
                labelWidth: 50,
                msgTarget: 'side',
                allowBlank: false,
                anchor: '100%',
                buttonText: 'Select file...'
            }],

            buttons: [{
                text: 'Upload',
                handler: function(self) {
                    var mainWindow = self.findParentByType('window');
                    var formParent = self.findParentByType('form');
                    var id = mainWindow.profileId;

                    var form = this.up('form').getForm();
                    var window = formParent.findParentByType('window');
                    if(form.isValid()){
                        var values = form.getFieldValues();
                        var file = values['filedata'].split('\\').last();

                        form.submit({
                            url: '/plugin/PimcoreBulkpump/file/post',
                            waitMsg: 'Uploading your file...',
                            success: function(fp, o) {
                                // update the form field
                                document.getElementById("load_path_field" + id +'-inputEl').value = file;
                                // reload the config list
                                //Ext.getCmp('csv_import_config_grid' + id).doLayout();
                                window.destroy();
                                pimcore.helpers.showNotification(t('file_uploaded_confirmation_title'), t('file_uploaded_confirmation'));
                            },
                            failure: function (form, action) {
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert('Failure', 'Ajax communication failed');
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert('Failure', action.result.message);
                                }
                            }
                        });
                    }
                }
            }]
        });
    },
    getFileWindow: function () {

        // ? bug
        var uploadForm = this.getUploadForm();
        /*var window = new Ext.Window({
            /!**
             * holder for the profile id
             *!/
            profileId: null,
            id: 'csv_import_file_upload_window',
            width: 500,
            closeAction: 'hide',
            plain: true,
            buttons: [
                new Ext.Button({
                    text: 'Close',
                    handler: function (self) {
                        var parent = self.findParentByType('window');
                        parent.hide();
                    }
                })
            ],
            items: [
                uploadForm
            ]
        });*/
        var window = Ext.create('Ext.window.Window', {
            profileId: null,
            id: 'csv_import_file_upload_window',
            title: 'Upload file',
            width: 500,
            closeAction: 'hide',
            plain: true,
            profileId: null,
            buttons: [
                new Ext.Button({
                    text: 'Close',
                    handler: function (self) {
                        var parent = self.findParentByType('window');
                        parent.destroy();
                    }
                })
            ],
            items: [
                uploadForm
            ]
        });
        return window;
    },
    initialize: function () {
     //   this.getFileWindow();
    }
});


    var uploadForm = new pimcore.plugin.CsvImport.admin.uploadForm();
