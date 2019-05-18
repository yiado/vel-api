App.Mtn.Wo.CurrentWoData = null;

App.Mtn.Wo.Interface = Ext.extend(Ext.Panel, 
{
    title: App.Language.Maintenance.maintenance,
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: [
    App.ModuleActions[7001], 
    {
        xtype: 'tbseparator',
        width: 10
    }, App.ModuleActions[7003], 
    {
        xtype: 'tbseparator',
        width: 10
    }, {
        text: App.Language.General.search,
        iconCls: 'search_icon_16',
        enableToggle: true,
        handler: function(b){
            if (b.ownerCt.ownerCt.formSearchWo.isVisible()) {
                b.ownerCt.ownerCt.formSearchWo.hide();
            } else {
                b.ownerCt.ownerCt.formSearchWo.show();
            }
            b.ownerCt.ownerCt.doLayout();
        }
    }],
    listeners: 
    {
        'beforerender': function()
        {
            node = Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId);
            App.Mtn.Principal.listener(node);
            App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 1);
        }
    },
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'grid',
            id: 'App.Mtn.Wo.Grid',
            margins: '5 5 5 5',
            border: true,
            region: 'center',
            loadMask: true,
            viewConfig: 
            {
                forceFit: true
            },
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex)
                {
                    record = grid.getStore().getAt(rowIndex);
                    App.Mtn.Wo.OpenEditMode(record.data.mtn_work_order_id);
                }
            },
            store: App.Mtn.Wo.Store,
            columns: [new Ext.grid.CheckboxSelectionModel(), 
            {
                dataIndex: 'mtn_work_order_folio',
                header: App.Language.Maintenance.folio,
                sortable: true
            }, {
                xtype: 'datecolumn',
                header: App.Language.General.creation_date,
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
                dataIndex: 'asset_name',
                header: App.Language.General.asset,
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
                dataIndex: 'asset_path',
                header: App.Language.General.route,
                sortable: true,
                renderer: function(value, metadata, record, rowIndex, colIndex, store){
                    metadata.attr = 'ext:qtip="' + value + '"';
                    return value;
                }
            }],
            sm: new Ext.grid.CheckboxSelectionModel()
        }, {
            xtype: 'form',
            id: 'App.Mtn.SearchForm',
            plugins: [new Ext.ux.OOSubmit()],
            ref: 'formSearchWo',
            hidden: true,
            border: false,
            margins: '5 5 0 5',
            region: 'north',
            height: 220,
            frame: true,
            fbar: 
            [{
                text: App.Language.General.search,
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    App.Mtn.Wo.Store.baseParams = form.getValues();
                    App.Mtn.Wo.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                    App.Mtn.Wo.Store.load();
                    return;
                }
            }, {
                text: App.Language.General.clean,
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
//                    temp = Ext.getCmp('App.Mtn.SearchForm').path_search.getValue();
                    form.reset();
                    Ext.getCmp('Start_Date').update();
//                    Ext.getCmp('App.Mtn.SearchForm').path_search.setValue(temp);
                    App.Mtn.Wo.Store.baseParams = [];
                    App.Mtn.Wo.Store.load();
                }
            }],
            items: 
            [
        {
                xtype: 'displayfield',
                fieldLabel: App.Language.General.searching,
//                id: 'lbl_search_in',
                hiddenName: 'node_id'
//                ref: 'path_search'
            }, 
            {
                layout: 'column',
                items: 
                [{
                    columnWidth: .5,
                    layout: 'form',
                    items: 
                    [{
                        xtype: 'textfield',
                        fieldLabel: App.Language.Maintenance.folio,
                        name: 'mtn_work_order_folio',
                        anchor: '60%'
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.General.state,
                        anchor: '95%',
                        store: App.Mtn.PossibleStatus.Store,
                        hiddenName: 'mtn_system_work_order_status_id',
                        displayField: 'mtn_system_work_order_status_name',
                        valueField: 'mtn_system_work_order_status_id',
                        selecOnFocus: true,
                        typeAhead: true,
                        selectOnFocus:true,
                        forceSelection:true,
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
                        forceSelection:true,
                        triggerAction: 'all',
                        store: App.Core.Provider.Store,
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
                        selectOnFocus:true,
                        forceSelection:true,
                        store: App.Mtn.WoTypesAll.Store,
                        hiddenName: 'mtn_work_order_type_id',
                        displayField: 'mtn_work_order_type_name',
                        valueField: 'mtn_work_order_type_id',
                        triggerAction: 'all',
                        mode: 'remote',
                        minChars: 0,
                        editable: true
                    }, {
                        xtype: 'checkbox',
                        hideLabel: true,
                        boxLabel: App.Language.Maintenance.include_closed_orders,
                        name: 'include_closed_wo',
                        inputValue: 1
                    }, {
                        xtype: 'checkbox',
                        hideLabel: true,
                        boxLabel: App.Language.General.perform_internal_search,
                        name: 'search_branch',
                        inputValue: 1
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    items: 
                    [{
                        hiddenName: 'node_id'
                    }, {
                        columnWidth: .2,
                        layout: 'form',
                        anchor: '90%',
                        items: 
                        [{
                            xtype: 'label',
                            text: App.Language.Maintenance.select_range_of_dates_of_creation_of_the_ot
                        }]
                    }, {
                        columnWidth: .4,
                        layout: 'column',
                        anchor: '95%',
                        frame: true,
                        items: 
                        [{
                            columnWidth: .5,
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'datefield',
                                ref: '../start_date',
                                id: 'Start_Date',
                                fieldLabel: App.Language.General.start_date,
                                name: 'start_date',
                                anchor: '95%',
                                listeners: 
                                {
                                    'select': function(fd, date)
                                    {
                                        fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                    }
                                }
                            }]
                        }, {
                            columnWidth: .5,
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'datefield',
                                ref: '../end_date',
                                fieldLabel: App.Language.General.end_date,
                                name: 'end_date',
                                anchor: '95%',
                                listeners: 
                                {
                                    'select': function(fd, date)
                                    {
                                        fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                    }
                                }
                            }]
                        }]
                    }, {
                        xtype: 'spacer',
                        height: 15
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.Asset.asset_type,
                        anchor: '95%',
                        store: App.Asset.Type.Store,
                        hiddenName: 'asset_type_id',
                        displayField: 'asset_type_name',
                        valueField: 'asset_type_id',
                        triggerAction: 'all',
                        mode: 'remote',
                        typeAhead: true,
                        selectOnFocus:true,
                        forceSelection:true,
                        minChars: 0
                    }, {
                        xtype: 'textfield',
                        fieldLabel: App.Language.General.requested_by,
                        name: 'mtn_work_order_requested_by',
                        anchor: '95%'
                    }]
                }]
            }]
        }];
        App.Mtn.Wo.Interface.superclass.initComponent.call(this);
    }
});

App.Mtn.generateWorkOrderWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.new_work_order,
    width: 780,
    height: 600,
    layout: 'fit',
    padding: 1,
    maximizable : true,
    modal: true,
    id: 'App.Mtn.Wo.WinWo',
    listeners: 
    {
        'beforerender': function(w)
        {
            if (App.Security.Actions[7002] === undefined) 
            {
                App.Asset.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                w.panel.form_wo.getForm().load
                ({
                    url: 'index.php/mtn/wo/getOne',
                    params: 
                    {
                        mtn_work_order_id: w.mtn_work_order_id
                    },
                    success: function(fp, o)
                    {
                        record = o.result;
						
                        App.Mtn.Wo.CurrentWoData = record.data;
						
                        App.Mtn.Wo.Id = record.data.mtn_work_order_id;
                        asset_id = record.data.asset_id;
                        mtn_work_order_comment = record.data.mtn_work_order_comment;
						
                        total_task = record.data.total_task;
                        total_other_costs = record.data.total_other_costs;
                        total_work_order = record.data.total_work_order;
						
                        w.setHeight(600);
                        w.setWidth(780);
                        w.panel.form_wo.tab_panel.panel_comment.comment.setValue(mtn_work_order_comment);
                        w.panel.form_wo.panel4.colum_11.form_11.total_task_dd.setValue(total_task);
                        w.panel.form_wo.panel4.colum_11.form_11.total_other_costs_dd.setValue(total_other_costs);
                        w.panel.form_wo.panel4.colum_11.form_11.total_work_order_dd.setValue(total_work_order);
                        w.setTitle(App.Language.Maintenance.work_order_number + record.data.mtn_work_order_folio);
						
                        App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_config_state_id', record.data.MtnConfigState.mtn_config_state_id);
						
                        App.Mtn.ConfigStateAsociados.Store.load();
                        Ext.getCmp('App.Mtn.Wo.TypeId').setVisible(false);
                        Ext.getCmp('App.Mtn.Wo.TypeId').setDisabled(true);
                        Ext.getCmp('App.Mtn.Wo.Type1').setVisible(false);
						
                        Ext.getCmp('App.Mtn.StateDisplay').setValue(record.data.MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name);
                        Ext.getCmp('App.Mtn.ColumState').setVisible(true);
						
                        Ext.getCmp('App.Mtn.Wo.Type2').setVisible(true);
                        Ext.getCmp('App.Mtn.Wo.Label1').setVisible(false);
                        Ext.getCmp('App.Mtn.Wo.TextOt').setVisible(true);
                        Ext.getCmp('App.Mtn.Wo.TextOt').setValue(record.data.MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name);
						
                        App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_work_order_type_id', record.data.MtnConfigState.mtn_work_order_type_id);
                        App.Mtn.ConfigStateAsociados.Store.load();
						
						
                        Ext.getCmp('App.Mtn.WoStateLabel1').setVisible(false);
                        Ext.getCmp('App.Mtn.WoStateCombo').setVisible(false);
						
                        Ext.getCmp('App.Mtn.Wo.Botton').setVisible(false);
                        Ext.getCmp('App.Mtn.Wo.Date').setDisabled(true);
						
                        Ext.getCmp('App.Mtn.Wo.Root').setValue(record.data.Asset.asset_path);
                        Ext.getCmp('App.Mtn.Wo.AssetDisplay').setValue(record.data.Asset.asset_name);
						
						
                        Ext.getCmp('App.Mtn.HiddenPanel').setVisible(true);
                        Ext.getCmp('App.Mtn.PanelTotales').setVisible(true);
                        Ext.getCmp('App.Mtn.Wo.TbarState').setDisabled(false);
                        Ext.getCmp('App.Mtn.Wo.TbarPrintIcon').setDisabled(false);
                        Ext.getCmp('App.Mtn.Wo.TbaDetail').setDisabled(false);
						
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
						
                        //Escondemos los Botones para hacer funcionar el Permiso
                        App.Mtn.Wo.CurrentWoData.mtn_work_order_closed = 1;
						
                        // deshabilitando botones de cambio cuando estah cerrada
                        if (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == 1) 
                        {
                            w.panel.form_wo.tab_panel.taskgrid.getTopToolbar().hide();
                            w.panel.form_wo.tab_panel.taskgrid.doLayout();
							
                            w.panel.form_wo.tab_panel.othercostgrid.getTopToolbar().hide();
                            w.panel.form_wo.tab_panel.othercostgrid.doLayout();
							
                            Ext.getCmp('App.Mtn.Wo.FormWo.BtnSaveWo').hide();
                            Ext.getCmp('App.Mtn.Wo.TbarState').hide();
							
                        }
						
                        // creator user
                        Ext.getCmp('App.Mtn.Wo.CreatorUser').setText(App.Mtn.Wo.CurrentWoData.User.user_name);
                        App.Mtn.Wo.Store.load();
                    }
                })
            } else {
                App.Asset.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                w.panel.form_wo.getForm().load
                ({
                    url: 'index.php/mtn/wo/getOne',
                    params: 
                    {
                        mtn_work_order_id: w.mtn_work_order_id
                    },
                    success: function(fp, o)
                    {
                        record = o.result;
						
                        App.Mtn.Wo.CurrentWoData = record.data;
						
                        App.Mtn.Wo.Id = record.data.mtn_work_order_id;
                        asset_id = record.data.asset_id;
                        mtn_work_order_comment = record.data.mtn_work_order_comment;
						
                        total_task = record.data.total_task;
                        total_other_costs = record.data.total_other_costs;
                        total_work_order = record.data.total_work_order;
						
                        w.setHeight(600);
                        w.setWidth(780);
                        w.panel.form_wo.tab_panel.panel_comment.comment.setValue(mtn_work_order_comment);
                        w.panel.form_wo.panel4.colum_11.form_11.total_task_dd.setValue(total_task);
                        w.panel.form_wo.panel4.colum_11.form_11.total_other_costs_dd.setValue(total_other_costs);
                        w.panel.form_wo.panel4.colum_11.form_11.total_work_order_dd.setValue(total_work_order);
                        w.setTitle(App.Language.Maintenance.work_order_number + record.data.mtn_work_order_folio);
						
                        App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_config_state_id', record.data.MtnConfigState.mtn_config_state_id);
						
                        App.Mtn.ConfigStateAsociados.Store.load();
                        Ext.getCmp('App.Mtn.Wo.TypeId').setVisible(false);
                        Ext.getCmp('App.Mtn.Wo.TypeId').setDisabled(true);
                        Ext.getCmp('App.Mtn.Wo.Type1').setVisible(false);
						
                        Ext.getCmp('App.Mtn.StateDisplay').setValue(record.data.MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name);
                        Ext.getCmp('App.Mtn.ColumState').setVisible(true);
						
                        Ext.getCmp('App.Mtn.Wo.Type2').setVisible(true);
                        Ext.getCmp('App.Mtn.Wo.Label1').setVisible(false);
                        Ext.getCmp('App.Mtn.Wo.TextOt').setVisible(true);
                        Ext.getCmp('App.Mtn.Wo.TextOt').setValue(record.data.MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name);
						
                        App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_work_order_type_id', record.data.MtnConfigState.mtn_work_order_type_id);
                        App.Mtn.ConfigStateAsociados.Store.load();
						
						
                        Ext.getCmp('App.Mtn.WoStateLabel1').setVisible(false);
                        Ext.getCmp('App.Mtn.WoStateCombo').setVisible(false);
						
                        Ext.getCmp('App.Mtn.Wo.Botton').setVisible(false);
                        Ext.getCmp('App.Mtn.Wo.Date').setDisabled(true);
						
                        Ext.getCmp('App.Mtn.Wo.Root').setValue(record.data.Asset.asset_path);
                        Ext.getCmp('App.Mtn.Wo.AssetDisplay').setValue(record.data.Asset.asset_name);
						
						
                        Ext.getCmp('App.Mtn.HiddenPanel').setVisible(true);
                        Ext.getCmp('App.Mtn.PanelTotales').setVisible(true);
                        Ext.getCmp('App.Mtn.Wo.TbarState').setDisabled(false);
                        Ext.getCmp('App.Mtn.Wo.TbarPrintIcon').setDisabled(false);
                        Ext.getCmp('App.Mtn.Wo.TbaDetail').setDisabled(false);
						
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
                        if (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == 1) 
                        {
                            w.panel.form_wo.tab_panel.taskgrid.getTopToolbar().hide();
                            w.panel.form_wo.tab_panel.taskgrid.doLayout();
							
                            w.panel.form_wo.tab_panel.othercostgrid.getTopToolbar().hide();
                            w.panel.form_wo.tab_panel.othercostgrid.doLayout();
							
                            Ext.getCmp('App.Mtn.Wo.FormWo.BtnSaveWo').hide();
                            Ext.getCmp('App.Mtn.Wo.TbarState').hide();
							
                        }
						
                        // creator user
                        Ext.getCmp('App.Mtn.Wo.CreatorUser').setText(App.Mtn.Wo.CurrentWoData.User.user_name);
                        App.Mtn.Wo.Store.load();
                    }
                })
            }
        },
        'close': function()
        {
            App.Mtn.Wo.resetWo();
        }
    },
    initComponent: function(){
        this.items = 
        [{
            xtype: 'panel',
            padding: 0,
            layout: 'fit',
            border: false,
            ref: 'panel',
            tbar: 
            {
                xtype: 'toolbar',
                height: 26,
                items: 
                [{
                    xtype: 'button',
                    text: App.Language.Maintenance.change_of_status,
                    id: 'App.Mtn.Wo.TbarState',
                    iconCls: 'changeState_icon',
                    disabled: true,
                    handler: function()
                    {
                        w = new App.Mtn.ChangeStateWindow();
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
                    text: App.Language.General.printer,
                    iconCls: 'print_icon',
                    disabled: true,
                    handler: function()
                    {
                        document.location = mtn_export_wordorder + App.Mtn.Wo.Id;
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    id: 'App.Mtn.Wo.TbaDetail',
                    iconCls: 'information_icon',
                    text: App.Language.Asset.asset_details,
                    disabled: true,
                    handler: function()
                    {
                        w = new App.Mtn.AssetdetailsAssetWindow();
                        App.Asset.Store.setBaseParam('asset_id', asset_id);
                        App.Asset.Store.load({params: {node_id: App.Interface.selectedNodeId,start: 0, limit: App.GridLimitAsset}});
                        App.Asset.OtrosDatos.Store.setBaseParam('asset_id', asset_id);
                        App.Asset.OtrosDatos.Store.load
                        ({
                            callback: function()
                            {
                                App.Asset.OtrosDatos.Store.each(function(record)
                                {
                                    field = new Ext.form.DisplayField
                                    ({
                                        xtype: 'label',
                                        width: 270,
                                        fieldLabel: record.data.label,
                                        value: record.data.value,
                                        name: record.data.asset_other_data_attribute_id
                                    });
                                    w.uno.tabpanel.otherdata.add(field);
                                    w.uno.tabpanel.otherdata.doLayout();
                                });
                            }
                        });
                        w.show();
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
                    id: 'App.Mtn.Wo.CreatorUser'
                }]
            },
            items: 
            [{
                xtype: 'form',
                id: 'App.Mtn.Wo.FormWo',
                plugins: [new Ext.ux.OOSubmit()],
                ref: 'form_wo',
                height: '100%',
                width: '100%',
                border: false,
                layout: 'border',
                items: 
                [{
                    /** formulario **/
                    xtype: 'panel',
                    region: 'north',
                    margins: '5 5 5 5',
                    frame: true,
                    labelWidth: 120,
                    ref: 'url_action',
                    border: false,
                    height: 150,
                    items: 
                    [{
                        layout: 'column',
                        border: true,
                        ref: 'colum_buscador',
                        items: 
                        [{
                            ref: 'form_buscador_1',
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'displayfield',
                                fieldLabel: App.Language.General.route,
                                name: 'asset_path',
                                id: 'App.Mtn.Wo.Root',
                                ref: 'asset_path',
                                anchor: '100%'
                            }]
                        }]
                    }, {
                        layout: 'column',
                        ref: 'colum_general',
                        items: 
                        [{
                            columnWidth: .6,
                            ref: 'form_general',
                            layout: 'form',
                            items: 
                            [{
                                layout: 'column',
                                ref: 'colum_asset',
                                items: 
                                [{
                                    columnWidth: .80,
                                    ref: 'form_asset',
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'displayfield',
                                        fieldLabel: App.Language.General.asset,
                                        id: 'App.Mtn.Wo.AssetDisplay',
                                        name: 'asset_name',
                                        ref: 'displayfield_asset',
                                        anchor: '95%'
                                    }]
                                }, {
                                    columnWidth: .20,
                                    layout: 'form',
                                    ref: 'form_asset_button',
                                    items: 
                                    [{
                                        xtype: 'button',
                                        id: 'App.Mtn.Wo.Botton',
                                        iconCls: 'search_icon_16',
                                        ref: 'botton_add_asset',
                                        value: '0',
                                        pressed: 'false',
                                        anchor: '53%',
                                        handler: function(b)
                                        {
                                            w = new App.Mtn.Wo.AddAssetWindow();
                                            b.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                            w.show();
                                        }
                                    }]
                                }]
                            }, {
                                layout: 'column',
                                labelWidth: 80,
                                id: 'App.Mtn.Wo.Type1',
                                hidden: false,
                                items: 
                                [{
                                    columnWidth: .2,
                                    ref: 'form_combo_type_ot',
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'label',
                                        text: App.Language.Maintenance.type_ot,
                                        ref: 'tipo_ot_label1',
                                        id: 'App.Mtn.Wo.Label1',
                                        hidden: false
                                    }]
                                }, {
                                    columnWidth: .8,
                                    layout: 'form',
                                    labelWidth: 30,
                                    ref: 'form_text_type_ot',
                                    items: 
                                    [{
                                        xtype: 'combo',
                                        id: 'App.Mtn.Wo.TypeId',
                                        store: App.Mtn.WoTypesAllByAssetSolo.Store,
                                        hiddenName: 'mtn_work_order_type_id',
                                        triggerAction: 'all',
                                        displayField: 'mtn_work_order_type_name',
                                        valueField: 'mtn_work_order_type_id',
                                        selecOnFocus: true,
                                        anchor: '88%',
                                        typeAhead: true,
                                        editable: true,
                                        allowBlank: false,
                                        hideLabel: false,
                                        hidden: false,
                                        mode: 'remote',
                                        minChars: 0,
                                        listeners: 
                                        {
//                                            'beforerender': function(w)
//                                            {
//                                                Ext.getCmp('App.Mtn.Wo.Type2').setVisible(false);
//                                            },
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
                                            },
                                            'select': function(cb, record){
                                                Ext.getCmp('App.Mtn.WoStateCombo').enable();
                                                App.Mtn.ConfigStateAsociados.Store.setBaseParam('mtn_work_order_type_id', record.data.mtn_work_order_type_id);
                                                App.Mtn.ConfigStateAsociados.Store.load();
                                            }
                                        }
                                    }]
                                }]
                            }, {
                                layout: 'column',
                                labelWidth: 80,
                                ref: 'colum_combo_type_ot',
                                id: 'App.Mtn.Wo.Type2',
                                items: 
                                [{
                                    columnWidth: .2,
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'label',
                                        text: App.Language.Maintenance.type_ot,
                                        id: 'App.Mtn.Wo.Label2'
                                    }]
                                }, {
                                    columnWidth: .8,
                                    labelWidth: 30,
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'displayfield',
                                        anchor: '88%',
                                        name: 'mtn_work_order_type_name',
                                        id: 'App.Mtn.Wo.TextOt'
                                    }]
                                }]
                            }, {
                                xtype: 'checkbox',
                                fieldLabel: App.Language.Maintenance.ot_cancelled,
                                id: 'App.Mtn.Wo.Estatus',
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
                            ref: 'form_data',
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'datefield',
                                fieldLabel: App.Language.General.date,
                                id: 'App.Mtn.Wo.Date',
                                ref: 'data',
                                name: 'mtn_work_order_date',
                                anchor: '95%',
                                editable: false,
                                allowBlank: false,
                                value: new Date()
                            }, {
                                layout: 'column',
                                labelWidth: 80,
                                id: 'App.Mtn.WoState1',
                                hidden: false,
                                items: 
                                [{
                                    columnWidth: .2,
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'label',
                                        text: App.Language.General.state,
                                        id: 'App.Mtn.WoStateLabel1',
                                        hidden: false
                                    }]
                                }, {
                                    columnWidth: .8,
                                    layout: 'form',
                                    labelWidth: 60,
                                    items: 
                                    [{
                                        xtype: 'combo',
                                        id: 'App.Mtn.WoStateCombo',
                                        store: App.Mtn.ConfigStateAsociados.Store,
                                        hiddenName: 'mtn_config_state_id',
                                        triggerAction: 'all',
                                        displayField: 'mtn_system_work_order_status_name',
                                        valueField: 'mtn_config_state_id',
                                        selecOnFocus: true,
                                        anchor: '95%',
                                        typeAhead: true,
                                        disabled: true,
                                        editable: true,
                                        allowBlank: false,
                                        hideLabel: false,
                                        hidden: false,
                                        mode: 'remote',
                                        minChars: 0
                                    }]
                                }]
                            }, {
                                layout: 'column',
                                labelWidth: 80,
                                items: 
                                [{
                                    columnWidth: .2,
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'label',
                                        hidden: true,
                                        id: 'App.Mtn.ColumState',
                                        text: App.Language.General.state
                                    }]
                                }, {
                                    columnWidth: .8,
                                    labelWidth: 60,
                                    layout: 'form',
                                    items: 
                                    [{
                                        xtype: 'displayfield',
                                        anchor: '100%',
                                        name: 'mtn_system_work_order_status_name',
                                        id: 'App.Mtn.StateDisplay'
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
                    ref: 'tab_panel',
                    border: true,
                    id: 'App.Mtn.HiddenPanel',
                    padding: 1,
                    margins: '0 5 5 5',
                    items: 
                    [{
                        xtype: 'grid',
                        title: App.Language.General.task,
                        ref: 'taskgrid',
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
                                    w = new App.Mtn.addTaskWindow();
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
                                    grid = Ext.getCmp('App.Mtn.WoTaskGrid');
                                    if (grid.getSelectionModel().getCount()) 
                                    {
                                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b){
                                            if (b == 'yes') 
                                            {
                                                grid.getSelectionModel().each(function(record)
                                                {
                                                    App.Mtn.WoTask.Store.remove(record);
                                                    Ext.getCmp('App.Mtn.Wo.WinWo').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWo'));
                                                });
                                            }
                                        });
                                    } else {
                                        Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                    }
                                }
                            }]
                        },
                        id: 'App.Mtn.WoTaskGrid',
                        store: App.Mtn.WoTask.Store,
                        padding: 2,
                        border: true,
                        viewConfig: 
                        {
                            forceFit: true
                        },
                        listeners: 
                        {
                            'rowdblclick': function(grid, rowIndex){
                                record = grid.getStore().getAt(rowIndex);
                                App.Mtn.WoTask.OpenEditMode(record);
                            }
                        },
                        columns: [new Ext.grid.CheckboxSelectionModel(), 
                        {
                            dataIndex: 'MtnTask',
                            header: App.Language.General.task_name,
                            sortable: true,
                            renderer: function(MtnTask)
                            {
                                return MtnTask.mtn_task_name;
                            }
                        }, {
                            dataIndex: 'mtn_work_order_task_price',
                            header: App.Language.General.price,
                            sortable: true
                        }, {
                            dataIndex: 'mtn_amount_component_in_task',
                            header: App.Language.Maintenance.input,
                            sortable: true
                        }, {
                            header: App.Language.General.time,
                            dataIndex: 'mtn_work_order_task_time_job',
                            sortable: true
                        }, {
                            header: App.Language.General.comment,
                            dataIndex: 'mtn_work_order_task_comment',
                            sortable: true
                        }],
                        sm: new Ext.grid.CheckboxSelectionModel()
                    }, {
                        xtype: 'grid',
                        title: App.Language.Maintenance.other_costs,
                        ref: 'othercostgrid',
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
                                    w = new App.Mtn.addOtherCostsWindow();
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
                                    grid = Ext.getCmp('App.Mtn.WoOtherCostsGrid');
                                    if (grid.getSelectionModel().getCount()) 
                                    {
                                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b){
                                            if (b == 'yes') 
                                            {
                                                grid.getSelectionModel().each(function(record)
                                                {
                                                    App.Mtn.OtherCostsWo.Store.remove(record);
                                                    Ext.getCmp('App.Mtn.Wo.WinWo').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWo'));
                                                });
                                            }
                                        });
                                    } else {
                                        Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                    }
                                }
                            }]
                        },
                        id: 'App.Mtn.WoOtherCostsGrid',
                        store: App.Mtn.OtherCostsWo.Store,
                        border: true,
                        padding: 2,
                        viewConfig: 
                        {
                            forceFit: true
                        },
                        listeners: 
                        {
                            'rowdblclick': function(grid, rowIndex)
                            {
                                record = grid.getStore().getAt(rowIndex);
                                App.Mtn.OtherCosts.OpenEditMode(record);
                            }
                        },
                        columns: [new Ext.grid.CheckboxSelectionModel(), 
                        {
                            dataIndex: 'mtn_other_costs_name',
                            header: App.Language.Maintenance.name_costs,
                            sortable: true,
                            width: 100
                        }, {
                            header: App.Language.General.value,
                            dataIndex: 'mtn_work_order_other_costs_costs',
                            sortable: true,
                            width: 100
                        }, {
                            header: App.Language.General.comment,
                            dataIndex: 'mtn_work_order_other_costs_comment',
                            sortable: true,
                            width: 100
                        }],
                        stripeRows: true,
                        sm: new Ext.grid.CheckboxSelectionModel()
                    }, {
                        xtype: 'grid',
                        title: App.Language.Asset.tracking,
                        store: App.Mtn.Log.Store,
                        border: true,
                        viewConfig: 
                        {
                            forceFit: true
                        },
                        listeners: 
                        {
                            'beforerender': function()
                            {
                                App.Mtn.Log.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
                                App.Mtn.Log.Store.load();
                            }
                        },
                        columns: 
                        [{
                            header: App.Language.Maintenance.state,
                            dataIndex: 'mtn_system_work_order_status_name',
                            sortable: true,
                            width: 35,
                            renderer: function(mtn_system_work_order_status_name, p, record)
                            {
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
                            renderer: function(value, p, record)
                            {
                                return String.format('{0}', value);
                            }
                        }]
                    }, {
                        xtype: 'panel',
                        title: App.Language.General.comment,
                        ref: 'panel_comment',
                        padding: 2,
                        border: true,
                        items: 
                        [{
                            xtype: 'textarea',
                            id: 'App.Mtn.Wo.OrderComment',
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
                    id: 'App.Mtn.PanelTotales',
                    ref: 'panel4',
                    padding: '5 0 0 5',
                    margins: '0 5 5 5',
                    height: 100,
                    border: false,
                    frame: true,
                    items: 
                    [{
                        layout: 'column',
                        border: false,
                        ref: 'colum_11',
                        items: 
                        [{
                            columnWidth: .5,
                            layout: 'form',
                            ref: 'form_11',
                            labelWidth: 150,
                            border: false,
                            items: 
                            [{
                                xtype: 'displayfield',
                                fieldLabel: App.Language.Maintenance.value_service,
                                ref: 'total_task_dd',
                                id: 'App.Mtn.Wo.TotalServicio',
                                anchor: '100%'
                            }, {
                                xtype: 'displayfield',
                                fieldLabel: App.Language.Maintenance.total_other_costs,
                                ref: 'total_other_costs_dd',
                                id: 'App.Mtn.Wo.TotalOtherCosts',
                                anchor: '100%'
                            }, {
                                xtype: 'displayfield',
                                fieldLabel: App.Language.Maintenance.total_ot,
                                ref: 'total_work_order_dd',
                                id: 'App.Mtn.Wo.Total',
                                anchor: '100%'
                            }]
                        }]
                    }]
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
                text: App.Language.General.save,
                id: 'App.Mtn.Wo.FormWo.BtnSaveWo',
                handler: function(b)
                {
                    if (undefined == window.asset_id && undefined == window.App.Mtn.Wo.Id) 
                    {
                        Ext.Msg.alert(App.Language.Core.notification, App.Language.Maintenance.do_not_believe_the_ot_you_must_first_select_an_asset);
                        b.ownerCt.ownerCt.ownerCt.close();
                    } else {
                        form = Ext.getCmp('App.Mtn.Wo.FormWo').getForm();
                        if (form.isValid() && App.Interface.selectedNodeId != 'root') 
                        {
                            form.submit
                            ({
                                clientValidation: true,
                                url: (App.Mtn.Wo.Id != null ? 'index.php/mtn/wo/update' : 'index.php/mtn/wo/addCorrective'),
                                params: 
                                {
                                    asset_id: asset_id,
                                    node_id: App.Interface.selectedNodeId
                                },
                                waitMsg: App.Language.General.message_guarding_information,
                                success: function(form, response)
                                {
                                    if (response.result.success == 'true') 
                                    {
                                        App.Mtn.Wo.Store.load
                                        ({
                                            callback: function()
                                            {
                                                mtn_work_order_id = response.result.mtn_work_order_id;
                                                App.Mtn.Wo.OpenEditMode(mtn_work_order_id);
                                                total_task = response.result.total_task;
                                                total_other_costs = response.result.total_other_costs;
                                                total_work_order = response.result.total_work_order;
                                            }
                                        });
                                        App.Mtn.Wo.Store.load();
                                        b.ownerCt.ownerCt.ownerCt.close();
                                    } else {
                                        alert(App.Language.Maintenance.error_creating_ot);
                                    }
                                },
                                failure: function(form, action)
                                {
                                    switch (action.failureType) 
                                    {
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
            }]
        }];
        App.Mtn.generateWorkOrderWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.addTaskWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.add_task,
    width: 600,
    loadMask: true,
    padding: 1,
    viewConfig: 
    {
        forceFit: true
    },
    modal: true,
    listeners: 
    {
        'beforerender': function()
        {
            App.Mtn.PriceListComponent.Store.setBaseParam('current_price_list', 'true');
            App.Mtn.PriceListComponent.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
            App.Mtn.PriceListComponent.Store.load();
        },
        'close': function()
        {
            App.Mtn.WoTask.Id = null;
            App.Mtn.WoTask.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
            App.Mtn.WoTask.Store.load();
            App.Mtn.WoTaskComponent.Store.baseParams = [];
            App.Mtn.WoTaskComponent.Store.load();
        }
    },
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'panel',
            border: false,
            items: 
            [{
                xtype: 'panel',
                border: false,
                viewConfig: 
                {
                    forceFit: true
                },
                items: 
                [{
                    xtype: 'form',
                    id: 'App.Mtn.Wo.FormWoTask',
                    labelAlign: 'top',
                    border: false,
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    frame: true,
                    padding: '5 5 5 5',
                    items: 
                    [{
                        border: false,
                        viewConfig: 
                        {
                            forceFit: true
                        },
                        items: 
                        [{
                            layout: 'form',
                            width: '100%',
                            border: false,
                            viewConfig: 
                            {
                                forceFit: true
                            },
                            defaultType: 'textfield',
                            items: 
                            [{
                                xtype: 'combo',
                                fieldLabel: App.Language.Maintenance.task,
                                anchor: '100%',
                                store: App.Mtn.Task.Store,
                                hiddenName: 'mtn_task_id',
                                displayField: 'mtn_task_name',
                                valueField: 'mtn_task_id',
                                selecOnFocus: true,
                                typeAhead: true,
                                selectOnFocus:true,
                                forceSelection:true,
                                typeAhead: true,
                                allowBlank: false,
                                triggerAction: 'all',
                                mode: 'remote',
                                minChars: 0
                            }]
                        }]
                    }, {
                        layout: 'column',
                        padding: '5 0 15 0',
                        items: 
                        [{
                            columnWidth: .5,
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'numberfield',
                                fieldLabel: App.Language.General.value,
                                anchor: '98%',
                                name: 'mtn_work_order_task_price'
                            }]
                        }, {
                            columnWidth: .5,
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'numberfield',
                                fieldLabel: App.Language.General.time,
                                anchor: '100%',
                                name: 'mtn_work_order_task_time_job'
                            }]
                        }]
                    }, {
                        items: 
                        [{
                            layout: 'form',
                            width: '100%',
                            border: false,
                            viewConfig: 
                            {
                                forceFit: true
                            },
                            defaultType: 'textarea',
                            items: 
                            [{
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
                id: 'App.Mtn.Wo.PanelInsumosTask',
                hidden: true,
                border: false,
                viewConfig: 
                {
                    forceFit: true
                },
                title: App.Language.Maintenance.Select_input_using_task,
                width: '100%',
                items: 
                [{
                    xtype: 'form',
                    layout: 'column',
                    padding: '10',
                    frame: true,
                    border: false,
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    height: 100,
                    items: 
                    [{
                        columnWidth: .8,
                        layout: 'form',
                        items: 
                        [{
                            xtype: 'combo',
                            fieldLabel: App.Language.Maintenance.input,
                            id: 'App.Mtn.WoTaskComponent.Component',
                            anchor: '98%',
                            selecOnFocus: true,
                            typeAhead: true,
                            hiddenName: 'mtn_price_list_component_id',
                            store: App.Mtn.PriceListComponent.Store,
                            displayField: 'mtn_component_with_type',
                            valueField: 'mtn_price_list_component_id',
                            mode: 'remote',
                            minChars: 0,
                            allowBlank: false,
                            listeners: 
                            {
                                'select': function(cb, record)
                                {
                                    if (record.data.MtnPriceListComponent.length == 0) 
                                    {
                                        //Habilitar el textfield para ingresar el valor
                                        Ext.getCmp('App.Mtn.WoTaskComponent.ComponentPrice').setDisabled(false);
                                        Ext.getCmp('App.Mtn.WoTaskComponent.ComponentPrice').setValue('');
                                    }
                                    else 
                                    {
                                        //Mantener deshabilitado el textfield para mostrar el valor del insumo
                                        Ext.getCmp('App.Mtn.WoTaskComponent.ComponentPrice').setDisabled(true);
                                        //Mostrar el valor del insumo
                                        var valor = record.data.MtnPriceListComponent[0].mtn_price_list_component_price;
                                        Ext.getCmp('App.Mtn.WoTaskComponent.ComponentPrice').setValue(valor);
                                    }
                                }
                            }
                        }, {
                            columnWidth: .1,
                            layout: 'form',
                            items: 
                            [{
                                xtype: 'numberfield',
                                name: 'mtn_work_order_component_price',
                                id: 'App.Mtn.WoTaskComponent.ComponentPrice',
                                fieldLabel: App.Language.General.price,
                                anchor: '50%',
                                disabled: true,
                                value: '',
                                allowBlank: false
                            }, {
                                xtype: 'numberfield',
                                name: 'mtn_work_order_component_amount',
                                id: 'App.Mtn.WoTaskComponent.ComponentAmount',
                                fieldLabel: App.Language.General.quantity,
                                anchor: '50%',
                                allowBlank: false
                            }]
                        }]
                    }, {
                        columnWidth: .1,
                        layout: 'form',
                        items: 
                        [{
                            xtype: 'button',
                            text: App.Language.General.add,
                            hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                            anchor: '100%',
                            handler: function(b)
                            {
                                form = b.ownerCt.ownerCt.getForm();
                                if (form.isValid()) 
                                {
                                    form.submit
                                    ({
                                        clientValidation: true,
                                        url: 'index.php/mtn/wotaskcomponent/add',
                                        params: 
                                        {
                                            mtn_work_order_task_id: App.Mtn.WoTask.Id
                                        },
                                        waitMsg: App.Language.General.message_guarding_information,
                                        success: function(form, response)
                                        {
                                            App.Mtn.WoTaskComponent.AddComponent();
                                        },
                                        failure: function(form, action)
                                        {
                                            switch (action.failureType) 
                                            {
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
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    tbar: 
                    {
                        xtype: 'toolbar',
                        height: 26,
                        items: 
                        [{
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                            iconCls: 'delete_icon',
                            handler: function(b)
                            {
                                grid = Ext.getCmp('App.Mtn.WoTaskComponentGrid');
                                if (grid.getSelectionModel().getCount()) 
                                {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b){
                                        if (b == 'yes') 
                                        {
                                            grid.getSelectionModel().each(function(record)
                                            {
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
                    items: 
                    [{
                        xtype: 'editorgrid',
                        id: 'App.Mtn.WoTaskComponentGrid',
                        height: 101,
                        width: '100%',
                        store: App.Mtn.WoTaskComponent.Store,
                        viewConfig: 
                        {
                            forceFit: true
                        },
                        clicksToEdit: 1,
                        columns: [new Ext.grid.CheckboxSelectionModel(), 
                        {
                            header: App.Language.Maintenance.input_name,
                            dataIndex: 'mtn_component_name',
                            sortable: true,
                            width: 420
                        }, {
                            header: App.Language.General.quantity,
                            dataIndex: 'mtn_work_order_component_amount',
                            sortable: true,
                            width: 130,
                            editor: new Ext.form.NumberField
                            ({
                                allowDecimals: false,
                                allowNegative: false,
                                blankText: App.Language.Maintenance.amount_greater_zero
                            })
                        }, {
                            header: App.Language.Maintenance.unit_price,
                            dataIndex: 'mtn_work_order_task_component_price',
                            sortable: true,
                            width: 170,
                            renderer: function(value)
                            {
                                return Ext.util.Format.number(value, App.General.DefaultSystemCurrencyFormatMoney);
                            }
                        }, {
                            header: App.Language.Maintenance.total_price,
                            dataIndex: 'mtn_work_order_task_component_price',
                            sortable: true,
                            width: 180,
                            renderer: function(value, metadata, record)
                            {
                                var total = value * record.data.mtn_work_order_component_amount;
                                return Ext.util.Format.number(total, App.General.DefaultSystemCurrencyFormatMoney);
                            }
                        }],
                        sm: new Ext.grid.CheckboxSelectionModel()
                    }]
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
                text: App.Language.General.save,
                hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                id: 'App.Mtn.Wo.FormWoTask.BtnSaveWoTask',
                handler: function(b)
                {
                    form = Ext.getCmp('App.Mtn.Wo.FormWoTask').getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            clientValidation: true,
                            url: (App.Mtn.WoTask.Id != null ? 'index.php/mtn/wotask/update' : 'index.php/mtn/wotask/add'),
                            params: 
                            {
                                mtn_work_order_id: App.Mtn.Wo.Id,
                                mtn_work_order_task_id: App.Mtn.WoTask.Id
                            },
                            waitMsg: App.Language.General.message_guarding_information,
                            success: function(form, response)
                            {
                                if (response.result.success == 'true') 
                                {
                                    App.Mtn.WoTask.ActiveGuiEditMode(response.result.mtn_work_order_task_id);
                                    App.Mtn.WoTask.Store.load({
                                        callback: function(){
                                            mtn_work_order_task_id = response.result.mtn_work_order_task_id;
                                        }
                                    });
                                    Ext.getCmp('App.Mtn.Wo.WinWo').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWo'));
                                    b.ownerCt.ownerCt.ownerCt.close();
                                } else {
                                    alert(App.Language.Maintenance.error_when_entering_the_job);
                                }
                            },
                            failure: function(form, action)
                            {
                                switch (action.failureType) 
                                {
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
        App.Mtn.addTaskWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.addOtherCostsWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.add_other_costs,
    width: 600,
    padding: 1,
    frame: true,
    viewConfig: 
    {
        forceFit: true
    },
    modal: true,
    id: 'App.Mtn.Wo.WinOtherCosts',
    listeners: 
    {
        'close': function()
        {
            App.Mtn.OtherCostsWo.Store.setBaseParam('mtn_work_order_id', App.Mtn.Wo.Id);
            App.Mtn.OtherCostsWo.Store.load();
        }
    },
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'panel',
            border: false,
            items: 
            [{
                xtype: 'form',
                id: 'App.Mtn.Wo.FormWoOtherCosts',
                labelAlign: 'top',
                border: false,
                frame: true,
                padding: '5 0 5 0',
                items: 
                [{
                    layout: 'column',
                    padding: '5 0 15 0',
                    items: 
                    [{
                        columnWidth: .6,
                        layout: 'form',
                        items: 
                        [{
                            xtype: 'combo',
                            fieldLabel: App.Language.Maintenance.name_costs,
                            anchor: '98%',
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus:true,
                            triggerAction:'all',
                            hiddenName: 'mtn_other_costs_id',
                            store: App.Mtn.OtherCosts.Store,
                            displayField: 'mtn_other_costs_name',
                            valueField: 'mtn_other_costs_id',
                            mode: 'remote',
                            minChars: 0,
                            allowBlank: false,
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
                        }]
                    }, {
                        columnWidth: .4,
                        layout: 'form',
                        items: 
                        [{
                            xtype: 'numberfield',
                            fieldLabel: App.Language.General.value,
                            anchor: '98%',
                            name: 'mtn_work_order_other_costs_costs'
                        }]
                    }]
                }, {
                    items: 
                    [{
                        layout: 'form',
                        width: '100%',
                        defaultType: 'textarea',
                        items: 
                        [{
                            fieldLabel: App.Language.General.comment,
                            name: 'mtn_work_order_other_costs_comment',
                            width: '100%',
                            height: 50
                        }]
                    }]
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
                text: App.Language.General.save,
                id: 'App.Mtn.Wo.FormWoOtherCosts.BtnSaveOtherCosts',
                hidden: (App.Mtn.Wo.CurrentWoData.mtn_work_order_closed == '1' ? true : false),
                handler: function(b)
                {
                    form = Ext.getCmp('App.Mtn.Wo.FormWoOtherCosts').getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            clientValidation: true,
                            url: 'index.php/mtn/woothercosts/add',
                            params: 
                            {
                                mtn_work_order_id: App.Mtn.Wo.Id
                            },
                            waitMsg: App.Language.General.message_guarding_information,
                            success: function(form, response)
                            {
                                App.Mtn.OtherCostsWo.Store.load();
                                Ext.getCmp('App.Mtn.Wo.WinWo').fireEvent('beforerender', Ext.getCmp('App.Mtn.Wo.WinWo'));
                                Ext.getCmp('App.Mtn.Wo.WinOtherCosts').close();
                            },
                            failure: function(form, action)
                            {
                                switch (action.failureType) 
                                {
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
        App.Mtn.addOtherCostsWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.WoTaskComponent.AddComponent = function()
{
    Ext.getCmp('App.Mtn.WoTaskComponent.Component').setValue('');
    Ext.getCmp('App.Mtn.WoTaskComponent.ComponentAmount').setValue('');
    Ext.getCmp('App.Mtn.WoTaskComponent.ComponentPrice').setValue(0);
    Ext.getCmp('App.Mtn.WoTaskComponent.ComponentPrice').setDisabled(true);
    App.Mtn.WoTaskComponent.Store.setBaseParam('mtn_work_order_task_id', App.Mtn.WoTask.Id);
    App.Mtn.WoTaskComponent.Store.load();
}

App.Mtn.Wo.resetWo = function()
{
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
    if (App.Mtn.Wo.EditModeFromGrid === true) 
    {
        App.Mtn.Wo.Store.load();
    }
    
    App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 1);
    App.Mtn.WoTypes.Store.load();
}


App.Mtn.Wo.OpenEditMode = function(mtn_work_order_id)
{
    w = new App.Mtn.generateWorkOrderWindow
    ({
        mtn_work_order_id: mtn_work_order_id
    });
    
    formWo = Ext.getCmp('App.Mtn.Wo.FormWo');
    btnWO = Ext.getCmp('App.Mtn.Wo.FormWo.BtnSaveWo');
    btnWO.handler = function(b)
    {
        form = formWo.getForm();
        if (form.isValid()) 
        {
            form.submit
            ({
                url: 'index.php/mtn/wo/update',
                params: 
                {
                    mtn_work_order_id: mtn_work_order_id
                },
                success: function(fp, o)
                {
                    App.Mtn.Wo.Store.load();
                    Ext.getCmp('App.Mtn.Wo.WinWo').close();
                },
                failure: function(fp, o)
                {
                    alert('Error:\n' + o.result.msg);
                }
            });
        }
    };
    w.show();
}

App.Mtn.WoTask.ActiveGuiEditMode = function(mtn_work_order_task_id)
{
    Ext.getCmp('App.Mtn.Wo.PanelInsumosTask').setVisible(true);
    App.Mtn.WoTask.Id = mtn_work_order_task_id;
    App.Mtn.WoTaskComponent.Store.setBaseParam('mtn_work_order_task_id', App.Mtn.WoTask.Id);
    App.Mtn.WoTaskComponent.Store.load();
}

App.Mtn.WoTask.OpenEditMode = function(record)
{
    w = new App.Mtn.addTaskWindow
    ({
        title: App.Language.Maintenance.edit_task
    });
    formWoTask = Ext.getCmp('App.Mtn.Wo.FormWoTask');
    formWoTask.record = record;
    App.Mtn.WoTask.ActiveGuiEditMode(record.data.mtn_work_order_task_id);
    formWoTask.getForm().loadRecord(record);
    w.show();
}

App.Mtn.OtherCosts.OpenEditMode = function(record)
{
    w = new App.Mtn.addOtherCostsWindow
    ({
        title: App.Language.Maintenance.edit_other_costs
    });
    formWoOtherCosts = Ext.getCmp('App.Mtn.Wo.FormWoOtherCosts');
    formWoOtherCosts.record = record;
    btnSaveOtherCosts = Ext.getCmp('App.Mtn.Wo.FormWoOtherCosts.BtnSaveOtherCosts');
    btnSaveOtherCosts.handler = function()
    {
        form = formWoOtherCosts.getForm();
        if (form.isValid()) 
        {
            form.updateRecord(formWoOtherCosts.record);
            w.close();
        }
    };
    formWoOtherCosts.getForm().loadRecord(record);
    w.show();
}

App.Mtn.Wo.AddAssetWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.General.asset_search,
    resizable: false,
    modal: true,
    border: true,
    width: 750,
    height: 450,
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
                height: 160,
                id: 'AddAssetContract',
                padding: '25 25 25 25',
                border: false,
                items: 
                [{
                    layout: 'column',
                    items: 
                    [{
                        columnWidth: .55,
                        layout: 'form',
                        items: 
                        [{
                            xtype: 'textfield',
                            fieldLabel: App.Language.General.name,
                            anchor: '80%',
                            name: 'asset_name',
                            checked: true
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.internal_number,
                            anchor: '80%',
                            name: 'asset_num_serie_intern',
                            checked: true
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.General.brand,
                            anchor: '80%',
                            store: App.Brand.Store,
                            hiddenName: 'brand_id',
                            triggerAction: 'all',
                            displayField: 'brand_name',
                            valueField: 'brand_id',
                            editable: false,
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
                        }]
                    }, {
                        columnWidth: .45,
                        layout: 'form',
                        items: 
                        [{
                            xtype: 'combo',
                            fieldLabel: App.Language.Asset.asset_type,
                            anchor: '100%',
                            store: App.Asset.Type.Store,
                            hiddenName: 'asset_type_id',
                            triggerAction: 'all',
                            displayField: 'asset_type_name',
                            valueField: 'asset_type_id',
                            editable: false,
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
                            fieldLabel: App.Language.General.condition,
                            anchor: '100%',
                            store: App.Asset.Condition.Store,
                            hiddenName: 'asset_condition_id',
                            triggerAction: 'all',
                            displayField: 'asset_condition_name',
                            valueField: 'asset_condition_id',
                            editable: false,
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
                        }]
                    }]
                }],
                buttons: 
                [{
                    text: App.Language.General.search,
                    handler: function(b)
                    {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Asset.Store.baseParams.node_id;
                        App.Asset.Store.baseParams = form.getSubmitValues();
                        App.Asset.Store.setBaseParam('node_id', node_id);
                        App.Asset.Store.setBaseParam('search_branch', 1);
                        App.Asset.Store.load({params: {node_id: App.Interface.selectedNodeId,start: 0, limit: App.GridLimitAsset}});
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b)
                    {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Asset.Store.baseParams.node_id;
                        form.reset();
                        App.Asset.Store.baseParams = {};
                        App.Asset.Store.setBaseParam('node_id', node_id);
                        App.Asset.Store.load({params: {node_id: App.Interface.selectedNodeId,start: 0, limit: App.GridLimitAsset}});
                    }
                }]
            }, {
                xtype: 'panel',
                border: false,
                padding: 1,
                items: 
                [{
                    xtype: 'grid',
                    height: 215,
                    id: 'App.Mtn.Wo.AddAsset',
                    width: '100%',
                    store: App.Asset.Store,
                    listeners: 
                    {
                        'beforerender': function()
                        {
                            App.Asset.Store.load({params: {node_id: App.Interface.selectedNodeId,start: 0, limit: App.GridLimitAsset}});
                        }
                    },
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(), 
                    {
                        header: App.Language.General.name,
                        sortable: true,
                        dataIndex: 'asset_name'
                    }, {
                        header: App.Language.General.brand,
                        sortable: true,
                        dataIndex: 'brand_name'
                    }, {
                        header: App.Language.General.type,
                        sortable: true,
                        dataIndex: 'asset_type_name'
                    }, {
                        header: App.Language.Asset.internal_number,
                        sortable: true,
                        dataIndex: 'asset_num_serie_intern'
                    }, {
                        header: App.Language.Core.location,
                        sortable: true,
                        dataIndex: 'asset_path',
                        align: 'center'
                    }],
                    sm: new Ext.grid.CheckboxSelectionModel
                    ({
                        singleSelect: true
                    })
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
                text: App.Language.General.select,
                handler: function(b)
                {
                    grid = Ext.getCmp('App.Mtn.Wo.AddAsset');
                    if (grid.getSelectionModel().getCount()) 
                    {
                        asset_id = grid.getSelectionModel().getSelected().id;
                        asset_name = grid.getSelectionModel().getSelected().json.asset_name;
                        asset_path = grid.getSelectionModel().getSelected().json.asset_path;
                        App.Mtn.Wo.Store.setBaseParam('asset_id', asset_id);
                        App.Mtn.Wo.Store.setBaseParam('asset_name', App.Mtn.Wo.assetNameName);
                        b.ownerCt.ownerCt.ownerCt.close();
                        w = new App.Mtn.generateWorkOrderWindow
                        ({
                            height: 250
                        });
                        w.panel.form_wo.url_action.colum_general.form_general.colum_asset.form_asset.displayfield_asset.setValue(asset_name);
                        w.panel.form_wo.url_action.colum_buscador.form_buscador_1.asset_path.setValue(asset_path);
                        Ext.getCmp('App.Mtn.HiddenPanel').setVisible(false);
                        Ext.getCmp('App.Mtn.PanelTotales').setVisible(false);
                        w.show();
                    } else {
                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                    }
                }
            }]
        }];
        App.Mtn.Wo.AddAssetWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.Wo.DateWO = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.change_dates,
    resizable: false,
    modal: true,
    width: 400,
    height: 140,
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
                xtype: 'datefield',
                fieldLabel: App.Language.Maintenance.new_date,
                format: App.General.DefaultSystemDate,
                name: 'mtn_work_order_date',
                anchor: '100%'
            }],
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.save,
                ref: '../saveButton',
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            url: 'index.php/mtn/wo/updateDate',
                            params: 
                            {
                                mtn_work_order_id: aux_mtn_work_order_id
                            },
                            success: function(fp, o)
                            {
                                App.Mtn.Wo.Store.load();
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
        App.Mtn.Wo.DateWO.superclass.initComponent.call(this);
    }
});

/*--Detalles de Asset solo vista---*/
App.Mtn.AssetdetailsAssetWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Asset.asset_details,
    width: 650,
    height: 500,
    layout: 'fit',
    border: true,
    padding: 5,
    modal: true,
    resizable: false,
    listeners: 
    {
        'beforerender': function(w)
        {
            Ext.getCmp('detalle').getForm().load
            ({
                url: 'index.php/asset/asset/getOne',
                params: 
                {
                    asset_id: asset_id
                },
                success: function(fp, o)
                {
                    record = o.result;
                    Ext.getCmp('Brand').setValue(record.data.Brand.brand_name);
                    Ext.getCmp('AssetType').setValue(record.data.AssetType.asset_type_name);
                    Ext.getCmp('AssetStatus').setValue(record.data.AssetStatus.asset_status_name);
                    Ext.getCmp('AssetCondition').setValue(record.data.AssetCondition.asset_condition_name);
                }
            })
        }
    },
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            id: 'detalle',
            ref: 'uno',
            plugins: [new Ext.ux.OOSubmit()],
            items: 
            [{
                xtype: 'tabpanel',
                activeTab: 0,
                height: 420,
                border: false,
                ref: 'tabpanel',
                defaults: 
                {
                    layout: 'form',
                    defaultType: 'textfield',
                    hideMode: 'offsets'
                },
                items: 
                [{
                    ref: 'detail',
                    title: App.Language.General.details,
                    padding: 5,
                    labelWidth: 150,
                    items: 
                    [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.name,
                        name: 'asset_name',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.serial_number,
                        name: 'asset_num_serie',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.internal_number,
                        name: 'asset_num_serie_intern',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.brand,
                        id: 'Brand',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.asset_type,
                        id: 'AssetType',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.state,
                        id: 'AssetStatus',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.condition,
                        id: 'AssetCondition',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.purchase_value,
                        id: 'asset_cost',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.current_cost,
                        id: 'asset_current_cost',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.purchase_date,
                        id: 'asset_purchase_date',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.lifetime,
                        id: 'asset_lifetime',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.description,
                        id: 'asset_description',
                        anchor: '100%',
                        allowBlank: false
                    }]
                }, {
                    title: App.Language.General.other_data,
                    ref: 'otherdata',
                    autoScroll: true,
                    labelWidth: 150,
                    padding: 5,
                    plugins: [new Ext.ux.OOSubmit()]
                }, {
                    xtype: 'grid',
                    title: App.Language.Asset.assurances,
                    store: App.Asset.Insurance.Store,
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    listeners:
                    {
                        'render': function()
                        {
                            this.store.setBaseParam('asset_id', asset_id);
                            this.store.load();
                        }
                    },
                    initComponent: function()
                    {
                        this.columns = 
                        [{
                            header: App.Language.General.provider,
                            sortable: true,
                            dataIndex: 'provider_name'
                        }, {
                            xtype: 'datecolumn',
                            sortable: true,
                            header: App.Language.General.start_date,
                            dataIndex: 'asset_insurance_begin_date',
                            format: App.General.DefaultDateFormat
                        }, {
                            xtype: 'datecolumn',
                            sortable: true,
                            header: App.Language.General.end_date,
                            dataIndex: 'asset_insurance_expiration_date',
                            format: App.General.DefaultDateFormat
                        }, {
                            header: App.Language.General.description,
                            sortable: true,
                            dataIndex: 'asset_insurance_description'
                        }, {
                            header: App.Language.General.state,
                            sortable: true,
                            dataIndex: 'asset_insurance_status_name'
                        }];
                        App.Asset.Insurance.GridPanel.superclass.initComponent.call(this);
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Asset.measurement,
                    store: App.Asset.Measurement.Store,
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    listeners: 
                    {
                        'render': function()
                        {
                            this.store.setBaseParam('asset_id', asset_id);
                            this.store.load();
                        }
                    },
                    initComponent: function()
                    {
                        this.columns = 
                        [{
                            header: App.Language.Asset.measurement,
                            sortable: true,
                            dataIndex: 'asset_measurement_cantity',
                            renderer: function(value, metaData, record)
                            {
                                return value + ' ' + record.data.measure_unit_name;
                            }
                        }, {
                            xtype: 'datecolumn',
                            header: App.Language.General.date,
                            sortable: true,
                            dataIndex: 'asset_measurement_date',
                            format: App.General.DefaultDateFormat
                        }, {
                            header: App.Language.General.comment,
                            sortable: true,
                            dataIndex: 'asset_measurement_comments'
                        }];
                        App.Asset.Measurement.GridPanel.superclass.initComponent.call(this);
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Asset.tracking,
                    store: App.Asset.Log.Store,
                    region: 'center',
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    listeners: 
                    {
                        'beforerender': function()
                        {
                            this.store.setBaseParam('asset_id', asset_id);
                            this.store.load();
                        }
                    },
                    initComponent: function()
                    {
                        this.columns = 
                        [{
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
                            renderer: function(value, metadata, record, rowIndex, colIndex, store)
                            {
                                metadata.attr = 'ext:qtip="' + value + '"';
                                return value;
                            }
                        }, {
                            header: App.Language.General.user,
                            sortable: true,
                            dataIndex: 'User',
                            renderer: function(User)
                            {
                                return User.user_name;
                            }
                        }];
                        App.Asset.Document.GridPanel.superclass.initComponent.call(this);
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.General.documents,
                    store: App.Asset.Document.Store,
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: 
                    {
                        forceFit: true
                    },
                    listeners: 
                    {
                        'render': function()
                        {
                            this.store.setBaseParam('asset_id', asset_id);
                            this.store.load();
                        }
                    },
                    initComponent: function()
                    {
                        this.columns = 
                        [{
                            header: App.Language.General.file_name,
                            dataIndex: 'asset_document_filename',
                            sortable: true,
                            renderer: function(val, metadata, record)
                            {
                                return "<a href='index.php/asset/assetdocument/download/" + record.data.asset_document_id + "'>" + val + "</a>";
                            }
                        }, {
                            header: App.Language.General.description,
                            sortable: true,
                            dataIndex: 'asset_document_description'
                        }, {
                            header: App.Language.General.uploaded_by,
                            sortable: true,
                            dataIndex: 'user_name'
                        }];
                        App.Asset.Document.GridPanel.superclass.initComponent.call(this);
                    }
                }]
            }],
            buttons: 
            [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }]
        }];
        App.Mtn.AssetdetailsAssetWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.ChangeStateWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Maintenance.change_of_status,
    resizable: false,
    modal: true,
    width: 450,
    height: 230,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            frame: false,
            labelWidth: 150,
            padding: 5,
            items: 
            [{
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
            buttons: 
            [{
                text: App.Language.General.close,
                handler: function(b)
                {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.save,
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) 
                    {
                        form.submit
                        ({
                            url: 'index.php/mtn/wo/updateState',
                            params: 
                            {
                                mtn_work_order_id: App.Mtn.Wo.CurrentWoData.mtn_work_order_id
                            },
                            success: function(fp, o)
                            {
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.getCmp('App.Mtn.Wo.WinWo').close();
                                App.Mtn.Wo.OpenEditMode(App.Mtn.Wo.CurrentWoData.mtn_work_order_id);
                                
                            },
                            failure: function(fp, o){
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Mtn.ChangeStateWindow.superclass.initComponent.call(this);
    }
});
