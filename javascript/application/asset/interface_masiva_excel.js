asset_load_id = null;
folio = null;

App.Asset.CargaMasiva.window = Ext.extend(Ext.Window, {
    title: App.Language.Asset.excel_bulk_upload,
    width: 1000,
    height: 550,
    layout: 'fit',
    border: false,
    modal: true,
    resizable: false,
    initComponent: function () {
        this.items = [{
                layout: 'border',
                border: true,
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                            text: App.Language.Asset.top_load_excel,
                            iconCls: 'export_icon',
                            handler: function (b) {
                                wsgm = new App.Asset.subirActivosMasivoViewWindow();
                                wsgm.show();
                            }
                        }, {
                            xtype: 'tbseparator',
                            width: 10
                        }, {
                            text: 'Descargar Pancheta',
                            iconCls: 'export_icon',
                            handler: function (b) {

                                grid = Ext.getCmp('App.Asset.CargaMasiva');
                                if (grid.getSelectionModel().getCount()) {

                                    document.location = asset_export_listado_folio + grid.getSelectionModel().getSelected().data.asset_load_id;

                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                                }


                            }
                        }, {
                            xtype: 'tbseparator',
                            width: 10
                        }, {
                            text: 'Descargar Listado',
                            iconCls: 'export_icon',
                            handler: function (b) {
                                document.location = 'index.php/asset/assetloadcontroller/exportListado';
                            }
                        }, {
                            xtype: 'tbseparator',
                            width: 10
                        }, {
                            text: App.Language.Asset.remove_bulk_upload,
                            iconCls: 'delete_icon',
                            handler: function (b) {
                                grid = Ext.getCmp('App.Asset.CargaMasiva');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Asset.really_remove_the_load_and_all_associated_assets_at_is,
                                            function (b) {
                                                if (b == 'yes') {

                                                    grid.getSelectionModel().each(function (record) {
                                                        App.Asset.AssetLoad.Store.remove(record);
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
                            handler: function (b) {
                                if (b.ownerCt.ownerCt.formCarga.isVisible()) {
                                    b.ownerCt.ownerCt.formCarga.hide();
                                } else {
                                    b.ownerCt.ownerCt.formCarga.show();
                                }
                                b.ownerCt.ownerCt.doLayout();
                            }
                        }]
                },
                items: [{
                        xtype: 'form',
                        region: 'north',
                        title: App.Language.General.search,
                        id: 'App.FormCargaMasiva',
                        frame: true,
                        ref: 'formCarga',
                        hidden: true,
                        height: 165,
                        style: 'padding: 5 5 0 5',
                        fbar: [{
                                text: App.Language.General.search,
                                handler: function (b) {
                                    form = Ext.getCmp('App.FormCargaMasiva').getForm();
                                    App.Asset.AssetLoad.Store.baseParams = form.getSubmitValues();
                                    Ext.getCmp('App.Asset.CargaMasiva').fireEvent('beforerender', Ext.getCmp('App.Asset.CargaMasiva'));
                                }
                            }, {
                                text: App.Language.General.clean,
                                handler: function (b) {
                                    form = Ext.getCmp('App.FormCargaMasiva').getForm();
                                    form.reset();
                                    App.Asset.AssetLoad.Store.baseParams = form.getSubmitValues();
                                    Ext.getCmp('App.Asset.CargaMasiva').fireEvent('beforerender', Ext.getCmp('App.Asset.CargaMasiva'));

                                }
                            }],
                        items: [{
                                layout: 'column',
                                items: [{
                                        columnWidth: .5,
                                        labelWidth: 150,
                                        layout: 'form',
                                        items: [{
                                                xtype: 'spacer',
                                                height: 5
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: App.Language.Asset.folio_number,
                                                anchor: '90%',
                                                name: 'asset_load_folio'
                                            }, {
                                                xtype: 'spacer',
                                                height: 5
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: App.Language.General.user_magazine,
                                                hideLabel: (App.Security.Session.user_type == 'A' ? false : true),
                                                hidden: (App.Security.Session.user_type == 'A' ? false : true),
                                                anchor: '90%',
                                                name: 'user_name'
                                            }, {
                                                xtype: 'spacer',
                                                height: 5
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: App.Language.General.commentary,
                                                anchor: '90%',
                                                name: 'asset_load_comment'
                                            }]
                                    }, {
                                        columnWidth: .5,
                                        layout: 'form',
                                        items: [{
                                                columnWidth: .2,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'label',
                                                        text: App.Language.Asset.select_a_date_range_bulk_upload
                                                    }]
                                            }, {
                                                columnWidth: .4,
                                                layout: 'column',
                                                anchor: '95%',
                                                frame: true,
                                                items: [{
                                                        columnWidth: .5,

                                                        layout: 'form',
                                                        items: [{
                                                                xtype: 'datefield',
                                                                ref: '../start_date_cordi',
                                                                fieldLabel: App.Language.General.start_date,
                                                                name: 'start_date',
                                                                anchor: '95%',
                                                                listeners: {
                                                                    'select': function (fd, date) {
                                                                        fd.ownerCt.ownerCt.end_date_cordi.setMinValue(date);
                                                                    }
                                                                }
                                                            }]
                                                    }, {
                                                        columnWidth: .5,
                                                        layout: 'form',
                                                        items: [{
                                                                xtype: 'datefield',
                                                                ref: '../end_date_cordi',
                                                                fieldLabel: App.Language.General.end_date,
                                                                name: 'end_date',
                                                                anchor: '95%',
                                                                listeners: {
                                                                    'select': function (fd, date) {
                                                                        fd.ownerCt.ownerCt.start_date_cordi.setMaxValue(date);
                                                                    }
                                                                }
                                                            }]
                                                    }]
                                            }]
                                    }]
                            }]
                    }, {
                        xtype: 'grid',
                        id: 'App.Asset.CargaMasiva',
                        region: 'center',
                        style: 'padding: 5 5 5 5',
                        clicksToEdit: 1,
                        loadMask: true,
                        height: 555,
                        store: App.Asset.AssetLoad.Store,
                        maskDisabled: false,
                        listeners: {
                            'beforerender': function (w) {
                                App.Asset.AssetLoad.Store.load();
                            },
                            'rowdblclick': function (grid, rowIndex) {
                                asset_load_id = grid.getStore().getAt(rowIndex).data.asset_load_id;
                                App.Asset.AssetLoadId.Store.load({params: {asset_load_id: asset_load_id, start: 0, limit: App.GridLimit}});
                                wacml = new App.Asset.CargaMasiva.ListaWindow();
                                wacml.setTitle(App.Language.Asset.list_loaded_with_folio_cargo_assets + grid.getStore().getAt(rowIndex).data.asset_load_folio);
                                wacml.show();
                            }
                        },
                        viewConfig: {
                            forceFit: true
                        },
                        columns: [new Ext.grid.CheckboxSelectionModel(),
                            {
                                dataIndex: 'asset_load_folio',
                                header: App.Language.Asset.load_folio,
                                width: 100,
                                sortable: true
                            }, {
                                dataIndex: 'User',
                                header: App.Language.General.user_magazine,
                                width: 100,
                                aling: 'center',
                                sortable: true,
                                renderer: function (User) {
                                    return User.user_name;
                                }
                            }, {
                                xtype: 'datecolumn',
                                header: App.Language.Plan.upload_date,
                                sortable: true,
                                dataIndex: 'asset_load_date',
                                width: 100,
                                format: 'd-m-Y',
                                align: 'center'
                            }, {
                                dataIndex: 'asset_load_comment',
                                header: App.Language.General.commentary,
                                width: 100,
                                sortable: true
                            }
                        ],
                        sm: new Ext.grid.CheckboxSelectionModel({
                            singleSelect: true
                        })

                    }],
                fbar: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Asset.CargaMasiva.window.superclass.initComponent.call(this);
    }
});

App.Asset.CargaMasiva.ListaWindow = Ext.extend(Ext.Window, {
    width: 950,
    height: 560,
    layout: 'fit',
    border: false,
    modal: true,
    resizable: false,
    initComponent: function () {
        this.items = [{
                layout: 'border',
                border: true,
                items: [{
                        xtype: 'grid',
                        region: 'center',
                        style: 'padding: 5 5 5 5',
                        clicksToEdit: 1,
                        loadMask: true,
                        height: 555,
                        store: App.Asset.AssetLoadId.Store,
                        maskDisabled: false,
                        viewConfig: {
                            forceFit: true

                        },

                        bbar: new Ext.PagingToolbar({
                            pageSize: App.GridLimit,
                            store: App.Asset.AssetLoadId.Store,
                            displayInfo: true,
                            displayMsg: App.Language.General.showing,
                            emptyMsg: App.Language.General.no_results,
                            listeners: {
                                'beforerender': function (w) {
                                    App.Asset.AssetLoadId.Store.setBaseParam('asset_load_id', asset_load_id);
                                }

                            }
                        }),
                        columns: [{
                                header: App.Language.General.name,
                                sortable: true,
                                width: 80,
                                dataIndex: 'asset_name'
                            }, {
                                header: App.Language.Asset.internal_number,
                                sortable: true,
                                width: 50,
                                align: 'center',
                                dataIndex: 'asset_num_serie_intern'
                            }, {
                                header: App.Language.Core.location,
                                sortable: true,
                                width: 350,
                                dataIndex: 'asset_path',
                                renderer: function (value, metadata, record, rowIndex, colIndex, store) {
                                    metadata.attr = 'ext:qtip="' + value + '"';
                                    return value;
                                }
                            }]

                    }],
                fbar: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Asset.CargaMasiva.ListaWindow.superclass.initComponent.call(this);
    }
});

App.Asset.subirActivosMasivoViewWindow = Ext.extend(Ext.Window, {
    title: App.Language.Asset.upload_excel_asset,
    width: 550,
    id: 'App.cargaActivos.addFileWindow',
    height: 260,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                fileUpload: true,
                labelWidth: 150,
                padding: 5,
                listeners: {
                    'beforerender': function (w) {
                        Ext.Ajax.request({
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/asset/assetloadcontroller/getFootSignature',

                            success: function (response) {
                                response = Ext.decode(response.responseText);

                                Ext.getCmp('asset_load_foot_signature1').setValue(response.results.asset_load_foot_signature1);
                                Ext.getCmp('asset_load_foot_signature2').setValue(response.results.asset_load_foot_signature2);
                                Ext.getCmp('asset_load_foot_signature3').setValue(response.results.asset_load_foot_signature3);
                            },
                            failure: function (response) {
                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                            }
                        });
                    }
                },
                items: [{
                        xtype: 'fileuploadfield',
                        emptyText: App.Language.Document.select_a_excel,
                        fieldLabel: "Documento",
                        anchor: '100%',
                        allowBlank: false,
                        fileUpload: true,
                        name: 'documentoExcel',
                        buttonText: '',
                        buttonCfg: {
                            iconCls: 'upload_icon'
                        }
                    }, {
                        xtype: 'spacer',
                        height: 3
                    }, {
                        xtype: 'textfield',
                        fieldLabel: 'Comentario',
                        anchor: '90%',
                        name: 'asset_load_comment'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: 'Jefe de Servicio',
                        anchor: '90%',
                        id: 'asset_load_foot_signature1',
                        name: 'asset_load_foot_signature1'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: 'Conservador de Inventario',
                        anchor: '90%',
                        id: 'asset_load_foot_signature2',
                        name: 'asset_load_foot_signature2'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: 'Encargado de Activo Fijo',
                        anchor: '90%',
                        id: 'asset_load_foot_signature3',
                        name: 'asset_load_foot_signature3'
                    }, {
                        xtype: 'spacer',
                        height: 10
                    }, {
                        xtype: 'button',
                        text: 'Descarga Formato Excel',
                        iconCls: 'add_icon',
                        handler: function (b) {
                            document.location = 'index.php/asset/assetload/exportarFormato';
                        }
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }, {
                        text: App.Language.Asset.upload_file,
                        ref: '../saveButton',
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    url: 'index.php/asset/assetload/addAssetMasivo',
                                    waitTitle: App.Language.General.message_please_wait,
                                    waitMsg: App.Language.General.lloading,
                                    success: function (fp, o) {
                                        App.Asset.AssetLoad.Store.load();
                                        b.ownerCt.ownerCt.ownerCt.close();
                                        wsgmvd = new App.Asset.subirActivosMasivoViewDescargaWindow();
                                        wsgmvd.show();
                                        asset_load_id = o.result.asset_load_id;
                                        folio = o.result.asset_load_folio;
                                        Ext.getCmp('App.Asset.Folio').setValue(folio);
                                    },
                                    failure: function (fp, o) {
                                        alert('Error:\n' + o.result.msg);
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Asset.subirActivosMasivoViewWindow.superclass.initComponent.call(this);
    }
});

App.Asset.subirActivosMasivoViewDescargaWindow = Ext.extend(Ext.Window, {
    title: 'Detalle Carga Masiva',
    width: 400,
    height: 160,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                fileUpload: true,
                labelWidth: 150,
                padding: 5,
                items: [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Asset.load_folio,
                        id: 'App.Asset.Folio',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'spacer',
                        height: 10
                    }, {
                        xtype: 'button',
                        text: 'Descargar Pancheta',
                        iconCls: 'add_icon',
                        handler: function (b) {

                            document.location = asset_export_listado_folio + asset_load_id;
                        }
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Asset.subirActivosMasivoViewDescargaWindow.superclass.initComponent.call(this);
    }
});