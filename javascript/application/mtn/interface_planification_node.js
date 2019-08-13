App.Mtn.Wo.PlanningNode = Ext.extend(Ext.Panel, {
    title: App.Language.Maintenance.planning,
    id: 'App.Asset.PlanningPrincipalNode',
    border: false,
    //    disabled: (App.Security.Actions[7004] === undefined ? true: false),
    layout: 'border',
    tbar: [{
        text: App.Language.Maintenance.associate_venues_plan,
        iconCls: 'settings_icon',
        handler: function(b) {
            grid = b.ownerCt.ownerCt.gridContractNode;
            if (grid.getSelectionModel().getCount()) {
                records = Ext.getCmp('gridContractNode').getSelectionModel().getSelections();
                aux = new Array();
                aux_node_id = new Array();
                for (var i = 0; i < records.length; i++) {
                    aux.push(records[i].data.node_id);
                }
                aux_node_id = aux.join(',');
                w = new App.Mtn.Wo.AssociatePlanNodes();
                w.show();
            } else {
                Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_at_least_one_node_to_associate);
            }
        }
    }],
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                region: 'north',
                title: App.Language.General.searching,
                id: 'App.Plan.SearchFormNode',
                frame: true,
                ref: 'form',
                height: 160,
                margins: '5 5 5 5',
                padding: '5 150 5 150',
                border: true,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        if (App.Interface.selectedNodeId != 'root') {
                            tipo_recinto = Ext.getCmp('App.Plan.TipoRecinto').getValue();
                            if (tipo_recinto == '') {
                                Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.you_should_look_for_some_type_of_enclosure);
                            } else {

                                Ext.Ajax.request({
                                    waitMsg: App.Language.General.message_generating_file,
                                    url: 'index.php/core/nodecontroller/getById',
                                    timeout: 10000000000,
                                    params: {
                                        node_id: App.Interface.selectedNodeId
                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);


                                        if (response.success == "true") {
                                            formNode = b.ownerCt.ownerCt.getForm();
                                            App.Mtn.WoPreventiveByNode.Store.baseParams = formNode.getSubmitValues();
                                            App.Mtn.WoPreventiveByNode.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                                            App.Mtn.WoPreventiveByNode.Store.load();

                                        } else {
                                            Ext.FlashMessage.alert(App.Language.Maintenance.you_must_add_a_provider_under_contract);
                                        }
                                    },
                                    failure: function(response) {
                                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                    }
                                });
                            }

                        } else {
                            Ext.FlashMessage.alert(App.Language.General.you_must_select_a_node);
                        }
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        formNode = b.ownerCt.ownerCt.getForm();
                        formNode.reset();
                        node_id = App.Mtn.WoPreventive.Store.baseParams.node_id;
                        App.Mtn.WoPreventiveByNode.Store.baseParams = {};
                        App.Mtn.WoPreventiveByNode.Store.load();
                    }
                }],
                items: [{
                    xtype: 'combo',
                    fieldLabel: App.Language.General.enclosure_type,
                    id: "App.Plan.TipoRecinto",
                    ref: "tipo_recinto",
                    anchor: '100%',
                    store: App.NodeType.Store,
                    hiddenName: 'node_type_id',
                    triggerAction: 'all',
                    displayField: 'node_type_name',
                    valueField: 'node_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
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
                }]
            }, {
                xtype: 'grid',
                id: 'gridContractNode',
                ref: 'gridContractNode',
                margins: '5 5 5 5',
                region: 'center',
                height: 600,
                border: true,
                loadMask: true,
                listeners: {
                    'beforerender': function() {
                        App.Mtn.WoPreventiveByNode.Store.load();
                    }
                },
                viewConfig: {
                    forceFit: true
                },
                store: App.Mtn.WoPreventiveByNode.Store,
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_name',
                        header: App.Language.General.venue_name,
                        sortable: true,
                        width: 80
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_name',
                        header: App.Language.General.enclosure_type,
                        sortable: true,
                        width: 80
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_ruta',
                        header: App.Language.General.trade_route,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }],
            App.Mtn.Wo.PlanningNode.superclass.initComponent.call(this);
    }
});


App.Mtn.Wo.AssociatePlanNodes = Ext.extend(Ext.Window, {
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
                store: App.Mtn.PlanByNode.Store,
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
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.add,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        var msg = Ext.MessageBox.wait(App.Language.General.message_please_wait, App.Language.General.message_loading_information);
                        form.submit({
                            url: 'index.php/mtn/wo/addPreventiveByNode',
                            params: {
                                node_id: aux_node_id
                            },
                            success: function(fp, o) {
                                App.Mtn.WoNode.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT } });
                                App.Mtn.WoPreventiveByNode.Store.baseParams = {};
                                App.Mtn.WoPreventiveByNode.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                                msg.hide();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Mtn.Wo.AssociatePlanNodes.superclass.initComponent.call(this);
    }
});