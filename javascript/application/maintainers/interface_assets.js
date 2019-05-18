App.Maintainers.addToModuleMenu('asset', 
{
    xtype: 'button',
    text: App.Language.Asset.assets,
    iconCls: 'equip_icon_32',
    scale: 'large',
    module: 'Assets',
    iconAlign: 'top'
});

App.Maintainers.Assets.Principal = Ext.extend(Ext.TabPanel, 
{
    activeTab: 0,
    border: false,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'grid',
            title: App.Language.Asset.asset_type,
            id: 'App.Maintainers.AssetTypeGrid',
            store: App.Asset.Type.Store,
            height: 900,
            viewConfig: 
            {
                forceFit: true
            },
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex)
                {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.AssetTypeOpenEditMode(record);
                },
                'beforerender': function()
                {
                    App.Asset.Type.Store.load();
                }
            },
            columns: 
            [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                header: App.Language.General.name,
                dataIndex: 'asset_type_name',
                sortable: true,
                width: 100
            }],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: 
            {
                xtype: 'toolbar',
                items: 
                [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function()
                    {
                        w = new App.Maintainers.addAssetTypeWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b){
                        grid = Ext.getCmp('App.Maintainers.AssetTypeGrid');
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Asset.Type.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            }
        }, {
            xtype: 'grid',
            title: App.Language.Asset.state_asset,
            id: 'App.Maintainers.AssetStatusGrid',
            store: App.Asset.Status.Store,
            height: 900,
            viewConfig: 
            {
                forceFit: true
            },
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex)
                {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.AssetStatusOpenEditMode(record);
                },
                'beforerender': function()
                {
                    App.Asset.Status.Store.load();
                }
            },
            columns: 
            [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                dataIndex: 'asset_status_name',
                header: App.Language.General.name,
                sortable: true,
                width: 100
            }, {
                xtype: 'gridcolumn',
                dataIndex: 'asset_status_description',
                header: App.Language.General.description,
                sortable: true,
                width: 100
            }],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: 
            {
                xtype: 'toolbar',
                items:
                [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function()
                    {
                        w = new App.Maintainers.addAssetStatusWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b)
                    {
                        grid = Ext.getCmp('App.Maintainers.AssetStatusGrid');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Asset.Status.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            }
        }, {
            xtype: 'grid',
            title: App.Language.Asset.asset_condition,
            id: 'App.Maintainers.AssetConditionGrid',
            store: App.Asset.Condition.Store,
            height: 900,
            viewConfig: 
            {
                forceFit: true
            },
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex){
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.AssetConditionOpenEditMode(record);
                },
                'beforerender': function(){
                    App.Asset.Condition.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                dataIndex: 'asset_condition_name',
                header: App.Language.General.name,
                sortable: true,
                width: 100
            }, {
                xtype: 'gridcolumn',
                dataIndex: 'asset_condition_description',
                header: App.Language.General.description,
                sortable: true,
                width: 100
            }],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: 
            {
                xtype: 'toolbar',
                items: 
                [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function()
                    {
                        w = new App.Maintainers.addAssetConditionWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b)
                    {
                        grid = Ext.getCmp('App.Maintainers.AssetConditionGrid');
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Asset.Condition.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            }
        }, {
            xtype: 'grid',
            title: App.Language.Asset.dynamic_data_assets,
            store: App.Asset.DatosDinamicos.Store,
            stripeRows: true,
            height: 900,
            viewConfig: 
            {
                forceFit: true
            },
            loadMask: true,
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex)
                {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.AssetDynamiDataOpenEditMode(record);
                },
                'beforerender': function()
                {
                    App.Asset.DatosDinamicos.Store.load();
                }
            },
            tbar: 
            {
                xtype: 'toolbar',
                items: 
                [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function()
                    {
                        w = new App.Maintainers.addAssetDynamiDataWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b)
                    {
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Asset.DatosDinamicos.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: 
            [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                dataIndex: 'asset_other_data_attribute_name',
                header: App.Language.Infrastructure.tag_name,
                sortable: true,
                width: 100
            }],
            sm: new Ext.grid.CheckboxSelectionModel()
        }, {
            xtype: 'form',
            title: App.Language.Asset.dynamic_data_associate_assets,
            width: 900,
            bodyStyle: 'padding:10px;',
            items: 
            [{
                xtype: 'combo',
                width: 200,
                fieldLabel: App.Language.Asset.asset_type,
                hiddenName: 'asset_type_id',
                triggerAction: 'all',
                store: App.Asset.Type.Store,
                displayField: 'asset_type_name',
                valueField: 'asset_type_id',
                mode: 'remote',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus:true,
                minChars: 0,
                listeners: 
                {
                    'select': function(cb, record)
                    {
                        App.Asset.DatosDinamicosDisponibles.Store.setBaseParam('asset_type_id', record.data.asset_type_id);
                        App.Asset.DatosDinamicosDisponibles.Store.load();
                        App.Asset.DatosDinamicosAsociados.Store.setBaseParam('asset_other_data_attribute_asset_type_id', record.data.asset_type_id);
                        App.Asset.DatosDinamicosAsociados.Store.load();
                    }
                }
            }, {
                xtype: 'itemselector',
                name: 'itemselector',
                fieldLabel: App.Language.Infrastructure.attributes,
                imagePath: 'javascript/extjs/ux/images/',
                multiselects: 
                [{
                    width: 350,
                    height: 300,
                    store: App.Asset.DatosDinamicosDisponibles.Store,
                    displayField: 'asset_other_data_attribute_name',
                    valueField: 'asset_other_data_attribute_id'
                }, {
                    width: 350,
                    height: 300,
                    store: App.Asset.DatosDinamicosAsociados.Store,
                    displayField: 'asset_other_data_attribute_name',
                    valueField: 'asset_other_data_attribute_id'
                }]
            }, {
                xtype: 'button',
                text: App.Language.General.save,
                bodyStyle: 'padding:5px 5px 0',
                width: 80,
                handler: function(b)
                {
                    form = b.ownerCt.getForm();
                    form.submit
                    ({
                        waitTitle: App.Language.General.message_please_wait,
                        waitMsg: App.Language.General.message_guarding_information,
                        url: 'index.php/asset/assetotherdataattributeassettype/add',
                        success: function(fp, o)
                        {
                            Ext.FlashMessage.alert(o.result.msg);
                        },
                        failure: function(fp, o)
                        {
                            alert('Error:\n' + o.result.msg);
                        }
                    });
                }
            }]
        }];
        App.Maintainers.Assets.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.addAssetTypeWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.General.add_type,
    resizable: false,
    modal: true,
    width: 380,
    height: 140,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name_type,
                name: 'asset_type_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            url: 'index.php/asset/assettype/add',
                            success: function(fp, o)
                            {
                                App.Asset.Type.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o)
                            {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.addAssetTypeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AssetTypeOpenEditMode = function(record)
{
    w = new App.Maintainers.addAssetTypeWindow
    ({
        title: App.Language.Asset.edit_type_asset
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function()
    {
        form = w.form.getForm();
        if (form.isValid()) 
        {
            form.updateRecord(w.form.record);
            w.close();
            App.NodeTypeCategory.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addAssetStatusWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Asset.add_asset_state,
    resizable: false,
    modal: true,
    width: 380,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.name_state,
                name: 'asset_status_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'asset_status_description',
                anchor: '100%'
            }],
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) 
                    {
                        form.submit({
                            url: 'index.php/asset/assetstatus/add',
                            success: function(fp, o)
                            {
                                App.Asset.Status.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o)
                            {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.addAssetStatusWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AssetStatusOpenEditMode = function(record)
{
    w = new App.Maintainers.addAssetStatusWindow
    ({
        title: App.Language.Asset.edit_asset_state
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function()
    {
        form = w.form.getForm();
        if (form.isValid()) 
        {
            form.updateRecord(w.form.record);
            w.close();
            App.NodeTypeCategory.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addAssetConditionWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Asset.add_asset_condition,
    resizable: false,
    modal: true,
    width: 380,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.condition_name,
                name: 'asset_condition_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'asset_condition_description',
                anchor: '100%'
            }],
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            url: 'index.php/asset/assetcondition/add',
                            success: function(fp, o)
                            {
                                App.Asset.Condition.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o)
                            {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.addAssetConditionWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AssetConditionOpenEditMode = function(record)
{
    w = new App.Maintainers.addAssetConditionWindow
    ({
        title: App.Language.Asset.edit_condition_asset
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function()
    {
        form = w.form.getForm();
        if (form.isValid()) 
        {
            form.updateRecord(w.form.record);
            w.close();
            App.NodeTypeCategory.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addAssetDynamiDataWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Infrastructure.add_dynamic_data,
    resizable: false,
    frame: true,
    modal: true,
    width: 380,
    height: 140,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.Infrastructure.tag_name,
                name: 'asset_other_data_attribute_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            url: 'index.php/asset/assetotherdataattribute/add',
                            success: function(fp, o)
                            {
                                App.Asset.DatosDinamicos.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o)
                            {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.addAssetDynamiDataWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AssetDynamiDataOpenEditMode = function(record)
{
    w = new App.Maintainers.addAssetDynamiDataWindow
    ({
        title: App.Language.Infrastructure.edit_dynamic_data
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function()
    {
        form = w.form.getForm();
        if (form.isValid()) 
        {
            form.updateRecord(w.form.record);
            w.close();
            App.Asset.DatosDinamicos.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}
