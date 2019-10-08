/* global App, Ext */

App.Asset.copiedAsset = new Array();
App.Asset.selectedAssetId = null;
valoraux = null;
App.Asset.treeSearchToolBar = [{
    text: App.Language.Asset.inventory,
    iconCls: 'inventory_sync_icon',
    cls: 'permits',
    id: 'ModuleAction_4007',
    hidden: (App.Security.Actions[4007] === undefined ? true : false),
    handler: function(b, state) {
        w = new App.Asset.Inventory.window();
        w.show();
    }
}, {
    xtype: 'tbseparator',
    cls: 'permits',
    width: 5
}, {
    text: App.Language.Asset.bulk_upload,
    iconCls: 'upload_icon',
    cls: 'permits',
    id: 'ModuleAction_4008',
    hidden: (App.Security.Actions[4008] === undefined ? true : false),
    handler: function(b, state) {
        wacm = new App.Asset.CargaMasiva.window();
        wacm.show();
    }
}];

App.Interface.addToModuleMenu('asset', App.ModuleActions[4000]);

App.Asset.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [
            new App.Asset.PrincipalClase(),
            {
                title: App.Language.General.bin,
                xtype: 'grid',
                iconCls: 'bin_icon',
                id: 'App.Asset.GridPapelera',
                ref: 'assetGridPapelera',
                margins: '5 5 5 5',
                region: 'center',
                height: 600,
                border: false,
                loadMask: true,
                tbar: [{
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    cls: 'permits',
                    id: 'ModuleAction_4004',
                    hidden: (App.Security.Actions[4004] === undefined ? true : false),
                    handler: function(b) {
                        grid = b.ownerCt.ownerCt.ownerCt.assetGridPapelera;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Asset.Papelera.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    text: App.Language.General.restore,
                    iconCls: 'restore_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('App.Asset.GridPapelera');
                        if (grid.getSelectionModel().getCount()) {
                            records = Ext.getCmp('App.Asset.GridPapelera').getSelectionModel().getSelections();
                            aux = new Array();
                            for (var i = 0; i < records.length; i++) {
                                aux.push(records[i].data.asset_id);
                            }
                            asset_id = (aux.join(','));

                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Document.this_sure_restore_or_document,
                                function(b) {
                                    if (b == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: App.Language.General.message_generating_file,
                                            url: 'index.php/asset/asset/sacarPapelera',
                                            timeout: 10000000000,
                                            params: {
                                                asset_id: asset_id
                                            },
                                            success: function(response) {
                                                response = Ext.decode(response.responseText);
                                                Ext.getCmp('App.Asset.GridPapelera').fireEvent('beforerender', Ext.getCmp('App.Asset.GridPapelera'));
                                                Ext.getCmp('App.Asset.Grid').fireEvent('beforerender', Ext.getCmp('App.Asset.Grid'));
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
                        App.Asset.Papelera.Store.load();
                    }

                },
                viewConfig: {
                    forceFit: true
                },
                store: App.Asset.Papelera.Store,
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        header: App.Language.General.name,
                        sortable: true,
                        dataIndex: 'asset_name'
                    }, {
                        header: App.Language.General.brand,
                        sortable: true,
                        dataIndex: 'brand_name'
                    }, {
                        header: App.Language.General.type,
                        sortable: true,
                        dataIndex: 'asset_type_name'
                    }, {
                        header: App.Language.Asset.purchase_value,
                        sortable: true,
                        dataIndex: 'asset_cost'
                    }, {
                        header: App.Language.Asset.internal_number,
                        sortable: true,
                        dataIndex: 'asset_num_serie_intern'
                    }, {
                        header: App.Language.Core.location,
                        sortable: true,
                        dataIndex: 'asset_path',
                        align: 'center',
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + value + '"';
                            return value;
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }
        ];
        App.Asset.Principal.superclass.initComponent.call(this);
    }
});


App.Asset.PrincipalClase = Ext.extend(Ext.Panel, {
    title: App.Language.Asset.assets,
    id: 'App.Asset.Principal',
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        items: [{
            text: App.Language.General.add,
            id: 'ModuleAction_4001',
            hidden: (App.Security.Actions[4001] === undefined ? true : false),
            iconCls: 'add_icon',
            cls: 'permits',
            handler: function() {
                w = new App.Asset.addAssetWindow();
                w.form.saveButton.handler = function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/asset/asset/add',
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            success: function(fp, o) {
                                App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
                                Ext.FlashMessage.alert(o.result.msg);
                                b.ownerCt.ownerCt.ownerCt.close();
                            },
                            failure: function(fp, o) {
                                Ext.FlashMessage.alert(o.result.msg);
                            }
                        });
                    }
                }
                w.show();
            }
        }, {
            xtype: 'spacer',
            cls: 'permits',
            width: 5
        }, {
            text: App.Language.General.move,
            iconCls: 'mover_icon',
            cls: 'permits',
            id: 'ModuleAction_4003',
            hidden: (App.Security.Actions[4003] === undefined ? true : false),
            handler: function(b) {
                checkCount = Ext.getCmp('App.Asset.Principal').assetGrid.getSelectionModel().getCount();

                if (checkCount >= 1) {
                    b.menu.items.get(0).enable();
                    if (valoraux == 1) {
                        b.menu.items.get(1).enable();
                    } else {
                        b.menu.items.get(1).disable();
                    }
                } else {
                    if (valoraux == 1) {
                        b.menu.items.get(0).disable();
                        b.menu.items.get(1).enable();
                    } else {
                        b.menu.items.get(0).disable();
                        b.menu.items.get(1).disable();
                    }
                }
            },
            menu: [{
                text: App.Language.General.select,
                iconCls: 'edit_icon',
                handler: function() {
                    valoraux = 1;

                    grid = Ext.getCmp('App.Asset.Principal');
                    records = Ext.getCmp('App.Asset.Principal').assetGrid.getSelectionModel().getSelections();
                    aux = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.asset_id);
                    }
                    App.Asset.MovProxy(aux.join(','));
                }
            }, {
                text: App.Language.General.relocate,
                iconCls: 'paste_icon',
                handler: function() {

                    node_id = App.Interface.selectedNodeId;
                    App.Asset.PasteProxy(node_id, function() {
                        App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });

                    });
                }
            }]
        }, {
            xtype: 'spacer',
            cls: 'permits',
            width: 5
        }, {
            text: App.Language.General.bin,
            cls: 'permits',
            iconCls: 'bin_icon',
            hidden: (App.Security.Actions[4004] === undefined ? true : false),
            handler: function(b) {
                grid = Ext.getCmp('App.Asset.Grid');
                if (grid.getSelectionModel().getCount()) {
                    records = Ext.getCmp('App.Asset.Grid').getSelectionModel().getSelections();
                    aux = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.asset_id);
                    }
                    asset_id = (aux.join(','));

                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.this_insurance_send_to_the_trash_or_document,
                        function(b) {
                            if (b == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: App.Language.General.message_generating_file,
                                    url: 'index.php/asset/asset/enviarPapelera',
                                    timeout: 10000000000,
                                    params: {
                                        asset_id: asset_id
                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);
                                        Ext.getCmp('App.Asset.Grid').fireEvent('beforerender', Ext.getCmp('App.Asset.Grid'));
                                        Ext.getCmp('App.Asset.GridPapelera').fireEvent('beforerender', Ext.getCmp('App.Asset.GridPapelera'));
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
        }, {
            xtype: 'tbseparator',
            width: 5
        }, {
            text: App.Language.General.eexport,
            iconCls: 'export_icon',
            cls: 'permits',
            id: 'ModuleAction_4005',
            hidden: (App.Security.Actions[4005] === undefined ? true : false),
            handler: function() {
                w = new App.Asset.exportListWindow();
                w.show();
            }
        }, {
            xtype: 'tbseparator',
            cls: 'permits',
            width: 5
        }, {
            text: App.Language.Asset.export_plancheta,
            iconCls: 'export_icon',
            cls: 'permits',
            id: 'ModuleAction_4006',
            hidden: (App.Security.Actions[4006] === undefined ? true : false),
            handler: function() {
                document.location = asset_export_plancheta + App.Interface.selectedNodeId;
            }
        }, {
            text: App.Language.Asset.export_plancheta_by_level,
            iconCls: 'export_icon',
            cls: 'permits',
            id: 'ModuleAction_4009',
            hidden: (App.Security.Actions[4009] === undefined ? true : false),
            handler: function() {
                Ext.Ajax.request({
                    waitMsg: App.Language.General.message_generating_file,
                    url: 'index.php/asset/assetuchileplancheta/validarNivel',
                    timeout: 10000000000,
                    params: {
                        node_id: App.Interface.selectedNodeId
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);
                        if (response.success === "false") {
                            Ext.FlashMessage.alert(response.msg);
                        }

                        if (response.success === "true") {
                            document.location = asset_export_plancheta_nivel + App.Interface.selectedNodeId;
                        }
                    },
                    failure: function(response) {
                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                    }
                });
            }
        }, {
            xtype: 'tbseparator',
            cls: 'permits',
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
        }]
    },
    initComponent: function() {
        this.items = [{
                xtype: 'form',
                region: 'north',
                title: App.Language.General.searching,
                id: 'App.Plan.SearchForm',
                frame: true,
                labelWidth: 150,
                ref: 'form',
                cls: 'formCls',
                autoScroll: true,
                hidden: true,
                height: 240,
                margins: '5 5 0 5',
                padding: '0 0 0 5',
                border: true,
                fbar: [{
                    text: App.Language.General.search,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Asset.Store.baseParams.node_id;
                        App.Asset.Store.baseParams = form.getSubmitValues();
                        App.Asset.Store.setBaseParam('node_id', node_id);
                        App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
                    }
                }, {
                    text: App.Language.General.clean,
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        node_id = App.Asset.Store.baseParams.node_id;
                        form.reset();
                        App.Asset.Store.baseParams = {};
                        App.Asset.Store.setBaseParam('node_id', node_id);
                        App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
                    }
                }],
                items: [{
                    layout: 'column',
                    items: [{
                        columnWidth: .5,
                        layout: 'form',
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.load_folio,
                            anchor: '80%',
                            name: 'asset_load_folio',
                            checked: true
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.General.name,
                            anchor: '80%',
                            name: 'asset_name',
                            checked: true
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.General.brand,
                            anchor: '80%',
                            triggerAction: 'all',
                            store: App.Brand.Store,
                            hiddenName: 'brand_id',
                            triggerAction: 'all',
                            displayField: 'brand_name',
                            valueField: 'brand_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
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
                            fieldLabel: App.Language.Asset.asset_type,
                            anchor: '80%',
                            store: App.Asset.Type.Store,
                            hiddenName: 'asset_type_id',
                            triggerAction: 'all',
                            displayField: 'asset_type_name',
                            valueField: 'asset_type_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
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
                            fieldLabel: App.Language.Asset.internal_number,
                            anchor: '80%',
                            name: 'asset_num_serie_intern',
                            checked: true
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
                        items: [{
                            columnWidth: .1,
                            layout: 'form',
                            items: [{
                                xtype: 'label',
                                text: App.Language.General.select_date_range_to_search_active_load
                            }]
                        }, {
                            columnWidth: .4,
                            layout: 'column',
                            frame: true,
                            items: [{
                                    bodyStyle: 'margin-right: 50px;',
                                    layout: 'form',
                                    items: [{
                                        xtype: 'datefield',
                                        ref: '../start_date',
                                        fieldLabel: App.Language.General.start_date,
                                        name: 'start_date_lifetime',
                                        anchor: '95%',
                                        listeners: {
                                            'select': function(fd, date) {
                                                fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                            }
                                        }
                                    }]
                                },
                                {
                                    layout: 'form',
                                    items: [{
                                        xtype: 'datefield',
                                        ref: '../end_date',
                                        fieldLabel: App.Language.General.end_date,
                                        name: 'end_date_lifetime',
                                        anchor: '95%',
                                        listeners: {
                                            'select': function(fd, date) {
                                                fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                            }
                                        }
                                    }]
                                }
                            ]
                        }, {
                            xtype: 'spacer',
                            height: 5
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.General.state,
                            anchor: '100%',
                            store: App.Asset.Status.Store,
                            hiddenName: 'asset_status_id',
                            triggerAction: 'all',
                            displayField: 'asset_status_name',
                            valueField: 'asset_status_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
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
                            fieldLabel: App.Language.General.condition,
                            anchor: '100%',
                            store: App.Asset.Condition.Store,
                            hiddenName: 'asset_condition_id',
                            triggerAction: 'all',
                            displayField: 'asset_condition_name',
                            valueField: 'asset_condition_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            mode: 'remote',
                            minChars: 0,
                            listeners: {
                                'afterrender': function(cb) {
                                    cb.__value = cb.value;
                                    cb.setValue('');
                                    cb.getStore().load();
                                }
                            }
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.invoice_number,
                            anchor: '100%',
                            name: 'asset_num_factura',
                            checked: true
                        }, {
                            xtype: 'checkbox',
                            hideLabel: true,
                            boxLabel: App.Language.Asset.asset_log_low,
                            name: 'written_off',
                            inputValue: 1
                        }]
                    }]
                }]
            }, {
                xtype: 'grid',
                id: 'App.Asset.Grid',
                ref: 'assetGrid',
                margins: '5 5 5 5',
                region: 'center',
                height: 600,
                border: true,
                loadMask: true,
                listeners: {
                    'afterrender': function() {
                        App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
                    },
                    //                'beforerender': function(w)
                    //                 {
                    //                    App.Asset.Store.load();
                    //                 },
                    'rowdblclick': function(grid, rowIndex) {

                        if ((App.Security.Actions[4002] === undefined) || (App.Interface.permits == false)) {

                            w = new App.Asset.AssetdetailsAssetWindow();
                            App.Asset.selectedAssetId = grid.getStore().getAt(rowIndex).data.asset_id;

                            App.Asset.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);

                            App.Asset.Insurance.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            App.Asset.Insurance.Store.load();

                            App.Asset.Measurement.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            App.Asset.Measurement.Store.load();

                            App.Asset.OtrosDatos.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            App.Asset.OtrosDatos.Store.load
                            w.show();
                        } else {

                            w = new App.Asset.editAssetWindow();
                            if (grid.getStore().getAt(rowIndex).data.AssetLoad.asset_load_folio === undefined) {
                                w.setTitle(App.Language.Asset.active_editing + ' - ' + App.Language.Asset.load_folio + ' -> ' + 'No Existe');
                            } else {
                                w.setTitle(App.Language.Asset.active_editing + ' - ' + App.Language.Asset.load_folio + ' -> ' + grid.getStore().getAt(rowIndex).data.AssetLoad.asset_load_folio);
                            }
                            App.Asset.selectedAssetId = grid.getStore().getAt(rowIndex).data.asset_id;

                            w.uno.getForm().loadRecord(grid.getStore().getAt(rowIndex));

                            App.Asset.Insurance.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            App.Asset.Insurance.Store.load();

                            App.Asset.Measurement.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            App.Asset.Measurement.Store.load();

                            App.Asset.OtrosDatos.Store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            App.Asset.OtrosDatos.Store.load({
                                callback: function() {
                                    App.Asset.OtrosDatos.Store.each(function(record) {
                                        field = new Ext.form.TextField({
                                            xtype: 'textfield',
                                            width: 250,
                                            fieldLabel: record.data.label,
                                            value: record.data.value,
                                            name: record.data.asset_other_data_attribute_id
                                        });
                                        w.uno.tabpanel.otherdata.add(field);
                                        w.uno.tabpanel.otherdata.doLayout();
                                    });
                                }
                            });
                            w.show();
                        }
                    }
                },
                viewConfig: {
                    forceFit: true
                },
                store: App.Asset.Store,
                bbar: new Ext.PagingToolbar({
                    pageSize: App.GridLimitAsset,
                    store: App.Asset.Store,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "Sin resultados.",
                    listeners: {
                        'beforerender': function(w) {
                            App.Asset.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                        }

                    }
                }),
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        header: App.Language.Asset.load_folio,
                        sortable: true,
                        dataIndex: 'AssetLoad',
                        renderer: function(AssetLoad) {
                            return AssetLoad.asset_load_folio;
                        }
                    },
                    {
                        header: App.Language.General.name,
                        sortable: true,
                        dataIndex: 'asset_name'
                    }, {
                        header: App.Language.General.document,
                        sortable: true,
                        dataIndex: 'asset_document_count',
                        width: 45,

                        renderer: function(val, metadata, record) {
                            if (record.data.asset_document_count >= 1) {
                                return "<div style='background-image: url(style/default/icons/clip_icon.png); background-repeat: no-repeat; height: 16; width: 16; float: left; padding-left: 20; padding-top: 2'></div>";
                            }
                        }
                    }, {
                        header: App.Language.General.brand,
                        sortable: true,
                        dataIndex: 'brand_name'
                    }, {
                        header: App.Language.General.type,
                        sortable: true,
                        dataIndex: 'asset_type_name'
                    }, {
                        header: App.Language.Asset.purchase_value,
                        sortable: true,
                        dataIndex: 'asset_cost'
                    }, {
                        header: App.Language.Asset.internal_number,
                        sortable: true,
                        dataIndex: 'asset_num_serie_intern'
                    }, {
                        header: App.Language.Asset.invoice_number,
                        sortable: true,
                        dataIndex: 'asset_num_factura'
                    }, {
                        header: App.Language.Core.location,
                        sortable: true,
                        dataIndex: 'asset_path',
                        align: 'center',
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + value + '"';
                            return value;
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }],
            App.Asset.PrincipalClase.superclass.initComponent.call(this);
    }
});

App.Asset.Principal.listener = function(node) {
    if (node && node.id) {
        App.Asset.Store.setBaseParam('node_id', node.id);
    }
    App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
};

App.Asset.addAssetWindow = Ext.extend(Ext.Window, {
    title: App.Language.Asset.add_asset_title,
    width: (screen.width < 440) ? screen.width - 50 : 440,
    height: 460,
    layout: 'fit',
    padding: 1,
    modal: true,
    maximizable: true,
    resizable: false,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            labelWidth: 150,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                anchor: '100%',
                name: 'asset_name',
                allowBlank: false
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.serial_number,
                anchor: '100%',
                name: 'asset_num_serie'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.internal_number,
                anchor: '100%',
                name: 'asset_num_serie_intern'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.invoice_number,
                anchor: '100%',
                name: 'asset_num_factura'
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.brand,
                anchor: '100%',
                store: App.Brand.Store,
                hiddenName: 'brand_id',
                triggerAction: 'all',
                displayField: 'brand_name',
                valueField: 'brand_id',
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
                xtype: 'combo',
                fieldLabel: App.Language.Asset.asset_type,
                anchor: '100%',
                store: App.Asset.Type.Store,
                hiddenName: 'asset_type_id',
                triggerAction: 'all',
                displayField: 'asset_type_name',
                valueField: 'asset_type_id',
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
                xtype: 'combo',
                fieldLabel: App.Language.General.state,
                anchor: '100%',
                triggerAction: 'all',
                store: App.Asset.Status.Store,
                hiddenName: 'asset_status_id',
                displayField: 'asset_status_name',
                valueField: 'asset_status_id',
                editable: true,
                typeAhead: true,
                selectOnFocus: true,
                forceSelection: true,
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
                xtype: 'combo',
                fieldLabel: App.Language.General.condition,
                anchor: '100%',
                triggerAction: 'all',
                store: App.Asset.Condition.Store,
                hiddenName: 'asset_condition_id',
                displayField: 'asset_condition_name',
                valueField: 'asset_condition_id',
                editable: false,
                mode: 'remote',
                allowBlank: false,
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
                fieldLabel: App.Language.Asset.purchase_value,
                anchor: '100%',
                name: 'asset_cost'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Asset.current_cost,
                anchor: '100%',
                name: 'asset_current_cost'
            }, {
                xtype: 'datefield',
                ref: 'asset_purchase_date',
                fieldLabel: App.Language.Asset.purchase_date,
                anchor: '100%',
                name: 'asset_purchase_date'
            }, {
                xtype: 'numberfield',
                ref: 'asset_lifetime',
                fieldLabel: App.Language.Asset.lifetime,
                anchor: '100%',
                name: 'asset_lifetime'
            }, {
                xtype: 'textarea',
                anchor: '100%',
                fieldLabel: App.Language.General.description,
                name: 'asset_description'
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton'
            }]
        }];
        App.Asset.addAssetWindow.superclass.initComponent.call(this);
    }
});

App.Asset.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.eexport_list,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    height: 250,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                anchor: '100%',
                name: 'file_name',
                maskRe: /^[a-zA-Z0-9_]/,
                regex: /^[a-zA-Z0-9_]/,
                allowBlank: false
            }, {
                xtype: 'radiogroup',
                fieldLabel: App.Language.General.output_type,
                columns: 1,
                items: [{
                    boxLabel: 'Excel',
                    name: 'output_type',
                    inputValue: 'e',
                    height: 25,
                    checked: true
                }, {
                    boxLabel: 'PDF',
                    name: 'output_type',
                    inputValue: 'p',
                    height: 25
                }]
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
                            timeout: 100000000000,
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/asset/asset/exportList',
                            params: App.Asset.Store.baseParams,
                            success: function(form, response) {
                                document.location = 'index.php/app/download/' + response.result.file;
                                //                                document.location = 'temp/' + response.result.file;
                                b.ownerCt.ownerCt.ownerCt.hide();
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
        App.Asset.exportListWindow.superclass.initComponent.call(this);
    }
});

App.Asset.editAssetWindow = Ext.extend(Ext.Window, {
    width: 650,
    height: 570,
    layout: 'fit',
    border: true,
    padding: 5,
    maximizable: true,
    modal: true,
    resizable: false,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'uno',
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'tabpanel',
                activeTab: 0,
                height: 450,
                border: false,
                ref: 'tabpanel',
                defaults: {
                    layout: 'form',
                    defaultType: 'textfield',
                    hideMode: 'offsets'
                },
                items: [{
                        ref: 'detail',
                        title: App.Language.General.details,
                        padding: 5,
                        labelWidth: 150,
                        ttbar: {
                            xtype: 'toolbar',
                            items: [{
                                text: App.Language.Asset.save_changes,
                                iconCls: 'save_icon',
                                handler: function(b) {
                                    form = b.ownerCt.ownerCt.getForm();
                                    if (form.isValid()) {
                                        form.submit({
                                            clientValidation: true,
                                            url: 'index.php/asset/asset/update',
                                            params: {
                                                asset_id: App.Asset.selectedAssetId,
                                                node_id: App.Interface.selectedNodeId
                                            },
                                            waitMsg: App.Language.General.message_guarding_information,
                                            success: function(form, response) {
                                                App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
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
                        },
                        items: [{
                            xtype: 'textfield',
                            fieldLabel: App.Language.General.name,
                            anchor: '100%',
                            name: 'asset_name',
                            allowBlank: false
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.serial_number,
                            anchor: '100%',
                            name: 'asset_num_serie'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.internal_number,
                            anchor: '100%',
                            name: 'asset_num_serie_intern'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.invoice_number,
                            name: 'asset_num_factura',
                            anchor: '100%'
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.General.brand,
                            anchor: '100%',
                            store: App.Brand.Store,
                            triggerAction: 'all',
                            hiddenName: 'brand_id',
                            displayField: 'brand_name',
                            valueField: 'brand_id',
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
                            xtype: 'combo',
                            anchor: '100%',
                            store: App.Asset.Type.Store,
                            fieldLabel: App.Language.Asset.asset_type,
                            hiddenName: 'asset_type_id',
                            displayField: 'asset_type_name',
                            valueField: 'asset_type_id',
                            triggerAction: 'all',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            allowBlank: false,
                            disabled: false,
                            mode: 'remote',
                            minChars: 0
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.General.state,
                            anchor: '100%',
                            triggerAction: 'all',
                            store: App.Asset.Status.Store,
                            hiddenName: 'asset_status_id',
                            displayField: 'asset_status_name',
                            valueField: 'asset_status_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            mode: 'remote',
                            minChars: 0
                        }, {
                            xtype: 'combo',
                            fieldLabel: App.Language.General.condition,
                            anchor: '100%',
                            triggerAction: 'all',
                            store: App.Asset.Condition.Store,
                            hiddenName: 'asset_condition_id',
                            displayField: 'asset_condition_name',
                            valueField: 'asset_condition_id',
                            editable: true,
                            typeAhead: true,
                            selectOnFocus: true,
                            forceSelection: true,
                            mode: 'remote',
                            minChars: 0
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.purchase_value,
                            anchor: '100%',
                            name: 'asset_cost'
                        }, {
                            xtype: 'textfield',
                            fieldLabel: App.Language.Asset.current_cost,
                            anchor: '100%',
                            name: 'asset_current_cost'
                        }, {
                            xtype: 'datefield',
                            ref: 'asset_purchase_date',
                            fieldLabel: App.Language.Asset.purchase_date,
                            anchor: '100%',
                            name: 'asset_purchase_date'
                        }, {
                            xtype: 'numberfield',
                            ref: 'asset_lifetime',
                            fieldLabel: App.Language.Asset.lifetime,
                            anchor: '100%',
                            name: 'asset_lifetime'
                        }, {
                            xtype: 'datefield',
                            anchor: '100%',
                            fieldLabel: App.Language.Asset.date_last_inventory,
                            name: 'asset_last_inventory'
                        }, {
                            xtype: 'textarea',
                            anchor: '100%',
                            fieldLabel: App.Language.General.description,
                            name: 'asset_description'
                        }, {
                            xtype: 'checkbox',
                            fieldLabel: App.Language.General.unsubscribe,
                            name: 'asset_estate',
                            inputValue: 0
                        }]
                    },
                    new App.Asset.OtrosDatos.Panel(),
                    new App.Asset.Insurance.GridPanel(),
                    new App.Asset.Measurement.GridPanel(),
                    new App.Asset.Log.GridPanel(),
                    new App.Asset.Document.GridPanel()
                ]
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    Ext.getCmp('App.Asset.Grid').fireEvent('beforerender', Ext.getCmp('App.Asset.Grid'));
                    b.ownerCt.ownerCt.ownerCt.close();

                }
            }, {
                text: App.Language.Asset.save_changes,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.ownerCt.uno.getForm();
                    if (form.isValid()) {
                        form.submit({
                            clientValidation: true,
                            url: 'index.php/asset/asset/update',
                            params: {
                                asset_id: App.Asset.selectedAssetId,
                                node_id: App.Interface.selectedNodeId
                            },
                            waitMsg: App.Language.General.message_guarding_information,
                            success: function(form, action) {
                                Ext.FlashMessage.alert(action.result.msg);
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
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
                                        Ext.FlashMessage.alert(action.result.msg);
                                }
                            }
                        });
                    }
                }
            }]
        }];
        App.Asset.editAssetWindow.superclass.initComponent.call(this);
    }
});

/*--Detalles de Asset solo vista---*/
App.Asset.AssetdetailsAssetWindow = Ext.extend(Ext.Window, {
    title: App.Language.Asset.asset_details,
    width: 650,
    height: 520,
    layout: 'fit',
    border: true,
    maximizable: true,
    padding: 5,
    modal: true,
    resizable: false,
    listeners: {
        'beforerender': function(w) {
            Ext.getCmp('detalle').getForm().load({
                url: 'index.php/asset/asset/getOne',
                params: {
                    asset_id: App.Asset.selectedAssetId
                },
                success: function(fp, o) {
                    record = o.result;
                    Ext.getCmp('Brand').setValue(record.data.Brand.brand_name);
                    Ext.getCmp('AssetType').setValue(record.data.AssetType.asset_type_name);
                    Ext.getCmp('AssetStatus').setValue(record.data.AssetStatus.asset_status_name);
                    Ext.getCmp('AssetCondition').setValue(record.data.AssetCondition.asset_condition_name);
                }
            })
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            id: 'detalle',
            ref: 'uno',
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'tabpanel',
                activeTab: 0,
                height: 420,
                border: false,
                ref: 'tabpanel',
                defaults: {
                    layout: 'form',
                    defaultType: 'textfield',
                    hideMode: 'offsets'
                },
                items: [{
                    ref: 'detail',
                    title: App.Language.General.details,
                    padding: 5,
                    labelWidth: 150,
                    items: [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.name,
                        name: 'asset_name',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.serial_number,
                        name: 'asset_num_serie',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.internal_number,
                        name: 'asset_num_serie_intern',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.invoice_number,
                        name: 'asset_num_factura',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.brand,
                        id: 'Brand',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.asset_type,
                        id: 'AssetType',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.state,
                        id: 'AssetStatus',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.condition,
                        id: 'AssetCondition',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.purchase_value,
                        id: 'asset_cost',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.current_cost,
                        id: 'asset_current_cost',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.purchase_date,
                        id: 'asset_purchase_date',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.lifetime,
                        id: 'asset_lifetime',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.description,
                        id: 'asset_description',
                        anchor: '100%',
                        allowBlank: false
                    }]
                }, {
                    title: App.Language.General.other_data,
                    ref: 'otherdata',
                    autoScroll: true,
                    labelWidth: 150,
                    padding: 5,
                    plugins: [new Ext.ux.OOSubmit()]
                }, {
                    xtype: 'grid',
                    title: App.Language.Asset.assurances,
                    store: App.Asset.Insurance.Store,
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'render': function() {
                            this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            this.store.load();
                        }
                    },
                    initComponent: function() {
                        this.columns = [{
                            header: App.Language.General.provider,
                            sortable: true,
                            dataIndex: 'provider_name'
                        }, {
                            xtype: 'datecolumn',
                            sortable: true,
                            header: App.Language.General.start_date,
                            dataIndex: 'asset_insurance_begin_date',
                            format: App.General.DefaultDateFormat
                        }, {
                            xtype: 'datecolumn',
                            sortable: true,
                            header: App.Language.General.end_date,
                            dataIndex: 'asset_insurance_expiration_date',
                            format: App.General.DefaultDateFormat
                        }, {
                            header: App.Language.General.description,
                            sortable: true,
                            dataIndex: 'asset_insurance_description'
                        }, {
                            header: App.Language.General.state,
                            sortable: true,
                            dataIndex: 'asset_insurance_status_name'
                        }];
                        App.Asset.Insurance.GridPanel.superclass.initComponent.call(this);
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Asset.measurement,
                    store: App.Asset.Measurement.Store,
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'render': function() {
                            this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            this.store.load();
                        }
                    },
                    initComponent: function() {
                        this.columns = [{
                            header: App.Language.Asset.measurement,
                            sortable: true,
                            dataIndex: 'asset_measurement_cantity',
                            renderer: function(value, metaData, record) {
                                return value + ' ' + record.data.measure_unit_name;
                            }
                        }, {
                            xtype: 'datecolumn',
                            header: App.Language.General.date,
                            sortable: true,
                            dataIndex: 'asset_measurement_date',
                            format: App.General.DefaultDateFormat
                        }, {
                            header: App.Language.General.comment,
                            sortable: true,
                            dataIndex: 'asset_measurement_comments'
                        }];
                        App.Asset.Measurement.GridPanel.superclass.initComponent.call(this);
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Asset.tracking,
                    store: App.Asset.Log.Store,
                    region: 'center',
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'beforerender': function() {
                            this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            this.store.load();
                        }
                    },
                    initComponent: function() {
                        this.columns = [{
                            header: App.Language.General.action,
                            sortable: true,
                            dataIndex: 'asset_log_type_name'
                        }, {
                            xtype: 'datecolumn',
                            sortable: true,
                            header: App.Language.General.date_time,
                            dataIndex: 'asset_log_datetime',
                            format: App.General.DefaultDateTimeFormat
                        }, {
                            header: App.Language.General.details,
                            sortable: true,
                            dataIndex: 'asset_log_detail',
                            renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                                metadata.attr = 'ext:qtip="' + value + '"';
                                return value;
                            }
                        }, {
                            header: App.Language.General.user,
                            sortable: true,
                            dataIndex: 'User',
                            renderer: function(User) {
                                return User.user_name;
                            }
                        }];
                        App.Asset.Document.GridPanel.superclass.initComponent.call(this);
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.General.documents,
                    store: App.Asset.Document.Store,
                    loadMask: true,
                    anchor: '100%',
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'render': function() {
                            this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
                            this.store.load();
                        }
                    },
                    initComponent: function() {
                        this.columns = [{
                            header: App.Language.General.file_name,
                            dataIndex: 'asset_document_filename',
                            sortable: true,
                            renderer: function(val, metadata, record) {
                                return App.Security.Session.user_username;
                            }
                        }, {
                            header: App.Language.General.description,
                            sortable: true,
                            dataIndex: 'asset_document_description'
                        }, {
                            header: App.Language.General.uploaded_by,
                            sortable: true,
                            dataIndex: 'user_name'
                        }];
                        App.Asset.Document.GridPanel.superclass.initComponent.call(this);
                    }
                }]
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }]
        }];
        App.Asset.AssetdetailsAssetWindow.superclass.initComponent.call(this);
    }
});