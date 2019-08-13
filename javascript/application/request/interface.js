App.Interface.addToModuleMenu('request', App.ModuleActions[8000]);
//NOTA HAY Q SACAR LOS LISTENES DE LOS COMBOS 
App.Request.allowRootGui = true;

App.Request.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [
            //            new App.Request.InfraEstructura()
            //            ,
            new App.Request.Asset() //DESCOMENTAR PARA PONER LOS ACTIVOS

        ];
        App.Request.Principal.superclass.initComponent.call(this);
    }
});

App.Request.InfraEstructura = Ext.extend(Ext.Panel, {
    // title: App.Language.Request.requests,
    title: App.Language.Infrastructure.infrastructure,
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: [
        App.ModuleActions[8001],
        //    {
        //        text: App.Language.General.add,
        //        iconCls: 'add_icon',
        //        // id: 'ModuleAction_8001',
        //        //  hidden: true,
        //        handler: function() 
        //        {
        //            if (App.Interface.selectedNodeId != 'root') {
        //                Ext.Ajax.request({
        //                    waitMsg: App.Language.General.message_generating_file,
        //                    url: 'index.php/core/nodecontroller/getById',
        //                    timeout: 10000000000,
        //                    params: {
        //                        node_id: App.Interface.selectedNodeId
        //                    },
        //                    success: function(response) {
        //                        response = Ext.decode(response.responseText);
        //
        //
        //                        if (response.success == "true") {
        //                            w = new App.Request.addRequestByNodeWindow({
        //                                title: App.Language.Request.add_request
        //                            });
        //                            w.show();
        //
        //                        } else {
        //                            //ESTO ES PARA EL BANCO SOLAMENTE   
        //                            Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.must_be_within_the_folder_locations_to_create_work_order);
        //
        //                        }
        //                    },
        //                    failure: function(response) {
        //                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
        //                    }
        //                });
        //
        //            } else {
        //                Ext.FlashMessage.alert(App.Language.Request.please_select_node);
        //            }
        //        }
        //    }, 
        {
            xtype: 'spacer',
            width: 10
        }, {
            xtype: 'tbseparator',
            width: 10
        },
        //    {
        //        text: App.Language.Request.approve,
        //        iconCls: 'approve_icon',
        ////            id: 'ModuleAction_8002',
        ////            hidden: true,
        //        handler: function(b)
        //        {
        //            grid = Ext.getCmp('App.RequestByNode.Grid');
        //            if (grid.getSelectionModel().getCount())
        //            {
        //                w = new App.Request.ApprovedByNodeWindow();
        //                w.show();
        //            } else {
        //                Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
        //            }
        //        }
        //    }, 
        App.ModuleActions[8002],
        {
            xtype: 'spacer',
            width: 10
        },
        //    {
        //        text: App.Language.Request.reject,
        //        iconCls: 'delete_icon',
        //        // id: 'ModuleAction_8003',
        //        //hidden: true,
        //        handler: function(b) 
        //        {
        //            grid = Ext.getCmp('App.RequestByNode.Grid');
        //            if (grid.getSelectionModel().getCount()) {
        //                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Request.do_you_want_to_reject_the_request, function(b) {
        //                    if (b == 'yes') {
        //                        w = new App.Request.rejectRequestByNodeWindow;
        //                        w.show();
        //
        //                        ventana = Ext.getCmp('editRequestByNodeWindow');
        //                        //console.log()
        //                        if (ventana != undefined) {
        //                            ventana.close();
        //                        }
        //
        //                    }
        //                });
        //            } else {
        //                Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
        //            }
        //        }
        //    }, 
        App.ModuleActions[8003],
        {
            xtype: 'tbseparator',
            width: 10
        },
        //    {
        //        text: App.Language.General.eexport,
        //        iconCls: 'export_icon',
        //        //   id: 'ModuleAction_8004',
        //        // hidden: true,
        //        handler: function()
        //        {
        //            w = new App.Request.exportListByNodeWindow();
        //            w.show();
        //        }
        //    }, 
        App.ModuleActions[8004],
        {
            xtype: 'tbseparator',
            width: 10
        }, {
            text: App.Language.General.search,
            iconCls: 'search_icon_16',
            enableToggle: true,
            handler: function(b) {
                if (b.ownerCt.ownerCt.form.isVisible()) {
                    b.ownerCt.ownerCt.form.hide();
                } else {
                    b.ownerCt.ownerCt.form.show();
                }
                b.ownerCt.ownerCt.doLayout();
            }
        }
    ],
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                region: 'north',
                id: 'App.Request.FormCentral',
                plugins: [new Ext.ux.OOSubmit()],
                title: App.Language.General.searching,
                frame: true,
                ref: 'form',
                hidden: true,
                height: 150,
                margins: '5 5 0 5',
                padding: '5 5 5 5',
                border: true,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.RequestByNode.Store.baseParams.node_id;
                        App.RequestByNode.Store.baseParams = form.getSubmitValues();
                        App.RequestByNode.Store.setBaseParam('node_id', node_id);
                        App.RequestByNode.Store.load();
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.RequestByNode.Store.baseParams.node_id;
                        form.reset();
                        App.RequestByNode.Store.setBaseParam([]);
                        App.RequestByNode.Store.setBaseParam('node_id', node_id);
                        App.RequestByNode.Store.load();
                    }
                }],
                items: [{
                    layout: 'column',
                    /*-------------COMBOS-------------*/
                    //id: 'column_form_column_start_date',
                    items: [{
                        columnWidth: .5,
                        layout: 'form',
                        items: [{
                            xtype: 'combo',
                            fieldLabel: App.Language.General.state,
                            triggerAction: 'all',
                            anchor: '95%',
                            store: App.Request.Status.Store,
                            hiddenName: 'request_status_id',
                            id: 'App.RequestByNode.form.request_status_id',
                            displayField: 'request_status_name',
                            valueField: 'request_status_id',
                            editable: true,
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            mode: 'remote',
                            minChars: 0
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.Request.failure,
                            triggerAction: 'all',
                            anchor: '95%',
                            store: App.Request.Problem.Store,
                            hiddenName: 'request_problem_id',
                            displayField: 'request_problem_name',
                            id: 'App.RequestByNode.form.request_problem_id',
                            valueField: 'request_problem_id',
                            editable: true,
                            selecOnFocus: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            mode: 'remote',
                            minChars: 0
                        }, {
                            xtype: 'textfield',
                            id: 'App.RequestByNode.Search.request_mail',
                            fieldLabel: 'Correo Resolutor',
                            name: 'request_mail',
                            anchor: '95%'
                        }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
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
                                    id: 'start_dateByNode',
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
                                    id: 'end_dateByNode',
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
                            xtype: 'checkbox',
                            hideLabel: true,
                            id: 'App.RequestByNode.Search.formId',
                            boxLabel: App.Language.General.perform_internal_search,
                            name: 'search_branch',
                            inputValue: 1
                        }, {
                            xtype: 'spacer',
                            height: 15
                        }]
                    }]
                }]
            }, {
                xtype: 'grid',
                id: 'App.RequestByNode.Grid',
                margins: '5 5 5 5',
                plugins: [new Ext.ux.OOSubmit()],
                region: 'center',
                border: true,
                loadMask: true,
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        w = new App.Request.editRequestByNodeWindow({
                            title: App.Language.General.details
                        });
                        w.form.record = grid.getStore().getAt(rowIndex);
                        // w.form.asset_name.setValue(w.form.record.data.Asset.asset_name);
                        w.form.request_problem_name.setValue(w.form.record.data.RequestProblem.request_problem_name);
                        w.form.getForm().loadRecord(grid.getStore().getAt(rowIndex));
                        w.show();
                    }
                },
                viewConfig: {
                    forceFit: true
                },
                store: App.RequestByNode.Store,
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        dataIndex: 'request_folio',
                        header: App.Language.Request.request_n,
                        sortable: true,
                        width: 100,
                        renderer: function(request_folio, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + request_folio + '"';
                            return request_folio;
                        }
                    }, {
                        dataIndex: 'node_name',
                        header: App.Language.General.venue_name,
                        sortable: true,
                        width: 100,
                        renderer: function(node_name, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + node_name + '"';
                            return node_name;
                        }
                    }, {
                        dataIndex: 'node_ruta',
                        header: App.Language.Core.location,
                        sortable: true,
                        width: 100,
                        renderer: function(node_ruta, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + node_ruta + '"';
                            return node_ruta;
                        }
                    }
                    //                    , {
                    //                        dataIndex: 'Asset',
                    //                        header: App.Language.Core.location,
                    //                        sortable: true,
                    //                        width: 100,
                    //                        renderer: function(Asset, metadata, record, rowIndex, colIndex, store)
                    //                        {
                    //                            metadata.attr = 'ext:qtip="' + Asset.asset_path + '"';
                    //                            return Asset.asset_path;
                    //                        }
                    //                    }
                    , {
                        dataIndex: 'RequestProblem',
                        header: App.Language.Request.failure,
                        sortable: true,
                        width: 100,
                        renderer: function(RequestProblem, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + RequestProblem.request_problem_name + '"';
                            return RequestProblem.request_problem_name;
                        }
                    }, {
                        dataIndex: 'request_mail',
                        header: App.Language.General.email_resolver,
                        sortable: true,
                        width: 100,
                        renderer: function(request_mail, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + request_mail + '"';
                            return request_mail;
                        }
                    }, {
                        dataIndex: 'request_subject',
                        header: App.Language.Request.subject,
                        sortable: true,
                        width: 100,
                        renderer: function(request_subject, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + request_subject + '"';
                            return request_subject;
                        }
                    }, {
                        xtype: 'datecolumn',
                        header: App.Language.General.creation_date,
                        dataIndex: 'request_date_creation',
                        sortable: true,
                        format: App.General.DefaultDateFormat,
                        align: 'center'
                    }, {
                        dataIndex: 'RequestStatus',
                        header: App.Language.General.state,
                        sortable: true,
                        width: 100,
                        renderer: function(RequestStatus) {
                            return RequestStatus.request_status_name;
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel({
                    singleSelect: true
                })
            }],
            App.Request.InfraEstructura.superclass.initComponent.call(this);
    }
});

//PROPUESTA PARA UNIVERSIDAD DE CHILE 04/08/2016

App.Request.Asset = Ext.extend(Ext.Panel, {
    //  title: App.Language.Request.requests,
    title: App.Language.Asset.assets,
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: [App.ModuleActions[8001],
        {
            xtype: 'spacer',
            width: 10
        }, {
            xtype: 'tbseparator',
            width: 10
        },
        App.ModuleActions[8002],
        {
            xtype: 'spacer',
            width: 10
        },
        App.ModuleActions[8003],
        {
            xtype: 'tbseparator',
            width: 10
        },
        App.ModuleActions[8004],
        {
            xtype: 'tbseparator',
            width: 10
        }, {
            text: App.Language.General.search,
            iconCls: 'search_icon_16',
            enableToggle: true,
            handler: function(b) {
                if (b.ownerCt.ownerCt.form.isVisible()) {
                    b.ownerCt.ownerCt.form.hide();
                } else {
                    b.ownerCt.ownerCt.form.show();
                }
                b.ownerCt.ownerCt.doLayout();
            }
        }
    ],
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                region: 'north',
                id: 'App.Request.FormCentral',
                plugins: [new Ext.ux.OOSubmit()],
                title: App.Language.General.searching,
                frame: true,
                ref: 'form',
                hidden: true,
                height: 150,
                margins: '5 5 0 5',
                padding: '5 5 5 5',
                border: true,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Request.Store.baseParams.node_id;
                        App.Request.Store.baseParams = form.getSubmitValues();
                        App.Request.Store.setBaseParam('node_id', node_id);
                        App.Request.Store.load();
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Request.Store.baseParams.node_id;
                        form.reset();
                        App.Request.Store.setBaseParam([]);
                        App.Request.Store.setBaseParam('node_id', node_id);
                        App.Request.Store.load();
                    }
                }],
                items: [{
                    layout: 'column',
                    /*-------------COMBOS-------------*/
                    //id: 'column_form_column_start_date',
                    items: [{
                        columnWidth: .5,
                        layout: 'form',
                        items: [{
                            xtype: 'combo',
                            fieldLabel: App.Language.General.state,
                            triggerAction: 'all',
                            anchor: '95%',
                            store: App.Request.Status.Store,
                            hiddenName: 'request_status_id',
                            id: 'App.Request.form.request_status_id',
                            displayField: 'request_status_name',
                            valueField: 'request_status_id',
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
                            fieldLabel: App.Language.Request.problem,
                            triggerAction: 'all',
                            anchor: '95%',
                            store: App.Request.Problem.Store,
                            hiddenName: 'request_problem_id',
                            displayField: 'request_problem_name',
                            id: 'App.Request.form.request_problem_id',
                            valueField: 'request_problem_id',
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
                            xtype: 'checkbox',
                            hideLabel: true,
                            id: 'App.Request.Search.formId',
                            boxLabel: App.Language.General.perform_internal_search,
                            name: 'search_branch',
                            inputValue: 1
                        }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
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
                            height: 15
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
                        App.Request.AssetEditMode(record);

                        //                    w = new App.Request.editRequestWindow
                        //                    ({
                        //                        title: App.Language.General.details
                        //                    });
                        //                    w.form.record = grid.getStore().getAt(rowIndex);
                        //                    w.form.asset_name.setValue(w.form.record.data.Asset.asset_name);
                        //                    w.form.request_problem_name.setValue(w.form.record.data.RequestProblem.request_problem_name);
                        //                    w.form.getForm().loadRecord(grid.getStore().getAt(rowIndex));
                        //                    w.show();

                    }
                },
                viewConfig: {
                    forceFit: true
                },
                store: App.Request.Solicitudes.Store,
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        header: 'Tipo de Solicitud',
                        sortable: true,
                        width: 55,
                        dataIndex: 'SolicitudType',
                        renderer: function(SolicitudType) {
                            return SolicitudType.solicitud_type_nombre;
                        }
                    }, {
                        dataIndex: 'solicitud_folio',
                        header: 'Folio',
                        align: 'center',
                        width: 45,
                        sortable: true
                    }, {
                        dataIndex: 'User',
                        header: 'Nombre',
                        width: 80,
                        sortable: true,
                        renderer: function(User) {
                            return User.user_username;
                        }
                    }, {
                        dataIndex: 'User',
                        header: 'Email',
                        width: 70,
                        sortable: true,
                        renderer: function(User) {
                            return User.user_email;
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
                            return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                        }
                    }, {
                        dataIndex: 'solicitud_factura_numero',
                        header: 'Nº Factura',
                        width: 45,
                        sortable: true
                    }, {
                        dataIndex: 'solicitud_oc_nombre',
                        header: 'OC',
                        sortable: true,
                        width: 68,
                        sortable: true,
                        renderer: function(val, metadata, record) {
                            return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                        }
                    }, {
                        dataIndex: 'solicitud_oc_numero',
                        header: 'Nº OC',
                        width: 45,
                        sortable: true
                    }, {
                        dataIndex: 'SolicitudEstado',
                        header: 'Estado',
                        sortable: true,
                        width: 50,
                        renderer: function(SolicitudEstado) {
                            return SolicitudEstado.solicitud_estado_nombre;
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel({
                    singleSelect: true
                })
            }],
            App.Request.Asset.superclass.initComponent.call(this);
    }
});

App.Request.AssetEditMode = function(record) {
    console.log(record);
    w = new App.Request.addRequestByNodeWindow({
        title: 'Editar Solicitud'
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Request.Solicitudes.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}


//App.Request.Asset = Ext.extend(Ext.Panel, {
//    //  title: App.Language.Request.requests,
//    title: App.Language.Asset.assets,
//    border: false,
//    loadMask: true,
//    layout: 'border',
//    tbar: [App.ModuleActions[8001],
//        {
//            xtype: 'spacer',
//            width: 10
//        }, {
//            xtype: 'tbseparator',
//            width: 10
//        },
//        App.ModuleActions[8002],
//        {
//            xtype: 'spacer',
//            width: 10
//        },
//        App.ModuleActions[8003],
//        {
//            xtype: 'tbseparator',
//            width: 10
//        },
//        App.ModuleActions[8004],
//        {
//            xtype: 'tbseparator',
//            width: 10
//        }, {
//            text: App.Language.General.search,
//            iconCls: 'search_icon_16',
//            enableToggle: true,
//            handler: function(b) {
//                if (b.ownerCt.ownerCt.form.isVisible())
//                {
//                    b.ownerCt.ownerCt.form.hide();
//                } else {
//                    b.ownerCt.ownerCt.form.show();
//                }
//                b.ownerCt.ownerCt.doLayout();
//            }
//        }
//    ],
//    initComponent: function() {
//        this.items = [{
//            xtype: 'form',
//            region: 'north',
//            id: 'App.Request.FormCentral',
//            plugins: [new Ext.ux.OOSubmit()],
//            title: App.Language.General.searching,
//            frame: true,
//            ref: 'form',
//            hidden: true,
//            height: 150,
//            margins: '5 5 0 5',
//            padding: '5 5 5 5',
//            border: true,
//            fbar: [{
//                text: App.Language.General.search,
//                handler: function(b)
//                {
//                    form = b.ownerCt.ownerCt.getForm();
//                    node_id = App.Request.Store.baseParams.node_id;
//                    App.Request.Store.baseParams = form.getSubmitValues();
//                    App.Request.Store.setBaseParam('node_id', node_id);
//                    App.Request.Store.load();
//                }
//            }, {
//                text: App.Language.General.clean,
//                handler: function(b)
//                {
//                    form = b.ownerCt.ownerCt.getForm();
//                    node_id = App.Request.Store.baseParams.node_id;
//                    form.reset();
//                    App.Request.Store.setBaseParam([]);
//                    App.Request.Store.setBaseParam('node_id', node_id);
//                    App.Request.Store.load();
//                }
//            }],
//            items: [{
//                layout: 'column',
//                /*-------------COMBOS-------------*/
//                //id: 'column_form_column_start_date',
//                items: [{
//                    columnWidth: .5,
//                    layout: 'form',
//                    items: [{
//                        xtype: 'combo',
//                        fieldLabel: App.Language.General.state,
//                        triggerAction: 'all',
//                        anchor: '95%',
//                        store: App.Request.Status.Store,
//                        hiddenName: 'request_status_id',
//                        id: 'App.Request.form.request_status_id',
//                        displayField: 'request_status_name',
//                        valueField: 'request_status_id',
//                        editable: true,
//                        selecOnFocus: true,
//                        typeAhead: true,
//                        selectOnFocus: true,
//                        mode: 'remote',
//                        minChars: 0,
//                        listeners:
//                        {
//                            'afterrender': function(cb)
//                            {
//                                cb.__value = cb.value;
//                                cb.setValue('');
//                                cb.getStore().load
//                                ({
//                                    callback: function()
//                                    {
//                                        cb.setValue(cb.__value);
//                                    }
//                                });
//                            }
//                        }
//                    }, {
//                        xtype: 'combo',
//                        fieldLabel: App.Language.Request.problem,
//                        triggerAction: 'all',
//                        anchor: '95%',
//                        store: App.Request.Problem.Store,
//                        hiddenName: 'request_problem_id',
//                        displayField: 'request_problem_name',
//                        id: 'App.Request.form.request_problem_id',
//                        valueField: 'request_problem_id',
//                        editable: true,
//                        selecOnFocus: true,
//                        typeAhead: true,
//                        selectOnFocus: true,
//                        mode: 'remote',
//                        minChars: 0,
//                        listeners: {
//                            'afterrender': function(cb)
//                            {
//                                cb.__value = cb.value;
//                                cb.setValue('');
//                                cb.getStore().load
//                                ({
//                                    callback: function()
//                                    {
//                                        cb.setValue(cb.__value);
//                                    }
//                                });
//                            }
//                        }
//                    }, {
//                        xtype: 'checkbox',
//                        hideLabel: true,
//                        id: 'App.Request.Search.formId',
//                        boxLabel: App.Language.General.perform_internal_search,
//                        name: 'search_branch',
//                        inputValue: 1
//                    }]
//                }, {
//                    columnWidth: .5,
//                    layout: 'form',
//                    id: 'form_column_start_date',
//                    items:
//                    [{
//                        columnWidth: .2,
//                        layout: 'form',
//                        items:
//                        [{
//                            xtype: 'label',
//                            text: App.Language.Request.select_a_date_range_to_for_the_request
//                        }]
//                    }, {
//                        columnWidth: .4,
//                        layout: 'column',
//                        id: 'column_start_date',
//                        frame: true,
//                        items:
//                        [{
//                            columnWidth: .5,
//                            layout: 'form',
//                            id: 'column_start_date1',
//                            items:
//                            [{
//                                xtype: 'datefield',
//                                id: 'start_date',
//                                ref: '../start_date',
//                                fieldLabel: App.Language.General.start_date,
//                                name: 'start_date',
//                                anchor: '95%',
//                                listeners:
//                                {
//                                    'select': function(fd, date)
//                                    {
//                                        fd.ownerCt.ownerCt.end_date.setMinValue(date);
//                                    }
//                                }
//                            }]
//                        }, {
//                            columnWidth: .5,
//                            layout: 'form',
//                            items:
//                            [{
//                                xtype: 'datefield',
//                                id: 'end_date',
//                                ref: '../end_date',
//                                fieldLabel: App.Language.General.end_date,
//                                name: 'end_date',
//                                anchor: '95%',
//                                listeners:
//                                {
//                                    'select': function(fd, date)
//                                    {
//                                        fd.ownerCt.ownerCt.start_date.setMaxValue(date);
//                                    }
//                                }
//                            }]
//                        }]
//                    }, {
//                        xtype: 'spacer',
//                        height: 15
//                    }]
//                }]
//            }]
//        }, {
//            xtype: 'grid',
//            id: 'App.Request.Grid',
//            margins: '5 5 5 5',
//            plugins: [new Ext.ux.OOSubmit()],
//            region: 'center',
//            border: true,
//            loadMask: true,
//            listeners:
//            {
//                'rowdblclick': function(grid, rowIndex)
//                {
//                    w = new App.Request.editRequestWindow
//                    ({
//                        title: App.Language.General.details
//                    });
//                    w.form.record = grid.getStore().getAt(rowIndex);
//                    w.form.asset_name.setValue(w.form.record.data.Asset.asset_name);
//                    w.form.request_problem_name.setValue(w.form.record.data.RequestProblem.request_problem_name);
//                    w.form.getForm().loadRecord(grid.getStore().getAt(rowIndex));
//                    w.show();
//                }
//            },
//            viewConfig:
//            {
//                forceFit: true
//            },
//            store: App.Request.Store,
//            columns: [new Ext.grid.CheckboxSelectionModel(),
//            {
//                dataIndex: 'request_folio',
//                header: App.Language.Request.request_n,
//                sortable: true,
//                width: 100,
//                renderer: function(request_folio, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + request_folio + '"';
//                    return request_folio;
//                }
//            }, {
//                dataIndex: 'Asset',
//                header: App.Language.General.asset,
//                sortable: true,
//                width: 100,
//                renderer: function(Asset, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + Asset.asset_name + '"';
//                    return Asset.asset_name;
//                }
//            }, {
//                dataIndex: 'Asset',
//                header: App.Language.Core.location,
//                sortable: true,
//                width: 100,
//                renderer: function(Asset, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + Asset.asset_path + '"';
//                    return Asset.asset_path;
//                }
//            }, {
//                dataIndex: 'RequestProblem',
//                header: App.Language.Request.problem,
//                sortable: true,
//                width: 100,
//                renderer: function(RequestProblem, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + RequestProblem.request_problem_name + '"';
//                    return RequestProblem.request_problem_name;
//                }
//            }, {
//                dataIndex: 'request_subject',
//                header: App.Language.Request.subject,
//                sortable: true,
//                width: 100,
//                renderer: function(request_subject, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + request_subject + '"';
//                    return request_subject;
//                }
//            }, {
//                xtype: 'datecolumn',
//                header: App.Language.General.creation_date,
//                dataIndex: 'request_date_creation',
//                sortable: true,
//                format: App.General.DefaultDateFormat,
//                align: 'center'
//            }, {
//                dataIndex: 'RequestStatus',
//                header: App.Language.General.state,
//                sortable: true,
//                width: 100,
//                renderer: function(RequestStatus)
//                {
//                    return RequestStatus.request_status_name;
//                }
//            }],
//            sm: new Ext.grid.CheckboxSelectionModel({
//                singleSelect: true
//            })
//        }],
//        App.Request.Asset.superclass.initComponent.call(this);
//    }
//});

App.Request.Principal.listener = function(node) {
    if (node && node.id) {
        App.RequestByNode.Store.setBaseParam('node_id', node.id);
        App.RequestByNode.Store.load();
        //        App.Request.Store.setBaseParam('node_id', node.id);
        //        App.Request.Store.load();
    }
};

App.Request.addRequestWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 490,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'fieldset',
                title: App.Language.Request.team_fail,
                items: [{
                    xtype: 'combo',
                    fieldLabel: App.Language.Request.team,
                    anchor: '100%',
                    triggerAction: 'all',
                    store: App.Asset.Store,
                    hiddenName: 'asset_id',
                    ref: '../asset_id',
                    displayField: 'asset_with_type',
                    valueField: 'asset_id',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    allowBlank: false,
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
                    fieldLabel: App.Language.Request.subject,
                    name: 'request_subject',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    anchor: '100%',
                    name: 'request_description',
                    fieldLabel: App.Language.General.description,
                    allowBlank: false
                }, {
                    xtype: 'combo',
                    fieldLabel: App.Language.Request.failure,
                    triggerAction: 'all',
                    anchor: '100%',
                    store: App.Request.Problem.Store,
                    hiddenName: 'request_problem_id',
                    displayField: 'request_problem_name',
                    valueField: 'request_problem_id',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    allowBlank: false,
                    mode: 'remote',
                    minChars: 0
                }]
            }, {
                xtype: 'fieldset',
                title: App.Language.Request.applicant_details,
                items: [{
                    xtype: 'textfield',
                    fieldLabel: App.Language.General.requested_by,
                    name: 'request_requested_by',
                    anchor: '100%'
                }, {
                    xtype: 'numberfield',
                    fieldLabel: App.Language.General.phone,
                    anchor: '100%',
                    name: 'request_fono'
                }, {
                    xtype: 'textareabutton',
                    fieldLabel: App.Language.General.mail_notification,
                    id: 'App.Request.AlertMailField',
                    vtype: 'multiemail',
                    name: 'request_mail',
                    anchor: '100%',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'account_icon',
                        handler: function(b) {
                            w = new App.Request.addUsersWindow({
                                sentTo: 'App.Request.AlertMailField'
                            });
                            w.show();
                        }
                    }
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
                            url: 'index.php/request/request/add',
                            params: {
                                node_id: App.Request.Store.baseParams.node_id,
                                request_mail: Ext.getCmp('App.Request.AlertMailField').getValue(),
                                request_status_id: 1
                            },
                            success: function(fp, o) {
                                App.Request.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
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
        App.Request.addRequestWindow.superclass.initComponent.call(this);
    }
});

App.Request.addAprobarWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 450,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'afterrender': function() {
            Ext.getCmp('App.Request.TipoSolicitud').setValue('Alta');
            Ext.getCmp('App.Request.Usuario').setValue(App.Security.Session.user_username);
            Ext.getCmp('App.Request.Email').setValue('admin@igeo.cl');
            Ext.getCmp('App.Request.Fecha').setValue('05/08/2016 09:50');

            Ext.getCmp('App.Request.FacturaNombre').setValue('ejemplo_factura.pdf');
            Ext.getCmp('App.Request.FacturaNumero').setValue('1234567890');
            Ext.getCmp('App.Request.OCNombre').setValue('ejemplo_oc.pdf');
            Ext.getCmp('App.Request.OCNumero').setValue('1234567890');
            Ext.getCmp('App.Request.Comentario').setValue('');

        }
    },
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
                }, {

                    xtype: 'displayfield',
                    fieldLabel: 'Fecha de Creación',
                    name: 'node_name',
                    id: 'App.Request.Fecha',
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

                }
            }]
        }];
        App.Request.addAprobarWindow.superclass.initComponent.call(this);
    }
});

App.Request.addRechazarWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 520,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'afterrender': function() {
            Ext.getCmp('App.RequestRechazar.TipoSolicitud').setValue('Alta');
            Ext.getCmp('App.RequestRechazar.Usuario').setValue(App.Security.Session.user_username);
            Ext.getCmp('App.RequestRechazar.Email').setValue('admin@igeo.cl');
            Ext.getCmp('App.RequestRechazar.Fecha').setValue('05/08/2016 09:50');

            Ext.getCmp('App.RequestRechazar.FacturaNombre').setValue('ejemplo_factura.pdf');
            Ext.getCmp('App.RequestRechazar.FacturaNumero').setValue('1234567890');
            Ext.getCmp('App.RequestRechazar.OCNombre').setValue('ejemplo_oc.pdf');
            Ext.getCmp('App.RequestRechazar.OCNumero').setValue('1234567890');
            Ext.getCmp('App.RequestRechazar.Comentario').setValue('');

        }
    },
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
                }, {

                    xtype: 'displayfield',
                    fieldLabel: 'Fecha de Creación',
                    name: 'node_name',
                    id: 'App.RequestRechazar.Fecha',
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
                    fieldLabel: 'Rechazada Por:',
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

                }
            }]
        }];
        App.Request.addRechazarWindow.superclass.initComponent.call(this);
    }
});

App.Request.addRequestByNodeWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 450,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'afterrender': function() {
            //            if (App.Interface.selectedNodeId != 'root') {
            //                Ext.Ajax.request({
            //                    waitMsg: App.Language.General.message_generating_file,
            //                    url: 'index.php/core/nodecontroller/getByIdNode',
            //                    timeout: 10000000000,
            //                    params: {
            //                        node_id: App.Interface.selectedNodeId
            //                    },
            //                    success: function(response) {
            //                        response = Ext.decode(response.responseText);
            //                        nodo = response.results.node_name;
            //                        ruta = response.results.node_ruta;
            //                        node_type_name = response.results.node_type_name;
            Ext.getCmp('App.Request.Usuario').setValue(App.Security.Session.user_username);
            Ext.getCmp('App.Request.Email').setValue('admin@igeo.cl');
            //                        //  Ext.getCmp('App.Mtn.Wo.DisplayNodeTipoNombre').setValue(node_type_name);
            //                    },
            //                    failure: function(response) {
            //                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
            //                    }
            //                });
            //            } else {
            //                Ext.FlashMessage.alert(App.Language.General.you_must_select_a_node);
            //            }
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            bodyStyle: 'padding: 10 10px 10',
            //            padding: 5,
            items: [{
                xtype: 'fieldset',
                title: 'Datos Solicitante',
                items: [{
                    xtype: 'combo',
                    fieldLabel: 'Tipo de Solicitud',
                    anchor: '100%',
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
                    allowBlank: false,
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




                    //                    xtype: 'combo',
                    //                    fieldLabel: 'Tipo de Solicitud',
                    //                    anchor: '100%',
                    //                    triggerAction: 'all',
                    //                    store: App.Request.SolicitudTipos.Store,
                    //                    hiddenName: 'solicitud_type_id',
                    //                    displayField: 'solicitud_type_nombre',
                    //                    valueField: 'solicitud_type_id',
                    //                    editable: true,
                    //                    selecOnFocus: true,
                    //                    typeAhead: true,
                    //                    selectOnFocus: true,
                    //                    allowBlank: false,
                    //                    mode: 'remote',
                    //                    minChars: 0
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
                }, {
                    xtype: 'datefield',
                    fieldLabel: 'Fecha de Creación',
                    value: new Date().add(Date.DAY, 0),
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Datos Solicitud',
                items: [{
                    xtype: 'fileuploadfield',
                    emptyText: 'Seleccione Factura',
                    fieldLabel: 'Factura',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'request_documento',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Nº Factura',
                    name: 'request_factura',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'fileuploadfield',
                    emptyText: 'Seleccione Orden de Compra',
                    fieldLabel: 'Orden de Compra',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'request_oc',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: 'Nº Orden de Compra',
                    name: 'request_oc_numero',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    anchor: '100%',
                    name: 'request_comentario',
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
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {

                        form.submit({
                            url: 'index.php/request/request/addByNode',
                            params: {
                                node_id: App.Interface.selectedNodeId,
                                request_mail: Ext.getCmp('App.Request.AlertMailField').getValue(),
                                request_status_id: 1
                            },
                            success: function(fp, o) {
                                App.RequestByNode.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                                App.RequestByNode.Store.load();

                                b.ownerCt.ownerCt.ownerCt.close();
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
        App.Request.addRequestWindow.superclass.initComponent.call(this);
    }
});

//App.Request.addRequestByNodeWindow = Ext.extend(Ext.Window, {
//    width: 700,
//    height: 490,
//    modal: true,
//    resizable: false,
//    layout: 'fit',
//    padding: 1,
//    listeners: {
//        'afterrender': function() {
//            if (App.Interface.selectedNodeId != 'root') {
//                Ext.Ajax.request({
//                    waitMsg: App.Language.General.message_generating_file,
//                    url: 'index.php/core/nodecontroller/getByIdNode',
//                    timeout: 10000000000,
//                    params: {
//                        node_id: App.Interface.selectedNodeId
//                    },
//                    success: function(response) {
//                        response = Ext.decode(response.responseText);
//                        nodo = response.results.node_name;
//                        ruta = response.results.node_ruta;
//                        node_type_name = response.results.node_type_name;
//                        Ext.getCmp('App.Request.DisplayNodeNombre').setValue(nodo);
//                        Ext.getCmp('App.Request.DisplayNodeRuta').setValue(ruta);
//                        //  Ext.getCmp('App.Mtn.Wo.DisplayNodeTipoNombre').setValue(node_type_name);
//                    },
//                    failure: function(response) {
//                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
//                    }
//                });
//            } else {
//                Ext.FlashMessage.alert(App.Language.General.you_must_select_a_node);
//            }
//        }
//    },
//    initComponent: function() {
//        this.items = 
//        [{
//            xtype: 'form',
//            ref: 'form',
//            labelWidth: 150,
//            padding: 5,
//            items: 
//            [{
//                xtype: 'fieldset',
//                title: App.Language.Request.campus_fail,
//                items: 
//                [{
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.General.venue_name,
//                    name: 'node_name',
//                    id: 'App.Request.DisplayNodeNombre',
//                    anchor: '100%'
//                }, {
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.Core.location,
//                    name: 'node_name',
//                    id: 'App.Request.DisplayNodeRuta',
//                    anchor: '100%'
//                }, {
//                    xtype: 'textfield',
//                    fieldLabel: App.Language.Request.subject,
//                    name: 'request_subject',
//                    anchor: '100%',
//                    allowBlank: false
//                }, {
//                    xtype: 'textarea',
//                    anchor: '100%',
//                    name: 'request_description',
//                    fieldLabel: App.Language.General.description,
//                    allowBlank: false
//                }, {
//                    xtype: 'combo',
//                    fieldLabel: App.Language.Request.failure,
//                    triggerAction: 'all',
//                    anchor: '100%',
//                    store: App.Request.Problem.Store,
//                    hiddenName: 'request_problem_id',
//                    displayField: 'request_problem_name',
//                    valueField: 'request_problem_id',
//                    editable: true,
//                    selecOnFocus: true,
//                    typeAhead: true,
//                    selectOnFocus: true,
//                    allowBlank: false,
//                    mode: 'remote',
//                    minChars: 0
//
//                }]
//            }, {
//                xtype: 'fieldset',
//                title: App.Language.Request.applicant_details,
//                items:
//                [{
//                    xtype: 'textfield',
//                    fieldLabel: App.Language.General.requested_by,
//                    name: 'request_requested_by',
//                    anchor: '100%'
//                }, {
//                    xtype: 'numberfield',
//                    fieldLabel: App.Language.General.phone,
//                    anchor: '100%',
//                    name: 'request_fono'
//                }, {
//                    xtype: 'textareabutton',
//                    fieldLabel: App.Language.General.email_resolver,
//                    id: 'App.Request.AlertMailField',
//                    vtype: 'multiemail',
//                    name: 'request_mail',
//                    anchor: '100%',
//                    buttonText: '',
//                    buttonCfg:
//                    {
//                        iconCls: 'account_icon',
//                        handler: function(b)
//                        {
//                            w = new App.Request.addUsersWindow
//                            ({
//                                sentTo: 'App.Request.AlertMailField'
//                            });
//                            w.show();
//                        }
//                    }
//                }]
//            }],
//            buttons:
//            [{
//                text: App.Language.General.close,
//                handler: function(b)
//                {
//                    b.ownerCt.ownerCt.ownerCt.close();
//                }
//            }, {
//                text: App.Language.General.save,
//                ref: '../saveButton',
//                handler: function(b)
//                {
//                    form = b.ownerCt.ownerCt.getForm();
//                    if (form.isValid())
//                    {
//
//                        form.submit
//                        ({
//                            url: 'index.php/request/request/addByNode',
//                            params:
//                            {
//                                node_id: App.Interface.selectedNodeId,
//                                request_mail: Ext.getCmp('App.Request.AlertMailField').getValue(),
//                                request_status_id: 1
//                            },
//                            success: function(fp, o)
//                            {
//                                App.RequestByNode.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
//                                App.RequestByNode.Store.load();
//
//                                b.ownerCt.ownerCt.ownerCt.close();
//                                Ext.FlashMessage.alert(o.result.msg);
//                            },
//                            failure: function(fp, o)
//                            {
//                                alert('Error:\n' + o.result.msg);
//                            }
//                        });
//                    }
//                }
//            }]
//        }];
//        App.Request.addRequestWindow.superclass.initComponent.call(this);
//    }
//});

App.Request.ApprovedByNodeWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 150,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'afterrender': function() {
            grid = Ext.getCmp('App.RequestByNode.Grid');
            // console.log(grid.getSelectionModel().getSelected());

            if (grid.getSelectionModel().getCount()) {

                //ACTUALIZA LOS PROVEDORES
                App.Mtn.WoNodeProvider.Store.setBaseParam('node_id', grid.getSelectionModel().getSelected().data.node_id);
                App.Mtn.WoNodeProvider.Store.load();

                //                records = Ext.getCmp('App.RequestByNode.Grid').getSelectionModel().getSelections();
                //                aux = new Array();
                //                record_array = new Array();
                //                for (var i = 0; i < records.length; i++)
                //                {
                //                    aux.push(records[i].data.request_id);
                //                }
                //                record_array = aux.join(',');
                //                App.Request.RequestAprovedByNode(2, record_array);
                //                App.RequestByNode.Store.load();


            } else {
                Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
            }
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 100,
            padding: 5,
            items: [{
                xtype: 'combo',
                fieldLabel: 'Proveedor',
                id: 'App.Mtn.Wo.RootProvider',
                store: App.Mtn.WoNodeProvider.Store,
                hiddenName: 'provider_id',
                triggerAction: 'all',
                displayField: 'provider_name',
                valueField: 'provider_id',
                selecOnFocus: true,
                anchor: '88%',
                typeAhead: true,
                editable: false,
                allowBlank: false,
                hideLabel: false,
                hidden: false,
                mode: 'remote',
                minChars: 0
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
                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Request.do_you_want_to_Approve_the_request, function(b2) {
                        if (b2 == 'yes') {
                            grid = Ext.getCmp('App.RequestByNode.Grid');
                            form = b.ownerCt.ownerCt.getForm();

                            if (form.isValid()) {

                                form.submit({
                                    url: 'index.php/request/request/updateByNode',
                                    params: {
                                        request_id: grid.getSelectionModel().getSelected().data.request_id,
                                        request_status_id: 2 //aprobada
                                    },
                                    success: function(fp, o) {
                                        App.RequestByNode.Store.load();
                                        b.ownerCt.ownerCt.ownerCt.close();
                                        Ext.FlashMessage.alert(o.result.msg);
                                    },
                                    failure: function(fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }

                        }
                    });
                }
            }]
        }];
        App.Request.ApprovedByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.editRequestWindow = Ext.extend(Ext.Window, {
    id: 'editRequestWindow',
    width: 500,
    height: 505,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            tbar: [
                App.ModuleActions[8002],
                {
                    xtype: 'spacer',
                    width: 10
                },
                App.ModuleActions[8003]
            ],
            items: [{
                xtype: 'fieldset',
                title: App.Language.General.asset,
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: App.Language.Request.request_n,
                    name: 'request_folio',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'hidden',
                    name: 'asset_id',
                    ref: '../asset_id'
                }, {
                    xtype: 'displayfield',
                    ref: '../asset_name',
                    fieldLabel: App.Language.General.asset,
                    anchor: '100%'
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.state,
                    name: 'request_status_name',
                    anchor: '100%'

                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.Request.subject,
                    name: 'request_subject',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.description,
                    name: 'request_description',
                    anchor: '100%',
                    allowBlank: false

                }, {
                    xtype: 'hidden',
                    name: 'request_problem_id',
                    ref: '../request_problem_id'
                }, {
                    xtype: 'displayfield',
                    ref: '../request_problem_name',
                    fieldLabel: App.Language.Request.failure,
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: App.Language.Request.applicant_details,
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.requested_by,
                    name: 'request_requested_by',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.phone,
                    name: 'request_fono',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    fieldLabel: App.Language.General.mail_notification,
                    name: 'request_mail',
                    vtype: 'multiemail',
                    anchor: '100%',
                    disabled: true
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.comment,
                    name: 'request_requested_by_comment',
                    anchor: '100%',
                    allowBlank: false
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Request.editRequestWindow.superclass.initComponent.call(this);
    }
});

App.Request.editRequestByNodeWindow = Ext.extend(Ext.Window, {
    id: 'editRequestByNodeWindow',
    width: 500,
    height: 505,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            tbar: [{
                text: App.Language.Request.approve,
                iconCls: 'approve_icon',
                //            id: 'ModuleAction_8002',
                //            hidden: true,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();

                    grid = Ext.getCmp('App.RequestByNode.Grid');

                    if (grid.getSelectionModel().getCount()) {
                        w = new App.Request.ApprovedByNodeWindow();
                        w.show();

                    } else {
                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                    }

                }
            }, {
                xtype: 'spacer',
                width: 10
            }, {
                text: App.Language.Request.reject,
                iconCls: 'delete_icon',
                // id: 'ModuleAction_8003',
                //hidden: true,
                handler: function(b) {
                    grid = Ext.getCmp('App.RequestByNode.Grid');
                    if (grid.getSelectionModel().getCount()) {
                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Request.do_you_want_to_reject_the_request, function(b) {
                            if (b == 'yes') {
                                w = new App.Request.rejectRequestByNodeWindow;
                                w.show();
                                ventana = Ext.getCmp('editRequestByNodeWindow');
                                //console.log()
                                if (ventana != undefined) {
                                    ventana.close();
                                }

                            }
                        });
                    } else {
                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                    }

                }
            }],
            items: [{
                xtype: 'fieldset',
                //title: App.Language.General.asset,
                title: 'Datos',
                items: [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Request.request_n,
                        name: 'request_folio',
                        anchor: '100%',
                        allowBlank: false
                    },
                    //                                                            {
                    //                                                                xtype: 'hidden',
                    //                                                                name: 'asset_id',
                    //                                                                ref: '../asset_id'
                    //                                                            }, {
                    //                                                                xtype: 'displayfield',
                    //                                                                ref: '../asset_name',
                    //                                                                fieldLabel: App.Language.General.asset,
                    //                                                                anchor: '100%'
                    //                                                            }, 
                    {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.state,
                        name: 'request_status_name',
                        anchor: '100%'

                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Request.subject,
                        name: 'request_subject',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.description,
                        name: 'request_description',
                        anchor: '100%',
                        allowBlank: false

                    }, {
                        xtype: 'hidden',
                        name: 'request_problem_id',
                        ref: '../request_problem_id'
                    }, {
                        xtype: 'displayfield',
                        ref: '../request_problem_name',
                        fieldLabel: App.Language.Request.failure,
                        anchor: '100%'
                    }
                ]
            }, {
                xtype: 'fieldset',
                title: App.Language.Request.applicant_details,
                items: [{
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.requested_by,
                    name: 'request_requested_by',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.phone,
                    name: 'request_fono',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    fieldLabel: App.Language.General.email_resolver,
                    name: 'request_mail',
                    vtype: 'multiemail',
                    anchor: '100%',
                    disabled: true
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.comment,
                    name: 'request_requested_by_comment',
                    anchor: '100%',
                    allowBlank: false
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Request.editRequestByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.RequestAproved = function(request_status_id, record_array) {
    Ext.Ajax.request({
        url: 'index.php/request/request/update',
        params: {
            request_id: record_array,
            request_status_id: request_status_id
        },
        method: 'POST',
        success: function(result, request) {
            json = Ext.decode(result.responseText);
            if (json.success == 'true') {
                Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
            } else {
                Ext.MessageBox.alert(App.Language.General.warning, json.msg);
            }
            App.Request.Store.load(); //--store--
        },
        failure: function(result, request) {
            Ext.MessageBox.alert(App.Language.General.error, result.msg);
        }
    });
}

App.Request.RequestAprovedByNode = function(request_status_id, record_array) {
    Ext.Ajax.request({
        url: 'index.php/request/request/updateByNode',
        params: {
            request_id: record_array,
            request_status_id: request_status_id
        },
        method: 'POST',
        success: function(result, request) {
            json = Ext.decode(result.responseText);
            if (json.success == 'true') {
                Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
            } else {
                Ext.MessageBox.alert(App.Language.General.warning, json.msg);
            }
            App.Request.Store.load(); //--store--
        },
        failure: function(result, request) {
            Ext.MessageBox.alert(App.Language.General.error, result.msg);
        }
    });
}

App.Request.RequestReject = function(request_status_id, record_array, request_requested_by_comment) {
    Ext.Ajax.request({
        url: 'index.php/request/request/update',
        params: {
            request_id: record_array,
            request_status_id: request_status_id,
            request_requested_by_comment: request_requested_by_comment
        },
        method: 'POST',
        success: function(result, request) {
            json = Ext.decode(result.responseText);
            if (json.success == 'true') {
                Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
            } else {
                Ext.MessageBox.alert(App.Language.General.warning, json.msg);
            }
            App.Request.Store.load(); //--store--
        },
        failure: function(result, request) {
            Ext.MessageBox.alert(App.Language.General.error, result.msg);
        }
    });
}

App.Request.RequestRejectByNode = function(request_status_id, record_array, request_requested_by_comment) {
    Ext.Ajax.request({
        url: 'index.php/request/request/updateByNode',
        params: {
            request_id: record_array,
            request_status_id: request_status_id,
            request_requested_by_comment: request_requested_by_comment
        },
        method: 'POST',
        success: function(result, request) {
            json = Ext.decode(result.responseText);
            if (json.success == 'true') {
                Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
            } else {
                Ext.MessageBox.alert(App.Language.General.warning, json.msg);
            }

            // App.Request.Store.load();//--store--
            App.RequestByNode.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
            App.RequestByNode.Store.load();
        },
        failure: function(result, request) {
            Ext.MessageBox.alert(App.Language.General.error, result.msg);
        }
    });
}

App.Request.addUsersWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.mail_notification,
    resizable: false,
    modal: true,
    width: 800,
    height: 500,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'panel',
            border: false,
            layout: 'border',
            fbar: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                handler: function(b) {
                    grid = Ext.getCmp('App.Request.GridUsers');
                    records = Ext.getCmp('App.Request.GridUsers').getSelectionModel().getSelections();
                    aux = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.user_email);
                    }
                    email = aux.join(',');
                    Ext.getCmp(b.ownerCt.ownerCt.ownerCt.sentTo).setValue(email);
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }],
            items: [{
                xtype: 'form',
                labelWidth: 150,
                region: 'north',
                margins: '5 5 0 5',
                plugins: [new Ext.ux.OOSubmit()],
                title: App.Language.General.searching,
                frame: true,
                ref: 'form',
                height: 120,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        App.Core.UserNotification.Store.baseParams = form.getSubmitValues();
                        App.Core.UserNotification.Store.setBaseParam('user_id', null);
                        App.Core.UserNotification.Store.load();
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        form.reset();
                        App.Core.UserNotification.Store.setBaseParam([]);
                        App.Core.UserNotification.Store.load();
                    }
                }],
                items: [{
                    layout: 'column',
                    items: [{
                        columnWidth: .5,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: App.Language.Core.username,
                            anchor: '90%',
                            name: 'user_name'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Core.english_username,
                            anchor: '90%',
                            name: 'user_username'
                        }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        items: [{
                            xtype: 'combo',
                            triggerAction: 'all',
                            fieldLabel: App.Language.Core.groups,
                            hiddenName: 'user_group_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            store: App.Core.Groups.Store,
                            displayField: 'user_group_name',
                            valueField: 'user_group_id',
                            mode: 'remote',
                            minChars: 0,
                            anchor: '100%'
                        }]
                    }]
                }]
            }, {
                xtype: 'grid',
                ref: 'gridUser',
                id: 'App.Request.GridUsers',
                loadMask: true,
                store: App.Core.UserNotification.Store,
                plugins: [new Ext.ux.OOSubmit()],
                region: 'center',
                margins: '5 5 5 5',
                viewConfig: {
                    forceFit: true,
                    getRowClass: function(record, index) {
                        var c = record.get('user_status');
                        if (c == 1) {
                            return 'red-row';
                        }
                    }
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.Users.EditUserSystem(record);
                    },
                    'beforerender': function() {
                        App.Core.UserNotification.Store.setBaseParam('show_admin_user', 1);
                        App.Core.UserNotification.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        dataIndex: 'user_name',
                        header: App.Language.Core.username,
                        sortable: true
                    }, {
                        dataIndex: 'user_username',
                        header: App.Language.Core.english_username,
                        sortable: true
                    }, {
                        dataIndex: 'user_email',
                        header: App.Language.Core.email,
                        sortable: true
                    }, {
                        dataIndex: 'user_type_name',
                        header: App.Language.General.user_type,
                        sortable: true
                    }, {
                        dataIndex: 'user_string_groups',
                        header: App.Language.Core.groups,
                        sortable: true
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }]
        }]
        App.Request.addUsersWindow.superclass.initComponent.call(this);
    }
});

App.Request.rejectRequestWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 150,
    title: App.Language.General.confirmation,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textarea',
                fieldLabel: App.Language.General.commentary,
                id: 'request_requested_by_comment',
                name: 'request_requested_by_comment',
                anchor: '100%',
                allowBlank: false
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
                    grid = Ext.getCmp('App.Request.Grid');
                    if (grid.getSelectionModel().getCount()) {
                        records = Ext.getCmp('App.Request.Grid').getSelectionModel().getSelections();
                        aux = new Array();
                        record_array = new Array();
                        for (var i = 0; i < records.length; i++) {
                            aux.push(records[i].data.request_id);
                        }
                        record_array = aux.join(',');
                        request_requested_by_comment = Ext.getCmp('request_requested_by_comment').getValue();

                        App.Request.RequestReject(3, record_array, request_requested_by_comment);
                        b.ownerCt.ownerCt.ownerCt.close();
                    } else {
                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                    }
                }
            }]
        }];
        App.Request.rejectRequestWindow.superclass.initComponent.call(this);
    }
});

App.Request.rejectRequestByNodeWindow = Ext.extend(Ext.Window, {
    width: 500,
    height: 150,
    title: App.Language.General.confirmation,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textarea',
                fieldLabel: App.Language.General.commentary,
                id: 'request_requested_by_comment',
                name: 'request_requested_by_comment',
                anchor: '100%',
                allowBlank: false
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

                    grid = Ext.getCmp('App.RequestByNode.Grid');
                    if (grid.getSelectionModel().getCount()) {
                        records = Ext.getCmp('App.RequestByNode.Grid').getSelectionModel().getSelections();
                        aux = new Array();
                        record_array = new Array();

                        for (var i = 0; i < records.length; i++) {
                            aux.push(records[i].data.request_id);
                        }
                        record_array = aux.join(',');
                        request_requested_by_comment = Ext.getCmp('request_requested_by_comment').getValue();

                        App.Request.RequestRejectByNode(3, record_array, request_requested_by_comment);
                        b.ownerCt.ownerCt.ownerCt.close();
                    } else {
                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                    }
                }
            }]
        }];
        App.Request.rejectRequestByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Request.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.export_request,
    width: 400,
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
                id: 'App.Request.form.file_name',
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
                        url: 'index.php/request/request/export',
                        method: 'POST',
                        params: {
                            node_id: App.Request.Store.baseParams.node_id,
                            file_name: Ext.getCmp('App.Request.form.file_name').getValue(),
                            request_status_id: Ext.getCmp('App.Request.form.request_status_id').getValue(),
                            request_problem_id: Ext.getCmp('App.Request.form.request_problem_id').getValue(),
                            start_date: Ext.getCmp('start_date').getValue(),
                            end_date: Ext.getCmp('end_date').getValue(),
                            search_branch: Ext.getCmp('App.Request.Search.formId').getValue()

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
        App.Costs.exportListWindow.superclass.initComponent.call(this);
    }
});

App.Request.exportListByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Request.export_request,
    width: 400,
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
                id: 'App.RequestByNode.form.file_name',
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
                        url: 'index.php/request/request/exportByNode',
                        method: 'POST',
                        params: {
                            node_id: App.RequestByNode.Store.baseParams.node_id,
                            file_name: Ext.getCmp('App.RequestByNode.form.file_name').getValue(),
                            request_status_id: Ext.getCmp('App.RequestByNode.form.request_status_id').getValue(),
                            request_problem_id: Ext.getCmp('App.RequestByNode.form.request_problem_id').getValue(),
                            start_date: Ext.getCmp('start_dateByNode').getValue(),
                            end_date: Ext.getCmp('end_dateByNode').getValue(),
                            search_branch: Ext.getCmp('App.RequestByNode.Search.formId').getValue(),
                            request_mail: Ext.getCmp('App.RequestByNode.Search.request_mail').getValue()

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