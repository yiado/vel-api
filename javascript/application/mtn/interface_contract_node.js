App.Mtn.Wo.ContractNode = Ext.extend(Ext.Panel, {
    title: App.Language.Maintenance.contracts,
    disabled: (App.Security.Actions[7009] === undefined ? true : false),
    border: false,
    layout: 'border',
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            border: false,
            id: 'App.Contract.Node',
            store: App.Core.ContractNode.Store,
            loadMask: true,
            margins: '5 5 5 5',
            region: 'center',
            height: 600,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Mtn.Wo.OpenEditModeContract(record);
                },
                'beforerender': function() {
                    App.Core.ContractNode.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'provider_name',
                    header: "Nombre de Proveedor",
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'datecolumn',
                    dataIndex: 'contract_date_start',
                    header: App.Language.General.start_date,
                    format: App.General.DefaultDateFormat,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'datecolumn',
                    dataIndex: 'contract_date_finish',
                    header: App.Language.General.end_date,
                    format: App.General.DefaultDateFormat,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'contract_description',
                    header: App.Language.General.description,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({
                singleSelect: true
            }),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    xtype: 'button',
                    hidden: (App.Security.Actions[7008] === undefined ? true : false),
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Mtn.Wo.AddContractNodeWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 5
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    hidden: (App.Security.Actions[7010] === undefined ? true : false),
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Contract.Node');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                if (b == 'yes') {
                                    Ext.Ajax.request({
                                        url: 'index.php/core/contract/delete',
                                        params: {
                                            contract_id: grid.getSelectionModel().getSelected().id
                                        },
                                        success: function(response) {
                                            App.Core.ContractNode.Store.load();
                                        },
                                        failure: function(response) {
                                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                        }
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
                    hidden: (App.Security.Actions[7012] === undefined ? true : false),
                    iconCls: 'settings_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Contract.Node');
                        if (grid.getSelectionModel().getCount()) {
                            contract_id = grid.getSelectionModel().getSelected().json.contract_id;
                            provider_id = grid.getSelectionModel().getSelected().json.provider_id;
                            App.Core.ContractNodeAsociated.Store.setBaseParam('contract_id', contract_id);
                            App.Core.ContractNodeAsociated.Store.setBaseParam('provider_id', provider_id);
                            w = new App.Mtn.Wo.ContractNodeConfigWindow();
                            w.show();
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                        }
                    }
                }]
            }
        }];
        App.Mtn.Wo.ContractNode.superclass.initComponent.call(this);
    }
});

App.Mtn.Wo.AddContractNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_contracts,
    resizable: false,
    modal: true,
    width: 400,
    height: 200,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            plugins: [new Ext.ux.OOSubmit()],
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.provider,
                anchor: '100%',
                allowBlank: false,
                selecOnFocus: true,
                typeAhead: true,
                triggerAction: 'all',
                store: App.Core.ProviderByNode.Store,
                hiddenName: 'provider_id',
                displayField: 'provider_name',
                valueField: 'provider_id',
                mode: 'remote',
                minChars: 0,
                listeners: {
                    'afterrender': function(cb) {
                        cb.__value = cb.value;
                        cb.setValue('');
                        cb.getStore().load({
                            callback: function() {
                                cb.setValue(cb.__value);
                            }
                        });
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'contract_date_start',
                fieldLabel: App.Language.General.start_date,
                format: App.General.DefaultDateFormat,
                name: 'contract_date_start',
                anchor: '100%',
                listeners: {
                    'select': function(fd, date) {
                        fd.ownerCt.contract_date_finish.setMinValue(date);
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'contract_date_finish',
                fieldLabel: App.Language.General.end_date,
                format: App.General.DefaultDateFormat,
                name: 'contract_date_finish',
                anchor: '100%',
                listeners: {
                    'select': function(fd, date) {
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
                            url: 'index.php/core/contract/addNode',
                            success: function(fp, o) {
                                App.Core.Contract.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Core.ContractNode.Store.load();
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
        App.Mtn.Wo.AddContractNodeWindow.superclass.initComponent.call(this);
    }
});


App.Mtn.Wo.OpenEditModeContract = function(record) {
    w = new App.Mtn.Wo.AddContractNodeWindow({
        title: App.Language.Maintenance.contract_edit
    });
    w.form.saveButton.hidden = (App.Security.Actions[7011] === undefined ? true : false);
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;

    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            Ext.getCmp('App.Contract.Node').fireEvent('beforerender', Ext.getCmp('App.Contract.Node'));

        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Mtn.Wo.ContractNodeConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.the_contract_associates_venues,
    resizable: false,
    modal: true,
    border: true,
    width: 950,
    height: 465,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.ContarctAsset',
                store: App.Core.ContractNodeAsociated.Store,
                height: 380,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'beforerender': function() {
                        App.Core.ContractNodeAsociated.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_name',
                        header: App.Language.General.venue_name,
                        sortable: true,
                        width: 150
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_name',
                        header: App.Language.General.enclosure_type,
                        sortable: true,
                        width: 80
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'contract_node_path',
                        header: App.Language.General.trade_route,
                        sortable: true,
                        width: 300
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            if (App.Interface.selectedNodeId != 'root') {
                                Ext.Ajax.request({
                                    waitMsg: App.Language.General.message_generating_file,
                                    url: 'index.php/core/nodecontroller/getByIdNode',
                                    timeout: 10000000000,
                                    params: {
                                        node_id: App.Interface.selectedNodeId
                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);
                                        nodo = response.results.node_name;
                                        ruta = response.results.node_ruta;
                                        node_type_name = response.results.node_type_name;
                                        Ext.getCmp('App.Mtn.Wo.DisplayNodeNombre').setValue(nodo);
                                        Ext.getCmp('App.Mtn.Wo.DisplayNodeRuta').setValue(ruta);
                                        Ext.getCmp('App.Mtn.Wo.DisplayNodeTipoNombre').setValue(node_type_name);
                                    },
                                    failure: function(response) {
                                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                    }
                                });
                                w = new App.Mtn.Wo.AddContractNodeAsociateWindow();
                                w.show();

                            } else {
                                Ext.FlashMessage.alert(App.Language.General.you_must_select_a_node);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        width: 10
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ContarctAsset');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {

                                            Ext.Ajax.request({
                                                url: 'index.php/core/contractnode/delete',
                                                params: {
                                                    contract_node_id: record.data.contract_node_id
                                                },
                                                success: function(response) {
                                                    response = Ext.decode(response.responseText);
                                                    Ext.FlashMessage.alert(response.msg);
                                                    App.Core.ContractNodeAsociated.Store.load();
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
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Mtn.Wo.ContractNodeConfigWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.Wo.AddContractNodeAsociateWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_to_campus_contract,
    resizable: false,
    modal: true,
    width: 650,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            plugins: [new Ext.ux.OOSubmit()],
            labelWidth: 130,
            padding: 5,
            items: [{
                xtype: 'displayfield',
                fieldLabel: App.Language.General.venue_name,
                name: 'node_name',
                id: 'App.Mtn.Wo.DisplayNodeNombre',
                anchor: '100%'
            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.General.enclosure_type,
                name: 'node_type_name',
                id: 'App.Mtn.Wo.DisplayNodeTipoNombre',
                anchor: '100%'
            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.General.trade_route,
                id: 'App.Mtn.Wo.DisplayNodeRuta',
                name: 'node_ruta',
                anchor: '100%'
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.add,
                iconCls: 'save_icon',
                handler: function(b) {
                    Ext.Ajax.request({
                        url: 'index.php/core/contractnode/add',
                        method: 'POST',
                        params: {
                            node_id: App.Interface.selectedNodeId,
                            contract_id: contract_id
                        },
                        success: function(response) {
                            App.Core.ContractNodeAsociated.Store.load();
                            response = Ext.decode(response.responseText);
                            Ext.FlashMessage.alert(response.msg);
                            b.ownerCt.ownerCt.ownerCt.close();
                        },
                        failure: function(response) {
                            alert('Error:\n' + response.msg);
                        }
                    });

                }
            }]
        }];
        App.Mtn.Wo.AddContractNodeAsociateWindow.superclass.initComponent.call(this);
    }
});