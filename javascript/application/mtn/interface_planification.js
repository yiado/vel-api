App.Mtn.Wo.Planning = Ext.extend(Ext.Panel, {
    title: App.Language.Maintenance.planning,
    id: 'App.Asset.PlanningPrincipal',
    border: false,
    disabled: (App.Security.Actions[7004] === undefined ? true : false),
    layout: 'border',
    tbar: [{
        text: App.Language.Maintenance.associate_plan_assets,
        iconCls: 'settings_icon',
        handler: function(b) {
            grid = b.ownerCt.ownerCt.gridContractAsset;
            if (grid.getSelectionModel().getCount()) {
                records = Ext.getCmp('gridContractAsset').getSelectionModel().getSelections();
                aux = new Array();
                aux_asset_id = new Array();
                for (var i = 0; i < records.length; i++) {
                    aux.push(records[i].data.asset_id);
                }
                aux_asset_id = aux.join(',');
                w = new App.Mtn.Wo.AssociatePlanAssets();
                w.show();
            } else {
                Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_at_least_one_active_to_associate);
            }
        }
    }],
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                region: 'north',
                title: App.Language.General.searching,
                id: 'App.Plan.SearchForm',
                frame: true,
                ref: 'form',
                height: 160,
                margins: '5 5 5 5',
                padding: '5 150 5 150',
                border: true,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        provider_combo = b.ownerCt.ownerCt.provider.getValue();
                        if (provider_combo == '') {
                            Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.enter_the_supplier_should_be_obliged);
                        } else if (App.Interface.selectedNodeId == 'root') {
                            Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.you_must_select_a_node_to_find);
                        } else {
                            App.Mtn.WoPreventive.Store.baseParams = form.getSubmitValues();
                            App.Mtn.WoPreventive.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                            App.Mtn.WoPreventive.Store.load();
                        }
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        form.reset();
                        node_id = App.Mtn.WoPreventive.Store.baseParams.node_id;
                        App.Mtn.WoPreventive.Store.baseParams = {};
                        App.Mtn.WoPreventive.Store.load();
                    }
                }],
                items: [{
                    xtype: 'combo',
                    fieldLabel: App.Language.General.provider,
                    anchor: '100%',
                    ref: 'provider',
                    store: App.Core.Provider.Store,
                    allowBlank: false,
                    hiddenName: 'provider_id',
                    triggerAction: 'all',
                    displayField: 'provider_name',
                    valueField: 'provider_id',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
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
                    selectOnFocus: true,
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
                    selectOnFocus: true,
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
                }]
            }, {
                xtype: 'grid',
                id: 'gridContractAsset',
                ref: 'gridContractAsset',
                margins: '5 5 5 5',
                region: 'center',
                height: 600,
                border: true,
                loadMask: true,
                listeners: {
                    'beforerender': function() {
                        App.Mtn.WoPreventive.Store.load();
                    }
                },
                viewConfig: {
                    forceFit: true
                },
                store: App.Mtn.WoPreventive.Store,
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
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }],
            App.Mtn.Wo.Planning.superclass.initComponent.call(this);
    }
});


App.Mtn.Wo.AssociatePlanAssets = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.setting_up_plan,
    width: 420,
    height: 300,
    layout: 'fit',
    padding: 1,
    modal: true,
    resizable: false,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            labelWidth: 150,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.Maintenance.plan,
                anchor: '100%',
                store: App.Mtn.Plan.Store,
                hiddenName: 'mtn_plan_id',
                triggerAction: 'all',
                displayField: 'mtn_plan_name',
                valueField: 'mtn_plan_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
                mode: 'remote',
                minChars: 0,
                allowBlank: false,
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
                fieldLabel: App.Language.General.start_date,
                anchor: '100%',
                allowBlank: false,
                name: 'mtn_work_order_date'
            }, {
                xtype: 'datefield',
                fieldLabel: App.Language.General.end_date,
                anchor: '100%',
                allowBlank: false,
                name: 'mtn_date_finish'
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.commentary,
                name: 'mtn_work_order_comment',
                width: '100%',
                height: 130
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.add,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/wo/addPreventive',
                            params: {
                                asset_id: aux_asset_id
                            },
                            success: function(fp, o) {
                                App.Mtn.WoPreventive.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
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
        App.Mtn.Wo.AssociatePlanAssets.superclass.initComponent.call(this);
    }
});