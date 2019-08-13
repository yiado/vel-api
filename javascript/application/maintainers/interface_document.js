App.Maintainers.addToModuleMenu('doc', {
    xtype: 'button',
    text: App.Language.General.documents,
    iconCls: 'document_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Document'
});

App.Maintainers.Document.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            title: App.Language.General.category,
            store: App.Document.Categoria.Store,
            height: '100%',
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Document.OpenEditMode(record);
                },
                'beforerender': function() {
                    App.Document.Categoria.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    xtype: 'button',
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.Document.addCategoryDocumentWindow();
                        w.show();
                    }
                }, {
                    xtype: 'tbseparator'
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Document.Categoria.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'doc_category_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'doc_category_description',
                    header: App.Language.General.description,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel()
        }, {
            xtype: 'grid',
            title: App.Language.Document.extensions,
            store: App.Document.Extension.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Document.ExtensionOpenEditMode(record);
                },
                'beforerender': function() {
                    App.Document.Extension.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    xtype: 'button',
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.Document.addDocumentExtensionWindow();
                        w.show();
                    }
                }, {
                    xtype: 'tbseparator'
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Document.Extension.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'doc_extension_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'doc_extension_extension',
                    header: App.Language.Document.extensions,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel()
        }];
        App.Maintainers.Document.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.Document.addCategoryDocumentWindow = Ext.extend(Ext.Window, {
    title: App.Language.Document.add_category_document,
    resizable: false,
    modal: true,
    width: 450,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'doc_category_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                anchor: '100%',
                name: 'doc_category_description'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/doc/doccategory/add',
                            success: function(fp, o) {
                                App.Document.Categoria.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.Document.addCategoryDocumentWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Document.OpenEditMode = function(record) {
    w = new App.Maintainers.Document.addCategoryDocumentWindow({
        title: App.Language.Document.edit_category_documents
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.Document.addDocumentExtensionWindow = Ext.extend(Ext.Window, {
    title: App.Language.Document.add_extensions_document,
    resizable: false,
    modal: true,
    width: 450,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'doc_extension_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.Document.examples_extensions,
                anchor: '100%',
                name: 'doc_extension_extension'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/doc/docextension/add',
                            success: function(fp, o) {
                                App.Document.Extension.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.Document.addDocumentExtensionWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Document.ExtensionOpenEditMode = function(record) {
    w = new App.Maintainers.Document.addDocumentExtensionWindow({
        title: App.Language.Document.edit_document_extensions
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}