App.Maintainers.addToModuleMenu('users',
        {
            xtype: 'button',
            text: App.Language.Core.users_groups,
            iconCls: 'people_icon_32',
            scale: 'large',
            iconAlign: 'top',
            module: 'Users'
        });
App.Maintainers.Users.Principal = Ext.extend(Ext.TabPanel,
        {
            activeTab: 0,
            border: false,
            title: App.Language.Core.users_groups,
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'panel',
                                title: App.Language.Core.users,
                                border: false,
                                layout: 'border',
                                tbar:
                                        {
                                            xtype: 'toolbar',
                                            items:
                                                    [{
                                                            xtype: 'button',
                                                            text: App.Language.General.nnew,
                                                            iconCls: 'add_icon',
                                                            handler: function ()
                                                            {
                                                                w = new App.Maintainers.Users.AddUserWindow();
                                                                w.show();
                                                            }
                                                        }, {
                                                            xtype: 'tbseparator',
                                                            width: 20
                                                        }, {
                                                            xtype: 'button',
                                                            text: App.Language.Core.enable,
                                                            iconCls: 'alta_user',
                                                            handler: function (b)
                                                            {
                                                                grid = b.ownerCt.ownerCt.gridUser;
                                                                if (grid.getSelectionModel().getCount())
                                                                {
                                                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Core.enable_user, function (b)
                                                                    {
                                                                        if (b == 'yes')
                                                                        {
                                                                            grid.getSelectionModel().each(function (record)
                                                                            {
                                                                                App.Maintainers.Users.ChangeStatusUserAcount(0, record);
                                                                            });
                                                                        }
                                                                    });
                                                                } else {
                                                                    Ext.FlashMessage.alert(App.Language.Core.select_user_enable);
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'spacer',
                                                            width: 10
                                                        }, {
                                                            xtype: 'button',
                                                            text: App.Language.Core.disable,
                                                            iconCls: 'baja_user',
                                                            handler: function (b)
                                                            {
                                                                grid = b.ownerCt.ownerCt.gridUser;
                                                                if (grid.getSelectionModel().getCount())
                                                                {
                                                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Core.disable_user, function (b)
                                                                    {
                                                                        if (b == 'yes')
                                                                        {
                                                                            grid.getSelectionModel().each(function (record)
                                                                            {
                                                                                App.Maintainers.Users.ChangeStatusUserAcount(1, record);
                                                                            });
                                                                            App.Core.User.Store.load();
                                                                        }
                                                                    });
                                                                } else {
                                                                    Ext.FlashMessage.alert(App.Language.Core.select_user_disable);
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'tbseparator',
                                                            width: 10
                                                        }, {
                                                            text: App.Language.General.search,
                                                            iconCls: 'search_icon_16',
                                                            enableToggle: true,
                                                            bodyStyle: 'padding:5px 5px 0',
                                                            handler: function (b)
                                                            {
                                                                if (b.ownerCt.ownerCt.form.isVisible())
                                                                {
                                                                    b.ownerCt.ownerCt.form.hide();
                                                                } else {
                                                                    b.ownerCt.ownerCt.form.show();
                                                                }
                                                                b.ownerCt.ownerCt.doLayout();
                                                            }
                                                        }, {
                                                            xtype: 'tbseparator',
                                                            width: 10
                                                        }, {
                                                            text: App.Language.General.eexport,
                                                            iconCls: 'export_icon',
                                                            bodyStyle: 'padding:5px 5px 0',
                                                            handler: function (b)
                                                            {
                                                                wap = new App.Maintainers.Users.exportListWindow();
                                                                wap.show();
                                                            }
                                                        }]
                                        },
                                items:
                                        [{
                                                xtype: 'form',
                                                labelWidth: 150,
                                                region: 'north',
                                                margins: '5 5 0 5',
                                                plugins: [new Ext.ux.OOSubmit()],
                                                title: App.Language.General.searching,
                                                frame: true,
                                                ref: 'form',
                                                hidden: true,
                                                height: 170,
                                                fbar:
                                                        [{
                                                                text: App.Language.General.search,
                                                                handler: function (b)
                                                                {
                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                    App.Core.User.Store.baseParams = form.getSubmitValues();
                                                                    App.Core.User.Store.setBaseParam('user_id', null);
                                                                    App.Core.User.Store.load();
                                                                }
                                                            }, {
                                                                text: App.Language.General.clean,
                                                                handler: function (b)
                                                                {
                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                    form.reset();
                                                                    App.Core.User.Store.setBaseParam([]);
                                                                    App.Core.User.Store.load();
                                                                }
                                                            }],
                                                items:
                                                        [{
                                                                layout: 'column',
                                                                items:
                                                                        [{
                                                                                columnWidth: .5,
                                                                                layout: 'form',
                                                                                items:
                                                                                        [{
                                                                                                xtype: 'textfield',
                                                                                                fieldLabel: App.Language.Core.username,
                                                                                                anchor: '80%',
                                                                                                name: 'user_name'
                                                                                            }, {
                                                                                                xtype: 'textfield',
                                                                                                fieldLabel: App.Language.Core.email,
                                                                                                anchor: '80%',
                                                                                                name: 'user_email'
                                                                                            }, {
                                                                                                xtype: 'textfield',
                                                                                                fieldLabel: App.Language.Core.english_username,
                                                                                                anchor: '80%',
                                                                                                name: 'user_username'
                                                                                            }]
                                                                            }, {
                                                                                columnWidth: .5,
                                                                                layout: 'form',
                                                                                items:
                                                                                        [{
                                                                                                columnWidth: .2,
                                                                                                layout: 'form',
                                                                                                items:
                                                                                                        [{
                                                                                                                xtype: 'label',
                                                                                                                text: App.Language.General.select_date_range_expiration
                                                                                                            }]
                                                                                            }, {
                                                                                                columnWidth: .4,
                                                                                                layout: 'column',
                                                                                                frame: true,
                                                                                                items:
                                                                                                        [{
                                                                                                                columnWidth: .5,
                                                                                                                layout: 'form',
                                                                                                                items:
                                                                                                                        [{
                                                                                                                                xtype: 'datefield',
                                                                                                                                ref: '../start_date',
                                                                                                                                fieldLabel: App.Language.General.start_date,
                                                                                                                                name: 'start_date',
                                                                                                                                anchor: '95%',
                                                                                                                                listeners:
                                                                                                                                        {
                                                                                                                                            'select': function (fd, date)
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
                                                                                                                                            'select': function (fd, date)
                                                                                                                                            {
                                                                                                                                                fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                            }]
                                                                                                            }]
                                                                                            }, {
                                                                                                columnWidth: .4,
                                                                                                layout: 'form',
                                                                                                items:
                                                                                                        [{
                                                                                                                xtype: 'spacer',
                                                                                                                height: 15
                                                                                                            }, {
                                                                                                                xtype: 'combo',
                                                                                                                fieldLabel: App.Language.Core.groups,
                                                                                                                hiddenName: 'user_group_id',
                                                                                                                triggerAction: 'all',
                                                                                                                store: App.Core.Groups.Store,
                                                                                                                displayField: 'user_group_name',
                                                                                                                valueField: 'user_group_id',
                                                                                                                mode: 'remote',
                                                                                                                minChars: 0,
                                                                                                                anchor: '100%'
                                                                                                            }]
                                                                                            }]
                                                                            }]
                                                            }]
                                            }, {
                                                xtype: 'grid',
                                                ref: 'gridUser',
                                                id: 'App.Maintainers.gridUser',
                                                loadMask: true,
                                                store: App.Core.User.Store,
                                                plugins: [new Ext.ux.OOSubmit()],
                                                region: 'center',
                                                margins: '5 5 5 5',
                                                viewConfig:
                                                        {
                                                            forceFit: true,
                                                            getRowClass: function (record, index)
                                                            {
                                                                var c = record.get('user_status');
                                                                if (c == 1)
                                                                {
                                                                    return 'red-row';
                                                                }
                                                            }
                                                        },
                                                listeners:
                                                        {
                                                            'rowdblclick': function (grid, rowIndex)
                                                            {
                                                                record = grid.getStore().getAt(rowIndex);
                                                                App.Maintainers.Users.EditUserSystem(record);
                                                            },
                                                            'beforerender': function ()
                                                            {
                                                                App.Core.User.Store.setBaseParam('show_admin_user', 1);
                                                                App.Core.User.Store.load();
                                                            }
                                                        },
                                                columns: [new Ext.grid.CheckboxSelectionModel(),
                                                    {
                                                        dataIndex: 'user_name',
                                                        header: App.Language.Core.username,
                                                        sortable: true,
                                                        width: 200
                                                    }, {
                                                        dataIndex: 'user_username',
                                                        header: App.Language.Core.english_username,
                                                        sortable: true,
                                                        width: 90
                                                    }, {
                                                        dataIndex: 'user_email',
                                                        header: App.Language.Core.email,
                                                        sortable: true,
                                                        width: 150
                                                    }, {
                                                        dataIndex: 'user_type_name',
                                                        header: App.Language.General.user_type,
                                                        sortable: true,
                                                        width: 100
                                                    }, {
                                                        dataIndex: 'user_string_groups',
                                                        header: App.Language.Core.groups,
                                                        sortable: true,
                                                        width: 220
                                                    }, {
                                                        dataIndex: 'user_tree_full_type',
                                                        header: App.Language.Core.full_access,
                                                        align: 'center',
                                                        sortable: true,
                                                        width: 100
                                                    }, {
                                                        xtype: 'datecolumn',
                                                        sortable: true,
                                                        dataIndex: 'user_expiration',
                                                        align: 'center',
                                                        width: 100,
                                                        header: App.Language.General.expiration_date,
                                                        format: App.General.DefaultDateFormat
                                                    }],
                                                sm: new Ext.grid.CheckboxSelectionModel()
                                            }]
                            }, {
                                xtype: 'grid',
                                height: 600,
                                title: App.Language.Core.groups,
                                store: App.Core.Groups.Store,
                                viewConfig:
                                        {
                                            forceFit: true
                                        },
                                listeners:
                                        {
                                            'rowdblclick': function (grid, rowIndex)
                                            {
                                                record = grid.getStore().getAt(rowIndex);
                                                App.Maintainers.Groups.OpenEditModeGroup(record);
                                            },
                                            'beforerender': function ()
                                            {
                                                App.Core.Groups.Store.load();
                                            }
                                        },
                                tbar:
                                        {
                                            xtype: 'toolbar',
                                            items:
                                                    [{
                                                            xtype: 'button',
                                                            text: App.Language.General.nnew,
                                                            iconCls: 'add_icon',
                                                            handler: function ()
                                                            {
                                                                w = new App.Maintainers.Groups.AddGroupWindow();
                                                                w.show();
                                                            }
                                                        }, {
                                                            xtype: 'tbseparator',
                                                            width: 20
                                                        }, {
                                                            xtype: 'button',
                                                            text: App.Language.General.ddelete,
                                                            iconCls: 'delete_icon',
                                                            handler: function (b)
                                                            {
                                                                grid = b.ownerCt.ownerCt;
                                                                if (grid.getSelectionModel().getCount())
                                                                {
                                                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Core.want_delete_group, function (b)
                                                                    {
                                                                        if (b == 'yes')
                                                                        {
                                                                            grid.getSelectionModel().each(function (record)
                                                                            {
                                                                                Ext.Ajax.request
                                                                                        ({
                                                                                            url: 'index.php/core/group/delete',
                                                                                            params: {
                                                                                                user_group_id: record.data.user_group_id
                                                                                            },
                                                                                            success: function (response)
                                                                                            {
                                                                                                response = Ext.decode(response.responseText);
                                                                                                Ext.FlashMessage.alert(response.msg);
                                                                                                App.Core.Groups.Store.load();
                                                                                            }
                                                                                        });
                                                                            });
                                                                        }
                                                                        App.Core.Groups.Store.load();
                                                                    });
                                                                } else {
                                                                    Ext.FlashMessage.alert(App.Language.Core.select_groups_delete);
                                                                }
                                                            }
                                                        }, {
                                                            xtype: 'spacer',
                                                            width: 10
                                                        }, {
                                                            xtype: 'button',
                                                            text: App.Language.Core.config_groups,
                                                            iconCls: 'config_group_icon',
                                                            handler: function ()
                                                            {
                                                                w = new App.Maintainers.Groups.ConfigGroupWindow();
                                                                w.show();
                                                            }
                                                        }]
                                        },
                                columns: [new Ext.grid.CheckboxSelectionModel(),
                                    {
                                        dataIndex: 'user_group_name',
                                        header: App.Language.General.name,
                                        sortable: true,
                                        width: 200
                                    }],
                                sm: new Ext.grid.CheckboxSelectionModel()
                            }, {
                                xtype: 'panel',
                                title: App.Language.General.users_and_providers,
                                border: false,
                                layout: 'border',
                                tbar:
                                        {
                                            xtype: 'toolbar',
                                            items:
                                                    [{
                                                            xtype: 'button',
                                                            text: App.Language.General.add,
                                                            iconCls: 'add_icon',
                                                            handler: function ()
                                                            {
                                                                w = new App.Maintainers.Users.AddUserProviderWindow();
                                                                w.show();
                                                            }
                                                        }, {
                                                            xtype: 'tbseparator'
                                                        }, {
                                                            xtype: 'button',
                                                            text: App.Language.General.ddelete,
                                                            iconCls: 'delete_icon',
                                                            handler: function (b)
                                                            {
                                                                grid = b.ownerCt.ownerCt.gridUserProvider;
                                                                if (grid.getSelectionModel().getCount())
                                                                {
                                                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function (b)
                                                                    {
                                                                        if (b == 'yes')
                                                                        {
                                                                            grid.getSelectionModel().each(function (record)
                                                                            {
                                                                                App.Core.UserProvider.Store.remove(record);
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
                                                            text: App.Language.General.search,
                                                            iconCls: 'search_icon_16',
                                                            enableToggle: true,
                                                            bodyStyle: 'padding:5px 5px 0',
                                                            handler: function (b)
                                                            {
                                                                if (b.ownerCt.ownerCt.form.isVisible())
                                                                {
                                                                    b.ownerCt.ownerCt.form.hide();
                                                                } else {
                                                                    b.ownerCt.ownerCt.form.show();
                                                                }
                                                                b.ownerCt.ownerCt.doLayout();
                                                            }
                                                        }]
                                        },
                                items:
                                        [{
                                                xtype: 'form',
                                                region: 'north',
                                                title: App.Language.General.searching,
                                                margins: '5 5 0 5',
                                                frame: true,
                                                ref: 'form',
                                                border: false,
                                                hidden: true,
                                                height: 120,
                                                fbar:
                                                        [{
                                                                text: App.Language.General.search,
                                                                handler: function (b)
                                                                {
                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                    App.Core.UserProvider.Store.baseParams = form.getSubmitValues();
                                                                    App.Core.UserProvider.Store.load();
                                                                }
                                                            }, {
                                                                text: App.Language.General.clean,
                                                                handler: function (b)
                                                                {
                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                    form.reset();
                                                                    App.Core.UserProvider.Store.setBaseParam([]);
                                                                    App.Core.UserProvider.Store.baseParams = form.getSubmitValues();
                                                                    App.Core.UserProvider.Store.load();
                                                                }
                                                            }],
                                                items:
                                                        [{
                                                                layout: 'column',
                                                                labelWidth: 150,
                                                                items:
                                                                        [{
                                                                                columnWidth: .4,
                                                                                layout: 'form',
                                                                                items:
                                                                                        [{
                                                                                                xtype: 'combo',
                                                                                                fieldLabel: "Tipo de Mantenimiento",
                                                                                                anchor: '95%',
                                                                                                store: App.Core.MtnMaintainerType.Store,
                                                                                                hiddenName: 'mtn_maintainer_type_id',
                                                                                                triggerAction: 'all',
                                                                                                displayField: 'mtn_maintainer_type_name',
                                                                                                valueField: 'mtn_maintainer_type_id',
                                                                                                editable: true,
                                                                                                selecOnFocus: true,
                                                                                                typeAhead: true,
                                                                                                selectOnFocus: true,
                                                                                                mode: 'remote',
                                                                                                minChars: 0,
                                                                                                listeners:
                                                                                                        {
                                                                                                            'select': function (cb, record) {
                                                                                                                Ext.getCmp('App.Core.ProviderTypeComboBuscador').setDisabled(false);
                                                                                                                App.Core.ProviderTypeAll.Store.setBaseParam('mtn_maintainer_type_id', record.data.mtn_maintainer_type_id);
                                                                                                                App.Core.ProviderTypeAll.Store.load();
                                                                                                            }
                                                                                                        }
                                                                                            }]
                                                                            }, {
                                                                                columnWidth: .3,
                                                                                layout: 'form',
                                                                                items:
                                                                                        [{
                                                                                                xtype: 'combo',
                                                                                                fieldLabel: App.Language.General.provider,
                                                                                                anchor: '95%',
                                                                                                disabled: true,
                                                                                                id: 'App.Core.ProviderTypeComboBuscador',
                                                                                                store: App.Core.ProviderTypeAll.Store,
                                                                                                hiddenName: 'provider_id',
                                                                                                triggerAction: 'all',
                                                                                                displayField: 'provider_name',
                                                                                                valueField: 'provider_id',
                                                                                                editable: true,
                                                                                                selecOnFocus: true,
                                                                                                typeAhead: true,
                                                                                                selectOnFocus: true,
                                                                                                mode: 'remote',
                                                                                                minChars: 0
                                                                                            }]
                                                                            }, {
                                                                                columnWidth: .3,
                                                                                layout: 'form',
                                                                                items:
                                                                                        [{
                                                                                                xtype: 'combo',
                                                                                                fieldLabel: App.Language.General.user,
                                                                                                anchor: '95%',
                                                                                                store: App.Core.UserFull.Store,
                                                                                                hiddenName: 'user_id',
                                                                                                triggerAction: 'all',
                                                                                                displayField: 'user_name',
                                                                                                valueField: 'user_id',
                                                                                                editable: true,
                                                                                                selecOnFocus: true,
                                                                                                typeAhead: true,
                                                                                                selectOnFocus: true,
                                                                                                mode: 'remote',
                                                                                                minChars: 0
                                                                                            }]
                                                                            }]
                                                            }]
                                            }, {
                                                xtype: 'grid',
                                                ref: 'gridUserProvider',
                                                store: App.Core.UserProvider.Store,
                                                margins: '5 5 5 5',
                                                region: 'center',
                                                viewConfig:
                                                        {
                                                            forceFit: true
                                                        },
                                                listeners:
                                                        {
                                                            'beforerender': function (b)
                                                            {
                                                                App.Core.UserProvider.Store.load();
                                                            }
                                                        },
                                                columns: [new Ext.grid.CheckboxSelectionModel(),
                                                    {
                                                        dataIndex: 'provider_name',
                                                        header: App.Language.General.provider,
                                                        sortable: true
                                                    }, {
                                                        dataIndex: 'user_name',
                                                        header: App.Language.General.user,
                                                        sortable: true
                                                    }, {
                                                        dataIndex: 'mtn_maintainer_type_name',
                                                        header: App.Language.General.maintenance_type,
                                                        sortable: true
                                                    }],
                                                sm: new Ext.grid.CheckboxSelectionModel()
                                            }]
                            }]
                App.Maintainers.Users.Principal.superclass.initComponent.call(this);
            }
        });
App.Maintainers.Users.exportListWindow = Ext.extend(Ext.Window,
        {
            title: App.Language.General.eexport_list,
            width: 400,
            height: 150,
            layout: 'fit',
            modal: true,
            resizable: false,
            padding: 1,
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'form',
                                padding: 5,
                                labelWidth: 150,
                                items:
                                        [{
                                                xtype: 'textfield',
                                                fieldLabel: App.Language.General.file_name,
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
                                                handler: function (b)
                                                {
                                                    b.ownerCt.ownerCt.ownerCt.close();
                                                }
                                            }, {
                                                xtype: 'button',
                                                text: App.Language.General.eexport,
                                                handler: function (b)
                                                {
                                                    fp = b.ownerCt.ownerCt;
                                                    form = b.ownerCt.ownerCt.getForm();
                                                    if (form.isValid())
                                                    {
                                                        form.submit
                                                                ({
                                                                    clientValidation: true,
                                                                    waitTitle: App.Language.General.message_please_wait,
                                                                    waitMsg: App.Language.General.message_generating_file,
                                                                    url: 'index.php/core/user/export',
                                                                    success: function (form, response)
                                                                    {
                                                                        document.location = 'index.php/app/download/' + response.result.file;
                                                                        b.ownerCt.ownerCt.ownerCt.hide();
                                                                    },
                                                                    failure: function (form, action)
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
                App.Maintainers.Users.exportListWindow.superclass.initComponent.call(this);
            }
        });
App.Maintainers.Groups.AddGroupWindow = Ext.extend(Ext.Window,
        {
            title: App.Language.Core.add_user_group,
            resizable: false,
            modal: true,
            width: 450,
            height: 140,
            layout: 'fit',
            padding: 1,
            initComponent: function ()
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
                                                name: 'user_group_name',
                                                anchor: '100%',
                                                minChars: 0,
                                                allowBlank: false
                                            }],
                                buttons:
                                        [{
                                                text: App.Language.General.close,
                                                handler: function (b)
                                                {
                                                    b.ownerCt.ownerCt.ownerCt.close();
                                                }
                                            }, {
                                                text: App.Language.General.save,
                                                ref: '../saveButton',
                                                handler: function (b)
                                                {
                                                    form = b.ownerCt.ownerCt.getForm();
                                                    if (form.isValid())
                                                    {
                                                        form.submit
                                                                ({
                                                                    url: 'index.php/core/group/add',
                                                                    success: function (fp, o)
                                                                    {
                                                                        App.Core.Groups.Store.load();
                                                                        b.ownerCt.ownerCt.ownerCt.close();
                                                                    },
                                                                    failure: function (fp, o)
                                                                    {
                                                                        Ext.FlashMessage.alert(o.result.msg);
                                                                    }
                                                                });
                                                    }
                                                }
                                            }]
                            }];
                App.Maintainers.Groups.AddGroupWindow.superclass.initComponent.call(this);
            }
        });
App.Maintainers.Groups.OpenEditModeGroup = function (record)
{
    w = new App.Maintainers.Groups.AddGroupWindow
            ({
                title: App.Language.Core.edit_users_group
            });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function ()
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

App.Maintainers.Groups.ConfigGroupWindow = Ext.extend(Ext.Window,
        {
            title: App.Language.Core.user_group_settings,
            resizable: false,
            modal: true,
            border: true,
            width: 752,
            height: 450,
            layout: 'fit',
            padding: 2,
            listeners:
                    {
                        'beforerender': function ()
                        {
                            //Stores; asociaci�n de premisos de modulos al grupo
                            App.Core.Modules.Actions.Store.setBaseParam('module_id', null);
                            App.Core.Modules.Actions.Store.setBaseParam('user_group_id', null);
                            App.Core.Modules.Actions.Store.load();
                            App.Core.Groups.Permissions.Store.setBaseParam('user_group_id', null);
                            App.Core.Groups.Permissions.Store.setBaseParam('module_id', null);
                            App.Core.Groups.Permissions.Store.load();
                            //Stores; asociaci�n de usuarios a grupos
                            App.Core.Groups.Users.Store.setBaseParam('user_group_id', null);
                            App.Core.Groups.Users.Store.load();
                            App.Core.Groups.UsersOutside.Store.setBaseParam('user_group_id', null);
                            App.Core.Groups.UsersOutside.Store.load();
                        }
                    },
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'tabpanel',
                                activeTab: 0,
                                border: false,
                                items:
                                        [{
                                                title: App.Language.Core.users,
                                                height: '100%',
                                                items:
                                                        [{
                                                                xtype: 'form',
                                                                ref: 'formUsuariosGroup',
                                                                labelWidth: 150,
                                                                padding: 5,
                                                                items:
                                                                        [{
                                                                                xtype: 'combo',
                                                                                ref: 'comboGroupId',
                                                                                fieldLabel: App.Language.Core.groups,
                                                                                triggerAction: 'all',
                                                                                hiddenName: 'user_group_id',
                                                                                store: App.Core.Groups.Store,
                                                                                displayField: 'user_group_name',
                                                                                valueField: 'user_group_id',
                                                                                mode: 'remote',
                                                                                editable: false,
                                                                                minChars: 0,
                                                                                allowBlank: false,
                                                                                listeners:
                                                                                        {
                                                                                            'select': function (cb, record)
                                                                                            {
                                                                                                var user_group_id = record.data.user_group_id;
                                                                                                App.Core.Groups.Users.Store.setBaseParam('user_group_id', user_group_id);
                                                                                                App.Core.Groups.Users.Store.load();
                                                                                                App.Core.Groups.UsersOutside.Store.setBaseParam('user_group_id', user_group_id);
                                                                                                App.Core.Groups.UsersOutside.Store.load();
                                                                                            }
                                                                                        }
                                                                            }, {
                                                                                xtype: 'panel',
                                                                                border: false,
                                                                                items:
                                                                                        [{
                                                                                                xtype: 'itemselector',
                                                                                                name: 'usersToGroup',
                                                                                                imagePath: 'javascript/extjs/ux/images/',
                                                                                                drawUpIcon: false,
                                                                                                drawDownIcon: false,
                                                                                                drawTopIcon: false,
                                                                                                drawBotIcon: false,
                                                                                                multiselects:
                                                                                                        [{
                                                                                                                width: 350,
                                                                                                                height: 300,
                                                                                                                store: App.Core.Groups.UsersOutside.Store,
                                                                                                                valueField: 'user_id',
                                                                                                                displayField: 'user_real_name_and_username',
                                                                                                                legend: App.Language.Core.users_outside_group
                                                                                                            }, {
                                                                                                                width: 350,
                                                                                                                height: 300,
                                                                                                                store: App.Core.Groups.Users.Store,
                                                                                                                valueField: 'user_id',
                                                                                                                displayField: 'user_real_name_and_username',
                                                                                                                legend: App.Language.Core.users_in_groups
                                                                                                            }]
                                                                                            }]
                                                                            }],
                                                                buttons:
                                                                        [{
                                                                                text: App.Language.General.close,
                                                                                handler: function (b)
                                                                                {
                                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                                }
                                                                            }, {
                                                                                text: App.Language.General.save,
                                                                                cls: 'button_permit',
                                                                                ref: '../saveButton',
                                                                                handler: function (b)
                                                                                {
                                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                                    if (form.isValid())
                                                                                    {
                                                                                        form.submit
                                                                                                ({
                                                                                                    url: 'index.php/core/group/addUser',
                                                                                                    success: function (fp, o)
                                                                                                    {
                                                                                                        Ext.FlashMessage.alert(o.result.msg);
                                                                                                    },
                                                                                                    failure: function (fp, o)
                                                                                                    {
                                                                                                        Ext.MessageBox.alert(App.Language.General.error, o.result.msg);
                                                                                                    }
                                                                                                });
                                                                                    }
                                                                                }
                                                                            }]
                                                            }]
                                            }, {
                                                title: App.Language.Core.permissions,
                                                height: '100%',
                                                autoScroll: true,
                                                items:
                                                        [{
                                                                xtype: 'form',
                                                                ref: 'formPermissionsGroup',
                                                                labelWidth: 150,
                                                                autoHeight: true,
                                                                padding: 5,
                                                                autoScroll: true,

                                                                items:
                                                                        [{
                                                                                xtype: 'combo',
                                                                                ref: 'comboGroupId',
                                                                                fieldLabel: App.Language.Core.groups,
                                                                                triggerAction: 'all',
                                                                                hiddenName: 'user_group_id',
                                                                                store: App.Core.Groups.Store,
                                                                                displayField: 'user_group_name',
                                                                                valueField: 'user_group_id',
                                                                                mode: 'remote',
                                                                                editable: false,
                                                                                minChars: 0,
                                                                                allowBlank: false,
                                                                                listeners:
                                                                                        {
                                                                                            'select': function (cb, record)
                                                                                            {
                                                                                                var module_id = cb.ownerCt.comboModulesId.getValue();
                                                                                                var user_group_id = record.data.user_group_id;
                                                                                                App.Core.Modules.Actions.Store.setBaseParam('module_id', module_id);
                                                                                                App.Core.Modules.Actions.Store.setBaseParam('user_group_id', user_group_id);
                                                                                                App.Core.Modules.Actions.Store.load();
                                                                                                App.Core.Groups.Permissions.Store.setBaseParam('user_group_id', user_group_id);
                                                                                                App.Core.Groups.Permissions.Store.setBaseParam('module_id', module_id);
                                                                                                App.Core.Groups.Permissions.Store.load();
                                                                                                
                                                                                                 if (module_id.length) {

                                                                                                    cb.ownerCt.treeGridNodes.getLoader().baseParams =
                                                                                                            {
                                                                                                                user_group_id: user_group_id,
                                                                                                                module_id: module_id
                                                                                                            };
                                                                                                    cb.ownerCt.treeGridNodes.getLoader().load(cb.ownerCt.treeGridNodes.getRootNode());
                                                                                                    
                                                                                                   jQuery('.node_asset').css("display", "block");
                                                                                                }else{
                                                                                                    jQuery('.node_asset').css("display", "none");
                                                                                                }
                                                                                            }
                                                                                        }
                                                                            }, {
                                                                                xtype: 'combo',
                                                                                ref: 'comboModulesId',
                                                                                fieldLabel: App.Language.Core.module,
                                                                                hiddenName: 'module_id',
                                                                                triggerAction: 'all',
                                                                                store: App.Core.Modules.Store.Front,
                                                                                displayField: 'module_name',
                                                                                valueField: 'module_id',
                                                                                mode: 'remote',
                                                                                editable: false,
                                                                                minChars: 0,
                                                                                allowBlank: false,
                                                                                listeners:
                                                                                        {
                                                                                            'select': function (cb, record)
                                                                                            {
                                                                                                var module_id = record.data.module_id;
                                                                                                
                                                                                                var user_group_id = cb.ownerCt.comboGroupId.getValue();
                                                                                                App.Core.Modules.Actions.Store.setBaseParam('module_id', module_id);
                                                                                                App.Core.Modules.Actions.Store.setBaseParam('user_group_id', user_group_id);
                                                                                                App.Core.Modules.Actions.Store.load();
                                                                                                App.Core.Groups.Permissions.Store.setBaseParam('user_group_id', user_group_id);
                                                                                                App.Core.Groups.Permissions.Store.setBaseParam('module_id', module_id);
                                                                                                App.Core.Groups.Permissions.Store.load();

                                                                                                if (module_id.length) {

                                                                                                    cb.ownerCt.treeGridNodes.getLoader().baseParams =
                                                                                                            {
                                                                                                                user_group_id: user_group_id,
                                                                                                                module_id: module_id
                                                                                                            };
                                                                                                    cb.ownerCt.treeGridNodes.getLoader().load(cb.ownerCt.treeGridNodes.getRootNode());
                                                                                                    
                                                                                                   jQuery('.node_asset').css("display", "block");
                                                                                                }else{
                                                                                                    jQuery('.node_asset').css("display", "none");
                                                                                                }
                                                                                            }
                                                                                        }
                                                                            }, {
                                                                                xtype: 'panel',
                                                                                border: false,
                                                                                items:
                                                                                        [{
                                                                                                xtype: 'itemselector',
                                                                                                name: 'permissionsToGroup',
                                                                                                imagePath: 'javascript/extjs/ux/images/',
                                                                                                drawUpIcon: false,
                                                                                                drawDownIcon: false,
                                                                                                drawTopIcon: false,
                                                                                                drawBotIcon: false,
                                                                                                multiselects:
                                                                                                        [{
                                                                                                                width: 350,
                                                                                                                height: 270,
                                                                                                                store: App.Core.Modules.Actions.Store,
                                                                                                                valueField: 'module_action_id',
                                                                                                                displayField: 'language_tag_value',
                                                                                                                legend: App.Language.Core.action_not_assigned_group
                                                                                                            }, {
                                                                                                                width: 350,
                                                                                                                height: 270,
                                                                                                                store: App.Core.Groups.Permissions.Store,
                                                                                                                valueField: 'module_action_id',
                                                                                                                maxLength: 255,
                                                                                                                displayField: 'language_tag_value',
                                                                                                                legend: App.Language.Core.group_permissions
                                                                                                            }]
                                                                                            }]
                                                                            },
                                                                            
                                                                            {
                                                                                xtype: 'treegrid',
                                                                                ref: 'treeGridNodes',
                                                                                border: false,
                                                                                cls: 'node_asset',
                                                                                loader:
                                                                                        {
                                                                                            dataUrl: 'index.php/core/permissions/expandAsset',
                                                                                            preloadChildren: true,
                                                                                            autoLoad: false
                                                                                        },
                                                                                viewConfig:
                                                                                        {
                                                                                            forceFit: true
                                                                                        },
                                                                                columns:
                                                                                        [{
                                                                                                dataIndex: 'text',
                                                                                                header: App.Language.General.name,
                                                                                                sortable: false,
                                                                                                width: 376
                                                                                            },  {
                                                                                                header: App.Language.Core.level,
                                                                                                dataIndex: 'checked',
                                                                                                align: 'center',
                                                                                                tpl: new Ext.XTemplate('<input class="x-tree-node-cb" type="checkbox" name="nodes[]" value="{id}"', '<tpl if="checked_node == true">', ' checked', '</tpl>', '/>'),
                                                                                                width: 158
                                                                                            }, {
                                                                                                header: App.Language.Core.branch,
                                                                                                dataIndex: 'checked',
                                                                                                align: 'center',
                                                                                                tpl: '<input class="x-tree-node-cb" type="checkbox" name="branches[]" value="{id}"/>',
                                                                                                width: 158
                                                                                            }]
                                                                            }
                                                                        ],
                                                                buttons:
                                                                        [{
                                                                                text: App.Language.General.close,
                                                                                handler: function (b)
                                                                                {
                                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                                }
                                                                            }, {
                                                                                text: App.Language.General.save,
                                                                                cls: 'button_permit',
                                                                                ref: '../saveButton',
                                                                                handler: function (b)
                                                                                {
                                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                                    if (form.isValid())
                                                                                    {
                                                                                        form.submit
                                                                                                ({
                                                                                                    url: 'index.php/core/permissions/add',
                                                                                                    success: function (fp, o)
                                                                                                    {
                                                                                                        Ext.FlashMessage.alert(o.result.msg);
                                                                                                    },
                                                                                                    failure: function (fp, o)
                                                                                                    {
                                                                                                        Ext.MessageBox.alert(App.Language.General.error, o.result.msg);
                                                                                                    }
                                                                                                });
                                                                                    }
                                                                                }
                                                                            }]
                                                            }]
                                            }]
                            }];
                App.Maintainers.Groups.ConfigGroupWindow.superclass.initComponent.call(this);
            }
        });
App.Maintainers.Users.AddUserWindow = Ext.extend(Ext.Window,
        {
            title: App.Language.Core.add_users_system,
            resizable: false,
            modal: true,
            width: 500,
            height: 400,
            layout: 'fit',
            padding: 1,
            listeners:
                    {
                        'beforerender': function ()
                        {
                            App.Core.Groups.Store.load();
                            App.Core.User.Groups.Store.setBaseParam('user_id', App.Maintainers.Users.id);
                            App.Core.User.GroupsOutside.Store.setBaseParam('user_id', App.Maintainers.Users.id);
                            App.Core.User.GroupsOutside.Store.load();
                            App.Core.User.Groups.Store.load();
                        }
                    },
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'tabpanel',
                                activeTab: 0,
                                ref: 'tabsPanel',
                                items:
                                        [{
                                                xtype: 'form',
                                                ref: '../form',
                                                plugins: [new Ext.ux.OOSubmit()],
                                                title: App.Language.General.details,
                                                labelWidth: 150,
                                                padding: 5,
                                                items:
                                                        [{
                                                                xtype: 'textfield',
                                                                fieldLabel: App.Language.Core.username,
                                                                name: 'user_name',
                                                                minLength: 5,
                                                                anchor: '100%',
                                                                minChars: 0,
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                fieldLabel: App.Language.Core.email,
                                                                name: 'user_email',
                                                                vtype: 'email',
                                                                anchor: '100%',
                                                                minChars: 0,
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                fieldLabel: App.Language.Core.english_username,
                                                                name: 'user_username',
                                                                ref: 'username',
                                                                minLength: 5,
                                                                anchor: '100%',
                                                                minChars: 0,
                                                                allowBlank: false
                                                            }, {
                                                                xtype: 'textfield',
                                                                fieldLabel: App.Language.Core.password,
                                                                name: 'user_password',
                                                                ref: 'user_password',
                                                                inputType: 'password',
                                                                anchor: '100%',
//                    maxLength: 12,
//                    minLength: 8,
                                                                maskRe: /([a-zA-Z0-9\s]+)$/
                                                            }, {
                                                                xtype: 'datefield',
                                                                fieldLabel: App.Language.General.expiration_date,
                                                                name: 'user_expiration',
                                                                ref: 'user_expiration',
                                                                anchor: '100%',
                                                                editable: true
                                                            }, {
                                                                xtype: 'checkbox',
                                                                fieldLabel: App.Language.Core.system_administrator,
                                                                name: 'user_type',
                                                                inputValue: 1
                                                            }, {
                                                                xtype: 'checkbox',
                                                                fieldLabel: App.Language.Core.full_access_to_the_tree,
                                                                name: 'user_tree_full',
                                                                inputValue: 1
                                                            }, {
                                                                xtype: 'checkbox',
                                                                fieldLabel: App.Language.General.user_provider,
                                                                name: 'user_provider',
                                                                inputValue: 1
                                                            }],
                                                buttons:
                                                        [{
                                                                text: App.Language.General.close,
                                                                handler: function (b)
                                                                {
                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                }
                                                            }, {
                                                                text: App.Language.General.save,
                                                                ref: '../saveButton',
                                                                handler: function (b)
                                                                {
                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                    if (form.isValid())
                                                                    {
                                                                        form.submit
                                                                                ({
                                                                                    url: 'index.php/core/user/add',
                                                                                    params:
                                                                                            {
                                                                                                user_expiration: b.ownerCt.ownerCt.user_expiration.getValue()
                                                                                            },
                                                                                    success: function (fp, o)
                                                                                    {
                                                                                        if (o.result.user_type == 'P')
                                                                                        {
                                                                                            b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                                            App.Core.User.Store.load();
                                                                                        } else {
                                                                                            App.Maintainers.Users.id = o.result.user_id;
                                                                                            b.ownerCt.ownerCt.ownerCt.ownerCt.formGruposUsuario.setDisabled(false);
                                                                                            Ext.MessageBox.alert(App.Language.Core.notification, o.result.msg + '  ' + App.Language.Core.associate_user_groups + '  <br/> ' + App.Language.Core.the_password_is + ': ' + o.result.password);
                                                                                            b.ownerCt.ownerCt.ownerCt.ownerCt.tabsPanel.setActiveTab(1);
                                                                                            App.Core.User.Store.load();
                                                                                        }
                                                                                    },
                                                                                    failure: function (fp, o)
                                                                                    {
                                                                                        alert('Error:\n' + o.result.msg);
                                                                                    }
                                                                                });
                                                                    }
                                                                }
                                                            }]
                                            }, {
                                                xtype: 'form',
                                                ref: '../formGruposUsuario',
                                                title: App.Language.Core.users_the_groups,
                                                disabled: true,
                                                items:
                                                        [{
                                                                xtype: 'panel',
                                                                border: false,
                                                                items:
                                                                        [{
                                                                                xtype: 'itemselector',
                                                                                name: 'groups_to_user',
                                                                                imagePath: 'javascript/extjs/ux/images/',
                                                                                drawUpIcon: false,
                                                                                drawDownIcon: false,
                                                                                drawTopIcon: false,
                                                                                drawBotIcon: false,
                                                                                multiselects:
                                                                                        [{
                                                                                                width: 230,
                                                                                                height: 200,
                                                                                                store: App.Core.User.GroupsOutside.Store,
                                                                                                valueField: 'user_group_id',
                                                                                                displayField: 'user_group_name',
                                                                                                legend: App.Language.Core.available_groups
                                                                                            }, {
                                                                                                width: 230,
                                                                                                height: 200,
                                                                                                store: App.Core.User.Groups.Store,
                                                                                                valueField: 'user_group_id',
                                                                                                displayField: 'user_group_name',
                                                                                                legend: App.Language.Core.users_the_groups
                                                                                            }]
                                                                            }]
                                                            }],
                                                buttons:
                                                        [{
                                                                text: App.Language.General.close,
                                                                handler: function (b)
                                                                {
                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                }
                                                            }, {
                                                                text: App.Language.General.save,
                                                                ref: '../saveButton',
                                                                handler: function (b)
                                                                {
                                                                    form = b.ownerCt.ownerCt.getForm();
                                                                    if (form.isValid())
                                                                    {
                                                                        form.submit
                                                                                ({
                                                                                    url: 'index.php/core/user/addGroup',
                                                                                    params:
                                                                                            {
                                                                                                user_id: App.Maintainers.Users.id
                                                                                            },
                                                                                    success: function (fp, o)
                                                                                    {
                                                                                        App.Core.User.Store.load();
                                                                                        Ext.FlashMessage.alert(o.result.msg);
                                                                                        b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                                    },
                                                                                    failure: function (fp, o)
                                                                                    {
                                                                                        alert('Error:\n' + o.result.msg);
                                                                                    }
                                                                                });
                                                                    }
                                                                }
                                                            }]
                                            },
                                            {
                                                xtype: 'form',
                                                layout: 'fit',
                                                title: 'Ruta Inicial',
                                                border: false,
                                                items:
                                                        [{
                                                                xtype: 'treegrid',
                                                                ref: 'treeGridNodes',
                                                                id: 'App.Maintainers.objUserPath',
                                                                border: false,
//                dataUrl: 'index.php/core/permissions/expandUserPath',
                                                                tbar:
                                                                        [

                                                                            {
                                                                                text: App.Language.General.save,
                                                                                iconCls: 'save_icon',
                                                                                handler: function (b)
                                                                                {


                                                                                    //SELECCIONA EL CHECKBOX
                                                                                    form = b.ownerCt.ownerCt.ownerCt.getForm();
                                                                                    if (form.isValid())
                                                                                    {

                                                                                        var aux = form.getValues().user_path;
                                                                                        if (Array.isArray(aux)) {
                                                                                            //TIENE SELECCIONADO MAS DE UN checkbox ERROR!
                                                                                            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                                                                                        } else {
                                                                                            //SELECCIONADO SOLO UNO BIEN!
                                                                                            grid = Ext.getCmp('App.Maintainers.gridUser');
                                                                                            form.submit
                                                                                                    ({
                                                                                                        url: 'index.php/core/permissions/setUserPath',
                                                                                                        params:
                                                                                                                {
                                                                                                                    user_id: grid.getSelectionModel().getSelected().data.user_id
                                                                                                                            //ESTO LO ENVIA AUTOMATICO user_path: aux //ESTO ENVIA UN NODE_ID

                                                                                                                },
                                                                                                        waitTitle: App.Language.General.message_please_wait,
                                                                                                        waitMsg: App.Language.General.message_guarding_information,
                                                                                                        success: function (fp, o)
                                                                                                        {
                                                                                                            //Habilitar el boton
                                                                                                            b.setDisabled(false);
                                                                                                            Ext.FlashMessage.alert(o.result.msg);
                                                                                                        },
                                                                                                        failure: function (fp, o)
                                                                                                        {
                                                                                                            alert('Error:\n' + o.result.msg);
                                                                                                        }
                                                                                                    });
                                                                                        }


                                                                                    }
                                                                                }
                                                                            }],
                                                                listeners:
                                                                        {
                                                                            'beforerender': function (cb, record)
                                                                            {


                                                                                grid = Ext.getCmp('App.Maintainers.gridUser');
                                                                                cb.ownerCt.treeGridNodes.getLoader().baseParams =
                                                                                        {
                                                                                            user_id: grid.getSelectionModel().getSelected().data.user_id
                                                                                        };
                                                                                //cb.ownerCt.treeGridNodes.getLoader().load(cb.ownerCt.treeGridNodes.getRootNode());

                                                                            }
                                                                        },
                                                                loader:
                                                                        {
                                                                            dataUrl: 'index.php/core/permissions/expandUserPath',
                                                                            preloadChildren: true,
                                                                            autoLoad: true
                                                                        },
                                                                viewConfig:
                                                                        {
                                                                            forceFit: true
                                                                        },
                                                                columns:
                                                                        [{
                                                                                dataIndex: 'text',
                                                                                header: App.Language.General.name,
                                                                                sortable: false,
                                                                                width: 300
                                                                            }, {
                                                                                header: 'Ruta Inicial',
                                                                                dataIndex: 'checked',
                                                                                align: 'center',
//                    tpl: new Ext.XTemplate('<input class="x-tree-node-cb" type="checkbox" name="user_path" value="{id}"'),
                                                                                tpl: new Ext.XTemplate('<input class="x-tree-node-cb" type="checkbox" name="user_path" value="{id}"', '<tpl if="checked_node == true">', ' checked', '</tpl>', '/>'),
                                                                                width: 100
                                                                            }
                                                                        ]
                                                            }]
                                            }




                                        ]
                            }];
                App.Maintainers.Users.AddUserWindow.superclass.initComponent.call(this);
            }
        });
//App.Maintainers.Tree.PathStart = Ext.extend(Ext.TabPanel, 
////{
////     activeTab: 0,
////title:'Ruta de Inicio',
////    border: false,
////    initComponent: function()
////    {
////        this.items = 
////        [
//    {
//            xtype: 'form',
//            layout: 'fit',
////            title: App.Language.Core.access_tree,
//            border: false,
//            items: 
//            [{
//                xtype: 'treegrid',
//                ref: 'treeGridNodes',
//                border: false,
//                 dataUrl: 'index.php/core/node/expand',
//                tbar: 
//                        [
////                {
////                    xtype: 'label',
////                    text: App.Language.Core.group
////                }, {
////                    xtype: 'spacer',
////                    width: 10
////                }, {
////                    xtype: 'combo',
////                    ref: '../comboGroups',
////                    triggerAction: 'all',
////                    fieldLabel: App.Language.Core.group,
////                    hiddenName: 'user_group_id',
////                    store: App.Core.Groups.Store,
////                    displayField: 'user_group_name',
////                    valueField: 'user_group_id',
////                    mode: 'remote',
////                    editable: true,
////                    selecOnFocus: true,
////                    typeAhead: true,
////                    selectOnFocus:true,
////                    minChars: 0,
////                    allowBlank: false,
////                    listeners: 
////                    {
////                        'select': function(cb, record)
////                        {
////                            cb.ownerCt.ownerCt.getLoader().baseParams = 
////                            {
////                                user_group_id: record.data.user_group_id
////                            };
////                            cb.ownerCt.ownerCt.getLoader().load(cb.ownerCt.ownerCt.getRootNode());
////                        }
////                    }
////                }, {
////                    xtype: 'spacer',
////                    width: 10
////                },{
////                    text: App.Language.General.maintain_the_configuration,
////                    iconCls: 'keep_add_icon',
////                    tooltip: App.Language.General.leaving_the_existing_configuration_and_adding_the_change,
////                   
////                   // hidden: true,
////                    enableToggle: true,
////                    toggleHandler: function(b, state)
////                    {
////                        App.PermissionEnableSelect = state;
////                    },
////                    listeners: 
////                    {
////                        'show': function(b)
////                        {
////                            if (App.PermissionEnableSelect) 
////                            {
////                                b.toggle(true);
////                            }
////                        }
////                    }
////                }, {
////                    xtype: 'spacer',
////                    width: 5
////                }, 
//                {
//                    text: App.Language.General.save,
//                    iconCls: 'save_icon',
//                    handler: function(b)
//                    {
//                        form = b.ownerCt.ownerCt.ownerCt.getForm();
//                        if (form.isValid()) 
//                        {
//                            b.setDisabled(true);
//                            
//                            form.submit
//                            ({
//                                url: 'index.php/core/permissions/setTree',
//                                params: 
//                                {
//                                    type_save:  App.PermissionEnableSelect 
//                               
//                                },
//                                waitTitle: App.Language.General.message_please_wait,
//                                waitMsg: App.Language.General.message_guarding_information,
//                                success: function(fp, o)
//                                {
//                                    //Habilitar el boton
//                                    b.setDisabled(false);
//                                    
//                                    Ext.FlashMessage.alert(o.result.msg);
//                                },
//                                failure: function(fp, o)
//                                {
//                                    alert('Error:\n' + o.result.msg);
//                                }
//                            });
//                        }
//                    }
//                }],
////                loader: 
////                {
////                    dataUrl: 'index.php/core/permissions/expand',
////                    preloadChildren: true,
////                    autoLoad: false
////                },
//                viewConfig: 
//                {
//                    forceFit: true
//                },
//                columns: 
//                [{
//                    dataIndex: 'text',
//                    header: App.Language.General.name,
//                    sortable: false,
//                    width: 300
//                }, {
//                    dataIndex: 'node_type_category_name',
//                    header: App.Language.General.category,
//                    sortable: false,
//                    width: 250
//                }, {
//                    dataIndex: 'node_type_name',
//                    header: App.Language.General.type,
//                    sortable: false,
//                    width: 250
//                }
////                , {
////                    header: App.Language.Core.level,
////                    dataIndex: 'checked',
////                    align: 'center',
////                    tpl: new Ext.XTemplate('<input class="x-tree-node-cb" type="checkbox" name="nodes[]" value="{id}"', '<tpl if="checked_node == true">', ' checked', '</tpl>', '/>'),
////                    width: 100
////                }, {
////                    header: App.Language.Core.branch,
////                    dataIndex: 'checked',
////                    align: 'center',
////                    tpl: '<input class="x-tree-node-cb" type="checkbox" name="branches[]" value="{id}"/>',
////                    width: 100
////                }
//            ]
//            }]
//        }
////    ];
////        App.Maintainers.Tree.PathStart.superclass.initComponent.call(this);
////    }
//})
//;




App.Maintainers.Users.EditUserSystem = function (record)
{
    w = new App.Maintainers.Users.AddUserWindow
            ({
                title: App.Language.Core.edit_users_sistem
            });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    App.Maintainers.Users.id = record.data.user_id;
    w.form.username.setDisabled(true);
    w.formGruposUsuario.setDisabled(false);
    w.form.saveButton.handler = function ()
    {
        form = w.form.getForm();
        if (form.isValid())
        {
            form.updateRecord(w.form.record);
            App.Core.User.Store.load();
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.form.user_password.setValue('');
    w.show();
}

App.Maintainers.Users.ChangeStatusUserAcount = function (new_status, record)
{
    Ext.Ajax.request
            ({
                url: 'index.php/core/user/status',
                params:
                        {
                            user_id: record.data.user_id,
                            user_status: new_status
                        },
                method: 'POST',
                success: function (result, request)
                {
                    json = Ext.decode(result.responseText);
                    Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
                    App.Core.User.Store.load();
                },
                failure: function (result, request)
                {
                    Ext.MessageBox.alert(App.Language.General.error, result.msg);
                }
            });
}

App.Maintainers.Users.AddUserProviderWindow = Ext.extend(Ext.Window,
        {
            title: App.Language.General.associate_users_and_provider,
            resizable: false,
            modal: true,
            width: 400,
            height: 160,
            layout: 'fit',
            padding: 1,
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'form',
                                ref: 'form',
                                labelWidth: 150,
                                padding: 5,
                                items:
                                        [{
                                                xtype: 'combo',
                                                fieldLabel: App.Language.General.maintenance_type,
                                                anchor: '100%',
                                                store: App.Core.MtnMaintainerType.Store,
                                                hiddenName: 'mtn_maintainer_type_id',
                                                triggerAction: 'all',
                                                displayField: 'mtn_maintainer_type_name',
                                                valueField: 'mtn_maintainer_type_id',
                                                editable: true,
                                                selecOnFocus: true,
                                                typeAhead: true,
                                                selectOnFocus: true,
                                                mode: 'remote',
                                                minChars: 0,
                                                listeners:
                                                        {
                                                            'select': function (cb, record) {
                                                                Ext.getCmp('App.Core.ProviderTypeCombo').setDisabled(false);
                                                                App.Core.ProviderTypeAll.Store.setBaseParam('mtn_maintainer_type_id', record.data.mtn_maintainer_type_id);
                                                                App.Core.ProviderTypeAll.Store.load();
                                                            }
                                                        }
                                            }, {
                                                xtype: 'combo',
                                                fieldLabel: App.Language.General.provider,
                                                disabled: true,
                                                id: 'App.Core.ProviderTypeCombo',
                                                anchor: '100%',
                                                store: App.Core.ProviderTypeAll.Store,
                                                hiddenName: 'provider_id',
                                                triggerAction: 'all',
                                                displayField: 'provider_name',
                                                valueField: 'provider_id',
                                                allowBlank: false,
                                                editable: true,
                                                selecOnFocus: true,
                                                typeAhead: true,
                                                selectOnFocus: true,
                                                mode: 'remote',
                                                minChars: 0
                                            }, {
                                                xtype: 'combo',
                                                fieldLabel: App.Language.General.user,
                                                anchor: '100%',
                                                store: App.Core.SpecialProvider.Store,
                                                hiddenName: 'user_id',
                                                triggerAction: 'all',
                                                displayField: 'user_name',
                                                valueField: 'user_id',
                                                allowBlank: false,
                                                editable: true,
                                                selecOnFocus: true,
                                                typeAhead: true,
                                                selectOnFocus: true,
                                                mode: 'remote',
                                                minChars: 0
                                            }],
                                buttons:
                                        [{
                                                text: App.Language.General.close,
                                                handler: function (b)
                                                {
                                                    b.ownerCt.ownerCt.ownerCt.close();
                                                }
                                            }, {
                                                text: App.Language.General.add,
                                                ref: '../saveButton',
                                                handler: function (b)
                                                {
                                                    form = b.ownerCt.ownerCt.getForm();
                                                    if (form.isValid())
                                                    {
                                                        form.submit
                                                                ({
                                                                    url: 'index.php/core/userprovider/add',
                                                                    success: function (fp, o)
                                                                    {
                                                                        App.Core.UserProvider.Store.load();
                                                                        b.ownerCt.ownerCt.ownerCt.close();
                                                                        Ext.FlashMessage.alert(o.result.msg);
                                                                    },
                                                                    failure: function (fp, o)
                                                                    {
                                                                        alert('Error:\n' + o.result.msg);
                                                                    }
                                                                });
                                                    }
                                                }
                                            }]
                            }];
                App.Maintainers.Users.AddUserProviderWindow.superclass.initComponent.call(this);
            }
        });

