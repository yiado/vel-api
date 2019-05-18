App.Maintainers.addToModuleMenu('request', 
{
//    xtype: 'button',
//   
    iconCls: 'request_icon_32',
     text: App.Language.Request.requests,
//    scale: 'large',
//    iconAlign: 'top',
    module: 'Request'
});

App.Maintainers.Request.Principal = Ext.extend(Ext.TabPanel, 
{
    activeTab: 0,
    border: false,
    title: App.Language.General.problems,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'grid',
            title: App.Language.General.problems,
            store: App.Request.Problem.Store,
            height: '100%',
            viewConfig: 
            {
                forceFit: true
            },
            listeners: 
            {
                'rowdblclick': function(grid, rowIndex)
                {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Request.OpenEditMode(record);
                },
                'beforerender': function()
                {
                    App.Request.Problem.Store.load();
                }
            },
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
                        w = new App.Maintainers.Request.addRequestDocumentWindow();
                        w.show();
                    }
                }, {
                    xtype: 'tbseparator'
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b)
                    {
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b){
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Request.Problem.Store.remove(record);
                                        App.Request.Problem.Store.load();
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
                dataIndex: 'request_problem_name',
                header: App.Language.General.name,
                sortable: true
            }],
            sm: new Ext.grid.CheckboxSelectionModel()
        }];
        App.Maintainers.Request.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.Request.OpenEditMode = function(record)
{
    w = new App.Maintainers.Request.addRequestDocumentWindow
    ({
        title: App.Language.Request.request_problem
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
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.Request.addRequestDocumentWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.Request.request_problem,
    resizable: false,
    modal: true,
    width: 450,
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
                fieldLabel: App.Language.General.name,
                name: 'request_problem_name',
                anchor: '100%',
                minChars: 0,
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
                            url: 'index.php/request/problem/add',
                            success: function(fp, o)
                            {
                                App.Request.Problem.Store.load();
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
        App.Maintainers.Request.addRequestDocumentWindow.superclass.initComponent.call(this);
    }
});
