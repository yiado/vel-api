/* global App, Ext */

App.Maintainers.addToModuleMenu('request', {
    xtype: 'button',
    text: App.Language.Request.requests,
    iconCls: 'request_icon_32',
    scale: 'large',
    module: 'Request',
    iconAlign: 'top'
});

App.Maintainers.Request.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            ref: 'RequestTipoGrid',
            title: 'Tipo de solicitud',
            id: 'App.Maintainers.Request.ActivosSolicitudTipos',
            store: App.Request.SolicitudTipos.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Request.editActivosSolicitudTiposWindow(record);
                },
                'beforerender': function() {
                    App.Request.SolicitudTipos.Store.load();
                }
            },
            columns: [
                new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    header: App.Language.General.name,
                    dataIndex: 'solicitud_type_nombre',
                    sortable: true,
                    width: 40
                }, {
                    xtype: 'gridcolumn',
                    header: App.Language.General.commentary,
                    dataIndex: 'solicitud_type_comentario',
                    sortable: true,
                    width: 60
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({
                singleSelect: true
            }),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.Request.addActivosSolicitudTiposWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Maintainers.Request.ActivosSolicitudTipos');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b === 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Request.SolicitudTipos.Store.remove(record);
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
            xtype: 'grid',
            ref: 'RequestEstadoGrid',
            title: 'Estado de Solicitud',
            id: 'App.Maintainers.Request.ActivosSolicitudEstados',
            store: App.Request.SolicitudEstados.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Request.editActivosSolicitudEstadosWindow(record);
                },
                'beforerender': function() {
                    App.Request.SolicitudEstados.Store.load();
                }
            },
            columns: [
                new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    header: App.Language.General.name,
                    dataIndex: 'solicitud_estado_nombre',
                    sortable: true,
                    width: 40
                }, {
                    xtype: 'gridcolumn',
                    header: App.Language.General.commentary,
                    dataIndex: 'solicitud_estado_comentario',
                    sortable: true,
                    width: 60
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({
                singleSelect: true
            }),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.Request.addActivosSolicitudEstadosWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Maintainers.Request.ActivosSolicitudEstados');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b === 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Request.SolicitudEstados.Store.remove(record);
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
            xtype: 'grid',
            ref: 'RequestServiceTypeGrid',
            title: 'Tipo de Servicio',
            id: 'App.Maintainers.Request.ServiceType',
            store: App.Request.ServicesType.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Request.editServiceTypeWindow(record);
                },
                'beforerender': function() {
                    App.Core.UserNotification.Store.setBaseParam('show_admin_user', true);
                    App.Core.UserNotification.Store.load();
                    App.Request.ServicesType.Store.load();
                }
            },
            columns: [
                new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    header: App.Language.General.name,
                    dataIndex: 'service_type_name',
                    sortable: true,
                    width: 20                
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'User',
                    header: 'Usuario Responsable',
                    width: 20,
                    sortable: true,
                    renderer: function(User) {
                        return User.user_username;
                    }
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'User',
                    header: 'Correo',
                    width: 20,
                    sortable: true,
                    renderer: function(User) {
                        return User.user_email;
                    }
                }, {
                    xtype: 'gridcolumn',
                    header: App.Language.General.commentary,
                    dataIndex: 'service_type_commentary',
                    sortable: true,
                    width: 30
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({
                singleSelect: true
            }),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.Request.addServiceTypeWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Maintainers.Request.ServiceType');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b === 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Request.ServicesType.Store.remove(record);
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
            xtype: 'grid',
            ref: 'RequestServiceStatusGrid',
            title: 'Estado de Servicio',
            id: 'App.Maintainers.Request.ServiceStatus',
            store: App.Request.ServicesStatus.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Request.editServiceStatusWindow(record);
                },
                'beforerender': function() {
                    App.Request.ServicesStatus.Store.load();
                }
            },
            columns: [
                new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    header: App.Language.General.name,
                    dataIndex: 'service_status_name',
                    sortable: true,
                    width: 40
                }, {
                    xtype: 'gridcolumn',
                    header: App.Language.General.commentary,
                    dataIndex: 'service_status_commentary',
                    sortable: true,
                    width: 60
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({
                singleSelect: true
            }),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.Request.addServiceStatusWindow();
                        w.show();
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Maintainers.Request.ServiceStatus');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b === 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Request.ServicesStatus.Store.remove(record);
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
        App.Maintainers.Request.Principal.superclass.initComponent.call(this);
    }
});

/**
 * Tipo de solicitud
 */
App.Maintainers.Request.addActivosSolicitudTiposWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.add_asset_state,
    resizable: false,
    modal: true,
    width: 380,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.name_state,
                name: 'solicitud_type_nombre',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.commentary,
                name: 'solicitud_type_comentario',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/tipo/add',
                            success: function(fp, o) {
                                App.Request.SolicitudTipos.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.Request.addActivosSolicitudTiposWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Request.editActivosSolicitudTiposWindow = function(record) {
    w = new App.Maintainers.Request.addActivosSolicitudTiposWindow({
        title: App.Language.Asset.edit_asset_state
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
};

/**
 * Estado de solicitud
 */
App.Maintainers.Request.addActivosSolicitudEstadosWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.add_asset_state,
    resizable: false,
    modal: true,
    width: 380,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.name_state,
                name: 'solicitud_estado_nombre',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.commentary,
                name: 'solicitud_estado_comentario',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/estado/add',
                            success: function(fp, o) {
                                App.Request.SolicitudEstados.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.Request.addActivosSolicitudEstadosWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Request.editActivosSolicitudEstadosWindow = function(record) {
    w = new App.Maintainers.Request.addActivosSolicitudEstadosWindow({
        title: App.Language.Asset.edit_asset_state
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
};

/**
 * Tipo de servicio
 */
App.Maintainers.Request.addServiceTypeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.add_asset_state,
    resizable: false,
    modal: true,
    width: 380,
    height: 220,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [
                {
                xtype: 'textfield',
                fieldLabel: 'Servicio',
                name: 'service_type_name',
                anchor: '100%',
                allowBlank: false
            },{
                    xtype: 'combo',
                    anchor: '100%',
                    triggerAction: 'all',
                    fieldLabel: 'Usuario Responsable',
                    hiddenName: 'user_id',
                    store: App.Core.UserNotification.Store,
                    displayField: 'user_name',
                    valueField: 'user_id',
                    mode: 'remote',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    fieldLabel: App.Language.General.description,
                    name: 'service_type_commentary',
                    anchor: '100%'
                }
            ],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/servicetype/add',
                            success: function(fp, o) {
                                App.Request.ServicesType.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];        
        App.Maintainers.Request.addServiceTypeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Request.editServiceTypeWindow = function(record) {
    w = new App.Maintainers.Request.addServiceTypeWindow({
        title: App.Language.Asset.edit_asset_state
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
};

/**
 * Estado de servicio
 */
App.Maintainers.Request.addServiceStatusWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.add_asset_state,
    resizable: false,
    modal: true,
    width: 380,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.name_state,
                name: 'service_status_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.commentary,
                name: 'service_status_commentary',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/servicestatus/add',
                            success: function(fp, o) {
                                App.Request.ServicesStatus.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.Request.addServiceStatusWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Request.editServiceStatusWindow = function(record) {
    w = new App.Maintainers.Request.addServiceStatusWindow({
        title: App.Language.Asset.edit_asset_state
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
};