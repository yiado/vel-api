/* global Ext, App */

App.Maintainers.addToModuleMenu('infra', {
    xtype: 'button',
    text: App.Language.Infrastructure.infrastructure,
    iconCls: 'infrastructure_icon_32',
    scale: 'large',
    module: 'Infrastructure',
    iconAlign: 'top'
});
App.Maintainers.Infrastructure.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    title: App.Language.Infrastructure.infrastructure,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            title: App.Language.General.category,
            store: App.NodeTypeCategory.Store,
            height: 900,
            laodMask: true,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.InfraCateOpenEditMode(record);
                },
                'beforerender': function() {
                    App.NodeTypeCategory.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.addInfraCategoryWindow();
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
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {

                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.elimination_confirmation_message, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.NodeTypeCategory.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'node_type_category_name',
                    header: App.Language.General.category,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel()
        }, {
            xtype: 'grid',
            title: App.Language.Infrastructure.node_type,
            store: App.NodeType.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.InfraestructureTypeNodeOpenEditMode(record);
                },
                'beforerender': function() {
                    App.NodeType.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        App.Maintainers.UrlSubmitFormNodeType = App.NodeType.Store.proxy.api.create.url;
                        w = new App.Maintainers.addTypeNodoWindow();
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
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.NodeType.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'node_type_name',
                    header: App.Language.Infrastructure.node_type,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'node_type_category_name',
                    header: App.Language.General.category,
                    sortable: true,
                    width: 100
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'node_type_state',
                    header: App.Language.General.node_type_location,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel()
        }, {
            xtype: 'tabpanel',
            border: false,
            title: App.Language.Infrastructure.structural_data,
            activeTab: 0,
            items: [{
                xtype: 'form',
                title: App.Language.Infrastructure.configure_tab,
                width: 900,
                id: 'App.Infrastructure.TypeNodeDataEstructural.Form',
                border: false,
                bodyStyle: 'padding:10px;',
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.replicating_settings,
                        iconCls: 'add_icon',
                        handler: function(b) {
                            form = Ext.getCmp('App.Infrastructure.TypeNodeDataEstructural.Form').getForm();
                            Ext.Ajax.request({
                                url: 'index.php/infra/infrainfoconfig/getExiste',
                                params: {
                                    node_type_id: form.getValues().node_type_id
                                },
                                success: function(response) {
                                    response = Ext.decode(response.responseText);
                                    if (response.success != false) {
                                        wmamw = new App.Maintainers.AssociateMultiDatosEstructuralesWindow();
                                        wmamw.show();
                                    } else {
                                        Ext.Msg.alert(App.Language.Core.notification, response.msg);
                                    }
                                }
                            });
                        }
                    }]
                },
                items: [{
                        xtype: 'combo',
                        width: 200,
                        id: 'App.Language.Infrastructure.node_type',
                        triggerAction: 'all',
                        fieldLabel: App.Language.Infrastructure.node_type,
                        hiddenName: 'node_type_id',
                        store: App.NodeType.Store,
                        displayField: 'node_type_name',
                        valueField: 'node_type_id',
                        mode: 'remote',
                        editable: true,
                        selecOnFocus: true,
                        typeAhead: true,
                        selectOnFocus: true,
                        minChars: 0,
                        listeners: {
                            'select': function(cb, record) {
                                App.InfraStructure.InfoConfigNuevo.Store.setBaseParam('node_type_id', record.data.node_type_id);
                                App.InfraStructure.InfoConfigNuevo.Store.load();
                                App.InfraStructure.InfoConfig.Store.setBaseParam('node_type_id', record.data.node_type_id);
                                App.InfraStructure.InfoConfig.Store.load();
                            }
                        }
                    }, {
                        xtype: 'itemselector',
                        name: 'itemselector',
                        fieldLabel: App.Language.Infrastructure.attributes,
                        imagePath: 'javascript/extjs/ux/images/',
                        multiselects: [{
                                width: 350,
                                height: 300,
                                store: App.InfraStructure.InfoConfig.Store,
                                displayField: 'label',
                                valueField: 'field'
                            }, {
                                width: 350,
                                height: 300,
                                store: App.InfraStructure.InfoConfigNuevo.Store,
                                displayField: 'label',
                                valueField: 'field',
                                listeners: {
                                    'change': function(obj, v) {

                                        Ext.MessageBox.confirm(App.Language.General.confirmation, '¿Agregar o Quitar de Ficha Resumen?', function(b) {
                                            if (b == 'yes') {
                                                Ext.Ajax.request({
                                                    waitMsg: App.Language.General.message_generating_file,
                                                    url: 'index.php/infra/infrainfo/updateSumary',
                                                    params: {
                                                        node_type_id: Ext.getCmp('App.Language.Infrastructure.node_type').getValue(),
                                                        infra_attribute: v

                                                    },
                                                    success: function(response) {
                                                        App.InfraStructure.InfoConfigNuevo.Store.setBaseParam('node_type_id', Ext.getCmp('App.Language.Infrastructure.node_type').getValue());
                                                        App.InfraStructure.InfoConfigNuevo.Store.load();
                                                        App.InfraStructure.InfoConfig.Store.setBaseParam('node_type_id', Ext.getCmp('App.Language.Infrastructure.node_type').getValue());
                                                        App.InfraStructure.InfoConfig.Store.load();
                                                        //                                                                        response = Ext.decode(response.responseText);
                                                        //
                                                        //                                                                        Ext.FlashMessage.alert(response.msg);
                                                    },
                                                    failure: function(response) {
                                                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                                    }
                                                });
                                            }
                                        });
                                    }

                                }

                            }

                        ]
                    },
                    {
                        xtype: 'button',
                        text: App.Language.General.save,
                        bodyStyle: 'padding:5px 5px 0',
                        width: 80,
                        handler: function(b) {
                            form = b.ownerCt.getForm();
                            form.submit({
                                waitTitle: App.Language.General.message_please_wait,
                                waitMsg: App.Language.General.message_guarding_information,
                                url: 'index.php/infra/infrainfoconfig/add',
                                success: function(fp, o) {
                                    Ext.FlashMessage.alert(o.result.msg);
                                },
                                failure: function(fp, o) {
                                    alert('Error:\n' + o.result.msg);
                                }
                            });
                        }
                    } // fin boton
                ]
            }, {
                title: App.Language.Infrastructure.set_merge_fields,
                layout: 'column',
                border: false,
                ref: 'tabCamposCombinados',
                bodyStyle: 'padding:10px 10px 0',
                items: [{
                    columnWidth: .7,
                    layout: 'form',
                    border: false,
                    items: [{
                        xtype: 'combo',
                        name: 'combo_1',
                        id: 'combo_1',
                        triggerAction: 'all',
                        fieldLabel: App.Language.Infrastructure.infra_info_option_id_1,
                        anchor: '100%',
                        hiddenName: 'infra_info_option_id_1',
                        store: App.InfraStructure.InfoOptionCombosAnidados1.Store,
                        displayField: 'infra_info_option_name',
                        valueField: 'infra_info_option_id',
                        mode: 'remote',
                        border: false,
                        minChars: 0,
                        editable: false,
                        listeners: {
                            'select': function(cb, record) {
                                Ext.getCmp('combo_2').clearValue();
                                Ext.getCmp('combo_3').clearValue();
                                Ext.getCmp('combo_4').clearValue();
                                Ext.getCmp('combo_2').enable();
                                Ext.getCmp('btn_combo_2').enable();
                                Ext.getCmp('combo_3').disable();
                                Ext.getCmp('btn_combo_3').disable();
                                Ext.getCmp('combo_4').disable();
                                Ext.getCmp('btn_combo_4').disable();
                                App.InfraStructure.InfoOptionCombosAnidados2.Store.setBaseParam('infra_info_option_parent_id', record.data.infra_info_option_id);
                                App.InfraStructure.InfoOptionCombosAnidados2.Store.load();
                            }
                        }
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.Infrastructure.infra_info_option_id_2,
                        typeAhead: true,
                        id: 'combo_2',
                        triggerAction: 'all',
                        name: 'combo_2',
                        anchor: '100%',
                        disabled: true,
                        border: false,
                        hiddenName: 'infra_info_option_id_2',
                        store: App.InfraStructure.InfoOptionCombosAnidados2.Store,
                        displayField: 'infra_info_option_name',
                        valueField: 'infra_info_option_id',
                        mode: 'remote',
                        minChars: 0,
                        editable: false,
                        listeners: {
                            'select': function(cb, record) {
                                Ext.getCmp('combo_3').clearValue();
                                Ext.getCmp('combo_4').clearValue();
                                Ext.getCmp('combo_3').enable();
                                Ext.getCmp('btn_combo_3').enable();
                                Ext.getCmp('combo_4').disable();
                                Ext.getCmp('btn_combo_4').disable();
                                App.InfraStructure.InfoOptionCombosAnidados3.Store.setBaseParam('infra_info_option_parent_id', record.data.infra_info_option_id);
                                App.InfraStructure.InfoOptionCombosAnidados3.Store.load();
                            }
                        }
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.Infrastructure.infra_info_option_id_3,
                        id: 'combo_3',
                        triggerAction: 'all',
                        name: 'combo_3',
                        disabled: true,
                        anchor: '100%',
                        hiddenName: 'infra_info_option_id_3',
                        store: App.InfraStructure.InfoOptionCombosAnidados3.Store,
                        displayField: 'infra_info_option_name',
                        valueField: 'infra_info_option_id',
                        mode: 'remote',
                        minChars: 0,
                        editable: false,
                        listeners: {
                            'select': function(cb, record) {
                                Ext.getCmp('combo_4').clearValue();
                                Ext.getCmp('combo_4').enable();
                                Ext.getCmp('btn_combo_4').enable();
                                App.InfraStructure.InfoOptionCombosAnidados4.Store.setBaseParam('infra_info_option_parent_id', record.data.infra_info_option_id);
                                App.InfraStructure.InfoOptionCombosAnidados4.Store.load();
                            }
                        }
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.Infrastructure.infra_info_option_id_4,
                        id: 'combo_4',
                        triggerAction: 'all',
                        name: 'combo_4',
                        disabled: true,
                        anchor: '100%',
                        hiddenName: 'infra_info_option_id_4',
                        store: App.InfraStructure.InfoOptionCombosAnidados4.Store,
                        displayField: 'infra_info_option_name',
                        valueField: 'infra_info_option_id',
                        mode: 'remote',
                        minChars: 0,
                        editable: false,
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
                    }]
                }, {
                    columnWidth: .3,
                    layout: 'form',
                    border: false,
                    padding: 1,
                    items: [{
                        xtype: 'button',
                        id: 'btn_combo_1',
                        border: false,
                        anchor: '10%',
                        height: 27,
                        iconCls: 'edit_icon',
                        handler: function(b) {
                            w = new App.Maintainers.InfraOpcionesComboAnidadoWindow();
                            w.infra_info_combo_parent.value = null;
                            w.infra_info_combo_options.value = 'combo_1';
                            w.show();
                        }
                    }, {
                        xtype: 'button',
                        border: false,
                        id: 'btn_combo_2',
                        anchor: '10%',
                        height: 27,
                        iconCls: 'edit_icon',
                        disabled: true,
                        handler: function(b) {
                            w = new App.Maintainers.InfraOpcionesComboAnidadoWindow();
                            w.infra_info_combo_parent.value = 'combo_1';
                            w.infra_info_combo_options.value = 'combo_2';
                            w.show();
                        }
                    }, {
                        xtype: 'button',
                        disabled: true,
                        border: false,
                        anchor: '10%',
                        height: 27,
                        id: 'btn_combo_3',
                        iconCls: 'edit_icon',
                        handler: function(b) {
                            w = new App.Maintainers.InfraOpcionesComboAnidadoWindow();
                            w.infra_info_combo_parent.value = 'combo_2';
                            w.infra_info_combo_options.value = 'combo_3';
                            w.show();
                        }
                    }, {
                        xtype: 'button',
                        disabled: true,
                        border: false,
                        height: 27,
                        anchor: '10%',
                        id: 'btn_combo_4',
                        iconCls: 'edit_icon',
                        handler: function(b) {
                            w = new App.Maintainers.InfraOpcionesComboAnidadoWindow();
                            w.infra_info_combo_parent.value = 'combo_3';
                            w.infra_info_combo_options.value = 'combo_4';
                            w.show();
                        }
                    }]
                }]
            }]
        }, {
            xtype: 'grid',
            title: App.Language.Infrastructure.dynamic_data,
            store: App.InfraStructure.DatosDinamicos.Store,
            stripeRows: true,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.InfraestructureDynamiDataOpenEditMode(record);
                },
                'beforerender': function() {
                    App.InfraStructure.DatosDinamicos.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.addDynamiDataWindow();
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
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraStructure.DatosDinamicos.Store.remove(record);
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
                    xtype: 'button',
                    text: App.Language.Core.groups,
                    iconCls: 'people_icon_16',
                    handler: function(b) {
                        wap = new App.Maintainers.GruposWindow();
                        wap.show();
                    }
                }]
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'infra_other_data_attribute_name',
                    header: App.Language.Infrastructure.tag_name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'InfraGrupo',
                    header: App.Language.Core.group,
                    sortable: true,
                    width: 100,
                    renderer: function(InfraGrupo) {
                        return InfraGrupo.infra_grupo_nombre;
                    }
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'infra_other_data_attribute_type_text',
                    header: App.Language.Infrastructure.field_type,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel()
        }, {
            xtype: 'panel',
            title: App.Language.Infrastructure.dynamic_data_associate,
            layout: 'fit',
            border: false,
            padding: 5,
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.replicating_settings,
                    iconCls: 'add_icon',
                    handler: function(b) {
                        form = Ext.getCmp('App.Infrastructure.TypeNode.Form').getForm();
                        Ext.Ajax.request({
                            url: 'index.php/infra/infraotherdataattributenodetype/getExiste',
                            params: {
                                node_type_id: form.getValues().node_type_id
                            },
                            success: function(response) {
                                response = Ext.decode(response.responseText);
                                if (response.success != false) {
                                    wmamw = new App.Maintainers.AssociateMultiWindow();
                                    wmamw.show();
                                } else {
                                    Ext.Msg.alert(App.Language.Core.notification, response.msg);
                                    //                                    Ext.FlashMessage.alert(response.msg);
                                }
                            }
                        });
                    }
                }]
            },
            items: [{
                xtype: 'form',
                labelWidth: 150,
                id: 'App.Infrastructure.TypeNode.Form',
                region: 'north',
                border: false,
                margins: '5 5 5 5',
                plugins: [new Ext.ux.OOSubmit()],
                items: [{
                    xtype: 'combo',
                    width: 200,
                    triggerAction: 'all',
                    id: 'App.InfrastructureOtherData.node_type',
                    fieldLabel: App.Language.Infrastructure.node_type,
                    hiddenName: 'node_type_id',
                    store: App.NodeType.Store,
                    displayField: 'node_type_name',
                    valueField: 'node_type_id',
                    mode: 'remote',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    minChars: 0,
                    listeners: {
                        'select': function(cb, record) {
                            App.InfraStructure.DatosDinamicosDisponibles.Store.setBaseParam('node_type_id', record.data.node_type_id);
                            App.InfraStructure.DatosDinamicosDisponibles.Store.load();
                            App.InfraStructure.DatosDinamicosAsociados.Store.setBaseParam('infra_other_data_attribute_node_type_id', record.data.node_type_id);
                            App.InfraStructure.DatosDinamicosAsociados.Store.load();
                        }
                    }
                }, {
                    xtype: 'itemselector',
                    name: 'itemselector',
                    fieldLabel: App.Language.Infrastructure.attributes,
                    imagePath: 'javascript/extjs/ux/images/',
                    multiselects: [{
                        width: 350,
                        height: 300,
                        store: App.InfraStructure.DatosDinamicosDisponibles.Store,
                        displayField: 'infra_other_data_attribute_name',
                        valueField: 'infra_other_data_attribute_id'
                    }, {
                        width: 350,
                        height: 300,
                        store: App.InfraStructure.DatosDinamicosAsociados.Store,
                        displayField: 'infra_other_data_attribute_name',
                        valueField: 'infra_other_data_attribute_id',
                        listeners: {
                            'change': function(obj, v) {

                                Ext.MessageBox.confirm(App.Language.General.confirmation, '¿Agregar o Quitar de Ficha Resumen?', function(b) {
                                    if (b == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: App.Language.General.message_generating_file,
                                            url: 'index.php/infra/infraotherdataattributenodetype/updateSumary',
                                            params: {
                                                node_type_id: Ext.getCmp('App.InfrastructureOtherData.node_type').getValue(),
                                                infra_other_data_attribute_id: v

                                            },
                                            success: function(response) {
                                                App.InfraStructure.DatosDinamicosDisponibles.Store.setBaseParam('node_type_id', Ext.getCmp('App.InfrastructureOtherData.node_type').getValue());
                                                App.InfraStructure.DatosDinamicosDisponibles.Store.load();
                                                App.InfraStructure.DatosDinamicosAsociados.Store.setBaseParam('infra_other_data_attribute_node_type_id', Ext.getCmp('App.InfrastructureOtherData.node_type').getValue());
                                                App.InfraStructure.DatosDinamicosAsociados.Store.load();
                                                //                                                                        response = Ext.decode(response.responseText);
                                                //
                                                //                                                                        Ext.FlashMessage.alert(response.msg);
                                            },
                                            failure: function(response) {
                                                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                            }
                                        });
                                    }
                                });
                            }

                        }
                    }]
                }, {
                    xtype: 'button',
                    text: App.Language.General.save,
                    bodyStyle: 'padding:5px 5px 0',
                    width: 80,
                    handler: function(b) {
                        form = b.ownerCt.getForm();
                        form.submit({
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.General.message_guarding_information,
                            url: 'index.php/infra/infraotherdataattributenodetype/add',
                            success: function(fp, o) {
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }]
            }]
        }];
        App.Maintainers.Infrastructure.Principal.superclass.initComponent.call(this);
    }
});
App.Maintainers.GruposWindow = Ext.extend(Ext.Window, {
    title: App.Language.Core.config_groups,
    modal: true,
    border: true,
    loadMask: true,
    width: 500,
    height: 450,
    layout: 'fit',
    padding: 2,
    viewConfig: {
        forceFit: true
    },
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.Grupos',
                store: App.InfraStructure.Grupos.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.InfraStructure.GruposById.Store.setBaseParam('infra_grupo_id', record.data.infra_grupo_id);
                        App.InfraStructure.GruposById.Store.load();
                        waidg = new App.InfraStructure.DetalleGrupoWindow();
                        waidg.show();
                    },
                    'beforerender': function() {
                        App.InfraStructure.Grupos.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        dataIndex: 'infra_grupo_nombre',
                        header: App.Language.General.group_name
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
                            wamagw = new App.Maintainers.AddGruposWindow();
                            wamagw.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            let grid = Ext.getCmp('App.Maintainers.Grupos');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.delete_is_really_sure_in_this_configuration_the_state, function(b) {
                                    if (b === 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            let infra_grupo_id = grid.getSelectionModel().getSelected().id;
                                            /**
                                             * infra_grupo_id 4 = Datos Generales
                                             */
                                            if (infra_grupo_id !== '4') {
                                                App.InfraStructure.Grupos.Store.setBaseParam('infra_grupo_id', infra_grupo_id);
                                                App.InfraStructure.Grupos.Store.remove(record);
                                            } else {
                                                Ext.FlashMessage.alert(`No es posible eliminar ${App.Language.Infrastructure.general_data}`);
                                            }
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        iconCls: 'down_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.Grupos');
                            if (grid.getSelectionModel().getCount()) {
                                infra_grupo_id = grid.getSelectionModel().getSelected().id;
                                App.InfraStructure.MovStateUp(infra_grupo_id);
                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                Ext.getCmp('App.Maintainers.Grupos').fireEvent('beforerender', Ext.getCmp('App.Maintainers.Grupos'));
                            } else {
                                Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_a_state_to_move_up);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        iconCls: 'up_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.Grupos');
                            if (grid.getSelectionModel().getCount()) {
                                infra_grupo_id = grid.getSelectionModel().getSelected().id;
                                App.InfraStructure.MovStateDown(infra_grupo_id);
                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                Ext.getCmp('App.Maintainers.Grupos').fireEvent('beforerender', Ext.getCmp('App.Maintainers.Grupos'));
                            } else {
                                Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_a_state_to_move_down);
                            }
                        }
                    }]
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Maintainers.GruposWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.AssociateMultiWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.selecting_nodes_to_replicate_types_configuration,
    modal: true,
    loadMask: true,
    width: 700,
    height: 500,
    layout: 'fit',
    padding: 2,
    viewConfig: {
        forceFit: true
    },
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Infrastructure.TypeNode.Grid',
                store: App.NodeType.Store,
                height: 420,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'beforerender': function() {
                        App.NodeType.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_name',
                        header: App.Language.Infrastructure.node_type,
                        sortable: true,
                        width: 900
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_category_name',
                        header: App.Language.General.category,
                        sortable: true,
                        width: 520
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.answer,
                        iconCls: 'add_icon',
                        handler: function() {
                            grid = Ext.getCmp('App.Infrastructure.TypeNode.Grid');
                            if (grid.getSelectionModel().getCount()) {
                                records = Ext.getCmp('App.Infrastructure.TypeNode.Grid').getSelectionModel().getSelections();
                                aux = new Array();
                                aux_mtn_work_order_id = new Array();
                                for (var i = 0; i < records.length; i++) {
                                    aux.push(records[i].data.node_type_id);
                                }
                                node_type_ids = aux.join(',');
                                Ext.Ajax.request({
                                    url: 'index.php/infra/infraotherdataattributenodetype/addConfiguration',
                                    params: {
                                        node_type_ids: node_type_ids,
                                        node_type_id: form.getValues().node_type_id,
                                        itemselector: form.getValues().itemselector
                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);
                                        Ext.getCmp('App.Infrastructure.TypeNode.Grid').fireEvent('beforerender', Ext.getCmp('App.Infrastructure.TypeNode.Grid'));
                                        if (response.success == false) {
                                            Ext.Msg.alert(App.Language.Core.notification, response.mso);
                                        } else {
                                            Ext.FlashMessage.alert(response.msg);
                                        }


                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.you_must_select_one_or_more_types_of_nodes_to_replicate);
                            }
                        }
                    }]
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Maintainers.AssociateMultiWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.AssociateMultiDatosEstructuralesWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.selecting_nodes_to_replicate_types_configuration,
    modal: true,
    loadMask: true,
    width: 700,
    height: 500,
    layout: 'fit',
    padding: 2,
    viewConfig: {
        forceFit: true
    },
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Infrastructure.TypeNodeDatosEstructurales.Grid',
                store: App.NodeType.Store,
                height: 420,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'beforerender': function() {
                        App.NodeType.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_name',
                        header: App.Language.Infrastructure.node_type,
                        sortable: true,
                        width: 900
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_category_name',
                        header: App.Language.General.category,
                        sortable: true,
                        width: 520
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.answer,
                        iconCls: 'add_icon',
                        handler: function() {
                            grid = Ext.getCmp('App.Infrastructure.TypeNodeDatosEstructurales.Grid');
                            if (grid.getSelectionModel().getCount()) {
                                records = Ext.getCmp('App.Infrastructure.TypeNodeDatosEstructurales.Grid').getSelectionModel().getSelections();
                                aux = new Array();
                                aux_mtn_work_order_id = new Array();
                                for (var i = 0; i < records.length; i++) {
                                    aux.push(records[i].data.node_type_id);
                                }
                                node_type_ids = aux.join(',');
                                Ext.Ajax.request({
                                    url: 'index.php/infra/infrainfoconfig/addConfiguration',
                                    params: {
                                        node_type_ids: node_type_ids,
                                        node_type_id: form.getValues().node_type_id,
                                        itemselector: form.getValues().itemselector
                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);
                                        Ext.getCmp('App.Infrastructure.TypeNodeDatosEstructurales.Grid').fireEvent('beforerender', Ext.getCmp('App.Infrastructure.TypeNodeDatosEstructurales.Grid'));
                                        if (response.success == false) {
                                            Ext.Msg.alert(App.Language.Core.notification, response.mso);
                                        } else {
                                            Ext.FlashMessage.alert(response.msg);
                                        }


                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.you_must_select_one_or_more_types_of_nodes_to_replicate);
                            }
                        }
                    }]
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Maintainers.AssociateMultiDatosEstructuralesWindow.superclass.initComponent.call(this);
    }
});
App.InfraStructure.DetalleGrupoWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.group_tags,
    width: 500,
    height: 380,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            store: App.InfraStructure.GruposById.Store,
            viewConfig: {
                forceFit: true
            },
            columns: [{
                header: App.Language.General.name,
                dataIndex: 'infra_other_data_attribute_name',
                sortable: true,
                editable: false
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.InfraStructure.DetalleGrupoWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.AddGruposWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_groups,
    resizable: false,
    modal: true,
    border: true,
    width: 400,
    height: 150,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            border: true,
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.group_name,
                id: 'App.NombreGrupo',
                name: 'infra_grupo_nombre',
                anchor: '100%'
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
                            url: 'index.php/infra/infragrupo/add',
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.InfraStructure.Grupos.Store.load();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                Ext.MessageBox.alert(App.Language.General.error, o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.AddGruposWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.addInfraCategoryWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.add_categories_infrastructure,
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
                xtype: 'textfield',
                fieldLabel: App.Language.Infrastructure.name_category,
                name: 'node_type_category_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
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
                            url: 'index.php/core/nodetypecategory/add',
                            success: function(fp, o) {
                                App.NodeTypeCategory.Store.load();
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
        App.Maintainers.addInfraCategoryWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.InfraCateOpenEditMode = function(record) {
    w = new App.Maintainers.addInfraCategoryWindow({
        title: App.Language.Infrastructure.infrastructure_category_edition
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.NodeTypeCategory.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addTypeNodoWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.add_type_node,
    resizable: false,
    modal: true,
    width: 380,
    height: 200,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            fileUpload: true,
            labelWidth: 150,
            padding: 5,
            items: [{
                    xtype: 'textfield',
                    fieldLabel: App.Language.Infrastructure.name_type_node,
                    name: 'node_type_name',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'combo',
                    anchor: '100%',
                    triggerAction: 'all',
                    fieldLabel: App.Language.General.category,
                    hiddenName: 'node_type_category_id',
                    store: App.NodeTypeCategory.Store,
                    displayField: 'node_type_category_name',
                    valueField: 'node_type_category_id',
                    mode: 'remote',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
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
                    xtype: 'fileuploadfield',
                    ref: 'icon',
                    emptyText: App.Language.General.select_image,
                    fieldLabel: 'Icono',
                    anchor: '100%',
                    allowBlank: false,
                    fileUpload: true,
                    name: 'icon',
                    buttonText: '',
                    buttonCfg: {
                        iconCls: 'upload_icon'
                    }
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: App.Language.General.node_type_location,
                    hiddenName: 'node_type_location',
                    boxLabel: 'Incluir Emplazamiento?',
                    name: 'node_type_location',
                    inputValue: '1'
                }
            ],
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
                    node_type_id = (b.ownerCt.ownerCt.record != undefined ? b.ownerCt.ownerCt.record.data.node_type_id : null);
                    if (form.isValid()) {
                        form.submit({
                            url: App.Maintainers.UrlSubmitFormNodeType,
                            params: {
                                node_type_id: node_type_id
                            },
                            success: function(fp, o) {
                                App.NodeType.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                Ext.FlashMessage.alert(o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.addTypeNodoWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.InfraestructureTypeNodeOpenEditMode = function(record) {
    App.Maintainers.UrlSubmitFormNodeType = App.NodeType.Store.proxy.api.update.url;
    w = new App.Maintainers.addTypeNodoWindow({
        title: App.Language.Infrastructure.edit_type_node
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.icon.allowBlank = true;
    w.form.record = record;
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addDynamiDataWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.add_dynamic_data,
    resizable: false,
    frame: true,
    modal: true,
    width: 400,
    height: 270,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'panel',
                anchor: '100%',
                title: App.Language.Infrastructure.message_node_type_invalid,
                ref: 'textoEditarMensaje',
                border: false,
                padding: 10,
                hidden: true
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Infrastructure.tag_name,
                name: 'infra_other_data_attribute_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.Core.group,
                anchor: '100%',
                triggerAction: 'all',
                store: App.InfraStructure.Grupos.Store,
                hiddenName: 'infra_grupo_id',
                displayField: 'infra_grupo_nombre',
                valueField: 'infra_grupo_id',
                editable: true,
                typeAhead: true,
                selectOnFocus: true,
                forceSelection: true,
                allowBlank: false,
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'combo',
                anchor: '100%',
                ref: 'comboTipos',
                fieldLabel: App.Language.Infrastructure.field_type,
                hiddenName: 'infra_other_data_attribute_type',
                store: new Ext.data.ArrayStore({
                    fields: [
                        'type_id',
                        'value'
                    ],
                    data: [
                        ['1', App.Language.General.text],
                        ['2', App.Language.General.number],
                        ['3', App.Language.General.decimal],
                        ['4', App.Language.General.date],
                        ['5', App.Language.General.selection],
                        ['6', App.Language.General.not_editable],
                        ['7', App.Language.General.checkbox]
                    ]
                }),
                displayField: 'value',
                valueField: 'type_id',
                editable: false,
                triggerAction: 'all',
                mode: 'local',
                minChars: 0,
                allowBlank: false
            }, {
                border: false,
                fbar: [{
                    xtype: 'button',
                    ref: '../../btnEditarSeleccion',
                    text: App.Language.Infrastructure.edit_selection_field_options,
                    hidden: true,
                    handler: function() {
                        w = new App.Maintainers.gridDatosDinamicosSeleccionWindow();
                        w.show();
                    }
                }]
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
                            url: 'index.php/infra/infraotherdataattribute/add',
                            success: function(fp, o) {
                                var parent = b.ownerCt.ownerCt.ownerCt;
                                App.InfraStructure.DatosDinamicos.Store.load();
                                //Llamar la ventana de las opciones del tipo selección
                                if (parent.form.comboTipos.getValue() == 5) {
                                    App.Maintainers.DatosDinamicosHiddenAttributeId = o.result.infra_other_data_attribute_id;
                                    w = new App.Maintainers.gridDatosDinamicosSeleccionWindow();
                                    w.show();
                                }
                                parent.close();
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
        App.Maintainers.addDynamiDataWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.InfraestructureDynamiDataOpenEditMode = function(record) {
    w = new App.Maintainers.addDynamiDataWindow({
        title: App.Language.Infrastructure.edit_dynamic_data
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    App.Maintainers.DatosDinamicosHiddenAttributeId = record.data.infra_other_data_attribute_id;
    if (record.data.infra_other_data_attribute_type == 5) {
        w.form.btnEditarSeleccion.show();
        w.form.comboTipos.setDisabled(true);
        w.form.textoEditarMensaje.show();
    } else {
        w.form.comboTipos.setDisabled(false);
        w.form.btnEditarSeleccion.hide();
        w.form.textoEditarMensaje.hide();
    }
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            if (record.data.infra_other_data_attribute_type == 5 && w.form.comboTipos.getValue() == 5) {
                w2 = new App.Maintainers.gridDatosDinamicosSeleccionWindow();
                w2.show();
            }
            w.close();
            App.InfraStructure.DatosDinamicos.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.gridDatosDinamicosSeleccionWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.selection_field_options,
    width: 450,
    height: 300,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.selModel = new Ext.grid.CheckboxSelectionModel({
            checkOnly: false
        });
        this.items = [{
            xtype: 'grid',
            ref: 'grid',
            store: App.InfraStructure.DatosDinamicosSeleccion.Store,
            loadMask: true,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.InfraDateDinaSelectEditMode(record);
                },
                'beforerender': function() {
                    App.InfraStructure.DatosDinamicosSeleccion.Store.setBaseParam('infra_other_data_attribute_id', App.Maintainers.DatosDinamicosHiddenAttributeId);
                    App.InfraStructure.DatosDinamicosSeleccion.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.addDatosDinamicosSeleccionWindow();
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
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraStructure.DatosDinamicosSeleccion.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: [this.selModel,
                {
                    header: App.Language.General.name,
                    dataIndex: 'infra_other_data_option_name'
                }
            ],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }]
        }];
        App.Maintainers.gridDatosDinamicosSeleccionWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.addDatosDinamicosSeleccionWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.add_selection_field_options,
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
                fieldLabel: App.Language.Infrastructure.option_name,
                name: 'infra_other_data_option_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/infra/infraotherdataoption/add',
                            params: {
                                infra_other_data_attribute_id: App.Maintainers.DatosDinamicosHiddenAttributeId
                            },
                            success: function(fp, o) {
                                App.InfraStructure.DatosDinamicosSeleccion.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }, {
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Maintainers.addDatosDinamicosSeleccionWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.InfraDateDinaSelectEditMode = function(record) {
    w = new App.Maintainers.addDatosDinamicosSeleccionWindow({
        title: App.Language.Infrastructure.edit_options_field
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.InfraStructure.DatosDinamicosSeleccion.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.InfraOpcionesComboAnidadoWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.selection_field_options,
    resizable: false,
    modal: true,
    width: 600,
    height: 400,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'hidden',
            ref: 'infra_info_combo_parent',
            name: 'infra_info_combo_parent_name'
        }, {
            xtype: 'hidden',
            ref: 'infra_info_combo_options',
            name: 'infra_info_combo_options_name'
        }, {
            xtype: 'grid',
            ref: 'grid',
            height: 400,
            laodMask: true,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.editOpcionComboAnidadoWindow(record);
                },
                'beforerender': function() {
                    var parent_name = this.ownerCt.infra_info_combo_parent.getValue();
                    var combo_options = this.ownerCt.infra_info_combo_options.getValue();
                    var infra_info_option_parent_id_value = (combo_options == 'combo_1' ? null : Ext.getCmp(parent_name).getValue());
                    this.ownerCt.grid.store = Ext.getCmp(combo_options).getStore();
                    this.ownerCt.grid.store.setBaseParam('infra_info_option_parent_id', infra_info_option_parent_id_value);
                    this.ownerCt.grid.store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        var parent_name = this.ownerCt.ownerCt.ownerCt.infra_info_combo_parent.getValue();
                        var combo_options = this.ownerCt.ownerCt.ownerCt.infra_info_combo_options.getValue();
                        w = new App.Maintainers.addOpcionComboAnidadoWindow();
                        w.form.infra_info_combo_parent.value = (combo_options == 'combo_1' ? null : Ext.getCmp(parent_name).getValue());
                        w.form.infra_info_combo_options.value = combo_options;
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
                        grid = b.ownerCt.ownerCt;
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Infrastructure.are_you_sure_you_want_to_delete, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        grid.getStore().remove(record);
                                    });
                                    grid.getStore().load();
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    header: App.Language.General.name,
                    dataIndex: 'infra_info_option_name',
                    sortable: true
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel()
        }];
        App.Maintainers.InfraOpcionesComboAnidadoWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.addOpcionComboAnidadoWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.add_option_to_select_field,
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
            padding: 5,
            items: [{
                xtype: 'hidden',
                ref: 'infra_info_combo_parent',
                name: 'infra_info_combo_parent_name'
            }, {
                xtype: 'hidden',
                ref: 'infra_info_combo_options',
                name: 'infra_info_combo_options_name'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'infra_info_option_name',
                anchor: '100%',
                allowBlank: false
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
                    infra_info_option_parent_id = b.ownerCt.ownerCt.infra_info_combo_parent.getValue();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/infra/infrainfooption/add',
                            params: {
                                infra_info_option_parent_id: infra_info_option_parent_id
                            },
                            success: function(fp, o) {
                                var combo_options = fp.findField('infra_info_combo_options_name').getValue();
                                store = Ext.getCmp(combo_options).getStore();
                                store.setBaseParam('infra_info_option_parent_id', o.result.infra_info_option_parent_id);
                                store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.addOpcionComboAnidadoWindow.superclass.initComponent.call(this);
    }
});
App.Maintainers.editOpcionComboAnidadoWindow = function(record) {
    w = new App.Maintainers.addOpcionComboAnidadoWindow({
        title: App.Language.Infrastructure.edit_the_option_selection_field
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
}