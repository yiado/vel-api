App.Request.Service_id = null;

App.Request.allowRootGui = true;

App.Request.Service = Ext.extend(Ext.Panel, {
    title: App.Language.General.services,
    id: 'App.Request.Principal',
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        items: [
            App.ModuleActions[8009],
            {
                xtype: 'spacer',
                width: 10
            }, {
                xtype: 'tbseparator',
                width: 10
            },
            App.ModuleActions[8010],
            {
                xtype: 'spacer',
                width: 10
            }, {
                xtype: 'tbseparator',
                width: 10
            },
            App.ModuleActions[8012],
            {
                xtype: 'tbseparator',
                width: 10
            },
            App.ModuleActions[8011]
        ]
    },
    initComponent: function () {
        this.items = [{
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
                height: 200,
                margins: '0 5 0 5',
                padding: '0 5 5 5',
                border: true,
                fbar: [{
                        text: App.Language.General.search,
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            App.Request.Service.Store.baseParams = form.getSubmitValues();
                            App.Request.Service.Store.load();
                        }
                    }, {
                        text: App.Language.General.clean,
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            form.reset();
                            App.Request.Service.Store.baseParams = {};
                            App.Request.Service.Store.load();
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
                                                columnWidth: .5,
                                                layout: 'form',
                                                id: 'column_start_service_date1',
                                                items: [{
                                                        xtype: 'datefield',
                                                        id: 'start_service_date',
                                                        ref: '../start_date',
                                                        fieldLabel: App.Language.General.start_date,
                                                        name: 'start_date',
                                                        anchor: '95%',
                                                        listeners: {
                                                            'select': function (fd, date) {
                                                                fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                columnWidth: .5,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'datefield',
                                                        id: 'end_service_date',
                                                        ref: '../end_date',
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
            }, {
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
                        App.Request.Service.Store.load();
                    },
                    'rowdblclick': function (grid, rowIndex) {
                        if (App.Security.Session.user_type != 'A') {
                            record = grid.getStore().getAt(rowIndex);

                            w = new App.Request.editRequestByNodeWindow({title: 'Editar Servicio'});
                            w.show();
                            /*App.Request.Service_id = record.data.service_id;
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
                            Ext.getCmp('App.RequestEdit.Fecha').setValue(iso_date.format("d/m/Y H:i"));*/

                        }
                    }
                },
                viewConfig: {
                    forceFit: true,
                    getRowClass: function (record, index) {
                        var c = record.get('service_status_id');
                        if (c == 3) {
                            return 'heavenly-row';
                        }
                    }
                },
                store: App.Request.Service.Store,
                columns: [
                    new Ext.grid.CheckboxSelectionModel(),
                    {
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
                        dataIndex: 'User',
                        header: 'Nombre',
                        width: 60,
                        sortable: true,
                        renderer: function (User) {
                            return User.user_username;
                        }
                    }, {
                        dataIndex: 'service_phone',
                        header: 'Telefono',
                        align: 'center',
                        width: 55,
                        sortable: true
                    }, {
                        xtype: 'datecolumn',
                        header: 'Fecha Servicio',
                        sortable: true,
                        dataIndex: 'service_date',
                        width: 60,
                        format: App.General.DefaultDateTimeFormat,
                        align: 'center'
                    }, {
                        dataIndex: 'service_commentary',
                        header: 'Comentario',
                        width: 100,
                        sortable: true
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel({
                    singleSelect: true
                })
            }],
                App.Request.Service.superclass.initComponent.call(this);
    }
});