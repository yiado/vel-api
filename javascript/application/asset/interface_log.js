App.Asset.Log.GridPanel = Ext.extend(Ext.grid.GridPanel, {
    title: App.Language.Asset.tracking,
    store: App.Asset.Log.Store,
    region: 'center',
    loadMask: true,
    listeners: {
        'beforerender': function() {
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
            text: App.Language.General.eexport,
            iconCls: 'export_icon',
            handler: function() {
                w = new App.Asset.Log.exportListWindow();
                w.show();
            }
        }]
    },
    initComponent: function() {
        this.columns = [{
            header: App.Language.General.action,
            sortable: true,
            dataIndex: 'asset_log_type_name'
        }, {
            xtype: 'datecolumn',
            sortable: true,
            header: App.Language.General.date_time,
            dataIndex: 'asset_log_datetime',
            format: App.General.DefaultDateTimeFormat
        }, {
            header: App.Language.General.details,
            sortable: true,
            dataIndex: 'asset_log_detail',
            renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                metadata.attr = 'ext:qtip="' + value + '"';
                return value;
            }
        }, {
            header: App.Language.General.user,
            sortable: true,
            dataIndex: 'User',
            renderer: function(User) {
                return User.user_name;
            }
        }];
        App.Asset.Document.GridPanel.superclass.initComponent.call(this);
    }
});

App.Asset.Log.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.eexport,
    width: 400,
    height: 150,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            labelWidth: 130,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                id: 'App.Asset.Log.file_name',
                anchor: '100%',
                name: 'file_name',
                maskRe: /^[a-zA-Z0-9_]/,
                regex: /^[a-zA-Z0-9_]/,
                allowBlank: false
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.eexport,
                handler: function(b) {

                    Ext.Ajax.request({
                        waitMsg: App.Language.General.message_generating_file,
                        url: 'index.php/asset/assetlog/export',
                        method: 'POST',
                        params: {
                            asset_id: App.Asset.selectedAssetId,
                            file_name: Ext.getCmp('App.Asset.Log.file_name').getValue()

                        },
                        success: function(response) {
                            response = Ext.decode(response.responseText);
                            document.location = response.file;
                            b.ownerCt.ownerCt.ownerCt.close();
                        },
                        failure: function(response) {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                        }
                    });
                }
            }]
        }];
        App.Asset.Log.exportListWindow.superclass.initComponent.call(this);
    }
});