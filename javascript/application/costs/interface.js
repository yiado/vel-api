App.Costs.selectedCostsId = null;

App.Costs.allowRootGui = true;
App.Interface.addToModuleMenu('costs', App.ModuleActions[10001]);

App.Costs.Principal = Ext.extend(Ext.Panel, 
{
    title: App.Language.Costs.costs,
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: 
    [{
        text: App.Language.General.add,
        iconCls: 'add_icon',
        id: 'ModuleAction_10000',
        hidden: (App.Security.Actions[10000] === undefined ? true: false),
        handler: function()
        {
            w = new App.Costs.addCostsWindow();
            w.form.saveButton.handler = function(b)
            {
                form = b.ownerCt.ownerCt.getForm();
                if (form.isValid()) 
                {
                    form.submit
                    ({
                        url: 'index.php/costs/costs/add',
                        params: 
                        {
                            node_id: App.Interface.selectedNodeId
                        },
                        success: function(fp, o)
                        {
                            App.Costs.Store.load
                            ({
                                callback: function()
                                {
                                    App.Costs.Store.load();
                                }
                            });
                            b.ownerCt.ownerCt.ownerCt.hide();
                        },
                        failure: function(fp, o)
                        {
                            alert('Error:\n' + o.result.msg);
                        }
                    });
                }
            }
            w.show();
        }
    }, {
        xtype: 'spacer',
        width: 10
    }, {
        text: App.Language.General.ddelete,
        iconCls: 'delete_icon',
        id: 'ModuleAction_10003',
        hidden: (App.Security.Actions[10003] === undefined ? true: false),
        handler: function(b)
        {
            grid = b.ownerCt.ownerCt.costsGrid;
            if (grid.getSelectionModel().getCount()) 
            {
                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b)
                {
                    if (b == 'yes') 
                    {
                        grid.getSelectionModel().each(function(record)
                        {
                            App.Costs.Store.remove(record);
                            App.Costs.Store.load();
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
        text: App.Language.General.eexport,
        iconCls: 'export_icon',
        id: 'ModuleAction_10004',
        hidden: (App.Security.Actions[10004] === undefined ? true: false),
        handler: function()
        {
            w = new App.Costs.exportListWindow();
            w.show();
        }
    }, {
        xtype: 'tbseparator',
        width: 10
    }, {
        text: App.Language.General.search,
        iconCls: 'search_icon_16',
        enableToggle: true,
        handler: function(b)
        {
            if (b.ownerCt.ownerCt.form.isVisible()) 
            {
                b.ownerCt.ownerCt.form.hide();
            } else {
                b.ownerCt.ownerCt.form.show();
            }
            b.ownerCt.ownerCt.doLayout();
        }
    }],
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            region: 'north',
            id: 'App.Costs.FormCentral',
            plugins: [new Ext.ux.OOSubmit()],
            title: App.Language.General.searching,
            frame: true,
            ref: 'form',
            hidden: true,
            height: 190,
            margins: '5 5 0 5',
            padding: '5 5 5 5',
            border: true,
            fbar: 
            [{
                text: App.Language.General.search,
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    node_id = App.Costs.Store.baseParams.node_id;
                    App.Costs.Store.baseParams = form.getSubmitValues();
                    App.Costs.Store.setBaseParam('node_id', node_id);
                    App.Costs.Store.load();
                }
            }, {
                text: App.Language.General.clean,
                handler: function(b)
                {
                    form = b.ownerCt.ownerCt.getForm();
                    node_id = App.Costs.Store.baseParams.node_id;
                    form.reset();
                    App.Costs.Store.setBaseParam([]);
                    App.Costs.Store.setBaseParam('node_id', node_id);
                    App.Costs.Store.load();
                }
            }],
            items: 
            [{
                layout: 'column',
                /*-------------COMBOS-------------*/
                //id: 'column_form_column_start_date',
                items: 
                [{
                    columnWidth: .5,
                    layout: 'form',
                    labelWidth: 150,
                    items: 
                    [{
                        xtype: 'combo',
                        fieldLabel: App.Language.Costs.concept,
                        triggerAction: 'all',
                        anchor: '95%',
                        store: App.Costs.CostsType.Store,
                        id: 'App.Costs.CostsType.formId',
                        hiddenName: 'costs_type_id',
                        displayField: 'costs_type_name',
                        valueField: 'costs_type_id',
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
                        fieldLabel: App.Language.Costs.month,
                        triggerAction: 'all',
                        anchor: '95%',
                        store: App.Costs.CostsMonth.Store,
                        id: 'App.Costs.CostsMonth.formId',
                        hiddenName: 'costs_month_id',
                        displayField: 'costs_month_name',
                        valueField: 'costs_month_id',
                        editable: false,
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
                        xtype: 'numberfield',
                        fieldLabel: App.Language.Costs.year,
                        minLength : 4,
                        id: 'App.Costs.Anio.formId',
                        maxValue : 3000,
                        name: 'costs_anio',
                        anchor: '40%'
                    }, {
                        xtype: 'checkbox',
                        hideLabel: true,  
                        id: 'App.Costs.Search.formId',
                        boxLabel: App.Language.General.perform_internal_search,
                        name: 'search_branch',
                        inputValue: 1
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    labelWidth: 150,
                    items: 
                    [{
                        xtype: 'textfield',
                        id: 'App.Costs.Ticket.formId',
                        fieldLabel: App.Language.Costs.ballot_number_or_invoice,
                        name: 'costs_number_ticket',
                        anchor: '95%'
                    }, {
                        xtype: 'numberfield',
                        id: 'App.Costs.Value.formId',
                        fieldLabel: App.Language.General.value,
                        name: 'costs_value',
                        anchor: '95%'
                    },{
                        xtype: 'textfield',
                        id: 'App.Costs.Detail.formId',
                        fieldLabel: App.Language.General.details,
                        name: 'costs_detail',
                        anchor: '95%'
                    }]
                }]
            }]
        }, {
            xtype: 'grid',
            id: 'App.Costs.Grid',
            ref: 'costsGrid',
            margins: '5 5 5 5',
            plugins: [new Ext.ux.OOSubmit()],
            region: 'center',
            border: true,
            loadMask: true,
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex){
                    record = grid.getStore().getAt(rowIndex);
                    App.Costs.costsOpenEditMode(record);
                },
                'beforerender': function(){
                    App.Costs.Store.load();
                }
            },
            viewConfig: 
            {
                forceFit: true
            },
            store: App.Costs.Store,
            columns: [new Ext.grid.CheckboxSelectionModel(), 
            {
                dataIndex: 'costs_type_name',
                header: App.Language.Costs.concept,
                sortable: true,
                width: 100
               
            }, {
                dataIndex: 'costs_month_name',                
                header: App.Language.Costs.month,
                sortable: true,
                width: 100
            }, {
                dataIndex: 'costs_anio',
                header: App.Language.Costs.year,
                sortable: true,
                width: 100
            }, {
                dataIndex: 'costs_number_ticket',
                header: App.Language.Costs.ballot_number_or_invoice,
                sortable: true,
                width: 100
            }, {
                dataIndex: 'costs_value',
                header: App.Language.General.value,
                sortable: true,
                width: 100
            }, {
                dataIndex: 'costs_detail',
                header: App.Language.General.details,
                sortable: true,
                width: 100
            }],
            
            sm: new Ext.grid.CheckboxSelectionModel()
        }], 
        App.Costs.Principal.superclass.initComponent.call(this);
    }
});

App.Costs.Principal.listener = function(node)
{
    if (node && node.id) 
    {
        App.Costs.selectedCostsId = App.Costs.Store.setBaseParam('node_id', node.id);
        App.Costs.Store.load();
    }
};

App.Costs.addCostsWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Costs.add_cost,
    width: 420,
    height: 280,
    layout: 'fit',
    padding: 1,
    modal: true,
    maximizable : true,
    resizable: false,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            labelWidth: 150,
            plugins: [new Ext.ux.OOSubmit()],
            items: 
            [{
                xtype: 'combo',
                fieldLabel: App.Language.Costs.concept,
                anchor: '100%',
                store: App.Costs.CostsType.Store,
                hiddenName: 'costs_type_id',
                triggerAction:'all',
                displayField: 'costs_type_name',
                valueField: 'costs_type_id',
                editable: true,
                typeAhead: true,
                selectOnFocus:true,
                forceSelection:true,
                mode: 'remote',
                minChars: 0,
                allowBlank: false,
                listeners: 
                {
                    'afterrender': function (cb)
                    {
                        cb.__value = cb.value;
                        cb.setValue('');
                        cb.getStore().load
                        ({
                            callback: function () 
                            {
                                cb.setValue(cb.__value);
                            }
                        });
                    }
                }
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.month,
                anchor: '100%',
                store: App.Costs.CostsMonth.Store,
                hiddenName: 'costs_month_id',
                triggerAction:'all',
                displayField: 'costs_month_name',
                valueField: 'costs_month_id',
                editable: false,
                typeAhead: true,
                selectOnFocus:true,
                forceSelection:true,
                mode: 'remote',
                minChars: 0,
                allowBlank: false,
                listeners: 
                {
                    'afterrender': function (cb)
                    {
                        cb.__value = cb.value;
                        cb.setValue('');
                        cb.getStore().load
                        ({
                            callback: function () 
                            {
                                cb.setValue(cb.__value);
                            }
                        });
                    }
                }
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.General.year,
                name: 'costs_anio',
                allowBlank: false,
                value: new Date().format('Y'),
                minLength : 4,
                maxValue : 3000,
                anchor: '50%'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Costs.ballot_number_or_invoice,
                allowBlank: false,
                name: 'costs_number_ticket',
                anchor: '100%'
            }, {
                xtype: 'numberfield',
                name: 'costs_value',
                allowBlank: false,
                fieldLabel: App.Language.General.value,
                sortable: true,
                anchor: '100%'
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.details,
                name: 'costs_detail',
                anchor: '100%'
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
                text: App.Language.General.add,
                ref: '../saveButton'
            }]
        }];
        App.Costs.addCostsWindow.superclass.initComponent.call(this);
    }
});

App.Costs.costsOpenEditMode = function(record)
{
    
    w = new App.Costs.addCostsWindow
    ({
        title: App.Language.Costs.edit_cost
    });
    if (App.Security.Actions[10002] === undefined) 
    {
        w.form.saveButton.hide();
    }
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function()
    {
        form = w.form.getForm();
        if (form.isValid()) 
        {        
            form.updateRecord(w.form.record);
            App.Costs.Store.load();
            w.close();
            
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}


App.Costs.exportListWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Costs.export_costs,
    width: 400,
    height: 150,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            labelWidth: 130,
            padding: 5,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                id: 'App.Costs.form.file_name',
                anchor: '100%',
                name: 'file_name',
                maskRe: /^[a-zA-Z0-9_]/,
                regex: /^[a-zA-Z0-9_]/,
                allowBlank: false
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
                text: App.Language.General.eexport,
                handler: function(b)
                {
                    
                    Ext.Ajax.request
                    ({
                        waitMsg: App.Language.General.message_generating_file,
                        url: 'index.php/costs/costs/export',
                        method: 'POST',
                        params: 
                        {
                            node_id: App.Costs.Store.baseParams.node_id,
                            file_name: Ext.getCmp('App.Costs.form.file_name').getValue(),
                            costs_type_id: Ext.getCmp('App.Costs.CostsType.formId').getValue(),
                            costs_month_id: Ext.getCmp('App.Costs.CostsMonth.formId').getValue(),
                            costs_anio: Ext.getCmp('App.Costs.Anio.formId').getValue(),
                            costs_number_ticket: Ext.getCmp('App.Costs.Ticket.formId').getValue(),
                            costs_value: Ext.getCmp('App.Costs.Value.formId').getValue(),
                            costs_detail: Ext.getCmp('App.Costs.Detail.formId').getValue(),
                            search_branch: Ext.getCmp('App.Costs.Search.formId').getValue() 
                      
                        },
                        success: function(response)
                        {
                            response = Ext.decode(response.responseText);
                            document.location = response.file;
                            b.ownerCt.ownerCt.ownerCt.close();
                        },
                        failure: function(response)
                        {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                        }
                    });
                }
            }]
        }];
        App.Costs.exportListWindow.superclass.initComponent.call(this);
    }
});