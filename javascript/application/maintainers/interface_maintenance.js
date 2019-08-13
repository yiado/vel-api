App.Maintainers.addToModuleMenu('mtn', {
    xtype: 'button',
    text: App.Language.Maintenance.maintenance,
    iconCls: 'maintain_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Maintenance'
});

App.Maintainers.Maintenance.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [{
                xtype: 'tabpanel',
                activeTab: 0,
                title: App.Language.Infrastructure.infrastructure,
                items: [{
                    xtype: 'grid',
                    title: App.Language.General.task,
                    id: 'App.Maintainers.TaskByNode',
                    store: App.Mtn.TaskByNode.StoreGrid,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.TaskOpenEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.TaskByNode.StoreGrid.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            header: App.Language.General.name,
                            dataIndex: 'mtn_task_name',
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel(),
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.addTaskByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.TaskByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                        if (b == 'yes') {
                                            grid.getSelectionModel().each(function(record) {
                                                App.Mtn.TaskByNode.StoreGrid.remove(record);
                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                            });
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }]
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Maintenance.plan,
                    id: 'App.Maintainers.PlanByNode',
                    store: App.Mtn.PlanByNode.Store,
                    height: 900,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.PlanOpenEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.PlanByNode.Store.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_plan_name',
                            header: App.Language.General.name,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_plan_description',
                            header: App.Language.General.description,
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
                                w = new App.Maintainers.addPlanByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.PlanByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                        if (b == 'yes') {
                                            grid.getSelectionModel().each(function(record) {
                                                App.Mtn.PlanByNode.Store.remove(record);
                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                            });
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }, {
                            xtype: 'tbseparator',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.settings,
                            iconCls: 'settings_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.PlanByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    mtn_plan_id = grid.getSelectionModel().getSelected().id;
                                    App.Mtn.PlanTask.Store.setBaseParam('mtn_plan_id', mtn_plan_id);
                                    w = new App.Maintainers.PlanByNodeConfigWindow();
                                    w.show();
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                                }
                            }
                        }]
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Maintenance.other_costs,
                    id: 'App.Maintainers.OtherCostsByNode',
                    store: App.Mtn.OtherCostsByNode.Store,
                    height: 900,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.OtherCostsConfigEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.OtherCostsByNode.Store.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_other_costs_name',
                            header: App.Language.General.name,
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel(),
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.addOtherCostsByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.OtherCostsByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                        if (b == 'yes') {
                                            grid.getSelectionModel().each(function(record) {
                                                App.Mtn.OtherCostsByNode.Store.remove(record);
                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                            });
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }]
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Maintenance.component_type,
                    id: 'App.Maintainers.TypeCompByNode',
                    store: App.Mtn.ComponentTypeByNode.Store,
                    height: 900,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.TypeComponentConfigEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.ComponentTypeByNode.Store.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_component_type_name',
                            header: App.Language.General.name,
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel(),
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.addTypeComponentByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.TypeCompByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                        if (b == 'yes') {
                                            grid.getSelectionModel().each(function(record) {

                                                Ext.Ajax.request({
                                                    url: 'index.php/mtn/componenttype/delete',
                                                    params: {
                                                        mtn_component_type_id: record.data.mtn_component_type_id
                                                    },
                                                    success: function(response) {
                                                        response = Ext.decode(response.responseText);
                                                        Ext.FlashMessage.alert(response.msg);
                                                        App.Mtn.ComponentTypeByNode.Store.load();
                                                    }
                                                });
                                            });
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }]
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.Maintenance.component,
                    id: 'App.Maintainers.ComponentByNode',
                    store: App.Mtn.ComponentByNode.Store,
                    height: 900,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.ComponentByNodeConfigEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.ComponentByNode.Store.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_component_name',
                            header: App.Language.General.name,
                            sortable: true,
                            width: 600
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_component_type_name',
                            header: App.Language.General.type,
                            sortable: true,
                            width: 300
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'measure_unit_name_and_description',
                            header: App.Language.General.unit,
                            sortable: true,
                            width: 110
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'Brand',
                            header: App.Language.General.brand,
                            sortable: true,
                            width: 80,
                            renderer: function(Brand) {
                                return Brand.brand_name;
                            }
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_component_model',
                            header: App.Language.Maintenance.model,
                            sortable: true,
                            width: 80
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel(),
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.addComponentByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.ComponentByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                        if (b == 'yes') {
                                            grid.getSelectionModel().each(function(record) {

                                                Ext.Ajax.request({
                                                    url: 'index.php/mtn/component/delete',
                                                    params: {
                                                        mtn_component_id: record.data.mtn_component_id
                                                    },
                                                    success: function(response) {
                                                        response = Ext.decode(response.responseText);
                                                        Ext.FlashMessage.alert(response.msg);
                                                        App.Mtn.ComponentByNode.Store.load();
                                                    }
                                                });
                                                //                                            App.Mtn.ComponentByNode.Store.remove(record);
                                                //                                            Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                            });
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }]
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.General.provider_type,
                    store: App.Core.ProviderTypeByNode.Store,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.General.OpenEditModeProviderType(record);
                        },
                        'beforerender': function(grid) {
                            grid.store.load();
                        }
                    },
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'button',
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.General.AddProviderTypeByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'tbseparator'
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
                                                App.Core.ProviderTypeByNode.Store.remove(record);
                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
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
                            dataIndex: 'provider_type_name',
                            header: App.Language.General.name,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'provider_type_description',
                            header: App.Language.General.description,
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel()
                }, {
                    xtype: 'grid',
                    title: App.Language.General.providers,
                    store: App.Core.ProviderByNode.Store,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.General.OpenEditModeProviderByNode(record);
                        },
                        'beforerender': function(grid) {
                            grid.store.load();
                        }
                    },
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'button',
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.General.AddProviderByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'tbseparator'
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
                                                App.Core.ProviderByNode.Store.remove(record);
                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
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
                            dataIndex: 'provider_name',
                            header: App.Language.General.name,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'provider_type_name',
                            header: App.Language.General.type,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'provider_contact',
                            header: App.Language.General.contact,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'provider_email',
                            header: App.Language.Core.email,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'provider_phone',
                            header: App.Language.General.phone,
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel()
                }, {
                    xtype: 'grid',
                    title: App.Language.General.list_price,
                    id: 'App.Maintainers.ListPriceByNode',
                    store: App.Mtn.PriceListByNode.Store,
                    height: 900,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.ListPriceByNodeOpenEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.PriceListByNode.Store.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'provider_name',
                            header: App.Language.Maintenance.provider_name,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'gridcolumn',
                            dataIndex: 'currency_name',
                            header: App.Language.General.type_currency,
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'datecolumn',
                            header: App.Language.General.start_date,
                            format: App.General.DefaultDateFormat,
                            dataIndex: 'mtn_price_list_date_validity_start',
                            sortable: true,
                            width: 100
                        }, {
                            xtype: 'datecolumn',
                            dataIndex: 'mtn_price_list_date_validity_finish',
                            header: App.Language.General.end_date,
                            format: App.General.DefaultDateFormat,
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel(),
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.addListPriceByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.ListPriceByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                        if (b == 'yes') {
                                            grid.getSelectionModel().each(function(record) {
                                                Ext.Ajax.request({
                                                    url: 'index.php/mtn/pricelist/delete',
                                                    params: {
                                                        mtn_price_list_id: record.data.mtn_price_list_id
                                                    },
                                                    success: function(response) {
                                                        response = Ext.decode(response.responseText);

                                                        if (response.success === "false") {
                                                            Ext.FlashMessage.alert(response.msg);
                                                        } else {
                                                            Ext.FlashMessage.alert(response.msg);
                                                            App.Mtn.PriceListByNode.Store.load();
                                                        }
                                                    }
                                                });
                                                //                                            App.Mtn.PriceListByNode.Store.remove(record);
                                                //                                            Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                            });
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }, {
                            xtype: 'tbseparator',
                            width: 20
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.settings,
                            iconCls: 'settings_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.ListPriceByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    mtn_price_list_id = grid.getSelectionModel().getSelected().id;
                                    App.Mtn.PriceListComponentAll.Store.setBaseParam('mtn_price_list_id', mtn_price_list_id);
                                    w = new App.Maintainers.ListPriceByNodeConfigWindow();
                                    w.show();
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                                }
                            }
                        }]
                    }
                }, {
                    xtype: 'grid',
                    title: App.Language.General.state_ot,
                    store: App.Mtn.PossibleStatusByNode.Store,
                    height: 900,
                    laodMask: true,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.StateOpenEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.PossibleStatusByNode.Store.load();
                        }
                    },
                    tbar: {
                        xtype: 'toolbar',
                        items: [{
                            text: App.Language.General.add,
                            iconCls: 'add_icon',
                            handler: function() {
                                w = new App.Maintainers.addStateWoByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
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
                                                App.Mtn.PossibleStatusByNode.Store.remove(record);
                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
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
                            dataIndex: 'mtn_system_work_order_status_name',
                            header: App.Language.General.state,
                            sortable: true,
                            width: 100
                        }
                    ],
                    sm: new Ext.grid.CheckboxSelectionModel()
                }, {
                    xtype: 'grid',
                    title: App.Language.Maintenance.type_ot,
                    id: 'App.Maintainers.TypeWOByNode',
                    store: App.Mtn.WoTypesAllByNode.Store,
                    height: 900,
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid, rowIndex) {
                            record = grid.getStore().getAt(rowIndex);
                            App.Maintainers.TypeWoConfigEditMode(record);
                        },
                        'beforerender': function() {
                            App.Mtn.WoTypesAllByNode.Store.load();
                        }
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'mtn_work_order_type_name',
                            header: App.Language.General.name,
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
                                w = new App.Maintainers.addTypeWoByNodeWindow();
                                w.show();
                            }
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.ddelete,
                            iconCls: 'delete_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.TypeWOByNode');
                                if (grid.getSelectionModel().getCount()) {

                                    Ext.Ajax.request({
                                        url: 'index.php/mtn/wotype/validatorDelete',
                                        params: {
                                            mtn_work_order_type_id: grid.getSelectionModel().getSelected().id
                                        },
                                        success: function(response) {
                                            response = Ext.decode(response.responseText);
                                            if (response.success == 'false') {

                                                Ext.FlashMessage.alert(response.msg);
                                            } else {

                                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Maintenance.delete_wo_action + ' (' + response.msg + ')', function(b) {
                                                    if (b == 'yes') {

                                                        grid.getSelectionModel().each(function(record) {
                                                            App.Mtn.WoTypesAllByNode.Store.remove(record);
                                                            Ext.FlashMessage.alert(App.Language.General.operation_successful);
                                                        });


                                                    }
                                                });
                                            }
                                        }
                                    });
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                }
                            }
                        }, {
                            xtype: 'tbseparator',
                            width: 20
                        }, {
                            xtype: 'spacer',
                            width: 5
                        }, {
                            xtype: 'button',
                            text: App.Language.General.settings,
                            iconCls: 'settings_icon',
                            handler: function(b) {
                                grid = Ext.getCmp('App.Maintainers.TypeWOByNode');
                                if (grid.getSelectionModel().getCount()) {
                                    mtn_work_order_type_id = grid.getSelectionModel().getSelected().id;
                                    App.Mtn.ConfigStateAsociadosAll.Store.setBaseParam('mtn_work_order_type_id', mtn_work_order_type_id);
                                    w = new App.Maintainers.StateByNodeConfigWOWindow();
                                    w.show();
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                                }
                            }
                        }]
                    }
                }]
            }
            //        , {
            //            xtype: 'tabpanel',
            //            activeTab: 0,
            //            title: 'Activos',
            //            items: [{
            //                    xtype: 'grid',
            //                    title: App.Language.General.task,
            //                    id: 'App.Maintainers.Task',
            //                    store: App.Mtn.Task.StoreGrid,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.TaskOpenEditMode(record);
            //                        },
            //                        'beforerender': function()
            //                        {
            //                            App.Mtn.Task.StoreGrid.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        header: App.Language.General.name,
            //                        dataIndex: 'mtn_task_name',
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel(),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addTaskWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.Task');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.Task.StoreGrid.remove(record);
            //                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.Maintenance.plan,
            //                    id: 'App.Maintainers.Plan',
            //                    store: App.Mtn.Plan.Store,
            //                    height: 900,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.PlanOpenEditMode(record);
            //                        },
            //                        'beforerender': function() {
            //                            App.Mtn.Plan.Store.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_plan_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_plan_description',
            //                        header: App.Language.General.description,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel
            //                    ({
            //                        singleSelect: true
            //                    }),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addPlanWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.Plan');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.Plan.Store.remove(record);
            //                                                Ext.FlashMessage.alert(App.Language.General.operation_successful);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }, {
            //                            xtype: 'tbseparator',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.settings,
            //                            iconCls: 'settings_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.Plan');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    mtn_plan_id = grid.getSelectionModel().getSelected().id;
            //                                    App.Mtn.PlanTask.Store.setBaseParam('mtn_plan_id', mtn_plan_id);
            //                                    w = new App.Maintainers.PlanConfigWindow();
            //                                    w.show();
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.Maintenance.other_costs,
            //                    id: 'App.Maintainers.OtherCosts',
            //                    store: App.Mtn.OtherCosts.Store,
            //                    height: 900,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.OtherCostsConfigEditMode(record);
            //                        },
            //                        'beforerender': function() {
            //                            App.Mtn.OtherCosts.Store.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_other_costs_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel(),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addOtherCostsWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.OtherCosts');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.OtherCosts.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.Maintenance.component_type,
            //                    id: 'App.Maintainers.TypeComp',
            //                    store: App.Mtn.ComponentType.Store,
            //                    height: 900,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.TypeComponentConfigEditMode(record);
            //                        },
            //                        'beforerender': function()
            //                        {
            //                            App.Mtn.ComponentType.Store.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_type_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel(),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addTypeComponentWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.TypeComp');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.ComponentType.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.Maintenance.component,
            //                    id: 'App.Maintainers.Component',
            //                    store: App.Mtn.Component.Store,
            //                    height: 900,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.ComponentConfigEditMode(record);
            //                        },
            //                        'beforerender': function()
            //                        {
            //                            App.Mtn.Component.Store.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_type_name',
            //                        header: App.Language.General.type,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_weight',
            //                        header: App.Language.General.unit,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'Brand',
            //                        header: App.Language.General.brand,
            //                        sortable: true,
            //                        width: 100,
            //                        renderer: function(Brand)
            //                        {
            //                            return Brand.brand_name;
            //                        }
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_model',
            //                        header: App.Language.Maintenance.model,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_manufacturer',
            //                        header: App.Language.Maintenance.manufacturer,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_component_comment',
            //                        header: App.Language.General.commentary,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel(),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addComponentWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.Component');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.Component.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.General.provider_type,
            //                    store: App.Core.ProviderType.Store,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.General.OpenEditModeProviderType(record);
            //                        },
            //                        'beforerender': function(grid)
            //                        {
            //                            grid.store.load();
            //                        }
            //                    },
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            xtype: 'button',
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.General.AddProviderTypeWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'tbseparator'
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = b.ownerCt.ownerCt;
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b)
            //                                    {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Core.ProviderType.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_type_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_type_description',
            //                        header: App.Language.General.description,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel()
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.General.providers,
            //                    store: App.Core.Provider.Store,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.General.OpenEditModeProvider(record);
            //                        },
            //                        'beforerender': function(grid)
            //                        {
            //                            grid.store.load();
            //                        }
            //                    },
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            xtype: 'button',
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.General.AddProviderWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'tbseparator'
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = b.ownerCt.ownerCt;
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b)
            //                                    {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Core.Provider.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    },
            //                    columns:
            //                    [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_type_name',
            //                        header: App.Language.General.type,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_contact',
            //                        header: App.Language.General.contact,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_email',
            //                        header: App.Language.Core.email,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_phone',
            //                        header: App.Language.General.phone,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel()
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.General.list_price,
            //                    id: 'App.Maintainers.ListPrice',
            //                    store: App.Mtn.PriceList.Store,
            //                    height: 900,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex) {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.ListPriceOpenEditMode(record);
            //                        },
            //                        'beforerender': function()
            //                        {
            //                            App.Mtn.PriceList.Store.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'provider_name',
            //                        header: App.Language.Maintenance.provider_name,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'currency_name',
            //                        header: App.Language.General.type_currency,
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'datecolumn',                 
            //                        header: App.Language.General.start_date,
            //                        format: App.General.DefaultDateFormat, 
            //                        dataIndex: 'mtn_price_list_date_validity_start',
            //                        sortable: true,
            //                        width: 100
            //                    }, {
            //                        xtype: 'datecolumn',
            //                        dataIndex: 'mtn_price_list_date_validity_finish',
            //                        header: App.Language.General.end_date,
            //                        format: App.General.DefaultDateFormat,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel(),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addListPriceWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.ListPrice');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.PriceList.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }, {
            //                            xtype: 'tbseparator',
            //                            width: 20
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.settings,
            //                            iconCls: 'config_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.ListPrice');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    mtn_price_list_id = grid.getSelectionModel().getSelected().id;
            //                                    App.Mtn.PriceListComponentAll.Store.setBaseParam('mtn_price_list_id', mtn_price_list_id);
            //                                    w = new App.Maintainers.ListPriceConfigWindow();
            //                                    w.show();
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.General.state_ot,
            //                    store: App.Mtn.PossibleStatus.Store,
            //                    height: 900,
            //                    laodMask: true,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.StateOpenEditMode(record);
            //                        },
            //                        'beforerender': function()
            //                        {
            //                            App.Mtn.PossibleStatus.Store.load();
            //                        }
            //                    },
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addStateWoWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = b.ownerCt.ownerCt;
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.PossibleStatus.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }]
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_system_work_order_status_name',
            //                        header: App.Language.General.state,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel()
            //                }, {
            //                    xtype: 'grid',
            //                    title: App.Language.Maintenance.type_ot,
            //                    id: 'App.Maintainers.TypeWO',
            //                    store: App.Mtn.WoTypesAll.Store,
            //                    height: 900,
            //                    viewConfig:
            //                    {
            //                        forceFit: true
            //                    },
            //                    listeners:
            //                    {
            //                        'rowdblclick': function(grid, rowIndex)
            //                        {
            //                            record = grid.getStore().getAt(rowIndex);
            //                            App.Maintainers.TypeWoConfigEditMode(record);
            //                        },
            //                        'beforerender': function()
            //                        {
            //                            App.Mtn.WoTypesAll.Store.load();
            //                        }
            //                    },
            //                    columns: [new Ext.grid.CheckboxSelectionModel(),
            //                    {
            //                        xtype: 'gridcolumn',
            //                        dataIndex: 'mtn_work_order_type_name',
            //                        header: App.Language.General.name,
            //                        sortable: true,
            //                        width: 100
            //                    }],
            //                    sm: new Ext.grid.CheckboxSelectionModel
            //                    ({
            //                        singleSelect: true
            //                    }),
            //                    tbar:
            //                    {
            //                        xtype: 'toolbar',
            //                        items:
            //                        [{
            //                            text: App.Language.General.add,
            //                            iconCls: 'add_icon',
            //                            handler: function()
            //                            {
            //                                w = new App.Maintainers.addTypeWoWindow();
            //                                w.show();
            //                            }
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.ddelete,
            //                            iconCls: 'delete_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.TypeWO');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
            //                                        if (b == 'yes')
            //                                        {
            //                                            grid.getSelectionModel().each(function(record)
            //                                            {
            //                                                App.Mtn.WoTypesAll.Store.remove(record);
            //                                            });
            //                                        }
            //                                    });
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
            //                                }
            //                            }
            //                        }, {
            //                            xtype: 'tbseparator',
            //                            width: 20
            //                        }, {
            //                            xtype: 'spacer',
            //                            width: 5
            //                        }, {
            //                            xtype: 'button',
            //                            text: App.Language.General.settings,
            //                            iconCls: 'settings_icon',
            //                            handler: function(b)
            //                            {
            //                                grid = Ext.getCmp('App.Maintainers.TypeWO');
            //                                if (grid.getSelectionModel().getCount())
            //                                {
            //                                    mtn_work_order_type_id = grid.getSelectionModel().getSelected().id;
            //                                    App.Mtn.ConfigStateAsociadosAll.Store.setBaseParam('mtn_work_order_type_id', mtn_work_order_type_id);
            //                                    w = new App.Maintainers.StateConfigWOWindow();
            //                                    w.show();
            //                                } else {
            //                                    Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
            //                                }
            //                            }
            //                        }]
            //                    }
            //                }]
            //        }
        ];
        App.Maintainers.Maintenance.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.addTaskWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_task,
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
                fieldLabel: App.Language.General.task_name,
                name: 'mtn_task_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/task/add',
                            success: function(fp, o) {
                                App.Mtn.Task.StoreGrid.load();
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
        App.Maintainers.addTaskWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addTaskByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_task,
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
                fieldLabel: App.Language.General.task_name,
                name: 'mtn_task_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/task/addByNode',
                            success: function(fp, o) {
                                App.Mtn.TaskByNode.StoreGrid.load();
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
        App.Maintainers.addTaskByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.TaskOpenEditMode = function(record) {
    w = new App.Maintainers.addTaskWindow({
        title: App.Language.Maintenance.edit_task
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.Task.StoreGrid.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addPlanWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_plan,
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
                fieldLabel: App.Language.General.name,
                name: 'mtn_plan_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'mtn_plan_description',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/plan/add',
                            success: function(fp, o) {
                                App.Mtn.Plan.Store.load();
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
        App.Maintainers.addPlanWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addPlanByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_plan,
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
                fieldLabel: App.Language.General.name,
                name: 'mtn_plan_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'mtn_plan_description',
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/plan/addByNode',
                            success: function(fp, o) {
                                App.Mtn.PlanByNode.Store.load();
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
        App.Maintainers.addPlanByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.PlanOpenEditMode = function(record) {
    w = new App.Maintainers.addPlanWindow({
        title: App.Language.Maintenance.adit_plan
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.Plan.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.AddPlanConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.the_plan_task_login,
    resizable: false,
    modal: true,
    border: true,
    width: 380,
    height: 140,
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
                xtype: 'combo',
                fieldLabel: App.Language.General.task,
                anchor: '100%',
                store: App.Mtn.Task.Store,
                allowBlank: false,
                hiddenName: 'mtn_task_id',
                triggerAction: 'all',
                displayField: 'mtn_task_name',
                valueField: 'mtn_task_id',
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
                xtype: 'numberfield',
                fieldLabel: App.Language.Maintenance.periodicity_days,
                anchor: '100%',
                name: 'mtn_plan_task_interval'
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
                            url: 'index.php/mtn/plantask/add',
                            params: {
                                mtn_plan_id: mtn_plan_id
                            },
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Mtn.PlanTask.Store.load();
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
        App.Maintainers.AddPlanConfigWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AddPlanByNodeConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.the_plan_task_login,
    resizable: false,
    modal: true,
    border: true,
    width: 380,
    height: 140,
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
                xtype: 'combo',
                fieldLabel: App.Language.General.task,
                anchor: '100%',
                store: App.Mtn.TaskByNode.StoreGrid, //ojo estoy usando del la grilla
                allowBlank: false,
                hiddenName: 'mtn_task_id',
                triggerAction: 'all',
                displayField: 'mtn_task_name',
                valueField: 'mtn_task_id',
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
                xtype: 'numberfield',
                fieldLabel: App.Language.Maintenance.periodicity_days,
                anchor: '100%',
                name: 'mtn_plan_task_interval'
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
                            url: 'index.php/mtn/plantask/add',
                            params: {
                                mtn_plan_id: mtn_plan_id
                            },
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Mtn.PlanTask.Store.load();
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
        App.Maintainers.AddPlanByNodeConfigWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.PlanConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.configuration_plan,
    resizable: false,
    modal: true,
    border: true,
    width: 500,
    height: 390,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.PlanTask',
                store: App.Mtn.PlanTask.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.PlanConfigEditMode(record);
                    },
                    'beforerender': function() {
                        App.Mtn.PlanTask.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_task_name',
                        header: App.Language.General.task,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_plan_task_interval',
                        header: App.Language.Maintenance.periodicity_days,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            App.Mtn.Task.Store.setBaseParam('mtn_plan_id', mtn_plan_id);
                            w = new App.Maintainers.AddPlanConfigWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.PlanTask');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            mtn_task_id = grid.getSelectionModel().getSelected().id;
                                            App.Mtn.PlanTask.Store.setBaseParam('mtn_task_id', mtn_task_id);
                                            App.Mtn.PlanTask.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
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
        App.Maintainers.PlanConfigWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.PlanByNodeConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.configuration_plan,
    resizable: false,
    modal: true,
    border: true,
    width: 500,
    height: 390,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.PlanTask',
                store: App.Mtn.PlanTask.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.PlanByNodeConfigEditMode(record);
                    },
                    'beforerender': function() {
                        App.Mtn.PlanTask.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_task_name',
                        header: App.Language.General.task,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_plan_task_interval',
                        header: App.Language.Maintenance.periodicity_days,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            App.Mtn.TaskByNode.StoreGrid.setBaseParam('mtn_plan_id', mtn_plan_id);
                            w = new App.Maintainers.AddPlanByNodeConfigWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.PlanTask');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            mtn_task_id = grid.getSelectionModel().getSelected().id;
                                            App.Mtn.PlanTask.Store.setBaseParam('mtn_task_id', mtn_task_id);
                                            App.Mtn.PlanTask.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
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
        App.Maintainers.PlanByNodeConfigWindow.superclass.initComponent.call(this);
    }
});


App.Maintainers.PlanConfigEditMode = function(record) {
    w = new App.Maintainers.AddPlanConfigWindow({
        title: App.Language.Maintenance.edit_plan_task
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            App.Mtn.PlanTask.Store.load();
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}
App.Maintainers.PlanByNodeConfigEditMode = function(record) {
    w = new App.Maintainers.AddPlanByNodeConfigWindow({
        title: App.Language.Maintenance.edit_plan_task
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            App.Mtn.PlanTask.Store.load();
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.PriceListEditMode = function(record) {
    w = new App.Maintainers.AddListPriceComponentConfigWindow({
        title: App.Language.Maintenance.edit_price_list
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            App.Mtn.PriceListComponentAll.Store.load();
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}
App.Maintainers.PriceListByNodeEditMode = function(record) {
    w = new App.Maintainers.AddListPriceComponentByNodeConfigWindow({
        title: App.Language.Maintenance.edit_price_list
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            App.Mtn.PriceListComponentAll.Store.load();
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.General.OpenEditModeProviderType = function(record) {
    w = new App.Maintainers.General.AddProviderTypeWindow({
        title: App.Language.General.edit_provider_type
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

App.Maintainers.General.AddProviderTypeWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_provider_type,
    resizable: false,
    modal: true,
    width: 450,
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
                fieldLabel: App.Language.General.name,
                name: 'provider_type_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                anchor: '100%',
                name: 'provider_type_description'
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
                            url: 'index.php/core/providertype/add',
                            success: function(fp, o) {
                                App.Core.ProviderType.Store.load();
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
        App.Maintainers.General.AddProviderTypeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.General.AddProviderTypeByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_provider_type,
    resizable: false,
    modal: true,
    width: 450,
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
                fieldLabel: App.Language.General.name,
                name: 'provider_type_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                anchor: '100%',
                name: 'provider_type_description'
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
                            url: 'index.php/core/providertype/addByNode',
                            success: function(fp, o) {
                                App.Core.ProviderTypeByNode.Store.load();
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
        App.Maintainers.General.AddProviderTypeByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.General.OpenEditModeProvider = function(record) {
    w = new App.Maintainers.General.AddProviderWindow({
        title: App.Language.General.edit_provider
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
App.Maintainers.General.OpenEditModeProviderByNode = function(record) {
    w = new App.Maintainers.General.AddProviderByNodeWindow({
        title: App.Language.General.edit_provider
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
App.Maintainers.General.AddProviderWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_provider,
    resizable: false,
    modal: true,
    width: 500,
    height: 320,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            defaultType: 'textfield',
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.type,
                anchor: '100%',
                store: App.Core.ProviderType.Store,
                hiddenName: 'provider_type_id',
                triggerAction: 'all',
                displayField: 'provider_type_name',
                valueField: 'provider_type_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
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
                fieldLabel: App.Language.General.name,
                name: 'provider_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                fieldLabel: App.Language.General.contact,
                anchor: '100%',
                name: 'provider_contact'
            }, {
                fieldLabel: App.Language.General.phone,
                anchor: '100%',
                name: 'provider_phone'
            }, {
                fieldLabel: App.Language.General.fax,
                anchor: '100%',
                name: 'provider_fax'
            }, {
                vtype: 'email',
                fieldLabel: App.Language.Core.email,
                anchor: '100%',
                name: 'provider_email'
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                anchor: '100%',
                name: 'provider_description'
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
                            url: 'index.php/core/provider/add',
                            success: function(fp, o) {
                                App.Core.Provider.Store.load();
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
        App.Maintainers.General.AddProviderWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.General.AddProviderByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_provider,
    resizable: false,
    modal: true,
    width: 500,
    height: 320,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            defaultType: 'textfield',
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.type,
                anchor: '100%',
                store: App.Core.ProviderTypeByNode.Store,
                hiddenName: 'provider_type_id',
                triggerAction: 'all',
                displayField: 'provider_type_name',
                valueField: 'provider_type_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
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
                fieldLabel: App.Language.General.name,
                name: 'provider_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                fieldLabel: App.Language.General.contact,
                anchor: '100%',
                name: 'provider_contact'
            }, {
                fieldLabel: App.Language.General.phone,
                anchor: '100%',
                name: 'provider_phone'
            }, {
                fieldLabel: App.Language.General.fax,
                anchor: '100%',
                name: 'provider_fax'
            }, {
                vtype: 'email',
                fieldLabel: App.Language.Core.email,
                anchor: '100%',
                name: 'provider_email'
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                anchor: '100%',
                name: 'provider_description'
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
                            url: 'index.php/core/provider/addByNode',
                            success: function(fp, o) {
                                App.Core.ProviderByNode.Store.load();
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
        App.Maintainers.General.AddProviderByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addTypeWoWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_type_ot,
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
                fieldLabel: App.Language.Maintenance.type_name_ot,
                name: 'mtn_work_order_type_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/wotype/add',
                            success: function(fp, o) {
                                App.Mtn.WoTypesAll.Store.load();
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
        App.Maintainers.addTypeWoWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addTypeWoByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_type_ot,
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
                fieldLabel: App.Language.Maintenance.type_name_ot,
                name: 'mtn_work_order_type_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/wotype/addByNode',
                            success: function(fp, o) {
                                App.Mtn.WoTypesAllByNode.Store.load();
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
        App.Maintainers.addTypeWoByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.TypeWoConfigEditMode = function(record) {
    w = new App.Maintainers.addTypeWoWindow({
        title: App.Language.Maintenance.edit_type_ot
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.WoTypesAll.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}


App.Maintainers.addTypeComponentWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_component_type,
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
                fieldLabel: App.Language.Maintenance.component_type_name,
                name: 'mtn_component_type_name',
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
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/componenttype/add',
                            success: function(fp, o) {
                                App.Mtn.ComponentType.Store.load();
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
        App.Maintainers.addTypeComponentWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addTypeComponentByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_component_type,
    resizable: false,
    modal: true,
    width: 680,
    height: 130,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 250,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.Maintenance.component_type_name,
                name: 'mtn_component_type_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/componenttype/addByNode',
                            success: function(fp, o) {
                                App.Mtn.ComponentTypeByNode.Store.load();
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
        App.Maintainers.addTypeComponentByNodeWindow.superclass.initComponent.call(this);
    }
});


App.Maintainers.TypeComponentConfigEditMode = function(record) {
    w = new App.Maintainers.addTypeComponentByNodeWindow({
        title: App.Language.Maintenance.edit_component_type
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.ComponentType.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addComponentWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_component,
    resizable: false,
    modal: true,
    width: 420,
    height: 280,
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
                fieldLabel: App.Language.General.name,
                name: 'mtn_component_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.type,
                anchor: '100%',
                store: App.Mtn.TypesComponent.Store,
                hiddenName: 'mtn_component_type_id',
                triggerAction: 'all',
                displayField: 'mtn_component_type_name',
                valueField: 'mtn_component_type_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
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
                fieldLabel: App.Language.General.unit,
                anchor: '100%',
                triggerAction: 'all',
                store: App.Core.MeasureUnit.Store,
                hiddenName: 'measure_unit_id',
                displayField: 'measure_unit_name_and_description',
                valueField: 'measure_unit_id',
                editable: true,
                typeAhead: true,
                selectOnFocus: true,
                forceSelection: true,
                allowBlank: false,
                mode: 'remote',
                minChars: 0
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
                fieldLabel: App.Language.Maintenance.model,
                name: 'mtn_component_model',
                anchor: '100%',
                minChars: 0
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Maintenance.manufacturer,
                name: 'mtn_component_manufacturer',
                anchor: '100%',
                minChars: 0
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.commentary,
                name: 'mtn_component_comment',
                anchor: '100%',
                minChars: 0
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/component/add',
                            success: function(fp, o) {
                                App.Mtn.Component.Store.load();
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
        App.Maintainers.addComponentWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addComponentByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_component,
    resizable: false,
    modal: true,
    width: 950,
    height: 280,
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
                fieldLabel: App.Language.General.name,
                name: 'mtn_component_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.type,
                anchor: '100%',
                store: App.Mtn.TypesComponentByNode.Store,
                hiddenName: 'mtn_component_type_id',
                triggerAction: 'all',
                displayField: 'mtn_component_type_name',
                valueField: 'mtn_component_type_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
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
                fieldLabel: App.Language.General.unit,
                anchor: '100%',
                triggerAction: 'all',
                store: App.Core.MeasureUnit.Store,
                hiddenName: 'measure_unit_id',
                displayField: 'measure_unit_name_and_description',
                valueField: 'measure_unit_id',
                editable: true,
                typeAhead: true,
                selectOnFocus: true,
                forceSelection: true,
                allowBlank: false,
                mode: 'remote',
                minChars: 0
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
                fieldLabel: App.Language.Maintenance.model,
                name: 'mtn_component_model',
                anchor: '100%',
                minChars: 0
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.Maintenance.manufacturer,
                name: 'mtn_component_manufacturer',
                anchor: '100%',
                minChars: 0
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.commentary,
                name: 'mtn_component_comment',
                anchor: '100%',
                minChars: 0
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
                            url: 'index.php/mtn/component/addByNode',
                            success: function(fp, o) {
                                App.Mtn.ComponentByNode.Store.load();
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
        App.Maintainers.addComponentByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.ComponentConfigEditMode = function(record) {
    w = new App.Maintainers.addComponentWindow({
        title: App.Language.Maintenance.edit_component
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            App.Mtn.Component.Store.load();
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.ComponentByNodeConfigEditMode = function(record) {
    w = new App.Maintainers.addComponentByNodeWindow({
        title: App.Language.Maintenance.edit_component
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            App.Mtn.Component.Store.load();
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addOtherCostsWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_other_costs,
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
                fieldLabel: App.Language.Maintenance.name_of_other_costs,
                name: 'mtn_other_costs_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/othercosts/add',
                            success: function(fp, o) {
                                App.Mtn.OtherCosts.Store.load();
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
        App.Maintainers.addOtherCostsWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addOtherCostsByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_other_costs,
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
                fieldLabel: App.Language.Maintenance.name_of_other_costs,
                name: 'mtn_other_costs_name',
                anchor: '100%',
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/othercosts/addByNode',
                            success: function(fp, o) {
                                App.Mtn.OtherCostsByNode.Store.load();
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
        App.Maintainers.addOtherCostsByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.OtherCostsConfigEditMode = function(record) {
    w = new App.Maintainers.addOtherCostsWindow({
        title: App.Language.Maintenance.edit_other_costs
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.OtherCosts.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}


App.Maintainers.addListPriceWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_price_list,
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
            labelWidth: 130,
            padding: 5,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.provider,
                anchor: '100%',
                store: App.Core.Provider.Store,
                triggerAction: 'all',
                hiddenName: 'provider_id',
                displayField: 'provider_name',
                valueField: 'provider_id',
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
                xtype: 'combo',
                fieldLabel: App.Language.Maintenance.currency,
                anchor: '100%',
                store: App.Core.Currency.Store,
                triggerAction: 'all',
                hiddenName: 'currency_id',
                displayField: 'currency_name',
                valueField: 'currency_id',
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
                xtype: 'datefield',
                ref: 'mtn_price_list_date_validity_start',
                fieldLabel: App.Language.General.start_date,
                name: 'mtn_price_list_date_validity_start',
                allowBlank: false,
                anchor: '100%',
                listeners: {
                    'select': function(fd, date) {
                        fd.ownerCt.mtn_price_list_date_validity_finish.setMinValue(date);
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'mtn_price_list_date_validity_finish',
                fieldLabel: App.Language.General.end_date,
                name: 'mtn_price_list_date_validity_finish',
                allowBlank: false,
                anchor: '100%',
                listeners: {
                    'select': function(fd, date) {
                        fd.ownerCt.mtn_price_list_date_validity_start.setMaxValue(date);
                    }
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/pricelist/add',
                            success: function(fp, o) {
                                App.Mtn.PriceList.Store.load();
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
        App.Maintainers.addListPriceWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addListPriceByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_price_list,
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
            labelWidth: 130,
            padding: 5,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.provider,
                anchor: '100%',
                store: App.Core.ProviderByNode.Store,
                triggerAction: 'all',
                hiddenName: 'provider_id',
                displayField: 'provider_name',
                valueField: 'provider_id',
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
                xtype: 'combo',
                fieldLabel: App.Language.Maintenance.currency,
                anchor: '100%',
                store: App.Core.Currency.Store,
                triggerAction: 'all',
                hiddenName: 'currency_id',
                displayField: 'currency_name',
                valueField: 'currency_id',
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
                xtype: 'datefield',
                ref: 'mtn_price_list_date_validity_start',
                fieldLabel: App.Language.General.start_date,
                name: 'mtn_price_list_date_validity_start',
                allowBlank: false,
                anchor: '100%',
                listeners: {
                    'select': function(fd, date) {
                        fd.ownerCt.mtn_price_list_date_validity_finish.setMinValue(date);
                    }
                }
            }, {
                xtype: 'datefield',
                ref: 'mtn_price_list_date_validity_finish',
                fieldLabel: App.Language.General.end_date,
                name: 'mtn_price_list_date_validity_finish',
                allowBlank: false,
                anchor: '100%',
                listeners: {
                    'select': function(fd, date) {
                        fd.ownerCt.mtn_price_list_date_validity_start.setMaxValue(date);
                    }
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/mtn/pricelist/addByNode',
                            success: function(fp, o) {
                                App.Mtn.PriceListByNode.Store.load();
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
        App.Maintainers.addListPriceByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.ListPriceOpenEditMode = function(record) {
    w = new App.Maintainers.addListPriceWindow({
        title: App.Language.Maintenance.edit_price_list
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.PriceList.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}
App.Maintainers.ListPriceByNodeOpenEditMode = function(record) {
    w = new App.Maintainers.addListPriceByNodeWindow({
        title: App.Language.Maintenance.edit_price_list
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Mtn.PriceList.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}


App.Maintainers.ListPriceConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.setting_the_price_list,
    resizable: false,
    modal: true,
    border: true,
    width: 500,
    height: 390,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.ListPriceComponent',
                store: App.Mtn.PriceListComponentAll.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.PriceListEditMode(record);
                    },
                    'beforerender': function() {
                        App.Mtn.PriceListComponentAll.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_component_name',
                        header: App.Language.Maintenance.component_name,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_price_list_component_price',
                        header: App.Language.General.price,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            App.Mtn.Component.Store.setBaseParam('mtn_price_list_id', mtn_price_list_id);
                            w = new App.Maintainers.AddListPriceComponentConfigWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ListPriceComponent');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.Mtn.PriceListComponentAll.Store.setBaseParam('mtn_price_list_id', mtn_price_list_id);
                                            App.Mtn.PriceListComponentAll.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
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
        App.Maintainers.ListPriceConfigWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.ListPriceByNodeConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.setting_the_price_list,
    resizable: false,
    modal: true,
    border: true,
    width: 900,
    height: 390,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                id: 'App.Maintainers.ListPriceComponent',
                store: App.Mtn.PriceListComponentAll.Store,
                height: 310,
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.PriceListByNodeEditMode(record);
                    },
                    'beforerender': function() {
                        App.Mtn.PriceListComponentAll.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'component_name_and_description',
                        header: App.Language.Maintenance.component_name,
                        sortable: true,
                        width: 750
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_price_list_component_price',
                        header: App.Language.General.price,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            App.Mtn.ComponentByNode.Store.setBaseParam('mtn_price_list_id', mtn_price_list_id);
                            w = new App.Maintainers.AddListPriceComponentByNodeConfigWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ListPriceComponent');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            Ext.Ajax.request({
                                                url: 'index.php/mtn/pricelistcomponent/delete',
                                                params: {
                                                    mtn_price_list_id: record.data.mtn_price_list_id,
                                                    mtn_price_list_component_id: record.data.mtn_price_list_component_id
                                                },
                                                success: function(response) {
                                                    response = Ext.decode(response.responseText);

                                                    if (response.success === "false") {
                                                        Ext.FlashMessage.alert(response.msg);
                                                    } else {
                                                        Ext.FlashMessage.alert(response.msg);
                                                        App.Mtn.PriceListComponentAll.Store.load();
                                                    }

                                                }
                                            });
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
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
        App.Maintainers.ListPriceByNodeConfigWindow.superclass.initComponent.call(this);
    }
});


App.Maintainers.AddListPriceComponentConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.enter_components_list_price,
    resizable: false,
    modal: true,
    border: true,
    width: 380,
    height: 160,
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
                xtype: 'combo',
                fieldLabel: App.Language.Maintenance.component,
                anchor: '100%',
                store: App.Mtn.Component.Store,
                hiddenName: 'mtn_component_id',
                triggerAction: 'all',
                displayField: 'mtn_component_name',
                valueField: 'mtn_component_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
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
                xtype: 'numberfield',
                fieldLabel: App.Language.General.price,
                anchor: '100%',
                allowBlank: false,
                name: 'mtn_price_list_component_price'
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
                            url: 'index.php/mtn/pricelistcomponent/add',
                            params: {
                                mtn_price_list_id: mtn_price_list_id
                            },
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Mtn.PriceListComponentAll.Store.load();
                                Ext.MessageBox.alert(App.Language.Core.notification, o.result.msg);
                            },
                            failure: function(fp, o) {
                                Ext.MessageBox.alert(App.Language.General.error, o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.AddListPriceComponentConfigWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AddListPriceComponentByNodeConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.enter_components_list_price,
    resizable: false,
    modal: true,
    border: true,
    width: 380,
    height: 160,
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
                xtype: 'combo',
                fieldLabel: App.Language.Maintenance.component,
                anchor: '100%',
                store: App.Mtn.ComponentByNode.Store,
                hiddenName: 'mtn_component_id',
                triggerAction: 'all',
                displayField: 'component_name_and_description',
                valueField: 'mtn_component_id',
                editable: true,
                selecOnFocus: true,
                typeAhead: true,
                selectOnFocus: true,
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
                xtype: 'numberfield',
                fieldLabel: App.Language.General.price,
                anchor: '100%',
                allowBlank: false,
                name: 'mtn_price_list_component_price'
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
                            url: 'index.php/mtn/pricelistcomponent/add',
                            params: {
                                mtn_price_list_id: mtn_price_list_id
                            },
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Mtn.PriceListComponentAll.Store.load();
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
        App.Maintainers.AddListPriceComponentConfigWindow.superclass.initComponent.call(this);
    }
});


App.Maintainers.StateOpenEditMode = function(record) {
    w = new App.Maintainers.addStateWoWindow({
        title: App.Language.Maintenance.editing_state_of_the_ot
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            App.Mtn.PossibleStatus.Store.load();
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.addStateWoWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_to_ot_state,
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
                fieldLabel: App.Language.General.state_ot,
                name: 'mtn_system_work_order_status_name',
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
                            url: 'index.php/mtn/posstatus/add',
                            success: function(fp, o) {
                                App.Mtn.PossibleStatus.Store.load();
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
        App.Maintainers.addStateWoWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addStateWoByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_to_ot_state,
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
                fieldLabel: App.Language.General.state_ot,
                name: 'mtn_system_work_order_status_name',
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
                            url: 'index.php/mtn/posstatus/addByNode',
                            success: function(fp, o) {
                                App.Mtn.PossibleStatus.Store.load();
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
        App.Maintainers.addStateWoByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.addStateWoByNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_to_ot_state,
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
                fieldLabel: App.Language.General.state_ot,
                name: 'mtn_system_work_order_status_name',
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
                            url: 'index.php/mtn/posstatus/addByNode',
                            success: function(fp, o) {
                                App.Mtn.PossibleStatusByNode.Store.load();
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
        App.Maintainers.addStateWoByNodeWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.StateConfigWOWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.configuring_the_states_of_the_ot,
    modal: true,
    border: true,
    loadMask: true,
    width: 800,
    height: 350,
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
                id: 'App.Maintainers.ConfigStateAsociados',
                store: App.Mtn.ConfigStateAsociadosAll.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.StateConfigEditMode(record);
                    },
                    'beforerender': function() {
                        App.Mtn.ConfigStateAsociadosAll.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_system_work_order_status_name',
                        header: App.Language.General.state,
                        sortable: true,
                        width: 100
                    }, {
                        dataIndex: 'mtn_config_state_access_user_type',
                        header: App.Language.General.user_access,
                        align: 'center'
                    }, {
                        dataIndex: 'mtn_config_state_access_provider_type',
                        header: App.Language.General.provider_access,
                        align: 'center'
                    }, {
                        dataIndex: 'mtn_config_state_duration',
                        header: App.Language.Maintenance.duration_of_status,
                        align: 'center'
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
                            App.Mtn.ConfigStateDisponibles.Store.setBaseParam('mtn_work_order_type_id', mtn_work_order_type_id);
                            w = new App.Maintainers.AddStateConfigWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigStateAsociados');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.delete_is_really_sure_in_this_configuration_the_state, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            mtn_system_work_order_status_id = grid.getSelectionModel().getSelected().id;
                                            App.Mtn.ConfigStateAsociadosAll.Store.setBaseParam('mtn_system_work_order_status_id', mtn_system_work_order_status_id);
                                            App.Mtn.ConfigStateAsociadosAll.Store.remove(record);
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
                        text: App.Language.Maintenance.scroll_down,
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigStateAsociados');
                            if (grid.getSelectionModel().getCount()) {
                                mtn_config_state_id = grid.getSelectionModel().getSelected().id;
                                App.Mtn.MovStateUp(mtn_config_state_id);
                                Ext.getCmp('App.Maintainers.ConfigStateAsociados').fireEvent('beforerender', Ext.getCmp('App.Maintainers.ConfigStateAsociados'));
                                App.Mtn.ConfigStateAsociadosAll.Store.load();
                            } else {
                                Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_a_state_to_move_up);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        iconCls: 'up_icon',
                        text: App.Language.Maintenance.scroll_up,
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigStateAsociados');
                            if (grid.getSelectionModel().getCount()) {
                                mtn_config_state_id = grid.getSelectionModel().getSelected().id;
                                App.Mtn.MovStateDown(mtn_config_state_id);
                                Ext.getCmp('App.Maintainers.ConfigStateAsociados').fireEvent('beforerender', Ext.getCmp('App.Maintainers.ConfigStateAsociados'));
                                App.Mtn.ConfigStateAsociadosAll.Store.load();
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
        App.Maintainers.StateConfigWOWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.StateByNodeConfigWOWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.configuring_the_states_of_the_ot,
    modal: true,
    border: true,
    loadMask: true,
    width: 800,
    height: 350,
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
                id: 'App.Maintainers.ConfigStateAsociados',
                store: App.Mtn.ConfigStateAsociadosAll.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.StateConfigEditMode(record);
                    },
                    'beforerender': function() {
                        App.Mtn.ConfigStateAsociadosAll.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'mtn_system_work_order_status_name',
                        header: App.Language.General.state,
                        sortable: true,
                        width: 100
                    }, {
                        dataIndex: 'mtn_config_state_access_user_type',
                        header: App.Language.General.user_access,
                        align: 'center'
                    }, {
                        dataIndex: 'mtn_config_state_access_provider_type',
                        header: App.Language.General.provider_access,
                        align: 'center'
                    }, {
                        dataIndex: 'mtn_config_state_duration',
                        header: App.Language.Maintenance.duration_of_status,
                        align: 'center'
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
                            App.Mtn.ConfigStateDisponiblesByNode.Store.setBaseParam('mtn_work_order_type_id', mtn_work_order_type_id);
                            w = new App.Maintainers.AddStateByNodeConfigWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigStateAsociados');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Maintenance.delete_is_really_sure_in_this_configuration_the_state, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            mtn_system_work_order_status_id = grid.getSelectionModel().getSelected().id;
                                            App.Mtn.ConfigStateAsociadosAll.Store.setBaseParam('mtn_system_work_order_status_id', mtn_system_work_order_status_id);
                                            App.Mtn.ConfigStateAsociadosAll.Store.remove(record);
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
                        text: App.Language.Maintenance.scroll_down,
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigStateAsociados');
                            if (grid.getSelectionModel().getCount()) {
                                mtn_config_state_id = grid.getSelectionModel().getSelected().id;
                                App.Mtn.MovStateUp(mtn_config_state_id);
                                Ext.getCmp('App.Maintainers.ConfigStateAsociados').fireEvent('beforerender', Ext.getCmp('App.Maintainers.ConfigStateAsociados'));
                                App.Mtn.ConfigStateAsociadosAll.Store.load();
                            } else {
                                Ext.FlashMessage.alert(App.Language.Maintenance.you_must_select_a_state_to_move_up);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        iconCls: 'up_icon',
                        text: App.Language.Maintenance.scroll_up,
                        handler: function(b) {
                            grid = Ext.getCmp('App.Maintainers.ConfigStateAsociados');
                            if (grid.getSelectionModel().getCount()) {
                                mtn_config_state_id = grid.getSelectionModel().getSelected().id;
                                App.Mtn.MovStateDown(mtn_config_state_id);
                                Ext.getCmp('App.Maintainers.ConfigStateAsociados').fireEvent('beforerender', Ext.getCmp('App.Maintainers.ConfigStateAsociados'));
                                App.Mtn.ConfigStateAsociadosAll.Store.load();
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
        App.Maintainers.StateConfigWOWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AddStateConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_to_ot_state,
    resizable: false,
    modal: true,
    border: true,
    width: 380,
    height: 200,
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
                xtype: 'combo',
                fieldLabel: App.Language.General.state,
                id: 'App.Mtn.ConfigStateCombo',
                store: App.Mtn.ConfigStateDisponibles.Store,
                allowBlank: false,
                hiddenName: 'mtn_system_work_order_status_id',
                triggerAction: 'all',
                displayField: 'mtn_system_work_order_status_name',
                valueField: 'mtn_system_work_order_status_id',
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
                xtype: 'numberfield',
                fieldLabel: App.Language.Maintenance.duration_of_status,
                anchor: '70%',
                name: 'mtn_config_state_duration',
                minChars: 0
            }, {
                xtype: 'checkbox',
                fieldLabel: App.Language.General.user_access,
                id: 'App.Mtn.AccessUsers',
                name: 'mtn_config_state_access_user',
                inputValue: 1
            }, {
                xtype: 'checkbox',
                fieldLabel: App.Language.General.provider_access,
                id: 'App.Mtn.AccessProvider',
                name: 'mtn_config_state_access_provider',
                inputValue: 1
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
                            url: 'index.php/mtn/configstate/add',
                            params: {
                                mtn_work_order_type_id: mtn_work_order_type_id
                            },
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Mtn.ConfigStateAsociadosAll.Store.load();
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
        App.Maintainers.AddStateConfigWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.AddStateByNodeConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_to_ot_state,
    resizable: false,
    modal: true,
    border: true,
    width: 380,
    height: 200,
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
                xtype: 'combo',
                fieldLabel: App.Language.General.state,
                id: 'App.Mtn.ConfigStateCombo',
                store: App.Mtn.ConfigStateDisponiblesByNode.Store,
                allowBlank: false,
                hiddenName: 'mtn_system_work_order_status_id',
                triggerAction: 'all',
                displayField: 'mtn_system_work_order_status_name',
                valueField: 'mtn_system_work_order_status_id',
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
                xtype: 'numberfield',
                fieldLabel: App.Language.Maintenance.duration_of_status,
                anchor: '70%',
                name: 'mtn_config_state_duration',
                minChars: 0
            }, {
                xtype: 'checkbox',
                fieldLabel: App.Language.General.user_access,
                id: 'App.Mtn.AccessUsers',
                name: 'mtn_config_state_access_user',
                inputValue: 1
            }, {
                xtype: 'checkbox',
                fieldLabel: App.Language.General.provider_access,
                id: 'App.Mtn.AccessProvider',
                name: 'mtn_config_state_access_provider',
                inputValue: 1
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
                            url: 'index.php/mtn/configstate/add',
                            params: {
                                mtn_work_order_type_id: mtn_work_order_type_id
                            },
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.Mtn.ConfigStateAsociadosAll.Store.load();
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
        App.Maintainers.AddStateByNodeConfigWindow.superclass.initComponent.call(this);
    }
});


App.Maintainers.StateConfigEditMode = function(record) {
    w = new App.Maintainers.AddStateConfigWindow({
        title: App.Language.Maintenance.edit_configuration_states
    });
    Ext.getCmp('App.Mtn.ConfigStateCombo').setValue(record.data.mtn_system_work_order_status_name);
    Ext.getCmp('App.Mtn.ConfigStateCombo').setDisabled(true);
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function(b) {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            App.Mtn.ConfigStateAsociadosAll.Store.load();
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}