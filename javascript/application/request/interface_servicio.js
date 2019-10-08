/* global App, Ext */

App.Request.Service_id = null;

App.Request.Service = Ext.extend(Ext.Panel, {
    title: App.Language.Request.services,
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        items: [
            App.ModuleActions[8009],
            App.ModuleActions[8013],
            {
                xtype: 'spacer',
                width: 10,
                hidden: (App.Security.Actions[8009] === undefined && App.Security.Actions[8013] === undefined ? true : false)

            }, {
                xtype: 'tbseparator',
                width: 10,
                hidden: (App.Security.Actions[8009] === undefined && App.Security.Actions[8013] === undefined ? true : false)
            },
            App.ModuleActions[8010],
            {
                xtype: 'spacer',
                width: 10,
                hidden: (App.Security.Actions[8010] === undefined? true : false)
            }, {
                xtype: 'tbseparator',
                width: 10,
                hidden: (App.Security.Actions[8010] === undefined? true : false)
            },
            App.ModuleActions[8012],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: (App.Security.Actions[8012] === undefined? true : false)
            },
            App.ModuleActions[8011]
        ]
    },
    initComponent: function () {
        this.items = [
            App.Request.Service.formSearching,
            App.Request.Service.Grilla
        ],       
        App.Request.Service.superclass.initComponent.call(this);
    }
});

App.Request.Service.formSearching = {
    xtype: 'form',
    region: 'north',
    id: 'App.Request.Service.FormCentral',
    plugins: [new Ext.ux.OOSubmit()],
    title: App.Language.General.searching,
    cls: 'formCls',
    autoScroll: true,
    frame: true,
    ref: 'form',
    hidden: true,
    height: 210,
    width: '100%',
    margins: '0 5 0 5',
    padding: '0 5 5 5',
    border: true,
    fbar: [{
            text: App.Language.General.search,
            handler: function (b) {
                form = b.ownerCt.ownerCt.getForm();
                node_id = App.Request.Services.Store.baseParams.node_id;
                App.Request.Services.Store.baseParams = form.getSubmitValues();
                App.Request.Services.Store.setBaseParam('node_id', node_id);
                App.Request.Services.Store.load();
            }
        }, {
            text: App.Language.General.clean,
            handler: function (b) {
                form = b.ownerCt.ownerCt.getForm();
                form.reset();
                node_id = App.Request.Services.Store.baseParams.node_id;
                App.Request.Services.Store.baseParams = {};
                App.Request.Services.Store.setBaseParam('node_id', node_id);
                App.Request.Services.Store.load();
            }
        }],
    items: [{
            layout: 'column',
            items: [{
                    columnWidth: .5,
                    layout: 'form',
                    labelWidth: 150,
                    items: [{
                            xtype: 'combo',
                            fieldLabel: 'Tipo de Servicio',
                            anchor: '95%',
                            id: 'App.Request.SearchServiceType',
                            store: App.Request.ServicesType.Store,
                            hiddenName: 'service_type_id',
                            triggerAction: 'all',
                            displayField: 'service_type_name',
                            valueField: 'service_type_id',
                            editable: true,
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            mode: 'remote',
                            minChars: 0,
                            listeners: {
                                'afterrender': function (cb) {
                                    cb.__value = cb.value;
                                    cb.setValue('');
                                    cb.getStore().load({
                                        callback: function () {
                                            cb.setValue(cb.__value);
                                        }
                                    });
                                }
                            }
                        }, {
                            xtype: 'combo',
                            fieldLabel: 'Estado de Servicio',
                            triggerAction: 'all',
                            anchor: '95%',
                            id: 'App.Request.SearchServiceStatus',
                            store: App.Request.ServicesStatus.Store,
                            hiddenName: 'service_status_id',
                            displayField: 'service_status_name',
                            valueField: 'service_status_id',
                            editable: true,
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            mode: 'remote',
                            minChars: 0,
                            listeners: {
                                'afterrender': function (cb) {
                                    cb.__value = cb.value;
                                    cb.setValue('');
                                    cb.getStore().load({
                                        callback: function () {
                                            cb.setValue(cb.__value);
                                        }
                                    });
                                }
                            }
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchServiceUser',
                            fieldLabel: 'Nombre',
                            name: 'user_username',
                            anchor: '95%',
                            disabled: App.Security.Session.user_type === 'A' ? false : true,
                            value: App.Security.Session.user_type === 'A' ? '' : App.Security.Session.user_username
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchServiceMail',
                            fieldLabel: 'Email',
                            name: 'user_email',
                            anchor: '95%',
                            disabled: App.Security.Session.user_type === 'A' ? false : true,
                            value: App.Security.Session.user_type === 'A' ? '' : App.Security.Session.user_email
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchServicePhone',
                            fieldLabel: 'Teléfono',
                            name: 'service_phone',
                            anchor: '95%'
                        }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    labelWidth: 150,
                    id: 'form_column_start_service_date',
                    items: [{
                            columnWidth: .2,
                            layout: 'form',
                            items: [{
                                    xtype: 'label',
                                    text: App.Language.Request.select_a_date_range_to_for_the_request
                                }]
                        }, {
                            columnWidth: .4,
                            layout: 'column',
                            id: 'column_start_service_date',
                            frame: true,
                            items: [{
                                    bodyStyle: 'margin-right: 50px;',
                                    layout: 'form',
                                    id: 'column_start_service_date1',
                                    items: [{
                                            xtype: 'datefield',
                                            id: 'start_service_date',
                                            ref: '../start_service_date',
                                            fieldLabel: App.Language.General.start_date,
                                            name: 'start_service_date',
                                            anchor: '95%',
                                            listeners: {
                                                'select': function (fd, date) {
                                                    fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                                }
                                            }
                                        }]
                                }, {
                                    layout: 'form',
                                    items: [{
                                            xtype: 'datefield',
                                            id: 'end_service_date',
                                            ref: '../end_service_date',
                                            fieldLabel: App.Language.General.end_date,
                                            name: 'end_date',
                                            anchor: '95%',
                                            listeners: {
                                                'select': function (fd, date) {
                                                    fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                                }
                                            }
                                        }]
                                }]
                        }, {
                            xtype: 'spacer',
                            height: 10
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchServiceOrganism',
                            fieldLabel: 'Organismo',
                            name: 'service_organism',
                            anchor: '100%'
                        }]
                }]
        }]
};

App.Request.Service.Grilla = {
    xtype: 'grid',
    id: 'App.Request.Service.Grid',
    ref: 'RequestServiceGrid',
    margins: '5 5 5 5',
    plugins: [new Ext.ux.OOSubmit()],
    region: 'center',
    border: true,
    loadMask: true,
    listeners: {
        'beforerender': function (w) {
            App.Request.Services.Store.load();
        },
        'rowdblclick': function (grid, rowIndex) {
            record = grid.getStore().getAt(rowIndex);
            if (App.Security.Session.user_username === record.data.User.user_username) {
                if (record.data.ServiceStatus.service_status_id === '4') {
                    Ext.FlashMessage.alert(`No es posible editar solicitudes finalizadas.`);
                    return;
                }
                w = new App.Request.editRequestServiceByNodeWindow({title: App.Language.Request.edit_request_service});
                w.show();
                App.Request.Service_id = record.data.service_id;
                Ext.getCmp('App.RequestServiceEdit.Alta').setValue(record.data.ServiceType.service_type_id);
                Ext.getCmp('App.RequestServiceEdit.Alta').setDisabled(true);
                Ext.getCmp('App.RequestServiceEdit.Usuario').setValue(record.data.User.user_username);
                Ext.getCmp('App.RequestServiceEdit.Email').setValue(record.data.User.user_email);
                Ext.getCmp('App.RequestServiceEdit.Telefono').setValue(record.data.service_phone);
                Ext.getCmp('App.RequestServiceEdit.Organismo').setValue(record.data.service_organism);
                Ext.getCmp('App.RequestServiceEdit.Coment').setValue(record.data.service_commentary);

                var iso_date = Date.parseDate(record.data.service_date, "Y-m-d H:i:s");
                Ext.getCmp('App.RequestServiceEdit.Fecha').setValue(iso_date.format("d/m/Y H:i"));
            } else {
                Ext.FlashMessage.alert(`No es posible editar registros de otros usuarios.`);
            }
        }
    },
    viewConfig: {
        forceFit: true,
        getRowClass: function (record, index) {
            var c = record.get('service_status_id');
            if (c === 3) {
                return 'heavenly-row';
            }
        }
    },
    store: App.Request.Services.Store,
    columns: [
        new Ext.grid.CheckboxSelectionModel(),
        {
            header: App.Language.Core.location,
            sortable: true,
            dataIndex: 'Node',
            align: 'center',
            renderer: function (Node, metaData, record) {
                return "<div style='background-image: url(" + record.data.icon + "); background-repeat: no-repeat; height: 16; width: 16; float: left; padding-left: 20; padding-top: 2'><a href='javascript: App.Request.Service.expand(" + record.data.node_id + ")'>" + Node.node_name + "</a></div>";
            }
        }, {
            header: 'Tipo',
            sortable: true,
            width: 50,
            dataIndex: 'ServiceType',
            renderer: function (ServiceType) {
                return ServiceType.service_type_name;
            }
        }, {
            dataIndex: 'ServiceStatus',
            header: 'Estado',
            sortable: true,
            width: 50,
            renderer: function (ServiceStatus) {
                return ServiceStatus.service_status_name;
            }
        }, {
            dataIndex: 'service_organism',
            header: 'Organismo',
            width: 100,
            sortable: true
        }, {
            dataIndex: 'User',
            header: 'Nombre',
            width: 60,
            sortable: true,
            renderer: function (User) {
                return User.user_username;
            }
        }, {
            dataIndex: 'service_phone',
            header: 'Teléfono',
            align: 'center',
            width: 55,
            sortable: true
        }, {
            xtype: 'datecolumn',
            header: 'Fecha',
            sortable: true,
            dataIndex: 'service_date',
            width: 60,
            format: App.General.DefaultDateTimeFormat,
            align: 'center'
        }, {                    
            dataIndex: 'service_commentary',
            header: 'Requerimiento',
            width: 100,
            sortable: true
        }
    ],
    sm: new Ext.grid.CheckboxSelectionModel({
        singleSelect: true
    })
};

App.Request.addRequestServiceByNodeWindow = Ext.extend(Ext.Window, {
    width: (screen.width < 500) ? screen.width - 50 : 500,
    height: 370,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'afterrender': function() {
            Ext.getCmp('App.Request.Usuario').setValue(App.Security.Session.user_username);
            Ext.getCmp('App.Request.Email').setValue(App.Security.Session.user_email);
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            plugins: [new Ext.ux.OOSubmit()],
            bodyStyle: 'padding: 10 10px 10',
            items: [{
                xtype: 'fieldset',
                title: 'Datos Solicitante',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre de Usuario',
                    name: 'user_name',
                    id: 'App.Request.Usuario',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Email',
                    name: 'user_email',
                    id: 'App.Request.Email',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                ref: 'solicitud',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: 'Teléfono',
                    name: 'service_phone',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Organismo',
                    name: 'service_organism',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Tipo de Servicio',
                    anchor: '100%',
                    id: 'App.Request.ServiceType',
                    store: App.Request.ServicesType.Store,
                    hiddenName: 'service_type_id',
                    triggerAction: 'all',
                    displayField: 'service_type_name',
                    valueField: 'service_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    anchor: '100%',
                    name: 'service_commentary',
                    fieldLabel: 'Requerimiento',
                    allowBlank: false
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.save,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/service/add',
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            success: function(fp, o) {
                                if (o.result.success === "false") {
                                    Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                } else {
                                    App.Request.Services.Store.load();

                                    b.ownerCt.ownerCt.ownerCt.close();
                                    Ext.FlashMessage.alert(o.result.msg);

                                }
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Request.addRequestByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.editRequestServiceByNodeWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 390,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            bodyStyle: 'padding: 10 10px 10',
            items: [{
                xtype: 'fieldset',
                title: 'Datos Solicitante',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre de Usuario',
                    name: 'user_name',
                    id: 'App.RequestServiceEdit.Usuario',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Email',
                    name: 'user_email',
                    id: 'App.RequestServiceEdit.Email',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                ref: 'solicitud',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Fecha',
                    name: 'service_date',
                    id: 'App.RequestServiceEdit.Fecha',
                    anchor: '100%'
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Tipo de Servicio',
                    anchor: '100%',
                    id: 'App.RequestServiceEdit.Alta',
                    store: App.Request.ServicesType.Store,
                    hiddenName: 'service_type_id',
                    triggerAction: 'all',
                    displayField: 'service_type_name',
                    valueField: 'service_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Organismo',
                    id: 'App.RequestServiceEdit.Organismo',
                    name: 'service_organism',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Teléfono',
                    id: 'App.RequestServiceEdit.Telefono',
                    name: 'service_phone',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    fieldLabel: 'Requerimiento',
                    id: 'App.RequestServiceEdit.Coment',
                    name: 'service_commentary',
                    anchor: '100%',
                    allowBlank: false
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: 'Editar',
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/service/update',
                            params: {
                                service_id: App.Request.Service_id
                            },
                            success: function(fp, o) {
                                if (o.result.success === "false") {
                                    Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                } else {
                                    App.Request.Services.Store.load();

                                    b.ownerCt.ownerCt.ownerCt.close();
                                    Ext.FlashMessage.alert(o.result.msg);

                                }
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Request.editRequestServiceByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.exportServiceListByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.export_request,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    height: 150,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                labelWidth: 130,
                padding: 5,
                items: [{
                        xtype: 'textfield',
                        fieldLabel: App.Language.General.file_name,
                        id: 'App.Request.SearchRequestNombre',
                        value: `${App.Language.General.services} ${new Date().add(Date.DAY, 0).format('d-m-Y')}`,
                        anchor: '100%',
                        name: 'file_name',
                        maskRe: /^[a-zA-Z0-9_]/,
                        regex: /^[a-zA-Z0-9_]/,
                        allowBlank: false
                    }],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        xtype: 'button',
                        text: App.Language.General.eexport,
                        handler: function (b) {
                            Ext.Ajax.request({
                                waitMsg: App.Language.General.message_generating_file,
                                url: 'index.php/request/service/export',
                                method: 'POST',
                                params: {
                                    node_id: App.Interface.selectedNodeId,
                                    file_name: Ext.getCmp('App.Request.SearchRequestNombre').getValue(),
                                    service_type_id: Ext.getCmp('App.Request.SearchServiceType').getValue(),
                                    service_status_id: Ext.getCmp('App.Request.SearchServiceStatus').getValue(),
                                    user_username: Ext.getCmp('App.Request.SearchServiceUser').getValue(),
                                    user_email: Ext.getCmp('App.Request.SearchServiceMail').getValue(),
                                    service_phone: Ext.getCmp('App.Request.SearchServicePhone').getValue(),
                                    start_date: Ext.getCmp('start_service_date').getValue(),
                                    end_date: Ext.getCmp('end_service_date').getValue(),
                                    service_organism: Ext.getCmp('App.Request.SearchServiceOrganism').getValue()

                                },
                                success: function (response) {
                                    response = Ext.decode(response.responseText);
                                    document.location = response.file;
                                    b.ownerCt.ownerCt.ownerCt.close();
                                },
                                failure: function (response) {
                                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                }
                            });
                        }
                    }]
            }];
        App.Request.exportListByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.historialServiceWindow = Ext.extend(Ext.Window, {
    resizable: false,
    modal: true,
    border: true,
    width: (screen.width < 750) ? screen.width - 50 : 750,
    height: 500,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                store: App.Request.ServicesLog.Store,
                height: 420,
                viewConfig: {
                    forceFit: true
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        dataIndex: 'User',
                        header: 'Usuario',
                        width: 70,
                        sortable: true,
                        renderer: function(User) {
                            return User.user_username;
                        }
                    }, {
                        dataIndex: 'User',
                        header: 'Email',
                        width: 80,
                        sortable: true,
                        renderer: function(User) {
                            return User.user_email;
                        }
                    }, {
                        xtype: 'datecolumn',
                        header: 'Fecha Acción',
                        sortable: true,
                        dataIndex: 'service_log_date',
                        width: 60,
                        format: App.General.DefaultDateTimeFormat,
                        align: 'center'
                    }, {
                        dataIndex: 'service_log_detail',
                        header: 'Acción',
                        width: 180,
                        sortable: true
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    App.Request.Service_id = null;
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Request.historialServiceWindow.superclass.initComponent.call(this);
    }
});

App.Request.changeServiceStatusWindow = Ext.extend(Ext.Window, {
    resizable: false,
    modal: true,
    border: true,
    width: (screen.width < 750) ? screen.width - 50 : 750,
    height: 500,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            plugins: [new Ext.ux.OOSubmit()],
            bodyStyle: 'padding: 10 10px 10',
            items: [{
                xtype: 'fieldset',
                title: 'Datos Solicitante',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre de Usuario',
                    name: 'user_name',
                    id: 'App.Request.Service.Usuario',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Email',
                    name: 'user_email',
                    id: 'App.Request.Service.Email',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                ref: 'solicitud',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Teléfono',
                    name: 'service_phone',
                    id: 'App.Request.Service.Phone',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Organismo',
                    name: 'service_organism',
                    id: 'App.Request.Service.Organism',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Tipo de Servicio',
                    anchor: '100%',
                    id: 'App.Request.Service.ServiceType',
                    store: App.Request.ServicesType.Store,
                    hiddenName: 'service_type_id',
                    triggerAction: 'all',
                    displayField: 'service_type_name',
                    valueField: 'service_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Estado actual',
                    anchor: '100%',
                    id: 'App.Request.Service.ServiceStatus',
                    store: App.Request.ServicesStatus.Store,
                    hiddenName: 'service_status_id',
                    triggerAction: 'all',
                    displayField: 'service_status_name',
                    valueField: 'service_status_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Estado nuevo',
                    anchor: '100%',
                    id: 'App.Request.Service.ServiceStatusNew',
                    store: App.Request.ServicesStatus.Store,
                    hiddenName: 'service_status_id',
                    triggerAction: 'all',
                    displayField: 'service_status_name',
                    valueField: 'service_status_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'displayfield',
                    anchor: '100%',
                    name: 'service_commentary',
                    fieldLabel: 'Requerimiento',
                    id: 'App.Request.Service.Commentary'
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                id: 'App.Service.Request.btnChangeServiceStatusWindow',
                text: App.Language.General.save,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/request/service/update',
                            params: {
                                service_id: App.Request.Service_id,
                                service_status_id: Ext.getCmp('App.Request.Service.ServiceStatusNew').getValue()
                            },
                            success: function(fp, o) {
                                if (o.result.success === "false") {
                                    Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                } else {
                                    App.Request.Services.Store.load();
                                    b.ownerCt.ownerCt.ownerCt.close();
                                    Ext.FlashMessage.alert(o.result.msg);
                                }
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Request.changeServiceStatusWindow.superclass.initComponent.call(this);
    }
});

App.Request.Service.expand = function(node_id) {
    App.Interface.selectedNodeId = node_id;
    node = Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id);
    App.Security.checkNodeAccess(node);
};