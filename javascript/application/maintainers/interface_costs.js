
App.Maintainers.addToModuleMenu('costs', 
{
    xtype: 'button',
    text: App.Language.Costs.costs,
    iconCls: 'costs_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Costs'
});

App.Maintainers.Costs.Principal = Ext.extend(Ext.TabPanel, 
{
    activeTab: 0,
    border: false,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'grid',
            title: App.Language.Costs.costs,
            id: 'App.Maintainers.CostsGrid',
            store: App.Costs.CostsType.Store,
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
                    App.Maintainers.Costs.OpenEditMode(record);
                },
                'beforerender': function()
                {
                    App.Costs.CostsType.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                dataIndex: 'costs_type_name',
                header: App.Language.Maintenance.name_costs,
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
                        w = new App.Maintainers.addCostsWindow();
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
                        grid = Ext.getCmp('App.Maintainers.CostsGrid');
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.elimination_confirmation_message_costs, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Costs.CostsType.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            }
        }];
        App.Maintainers.Costs.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.addCostsWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Costs.add_name_cost,
    resizable: false,
    modal: true,
    width: 380,
    height: 150,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            labelWidth: 150,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.Maintenance.name_costs,
                name: 'costs_type_name',
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
                            url: 'index.php/costs/coststype/add',
                            success: function(fp, o)
                            {
                                App.Costs.CostsType.Store.load();
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
        App.Maintainers.addCostsWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Costs.OpenEditMode = function(record)
{
    w = new App.Maintainers.addCostsWindow
    ({
        title: App.Language.Costs.edit_name_costs
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
            App.Costs.CostsType.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}
