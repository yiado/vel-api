/* global App, Ext */

App.Request.Rdi_id = null;

App.Request.Rdi = Ext.extend(Ext.Panel, {
    title: 'RDI',
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        items: [
            App.ModuleActions[8016],
            App.ModuleActions[8019],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: false
            },
            App.ModuleActions[8017],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: false
            },
            App.ModuleActions[8020],
            {
                xtype: 'tbseparator',
                width: 10,
                hidden: false
            },
            App.ModuleActions[8018]
        ]
    },
    initComponent: function () {
        this.items = [
            App.Request.Rdi.formSearching,
            App.Request.Rdi.Grilla
        ],
                App.Request.Rdi.superclass.initComponent.call(this);
    }
});

App.Request.Rdi.formSearching = {
    xtype: 'form',
    region: 'north',
    id: 'App.Request.Rdi.FormCentral',
    plugins: [new Ext.ux.OOSubmit()],
    title: App.Language.General.searching,
    cls: 'formCls',
    autoScroll: true,
    frame: true,
    ref: 'form',
    hidden: true,
    height: 240,
    width: '100%',
    margins: '0 5 0 5',
    padding: '0 5 5 5',
    border: true,
    fbar: [{
            text: App.Language.General.search,
            handler: function (b) {
                form = b.ownerCt.ownerCt.getForm();
                node_id = App.Request.Information.Store.baseParams.node_id;
                App.Request.Information.Store.baseParams = form.getSubmitValues();
                App.Request.Information.Store.setBaseParam('node_id', node_id);
                App.Request.Information.Store.load({params: {start: 0, limit: App.GridLimit}});
            }
        }, {
            text: App.Language.General.clean,
            handler: function (b) {
                form = b.ownerCt.ownerCt.getForm();
                form.reset();
                node_id = App.Request.Information.Store.baseParams.node_id;
                App.Request.Information.Store.baseParams = {};
                App.Request.Information.Store.setBaseParam('node_id', node_id);
                App.Request.Information.Store.load({params: {start: 0, limit: App.GridLimit}});
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
                            fieldLabel: 'Estado de requerimiento',
                            triggerAction: 'all',
                            anchor: '95%',
                            id: 'App.Request.SearchRdiStatus',
                            store: App.Request.InformationStatus.Store,
                            hiddenName: 'rdi_status_id',
                            displayField: 'rdi_status_name',
                            valueField: 'rdi_status_id',
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
                            id: 'App.Request.SearchRdiUser',
                            fieldLabel: 'Nombre',
                            name: 'user_username',
                            anchor: '95%',
                            disabled: App.Security.Session.user_type === 'A' ? false : true,
                            value: App.Security.Session.user_type === 'A' ? '' : App.Security.Session.user_username
                        }, {
                            xtype: 'textfield',
                            id: 'App.Request.SearchRdiMail',
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
                    id: 'form_column_start_rdi_date',
                    items: [{
                            columnWidth: .2,
                            layout: 'form',
                            items: [{
                                    xtype: 'label',
                                    text: "Fecha de creación"
                                }]
                        }, {
                            columnWidth: .4,
                            layout: 'column',
                            id: 'column_start_rdi_date',
                            frame: true,
                            items: [{
                                    bodyStyle: 'margin-right: 50px;',
                                    layout: 'form',
                                    id: 'column_start_rdi_date1',
                                    items: [{
                                            xtype: 'datefield',
                                            id: 'start_rdi_date',
                                            ref: '../start_rdi_date',
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
                                    layout: 'form',
                                    items: [{
                                            xtype: 'datefield',
                                            id: 'end_rdi_date',
                                            ref: '../end_rdi_date',
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
                        }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    labelWidth: 150,
                    id: 'form_column_start_rdi_updated_at',
                    items: [{
                            columnWidth: .2,
                            layout: 'form',
                            items: [{
                                    xtype: 'label',
                                    text: "Fecha de modificación"
                                }]
                        }, {
                            columnWidth: .4,
                            layout: 'column',
                            id: 'column_start_rdi_updated_at',
                            frame: true,
                            items: [{
                                    bodyStyle: 'margin-right: 50px;',
                                    layout: 'form',
                                    id: 'column_start_rdi_updated_at1',
                                    items: [{
                                            xtype: 'datefield',
                                            id: 'start_rdi_updated_at',
                                            ref: '../start_rdi_updated_at',
                                            fieldLabel: App.Language.General.start_date,
                                            name: 'start_updated_at',
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
                                            id: 'end_rdi_updated_at',
                                            ref: '../end_rdi_updated_at',
                                            fieldLabel: App.Language.General.end_date,
                                            name: 'end_updated_at',
                                            anchor: '95%',
                                            listeners: {
                                                'select': function (fd, date) {
                                                    fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                                }
                                            }
                                        }]
                                }]
                        }]
                }]
        }]
};

App.Request.Rdi.Grilla = {
    xtype: 'grid',
    id: 'App.Request.Rdi.Grid',
    ref: 'RequestRdiGrid',
    margins: '5 5 5 5',
    plugins: [new Ext.ux.OOSubmit()],
    region: 'center',
    border: true,
    loadMask: true,
    listeners: {
        'beforerender': function (w) {
            App.Request.Information.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimit}});
        },
        'rowdblclick': function (grid, rowIndex) {
            let record = grid.getStore().getAt(rowIndex);
            if (App.Security.Session.user_username === record.data.User.user_username) {
                if (record.data.RdiStatus.rdi_status_id === '4') {
                    Ext.FlashMessage.alert(`No es posible editar requerimientos finalizados.`);
                    return;
                }
                w = new App.Request.editRequestRdiByNodeWindow({title: App.Language.Request.edit_request_rdi});
                w.show();
                App.Request.Rdi_id = record.data.rdi_id;
                Ext.getCmp('App.RequestRdiEdit.Usuario').setValue(record.data.User.user_username);
                Ext.getCmp('App.RequestRdiEdit.Email').setValue(record.data.User.user_email);
                Ext.getCmp('App.RequestRdiEdit.Description').setValue(record.data.rdi_description);

                var iso_date = Date.parseDate(record.data.rdi_created_at, "Y-m-d H:i:s");
                Ext.getCmp('App.RequestRdiEdit.Fecha').setValue(iso_date.format("d/m/Y H:i"));
            } else {
                Ext.FlashMessage.alert(`No es posible editar registros de otros usuarios.`);
            }
        }
    },
    viewConfig: {
        forceFit: true,
        getRowClass: function (record, index) {
            var c = record.get('rdi_status_id');
            if (c === 3) {
                return 'heavenly-row';
            }
        }
    },
    store: App.Request.Information.Store,
    columns: [
        new Ext.grid.CheckboxSelectionModel(),
        {
            header: App.Language.Core.location,
            sortable: true,
            dataIndex: 'Node',
            align: 'center',
            renderer: function (Node, metaData, record) {
                return `<a href='javascript: App.InfraStructure.expandDeepNode(${record.data.node_id})'>${Node.node_name}</a>`;
            }
        }, {
            dataIndex: 'RdiStatus',
            header: 'Estado',
            sortable: true,
            width: 50,
            renderer: function (RdiStatus) {
                return RdiStatus.rdi_status_name;
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
            dataIndex: 'rdi_description',
            header: 'Requerimiento',
            width: 100,
            sortable: true
        }, {
            dataIndex: 'rdi_created_at',
            header: 'Fecha de creación',
            width: 100,
            sortable: true
        }, {
            dataIndex: 'rdi_updated_at',
            header: 'Fecha de modificación',
            width: 100,
            sortable: true
        }
    ],
    sm: new Ext.grid.CheckboxSelectionModel({
        singleSelect: true
    }),
    bbar: new Ext.PagingToolbar({
        store: App.Request.Information.Store,
        displayInfo: true,
        pageSize: App.GridLimit,
        prependButtons: true,
        listeners: {
            'beforerender': function (w) {
                App.Request.Information.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
            }

        }
    })
};

App.Request.addRdiByNodeWindow = Ext.extend(Ext.Window, {
    width: (screen.width < 500) ? screen.width - 50 : 500,
    height: 300,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'afterrender': function () {
            Ext.getCmp('App.Request.Usuario').setValue(App.Security.Session.user_username);
            Ext.getCmp('App.Request.Email').setValue(App.Security.Session.user_email);
        }
    },
    initComponent: function () {
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
                        title: 'Datos del Requerimiento',
                        ref: 'solicitud',
                        items: [{
                                xtype: 'textarea',
                                anchor: '100%',
                                name: 'rdi_description',
                                fieldLabel: 'Requerimiento',
                                allowBlank: false
                            }]
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        text: App.Language.General.save,
                        ref: '../saveButton',
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: 'index.php/request/rdi/add',
                                    params: {
                                        node_id: App.Interface.selectedNodeId
                                    },
                                    success: function (fp, o) {
                                        if (o.result.success === "false") {
                                            Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                        } else {
                                            App.Request.Information.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimit}});

                                            b.ownerCt.ownerCt.ownerCt.close();
                                            Ext.FlashMessage.alert(o.result.msg);

                                        }
                                    },
                                    failure: function (fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Request.addRdiByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.editRequestRdiByNodeWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 390,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
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
                                id: 'App.RequestRdiEdit.Usuario',
                                anchor: '100%'
                            }, {
                                xtype: 'displayfield',
                                fieldLabel: 'Email',
                                name: 'user_email',
                                id: 'App.RequestRdiEdit.Email',
                                anchor: '100%'
                            }]
                    }, {
                        xtype: 'fieldset',
                        title: 'Datos Solicitud',
                        ref: 'solicitud',
                        items: [{
                                xtype: 'displayfield',
                                fieldLabel: 'Fecha',
                                name: 'rdi_created_at',
                                id: 'App.RequestRdiEdit.Fecha',
                                anchor: '100%'
                            }, {
                                xtype: 'textarea',
                                fieldLabel: 'Descripción',
                                id: 'App.RequestRdiEdit.Description',
                                name: 'rdi_description',
                                anchor: '100%',
                                allowBlank: false
                            }]
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        text: 'Editar',
                        ref: '../saveButton',
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: 'index.php/request/rdi/update',
                                    params: {
                                        rdi_id: App.Request.Rdi_id
                                    },
                                    success: function (fp, o) {
                                        if (o.result.success === "false") {
                                            Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                        } else {
                                            App.Request.Information.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimit}});

                                            b.ownerCt.ownerCt.ownerCt.close();
                                            Ext.FlashMessage.alert(o.result.msg);

                                        }
                                    },
                                    failure: function (fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Request.editRequestRdiByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.exportRdiListByNodeWindow = Ext.extend(Ext.Window, {
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
                        id: 'App.Request.SearchRdiName',
                        value: `${App.Language.General.rdi} ${new Date().add(Date.DAY, 0).format('d-m-Y')}`,
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
                                url: 'index.php/request/rdi/export',
                                method: 'POST',
                                params: {
                                    node_id: App.Interface.selectedNodeId,
                                    file_name: Ext.getCmp('App.Request.SearchRdiName').getValue(),
                                    rdi_status_id: Ext.getCmp('App.Request.SearchRdiStatus').getValue(),
                                    user_username: Ext.getCmp('App.Request.SearchRdiUser').getValue(),
                                    user_email: Ext.getCmp('App.Request.SearchRdiMail').getValue(),
                                    start_date: Ext.getCmp('start_rdi_date').getValue(),
                                    end_date: Ext.getCmp('end_rdi_date').getValue()
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

App.Request.historialRdiWindow = Ext.extend(Ext.Window, {
    resizable: false,
    modal: true,
    border: true,
    width: (screen.width < 750) ? screen.width - 50 : 750,
    height: 500,
    layout: 'fit',
    padding: 2,
    initComponent: function () {
        this.items = [{
                border: true,
                items: [{
                        border: false,
                        xtype: 'grid',
                        store: App.Request.InformationLog.Store,
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
                                renderer: function (User) {
                                    return User.user_username;
                                }
                            }, {
                                dataIndex: 'User',
                                header: 'Email',
                                width: 80,
                                sortable: true,
                                renderer: function (User) {
                                    return User.user_email;
                                }
                            }, {
                                xtype: 'datecolumn',
                                header: 'Fecha Acción',
                                sortable: true,
                                dataIndex: 'rdi_log_date',
                                width: 60,
                                format: App.General.DefaultDateTimeFormat,
                                align: 'center'
                            }, {
                                dataIndex: 'rdi_log_detail',
                                header: 'Acción',
                                width: 180,
                                sortable: true
                            }
                        ],
                        sm: new Ext.grid.CheckboxSelectionModel()
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            App.Request.Rdi_id = null;
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Request.historialRdiWindow.superclass.initComponent.call(this);
    }
});

App.Request.changeRdiStatusWindow = Ext.extend(Ext.Window, {
    resizable: false,
    modal: true,
    border: true,
    width: screen.width < 600 ? screen.width - 100 : 600,
    height: 400,
    layout: 'fit',
    padding: 2,
    initComponent: function () {
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
                                id: 'App.Request.Rdi.Usuario',
                                anchor: '100%'
                            }, {
                                xtype: 'displayfield',
                                fieldLabel: 'Email',
                                name: 'user_email',
                                id: 'App.Request.Rdi.Email',
                                anchor: '100%'
                            }]
                    }, {
                        xtype: 'fieldset',
                        title: 'Datos Requerimiento',
                        ref: 'rdi',
                        items: [{
                                xtype: 'combo',
                                fieldLabel: 'Estado actual',
                                anchor: '100%',
                                id: 'App.Request.Rdi.RdiStatus',
                                store: App.Request.InformationStatus.Store,
                                hiddenName: 'rdi_status_id',
                                triggerAction: 'all',
                                displayField: 'rdi_status_name',
                                valueField: 'rdi_status_id',
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
                                id: 'App.Request.Rdi.RdiStatusNew',
                                store: App.Request.InformationStatus.Store,
                                hiddenName: 'rdi_status_id',
                                triggerAction: 'all',
                                displayField: 'rdi_status_name',
                                valueField: 'rdi_status_id',
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
                                name: 'rdi_description',
                                fieldLabel: 'Requerimiento',
                                id: 'App.Request.Rdi.Description'
                            }]
                    }, {
                        xtype: 'fieldset',
                        title: App.Language.Request.rejection,
                        id: "App.Request.Rdi.Reject.Comentary",
                        hidden: true,
                        items: [{
                                xtype: 'textarea',
                                anchor: '100%',
                                name: 'rdi_reject',
                                fieldLabel: App.Language.Request.rejected_by,
                                id: "App.Request.Rdi.Reject.Comentary.Box",
                                allowBlank: true
                            }]
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        id: 'App.Rdi.Request.btnChangeRdiStatusWindow',
                        text: App.Language.General.save,
                        ref: '../saveButton',
                        handler: function (b) {
                            let form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: 'index.php/request/rdi/update',
                                    params: {
                                        rdi_id: App.Request.Rdi_id,
                                        rdi_status_id: Ext.getCmp('App.Request.Rdi.RdiStatusNew').getValue()
                                    },
                                    success: function (fp, o) {
                                        if (o.result.success === "false") {
                                            Ext.FlashMessage.alert('Error al Ingreso de Datos');
                                        } else {
                                            App.Request.Information.Store.load({params: {node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimit}});
                                            b.ownerCt.ownerCt.ownerCt.close();
                                            Ext.FlashMessage.alert(o.result.msg);
                                        }
                                    },
                                    failure: function (fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Request.changeRdiStatusWindow.superclass.initComponent.call(this);
    }
});