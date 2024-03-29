App.Mtn.Wo.CurrentWoData = null;

App.Mtn.Wo.InterfaceNode = Ext.extend(Ext.Panel, {
    title: App.Language.Maintenance.maintenance,
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: [{
            text: App.Language.Maintenance.generate_work_order,
            hidden: (App.Security.Actions[7001] === undefined ? true : false),
            iconCls: 'add_icon',
            handler: function () {
                if (App.Interface.selectedNodeId != 'root') {
                    Ext.Ajax.request({
                        waitMsg: App.Language.General.message_generating_file,
                        url: 'index.php/core/nodecontroller/getById',
                        timeout: 10000000000,
                        params: {
                            node_id: App.Interface.selectedNodeId
                        },
                        success: function (response) {
                            response = Ext.decode(response.responseText);


                            if (response.success == "true") {
                                nodo = response.results.node_name;
                                ruta = response.results.node_ruta;
                                node_tipo = response.results.node_type_name;

                                w = new App.Mtn.generateWorkOrderNodeWindow();
                                Ext.getCmp('App.Mtn.HiddenPanelNode').setVisible(false);
                                Ext.getCmp('App.Mtn.PanelTotaleslNode').setVisible(false);
                                Ext.getCmp('App.Mtn.Wo.NodeDisplay').setValue(nodo);
                                Ext.getCmp('App.Mtn.Wo.NodeDisplayType').setValue(node_tipo);
                                Ext.getCmp('App.Mtn.Wo.RootNode').setValue(ruta);
                                App.Mtn.Wo.EditModeFromGrid = false;

                                App.Mtn.WoNodeProvider.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                                App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 0);
                                w.show();
                                w.setHeight(320);
                                w.setWidth(800);
                                w.center();

                            } else {
                                //ESTO ES PARA EL BANCO SOLAMENTE   
                                Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.must_be_within_the_folder_locations_to_create_work_order);
                            }
                        },
                        failure: function (response) {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                        }
                    });

                } else {
                    Ext.FlashMessage.alert(App.Language.General.you_must_select_a_node);
                }

            }

        }, {
            xtype: 'tbseparator',
            width: 10
        }, {
            text: App.Language.Maintenance.change_dates,
            hidden: (App.Security.Actions[7002] === undefined ? true : false),
            iconCls: 'ico_fecha',
            handler: function (b) {
                grid = Ext.getCmp('App.Mtn.Wo.GridlNode');
                if (grid.getSelectionModel().getCount()) {
                    records = Ext.getCmp('App.Mtn.Wo.GridlNode').getSelectionModel().getSelections();
                    aux = new Array();
                    aux_mtn_work_order_id = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.mtn_work_order_id);
                    }
                    aux_mtn_work_order_id = aux.join(',');
                    w = new App.Mtn.Wo.DateNodeWO();
                    w.show();
                } else {
                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                }
            }
        }, {
            xtype: 'tbseparator',
            width: 10
        }, {
            text: App.Language.General.search,
            iconCls: 'search_icon_16',
            enableToggle: true,
            handler: function (b) {
                if (b.ownerCt.ownerCt.formSearchWoNode.isVisible()) {
                    b.ownerCt.ownerCt.formSearchWoNode.hide();
                } else {
                    b.ownerCt.ownerCt.formSearchWoNode.show();
                }
                b.ownerCt.ownerCt.doLayout();
            }
        }],
    listeners: {
        'beforerender': function () {
            node = Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId);

            App.Mtn.Principal.listener(node);
            App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 1);
        }
    },
    initComponent: function () {
        this.items = [{
                xtype: 'grid',
                id: 'App.Mtn.Wo.GridlNode',
                margins: '5 5 5 5',
                border: true,
                region: 'center',
                loadMask: true,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'afterrender': function () {
                        App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
                    },
                    'rowdblclick': function (grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Mtn.Wo.OpenEditModeNode(record.data.mtn_work_order_id);
                    }
                },
                bbar: new Ext.PagingToolbar({
                    pageSize: App.GridLimitNumOT,
                    store: App.Mtn.WoNode.Store,
                    displayInfo: true,
                    displayMsg: App.Language.General.showing,
                    emptyMsg: App.Language.General.no_results,
                    listeners: {
                        'beforerender': function (w) {
                            App.Mtn.WoNode.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                        }

                    }
                }),
                store: App.Mtn.WoNode.Store,
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        dataIndex: 'mtn_work_order_folio',
                        header: App.Language.Maintenance.folio,
                        sortable: true
                    }, {
                        xtype: 'datecolumn',
                        header: App.Language.General.creation_date,
                        format: App.General.DefaultDateFormat,
                        dataIndex: 'mtn_work_order_date',
                        sortable: true,
                        align: 'center'
                    }, {
                        dataIndex: 'mtn_system_work_order_status_name',
                        header: App.Language.General.state,
                        sortable: true
                    }, {
                        dataIndex: 'provider_name',
                        header: App.Language.General.provider,
                        sortable: true
                    }, {
                        dataIndex: 'mtn_work_order_type_name',
                        header: App.Language.Maintenance.type_ot,
                        sortable: true
                    }, {
                        dataIndex: 'mtn_work_order_requested_by',
                        header: App.Language.General.requested_by,
                        sortable: true
                    }, {
                        dataIndex: 'node_ruta',
                        header: App.Language.General.route,
                        sortable: true,
                        renderer: function (value, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + value + '"';
                            return value;
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }, {
                xtype: 'form',
                id: 'App.Mtn.SearchFormNode',
                plugins: [new Ext.ux.OOSubmit()],
                ref: 'formSearchWoNode',
                hidden: true,
                border: false,
                margins: '5 5 0 5',
                region: 'north',
                height: 200,
                frame: true,
                fbar: [{
                        text: App.Language.General.search,
                        handler: function (b) {
                            formNode = b.ownerCt.ownerCt.getForm();
                            App.Mtn.WoNode.Store.baseParams = formNode.getValues();
                            App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
                            return;
                        }
                    }, {
                        text: App.Language.General.clean,
                        handler: function (b) {
                            formNode = b.ownerCt.ownerCt.getForm();
                            temp = Ext.getCmp('App.Mtn.SearchFormNode').path_search_node.getValue();
                            formNode.reset();
                            Ext.getCmp('Start_Date_Node').update();
                            Ext.getCmp('App.Mtn.SearchFormNode').path_search_node.setValue(temp);
                            App.Mtn.WoNode.Store.baseParams = [];
                            App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
                        }
                    }],
                items: [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.searching,
                        id: 'lbl_search_in',
                        hiddenName: 'node_id',
                        ref: 'path_search_node'
                    }, {
                        layout: 'column',
                        items: [{
                                columnWidth: .5,
                                layout: 'form',
                                items: [{
                                        xtype: 'textfield',
                                        fieldLabel: App.Language.Maintenance.folio,
                                        name: 'mtn_work_order_folio',
                                        anchor: '60%'
                                    }, {
                                        xtype: 'combo',
                                        fieldLabel: App.Language.General.state,
                                        anchor: '95%',
                                        store: App.Mtn.PossibleStatusByNode.Store,
                                        hiddenName: 'mtn_system_work_order_status_id',
                                        displayField: 'mtn_system_work_order_status_name',
                                        valueField: 'mtn_system_work_order_status_id',
                                        selecOnFocus: true,
                                        typeAhead: true,
                                        selectOnFocus: true,
                                        editable: false,
                                        forceSelection: true,
                                        typeAhead: true,
                                        triggerAction: 'all',
                                        mode: 'remote',
                                        minChars: 0
                                    }, {
                                        xtype: 'combo',
                                        fieldLabel: App.Language.General.provider,
                                        anchor: '95%',
                                        selecOnFocus: true,
                                        typeAhead: true,
                                        forceSelection: true,
                                        triggerAction: 'all',
                                        editable: false,
                                        store: App.Core.ProviderByNode.Store,
                                        hiddenName: 'provider_id',
                                        displayField: 'provider_name',
                                        valueField: 'provider_id',
                                        mode: 'remote',
                                        minChars: 0
                                    }, {
                                        xtype: 'combo',
                                        fieldLabel: App.Language.Maintenance.type_ot,
                                        anchor: '95%',
                                        typeAhead: true,
                                        selectOnFocus: true,
                                        editable: false,
                                        forceSelection: true,
                                        store: App.Mtn.WoTypesAllByNode.Store,
                                        hiddenName: 'mtn_work_order_type_id',
                                        displayField: 'mtn_work_order_type_name',
                                        valueField: 'mtn_work_order_type_id',
                                        triggerAction: 'all',
                                        mode: 'remote',
                                        minChars: 0,
                                        editable: false
                                    }, {
                                        xtype: 'checkbox',
                                        hideLabel: true,
                                        boxLabel: App.Language.Maintenance.include_closed_orders,
                                        name: 'include_closed_wo',
                                        inputValue: 1
                                    }]
                            }, {
                                columnWidth: .5,
                                layout: 'form',
                                items: [{
                                        hiddenName: 'node_id'
                                    }, {
                                        columnWidth: .2,
                                        layout: 'form',
                                        anchor: '90%',
                                        items: [{
                                                xtype: 'label',
                                                text: App.Language.Maintenance.select_range_of_dates_of_creation_of_the_ot
                                            }]
                                    }, {
                                        columnWidth: .4,
                                        layout: 'column',
                                        anchor: '95%',
                                        frame: true,
                                        items: [{
                                                columnWidth: .5,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'datefield',
                                                        ref: '../start_date',
                                                        id: 'Start_Date_Node',
                                                        fieldLabel: App.Language.General.start_date,
                                                        name: 'start_date',
                                                        anchor: '95%',
                                                        listeners: {
                                                            'select': function (fd, date) {
                                                                fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                columnWidth: .5,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'datefield',
                                                        ref: '../end_date',
                                                        fieldLabel: App.Language.General.end_date,
                                                        name: 'end_date',
                                                        anchor: '95%',
                                                        listeners: {
                                                            'select': function (fd, date) {
                                                                fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                                            }
                                                        }
                                                    }]
                                            }]
                                    }, {
                                        xtype: 'spacer',
                                        height: 15
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: App.Language.General.requested_by,
                                        name: 'mtn_work_order_requested_by',
                                        anchor: '95%'
                                    }]
                            }]
                    }]
            }];
        App.Mtn.Wo.InterfaceNode.superclass.initComponent.call(this);
    }
});

App.Mtn.Wo.addContractProviderWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.contract_for_supplier_selection,
    resizable: false,
    modal: true,
    width: 380,
    height: 160,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                labelWidth: 150,
                padding: 5,
                items: [{
                        xtype: 'combo',
                        fieldLabel: App.Language.General.provider_type,
                        anchor: '95%',
                        store: App.Core.ProviderTypeByNode.Store,
                        hiddenName: 'provider_type_id',
                        displayField: 'provider_type_name',
                        id: 'App.Type.ProviderId',
                        valueField: 'provider_type_id',
                        selecOnFocus: true,
                        typeAhead: true,
                        selectOnFocus: true,
                        forceSelection: true,
                        typeAhead: true,
                        triggerAction: 'all',
                        mode: 'remote',
                        minChars: 0,
                        listeners: {
                            'select': function (cb, record) {
                                Ext.getCmp('App.ContractNodeID').setDisabled(false);
                                App.Core.ProviderByType.Store.setBaseParam('provider_type_id', record.data.provider_type_id);
                                App.Core.ProviderByType.Store.load();
                            }
                        }
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.General.provider,
                        anchor: '95%',
                        store: App.Core.ProviderByType.Store,
                        hiddenName: 'contract_id',
                        displayField: 'provider_name',
                        id: 'App.ContractNodeID',
                        valueField: 'contract_id',
                        disabled: true,
                        selecOnFocus: true,
                        typeAhead: true,
                        selectOnFocus: true,
                        forceSelection: true,
                        typeAhead: true,
                        triggerAction: 'all',
                        mode: 'remote',
                        minChars: 0
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        text: App.Language.General.add,
                        ref: '../saveButton',
                        handler: function (b) {
                            Ext.Ajax.request({
                                url: 'index.php/core/contractnode/add',
                                method: 'POST',
                                params: {
                                    node_id: App.Interface.selectedNodeId,
                                    contract_id: Ext.getCmp('App.ContractNodeID').getValue()
                                },
                                success: function (response) {
                                    response = Ext.decode(response.responseText);
                                    Ext.FlashMessage.alert(response.msg);
                                    b.ownerCt.ownerCt.ownerCt.close();
                                    Ext.Ajax.request({
                                        waitMsg: App.Language.General.message_generating_file,
                                        url: 'index.php/core/nodecontroller/getById',
                                        timeout: 10000000000,
                                        params: {
                                            node_id: App.Interface.selectedNodeId
                                        },
                                        success: function (response) {
                                            response = Ext.decode(response.responseText);
                                            nodo = response.results.node_name;
                                            ruta = response.results.node_ruta;
                                            node_tipo = response.results.node_type_name;

                                            w = new App.Mtn.generateWorkOrderNodeWindow();
                                            Ext.getCmp('App.Mtn.HiddenPanelNode').setVisible(false);
                                            Ext.getCmp('App.Mtn.PanelTotaleslNode').setVisible(false);
                                            Ext.getCmp('App.Mtn.Wo.NodeDisplay').setValue(nodo);
                                            Ext.getCmp('App.Mtn.Wo.NodeDisplayType').setValue(node_tipo);
                                            Ext.getCmp('App.Mtn.Wo.RootNode').setValue(ruta);
                                            App.Mtn.Wo.EditModeFromGrid = false;
                                            App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 0);
                                            App.Mtn.WoNodeProvider.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                                            w.show();
                                            w.setHeight(320);
                                            w.setWidth(800);
                                            w.center();

                                        },
                                        failure: function (response) {
                                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                        }
                                    });

                                },
                                failure: function (response) {
                                    alert('Error:\n' + response.msg);
                                }
                            });
                        }
                    }]
            }];
        App.Mtn.Wo.addContractProviderWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.Wo.ContractNodeAddProviderWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.the_contract_associates_venues,
    resizable: false,
    modal: true,
    border: true,
    width: 950,
    height: 465,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                border: true,
                items: [{
                        border: false,
                        xtype: 'grid',
                        id: 'App.Maintainers.ContarctAsset',
                        store: App.Core.ContractNodeAsociated.Store,
                        height: 350,
                        viewConfig: {
                            forceFit: true
                        },
                        listeners: {
                            'beforerender': function () {
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
                                    handler: function () {
                                        if (App.Interface.selectedNodeId != 'root') {
                                            Ext.Ajax.request({
                                                waitMsg: App.Language.General.message_generating_file,
                                                url: 'index.php/core/nodecontroller/getByIdNode',
                                                timeout: 10000000000,
                                                params: {
                                                    node_id: App.Interface.selectedNodeId
                                                },
                                                success: function (response) {
                                                    response = Ext.decode(response.responseText);
                                                    nodo = response.results.node_name;
                                                    ruta = response.results.node_ruta;
                                                    node_type_name = response.results.node_type_name;
                                                    Ext.getCmp('App.Mtn.Wo.DisplayNodeNombre').setValue(nodo);
                                                    Ext.getCmp('App.Mtn.Wo.DisplayNodeRuta').setValue(ruta);
                                                    Ext.getCmp('App.Mtn.Wo.DisplayNodeTipoNombre').setValue(node_type_name);


                                                },
                                                failure: function (response) {
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
                                    handler: function (b) {
                                        grid = Ext.getCmp('App.Maintainers.ContarctAsset');
                                        if (grid.getSelectionModel().getCount()) {
                                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function (b) {
                                                if (b == 'yes') {
                                                    grid.getSelectionModel().each(function (record) {
                                                        App.Core.ContractNodeAsociated.Store.remove(record);
                                                        App.Core.ContractNodeAsociated.Store.load();
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
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Mtn.Wo.ContractNodeAddProviderWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.generateWorkOrderNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.new_work_order,
    width: 800,
    height: 640,
    layout: 'fit',
    padding: 1,
    maximizable: true,
    modal: true,
    id: 'App.Mtn.Wo.WinWoNode',
    listeners: {
        'beforerender': function (w) {
            w.panel.form_wo_node.getForm().load({
                url: 'index.php/mtn/wo/getOneNode',
                params: {
                    mtn_work_order_id: w.mtn_work_order_id
                },
                success: function (fp, o) {
                    record = o.result;

                    App.Mtn.Wo.CurrentWoData = record.data;

                    App.Mtn.Wo.Id = record.data.mtn_work_order_id;
                    mtn_work_order_comment = record.data.mtn_work_order_comment;

                    total_task = record.data.total_task;
                    total_other_costs = record.data.total_other_costs;
                    total_work_order = record.data.total_work_order;
                    total_work_order_iva = record.data.total_work_order_iva;

                    w.setHeight(620);
                    w.setWidth(800);
                    w.panel.form_wo_node.tab_panel_node.panel_comment.comment.setValue(mtn_work_order_comment);
                    w.panel.form_wo_node.panel4.colum_11.form_11_node.total_task_dd_node.setValue('$ ' + total_task);
                    w.panel.form_wo_node.panel4.colum_11.form_11_node.total_other_costs_dd_node.setValue('$ ' + total_other_costs);
                    w.panel.form_wo_node.panel4.colum_11.form_11_node.total_work_order_dd_node.setValue('$ ' + total_work_order);
                    w.panel.form_wo_node.panel4.colum_11.form_11_node.total_work_order_dd_iva.setValue('$ ' + total_work_order_iva);
                    w.setTitle(App.Language.Maintenance.work_order_number + record.data.mtn_work_order_folio);

                    App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_config_state_id', record.data.MtnConfigState.mtn_config_state_id);

                    App.Mtn.ConfigStateAsociados.Store.load();

                    //PARA EL PROVEEDOR
                    Ext.getCmp('App.Mtn.Wo.ProviderColum1').setVisible(false);
                    Ext.getCmp('App.Mtn.Wo.ProviderLabel1').setVisible(false);
                    Ext.getCmp('App.Mtn.Wo.RootProvider').setVisible(false);
                    Ext.getCmp('App.Mtn.Wo.RootProvider').setDisabled(true);
                    Ext.getCmp('App.Mtn.Wo.Provider2Node').setVisible(true);

                    Ext.getCmp('App.Mtn.Wo.ProviderLabel2Node').setVisible(true);
                    Ext.getCmp('App.Mtn.Wo.TextProviderNode').setVisible(true);
                    Ext.getCmp('App.Mtn.Wo.TextProviderNode').setValue(record.data.Provider.provider_name);

                    //PARA EL TIPO DE OT
                    Ext.getCmp('App.Mtn.Wo.Type1Node').setVisible(false);
                    Ext.getCmp('App.Mtn.Wo.Label1').setVisible(false);
                    Ext.getCmp('App.Mtn.Wo.TypeId').setVisible(false);
                    Ext.getCmp('App.Mtn.Wo.TypeId').setDisabled(true);
                    Ext.getCmp('App.Mtn.Wo.Type2Node').setVisible(true);

                    Ext.getCmp('App.Mtn.Wo.Label2Node').setVisible(true);
                    Ext.getCmp('App.Mtn.Wo.TextOtNode').setVisible(true);
                    Ext.getCmp('App.Mtn.Wo.TextOtNode').setValue(record.data.MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name);


                    Ext.getCmp('App.Mtn.StateDisplayNode').setValue(record.data.MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name);
                    Ext.getCmp('App.Mtn.ColumStateNode').setVisible(true);

                    App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_work_order_type_id', record.data.MtnConfigState.mtn_work_order_type_id);
                    App.Mtn.ConfigStateAsociados.Store.load();

                    Ext.getCmp('App.Mtn.HiddenPanelNode').setVisible(true);
                    Ext.getCmp('App.Mtn.PanelTotaleslNode').setVisible(true);
                    Ext.getCmp('App.Mtn.Wo.TbarStateNode').setDisabled(false);
                    Ext.getCmp('App.Mtn.Wo.TbarPrintIcon').setDisabled(false);

                    //Cargamos las tareas de la OT   
                    App.Mtn.WoTask.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
                    App.Mtn.WoTask.Store.load();

                    //Cargamos los otros costos asociados a la OT
                    App.Mtn.OtherCostsWo.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
                    App.Mtn.OtherCostsWo.Store.load();

                    //Cargamos la tabla de los Log
                    App.Mtn.Log.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
                    App.Mtn.Log.Store.load()

                    //Seteamos un flag para identificar que la ot en ediciÃ³n saliÃ³ de la grid. (solo para actualizar la grid del buscador de OT)
                    App.Mtn.Wo.EditModeFromGrid = true;

                    // deshabilitando botones de cambio cuando estah cerrada
                    if (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == 1) {
                        w.panel.form_wo_node.tab_panel_node.taskgridnode.getTopToolbar().hide();
                        w.panel.form_wo_node.tab_panel_node.taskgridnode.doLayout();

                        w.panel.form_wo_node.tab_panel_node.othercostgrid.getTopToolbar().hide();
                        w.panel.form_wo_node.tab_panel_node.othercostgrid.doLayout();

                        Ext.getCmp('App.Mtn.Wo.FormWoNode.BtnSaveWo').hide();
                        Ext.getCmp('App.Mtn.Wo.TbarStateNode').hide();

                    }

                    // creator user
                    Ext.getCmp('App.Mtn.Wo.CreatorUserNode').setText(App.Mtn.Wo.CurrentWoData.User.user_name);
                    App.Mtn.WoNode.Store.load();
                }
            })
        },
        'close': function () {
            App.Mtn.Wo.resetWoNode();
        }
    },
    initComponent: function () {
        this.items = [{
                xtype: 'panel',
                padding: 0,
                layout: 'fit',
                border: false,
                ref: 'panel',
                tbar: {
                    xtype: 'toolbar',
                    height: 26,
                    items: [{
                            xtype: 'button',
                            text: App.Language.Maintenance.change_of_status,
                            hidden: (App.Security.Actions[7003] === undefined ? true : false),
                            id: 'App.Mtn.Wo.TbarStateNode',
                            iconCls: 'changeState_icon',
                            disabled: true,
                            handler: function () {
                                w = new App.Mtn.ChangeStateNodeWindow();
                                App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_work_order_type_id', App.Mtn.Wo.CurrentWoData.MtnConfigState.mtn_work_order_type_id);
                                App.Mtn.WoStateForm.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.CurrentWoData.mtn_work_order_id);
                                w.wo_type.setValue(App.Mtn.Wo.CurrentWoData.MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name);
                                w.current_state.setValue(App.Mtn.Wo.CurrentWoData.MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name);
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 10
                        }, {
                            xtype: 'button',
                            id: 'App.Mtn.Wo.TbarPrintIcon',
                            hidden: (App.Security.Actions[7004] === undefined ? true : false),
                            text: App.Language.General.printer,
                            iconCls: 'print_icon',
                            disabled: true,
                            handler: function () {
                                document.location = mtn_export_wordorder_node + App.Mtn.Wo.Id;
                            }
                        },
                        '->',
                        {
                            xtype: 'label',
                            text: App.Language.Maintenance.created_by
                        }, {
                            xtype: 'spacer',
                            width: 10
                        }, {
                            xtype: 'label',
                            id: 'App.Mtn.Wo.CreatorUserNode'
                        }
                    ]
                },
                items: [{
                        xtype: 'form',
                        id: 'App.Mtn.Wo.FormWoNode',
                        plugins: [new Ext.ux.OOSubmit()],
                        ref: 'form_wo_node',
                        height: '100%',
                        width: '100%',
                        border: false,
                        layout: 'border',
                        items: [{
                                /** formulario **/
                                xtype: 'panel',
                                region: 'north',
                                margins: '5 5 5 5',
                                frame: true,
                                labelWidth: 120,
                                ref: 'url_action_node',
                                border: false,
                                height: 200,
                                items: [{
                                        layout: 'column',
                                        border: true,
                                        ref: 'colum_buscador_node',
                                        items: [{
                                                ref: 'form_buscador_1_node',
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'displayfield',
                                                        fieldLabel: App.Language.General.route,
                                                        name: 'node_ruta',
                                                        id: 'App.Mtn.Wo.RootNode',
                                                        ref: 'node_path',
                                                        anchor: '100%'
                                                    }]
                                            }]
                                    }, {
                                        layout: 'column',
                                        ref: 'colum_general_node',
                                        items: [{
                                                columnWidth: .6,
                                                ref: 'form_general_node',
                                                layout: 'form',
                                                items: [{
                                                        layout: 'column',
                                                        ref: 'colum_node',
                                                        items: [{
                                                                columnWidth: 1,
                                                                ref: 'form_node',
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'displayfield',
                                                                        fieldLabel: App.Language.General.venue_name,
                                                                        id: 'App.Mtn.Wo.NodeDisplay',
                                                                        name: 'node_name',
                                                                        ref: 'displayfield_node',
                                                                        anchor: '95%'
                                                                    }]
                                                            }]
                                                    }, {
                                                        layout: 'column',
                                                        labelWidth: 80,
                                                        id: 'App.Mtn.Wo.ProviderColum1',
                                                        hidden: false,
                                                        items: [{
                                                                columnWidth: .2,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'label',
                                                                        text: App.Language.General.provider,
                                                                        id: 'App.Mtn.Wo.ProviderLabel1',
                                                                        hidden: false
                                                                    }]
                                                            }, {
                                                                columnWidth: .8,
                                                                layout: 'form',
                                                                labelWidth: 30,
                                                                items: [{
                                                                        xtype: 'combo',
                                                                        id: 'App.Mtn.Wo.RootProvider',
                                                                        store: App.Mtn.WoNodeProvider.Store,
                                                                        hiddenName: 'provider_id',
                                                                        triggerAction: 'all',
                                                                        displayField: 'provider_name',
                                                                        valueField: 'provider_id',
                                                                        selecOnFocus: true,
                                                                        anchor: '88%',
                                                                        typeAhead: true,
                                                                        editable: false,
                                                                        allowBlank: false,
                                                                        hideLabel: false,
                                                                        hidden: false,
                                                                        mode: 'remote',
                                                                        minChars: 0,
                                                                        listeners: {
                                                                            'afterrender': function (cb) {
                                                                                cb.__value = cb.value;
                                                                                cb.setValue('');
                                                                                cb.getStore().load({
                                                                                    callback: function () {
                                                                                        cb.setValue(cb.__value);
                                                                                    }
                                                                                });
                                                                            }
                                                                        }
                                                                    }]
                                                            }]
                                                    }, {
                                                        layout: 'column',
                                                        labelWidth: 80,

                                                        ref: 'colum_combo_provider_node',
                                                        id: 'App.Mtn.Wo.Provider2Node',
                                                        items: [{
                                                                columnWidth: .2,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'label',
                                                                        hidden: true,
                                                                        text: App.Language.General.provider,
                                                                        id: 'App.Mtn.Wo.ProviderLabel2Node'
                                                                    }]
                                                            }, {
                                                                columnWidth: .8,
                                                                labelWidth: 30,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'displayfield',
                                                                        anchor: '88%',
                                                                        name: 'provider_name',
                                                                        id: 'App.Mtn.Wo.TextProviderNode'
                                                                    }]
                                                            }]
                                                    }, {
                                                        layout: 'column',
                                                        labelWidth: 80,
                                                        id: 'App.Mtn.Wo.Type1Node',
                                                        hidden: false,
                                                        items: [{
                                                                columnWidth: .2,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'label',
                                                                        text: App.Language.Maintenance.type_ot,
                                                                        id: 'App.Mtn.Wo.Label1',
                                                                        hidden: false
                                                                    }]
                                                            }, {
                                                                columnWidth: .8,
                                                                layout: 'form',
                                                                labelWidth: 30,
                                                                items: [{
                                                                        xtype: 'combo',
                                                                        id: 'App.Mtn.Wo.TypeId',
                                                                        store: App.Mtn.WoTypesAllByNodeSolo.Store,
                                                                        hiddenName: 'mtn_work_order_type_id',
                                                                        triggerAction: 'all',
                                                                        displayField: 'mtn_work_order_type_name',
                                                                        valueField: 'mtn_work_order_type_id',
                                                                        selecOnFocus: true,
                                                                        anchor: '88%',
                                                                        typeAhead: true,
                                                                        editable: false,
                                                                        allowBlank: false,
                                                                        hideLabel: false,
                                                                        hidden: false,
                                                                        mode: 'remote',
                                                                        minChars: 0,
                                                                        listeners: {
                                                                            'afterrender': function (cb) {
                                                                                cb.__value = cb.value;
                                                                                cb.setValue('');
                                                                                cb.getStore().load({
                                                                                    callback: function () {
                                                                                        cb.setValue(cb.__value);
                                                                                    }
                                                                                });
                                                                            },
                                                                            'select': function (cb, record) {

                                                                                Ext.Ajax.request({
                                                                                    url: 'index.php/mtn/configstate/getAssociatedPrimero',
                                                                                    params: {
                                                                                        mtn_work_order_type_id: record.data.mtn_work_order_type_id
                                                                                    },
                                                                                    success: function (response) {
                                                                                        response = Ext.decode(response.responseText);

                                                                                        if (response.total == 1) {
                                                                                            state = response.results.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name;
                                                                                            mtn_config_state_id = response.results.mtn_config_state_id;
                                                                                            Ext.getCmp('App.Mtn.StateDisplayNode').setValue(state);

                                                                                        }

                                                                                    }
                                                                                });
                                                                            }
                                                                        }
                                                                    }]
                                                            }]
                                                    }, {
                                                        layout: 'column',
                                                        labelWidth: 80,
                                                        ref: 'colum_combo_type_ot_node',
                                                        id: 'App.Mtn.Wo.Type2Node',
                                                        items: [{
                                                                columnWidth: .2,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'label',
                                                                        hidden: true,
                                                                        text: App.Language.Maintenance.type_ot,
                                                                        id: 'App.Mtn.Wo.Label2Node'
                                                                    }]
                                                            }, {
                                                                columnWidth: .8,
                                                                labelWidth: 30,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'displayfield',
                                                                        anchor: '88%',
                                                                        name: 'mtn_work_order_type_name',
                                                                        id: 'App.Mtn.Wo.TextOtNode'
                                                                    }]
                                                            }]
                                                    }, {
                                                        xtype: 'checkbox',
                                                        fieldLabel: App.Language.Maintenance.ot_cancelled,
                                                        id: 'App.Mtn.Wo.EstatusNode',
                                                        anchor: '100%',
                                                        name: 'mtn_work_order_status',
                                                        inputValue: 1
                                                    }, {
                                                        xtype: 'checkbox',
                                                        fieldLabel: App.Language.Maintenance.closed_order,
                                                        id: 'App.Mtn.Wo.Closed',
                                                        anchor: '100%',
                                                        name: 'mtn_work_order_closed',
                                                        inputValue: 1
                                                    }]
                                            }, {
                                                columnWidth: .4,
                                                ref: 'form_data_node',
                                                layout: 'form',
                                                items: [{
                                                        layout: 'column',
                                                        ref: 'colum_node',
                                                        items: [{
                                                                columnWidth: 1,
                                                                ref: 'form_node',
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'displayfield',
                                                                        fieldLabel: App.Language.General.enclosure_type,
                                                                        id: 'App.Mtn.Wo.NodeDisplayType',
                                                                        name: 'node_type_name',
                                                                        ref: 'displayfield_node_type',
                                                                        anchor: '95%'
                                                                    }]
                                                            }]
                                                    }, {
                                                        xtype: 'datefield',
                                                        fieldLabel: App.Language.General.date,
                                                        id: 'App.Mtn.Wo.DateNode',
                                                        ref: 'data',
                                                        name: 'mtn_work_order_date',
                                                        anchor: '95%',
                                                        editable: false,
                                                        allowBlank: false,
                                                        value: new Date()
                                                    }, {
                                                        layout: 'column',
                                                        labelWidth: 80,
                                                        items: [{
                                                                columnWidth: .2,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'label',
                                                                        //                                        hidden: true,
                                                                        id: 'App.Mtn.ColumStateNode',
                                                                        text: App.Language.General.state
                                                                    }]
                                                            }, {
                                                                columnWidth: .8,
                                                                labelWidth: 60,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'displayfield',
                                                                        anchor: '100%',
                                                                        name: 'mtn_system_work_order_status_name',
                                                                        id: 'App.Mtn.StateDisplayNode'
                                                                    }]
                                                            }]
                                                    }, {
                                                        xtype: 'textfield',
                                                        id: 'App.Mtn.Wo.RequestedBy',
                                                        fieldLabel: App.Language.General.requested_by,
                                                        name: 'mtn_work_order_requested_by',
                                                        anchor: '95%'
                                                    }]
                                            }]
                                    }]
                            }, {
                                /** grillas **/
                                xtype: 'tabpanel',
                                region: 'center',
                                activeTab: 0,
                                ref: 'tab_panel_node',
                                border: true,
                                id: 'App.Mtn.HiddenPanelNode',
                                padding: 1,
                                margins: '0 5 5 5',
                                items: [{
                                        xtype: 'grid',
                                        title: App.Language.General.task,
                                        ref: 'taskgridnode',
                                        tbar: {
                                            xtype: 'toolbar',
                                            items: [{
                                                    xtype: 'button',
                                                    text: App.Language.General.add,
                                                    hidden: (App.Security.Actions[7005] === undefined ? true : false),
                                                    iconCls: 'add_icon',
                                                    handler: function () {
                                                        w = new App.Mtn.addTaskNodeWindow();
                                                        w.show();
                                                    }
                                                }, {
                                                    xtype: 'spacer',
                                                    width: 5
                                                }, {
                                                    xtype: 'button',
                                                    text: App.Language.General.ddelete,
                                                    hidden: (App.Security.Actions[7006] === undefined ? true : false),
                                                    iconCls: 'delete_icon',
                                                    handler: function (b) {
                                                        grid = Ext.getCmp('App.Mtn.WoTaskNodeGrid');
                                                        if (grid.getSelectionModel().getCount()) {
                                                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function (b) {
                                                                if (b == 'yes') {
                                                                    grid.getSelectionModel().each(function (record) {
                                                                        App.Mtn.WoTask.Store.remove(record);
                                                                        Ext.getCmp('App.Mtn.Wo.WinWoNode').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWoNode'));
                                                                    });
                                                                }
                                                            });
                                                        } else {
                                                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                                        }
                                                    }
                                                }]
                                        },
                                        id: 'App.Mtn.WoTaskNodeGrid',
                                        store: App.Mtn.WoTask.Store,
                                        padding: 2,
                                        border: true,
                                        viewConfig: {
                                            forceFit: true
                                        },
                                        listeners: {
                                            'rowdblclick': function (grid, rowIndex) {
                                                record = grid.getStore().getAt(rowIndex);
                                                App.Mtn.WoTask.OpenEditMode(record);
                                            }
                                        },
                                        columns: [new Ext.grid.CheckboxSelectionModel(),
                                            {
                                                dataIndex: 'MtnTask',
                                                header: App.Language.General.task_name,
                                                sortable: true,
                                                width: 200,
                                                renderer: function (MtnTask) {
                                                    return MtnTask.mtn_task_name;
                                                }
                                            }, {
                                                dataIndex: 'mtn_work_order_task_price',
                                                header: App.Language.General.price,
                                                sortable: true,
                                                width: 90,
                                                renderer: function (value) {
                                                    return Ext.util.Format.number(value, App.General.DefaultSystemCurrencyFormatMoney);
                                                }
                                            }, {
                                                dataIndex: 'mtn_amount_component_in_task',
                                                header: App.Language.Maintenance.input,
                                                align: 'center',
                                                width: 50,
                                                sortable: true
                                            }, {
                                                dataIndex: 'mtn_work_order_component_price',
                                                header: App.Language.Maintenance.unit_price,
                                                sortable: true,
                                                width: 80,
                                                renderer: function (value) {
                                                    return Ext.util.Format.number(value, App.General.DefaultSystemCurrencyFormatMoney);
                                                }
                                            }, {
                                                header: App.Language.Maintenance.total_price,
                                                dataIndex: 'mtn_costos_component_in_task',
                                                sortable: true,
                                                width: 90,
                                                renderer: function (value) {
                                                    return Ext.util.Format.number(value, App.General.DefaultSystemCurrencyFormatMoney);
                                                }
                                            }, {
                                                header: App.Language.General.time,
                                                dataIndex: 'mtn_work_order_task_time_job',
                                                align: 'center',
                                                width: 50,
                                                sortable: true
                                            }, {
                                                header: App.Language.General.comment,
                                                dataIndex: 'mtn_work_order_task_comment',
                                                width: 100,
                                                sortable: true
                                            }
                                        ],
                                        sm: new Ext.grid.CheckboxSelectionModel()
                                    }, {
                                        xtype: 'grid',
                                        title: App.Language.Maintenance.direct_costs,
                                        ref: 'othercostgrid',
                                        id: 'App.Mtn.WoOtherCostsNodeGrid',
                                        store: App.Mtn.OtherCostsWo.Store,
                                        border: true,
                                        padding: 2,
                                        viewConfig: {
                                            forceFit: true
                                        },
                                        listeners: {
                                            'rowdblclick': function (grid, rowIndex) {
                                                record = grid.getStore().getAt(rowIndex);
                                                App.Mtn.OtherCosts.OpenEditMode(record);
                                            }
                                        },
                                        columns: [new Ext.grid.CheckboxSelectionModel(),
                                            {
                                                dataIndex: 'mtn_other_costs_name',
                                                header: App.Language.Maintenance.name_direct_cost,
                                                sortable: true,
                                                width: 100
                                            }, {
                                                header: App.Language.General.value,
                                                dataIndex: 'mtn_work_order_other_costs_costs',
                                                sortable: true,
                                                width: 100,
                                                renderer: function (value) {
                                                    return Ext.util.Format.number(value, App.General.DefaultSystemCurrencyFormatMoney);
                                                }
                                            }, {
                                                header: App.Language.Maintenance.percentage,
                                                dataIndex: 'mtn_work_order_other_costs_comment',
                                                sortable: true,
                                                width: 100
                                                        //                            ,
                                                        //                            renderer: function(value, metadata, record)
                                                        //                            {
                                                        //                                return mtn_work_order_other_costs_comment + "%";
                                                        //                            }
                                            }
                                        ],
                                        stripeRows: true,
                                        sm: new Ext.grid.CheckboxSelectionModel()
                                    }, {
                                        xtype: 'grid',
                                        title: App.Language.Asset.tracking,
                                        store: App.Mtn.Log.Store,
                                        border: true,
                                        viewConfig: {
                                            forceFit: true
                                        },
                                        listeners: {
                                            'beforerender': function () {
                                                App.Mtn.Log.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
                                                App.Mtn.Log.Store.load();
                                            }
                                        },
                                        columns: [{
                                                header: App.Language.Maintenance.state,
                                                dataIndex: 'mtn_system_work_order_status_name',
                                                sortable: true,
                                                width: 35,
                                                renderer: function (mtn_system_work_order_status_name, p, record) {
                                                    return String.format('<b>{0}</b><br>{1}', mtn_system_work_order_status_name, record.data.mtn_status_log_datetime.dateFormat(App.General.DefaultDateTimeFormat));
                                                }
                                            }, {
                                                header: App.Language.General.user,
                                                dataIndex: 'user_name',
                                                sortable: true,
                                                width: 25
                                            }, {
                                                dataIndex: 'mtn_status_log_comments',
                                                header: App.Language.General.details,
                                                sortable: true,
                                                css: 'white-space:normal;',
                                                cls: 'x-grid33-cell-inner',
                                                renderer: function (value, p, record) {
                                                    return String.format('{0}', value);
                                                }
                                            }]
                                    }, {
                                        xtype: 'panel',
                                        title: App.Language.General.comment,
                                        ref: 'panel_comment',
                                        padding: 2,
                                        border: true,
                                        items: [{
                                                xtype: 'textarea',
                                                id: 'App.Mtn.Wo.OrderCommentNode',
                                                ref: 'comment',
                                                name: 'mtn_work_order_comment',
                                                width: '100%',
                                                height: '100%'
                                            }]
                                    }]
                            }, {
                                /** panel totales **/
                                xtype: 'panel',
                                region: 'south',
                                id: 'App.Mtn.PanelTotaleslNode',
                                ref: 'panel4',
                                padding: '5 0 0 5',
                                margins: '0 5 5 5',
                                height: 120,
                                border: false,
                                frame: true,
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        ref: 'colum_11',
                                        items: [{
                                                columnWidth: .5,
                                                layout: 'form',
                                                ref: 'form_11_node',
                                                labelWidth: 150,
                                                border: false,
                                                items: [{
                                                        xtype: 'displayfield',
                                                        fieldLabel: App.Language.Maintenance.value_service,
                                                        ref: 'total_task_dd_node',
                                                        id: 'App.Mtn.Wo.TotalNodeServicioNode',
                                                        anchor: '100%'
                                                    }, {
                                                        xtype: 'displayfield',
                                                        fieldLabel: App.Language.Maintenance.total_direct_costs,
                                                        ref: 'total_other_costs_dd_node',
                                                        id: 'App.Mtn.Wo.TotalNodeOtherCostsNode',
                                                        anchor: '100%'
                                                    }, {
                                                        xtype: 'displayfield',
                                                        fieldLabel: App.Language.Maintenance.total_ot,
                                                        ref: 'total_work_order_dd_node',
                                                        id: 'App.Mtn.Wo.TotalNode',
                                                        anchor: '100%'
                                                    }, {
                                                        xtype: 'displayfield',
                                                        fieldLabel: App.Language.Maintenance.total_work_order,
                                                        ref: 'total_work_order_dd_iva',
                                                        //                                id: 'App.Mtn.Wo.TotalNodeOtherCostsNode',
                                                        anchor: '100%'
                                                    }]
                                            }]
                                    }]
                            }]
                    }],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        xtype: 'button',
                        text: App.Language.General.save,
                        hidden: (App.Security.Actions[7007] === undefined ? true : false),
                        id: 'App.Mtn.Wo.FormWoNode.BtnSaveWo',
                        handler: function (b) {
                            form = Ext.getCmp('App.Mtn.Wo.FormWoNode').getForm();
                            if (form.isValid() && App.Interface.selectedNodeId != 'root') {
                                form.submit({
                                    clientValidation: true,
                                    url: (App.Mtn.Wo.Id != null ? 'index.php/mtn/wo/update' : 'index.php/mtn/wo/addCorrectiveNode'),
                                    params: {
                                        mtn_config_state_id: mtn_config_state_id,
                                        node_id: App.Interface.selectedNodeId
                                    },
                                    waitMsg: App.Language.General.message_guarding_information,
                                    success: function (form, response) {
                                        if (response.result.success == 'true') {
                                            App.Mtn.WoNode.Store.load({
                                                callback: function () {
                                                    mtn_work_order_id = response.result.mtn_work_order_id;
                                                    App.Mtn.Wo.OpenEditModeNode(mtn_work_order_id);
                                                    total_task = response.result.total_task;
                                                    total_other_costs = response.result.total_other_costs;
                                                    total_work_order = response.result.total_work_order;
                                                    total_work_order_iva = response.result.total_work_order_iva;
                                                }
                                            });
                                            App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
                                            b.ownerCt.ownerCt.ownerCt.close();
                                        } else {
                                            alert(App.Language.Maintenance.error_creating_ot);
                                        }
                                    },
                                    failure: function (form, action) {
                                        switch (action.failureType) {
                                            case Ext.form.Action.CLIENT_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                                break;
                                            case Ext.form.Action.CONNECT_FAILURE:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                                break;
                                            case Ext.form.Action.SERVER_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                        }
                                    }
                                });

                            }
                        }
                        //                }
                    }]
            }];
        App.Mtn.generateWorkOrderNodeWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.addTaskNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_task,
    width: 820,
    loadMask: true,
    padding: 1,
    viewConfig: {
        forceFit: true
    },
    modal: true,
    listeners: {
        'beforerender': function () {
            App.Mtn.PriceListComponentNode.Store.setBaseParam('current_price_list', 'true');
            App.Mtn.PriceListComponentNode.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
            App.Mtn.PriceListComponentNode.Store.load();
        },
        'close': function () {
            App.Mtn.WoTask.Id = null;
            App.Mtn.WoTask.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
            App.Mtn.WoTask.Store.load();
            App.Mtn.WoTaskComponent.Store.baseParams = [];
            App.Mtn.WoTaskComponent.Store.load();
        }
    },
    initComponent: function () {
        this.items = [{
                xtype: 'panel',
                width: 795,
                border: false,
                viewConfig: {
                    forceFit: true
                },
                items: [{
                        xtype: 'panel',
                        border: false,
                        padding: 1,
                        viewConfig: {
                            forceFit: true
                        },
                        items: [{
                                xtype: 'form',
                                id: 'App.Mtn.Wo.FormWoNodeTask',
                                labelAlign: 'top',
                                border: false,
                                viewConfig: {
                                    forceFit: true
                                },
                                frame: true,
                                padding: '5 5 5 5',
                                items: [{
                                        border: false,
                                        viewConfig: {
                                            forceFit: true
                                        },
                                        items: [{
                                                layout: 'form',
                                                width: '100%',
                                                border: false,
                                                viewConfig: {
                                                    forceFit: true
                                                },
                                                defaultType: 'textfield',
                                                items: [{
                                                        xtype: 'combo',
                                                        fieldLabel: App.Language.General.task,
                                                        id: 'App.Mtn.TaskCombo',
                                                        anchor: '98%',
                                                        selecOnFocus: true,
                                                        typeAhead: true,
                                                        selectOnFocus: true,
                                                        triggerAction: 'all',
                                                        hiddenName: 'mtn_task_id',
                                                        editable: false,
                                                        store: App.Mtn.TaskByNode.StoreGrid,
                                                        displayField: 'mtn_task_name',
                                                        valueField: 'mtn_task_id',
                                                        mode: 'remote',
                                                        minChars: 0,
                                                        allowBlank: false,
                                                        listeners: {
                                                            'afterrender': function (cb) {
                                                                cb.__value = cb.value;
                                                                cb.setValue('');
                                                                cb.getStore().load({
                                                                    callback: function () {
                                                                        cb.setValue(cb.__value);
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }]
                                            }]
                                    }, {
                                        layout: 'column',
                                        padding: '5 0 15 0',
                                        items: [{
                                                columnWidth: .3,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'numberfield',
                                                        fieldLabel: App.Language.General.value,
                                                        anchor: '98%',
                                                        name: 'mtn_work_order_task_price'
                                                    }]
                                            }, {
                                                columnWidth: .4,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'combo',
                                                        fieldLabel: App.Language.Maintenance.currency,
                                                        anchor: '98%',
                                                        selecOnFocus: true,
                                                        typeAhead: true,
                                                        selectOnFocus: true,
                                                        triggerAction: 'all',
                                                        hiddenName: 'currency_id',
                                                        editable: false,
                                                        store: App.Core.Currency.Store,
                                                        displayField: 'currency_name',
                                                        valueField: 'currency_id',
                                                        mode: 'remote',
                                                        minChars: 0,
                                                        allowBlank: false,
                                                        listeners: {
                                                            'afterrender': function (cb) {
                                                                cb.__value = cb.value;
                                                                cb.setValue('');
                                                                cb.getStore().load({
                                                                    callback: function () {
                                                                        cb.setValue(cb.__value);
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                columnWidth: .3,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'numberfield',
                                                        fieldLabel: App.Language.General.duration_of_task,
                                                        anchor: '96%',
                                                        name: 'mtn_work_order_task_time_job'
                                                    }]
                                            }]
                                    }, {
                                        items: [{
                                                layout: 'form',
                                                width: '98%',
                                                border: false,
                                                viewConfig: {
                                                    forceFit: true
                                                },
                                                defaultType: 'textarea',
                                                items: [{
                                                        fieldLabel: App.Language.General.comment,
                                                        name: 'mtn_work_order_task_comment',
                                                        width: '100%',
                                                        height: 50
                                                    }]
                                            }]
                                    }]
                            }]
                    }, {
                        xtype: 'panel',
                        id: 'App.Mtn.Wo.PanelInsumosTaskNode',
                        hidden: true,
                        border: false,
                        viewConfig: {
                            forceFit: true
                        },
                        title: App.Language.Maintenance.Select_input_using_task,
                        width: '100%',
                        padding: 1,
                        items: [{
                                xtype: 'form',
                                layout: 'column',
                                padding: '10',
                                frame: true,
                                border: false,
                                viewConfig: {
                                    forceFit: true
                                },
                                height: 140,
                                items: [{
                                        columnWidth: .93,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'combo',
                                                fieldLabel: App.Language.Maintenance.input_type,
                                                id: 'App.Mtn.WoTaskComponent.ComponentTypeNode',
                                                anchor: '98%',
                                                selecOnFocus: true,
                                                typeAhead: true,
                                                hiddenName: 'mtn_component_type_id',
                                                store: App.Mtn.ComponentTypeByNode.Store,
                                                displayField: 'mtn_component_type_name',
                                                valueField: 'mtn_component_type_id',
                                                mode: 'remote',
                                                minChars: 0,
                                                editable: false,
                                                allowBlank: false,
                                                listeners: {
                                                    'select': function (cb, record) {
                                                        Ext.getCmp('App.Mtn.WoTaskComponent.ComponentNode').setDisabled(false);
                                                        App.Mtn.PriceListComponentNode.Store.setBaseParam('mtn_component_type_id', record.data.mtn_component_type_id);
                                                        App.Mtn.PriceListComponentNode.Store.load();
                                                    }
                                                }
                                            }]
                                    }, {
                                        columnWidth: .93,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'combo',
                                                fieldLabel: App.Language.Maintenance.input,
                                                id: 'App.Mtn.WoTaskComponent.ComponentNode',
                                                anchor: '98%',
                                                selecOnFocus: true,
                                                typeAhead: true,
                                                hiddenName: 'mtn_price_list_component_id',
                                                store: App.Mtn.PriceListComponentNode.Store,
                                                displayField: 'mtn_component_with_type',
                                                valueField: 'mtn_price_list_component_id',
                                                disabled: true,
                                                mode: 'remote',
                                                minChars: 0,
                                                editable: false,
                                                allowBlank: false,
                                                listeners: {
                                                    'select': function (cb, record) {
                                                        if (record.data.MtnPriceListComponent.length == 0) {
                                                            //Habilitar el textfield para ingresar el valor
                                                            Ext.getCmp('App.Mtn.PriceListComponentNode.Store').setDisabled(false);
                                                            Ext.getCmp('App.Mtn.PriceListComponentNode.Store').setValue('');
                                                        } else {
                                                            //Mantener deshabilitado el textfield para mostrar el valor del insumo
                                                            Ext.getCmp('App.Mtn.PriceListComponentNode.Store').setDisabled(true);
                                                            //Mostrar el valor del insumo
                                                            var valor = record.data.MtnPriceListComponent[0].mtn_price_list_component_price;
                                                            Ext.getCmp('App.Mtn.PriceListComponentNode.Store').setValue(valor);
                                                        }
                                                    }
                                                }
                                            }, {
                                                columnWidth: .1,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'numberfield',
                                                        name: 'mtn_work_order_component_price',
                                                        id: 'App.Mtn.PriceListComponentNode.Store',
                                                        fieldLabel: App.Language.General.price,
                                                        anchor: '50%',
                                                        disabled: true,
                                                        value: '',
                                                        allowBlank: false
                                                    }, {
                                                        xtype: 'numberfield',
                                                        name: 'mtn_work_order_component_amount',
                                                        id: 'App.Mtn.WoTaskComponent.ComponentNodeAmount',
                                                        fieldLabel: App.Language.General.quantity,
                                                        anchor: '50%',
                                                        allowBlank: false
                                                    }]
                                            }]
                                    }, {
                                        columnWidth: .07,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'spacer',
                                                width: 5
                                            }, {
                                                xtype: 'button',
                                                text: App.Language.General.add,
                                                hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                                                anchor: '100%',
                                                handler: function (b) {
                                                    form = b.ownerCt.ownerCt.getForm();
                                                    if (form.isValid()) {
                                                        form.submit({
                                                            clientValidation: true,
                                                            url: 'index.php/mtn/wotaskcomponent/addNode',
                                                            params: {
                                                                mtn_work_order_task_id: App.Mtn.WoTask.Id
                                                            },
                                                            waitMsg: App.Language.General.message_guarding_information,
                                                            success: function (form, response) {
                                                                App.Mtn.WoTaskComponent.AddComponent();
                                                            },
                                                            failure: function (form, action) {
                                                                switch (action.failureType) {
                                                                    case Ext.form.Action.CLIENT_INVALID:
                                                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                                                        break;
                                                                    case Ext.form.Action.CONNECT_FAILURE:
                                                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                                                        break;
                                                                    case Ext.form.Action.SERVER_INVALID:
                                                                        Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            }]
                                    }]
                            }, {
                                xtype: 'panel',
                                border: false,
                                padding: 1,
                                viewConfig: {
                                    forceFit: true
                                },
                                tbar: {
                                    xtype: 'toolbar',
                                    height: 26,
                                    border: false,
                                    items: [{
                                            xtype: 'button',
                                            text: App.Language.General.ddelete,
                                            hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                                            iconCls: 'delete_icon',
                                            handler: function (b) {
                                                grid = Ext.getCmp('App.Mtn.WoTaskComponentNodeGrid');
                                                if (grid.getSelectionModel().getCount()) {
                                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function (b) {
                                                        if (b == 'yes') {
                                                            grid.getSelectionModel().each(function (record) {
                                                                App.Mtn.WoTaskComponent.Store.remove(record);
                                                            });
                                                        }
                                                    });
                                                } else {
                                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                                }
                                            }
                                        }]
                                },
                                items: [{
                                        xtype: 'editorgrid',
                                        id: 'App.Mtn.WoTaskComponentNodeGrid',
                                        height: 101,
                                        width: '100%',
                                        padding: '5 5 5 5',
                                        //                        margins: '5 5 5 5',
                                        border: true,
                                        store: App.Mtn.WoTaskComponent.Store,
                                        viewConfig: {
                                            forceFit: true
                                        },
                                        clicksToEdit: 1,
                                        columns: [new Ext.grid.CheckboxSelectionModel(),
                                            {
                                                header: App.Language.Maintenance.input_name,
                                                dataIndex: 'mtn_component_name',
                                                sortable: true,
                                                width: 480,
                                                renderer: function (value, metadata, record, rowIndex, colIndex, store) {
                                                    metadata.attr = 'ext:qtip="' + value + '"';
                                                    return value;
                                                }
                                            }, {
                                                header: App.Language.General.quantity,
                                                dataIndex: 'mtn_work_order_component_amount',
                                                sortable: true,
                                                align: 'center',
                                                width: 70,
                                                editor: new Ext.form.NumberField({
                                                    allowDecimals: false,
                                                    allowNegative: false,
                                                    blankText: App.Language.Maintenance.amount_greater_zero
                                                })
                                            }, {
                                                header: App.Language.Maintenance.unit_price,
                                                dataIndex: 'mtn_work_order_task_component_price',
                                                sortable: true,
                                                width: 90,
                                                renderer: function (value) {
                                                    return Ext.util.Format.number(value, App.General.DefaultSystemCurrencyFormatMoney);
                                                }
                                            }, {
                                                header: App.Language.Maintenance.total_price,
                                                dataIndex: 'mtn_work_order_task_component_price',
                                                sortable: true,
                                                width: 90,
                                                renderer: function (value, metadata, record) {
                                                    var total = value * record.data.mtn_work_order_component_amount;
                                                    return Ext.util.Format.number(total, App.General.DefaultSystemCurrencyFormatMoney);
                                                }
                                            }
                                        ],
                                        sm: new Ext.grid.CheckboxSelectionModel()
                                    }]
                            }]
                    }],
                buttons: [
                    //        {
                    //                xtype: 'button',
                    //                text: App.Language.General.close,
                    //                handler: function(b)
                    //                {
                    //                    b.ownerCt.ownerCt.ownerCt.close();
                    //                }
                    //            },
                    {
                        xtype: 'button',
                        text: App.Language.General.save,
                        hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                        id: 'App.Mtn.Wo.FormWoNodeTask.BtnSaveWoTask',
                        handler: function (b) {
                            form = Ext.getCmp('App.Mtn.Wo.FormWoNodeTask').getForm();
                            if (form.isValid()) {
                                form.submit({
                                    clientValidation: true,
                                    url: (App.Mtn.WoTask.Id != null ? 'index.php/mtn/wotask/updateNode' : 'index.php/mtn/wotask/addNode'),
                                    params: {
                                        mtn_work_order_id: App.Mtn.Wo.Id,
                                        mtn_work_order_task_id: App.Mtn.WoTask.Id
                                    },
                                    waitMsg: App.Language.General.message_guarding_information,
                                    success: function (form, response) {
                                        if (response.result.success == 'true') {
                                            App.Mtn.WoTask.ActiveGuiEditMode(response.result.mtn_work_order_task_id);
                                            App.Mtn.WoTask.Store.load({
                                                callback: function () {
                                                    mtn_work_order_task_id = response.result.mtn_work_order_task_id;
                                                }
                                            });
                                            Ext.getCmp('App.Mtn.Wo.WinWoNode').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWoNode'));
                                            b.ownerCt.ownerCt.ownerCt.close();
                                        } else {
                                            if (response.result.success == 'false_uf') {
                                                Ext.Msg.alert(App.Language.General.error, response.result.msg);
                                            } else {
                                                alert(App.Language.Maintenance.error_when_entering_the_job);
                                            }
                                        }
                                    },
                                    failure: function (form, action) {
                                        switch (action.failureType) {
                                            case Ext.form.Action.CLIENT_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                                break;
                                            case Ext.form.Action.CONNECT_FAILURE:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                                break;
                                            case Ext.form.Action.SERVER_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                        }
                                    }
                                });
                            }
                        }
                    }
                ]
            }];
        App.Mtn.addTaskNodeWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.addOtherCostsNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_other_costs,
    width: 600,
    padding: 1,
    frame: true,
    viewConfig: {
        forceFit: true
    },
    modal: true,
    id: 'App.Mtn.Wo.WinOtherCostsNode',
    listeners: {
        'close': function () {
            App.Mtn.OtherCostsWo.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
            App.Mtn.OtherCostsWo.Store.load();
        }
    },
    initComponent: function () {
        this.items = [{
                xtype: 'panel',
                border: false,
                items: [{
                        xtype: 'form',
                        id: 'App.Mtn.Wo.FormWoNodeOtherCosts',
                        labelAlign: 'top',
                        border: false,
                        frame: true,
                        padding: '5 0 5 0',
                        items: [{
                                layout: 'column',
                                padding: '5 0 15 0',
                                items: [{
                                        columnWidth: .6,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'combo',
                                                fieldLabel: App.Language.Maintenance.name_costs,
                                                anchor: '98%',
                                                selecOnFocus: true,
                                                typeAhead: true,
                                                selectOnFocus: true,
                                                triggerAction: 'all',
                                                editable: false,
                                                hiddenName: 'mtn_other_costs_id',
                                                store: App.Mtn.OtherCostsByNode.Store,
                                                displayField: 'mtn_other_costs_name',
                                                valueField: 'mtn_other_costs_id',
                                                mode: 'remote',
                                                minChars: 0,
                                                allowBlank: false,
                                                listeners: {
                                                    'afterrender': function (cb) {
                                                        cb.__value = cb.value;
                                                        cb.setValue('');
                                                        cb.getStore().load({
                                                            callback: function () {
                                                                cb.setValue(cb.__value);
                                                            }
                                                        });
                                                    }
                                                }
                                            }]
                                    }, {
                                        columnWidth: .4,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'numberfield',
                                                fieldLabel: App.Language.General.value,
                                                anchor: '98%',
                                                name: 'mtn_work_order_other_costs_costs'
                                            }]
                                    }]
                            }, {
                                items: [{
                                        layout: 'form',
                                        width: '100%',
                                        defaultType: 'textarea',
                                        items: [{
                                                fieldLabel: App.Language.General.comment,
                                                name: 'mtn_work_order_other_costs_comment',
                                                width: '100%',
                                                height: 50
                                            }]
                                    }]
                            }]
                    }],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        xtype: 'button',
                        text: App.Language.General.save,
                        id: 'App.Mtn.Wo.FormWoNodeOtherCosts.BtnSaveOtherCosts',
                        hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                        handler: function (b) {
                            form = Ext.getCmp('App.Mtn.Wo.FormWoNodeOtherCosts').getForm();
                            if (form.isValid()) {
                                form.submit({
                                    clientValidation: true,
                                    url: 'index.php/mtn/woothercosts/addNode',
                                    params: {
                                        mtn_work_order_id: App.Mtn.Wo.Id
                                    },
                                    waitMsg: App.Language.General.message_guarding_information,
                                    success: function (form, response) {
                                        App.Mtn.OtherCostsWo.Store.load();
                                        Ext.getCmp('App.Mtn.Wo.WinWoNode').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWoNode'));
                                        Ext.getCmp('App.Mtn.Wo.WinOtherCostsNode').close();
                                    },
                                    failure: function (form, action) {
                                        switch (action.failureType) {
                                            case Ext.form.Action.CLIENT_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                                break;
                                            case Ext.form.Action.CONNECT_FAILURE:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                                break;
                                            case Ext.form.Action.SERVER_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                        }
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Mtn.addOtherCostsNodeWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.WoTaskComponent.AddComponent = function () {
    Ext.getCmp('App.Mtn.WoTaskComponent.ComponentNode').setValue('');
    Ext.getCmp('App.Mtn.WoTaskComponent.ComponentNodeAmount').setValue('');
    Ext.getCmp('App.Mtn.PriceListComponentNode.Store').setValue(0);
    Ext.getCmp('App.Mtn.PriceListComponentNode.Store').setDisabled(true);
    App.Mtn.WoTaskComponent.Store.setBaseParam('mtn_work_order_task_id', App.Mtn.WoTask.Id);
    App.Mtn.WoTaskComponent.Store.load();
}

App.Mtn.Wo.resetWoNode = function () {
    App.Mtn.Wo.Id = null;
    App.Mtn.WoTotal = 0;
    App.Mtn.OtherCosts.Total = 0;
    App.Mtn.WoTask.Total = 0;

    App.Mtn.WoTask.Store.baseParams = [];
    App.Mtn.OtherCostsWo.Store.baseParams = [];
    App.Mtn.FlowWo.Store.baseParams = [];

    //Caso especial para los components
    App.Mtn.WoTaskComponent.Store.baseParams = [];
    App.Mtn.WoTaskComponent.Store.load();

    //Actualizamos el store del buscador de ot
    if (App.Mtn.Wo.EditModeFromGrid === true) {
        App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
    }

    App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 1);
    App.Mtn.WoTypes.Store.load();
}

App.Mtn.Wo.OpenEditModeNode = function (mtn_work_order_id) {
    w = new App.Mtn.generateWorkOrderNodeWindow({
        mtn_work_order_id: mtn_work_order_id
    });

    formWo = Ext.getCmp('App.Mtn.Wo.FormWoNode');
    btnWO = Ext.getCmp('App.Mtn.Wo.FormWoNode.BtnSaveWo');
    btnWO.handler = function (b) {
        form = formWo.getForm();
        if (form.isValid()) {
            form.submit({
                url: 'index.php/mtn/wo/updateNode',
                params: {
                    mtn_work_order_id: mtn_work_order_id
                },
                success: function (fp, o) {
                    App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
                    Ext.getCmp('App.Mtn.Wo.WinWoNode').close();
                },
                failure: function (fp, o) {
                    alert('Error:\n' + o.result.msg);
                }
            });
        }
    };
    w.show();
}

App.Mtn.WoTask.ActiveGuiEditMode = function (mtn_work_order_task_id) {
    Ext.getCmp('App.Mtn.Wo.PanelInsumosTaskNode').setVisible(true);
    App.Mtn.WoTask.Id = mtn_work_order_task_id;
    App.Mtn.WoTaskComponent.Store.setBaseParam('mtn_work_order_task_id', App.Mtn.WoTask.Id);
    App.Mtn.WoTaskComponent.Store.load();
}

App.Mtn.WoTask.OpenEditMode = function (record) {
    w = new App.Mtn.addTaskNodeWindow({
        title: App.Language.Maintenance.edit_task
    });
    formWoTask = Ext.getCmp('App.Mtn.Wo.FormWoNodeTask');

    formWoTask.record = record;
    App.Mtn.WoTask.ActiveGuiEditMode(record.data.mtn_work_order_task_id);
    formWoTask.getForm().loadRecord(record);
    w.show();
}

App.Mtn.OtherCosts.OpenEditMode = function (record) {
    w = new App.Mtn.addOtherCostsNodeWindow({
        title: App.Language.Maintenance.edit_other_costs
    });
    formWoOtherCosts = Ext.getCmp('App.Mtn.Wo.FormWoNodeOtherCosts');
    formWoOtherCosts.record = record;
    btnSaveOtherCosts = Ext.getCmp('App.Mtn.Wo.FormWoNodeOtherCosts.BtnSaveOtherCosts');
    btnSaveOtherCosts.handler = function () {
        form = formWoOtherCosts.getForm();
        if (form.isValid()) {
            form.updateRecord(formWoOtherCosts.record);
            w.close();
        }
    };
    formWoOtherCosts.getForm().loadRecord(record);
    w.show();
}

App.Mtn.Wo.DateNodeWO = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.change_dates,
    resizable: false,
    modal: true,
    width: 400,
    height: 140,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                plugins: [new Ext.ux.OOSubmit()],
                labelWidth: 150,
                padding: 5,
                items: [{
                        xtype: 'datefield',
                        fieldLabel: App.Language.Maintenance.new_date,
                        format: App.General.DefaultDateFormat,
                        name: 'mtn_work_order_date',
                        anchor: '100%'
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        text: App.Language.General.save,
                        ref: '../saveButton',
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: 'index.php/mtn/wo/updateDate',
                                    params: {
                                        mtn_work_order_id: aux_mtn_work_order_id
                                    },
                                    success: function (fp, o) {
                                        App.Mtn.WoNode.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitNumOT}});
                                        b.ownerCt.ownerCt.ownerCt.close();
                                        Ext.FlashMessage.alert(o.result.msg);
                                    },
                                    failure: function (fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Mtn.Wo.DateNodeWO.superclass.initComponent.call(this);
    }
});

App.Mtn.ChangeStateNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.change_of_status,
    resizable: false,
    modal: true,
    width: 450,
    height: 230,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                frame: false,
                labelWidth: 150,
                padding: 5,
                items: [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Maintenance.type_ot,
                        ref: '../wo_type'
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Maintenance.current_status,
                        ref: '../current_state'
                    }, {
                        xtype: 'combo',
                        id: 'App.Mtn.ComboState',
                        fieldLabel: App.Language.General.state,
                        anchor: '100%',
                        store: App.Mtn.ConfigStateAsociados.Store,
                        hiddenName: 'mtn_config_state_id',
                        displayField: 'mtn_system_work_order_status_name',
                        valueField: 'mtn_config_state_id',
                        typeAhead: true,
                        allowBlank: false,
                        triggerAction: 'all',
                        mode: 'remote',
                        editable: false,
                        minChars: 0
                    }, {
                        xtype: 'textarea',
                        fieldLabel: App.Language.General.commentary,
                        anchor: '100%',
                        name: 'mtn_status_log_comments'
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        text: App.Language.General.save,
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: 'index.php/mtn/wo/updateStateNode',
                                    params: {
                                        mtn_work_order_id: App.Mtn.Wo.CurrentWoData.mtn_work_order_id
                                    },
                                    success: function (fp, o) {
                                        b.ownerCt.ownerCt.ownerCt.close();
                                        Ext.getCmp('App.Mtn.Wo.WinWoNode').close();
                                        App.Mtn.Wo.OpenEditModeNode(App.Mtn.Wo.CurrentWoData.mtn_work_order_id);

                                    },
                                    failure: function (fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Mtn.ChangeStateNodeWindow.superclass.initComponent.call(this);
    }
});