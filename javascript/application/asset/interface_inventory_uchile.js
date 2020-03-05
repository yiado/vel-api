App.Asset.Inventory.window = Ext.extend(Ext.Window, {
    title: App.Language.Asset.inventory,
    width: 1000,
    height: 580,
    modal: true,
    maximizable: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            id: 'App.Asset.inventoryID',
            ref: 'grid',
            store: App.Asset.Trasladados.Store,
            listeners: {
                'afterrender': function() {
                    App.Asset.Trasladados.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitTrasladados } });
                },
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    waaid = new App.Asset.Inventory.DetalleWindow();
                    waaid.form.getForm().loadRecord(record);
                    waaid.show();
                }
            },
            tbar: [{
                text: App.Language.Asset.load_collect,
                iconCls: 'upload_icon',
                id: 'App.CargarCollect',
                handler: function(b) {
                    wap = new App.Asset.Inventory.Cargar({
                        win: b.ownerCt.ownerCt.ownerCt
                    });
                    wap.show();
                }
            }, {
                xtype: 'tbseparator',
                width: 5
            }, {
                text: App.Language.General.generate_reports,
                id: 'App.GenerarReporte',
                iconCls: 'filter_icon',
                handler: function(b) {
                    wepo = new App.Asset.Inventory.uploadWindow({
                        win: b.ownerCt.ownerCt.ownerCt
                    });
                    wepo.show();
                }
            }, {
                xtype: 'tbseparator',
                width: 5
            }, {
                text: App.Language.Asset.finish_collect,
                id: 'App.FinalizaCollect',
                iconCls: 'clean_icon',
                handler: function(b) {
                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Asset.this_insurance_empty_tables, function(b) {
                        if (b == 'yes') {
                            var msg = Ext.MessageBox.wait(App.Language.General.please_wait, App.Language.Asset.processing_file);
                            Ext.Ajax.request({
                                waitTitle: App.Language.General.message_please_wait,
                                waitMsg: App.Language.General.message_generating_file,
                                url: 'index.php/asset/assetinventory/truncateTable',
                                success: function(response) {
                                    response = Ext.decode(response.responseText);
                                    App.Asset.Trasladados.Store.load();
                                    Ext.FlashMessage.alert(response.msg);
                                },
                                failure: function(response) {
                                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                },
                                callback: function() {
                                    msg.hide()
                                }

                            });
                        }
                    });
                }
            }, {
                xtype: 'tbseparator',
                width: 15
            }, {
                xtype: 'tbseparator',
                width: 15
            }, {
                text: App.Language.Asset.translate,
                iconCls: 'approve_icon',
                handler: function(b) {
                    grid = b.ownerCt.ownerCt;
                    if (grid.getSelectionModel().getCount()) {
                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Asset.sure_move_assets_to_the_site,
                            function(b) {
                                if (b == 'yes') {
                                    records = grid.getSelectionModel().getSelections();
                                    aux = new Array();
                                    for (var i = 0; i < records.length; i++) {
                                        aux.push(records[i].data.asset_inventory_auxiliar_proceso_id);
                                    }

                                    Ext.Ajax.request({
                                        url: 'index.php/asset/assetinventory/move',
                                        params: {
                                            asset_inventory_auxiliar_proceso_id: aux.join(',')
                                        },
                                        success: function() {
                                            App.Asset.Trasladados.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitTrasladados } });
                                            App.Asset.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitAsset } });
                                        }
                                    })

                                }
                            });
                    } else {
                        Ext.MessageBox.alert(App.Language.General.oops, App.Language.Asset.you_must_select_an_Item_to_translate);
                    }

                }
            }, {
                xtype: 'spacer',
                width: 5
            }, {
                text: App.Language.Asset.origin_return,
                iconCls: 'return_icon',
                handler: function(b) {
                    grid = b.ownerCt.ownerCt;
                    if (grid.getSelectionModel().getCount()) {
                        Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Asset.sure_to_return_assets_to_room_of_origin,
                            function(b) {
                                if (b == 'yes') {
                                    records = grid.getSelectionModel().getSelections();
                                    aux = new Array();
                                    for (var i = 0; i < records.length; i++) {
                                        aux.push(records[i].data.asset_inventory_auxiliar_proceso_id);
                                    }


                                    Ext.Ajax.request({
                                        url: 'index.php/asset/assetinventory/returnOrigen',
                                        params: {
                                            asset_inventory_auxiliar_proceso_id: aux.join(',')
                                        },
                                        success: function() {
                                            App.Asset.Trasladados.Store.load({ params: { node_id: App.Interface.selectedNodeId, start: 0, limit: App.GridLimitTrasladados } });
                                        }
                                    })
                                }
                            });
                    } else {
                        Ext.MessageBox.alert(App.Language.General.oops, App.Language.Asset.you_must_select_an_Item_to_translate);
                    }
                }
            }],
            viewConfig: {
                forceFit: true
            },

            bbar: new Ext.PagingToolbar({
                pageSize: App.GridLimitTrasladados,
                store: App.Asset.Trasladados.Store,
                displayInfo: true,
                displayMsg: App.Language.General.showing,
                emptyMsg: App.Language.General.no_results,
                listeners: {
                    'beforerender': function(w) {
                        App.Asset.Trasladados.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                    }

                }
            }),
            columns: [
                new Ext.grid.CheckboxSelectionModel(),
                {
                    header: App.Language.General.name,
                    dataIndex: 'asset_name',
                    width: 20,
                    sortable: true,
                    editable: false
                }, {
                    header: App.Language.Asset.internal_number,
                    dataIndex: 'asset_num_serie_intern',
                    width: 10,
                    sortable: true,
                    editable: false
                }, {
                    header: App.Language.Asset.original_location,
                    dataIndex: 'original_location',
                    width: 70,
                    sortable: true,
                    editable: false
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel(),
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Asset.Inventory.window.superclass.initComponent.call(this);
    }
});

App.Asset.Inventory.uploadWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.bulk_generate_reports,
    width: 550,
    height: 300,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            labelWidth: 120,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'datefield',
                fieldLabel: App.Language.Asset.date_inventory,
                name: 'asset_inventory_date',
                ref: 'asset_inventory_date_masiva',
                value: new Date(),
                anchor: '70%'
            }, {
                xtype: 'radiogroup',
                fieldLabel: App.Language.General.action,
                columns: 1,
                items: [{
                    boxLabel: App.Language.Asset.generate_report_inventory_control,
                    name: 'output_type',
                    inputValue: 'ct',
                    height: 25,
                    checked: true
                }, {
                    boxLabel: App.Language.Asset.generate_report_missing,
                    name: 'output_type',
                    inputValue: 'cf',
                    height: 25
                }, {
                    boxLabel: App.Language.Asset.generate_report_transferred,
                    name: 'output_type',
                    inputValue: 'ctt',
                    height: 25
                }, {
                    boxLabel: App.Language.Asset.generate_report_unregistered,
                    name: 'output_type',
                    inputValue: 'canr',
                    height: 25
                }, {
                    boxLabel: App.Language.Asset.generate_asset_report_without_changes,
                    name: 'output_type',
                    inputValue: 'cc',
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
                text: App.Language.General.download,
                handler: function(b) {
                    fp = b.ownerCt.ownerCt;
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            clientValidation: true,
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.Asset.processing_file,
                            url: 'index.php/asset/assetinventory/uploadCargaMasiva',
                            params: {
                                asset_inventory_date: fp.asset_inventory_date_masiva.getSubmitValue()
                            },
                            success: function(form, response) {
                                b.ownerCt.ownerCt.ownerCt.win.grid.getStore().load({
                                    callback: function() {
                                        document.location = 'index.php/app/download/' + response.result.file;
                                    }
                                });
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
        App.Asset.Inventory.uploadWindow.superclass.initComponent.call(this);
    }
});

App.Asset.Inventory.DetalleWindow = Ext.extend(Ext.Window, {
    title: App.Language.Asset.asset_details,
    width: 900,
    height: 370,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            labelWidth: 200,
            ref: 'form',
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.name_asset,
                name: 'asset_name',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.General.brand,
                name: 'brand_name',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.internal_number,
                name: 'asset_num_serie_intern',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.auge_code,
                name: 'codigo_auge',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.original_location,
                name: 'original_location',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.original_department,
                name: 'departamento_original',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.original_name_subrecinto,
                name: 'nombre_subrecinto_original',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.location_transfer,
                name: 'location_transfer',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.department_of_transportation,
                name: 'departamento_transfer',
                anchor: '100%'

            }, {
                xtype: 'displayfield',
                fieldLabel: App.Language.Asset.subrecinto_name_transfer,
                name: 'nombre_subrecinto_transfer',
                anchor: '100%'

            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Asset.Inventory.DetalleWindow.superclass.initComponent.call(this);
    }
});


App.Asset.Inventory.Cargar = Ext.extend(Ext.Window, {
    title: App.Language.Asset.load_collect,
    width: 400,
    height: 200,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            labelWidth: 120,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'fileuploadfield',
                emptyText: App.Language.General.select_document,
                fieldLabel: App.Language.Asset.archive,
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'inventory_file',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.Infrastructure.level_up,
                handler: function(b) {
                    var msg = Ext.MessageBox.wait(App.Language.General.please_wait, App.Language.Asset.processing_inventory);
                    fp = b.ownerCt.ownerCt;
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            clientValidation: true,
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.Asset.processing_file,
                            url: 'index.php/asset/assetinventory/upload',
                            params: {
                                output_type: "c"
                            },
                            success: function(form, response) {
                                b.ownerCt.ownerCt.ownerCt.win.grid.getStore().load({
                                    callback: function() {
                                        Ext.FlashMessage.alert(App.Language.Asset.i_selected_the_option_generate_reports);
                                    }
                                });
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
                            },
                            callback: function() {
                                msg.hide()
                            }
                        });
                    }
                }
            }]
        }];
        App.Asset.Inventory.uploadWindow.superclass.initComponent.call(this);
    }
});