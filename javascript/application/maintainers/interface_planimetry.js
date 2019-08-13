App.Maintainers.addToModuleMenu('plan', {
    xtype: 'button',
    text: App.Language.Plan.planimetry,
    iconCls: 'plane_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Planimetry'
});

App.Maintainers.Planimetry.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            title: App.Language.General.category,
            id: 'App.Maintainers.PlanCategoryGrid',
            store: App.Plan.Category.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Maintainers.Planimetry.OpenEditMode(record);
                },
                'beforerender': function() {
                    App.Plan.Category.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'plan_category_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'plan_category_description',
                    header: App.Language.General.description,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'plan_category_is_default',
                    header: App.Language.Plan.use_category_for_linking_nodes,
                    sortable: true,
                    width: 100
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
                        w = new App.Maintainers.addPlanCategoryWindow();
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
                        grid = Ext.getCmp('App.Maintainers.PlanCategoryGrid');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Plan.Category.Store.remove(record);
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
                    text: App.Language.General.settings,
                    iconCls: 'settings_icon',
                    handler: function(b) {
                        w = new App.Maintainers.addPlanConfigCategoryWindow();
                        w.show();
                    }
                }]
            }
        }];
        App.Maintainers.Planimetry.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.addPlanConfigCategoryWindow = Ext.extend(Ext.Window, {
    title: 'Configuraci&oacute;n de la Categor&iacute;a con el Tipo de Nodo',
    resizable: false,
    modal: true,
    border: true,
    width: 700,
    height: 500,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.ConfigCategory',
                ref: 'planCategoryGrid',
                store: App.Plan.Config.Store,
                height: 420,
                viewConfig: {
                    forceFit: true
                },
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'beforerender': function() {
                        App.Plan.Config.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'node_type_name',
                        header: App.Language.Infrastructure.node_type,
                        sortable: true,
                        width: 100
                    }
                    //                , 
                    //                {
                    //                    xtype: 'gridcolumn',
                    //                    dataIndex: 'node_type_category_name',
                    //                    header: App.Language.General.category,
                    //                    sortable: true,
                    //                    width: 100
                    //                }
                    , {
                        xtype: 'gridcolumn',
                        dataIndex: 'PlanCategory',
                        header: 'Categoria',
                        sortable: true,
                        width: 100,
                        renderer: function(PlanCategory) {
                            if (PlanCategory.plan_category_name != undefined) {
                                return PlanCategory.plan_category_name;
                            }
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: 'Asociar Categor&iacute;a',
                        iconCls: 'add_icon',
                        handler: function() {
                            grid = Ext.getCmp('App.Maintainers.ConfigCategory');
                            if (grid.getSelectionModel().getCount()) {

                                records = grid.getSelectionModel().getSelections();
                                aux = new Array();
                                for (var i = 0; i < records.length; i++) {
                                    aux.push(records[i].data.node_type_id);
                                }
                                node_type_ids = (aux.join(','));

                                w = new App.Maintainers.addConfigCategoryTypeNodeWindow();
                                w.show();
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        xtype: 'button',
                        text: 'Desasociar Categor&iacute;a',
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigCategory');
                            if (grid.getSelectionModel().getCount()) {

                                records = grid.getSelectionModel().getSelections();
                                aux = new Array();
                                for (var i = 0; i < records.length; i++) {
                                    aux.push(records[i].data.node_type_id);
                                }
                                node_type_ids = (aux.join(','));

                                Ext.MessageBox.confirm(App.Language.General.confirmation, 'Esta Seguro de Desasociar Estos Tipos de Nodos?',
                                    function(b) {
                                        if (b == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: App.Language.General.message_generating_file,
                                                url: 'index.php/plan/category/desasociarPlanCategoryAndTypeNode',
                                                timeout: 10000000000,
                                                params: {
                                                    node_type_ids: node_type_ids

                                                },
                                                success: function(response) {
                                                    response = Ext.decode(response.responseText);
                                                    Ext.getCmp('App.Maintainers.ConfigCategory').fireEvent('beforerender', Ext.getCmp('App.Maintainers.ConfigCategory'));
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
        App.Maintainers.addPlanConfigCategoryWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addConfigCategoryTypeNodeWindow = Ext.extend(Ext.Window, {
    title: 'Asociar Categor&iacute;a al Tipo de Nodo',
    id: 'App.Maintainers.PlanCategoryVentana',
    resizable: false,
    modal: true,
    width: 450,
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
                id: 'App.Maintainers.PlanCategory',
                anchor: '100%',
                store: App.Plan.Category.Store,
                hiddenName: 'plan_category_id',
                triggerAction: 'all',
                displayField: 'plan_category_name',
                valueField: 'plan_category_id',
                editable: false,
                mode: 'remote',
                minChars: 0,
                listeners: {
                    'afterrender': function(cb) {
                        cb.__value = cb.value;
                        cb.setValue('');
                        cb.getStore().load({
                            callback: function() {
                                if (cb.store) {
                                    cb.setValue(cb.__value);
                                }
                            }
                        });
                    },
                    'beforedestroy': function(cb) {
                        cb.purgeListeners();
                    }
                }
            }],
            buttons: [{
                text: 'Cancelar',
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    Ext.MessageBox.confirm(App.Language.General.confirmation, 'Esta Seguro de Asociar Estos Tipos de Nodos a Esta Categor&iacute;a?',
                        function(b) {
                            if (b == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: App.Language.General.message_generating_file,
                                    url: 'index.php/plan/category/asociarPlanCategoryAndTypeNode',
                                    timeout: 10000000000,
                                    params: {
                                        node_type_ids: node_type_ids,
                                        plan_category_id: Ext.getCmp('App.Maintainers.PlanCategory').getValue()

                                    },
                                    success: function(response) {
                                        response = Ext.decode(response.responseText);
                                        Ext.getCmp('App.Maintainers.ConfigCategory').fireEvent('beforerender', Ext.getCmp('App.Maintainers.ConfigCategory'));
                                        Ext.getCmp('App.Maintainers.PlanCategoryVentana').close();
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

                }
            }]
        }];
        App.Maintainers.addConfigCategoryTypeNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addPlanCategoryWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_category,
    resizable: false,
    modal: true,
    width: 550,
    height: 250,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            labelWidth: 200,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'plan_category_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'plan_category_description',
                anchor: '100%'
            }, {
                xtype: 'checkbox',
                ref: 'chkSetDefaultCategory',
                fieldLabel: App.Language.Plan.set_category_for_linking_nodes,
                name: 'plan_category_default',
                inputValue: 1
            }, {
                xtype: 'checkbox',
                ref: 'chkSetVistaDetalle',
                fieldLabel: 'Vista Ficha de Detalle',
                name: 'plan_front_view',
                inputValue: 1
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
                            url: 'index.php/plan/category/add',
                            success: function(fp, o) {
                                App.Plan.Category.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
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
        App.Maintainers.addPlanCategoryWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.Planimetry.OpenEditMode = function(record) {
    w = new App.Maintainers.addPlanCategoryWindow({
        title: App.Language.General.edit_category
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    if (record.data.plan_category_default == 1) {
        w.form.chkSetDefaultCategory.hideLabel = true;
        w.form.chkSetDefaultCategory.hide();
        w.height = 200;
    }
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Plan.Category.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}