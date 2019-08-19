App.Request.Solicitud_id = null;
App.Request.allowRootGui = true;

App.Interface.addToModuleMenu('request', App.ModuleActions[8000]);

App.Request.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [
            new App.Request.Asset(),
            new App.Request.Service(),
            //new App.Request.Information()
        ];
        App.Request.Principal.superclass.initComponent.call(this);
    }
});

App.Request.Asset = Ext.extend(Ext.Panel, {
    title: App.Language.Asset.assets,
    border: false,
    id: 'App.Request.Principal',
    loadMask: true,
    layout: 'border',
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        items: [
            App.ModuleActions[8001],
            {
                xtype: 'spacer',
                width: 10,
                hidden: (App.Security.Actions[8001] === undefined ? true : false)
            }, {
                xtype: 'tbseparator',
                width: 10
            },
            App.ModuleActions[8002],
            {
                xtype: 'spacer',
                width: 10,
                hidden: (App.Security.Actions[8002] === undefined ? true : false)
            },
            App.ModuleActions[8003],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: (App.Security.Actions[8003] === undefined ? true : false)
            },
            App.ModuleActions[8004],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: (App.Security.Actions[8004] === undefined ? true : false)
            },
            App.ModuleActions[8006],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: (App.Security.Actions[8006] === undefined ? true : false)
            },
            App.ModuleActions[8005]
        ]
    },
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                region: 'north',
                id: 'App.Request.FormCentral',
                plugins: [new Ext.ux.OOSubmit()],
                title: App.Language.General.searching,
                cls: 'formCls',
                autoScroll: true,
                frame: true,
                ref: 'form',
                hidden: true,
                height: 242,
                margins: '0 5 0 5',
                padding: '0 5 5 5',
                border: true,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Request.Solicitudes.Store.baseParams.node_id;
                        App.Request.Solicitudes.Store.baseParams = form.getSubmitValues();
                        App.Request.Solicitudes.Store.setBaseParam('node_id', node_id);
                        App.Request.Solicitudes.Store.load();
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Request.Solicitudes.Store.baseParams.node_id;
                        form.reset();
                        App.Request.Solicitudes.Store.baseParams = {};
                        App.Request.Solicitudes.Store.setBaseParam('node_id', node_id);
                        App.Request.Solicitudes.Store.load();
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
                            fieldLabel: 'Tipo de Solicitud',
                            anchor: '95%',
                            id: 'App.Request.SearchTipo',
                            store: App.Request.SolicitudTipos.Store,
                            hiddenName: 'solicitud_type_id',
                            triggerAction: 'all',
                            displayField: 'solicitud_type_nombre',
                            valueField: 'solicitud_type_id',
                            editable: true,
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            mode: 'remote',
                            minChars: 0,
                            listeners: {
                                'afterrender': function(cb) {
                                    cb.__value = cb.value;
                                    cb.setValue('');
                                    cb.getStore().load({
                                        callback: function() {
                                            cb.setValue(cb.__value);
                                        }
                                    });
                                }
                            }
                        }, {
                            xtype: 'combo',
                            fieldLabel: 'Estado de Solicitud',
                            triggerAction: 'all',
                            anchor: '95%',
                            id: 'App.Request.SearchEstado',
                            store: App.Request.SolicitudEstados.Store,
                            hiddenName: 'solicitud_estado_id',
                            displayField: 'solicitud_estado_nombre',
                            valueField: 'solicitud_estado_id',
                            editable: true,
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            mode: 'remote',
                            minChars: 0,
                            listeners: {
                                'afterrender': function(cb) {
                                    cb.__value = cb.value;
                                    cb.setValue('');
                                    cb.getStore().load({
                                        callback: function() {
                                            cb.setValue(cb.__value);
                                        }
                                    });
                                }
                            }
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchFolio',
                            fieldLabel: 'Nº Folio',
                            name: 'solicitud_folio',
                            anchor: '95%'
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchUser',
                            fieldLabel: 'Nombre',
                            name: 'user_username',
                            anchor: '95%',
                            disabled: App.Security.Session.user_type === 'A' ? false : true,
                            value: App.Security.Session.user_type === 'A' ? '' : App.Security.Session.user_username
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchMail',
                            fieldLabel: 'Email',
                            name: 'user_email',
                            anchor: '95%',
                            disabled: App.Security.Session.user_type === 'A' ? false : true,
                            value: App.Security.Session.user_type === 'A' ? '' : App.Security.Session.user_email
                        }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        labelWidth: 150,
                        id: 'form_column_start_date',
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
                            id: 'column_start_date',
                            frame: true,
                            items: [{
                                columnWidth: .5,
                                layout: 'form',
                                id: 'column_start_date1',
                                items: [{
                                    xtype: 'datefield',
                                    id: 'start_date',
                                    ref: '../start_date',
                                    fieldLabel: App.Language.General.start_date,
                                    name: 'start_date',
                                    anchor: '95%',
                                    listeners: {
                                        'select': function(fd, date) {
                                            fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                        }
                                    }
                                }]
                            }, {
                                columnWidth: .5,
                                layout: 'form',
                                items: [{
                                    xtype: 'datefield',
                                    id: 'end_date',
                                    ref: '../end_date',
                                    fieldLabel: App.Language.General.end_date,
                                    name: 'end_date',
                                    anchor: '95%',
                                    listeners: {
                                        'select': function(fd, date) {
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
                            id: 'App.Request.SearchFactura',
                            fieldLabel: 'Nombre Factura',
                            name: 'solicitud_factura_nombre',
                            anchor: '100%'
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchNumFactura',
                            fieldLabel: 'Nº Factura',
                            name: 'solicitud_factura_numero',
                            anchor: '100%'
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchOC',
                            fieldLabel: 'Nombre Orden de Compra',
                            name: 'solicitud_oc_nombre',
                            anchor: '100%'
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchNumOC',
                            fieldLabel: 'Nº Orden de Compra',
                            name: 'solicitud_oc_numero',
                            anchor: '100%'
                        }]
                    }]
                }]
            }, {
                xtype: 'grid',
                id: 'App.Request.Grid',
                ref: 'RequestGrid',
                margins: '5 5 5 5',
                plugins: [new Ext.ux.OOSubmit()],
                region: 'center',
                border: true,
                loadMask: true,
                listeners: {
                    'beforerender': function(w) {
                        App.Request.Solicitudes.Store.load();
                    },
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        if (App.Security.Session.user_username === record.data.User.user_username) {
                            w = new App.Request.editRequestByNodeWindow({ title: 'Editar Solicitud' });
                            w.show();
                            
                            App.Request.Solicitud_id = record.data.solicitud_id;
                            Ext.getCmp('App.RequestEdit.Alta').setValue(record.data.SolicitudType.solicitud_type_id);
                            Ext.getCmp('App.RequestEdit.Alta').setDisabled(true);
                            Ext.getCmp('App.RequestEdit.Usuario').setValue(record.data.User.user_username);
                            Ext.getCmp('App.RequestEdit.Email').setValue(record.data.User.user_email);
                            Ext.getCmp('App.RequestEdit.Factura').setValue(record.data.solicitud_factura_nombre);
                            Ext.getCmp('App.RequestEdit.FacturaNum').setValue(record.data.solicitud_factura_numero);
                            Ext.getCmp('App.RequestEdit.OC').setValue(record.data.solicitud_oc_nombre);
                            Ext.getCmp('App.RequestEdit.OCNum').setValue(record.data.solicitud_oc_numero);
                            Ext.getCmp('App.RequestEdit.Coment').setValue(record.data.solicitud_comen_user);
                            Ext.getCmp('App.RequestEdit.ComentAdmin').setValue(record.data.solicitud_comen_admin);
                            Ext.getCmp('App.RequestEdit.Folio').setValue(record.data.solicitud_folio);

                            var iso_date = Date.parseDate(record.data.solicitud_fecha, "Y-m-d H:i:s");
                            Ext.getCmp('App.RequestEdit.Fecha').setValue(iso_date.format("d/m/Y H:i"));
                        } else {
                            Ext.FlashMessage.alert(`No es posible editar registros de otros usuarios.`);
                        }
                    }
                },
                viewConfig: {
                    forceFit: true,
                    getRowClass: function(record, index) {
                        var c = record.get('solicitud_estado_id');
                        if (c == 3) {
                            return 'heavenly-row';
                        }
                    }
                },
                store: App.Request.Solicitudes.Store,
                columns: [
                    new Ext.grid.CheckboxSelectionModel(),
                    {
                        header: 'Tipo',
                        sortable: true,
                        width: 50,
                        dataIndex: 'SolicitudType',
                        renderer: function(SolicitudType) {
                            return SolicitudType.solicitud_type_nombre;
                        }
                    }, {
                        dataIndex: 'SolicitudEstado',
                        header: 'Estado',
                        sortable: true,
                        width: 50,
                        renderer: function(SolicitudEstado) {
                            return SolicitudEstado.solicitud_estado_nombre;
                        }
                    }, {
                        dataIndex: 'solicitud_folio',
                        header: 'Nº Folio',
                        align: 'center',
                        width: 55,
                        sortable: true
                    }, {
                        dataIndex: 'User',
                        header: 'Nombre',
                        width: 60,
                        sortable: true,
                        renderer: function(User) {
                            return User.user_username;
                        }
                    }, {
                        xtype: 'datecolumn',
                        header: 'Fecha Solicitud',
                        sortable: true,
                        dataIndex: 'solicitud_fecha',
                        width: 60,
                        format: App.General.DefaultDateTimeFormat,
                        align: 'center'
                    }, {
                        dataIndex: 'solicitud_factura_nombre',
                        header: 'Factura',
                        width: 68,
                        sortable: true,
                        renderer: function(val, metadata, record) {
                            return `<a href='index.php/request/solicitud/downloadFactura/${record.data.solicitud_id}'>${val}</a>`;
                        }
                    }, {
                        dataIndex: 'solicitud_factura_numero',
                        header: 'Nº Factura',
                        width: 45,
                        sortable: true
                    }, {
                        dataIndex: 'solicitud_oc_nombre',
                        header: 'OC',
                        width: 68,
                        sortable: true,
                        renderer: function(val, metadata, record) {
                            return `<a href='index.php/request/solicitud/downloadOC/${record.data.solicitud_id}'>${val}</a>`;
                        }
                    }, {
                        dataIndex: 'solicitud_oc_numero',
                        header: 'Nº OC',
                        width: 45,
                        sortable: true
                    }, {
                        dataIndex: 'solicitud_comen_user',
                        header: 'Comentario',
                        width: 100,
                        sortable: true
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel({
                    singleSelect: true
                })
            }],
            App.Request.Asset.superclass.initComponent.call(this);
    }
});

App.Request.editRequestByNodeWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 550,
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
                    id: 'App.RequestEdit.Usuario',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Email',
                    name: 'user_email',
                    id: 'App.RequestEdit.Email',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                ref: 'solicitud',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Folio',
                    name: 'solicitud_folio',
                    id: 'App.RequestEdit.Folio',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Fecha',
                    name: 'solicitud_fecha',
                    id: 'App.RequestEdit.Fecha',
                    anchor: '100%'
                }, {
                    xtype: 'combo',
                    fieldLabel: 'Tipo de Solicitud',
                    anchor: '100%',
                    id: 'App.RequestEdit.Alta',
                    store: App.Request.SolicitudTipos.Store,
                    hiddenName: 'solicitud_type_id',
                    triggerAction: 'all',
                    displayField: 'solicitud_type_nombre',
                    valueField: 'solicitud_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'fileuploadfield',
                    emptyText: 'Seleccione Factura',
                    fieldLabel: 'Factura',
                    ref: 'solicitud_factura',
                    id: 'App.RequestEdit.Factura',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'solicitud_factura_nombre',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Nº Factura',
                    id: 'App.RequestEdit.FacturaNum',
                    name: 'solicitud_factura_numero',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'fileuploadfield',
                    emptyText: 'Seleccione Orden de Compra',
                    fieldLabel: 'Orden de Compra',
                    ref: 'solicitud_oc',
                    id: 'App.RequestEdit.OC',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'solicitud_oc_nombre',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Nº Orden de Compra',
                    id: 'App.RequestEdit.OCNum',
                    name: 'solicitud_oc_numero',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    anchor: '100%',
                    id: 'App.RequestEdit.Coment',
                    name: 'solicitud_comen_user',
                    fieldLabel: 'Comentarios',
                    allowBlank: false

                }]
            }, {
                xtype: 'fieldset',
                title: 'Rechazo',
                hidden: record.data.SolicitudEstado.solicitud_estado_id == 3 ?false:true,
                items: [{
                    xtype: 'displayfield',
                    anchor: '100%',
                    name: 'solicitud_comen_admin',
                    fieldLabel: 'Rechazada Por',
                    id: 'App.RequestEdit.ComentAdmin',
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
                    if (Ext.getCmp('App.RequestEdit.Factura').getValue() == Ext.getCmp('App.RequestEdit.OC').getValue()) {
                        Ext.FlashMessage.alert('Los Documentos Factura y Orden de Compra no pueden ser Iguales');
                    } else {
                        form = b.ownerCt.ownerCt.getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: 'index.php/request/solicitud/update',
                                params: {
                                    solicitud_id: App.Request.Solicitud_id
                                },
                                success: function(fp, o) {
                                    if (o.result.success === "false") {
                                        Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                    } else {
                                        App.Request.Solicitudes.Store.load();

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
                }
            }]
        }];
        App.Request.editRequestByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.Principal.listener = function(node) {
    if (node && node.id) {
        App.Request.Services.Store.setBaseParam('node_id', node.id);
        App.Request.Services.Store.load();
        
        App.Request.Solicitudes.Store.setBaseParam('node_id', node.id);
        App.Request.Solicitudes.Store.load();
    }
};

App.Request.addAprobarWindow = Ext.extend(Ext.Window, {
    width: (screen.width < 500) ? screen.width - 50 : 500,
    height: 420,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            bodyStyle: 'padding: 10 10px 10',
            items: [{
                xtype: 'fieldset',
                title: 'Datos Solicitante',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Folio',
                    name: 'solicitud_folio',
                    id: 'App.Request.Folio',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Fecha de Creación',
                    name: 'node_name',
                    id: 'App.Request.Fecha',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Tipo de Solicitud',
                    name: 'solicitud_type',
                    id: 'App.Request.TipoSolicitud',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre de Usuario',
                    name: 'node_name',
                    id: 'App.Request.Usuario',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Email',
                    name: 'node_name',
                    id: 'App.Request.Email',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Factura',
                    name: 'solicitud_factura',
                    id: 'App.Request.FacturaNombre',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Nº Factura',
                    name: 'solicitud_factura_numero',
                    id: 'App.Request.FacturaNumero',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Orden de Compra',
                    name: 'solicitud_oc_nombre',
                    id: 'App.Request.OCNombre',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Nº Orden de Compra',
                    name: 'solicitud_oc_numero',
                    id: 'App.Request.OCNumero',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Comentarios',
                    name: 'solicitud_comentarios',
                    id: 'App.Request.Comentario',
                    anchor: '100%'
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: 'Aprobar',
                ref: '../saveButton',
                handler: function(b) {
                    Ext.Ajax.request({
                        waitMsg: App.Language.General.message_generating_file,
                        url: 'index.php/request/solicitud/approve',
                        timeout: 10000000000,
                        params: {
                            solicitud_id: App.Request.Solicitud_id
                        },
                        success: function(response) {
                            response = Ext.decode(response.responseText);
                            b.ownerCt.ownerCt.ownerCt.close();
                            App.Request.Solicitudes.Store.load();
                            Ext.FlashMessage.alert(response.msg);

                        },
                        failure: function(response) {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                        }
                    });

                }
            }]
        }];
        App.Request.addAprobarWindow.superclass.initComponent.call(this);
    }
});

App.Request.addRechazarWindow = Ext.extend(Ext.Window, {
    width: (screen.width < 500) ? screen.width - 50 : 500,
    height: 520,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            bodyStyle: 'padding: 10 10px 10',
            items: [{
                xtype: 'fieldset',
                title: 'Datos Solicitante',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Folio',
                    name: 'solicitud_folio',
                    id: 'App.RequestRechazar.Folio',
                    anchor: '100%'
                }, {

                    xtype: 'displayfield',
                    fieldLabel: 'Fecha de Creación',
                    name: 'node_name',
                    id: 'App.RequestRechazar.Fecha',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Tipo de Solicitud',
                    name: 'solicitud_type',
                    id: 'App.RequestRechazar.TipoSolicitud',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Nombre de Usuario',
                    name: 'node_name',
                    id: 'App.RequestRechazar.Usuario',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Email',
                    name: 'node_name',
                    id: 'App.RequestRechazar.Email',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: 'Factura',
                    name: 'solicitud_factura',
                    id: 'App.RequestRechazar.FacturaNombre',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Nº Factura',
                    name: 'solicitud_factura_numero',
                    id: 'App.RequestRechazar.FacturaNumero',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Orden de Compra',
                    name: 'solicitud_oc_nombre',
                    id: 'App.RequestRechazar.OCNombre',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Nº Orden de Compra',
                    name: 'solicitud_oc_numero',
                    id: 'App.RequestRechazar.OCNumero',
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: 'Comentarios',
                    name: 'solicitud_comentarios',
                    id: 'App.RequestRechazar.Comentario',
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Rechazo',
                items: [{
                    xtype: 'textarea',
                    anchor: '100%',
                    name: 'request_comentario',
                    fieldLabel: 'Rechazada Por',
                    id: 'App.RequestRechazar.ComentarioAdmin',
                    allowBlank: false
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: 'Rechazar',
                ref: '../saveButton',
                handler: function(b) {
                    if (Ext.getCmp('App.RequestRechazar.ComentarioAdmin').getValue()) {
                        Ext.Ajax.request({
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/request/solicitud/rejects',
                            timeout: 10000000000,
                            params: {
                                solicitud_id: App.Request.Solicitud_id,
                                solicitud_comen_admin: Ext.getCmp('App.RequestRechazar.ComentarioAdmin').getValue()
                            },
                            success: function(response) {
                                response = Ext.decode(response.responseText);
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Request.Solicitudes.Store.load();
                                Ext.FlashMessage.alert(response.msg);

                            },
                            failure: function(response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            }
                        });
                    } else {
                        Ext.FlashMessage.alert('El campo de comentario es obligatorio');
                    }


                }
            }]
        }];
        App.Request.addRechazarWindow.superclass.initComponent.call(this);
    }
});

App.Request.addRequestByNodeWindow = Ext.extend(Ext.Window, {
    width: (screen.width < 500) ? screen.width - 50 : 500,
    height: 430,
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
                    xtype: 'combo',
                    fieldLabel: 'Tipo de Solicitud',
                    anchor: '100%',
                    id: 'App.Request.Alta',
                    store: App.Request.SolicitudTipos.Store,
                    hiddenName: 'solicitud_type_id',
                    triggerAction: 'all',
                    displayField: 'solicitud_type_nombre',
                    valueField: 'solicitud_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false
                }, {
                    xtype: 'fileuploadfield',
                    emptyText: 'Seleccione Factura',
                    fieldLabel: 'Factura',
                    ref: 'solicitud_factura',
                    id: 'App.Request.Factura',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'solicitud_factura_nombre',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Nº Factura',
                    name: 'solicitud_factura_numero',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'fileuploadfield',
                    emptyText: 'Seleccione Orden de Compra',
                    fieldLabel: 'Orden de Compra',
                    ref: 'solicitud_oc',
                    id: 'App.Request.OC',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'solicitud_oc_nombre',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Nº Orden de Compra',
                    name: 'solicitud_oc_numero',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    anchor: '100%',
                    name: 'solicitud_comen_user',
                    fieldLabel: 'Comentarios',
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
                    if (Ext.getCmp('App.Request.Factura').getValue() == Ext.getCmp('App.Request.OC').getValue()) {
                        Ext.FlashMessage.alert('Los Documentos Factura y Orden de Compra no pueden ser Iguales');
                    } else {
                        form = b.ownerCt.ownerCt.getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: 'index.php/request/solicitud/add',
                                params: {
                                    node_id: App.Interface.selectedNodeId
                                },
                                success: function(fp, o) {
                                    if (o.result.success === "false") {
                                        Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                    } else {
                                        App.Request.Solicitudes.Store.load();

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
                }
            }]
        }];
        App.Request.addRequestByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.exportListByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.export_request,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    height: 150,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            labelWidth: 130,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                id: 'App.Request.SearchNombre',
                value: App.Language.Request.requests + ' ' + new Date().add(Date.DAY, 0).format('d-m-Y'),
                anchor: '100%',
                name: 'file_name',
                maskRe: /^[a-zA-Z0-9_]/,
                regex: /^[a-zA-Z0-9_]/,
                allowBlank: false
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.eexport,
                handler: function(b) {
                    Ext.Ajax.request({
                        waitMsg: App.Language.General.message_generating_file,
                        url: 'index.php/request/solicitud/export',
                        method: 'POST',
                        params: {
                            node_id: App.Interface.selectedNodeId,
                            file_name: Ext.getCmp('App.Request.SearchNombre').getValue(),
                            solicitud_type_id: Ext.getCmp('App.Request.SearchTipo').getValue(),
                            solicitud_estado_id: Ext.getCmp('App.Request.SearchEstado').getValue(),
                            solicitud_folio: Ext.getCmp('App.Request.SearchFolio').getValue(),
                            user_username: Ext.getCmp('App.Request.SearchUser').getValue(),
                            user_email: Ext.getCmp('App.Request.SearchMail').getValue(),
                            start_date: Ext.getCmp('start_date').getValue(),
                            end_date: Ext.getCmp('end_date').getValue(),
                            solicitud_factura_nombre: Ext.getCmp('App.Request.SearchFactura').getValue(),
                            solicitud_factura_numero: Ext.getCmp('App.Request.SearchNumFactura').getValue(),
                            solicitud_oc_nombre: Ext.getCmp('App.Request.SearchOC').getValue(),
                            solicitud_oc_numero: Ext.getCmp('App.Request.SearchNumOC').getValue()

                        },
                        success: function(response) {
                            response = Ext.decode(response.responseText);
                            document.location = response.file;
                            b.ownerCt.ownerCt.ownerCt.close();
                        },
                        failure: function(response) {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                        }
                    });
                }
            }]
        }];
        App.Request.exportListByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.historialWindow = Ext.extend(Ext.Window, {
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
                store: App.Request.SolicitudLog.Store,
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
                        dataIndex: 'solicitud_log_fecha',
                        width: 60,
                        format: App.General.DefaultDateTimeFormat,
                        align: 'center'
                    }, {
                        dataIndex: 'solicitud_log_detalle',
                        header: 'Acción',
                        width: 180,
                        sortable: true
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    App.Request.Solicitud_id = null;
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Request.historialWindow.superclass.initComponent.call(this);
    }
});