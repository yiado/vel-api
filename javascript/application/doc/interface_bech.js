App.Document.selectedDocumentId = null;
App.Document.CategoryName = null;
App.Document.doc_version_filename = null;
App.Document.doc_image_web = null;

App.Interface.addToModuleMenu('doc', App.ModuleActions[2006]);

App.Document.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [new App.Document.PrincipalClase(), {
            title: App.Language.General.bin,
            border: false,
            xtype: 'grid',
            iconCls: 'bin_icon',
            id: 'App.Document.GridPapelera',
            ref: 'docsGridPapelera',
            margins: '5 5 5 5',
            plugins: [new Ext.ux.OOSubmit()],
            region: 'center',
            loadMask: true,
            tbar: [App.ModuleActions[2002], {
                xtype: 'spacer',
                width: 10
            }, {
                text: App.Language.General.restore,
                iconCls: 'restore_icon',
                handler: function(b) {
                    grid = Ext.getCmp('App.Document.GridPapelera');
                    if (grid.getSelectionModel().getCount()) {
                        records = Ext.getCmp('App.Document.GridPapelera').getSelectionModel().getSelections();
                        aux = new Array();
                        for (var i = 0; i < records.length; i++) {
                            aux.push(records[i].data.doc_document_id);
                        }
                        doc_document_id = (aux.join(','));

                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Documentt.this_sure_restore_or_document, function(b) {
                            if (b == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: App.Language.General.message_generating_file,
                                    url: 'index.php/doc/document/sacarPapelera',
                                    timeout: 10000000000,
                                    params: {
                                        doc_document_id: doc_document_id
                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);
                                        //ACTUALIZA LOS THUMB (GALLERY)
                                        App.Document.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                                        App.Document.Store.load();
                                        Ext.getCmp('App.Document.GridPapelera').fireEvent('beforerender', Ext.getCmp('App.Document.GridPapelera'));
                                        Ext.getCmp('App.Document.GridDoc').fireEvent('beforerender', Ext.getCmp('App.Document.GridDoc'));
                                        Ext.FlashMessage.alert(response.msg);

                                    },
                                    failure: function(response) {
                                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                    }
                                });

                            } else {
                                Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                            }
                        });


                    } else {
                        Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                    }
                }
            }],
            listeners: {
                'beforerender': function(w) {
                    App.Document.Papelera.Store.load();
                }
            },
            viewConfig: {
                forceFit: true
            },
            store: App.Document.Papelera.Store,
            columns: [new Ext.grid.CheckboxSelectionModel(), {
                header: App.Language.General.file_name,
                sortable: true,
                dataIndex: 'doc_document_filename',
                renderer: function(val, metadata, record) {
                    return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                }
            }, {
                header: App.Language.General.version,
                sortable: true,
                width: 50,
                dataIndex: 'doc_version_code_client'

            }, {
                header: App.Language.Document.document_type,
                sortable: true,
                dataIndex: 'doc_extension_name',
                width: 70
            }, {
                header: App.Language.General.category,
                sortable: true,
                dataIndex: 'doc_category_name',
                width: 50
            }, {
                xtype: 'datecolumn',
                header: App.Language.Plan.upload_date,
                sortable: true,
                dataIndex: 'doc_document_creation',
                width: 60,
                format: App.General.DefaultDateTimeFormat,
                align: 'center'
            }, {
                xtype: 'datecolumn',
                header: App.Language.General.expiration_date,
                sortable: true,
                renderer: App.Document.ColorRowStatusUser,
                dataIndex: 'doc_version_expiration',
                width: 60,
                format: App.General.DatPatterns.DefaultDateFormat,
                aling: 'center'
            }, {
                dataIndex: 'doc_path',
                header: App.Language.Core.location,
                sortable: true,
                width: 100,
                renderer: function(doc_path, metadata, record, rowIndex, colIndex, store) {
                    metadata.attr = 'ext:qtip="' + doc_path + '"';
                    return doc_path;
                }
            }],
            sm: new Ext.grid.CheckboxSelectionModel()
        }];
        App.Document.Principal.superclass.initComponent.call(this);
    }
});

App.Document.TBar = [App.ModuleActions[2001], {
        xtype: 'spacer',
        width: 10
    }, App.ModuleActions[2008], {
        xtype: 'spacer',
        width: 10
    },
    {
        text: App.Language.General.send_to_trash,
        iconCls: 'bin_icon',
        handler: function(b) {
            grid = Ext.getCmp('App.Document.GridDoc');
            if (grid === undefined) {
                //ENTRA CUANDO ES XTEMPLATE
                gallery = Ext.getCmp('App.Document.Gallery');
                records = gallery.getSelectedRecords();

                aux = new Array();
                for (var i = 0; i < records.length; i++) {
                    aux.push(records[i].data.doc_document_id);
                }
                doc_document_id = (aux.join(','));

                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.this_insurance_send_to_the_trash_or_document, function(b) {
                    if (b == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/doc/document/enviarPapelera',
                            timeout: 10000000000,
                            params: {
                                doc_document_id: doc_document_id
                            },
                            success: function(response) {
                                response = Ext.decode(response.responseText);

                                //ACTUALIZA LOS THUMB (GALLERY)
                                App.Document.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                                App.Document.Store.load();

                                Ext.getCmp('App.Document.GridPapelera').fireEvent('beforerender', Ext.getCmp('App.Document.GridPapelera'));
                                Ext.FlashMessage.alert(response.msg);

                            },
                            failure: function(response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            }
                        });
                    }
                });
            } else { //ENTRA CUANDO ES GRILLA
                if (grid.getSelectionModel().getCount()) {
                    records = Ext.getCmp('App.Document.GridDoc').getSelectionModel().getSelections();
                    aux = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.doc_document_id);
                    }
                    doc_document_id = (aux.join(','));

                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.this_insurance_send_to_the_trash_or_document, function(b) {
                        if (b == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: App.Language.General.message_generating_file,
                                url: 'index.php/doc/document/enviarPapelera',
                                timeout: 10000000000,
                                params: {
                                    doc_document_id: doc_document_id
                                },
                                success: function(response) {
                                    response = Ext.decode(response.responseText);
                                    Ext.getCmp('App.Document.GridDoc').fireEvent('beforerender', Ext.getCmp('App.Document.GridDoc'));
                                    Ext.getCmp('App.Document.GridPapelera').fireEvent('beforerender', Ext.getCmp('App.Document.GridPapelera'));
                                    Ext.FlashMessage.alert(response.msg);
                                },
                                failure: function(response) {
                                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                }
                            });

                        }
                    });
                } else {
                    Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                }
            }
        }
    }, {
        xtype: 'tbseparator',
        width: 10
    },
    App.ModuleActions[2007], {
        xtype: 'spacer',
        width: 10
    },
    {
        text: App.Language.General.view_versions,
        iconCls: 'old_version_icon',
        handler: function(b) {
            grid = Ext.getCmp('App.Document.GridDoc');
            if (grid.getSelectionModel().getCount()) {
                App.Document.selectedDocumentId = grid.getSelectionModel().getSelected().data.doc_document_id;
                App.Document.CategoryName = grid.getSelectionModel().getSelected().data.doc_category_name;
                App.Document.Version.Store.setBaseParam('doc_document_id', App.Document.selectedDocumentId);
                App.Document.Version.Store.setBaseParam('doc_category_name', App.Document.CategoryName);
                App.Document.Version.Store.load({
                    callback: function() {
                        w = new App.Document.Version.Window();
                        w.show();
                    }
                });
            } else {
                Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
            }
        }
    }, {
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
    }, {
        xtype: 'tbseparator',
        width: 10
    }, {
        text: App.Language.General.img,
        iconCls: 'img_icon',
        handler: function(b) {
            App.Document.Store.load({ params: { node_id: App.Interface.selectedNodeId, doc_extension_id: 2 } });
        }
    }, {
        xtype: 'spacer',
        width: 10
    }, {
        text: App.Language.General.doc,
        iconCls: 'doc_icon',
        handler: function(b) {
            App.Document.Store.load({ params: { node_id: App.Interface.selectedNodeId, doc_extension_id: 100 } });
        }
    }, {
        xtype: 'spacer',
        width: 10
    }, {
        text: 'dwg',
        iconCls: 'img_icon',
        handler: function(b) {
            App.Document.Store.load({ params: { node_id: App.Interface.selectedNodeId, doc_extension_id: 101 } });
        }
    }, {
        xtype: 'spacer',
        width: 10
    }, {
        text: App.Language.General.all,
        iconCls: 'paste_icon',
        handler: function(b) {
            node_id = App.Document.Store.baseParams.node_id;
            App.Document.Store.baseParams = {};
            App.Document.Store.setBaseParam('node_id', node_id);
            App.Document.Store.load()
        }
    }, {
        xtype: 'tbseparator',
        width: 5
    }, {
        text: App.Language.General.eexport,
        iconCls: 'export_icon',
        handler: function() {
            w = new App.Document.exportListWindow();
            w.show();
        }
    }, {
        xtype: 'tbseparator',
        width: 10
    }, {
        xtype: 'tbseparator',
        width: 10
    },
    //    '->', 
    {
        text: App.Language.General.list,
        iconCls: 'list_icon',
        handler: function() {
            Ext.getCmp('App.Document.Principal').Principal.removeAll();
            Ext.getCmp('App.Document.Principal').Principal.add(new App.Document.GridView());
            Ext.getCmp('App.Document.Principal').Principal.doLayout();
        }
    }, {
        text: App.Language.General.gallery,
        iconCls: 'miniature_icon',
        handler: function() {
            Ext.getCmp('App.Document.Principal').Principal.removeAll();
            Ext.getCmp('App.Document.Principal').Principal.add(new App.Document.ThumbView());
            Ext.getCmp('App.Document.Principal').Principal.doLayout();
        }
    }
];

App.Document.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.eexport_list,
    width: 400,
    height: 150,
    layout: 'fit',
    modal: true,
    resizable: false,

    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            labelWidth: 150,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
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
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.eexport,
                handler: function(b) {
                    fp = b.ownerCt.ownerCt;
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            clientValidation: true,
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/doc/document/exportList',
                            params: App.Document.Store.baseParams,
                            success: function(form, response) {
                                document.location = 'index.php/app/download/' + response.result.file;
                                b.ownerCt.ownerCt.ownerCt.close();
                            },
                            failure: function(form, action) {
                                switch (action.failureType) {
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
        App.Document.exportListWindow.superclass.initComponent.call(this);
    }
});

App.Document.PrincipalClase = Ext.extend(Ext.Panel, {
    title: App.Language.General.documents,
    id: 'App.Document.Principal',
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: App.Document.TBar,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            region: 'north',
            plugins: [new Ext.ux.OOSubmit()],
            title: App.Language.General.searching,
            frame: true,
            ref: 'form',
            hidden: true,
            height: 260,
            margins: '5 5 0 5',
            padding: '5 5 5 5',
            border: true,
            fbar: [{
                text: App.Language.General.search,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    node_id = App.Document.Store.baseParams.node_id;
                    App.Document.Store.baseParams = form.getSubmitValues();
                    App.Document.Store.setBaseParam('node_id', node_id);
                    App.Document.Store.load();
                }
            }, {
                text: App.Language.General.clean,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    node_id = App.Document.Store.baseParams.node_id;
                    form.reset();
                    App.Document.Store.baseParams = {};
                    App.Document.Store.setBaseParam('node_id', node_id);
                    App.Document.Store.load();
                }
            }],
            items: [{
                layout: 'column',
                id: 'column_form_column_start_date',
                labelWidth: 130,
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: App.Language.General.file_name,
                        anchor: '90%',
                        name: 'doc_document_filename'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: App.Language.General.description,
                        anchor: '90%',
                        name: 'doc_document_description'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: App.Language.Document.keywords,
                        anchor: '90%',
                        name: 'doc_version_keyword'
                    }, {
                        xtype: 'textarea',
                        fieldLabel: App.Language.General.comment,
                        name: 'doc_version_comments',
                        height: 20,
                        anchor: '90%'
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.General.category,
                        anchor: '90%',
                        store: App.Document.Categoria.Store,
                        hiddenName: 'doc_category_id',
                        triggerAction: 'all',
                        displayField: 'doc_category_name',
                        valueField: 'doc_category_id',
                        editable: true,
                        typeAhead: true,
                        selectOnFocus: true,
                        forceSelection: true,
                        mode: 'remote',
                        minChars: 0
                    }, {
                        xtype: 'checkbox',
                        hideLabel: true,
                        boxLabel: App.Language.General.perform_internal_search,
                        name: 'search_branch',
                        inputValue: 1
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    id: 'form_column_start_date_interna',
                    items: [{
                        columnWidth: .2,
                        layout: 'form',
                        items: [{
                            xtype: 'label',
                            text: 'Seleccione un rango de fecha Interna del documento'
                        }]
                    }, {
                        columnWidth: .4,
                        layout: 'column',
                        id: 'column_start_date_interna',
                        frame: true,
                        items: [{
                            columnWidth: .5,
                            layout: 'form',
                            id: 'column_start_date_interna_1',
                            items: [{
                                xtype: 'datefield',
                                id: 'start_date_interna',
                                ref: '../start_date_interna',
                                fieldLabel: App.Language.General.start_date,
                                name: 'start_date_interna',
                                anchor: '95%',
                                listeners: {
                                    'select': function(fd, date) {
                                        fd.ownerCt.ownerCt.end_date_interna.setMinValue(date);
                                    }
                                }
                            }]
                        }, {
                            columnWidth: .5,
                            layout: 'form',
                            items: [{
                                xtype: 'datefield',
                                id: 'end_date_interna',
                                ref: '../end_date_interna',
                                fieldLabel: App.Language.General.end_date,
                                name: 'end_date_interna',
                                anchor: '95%',
                                listeners: {
                                    'select': function(fd, date) {
                                        fd.ownerCt.ownerCt.start_date_interna.setMaxValue(date);
                                    }
                                }
                            }]
                        }]
                    }, {
                        xtype: 'spacer',
                        height: 15
                    }, {
                        columnWidth: .2,
                        layout: 'form',
                        items: [{
                            xtype: 'label',
                            text: App.Language.Document.select_date_range_of_document_upload
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
                    }, {
                        columnWidth: .2,
                        layout: 'form',
                        items: [{
                            xtype: 'label',
                            text: App.Language.Document.select_range_of_document_expiration_dates
                        }]
                    }, {
                        columnWidth: .4,
                        layout: 'column',
                        frame: true,
                        items: [{
                            columnWidth: .5,
                            layout: 'form',
                            items: [{
                                xtype: 'datefield',
                                ref: '../start_date_exp',
                                fieldLabel: App.Language.General.start_date,
                                name: 'start_date_exp',
                                anchor: '95%',
                                listeners: {
                                    'select': function(fd, date) {
                                        fd.ownerCt.ownerCt.end_date_exp.setMinValue(date);
                                    }
                                }
                            }]
                        }, {
                            columnWidth: .5,
                            layout: 'form',
                            items: [{
                                xtype: 'datefield',
                                ref: '../end_date_exp',
                                fieldLabel: App.Language.General.end_date,
                                name: 'end_date_exp',
                                anchor: '95%',
                                listeners: {
                                    'select': function(fd, date) {
                                        fd.ownerCt.ownerCt.start_date_exp.setMaxValue(date);
                                    }
                                }
                            }]
                        }]
                    }]
                }]
            }]
        }, {
            xtype: 'panel',
            ref: 'Principal',
            region: 'center',
            layout: 'fit',
            border: false,
            margins: '5 5 5 5',
            items: new App.Document.GridView()
        }], App.Document.PrincipalClase.superclass.initComponent.call(this);
    }
});

App.Document.GridView = Ext.extend(Ext.grid.GridPanel, {
    xtype: 'grid',
    id: 'App.Document.GridDoc',
    ref: 'docsGrid',
    plugins: [new Ext.ux.OOSubmit()],
    region: 'center',
    border: true,
    loadMask: true,
    listeners: {
        'beforerender': function(w) {
            App.Document.Store.load();
        },
        'rowdblclick': function(grid, rowIndex) {

            App.Document.doc_version_filename = grid.getStore().getAt(rowIndex).data.doc_version_filename;
            App.Document.doc_image_web = grid.getStore().getAt(rowIndex).data.DocCurrentVersion.doc_image_web;
            //VENTANA AMPLIADA CON SUS VERSIONES
            App.Document.currentPosition = rowIndex;
            //            doc_version_filename = grid.getStore().getAt(rowIndex).data.doc_version_filename;
            w = new App.Document.VersionImagenWindow();
            w.show();

            //STORE DE LAS VERSIONES    
            App.Document.selectedDocumentId = grid.getStore().getAt(rowIndex).data.doc_document_id;
            App.Document.CategoryName = grid.getStore().getAt(rowIndex).data.doc_category_name;
            App.Document.Version.Store.setBaseParam('doc_document_id', App.Document.selectedDocumentId);
            App.Document.Version.Store.setBaseParam('doc_category_name', App.Document.CategoryName);
            App.Document.Version.Store.load();
            //            App.Document.doc_version_filename = grid.getStore().getAt(rowIndex).data.doc_version_filename;
        }
    },
    viewConfig: {
        forceFit: true
    },
    store: App.Document.Store,
    initComponent: function() {
        this.columns = [new Ext.grid.CheckboxSelectionModel(), {
                header: App.Language.General.file_name,
                sortable: true,
                dataIndex: 'doc_document_filename',
                renderer: function(val, metadata, record) {
                    return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                }
            }, {
                header: App.Language.General.version,
                sortable: true,
                width: 50,
                dataIndex: 'doc_version_code_client'

            }, {
                header: App.Language.Document.document_type,
                sortable: true,
                dataIndex: 'doc_extension_name',
                width: 70
            }, {
                header: App.Language.General.category,
                sortable: true,
                dataIndex: 'doc_category_name',
                width: 50
            }, {
                header: App.Language.Document.keywords,
                sortable: true,
                dataIndex: 'doc_version_keyword',
                width: 50
            }, {
                header: App.Language.General.comment,
                sortable: true,
                dataIndex: 'doc_version_comments',
                width: 50
            }, {
                xtype: 'datecolumn',
                header: 'Fecha Documento',
                sortable: true,
                dataIndex: 'doc_version_internal',
                format: App.General.DatPatterns.DefaultDateFormat,
                width: 60,
                aling: 'center'
            }, {
                xtype: 'datecolumn',
                header: App.Language.Plan.upload_date,
                sortable: true,
                dataIndex: 'doc_document_creation',
                width: 60,
                format: App.General.DefaultDateTimeFormat,
                align: 'center'
            }, {
                xtype: 'datecolumn',
                header: App.Language.General.expiration_date,
                sortable: true,
                renderer: App.Document.ColorRowStatusUser,
                dataIndex: 'doc_version_expiration',
                width: 60,
                format: App.General.DatPatterns.DefaultDateFormat,
                aling: 'center'
            }, {
                xtype: 'gridcolumn',
                header: App.Language.General.uploaded_by,
                dataIndex: 'user_name',
                width: 70,
                sortable: true
            }, {
                dataIndex: 'doc_path',
                header: App.Language.Core.location,
                sortable: true,
                width: 100,
                renderer: function(doc_path, metadata, record, rowIndex, colIndex, store) {
                    metadata.attr = 'ext:qtip="' + doc_path + '"';
                    return doc_path;
                }
            }],
            this.sm = new Ext.grid.CheckboxSelectionModel(), App.Document.GridView.superclass.initComponent.call(this);
    }

});
//ES LLAMADA DE GUI/INTERFACE2.JS
//ESTA ES LA VISTA PARA LA INTERFACE2 VISTA ARQUITECTURA
App.Document.GridView2 = Ext.extend(Ext.grid.GridPanel, {
    xtype: 'grid',
    id: 'App.Document.GridDoc2',
    plugins: [new Ext.ux.OOSubmit()],
    region: 'center',
    border: true,
    loadMask: true,
    listeners: {
        'beforerender': function(w) {
            App.Document.Store.load();
        },
        'rowdblclick': function(grid, rowIndex) {

            //VENTANA AMPLIADA CON SUS VERSIONES
            App.Document.currentPosition = rowIndex;
            w = new App.Document.VersionImagenWindow();
            w.show();

            //STORE DE LAS VERSIONES    
            App.Document.selectedDocumentId = grid.getStore().getAt(rowIndex).data.doc_document_id;
            App.Document.CategoryName = grid.getStore().getAt(rowIndex).data.doc_category_name;
            App.Document.Version.Store.setBaseParam('doc_document_id', App.Document.selectedDocumentId);
            App.Document.Version.Store.setBaseParam('doc_category_name', App.Document.CategoryName);
            App.Document.Version.Store.load();

        }
    },
    viewConfig: {
        forceFit: true
    },
    store: App.Document.Store,
    initComponent: function() {
        this.columns = [new Ext.grid.CheckboxSelectionModel(), {
                header: App.Language.General.file_name,
                sortable: true,
                dataIndex: 'doc_document_filename',
                renderer: function(val, metadata, record) {
                    return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                }
            }, {
                xtype: 'datecolumn',
                header: App.Language.Plan.upload_date,
                dataIndex: 'doc_document_creation',
                width: 60,
                format: App.General.DefaultDateTimeFormat,
                aling: 'center'
            }],
            this.sm = new Ext.grid.CheckboxSelectionModel(), App.Document.GridView2.superclass.initComponent.call(this);
    }

});

App.Document.AbrirImagen = function(doc_document_id, position) {
    App.Document.selectedDocumentId = doc_document_id;
    App.Document.currentPosition = position - 1; // SE RESTA 1 PORQUE EL STORE PARTE EN 0 Y EL XTemplate PARTE EN 1 
    //VENTANA AMPLIADA CON SUS VERSIONES
    w = new App.Document.VersionImagenWindow();
    w.show();
};

App.Document.ThumbView = Ext.extend(Ext.DataView, {
    id: 'App.Document.Gallery',
    itemSelector: 'div.thumb-wrap',
    style: 'overflow:auto',
    region: 'center',
    multiSelect: true,
    store: App.Document.Store,
    tpl: new Ext.XTemplate('<tpl for=".">',
        '<div class="thumb-wrap" id="{doc_document_id}">',
        '<tpl if="values.DocCurrentVersion.doc_image_web == \'0\'">',
        '<div class="thumb"   ><img  src="docs/thumb/not_image_icon.png" ondblclick="App.Document.AbrirImagen({doc_document_id}, {#})" class="thumb-img"/></div>',
        '<span class="thumb-wrap-span">{doc_document_filename}</span></div>',
        '</tpl>',
        '<tpl if="values.DocCurrentVersion.doc_image_web == \'1\'">',
        '<div class="thumb"   ><img src="docs/thumb/{doc_version_filename}?id={[new Date().getTime()]}" ondblclick="App.Document.AbrirImagen({doc_document_id}, {#})" class="thumb-img"/></div>',
        '<span class="thumb-wrap-span">{doc_document_filename}</span></div>',
        '</tpl>',
        '</div>',
        '</tpl>',
        '<div class="x-clear"></div>'),
    listeners: {
        'beforerender': function(w) {
            App.Document.Store.load();
        }
    }
});

App.Document.VersionImagenWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.versions_of_stock,
    width: 1000,
    height: 600,
    id: 'App.Document.VersionImagenWindow',
    modal: true,
    maximizable: true,
    resizable: true,
    layout: 'border',
    border: false,
    enableKeyEvents: true,
    tbar: [{
        xtype: 'button',
        iconCls: 'previous_icon',
        handler: function(b) {
            App.Document.currentPosition = App.Document.currentPosition - 1;
            record = App.Document.Store.getAt(App.Document.currentPosition);
            // VALIDA QUE NO SE MENOR A CERO
            if (App.Document.currentPosition >= 0) {

                b.ownerCt.ownerCt.updateImage(record.data.doc_version_filename, record.data.doc_image_web);
            } else {
                App.Document.currentPosition = App.Document.currentPosition + 1;
            }
        }
    }, {
        xtype: 'tbseparator',
        width: 5
    }, {
        iconCls: 'next_icon',
        handler: function(b) {
            App.Document.currentPosition = App.Document.currentPosition + 1;
            record = App.Document.Store.getAt(App.Document.currentPosition);
            //VALIDA QUE NO SEA MAYOR AL TOTAL
            if (App.Document.currentPosition < App.Document.Store.getCount()) {
                b.ownerCt.ownerCt.updateImage(record.data.doc_version_filename, record.data.doc_image_web);
            } else {
                App.Document.currentPosition = App.Document.currentPosition - 1;
            }
        }
    }, {
        xtype: 'tbseparator',
        width: 5
    }, {
        text: App.Language.General.download,
        iconCls: 'download_icon',
        handler: function(b) {
            grid = Ext.getCmp('App.Document.GridDocVersion');
            if (grid.getSelectionModel().getCount()) {
                window.location = 'index.php/doc/document/download/' + grid.getSelectionModel().getSelected().data.doc_version_id;
            } else {
                Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
            }
        }
    }, {
        xtype: 'tbseparator',
        width: 20
    }, {
        text: App.Language.Document.rotate_left,
        iconCls: 'arrow-rotate-1',
        handler: function(b) {
            grid = Ext.getCmp('App.Document.GridDoc');
            if (grid === undefined) {

                Ext.Ajax.request({
                    waitTitle: App.Language.General.message_please_wait,
                    waitMsg: App.Language.Document.rotating_picture,
                    url: 'index.php/doc/document/vuelveFile',
                    method: 'POST',
                    params: {
                        doc_document_id: App.Document.selectedDocumentId
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);

                        doc_version_filename = response.data.DocVersion[0].doc_version_filename;

                        var msg = Ext.MessageBox.wait(App.Language.General.please_wait, "Rotando Imagen");
                        Ext.Ajax.request({
                            url: 'index.php/doc/document/rotacion1',
                            method: 'POST',
                            params: {
                                doc_version_filename: doc_version_filename
                            },
                            success: function(response) {
                                response = Ext.decode(response.responseText);

                                doc_image_web = response.data.DocCurrentVersion.doc_image_web;
                                Ext.getCmp('App.Document.VersionImagenWindow').updateImage(doc_version_filename, doc_image_web);
                                Ext.getCmp('App.Document.VersionImagenWindow').fireEvent('beforerender', Ext.getCmp('App.Document.VersionImagenWindow'));
                                App.Document.Store.load();

                            },
                            failure: function(response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            },
                            callback: function() {
                                msg.hide()
                            }
                        });

                    },
                    failure: function(response) {
                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                    }
                });
            } else {
                var msg = Ext.MessageBox.wait(App.Language.General.please_wait, App.Language.Document.rotating_picture);
                Ext.Ajax.request({
                    url: 'index.php/doc/document/rotacion1',
                    method: 'POST',
                    params: {
                        doc_version_filename: App.Document.doc_version_filename
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);

                        Ext.getCmp('App.Document.VersionImagenWindow').updateImage(response.data.DocCurrentVersion.doc_version_filename, response.data.DocCurrentVersion.doc_image_web);
                        Ext.getCmp('App.Document.VersionImagenWindow').fireEvent('beforerender', Ext.getCmp('App.Document.VersionImagenWindow'));
                        App.Document.Store.load();

                    },
                    failure: function(response) {
                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                    },
                    callback: function() {
                        msg.hide()
                    }
                });
            }
        }
    }, {
        text: App.Language.Document.rotate_right,
        iconCls: 'arrow-rotate-2',
        handler: function(b) {
            grid = Ext.getCmp('App.Document.GridDoc');
            if (grid === undefined) {
                Ext.Ajax.request({
                    waitTitle: App.Language.General.message_please_wait,
                    waitMsg: App.Language.Document.rotating_picture,
                    url: 'index.php/doc/document/vuelveFile',
                    method: 'POST',
                    params: {
                        doc_document_id: App.Document.selectedDocumentId
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);

                        doc_version_filename = response.data.DocVersion[0].doc_version_filename;

                        var msg = Ext.MessageBox.wait(App.Language.General.please_wait, App.Language.Document.rotating_picture);
                        Ext.Ajax.request({
                            url: 'index.php/doc/document/rotacion2',
                            method: 'POST',
                            params: {
                                doc_version_filename: doc_version_filename
                            },
                            success: function(response) {
                                response = Ext.decode(response.responseText);

                                doc_image_web = response.data.DocCurrentVersion.doc_image_web;
                                Ext.getCmp('App.Document.VersionImagenWindow').updateImage(doc_version_filename, doc_image_web);
                                Ext.getCmp('App.Document.VersionImagenWindow').fireEvent('beforerender', Ext.getCmp('App.Document.VersionImagenWindow'));
                                App.Document.Store.load();

                            },
                            failure: function(response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            },
                            callback: function() {
                                msg.hide()
                            }
                        });

                    },
                    failure: function(response) {
                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                    }
                });
            } else {
                var msg = Ext.MessageBox.wait(App.Language.General.please_wait, App.Language.Document.rotating_picture);
                Ext.Ajax.request({
                    url: 'index.php/doc/document/rotacion2',
                    method: 'POST',
                    params: {
                        doc_version_filename: App.Document.doc_version_filename
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);

                        Ext.getCmp('App.Document.VersionImagenWindow').updateImage(response.data.DocCurrentVersion.doc_version_filename, response.data.DocCurrentVersion.doc_image_web);
                        Ext.getCmp('App.Document.VersionImagenWindow').fireEvent('beforerender', Ext.getCmp('App.Document.VersionImagenWindow'));
                        App.Document.Store.load();

                    },
                    failure: function(response) {
                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                    },
                    callback: function() {
                        msg.hide()
                    }
                });
            }
        }

    }],
    updateImage: function(doc_version_filename, doc_image_web) {
        //ACTUALIZA LA IMAGEN       

        var d = new Date();
        var n = d.getTime();

        this.imagepanel.removeAll();

        this.imagepanel.add(new Ext.Panel({
            layout: 'fit',
            overflowY: 'scroll',
            html: (doc_image_web == 1 ? '<img width=100% src="docs/' + doc_version_filename + '?id=' + n + '" />' : '<div align="center"><br><br><br><br><br><br><br><br><br><br><br><br><img  src="docs/thumb/not_image_icon.png" /></div>')
                //html: (doc_image_web == 1 ? '<img width=100% src="docs/' + doc_version_filename + '" />' : '<div align="center"><br><br><br><br><br><br><br><br><br><br><br><br><img  src="docs/thumb/not_image_icon.png" /></div>')
        }));
        this.imagepanel.doLayout();
        record = App.Document.Store.getAt(App.Document.currentPosition);
        //CARGA LAS VERSIONES
        App.Document.Version.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        App.Document.Version.Store.setBaseParam('doc_document_id', record.data.doc_document_id);
        App.Document.Version.Store.load();

    },
    keys: [{
        key: Ext.EventObject.RIGHT,
        fn: function() {
            App.Document.currentPosition = App.Document.currentPosition + 1;
            record = App.Document.Store.getAt(App.Document.currentPosition);
            //VALIDA QUE NO SEA MAYOR AL TOTAL
            if (App.Document.currentPosition < App.Document.Store.getCount()) {
                Ext.getCmp('App.Document.VersionImagenWindow').updateImage(record.data.doc_version_filename, record.data.doc_image_web);
            } else {
                App.Document.currentPosition = App.Document.currentPosition - 1;
            }
        }
    }, {
        key: Ext.EventObject.LEFT,
        fn: function() {

            App.Document.currentPosition = App.Document.currentPosition - 1;
            record = App.Document.Store.getAt(App.Document.currentPosition);
            // VALIDA QUE NO SE MENOR A CERO
            if (App.Document.currentPosition >= 0) {
                Ext.getCmp('App.Document.VersionImagenWindow').updateImage(record.data.doc_version_filename, record.data.doc_image_web);
            } else {
                App.Document.currentPosition = App.Document.currentPosition + 1;
            }
        }
    }],
    listeners: {
        'beforerender': function(w) {
            record = App.Document.Store.getAt(App.Document.currentPosition);
            w.updateImage(record.data.doc_version_filename, record.data.doc_image_web);
        }
    },
    initComponent: function() {
        this.items = [{
            //ESTA ES LA IMAGEN AMPLIADA
            xtype: 'panel',
            id: 'App.Document.PanelImagen',
            style: 'padding: 5 0 5 5',
            region: 'center',
            ref: 'imagepanel',
            autoScroll: true,
            border: false,
            layoutConfig: {
                align: 'middle'
            },
            overflowY: 'scroll',
            layout: 'fit'
        }, {
            xtype: 'grid',
            id: 'App.Document.GridDocVersion',
            title: App.Language.General.versions,
            region: 'east',
            resizable: true,
            layauot: 'fit',
            split: true,
            collapsible: true,
            collapseFirst: true,
            width: 300,
            style: 'padding: 5 5 5 0',
            loadMask: true,
            store: App.Document.Version.Store,
            maskDisabled: false,
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    w = grid.ownerCt;
                    w.updateImage(grid.getStore().getAt(rowIndex).data.doc_version_filename, grid.getStore().getAt(rowIndex).data.doc_image_web);
                }
            },
            tbar: {
                xtype: 'toolbar',
                padding: 5,
                plugins: [new Ext.ux.OOSubmit()],
                items: [{
                    xtype: 'spacer',
                    width: 5
                }, App.ModuleActions[2003], {
                    xtype: 'spacer',
                    width: 5
                }, App.ModuleActions[2005]]
            },
            viewConfig: {
                forceFit: true,
                folderSort: true
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    header: App.Language.General.version,
                    sortable: true,
                    width: 150,
                    dataIndex: 'doc_version_code_client'
                }, {
                    header: App.Language.General.file_name,
                    sortable: true,
                    width: 60,
                    dataIndex: 'DocDocument',
                    renderer: function(val, metadata, record) {
                        var class_expiration = '';
                        if (App.Document.ExpirationDocument(val, record) == true) {
                            class_expiration = "style='color:red'";
                        }
                        return "<a href='index.php/doc/document/download/" + record.data.doc_version_id + "' " + class_expiration + ">" + val.doc_document_filename + "</a>";
                    }
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({ singleSelect: true })
        }];
        App.Document.VersionImagenWindow.superclass.initComponent.call(this);
    }
});

App.Document.Principal.listener = function(node) {
    if (node && node.id) {
        App.Document.Store.setBaseParam('node_id', node.id);
        App.Document.Store.load();
    }
};

App.Document.Version.Window = Ext.extend(Ext.Window, {
    title: App.Language.General.versions,
    width: 900,
    height: 410,
    modal: true,
    maximizable: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.selModel = new Ext.grid.CheckboxSelectionModel({
            checkOnly: false
        });
        this.items = [{
            xtype: 'grid',
            id: 'App.Document.VersionGrid',
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'beforerender': function() {
                    App.Document.Version.Store.load();
                },
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Document.editionNewVersion(record);
                }
            },
            store: App.Document.Version.Store,
            tbar: {
                xtype: 'toolbar',
                padding: 5,
                plugins: [new Ext.ux.OOSubmit()],
                items: [App.ModuleActions[2003], {
                    xtype: 'spacer',
                    width: 5
                }, App.ModuleActions[2005]]
            },
            columns: [this.selModel, {
                header: App.Language.General.file_name,
                sortable: true,
                width: 160,
                dataIndex: 'DocDocument',
                renderer: function(val, metadata, record) {
                    var class_expiration = '';
                    if (App.Document.ExpirationDocument(val, record) == true) {
                        class_expiration = "style='color:red'";
                    }
                    return "<a href='index.php/doc/document/download/" + record.data.doc_version_id + "' " + class_expiration + ">" + val.doc_document_filename + "</a>";
                }
            }, {
                header: App.Language.General.version,
                sortable: true,
                width: 50,
                dataIndex: 'doc_version_code_client'
            }, {
                header: App.Language.Document.keywords,
                sortable: true,
                dataIndex: 'doc_version_keyword',
                width: 50
            }, {
                header: App.Language.General.comment,
                renderer: App.Document.ColorRowStatusUser,
                sortable: true,
                width: 50,
                dataIndex: 'doc_version_comments'
            }, {
                xtype: 'datecolumn',
                header: 'Fecha Documento',
                sortable: true,
                dataIndex: 'doc_version_internal',
                format: App.General.DatPatterns.DefaultDateFormat,
                width: 60,
                aling: 'center'
            }, {
                xtype: 'datecolumn',
                header: App.Language.Plan.upload_date,
                renderer: App.Document.ColorRowStatusUser,
                dataIndex: 'doc_version_creation',
                sortable: true,
                width: 60,
                format: App.General.DefaultDateTimeFormat,
                aling: 'center'
            }, {
                xtype: 'datecolumn',
                header: App.Language.General.expiration_date,
                renderer: App.Document.ColorRowStatusUser,
                dataIndex: 'doc_version_expiration',
                width: 60,
                format: App.General.DatPatterns.DefaultDateFormat,
                aling: 'center'
            }, {
                xtype: 'gridcolumn',
                header: App.Language.General.uploaded_by,
                renderer: App.Document.ColorRowStatusUser,
                dataIndex: 'user_name',
                width: 70,
                sortable: true
            }, {
                dataIndex: 'DocDocument',
                header: App.Language.Core.location,
                sortable: true,
                width: 100,
                renderer: function(DocDocument, metadata, record, rowIndex, colIndex, store) {
                    metadata.attr = 'ext:qtip="' + DocDocument.doc_path + '"';
                    return DocDocument.doc_path;
                }
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    App.Document.Store.load();
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Document.Version.Window.superclass.initComponent.call(this);
    }
});

App.Document.updateCategoryWindow = Ext.extend(Ext.Window, {
    title: 'Edición de la Categoría del Documento',
    resizable: false,
    modal: true,
    width: 380,
    height: 140,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.category,
                anchor: '100%',
                id: 'App.Document.updateCate',
                store: App.Document.Categoria.Store,
                hiddenName: 'doc_category_id',
                triggerAction: 'all',
                displayField: 'doc_category_name',
                valueField: 'doc_category_id',
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
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                handler: function(b) {
                    if (Ext.getCmp('App.Document.updateCate').getValue() != '') {
                        Ext.Ajax.request({
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/doc/document/updateCategory',
                            params: {
                                doc_document_id: doc_document_id,
                                doc_category_id: Ext.getCmp('App.Document.updateCate').getValue()
                            },
                            success: function(response) {
                                response = Ext.decode(response.responseText);
                                App.Document.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(response.msg);

                            },
                            failure: function(response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            }
                        });
                    } else {
                        Ext.FlashMessage.alert('Debe Seleccionar una Categoría para Editar ');
                    }
                }
            }]
        }];
        App.Document.updateCategoryWindow.superclass.initComponent.call(this);
    }
});

App.Document.addDocumentWindow = Ext.extend(Ext.Window, {
    title: App.Language.Document.add_document_title,
    resizable: false,
    modal: true,
    width: (screen.width < 400) ? screen.width : 500,
    height: 500,
    maximizable: true,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            padding: 5,
            items: [{
                xtype: 'fileuploadfield',
                emptyText: App.Language.General.select_document,
                fieldLabel: App.Language.General.document,
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'documento',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.description,
                name: 'doc_document_description',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.version,
                name: 'doc_version_code_client',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'combo',
                ref: '../categoria',
                fieldLabel: App.Language.General.category,
                anchor: '100%',
                store: App.Document.Categoria.Store,
                hiddenName: 'doc_category_id',
                triggerAction: 'all',
                displayField: 'doc_category_name',
                valueField: 'doc_category_id',
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
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Document.keywords,
                name: 'doc_version_keyword',
                anchor: '100%',
                allowBlank: true
            }, {
                xtype: 'datefield',
                anchor: '100%',
                fieldLabel: 'Fecha Documento',
                name: 'doc_version_internal'
            }, {
                xtype: 'datefield',
                anchor: '100%',
                fieldLabel: App.Language.General.expiration_date,
                name: 'doc_version_expiration'
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.Document.alert_days,
                name: 'doc_version_alert',
                anchor: '100%',
                allowBlank: true
            }, {
                xtype: 'textareabutton',
                fieldLabel: App.Language.General.expiration_mail_alert,
                id: 'App.Document.AlertMailField',
                vtype: 'multiemail',
                name: 'doc_version_alert_email',
                anchor: '100%',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'account_icon',
                    handler: function(b) {
                        w = new App.Document.addUsersWindow({
                            sentTo: 'App.Document.AlertMailField'
                        });
                        w.show();
                    }
                }
            }, {
                xtype: 'textareabutton',
                fieldLabel: App.Language.General.mail_notification,
                id: 'App.Document.NotificationMailField',
                vtype: 'multiemail',
                name: 'doc_version_notification_email',
                anchor: '100%',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'account_icon',
                    handler: function(b) {
                        w = new App.Document.addUsersWindow({
                            sentTo: 'App.Document.NotificationMailField'
                        });
                        w.show();
                    }
                }
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.comment,
                name: 'doc_version_comments',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/doc/document/add',
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            waitMsg: App.Language.General.message_up_document,
                            success: function(fp, o) {
                                App.Document.Store.load();
                                App.Document.Vencido.Store.load();
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
        App.Document.addDocumentWindow.superclass.initComponent.call(this);
    }
});

App.Document.addMasiveDocumentWindow = Ext.extend(Ext.Window, {
    title: App.Language.Document.add_document_title,
    resizable: false,
    modal: true,
    width: (screen.width < 400) ? screen.width : 500,
    height: 500,
    maximizable: true,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            padding: 5,
            items: [{
                xtype: 'fileuploadfield',
                emptyText: App.Language.General.select_document,
                fieldLabel: App.Language.General.document,
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'documento',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.description,
                name: 'doc_document_description',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.version,
                name: 'doc_version_code_client',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'combo',
                ref: '../categoria',
                fieldLabel: App.Language.General.category,
                anchor: '100%',
                store: App.Document.Categoria.Store,
                hiddenName: 'doc_category_id',
                triggerAction: 'all',
                displayField: 'doc_category_name',
                valueField: 'doc_category_id',
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
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Document.keywords,
                name: 'doc_version_keyword',
                anchor: '100%',
                allowBlank: true
            }, {
                xtype: 'datefield',
                anchor: '100%',
                fieldLabel: 'Fecha Documento',
                name: 'doc_version_internal'
            }, {
                xtype: 'datefield',
                anchor: '100%',
                fieldLabel: App.Language.General.expiration_date,
                name: 'doc_version_expiration'
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.Document.alert_days,
                name: 'doc_version_alert',
                anchor: '100%',
                allowBlank: true
            }, {
                xtype: 'textareabutton',
                fieldLabel: App.Language.General.expiration_mail_alert,
                id: 'App.Document.AlertMailField',
                vtype: 'multiemail',
                name: 'doc_version_alert_email',
                anchor: '100%',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'account_icon',
                    handler: function(b) {
                        w = new App.Document.addUsersWindow({
                            sentTo: 'App.Document.AlertMailField'
                        });
                        w.show();
                    }
                }
            }, {
                xtype: 'textareabutton',
                fieldLabel: App.Language.General.mail_notification,
                id: 'App.Document.NotificationMailField',
                vtype: 'multiemail',
                name: 'doc_version_notification_email',
                anchor: '100%',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'account_icon',
                    handler: function(b) {
                        w = new App.Document.addUsersWindow({
                            sentTo: 'App.Document.NotificationMailField'
                        });
                        w.show();
                    }
                }
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.comment,
                name: 'doc_version_comments',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/doc/document/addMasive',
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            waitMsg: App.Language.General.message_up_document,
                            success: function(fp, o) {
                                App.Document.Store.load();
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
        App.Document.addMasiveDocumentWindow.superclass.initComponent.call(this);
    }
});

App.Document.addMasiveZipExcelWindow = Ext.extend(Ext.Window, {
    title: App.Language.Document.bulk_upload_documents,
    resizable: false,
    modal: true,
    width: (screen.width < 500) ? screen.width : 500,
    height: 200,
    maximizable: true,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            padding: 5,
            tbar: {
                xtype: 'toolbar',
                padding: 5,
                plugins: [new Ext.ux.OOSubmit()],
                items: [{
                    text: App.Language.Document.download_excel_format,
                    iconCls: 'export_icon',
                    handler: function() {
                        Ext.Ajax.request({
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/doc/document/formatoExcelDetalle',
                            method: 'POST',
                            success: function(response) {
                                response = Ext.decode(response.responseText);
                                document.location = response.file;
                            },
                            failure: function(response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            }
                        });
                    }
                }]
            },
            items: [{
                xtype: 'fileuploadfield',
                emptyText: App.Language.Document.select_a_zip,
                fieldLabel: App.Language.General.document,
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'documentoZip',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }, {
                xtype: 'fileuploadfield',
                emptyText: App.Language.Document.select_a_excel,
                fieldLabel: App.Language.Document.document_details,
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'documentoExcel',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/doc/document/addZipExcel',
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            waitMsg: App.Language.General.message_up_document,
                            success: function(fp, o) {
                                App.Document.Store.load();
                                App.Document.Vencido.Store.load();
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
        App.Document.addMasiveZipExcelWindow.superclass.initComponent.call(this);
    }
});

App.Document.addVersionDocumentWindow = Ext.extend(Ext.Window, {
    title: App.Language.Document.add_version_document_title,
    resizable: false,
    modal: true,
    width: 500,
    maximizable: true,
    height: 500,
    layout: 'fit',
    padding: 1,
    listeners: {
        'beforerender': function(w) {
            Ext.getCmp('App.Document.VersionCat').setValue(App.Document.CategoryName);
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            plugins: [new Ext.ux.OOSubmit()],
            fileUpload: true,
            padding: 5,
            labelWidth: 152,
            items: [{
                xtype: 'fileuploadfield',
                emptyText: App.Language.General.select_document,
                fieldLabel: App.Language.General.document,
                ref: 'document',
                anchor: '100%',
                fileUpload: true,
                allowBlank: false,
                name: 'documento',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }, {
                layout: 'column',
                border: false,
                hidden: false,
                id: 'App.Document.VersionTextfield',
                items: [{
                    columnWidth: .32,
                    border: false,
                    labelWidth: '100%',
                    layout: 'form',
                    items: [{
                        xtype: 'label',
                        text: App.Language.General.version,
                        anchor: '100%'
                    }]
                }, {
                    columnWidth: .68,
                    ref: 'form_asset',
                    labelWidth: 1,
                    border: false,
                    layout: 'form',
                    items: [{
                        xtype: 'textfield',
                        allowBlank: true,
                        name: 'doc_version_code_client',
                        anchor: '100%'
                    }]
                }]
            }, {
                layout: 'column',
                border: false,
                hidden: true,
                id: 'App.Document.VersionDisplayColum',
                items: [{
                    columnWidth: .32,
                    border: false,
                    labelWidth: '100%',
                    layout: 'form',
                    items: [{
                        xtype: 'label',
                        text: App.Language.General.version,
                        anchor: '100%'
                    }]
                }, {
                    columnWidth: .68,
                    ref: 'form_asset',
                    labelWidth: 1,
                    border: false,
                    layout: 'form',
                    items: [{
                        xtype: 'displayfield',
                        id: 'App.Document.VersionDisplay',
                        anchor: '100%'
                    }]
                }]
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Document.keywords,
                name: 'doc_version_keyword',
                anchor: '100%'
            }, {
                xtype: 'datefield',
                fieldLabel: 'Fecha Documento',
                name: 'doc_version_internal',
                anchor: '100%',
                height: 100
            }, {
                xtype: 'datefield',
                fieldLabel: App.Language.General.expiration_date,
                name: 'doc_version_expiration',
                anchor: '100%',
                height: 100
            }, {
                xtype: 'displayfield',
                id: 'App.Document.VersionCat',
                fieldLabel: App.Language.General.category,
                name: 'doc_category_name'
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.Document.alert_days,
                name: 'doc_version_alert',
                anchor: '100%'
            }, {
                xtype: 'textareabutton',
                fieldLabel: App.Language.General.expiration_mail_alert,
                id: 'App.Document.AlertMailField',
                vtype: 'multiemail',
                name: 'doc_version_alert_email',
                anchor: '100%',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'account_icon',
                    handler: function(b) {
                        w = new App.Document.addUsersWindow({
                            sentTo: 'App.Document.AlertMailField'
                        });
                        w.show();
                    }
                }
            }, {
                xtype: 'textareabutton',
                fieldLabel: App.Language.General.mail_notification,
                id: 'App.Document.NotificationMailField',
                vtype: 'multiemail',
                name: 'doc_version_notification_email',
                anchor: '100%',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'account_icon',
                    handler: function(b) {
                        w = new App.Document.addUsersWindow({
                            sentTo: 'App.Document.NotificationMailField'
                        });
                        w.show();
                    }
                }
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.comment,
                name: 'doc_version_comments',
                anchor: '100%',
                height: 100
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/doc/docversion/add',
                            params: {
                                node_id: App.Interface.selectedNodeId,
                                doc_document_id: App.Document.selectedDocumentId
                            },
                            waitMsg: App.Language.General.message_up_document,
                            success: function(fp, o) {
                                App.Document.Version.Store.load();
                                App.Document.Store.load();
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
        App.Document.addVersionDocumentWindow.superclass.initComponent.call(this);
    }
});

App.Document.editionNewVersion = function(record) {
    w = new App.Document.addVersionDocumentWindow({
        title: App.Language.Document.edit_version_document_title
    });
    if (App.Security.Actions[2004] === undefined) {
        w.form.saveButton.hide();
    }
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    record = w.form.record;
    w.form.document.setDisabled(true);
    form = w.form.getForm();
    doc_version_code_client = record.data.doc_version_code_client;
    Ext.getCmp('App.Document.VersionTextfield').setVisible(false);
    Ext.getCmp('App.Document.VersionDisplay').setValue(doc_version_code_client);
    Ext.getCmp('App.Document.VersionDisplayColum').setVisible(true);
    Ext.getCmp('App.Document.NotificationMailField').setDisabled(true);
    w.form.saveButton.handler = function(b) {
        if (form.isValid()) {
            form.updateRecord(record);
            App.Document.Version.Store.load();
            Ext.getCmp('App.Document.VersionGrid').fireEvent('beforerender', Ext.getCmp('App.Document.VersionGrid'));
            b.ownerCt.ownerCt.ownerCt.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Document.ExpirationDocument = function(value, record) {
    var fecha_expiration = record.get('doc_version_expiration');
    var fecha_system = App.Security.Session.system_current_date.split('-');
    var status = false;
    if (fecha_expiration != null) {
        var miFecha = new Date(fecha_expiration);
        var diaExpira = miFecha.getDate();
        var mesExpira = miFecha.getMonth() + 1; //Los meses van del 0 al 11
        var anoExpira = miFecha.getFullYear();
        var f1 = new Date(anoExpira, mesExpira, diaExpira);
        var f2 = new Date(fecha_system[0], fecha_system[1], fecha_system[2]);
        if (f2.getTime() > f1.getTime()) {
            status = true;
        }
    }
    return status;
}

App.Document.addUsersWindow = Ext.extend(Ext.Window, {
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
                    grid = Ext.getCmp('App.Document.GridUsers');
                    records = Ext.getCmp('App.Document.GridUsers').getSelectionModel().getSelections();
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
                        App.Core.User.Store.baseParams = form.getSubmitValues();
                        App.Core.User.Store.setBaseParam('user_id', null);
                        App.Core.User.Store.load();
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        form.reset();
                        App.Core.User.Store.setBaseParam([]);
                        App.Core.User.Store.load();
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
                id: 'App.Document.GridUsers',
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
                columns: [new Ext.grid.CheckboxSelectionModel(), {
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
                }],
                sm: new Ext.grid.CheckboxSelectionModel()
            }]
        }]
        App.Document.addUsersWindow.superclass.initComponent.call(this);
    }
});