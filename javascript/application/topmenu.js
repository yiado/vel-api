App.General.TopMenu =
        [
//            {
//                xtype: 'button',
//                iconCls: 'workspace_icon',
//                text: "Area de Trabajo",
//                handler: function ()
//                {
//                    document.location = App.BaseUrl;
//                }
//            },
            {
                xtype: 'spacer',
                width: 10
            }, 
//            {
//                xtype: 'button',
//                iconCls: 'settings_icon',
//                text: "Panel de Administración",
//                handler: function ()
//                {
//                    document.location = App.BaseUrl + 'index.php/administrator';
//                },
//                hidden: (App.Security.Session.user_type == 'N' || App.Security.Session.user_type == 'P' ? true : false)
//            },
            // Regresar cambios para presentación
            {
                xtype: 'spacer',
                width: 10
            },

            //Hasta aquiiiiiiiiii
            '->',
            {
                xtype: 'label',
                text: App.Security.Session.user_username
            }, {
                xtype: 'spacer',
                hidden: (screen.width<400) ? true : false,
                width: 30
            }, {
                text: "Ayuda",
                iconCls: 'help_icon',
                hidden: (screen.width<400) ? true : false,
                handler: function ()
                {

                    w = new App.Help.HelpWindow();
                    w.show();


//        document.location = App.BaseUrl + 'index.php/app/downloaddir/media~PAF.pdf'
                }
            }, {
                xtype: 'spacer',
                width: 10
            }, {
                xtype: 'button',
                text: "Preferencias",
                iconCls: 'account_icon',
                handler: function ()
                {
                    w = new App.Preferences.UsersWindow();
                    App.Core.User.Store.setBaseParam('show_admin_user', 1);
                    App.Core.Languages.Store.load();
                    App.Core.User.Store.load
                            ({
                                callback: function (records)
                                {
                                    record = App.Core.User.Store.getById(App.Security.Session.user_id);
                                    record.data.user_password = null;
                                    w.form.record = record;
                                    w.form.getForm().loadRecord(record);
                                    w.show();
                                }
                            });
                }
            }, {
                xtype: 'spacer',
                width: 10
            }, {
                text: "Cerrar Sesión",
                iconCls: 'logout_icon',
                handler: function ()
                {
                    Ext.MessageBox.confirm(App.Language.General.cession_close, App.Language.General.sure_exit_system, function (btn)
                    {
                        if (btn == 'yes')
                        {
                            document.location = App.BaseUrl + 'index.php/core/auth/logout'
                        }
                    });
                }
            }];


App.Help.HelpWindow = Ext.extend(Ext.Window, {
    title: 'Manual de Usuario',
    width: 500,
    height: 380,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'grid',
                store: App.Core.Help.Store,
                viewConfig: {
                    forceFit: true
                },
                tbar:
                        [{
                                text: 'Descarga en pdf',
                                iconCls: 'download_icon',
                                handler: function (b)
                                {
                                    document.location = App.BaseUrl + 'index.php/app/downloaddir/media~PAF.pdf'
                                }
                            }],
                listeners:
                        {
                            'beforerender': function ()
                            {
                                App.Core.Help.Store.load();
                            }
                        },
                columns: [{
                        header: 'Titulo',
                        dataIndex: 'help_title',
                        sortable: true,
                        editable: false
                    }, {
                        header: 'Enlace',
                        dataIndex: 'help_url',
                        sortable: true,
                        renderer: function (val, metadata, record)
                        {
                            return "<a href=' " + record.data.help_url + " ' TARGET='_blank'> Enlace  </a>";
                        }
                    }
                ],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Help.HelpWindow.superclass.initComponent.call(this);
    }
});