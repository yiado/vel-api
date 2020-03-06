App.Asset.Document.GridPanel = Ext.extend(Ext.grid.GridPanel, {
    title: App.Language.General.documents,
    store: App.Asset.Document.Store,
    id: 'App.Asset.Document.Grid',
    loadMask: true,
    listeners: {
        'rowdblclick': function (grid, rowIndex) {
            record = grid.getStore().getAt(rowIndex);
            App.Asset.Document.documentOpenEditMode(record);
        },
        'render': function () {
            this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
            this.store.load();
        }
    },
    viewConfig: {
        forceFit: true
    },
    tbar: {
        xtype: 'toolbar',
        items: [{
                text: App.Language.General.add,
                iconCls: 'add_icon',
                handler: function (b) {
                    w = new App.Asset.Document.formWindow({
                        title: App.Language.Asset.add_document_title
                    });
                    w.form.saveButton.setText(App.Language.General.add);
                    w.form.saveButton.handler = function (bb) {
                        form = w.form.getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: 'index.php/asset/assetdocument/add',
                                params: {
                                    asset_id: App.Asset.selectedAssetId
                                },
                                waitMsg: App.Language.General.message_up_document,
                                success: function (fp, o) {
                                    App.Asset.Document.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                                    App.Asset.Document.Store.load();
                                    w.form.ownerCt.close();
                                    Ext.getCmp('App.Asset.Document.Grid').fireEvent('render', Ext.getCmp('App.Asset.Document.Grid'));
                                },
                                failure: function (fp, o) {
                                    alert('Error:\n' + o.result.msg);
                                }
                            });
                        }
                    };
                    w.show();
                }
            }, {
                xtype: 'spacer',
                width: 5
            }, {
                text: App.Language.General.ddelete,
                iconCls: 'delete_icon',
                handler: function (b) {
                    grid = b.ownerCt.ownerCt;
                    if (grid.getSelectionModel().getCount()) {
                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete,
                                function (b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function (record) {
                                            Ext.Ajax.request({
                                                url: 'index.php/asset/assetdocument/delete',
                                                params: {
                                                    asset_document_id: record.data.asset_document_id
                                                },
                                                success: function (response) {
                                                    App.Asset.Document.Store.load();
                                                    Ext.getCmp('App.Asset.Document.Grid').fireEvent('render', Ext.getCmp('App.Asset.Document.Grid'));
                                                    Ext.getCmp('App.Asset.Grid').fireEvent('beforerender', Ext.getCmp('App.Asset.Grid'));
                                                }
                                            });


                                        });
                                    }
                                });
                    } else {
                        Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                    }
                }
            }]
    },
    initComponent: function () {
        this.selModel = new Ext.grid.CheckboxSelectionModel({
            checkOnly: false
        });
        this.columns = [
            this.selModel,
            {
                header: App.Language.General.description,
                sortable: true,
                dataIndex: 'asset_document_description',
                renderer: function (val, metadata, record) {
                    return "<a href='index.php/asset/assetdocument/download/" + record.data.asset_document_id + "'>" + val + "</a>";
                }
            }, {
                header: App.Language.General.comment,
                sortable: true,
                dataIndex: 'asset_document_comments'
            }, {
                header: App.Language.General.uploaded_by,
                sortable: true,
                dataIndex: 'user_name'
            }
        ];
        App.Asset.Document.GridPanel.superclass.initComponent.call(this);
    }
});

App.Asset.Document.formWindow = Ext.extend(Ext.Window, {
    width: 400,
    height: 250,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                fileUpload: true,
                padding: 5,
                plugins: [new Ext.ux.OOSubmit()],
                items: [{
                        xtype: 'fileuploadfield',
                        emptyText: App.Language.General.select_document,
                        fieldLabel: App.Language.General.document,
                        ref: 'document',
                        id: 'App.Asset.Document.file',
                        anchor: '100%',
                        fileUpload: true,
                        allowBlank: false,
                        name: 'documento',
                        buttonText: '',
                        buttonCfg: {
                            iconCls: 'upload_icon'
                        }
                    }, {
                        xtype: 'textfield',
                        ref: 'nameDocument',
                        fieldLabel: App.Language.General.description,
                        name: 'asset_document_description',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'textarea',
                        fieldLabel: App.Language.General.comment,
                        name: 'asset_document_comments',
                        anchor: '100%',
                        height: 100
                    }],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();

                        }
                    }, {
                        xtype: 'button',
                        ref: '../saveButton',
                        text: ''
                    }]
            }];
        App.Asset.Document.formWindow.superclass.initComponent.call(this);
    }
});

App.Asset.Document.documentOpenEditMode = function (record) {

    w = new App.Asset.Document.formWindow({
        title: App.Language.Asset.edit_document
    });
    Ext.getCmp('App.Asset.Document.file').disable();
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function () {
        form = w.form.getForm();
        if (form.isValid()) {
            form.submit({
                url: 'index.php/asset/assetdocument/update',
                params: {
                    asset_document_id: record.id,
                    node_id: App.Interface.selectedNodeId
                },
                waitMsg: App.Language.General.message_up_document,
                success: function (fp, o) {

                    App.Asset.Document.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                    App.Asset.Document.Store.load();
                    w.form.ownerCt.close();
                    Ext.getCmp('App.Asset.Document.Grid').fireEvent('render', Ext.getCmp('App.Asset.Document.Grid'));
                },
                failure: function (fp, o) {
                    alert('Error:\n' + o.result.msg);
                }
            });
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}