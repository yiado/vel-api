Ext.onReady(function() 
{
    LoginWindow = Ext.extend(Ext.Window, 
    {
        width: 300,
        height: 300,
        bodyBorder: false,
        modal: false,
        shadowOffset: 6,
        resizable: false,
        frame: true,
        layout: 'fit',
        padding: 1,
        draggable: false,
        closable: false,
        initComponent: function() 
        {
            this.items = 
            [{
                xtype: 'form',
                ref: 'form',
                padding: 20,
                labelAlign: 'top',
                layoutConfig: 
                {
                    labelSeparator: ''
                },
                items: 
                [{
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.title_login,
                    labelStyle: 'font-weight:bold;font-size: 20',
                    style: 
                    {
                        marginBottom: '10px'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: App.Language.General.user,
                    name: 'username',
                    height: 30,
                    anchor: '100%',
                    allowBlank: false,
                    style: 
                    {
                        marginBottom: '10px'
                    },
                    enableKeyEvents: true,
                    listeners:
                    {
                        'specialkey': function ( tf, e ) 
                        {
                            if (e.getKey() == e.ENTER) 
                            {
                                tf.ownerCt.loginButton.handler(tf.ownerCt.loginButton);
                            }
                        }
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: App.Language.Core.password,
                    name: 'password',
                    inputType: 'password',
                    anchor: '100%',
                    height: 30,
                    allowBlank: false,
                    enableKeyEvents: true,
                    listeners:{
                        'specialkey': function ( tf, e ) 
                        {
                            if (e.getKey() == e.ENTER) 
                            {
                                tf.ownerCt.loginButton.handler(tf.ownerCt.loginButton);
                            }
                        }
                    }
                }, {
                    xtype: 'displayfield',
                    fieldLabel: '',
                    ref: 'error',
                    style: 'color: red;'
                }],
                buttons:
                [{
                    text: App.Language.General.button_login,
                    ref: '../loginButton',
                    handler : function ( w ) 
                    {
                        f = w.ownerCt.ownerCt.getForm();
                        if (f.isValid()) 
                        {
                            f.submit
                            ({
                                url: 'index.php/core/auth/autentication',
                                params: {
                                    action: 'validar'
                                },
                                waitMsg: App.Language.General.validating_user,
                                success: function(form, response)
                                {
                                    console.log('response: ', response );
                                    if (response.result.user_type == 'N') 
                                    {
                                        console.log('Search permisos: ');
                                        var msg = Ext.MessageBox.wait(App.Language.General.please_wait, App.Language.General.loading_permissions);
                                        Ext.Ajax.request
                                        ({
                                            timeout: 10000000000,
                                            url: 'index.php/core/auth/permissions',
                                            success : function ( resp ) 
                                            {
                                                new Ext.LoadMask(Ext.getBody(), {
                                                    msg: App.Language.Core.loading
                                                    }).show();
                                                    
                                                document.location = response.result.base_url;
                                            },
                                            failure: function () 
                                            {
                                                w.ownerCt.ownerCt.get(3).setValue(App.Language.General.problem_loading_permits);
                                            },
                                            callback: function () 
                                            {
                                                msg.hide();
                                            }
                                        });
                                    } else {
                                     
                                        new Ext.LoadMask(Ext.getBody(), {
                                            msg: App.Language.Core.loading
                                            }).show();
                                        document.location = response.result.base_url;
                                    }
                                },
                                failure: function(form, response)
                                {
                                    w.ownerCt.ownerCt.get(3).setValue(response.result.message);
                                }
                            });
                        }
                    }
                }]
            }];
            LoginWindow.superclass.initComponent.call(this);
        }
    });
    var win = new LoginWindow().show();
    w = Ext.lib.Dom.getViewWidth(true);
    h = Ext.lib.Dom.getViewHeight(true);

    var divTag = document.createElement("div");
    divTag.setAttribute("align","center");
    divTag.style.position = 'absolute';
    divTag.style.left = ((w - 190) / 2);
    divTag.style.top = ((h - 86) / 2)  + 210;
    divTag.innerHTML = '<img src="style/default/images/login_logo.png">';
    document.body.appendChild(divTag);
});
