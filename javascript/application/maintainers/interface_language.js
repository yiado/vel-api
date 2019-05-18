App.Maintainers.addToModuleMenu('language', 
{
    xtype: 'button',
    text: App.Language.Core.language,
    iconCls: 'language_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Language'
});

App.Maintainers.Language.Principal = Ext.extend(Ext.TabPanel, 
{
    activeTab: 0,
    border: false,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'grid',
            title: App.Language.General.languages,
            id: 'App.Maintainers.LanguageGrid',
            store: App.Core.Languages.Store,
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
                    App.Maintainers.Language.OpenEditMode(record);
                },
                'beforerender': function()
                {
                    App.Core.Languages.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(), 
            {
                xtype: 'gridcolumn',
                dataIndex: 'language_name',
                header: App.Language.General.name,
                sortable: true,
                width: 100
            }, {
                xtype: 'gridcolumn',
                dataIndex: 'language_is_default',
                header: App.Language.General.in_use_system,
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
                        w = new App.Maintainers.addLanguageWindow();
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
                        grid = Ext.getCmp('App.Maintainers.LanguageGrid');
                        if (grid.getSelectionModel().getCount()) 
                        {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b)
                            {
                                if (b == 'yes') 
                                {
                                    grid.getSelectionModel().each(function(record)
                                    {
                                        App.Core.Languages.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            }
        }, {
            xtype: 'form',
            layout: 'fit',
            title: App.Language.General.settings,
            border: false,
            items: 
            [{
                xtype: 'editorgrid',
                height: 200,
                layout: 'fit',
                store: App.Core.LanguagesTag.Store,
                border: false,
                tbar: 
                [{
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'label',
                    text: App.Language.General.languages
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'combo',
                    ref: '../comboLanguages',
                    triggerAction: 'all',
                    hiddenName: 'language_id',
                    store: App.Core.Languages.Store,
                    displayField: 'language_name',
                    valueField: 'language_id',
                    mode: 'remote',
                    editable: false,
                    minChars: 0,
                    allowBlank: false,
                    listeners: 
                    {
                        'select': function(cb, record)
                        {
                            var language_id = cb.getValue();
                            var module_id = cb.ownerCt.ownerCt.comboModules.getValue();
                            App.Core.LanguagesTag.Store.setBaseParam('module_id', module_id);
                            App.Core.LanguagesTag.Store.setBaseParam('language_id', language_id);
                            App.Core.LanguagesTag.Store.load();
                        }
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'label',
                    text: App.Language.Core.module
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'combo',
                    ref: '../comboModules',
                    triggerAction: 'all',
                    hiddenName: 'module_id',
                    store: App.Core.Modules.Store,
                    displayField: 'module_name',
                    valueField: 'module_id',
                    mode: 'remote',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus:true,
                    minChars: 0,
                    allowBlank: false,
                    listeners: 
                    {
                        'select': function(cb, record)
                        {
                            var language_id = cb.ownerCt.ownerCt.comboLanguages.getValue()
                            var module_id = cb.getValue();
                            App.Core.LanguagesTag.Store.setBaseParam('module_id', module_id);
                            App.Core.LanguagesTag.Store.setBaseParam('language_id', language_id);
                            App.Core.LanguagesTag.Store.load();
                        }
                    }
                }],
                loader: 
                {
                    dataUrl: 'index.php/core/permissions/expand',
                    preloadChildren: true,
                    autoLoad: false
                },
                viewConfig: 
                {
                    forceFit: true
                },
                columns: 
                [{
                    xtype: 'gridcolumn',
                    dataIndex: 'language_tag_value',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100,
                    editor: new Ext.form.TextField({})
                }]
            }]
        
        }];
        App.Maintainers.Language.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.addLanguageWindow = Ext.extend(Ext.Window, 
{
    title: App.Language.General.add_language,
    resizable: false,
    modal: true,
    width: 380,
    height: 200,
    layout: 'fit',
    padding: 1,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            items: 
            [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'language_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'combo',
                ref: 'languageRef',
                triggerAction: 'all',
                fieldLabel: App.Language.General.language_reference,
                hiddenName: 'language_id',
                store: App.Core.Languages.Store,
                displayField: 'language_name',
                valueField: 'language_id',
                mode: 'remote',
                editable: false,
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
            }, {
                xtype: 'checkbox',
                ref: 'chkSetDefaultLanguage',
                fieldLabel: App.Language.General.set_current_language,
                anchor: '90%',
                name: 'language_default',
                inputValue: 1
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
                            url: 'index.php/core/language/add',
                            success: function(fp, o)
                            {
                                App.Core.Languages.Store.load();
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
        App.Maintainers.addLanguageWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Language.OpenEditMode = function(record)
{
    w = new App.Maintainers.addLanguageWindow
    ({
        title: App.Language.General.edit_language
    });
    w.form.saveButton.setText(App.Language.General.edit);
    if (record.data.language_default == 1) 
    {
        w.form.chkSetDefaultLanguage.hideLabel = true;
        w.form.chkSetDefaultLanguage.hide();
    }
    w.form.languageRef.hideLabel = true;
    w.form.languageRef.hide();
    w.form.record = record;
    w.form.saveButton.handler = function()
    {
        form = w.form.getForm();
        if (form.isValid()) 
        {
            record = w.form.record;
            form.updateRecord(record);
            w.close();
            App.Core.Languages.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}
