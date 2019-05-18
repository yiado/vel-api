App.Asset.Insurance.GridPanel = Ext.extend(Ext.grid.GridPanel, 
    {
        title: App.Language.Asset.assurances,
        id: 'App.Asset.Insurance.Grid',
        store: App.Asset.Insurance.Store,
        loadMask: true,
        listeners: 
        {
            'render' : function () 
            {
                this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                this.store.load();
            },
            'rowdblclick' : function ( grid, rowIndex ) 
            {
                record = grid.getStore().getAt(rowIndex);
                App.Asset.Insurance.AssetInsuranceEditMode(record);
                App.Core.Provider.Store.reload
                ({
                    callback: function () 
                    {
                        w.show();
                    }
                });
                
            }
        },
        viewConfig: 
        {
            forceFit: true
        },
        tbar: 
        {
            xtype: 'toolbar',
            items: 
            [{
                text: App.Language.General.add,
                iconCls: 'add_icon',
                handler: function (b) 
                {
                    w = new App.Asset.Insurance.formWindow
                    ({
                        title: App.Language.Asset.add_assurances_action
                    });
                    w.form.saveButton.setText(App.Language.General.add);
                    w.form.saveButton.handler = function (bb) 
                    {
                        form = w.form.getForm();
                        if (form.isValid()) 
                        {
                            var u = new App.Asset.Insurance.Store.recordType(w.form.getForm().getSubmitValues());
                            u.set('asset_id', App.Asset.selectedAssetId);
                            App.Asset.Insurance.Store.insert(0, u);
                            bb.ownerCt.ownerCt.ownerCt.close();
                            Ext.getCmp('App.Asset.Insurance.Grid').fireEvent('render', Ext.getCmp('App.Asset.Insurance.Grid'));
                            
                        } else {
                            Ext.Msg.alert(App.Language.General.error, App.Language.General.message_required_fields);
                        }
                    };
                    w.show();
                }
            }, {
                xtype: 'spacer',
                width: 5
            }, {
                text: App.Language.General.ddelete,
                iconCls: 'delete_icon',
                handler: function (b) 
                {
                    grid = b.ownerCt.ownerCt;
                    if (grid.getSelectionModel().getCount()) 
                    {
                        Ext.MessageBox.confirm( App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete,
                            function (b) 
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function (record) 
                                    {
                                        Ext.Ajax.request
                                        ({
                                            url: 'index.php/asset/assetinsurance/delete',
                                            params: {
                                                asset_insurance_id: record.data.asset_insurance_id
                                            },
                                            success: function(response)
                                            {
                                                App.Asset.Insurance.Store.load();
                                                Ext.getCmp('App.Asset.Insurance.Grid').fireEvent('render', Ext.getCmp('App.Asset.Insurance.Grid'));
                                            }
                                        });
										
										
                                    });
                                }
                            });
                    }else{
                        Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                    }
                }
            }]
        },
        initComponent: function() 
        {
            this.selModel = new Ext.grid.CheckboxSelectionModel
            ({
                checkOnly: false
            });
            this.columns = 
            [
            this.selModel,
            {
                header:  App.Language.General.provider,
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
            }
            ];
            App.Asset.Insurance.GridPanel.superclass.initComponent.call(this);
        }
    });
	
App.Asset.Insurance.formWindow = Ext.extend(Ext.Window, 
{
    width: 380,
    height: 300,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() 
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            plugins: [new Ext.ux.OOSubmit()],
            items: 
            [{
                xtype: 'hidden',
                name: 'asset_insurance_id'
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.provider,
                anchor: '100%',
                triggerAction: 'all',
                hiddenName: 'provider_id',
                store: App.Core.Provider.Store,
                displayField: 'provider_name', 
                valueField: 'provider_id',
                editable: true,
                typeAhead: true,
                selectOnFocus:true,
                forceSelection:true, 
                mode: 'remote',  
                minChars : 0,
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
                xtype: 'datefield',
                ref: 'asset_insurance_begin_date',
                fieldLabel: App.Language.General.start_date,
                name: 'asset_insurance_begin_date',
                anchor: '100%',
                listeners: 
                {
                    'select': function (fd, date) 
                    {
                        fd.ownerCt.asset_insurance_expiration_date.setMinValue(date);
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'asset_insurance_expiration_date',
                fieldLabel: App.Language.General.end_date,
                name: 'asset_insurance_expiration_date',
                anchor: '100%',
                listeners: 
                {
                    'select': function (fd, date) 
                    {
                        fd.ownerCt.asset_insurance_begin_date.setMaxValue(date);
                    }
                }
            }, {
                xtype: 'textarea',
                anchor: '100%',
                fieldLabel: App.Language.General.description,
                name: 'asset_insurance_description'
            }],
            buttons: 
            [{
                xtype: 'button',
                text: App.Language.General.close,
                handler : function (b) 
                {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                xtype: 'button',
                ref: '../saveButton'
            }]
        }];
        App.Asset.Insurance.formWindow.superclass.initComponent.call(this);
    }
});



App.Asset.Insurance.AssetInsuranceEditMode = function(record)
{
    w = new App.Asset.Insurance.formWindow
    ({
        title: App.Language.Asset.edit_assurances_title
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
            App.Asset.Insurance.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
   
    w.show();
}