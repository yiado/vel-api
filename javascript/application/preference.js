App.Preferences.UsersWindow = Ext.extend(Ext.Window, {
    title: 'Preferencias',
    resizable: false,
    modal: true,
    width: (screen.width < 430) ? screen.width - 50 : 430,
    border: true,
    padding: 1,
    height: 350,
    layout: 'fit',
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            layout: 'fit',
            border: false,
            fieldDefaults: {
                labelWidth: 90,
                msgTarget: 'side'
            },
            items: {
                xtype: 'tabpanel',
                activeTab: 0,
                ref: '../tabpanel',
                border: true,
                height: 238,
                defaults: {
                    layout: 'form',
                    defaultType: 'textfield',
                    bodyStyle: 'padding:10px 20px 10',
                    labelWidth: 100,
                    hideMode: 'offsets'
                },
                items: [{
                    title: App.Language.General.personal_data,
                    layout: 'form',
                    items: [{
                        fieldLabel: App.Language.General.name,
                        name: 'user_name',
                        anchor: '100%'
                    }, {
                        fieldLabel: App.Language.Core.email,
                        name: 'user_email',
                        vtype: 'email',
                        anchor: '100%'
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.General.change_language,
                        anchor: '100%',
                        store: App.Core.Languages.Store,
                        hiddenName: 'language_id',
                        displayField: 'language_name',
                        valueField: 'language_id',
                        triggerAction: 'all',
                        mode: 'remote',
                        minChars: 0,
                        allowBlank: false,
                        editable: false
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.Core.module,
                        anchor: '100%',
                        store: App.Core.User.ModuleStore,
                        hiddenName: 'user_default_module',
                        displayField: 'module_name',
                        valueField: 'module_id',
                        triggerAction: 'all',
                        mode: 'remote',
                        minChars: 0,
                        allowBlank: true,
                        editable: false
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.General.vview,
                        anchor: '100%',
                        store: new Ext.data.ArrayStore({
                            fields: ['user_preference_id', 'interface'],
                            data: [
                                ['1', App.Language.General.ddefault],
                                ['2', App.Language.General.architecture]
                            ]
                        }),
                        displayField: 'interface',
                        typeAhead: true,
                        hiddenName: 'user_preference',
                        valueField: 'user_preference_id',
                        mode: 'local',
                        forceSelection: true,
                        triggerAction: 'all',
                        selectOnFocus: true,
                        editable: false
                    }, {
                        xtype: 'spacer',
                        height: 10
                    }, {
                        xtype: 'displayfield',
                        name: 'user_string_groups',
                        fieldLabel: App.Language.Core.groups,
                        anchor: '100%'
                    }]
                }, {
                    title: App.Language.Core.password,
                    labelWidth: 150,
                    items: [{
                        fieldLabel: App.Language.Core.current_password,
                        inputType: 'password',
                        name: 'user_password',
                        anchor: '100%'
                    }, {
                        xtype: 'spacer',
                        height: 20
                    }, {
                        fieldLabel: App.Language.Core.new_password,
                        name: 'new_password',
                        inputType: 'password',
                        maxLength: 12,
                        minLength: 8,
                        id: 'pass_users',
                        fieldDefaults: {
                            labelWidth: 125,
                            msgTarget: 'side',
                            autoFitErrors: false
                        },
                        anchor: '100%',
                        maskRe: /([a-zA-Z0-9\s]+)$/
                    }, {
                        fieldLabel: App.Language.Core.confirm_password,
                        inputType: 'password',
                        initialPassField: 'pass_users',
                        name: 'new_password_cfrm',
                        maxLength: 12,
                        minLength: 8,
                        anchor: '100%',
                        maskRe: /([a-zA-Z0-9\s]+)$/
                    }, {
                        xtype: 'spacer',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Core.password_requirements,
                        anchor: '100%'
                    }, {
                        xtype: 'spacer',
                        height: 10
                    }, {
                        xtype: 'label',
                        text: App.Language.Core.example_password,
                        maxLength: 255,
                        anchor: '100%'
                    }]
                }]
            },
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.save,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/core/user/preferences',
                            success: function(fp, o) {
                                if (o.result.preference == true) {
                                    document.location = App.BaseUrl;
                                } else {
                                    Ext.FlashMessage.alert(o.result.msg);
                                }

                                b.ownerCt.ownerCt.ownerCt.close();
                            },
                            failure: function(fp, o) {
                                Ext.FlashMessage.alert(o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Preferences.UsersWindow.superclass.initComponent.call(this);
    }
});