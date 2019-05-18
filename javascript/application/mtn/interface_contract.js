App.Mtn.Wo.Contract = Ext.extend(Ext.Panel, 
{
    title: App.Language.Maintenance.contracts,
    disabled: (App.Security.Actions[7006] === undefined ? true: false),
    border: false,
    layout: 'border',
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'grid',
            border: false,
            id: 'App.Contract.Asset',
            store: App.Core.Contract.Store,
            loadMask: true,
            margins: '5 5 5 5',
            region: 'center',
            height: 600,
            viewConfig: 
            {
                forceFit: true
            },
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex)
                {
                    record = grid.getStore().getAt(rowIndex);
                    App.Mtn.Wo.OpenEditModeContract(record);
                },
                'beforerender': function()
                {
                    App.Core.Contract.Store.load();
                }
            },
            columns: 
            [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                dataIndex: 'provider_name',
                header: App.Language.General.name,
                sortable: true,
                width: 100
            }, {
                xtype: 'datecolumn',
                dataIndex: 'contract_date_start',
                header: App.Language.General.start_date,
                format: App.General.DefaultSystemDate,
                sortable: true,
                width: 100
            }, {
                xtype: 'datecolumn',
                dataIndex: 'contract_date_finish',
                header: App.Language.General.end_date,
                format: App.General.DefaultSystemDate,
                sortable: true,
                width: 100
            }, {
                xtype: 'gridcolumn',
                dataIndex: 'contract_description',
                header: App.Language.General.description,
                sortable: true,
                width: 100
            }],
            sm: new Ext.grid.CheckboxSelectionModel
            ({
                singleSelect: true
            }),
            tbar: 
            {
                xtype: 'toolbar',
                items: 
                [{
                    xtype: 'button',
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function()
                    {
                        w = new App.Mtn.Wo.AddContractWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 5
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b)
                    {
                        grid = Ext.getCmp('App.Contract.Asset');
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Core.Contract.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }, {
                    xtype: 'tbseparator',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.settings,
                    iconCls: 'settings_icon',
                    handler: function(b)
                    {
                        grid = Ext.getCmp('App.Contract.Asset');
                        if (grid.getSelectionModel().getCount()) 
                        {
                            contract_id = grid.getSelectionModel().getSelected().json.contract_id;
                            provider_id = grid.getSelectionModel().getSelected().json.provider_id;
                            App.Core.ContractAsset.Store.setBaseParam('contract_id', contract_id);
                            App.Core.ContractAsset.Store.setBaseParam('provider_id', provider_id);
                            w = new App.Mtn.Wo.ContractAssetConfigWindow();
                            w.show();
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                        }
                    }
                }]
            }
        }];
        App.Mtn.Wo.Contract.superclass.initComponent.call(this);
    }
});

App.Mtn.Wo.AddContractWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.add_contracts,
    resizable: false,
    modal: true,
    width: 400,
    height: 200,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            plugins: [new Ext.ux.OOSubmit()],
            labelWidth: 150,
            padding: 5,
            items: 
            [{
                xtype: 'combo',
                fieldLabel: App.Language.General.provider,
                anchor: '100%',
                allowBlank: false,
                selecOnFocus: true,
                typeAhead: true,
                triggerAction: 'all',
                store: App.Core.Provider.Store,
                hiddenName: 'provider_id',
                displayField: 'provider_name',
                valueField: 'provider_id',
                mode: 'remote',
                minChars: 0,
                listeners: 
                {
                    'afterrender': function(cb)
                    {
                        cb.__value = cb.value;
                        cb.setValue('');
                        cb.getStore().load
                        ({
                            callback: function()
                            {
                                cb.setValue(cb.__value);
                            }
                        });
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'contract_date_start',
                fieldLabel: App.Language.General.start_date,
                format: App.General.DefaultSystemDate,
                name: 'contract_date_start',
                anchor: '100%',
                listeners: 
                {
                    'select': function(fd, date)
                    {
                        fd.ownerCt.contract_date_finish.setMinValue(date);
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'contract_date_finish',
                fieldLabel: App.Language.General.end_date,
                format: App.General.DefaultSystemDate,
                name: 'contract_date_finish',
                anchor: '100%',
                listeners: 
                {
                    'select': function(fd, date)
                    {
                        fd.ownerCt.contract_date_start.setMaxValue(date);
                    }
                }
            }, {
                xtype: 'textfield',
                allowBlank: false,
                fieldLabel: App.Language.General.description,
                anchor: '100%',
                name: 'contract_description'
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
                            url: 'index.php/core/contract/add',
                            success: function(fp, o)
                            {
                                App.Core.Contract.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
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
        App.Mtn.Wo.AddContractWindow.superclass.initComponent.call(this);
    }
});


App.Mtn.Wo.OpenEditModeContract = function(record)
{
    w = new App.Mtn.Wo.AddContractWindow
    ({
        title: App.Language.Maintenance.contract_edit
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
            Ext.getCmp('App.Contract.Asset').fireEvent('beforerender', Ext.getCmp('App.Contract.Asset'));

        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Mtn.Wo.ContractAssetConfigWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.assets_associated_with_the_contract,
    resizable: false,
    modal: true,
    border: true,
    width: 800,
    height: 465,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            border: true,
            items: 
            [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.ContarctAsset',
                store: App.Core.ContractAsset.Store,
                height: 350,
                viewConfig: 
                {
                    forceFit: true
                },
                listeners: 
                {
                    'beforerender': function()
                    {
                        App.Core.ContractAsset.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(), 
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'asset_name',
                    header: App.Language.Asset.name_asset,
                    sortable: true
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'asset_type_name',
                    header: App.Language.Asset.asset_type,
                    sortable: true
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'brand_name',
                    header: App.Language.General.brand,
                    sortable: true
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
                            w = new App.Mtn.Wo.AddContractAssetWindow();
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
                            grid = Ext.getCmp('App.Maintainers.ContarctAsset');
                            if (grid.getSelectionModel().getCount()) 
                            {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b)
                                {
                                    if (b == 'yes') 
                                    {
                                        grid.getSelectionModel().each(function(record){
                                            App.Core.ContractAsset.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                            }
                        }
                    }]
                }
            }],
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Mtn.Wo.ContractAssetConfigWindow.superclass.initComponent.call(this);
    }
});


App.Mtn.Wo.AddContractAssetWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.add_to_the_contract_assets,
    resizable: false,
    modal: true,
    border: true,
    width: 800,
    height: 465,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'panel',
            padding: 1,
            border: false,
            viewConfig: 
            {
                forceFit: true
            },
            items: 
            [{
                xtype: 'form',
                plugins: [new Ext.ux.OOSubmit()],
                frame: true,
                id: 'AddAssetContract',
                padding: '25 25 25 25',
                border: false,
                items: 
                [{
                    xtype: 'combo',
                    fieldLabel: App.Language.General.brand,
                    anchor: '100%',
                    store: App.Brand.Store,
                    hiddenName: 'brand_id',
                    triggerAction: 'all',
                    displayField: 'brand_name',
                    valueField: 'brand_id',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus:true,
                    mode: 'remote',
                    minChars: 0,
                    listeners: 
                    {
                        'afterrender': function(cb)
                        {
                            cb.__value = cb.value;
                            cb.setValue('');
                            cb.getStore().load
                            ({
                                callback: function()
                                {
                                    cb.setValue(cb.__value);
                                }
                            });
                        }
                    }
                }, {
                    xtype: 'combo',
                    fieldLabel: App.Language.Asset.asset_type,
                    anchor: '100%',
                    store: App.Asset.Type.Store,
                    hiddenName: 'asset_type_id',
                    triggerAction: 'all',
                    displayField: 'asset_type_name',
                    valueField: 'asset_type_id',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus:true,
                    mode: 'remote',
                    minChars: 0,
                    listeners: 
                    {
                        'afterrender': function(cb)
                        {
                            cb.__value = cb.value;
                            cb.setValue('');
                            cb.getStore().load
                            ({
                                callback: function()
                                {
                                    cb.setValue(cb.__value);
                                }
                            });
                        }
                    }
                }],
                buttons: 
                [{
                    xtype: 'button',
                    text: App.Language.General.search,
                    handler: function(b)
                    {
                        if (App.Interface.selectedNodeId == 'root') 
                        { 
                            Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.you_must_select_a_node_to_find);
                        } else
{
                            form = b.ownerCt.ownerCt.getForm();
                            App.Core.ContractAssetAll.Store.baseParams = form.getSubmitValues();
                            App.Core.ContractAssetAll.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                            App.Core.ContractAssetAll.Store.load();
							
                        }
                    }
                }, {
                    xtype: 'button',
                    text: App.Language.General.clean,
                    handler: function(b)
                    {
                        form = b.ownerCt.ownerCt.getForm();
                        form.reset();
                        node_id = App.Core.ContractAssetAll.Store.baseParams.node_id;
                        App.Core.ContractAssetAll.Store.baseParams = {};
                        App.Core.ContractAssetAll.Store.load();
                    }
                }]
            }, {
                xtype: 'panel',
                border: false,
                padding: 1,
                items: 
                [{
                    xtype: 'grid',
                    height: 240,
                    id: 'gridContractAsset',
                    width: '100%',
                    store: App.Core.ContractAssetAll.Store,
                    listeners: 
                    {
                        'beforerender': function()
                        {
                            App.Core.ContractAssetAll.Store.load();
                        }
                    },
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(), 
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'asset_name',
                        header: App.Language.Asset.name_asset,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'asset_type_name',
                        header: App.Language.Asset.asset_type,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'brand_name',
                        header: App.Language.General.brand,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'asset_path',
                        header: App.Language.Core.location,
                        sortable: true,
                        width: 100
                    }],
                    sm: new Ext.grid.CheckboxSelectionModel()
                }]
            }],
            buttons: 
            [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.add,
                iconCls: 'save_icon',
                handler: function(b)
                {
                    grid = Ext.getCmp('gridContractAsset');
                    if (grid.getSelectionModel().getCount()) 
                    {
                        grid.getSelectionModel().each(function(record)
                        {
                            asset_id = record.data.asset_id;
                            Ext.Ajax.request
                            ({
                                url: 'index.php/core/contractasset/add',
                                method: 'POST',
                                params: 
                                {
                                    asset_id: asset_id,
                                    contract_id: contract_id
                                },
                                success: function(fp, o)
                                {
                                    App.Core.ContractAssetAll.Store.load();
                                    App.Core.ContractAsset.Store.load();
                                    b.ownerCt.ownerCt.ownerCt.hide();
                                },
                                failure: function(fp, o)
                                {
                                    alert('Error:\n' + o.result.msg);
                                }
                            });
                        });
                    } else {
                        Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_at_least_one_activity_to_add);
                    }
                }
            }]
        }];
        App.Mtn.Wo.AddContractAssetWindow.superclass.initComponent.call(this);
    }
});
