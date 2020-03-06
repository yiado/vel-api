App.InfraMtn.NodePlan.plan = null;
App.InfraMtn.NodePriceList.Id = null;

App.Maintainers.addToModuleMenu('inframtn', {
    xtype: 'button',
    text: App.Language.Infrastructure.Infra_maintenance,
    iconCls: 'inframaintain_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'InfraMtn'
});


App.Maintainers.InfraMtn.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            title: App.Language.Infrastructure.budget,
            id: 'App.InfraMtn.Budget.Grid',
            margins: '5 5 5 5',
            border: true,
            region: 'center',
            loadMask: true,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.InfraMtn.Budget.Edit(record.data.mtn_node_budget_id);
                }
            },
            store: App.InfraMtn.NodeBudget.Store,
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    dataIndex: 'mtn_node_budget_folio',
                    header: App.Language.Maintenance.folio,
                    sortable: true
                }, {
                    xtype: 'datecolumn',
                    header: App.Language.General.creation_date,
                    dataIndex: 'mtn_node_budget_date_created',
                    sortable: true,
                    align: 'center'
                }, {
                    dataIndex: 'mtn_node_budget_description',
                    header: App.Language.General.description,
                    sortable: true
                }, {
                    dataIndex: 'mtn_node_budget_total',
                    header: App.Language.General.total,
                    sortable: true
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.addTaskWindow();
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
                        grid = Ext.getCmp('App.Maintainers.Task');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Mtn.Task.StoreGrid.remove(record);
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
            id: 'App.InfraMtn.Plan',
            store: App.InfraMtn.NodePlan.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    mtn_node_plan_id = record.data.mtn_node_plan_id;
                    //App.InfraMtn.Wo.Edit(record.data.mtn_node_work_order_id);
                    new App.InfraMtn.NodePlan.Edit({
                        mtn_node_plan_id: mtn_node_plan_id
                    }).show();
                },
                'beforerender': function() {
                    App.InfraMtn.NodePlan.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'mtn_node_plan_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'mtn_node_plan_description',
                    header: App.Language.General.description,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'datecolumn',
                    dataIndex: 'mtn_node_plan_date_begin',
                    header: App.Language.General.start_date,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'datecolumn',
                    dataIndex: 'mtn_node_plan_date_finish',
                    header: App.Language.General.end_date,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'mtn_node_plan_total',
                    header: App.Language.General.total,
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
                        w = new App.InfraMtn.NodePlan.FormWindow();
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
                        grid = Ext.getCmp('App.InfraMtn.Plan');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraMtn.NodePlan.Store.remove(record);
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
                }]
            }
        }, {
            xtype: 'grid',
            title: App.Language.General.tasks,
            id: 'InfraMtn.Task',
            store: App.InfraMtn.Task.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.InfraMtn.Task.Edit(record);
                },
                'beforerender': function() {
                    App.InfraMtn.Task.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'mtn_node_task_name',
                    header: App.Language.General.task_name,
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
                        w = new App.InfraMtn.Task.formWindow();
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
                        grid = Ext.getCmp('InfraMtn.Task');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraMtn.Task.Store.remove(record);
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
            title: App.Language.General.list_price,
            id: 'App.InfraMtn.NodePriceList.Store',
            store: App.InfraMtn.NodePriceList.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    node_price_list_id = record.data.mtn_node_price_list_id;
                    new App.InfraMtn.NodePriceList.editWindow({
                        mtn_node_price_list_id: node_price_list_id
                    }).show();
                },
                'beforerender': function() {
                    App.InfraMtn.NodePriceList.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'mtn_node_price_list_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'mtn_node_price_list_description',
                    header: App.Language.General.description,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'node_id',
                    header: App.Language.General.node,
                    sortable: true,
                    width: 100,
                    renderer: function(Node) {
                        return Node.node_name;
                    }
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.InfraMtn.NodePriceList.addWindow();
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
                        grid = Ext.getCmp('App.InfraMtn.NodePriceList.Store');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraMtn.NodePriceList.Store.remove(record);
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
            title: App.Language.General.applicant,
            id: 'App.InfraMtn.Applicant',
            store: App.InfraMtn.Applicant.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.InfraMtn.Applicant.Edit(record);
                },
                'beforerender': function() {
                    App.InfraMtn.Applicant.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'applicant_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'applicant_status',
                    header: App.Language.General.asset,
                    sortable: true,
                    width: 100,
                    renderer: function(applicant_status) {
                        return (applicant_status == 1) ? 'Si' : 'No';
                    }
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.InfraMtn.Applicant.formWindow();
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
                        grid = Ext.getCmp('App.InfraMtn.Applicant');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraMtn.Applicant.Store.remove(record);
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
            title: App.Language.General.responsible,
            id: 'App.InfraMtn.Responsible',
            store: App.InfraMtn.Responsible.Store,
            height: 900,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.InfraMtn.Responsible.Edit(record);
                },
                'beforerender': function() {
                    App.InfraMtn.Responsible.Store.load();
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'responsible_name',
                    header: App.Language.General.name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'responsible_status',
                    header: App.Language.General.asset,
                    sortable: true,
                    width: 100,
                    renderer: function(responsible_status) {
                        return (responsible_status == 1) ? 'Si' : 'No';
                    }
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.InfraMtn.Responsible.FormWindow();
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
                        grid = Ext.getCmp('App.InfraMtn.Responsible');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.InfraMtn.Responsible.Store.remove(record);
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
            title: App.Language.General.state_ot,
            store: App.Mtn.PossibleStatus.Store,
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
                    App.Mtn.PossibleStatus.Store.load();
                }
            },
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Maintainers.addStateWoWindow();
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
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Mtn.PossibleStatus.Store.remove(record);
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
            id: 'App.Maintainers.TypeWO',
            store: App.Mtn.WoTypesAll.Store,
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
                    App.Mtn.WoTypesAll.Store.load();
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
                        w = new App.Maintainers.addTypeWoWindow();
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
                        grid = Ext.getCmp('App.Maintainers.TypeWO');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Mtn.WoTypesAll.Store.remove(record);
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
                        grid = Ext.getCmp('App.Maintainers.TypeWO');
                        if (grid.getSelectionModel().getCount()) {
                            mtn_work_order_type_id = grid.getSelectionModel().getSelected().id;
                            App.Mtn.ConfigStateAsociadosAll.Store.setBaseParam('mtn_work_order_type_id', mtn_work_order_type_id);
                            w = new App.Maintainers.StateConfigWOWindow();
                            w.show();
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
                        }
                    }
                }]
            }
        }];
        App.Maintainers.InfraMtn.Principal.superclass.initComponent.call(this);
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


App.InfraMtn.NodePlan.FormWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_plan,
    resizable: false,
    modal: true,
    width: 380,
    height: 280,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            plugins: [new Ext.ux.OOSubmit()],
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'mtn_node_plan_name',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'mtn_node_plan_description',
                anchor: '100%'
            }, {
                xtype: 'datefield',
                fieldLabel: App.Language.General.start_date,
                anchor: '100%',
                name: 'mtn_node_plan_date_begin',
                allowBlank: false
            }, {
                xtype: 'datefield',
                fieldLabel: App.Language.General.end_date,
                anchor: '100%',
                name: 'mtn_node_plan_date_finish',
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
                            url: 'index.php/inframtn/nodeplan/add',
                            success: function(fp, o) {
                                App.InfraMtn.NodePlan.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                new App.InfraMtn.NodePlan.Edit({
                                    mtn_node_plan_id: o.result.mtn_node_plan_id
                                }).show();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.InfraMtn.NodePlan.FormWindow.superclass.initComponent.call(this);
    }
});

App.InfraMtn.NodePlan.Edit = Ext.extend(Ext.Window, {
    title: App.Language.General.detail_plan,
    layout: 'fit',
    width: 650,
    height: 400,
    modal: true,
    resizable: false,
    listeners: {
        'beforerender': function(w) {
            mtn_node_plan_id = w.mtn_node_plan_id;

            //Cargar grid de Tareas
            //App.InfraMtn.NodePlanTask.Store.removeAll(true);
            App.InfraMtn.NodePlanTask.Store.setBaseParam('mtn_node_plan_id', w.mtn_node_plan_id);
            App.InfraMtn.NodePlanTask.Store.load();
            w.tab.form.getForm().load({
                url: 'index.php/inframtn/nodeplan/getOne',
                params: {
                    mtn_node_plan_id: mtn_node_plan_id
                },
                success: function(form, action) {

                    w.tab.tareas.setDisabled(false);
                    w.tab.regusu.setDisabled(false);
                    w.closeButton.show();

                    App.InfraMtn.NodePlan.plan = action.result.data.mtn_node_plan_id;
                    mtn_node_plan_status = action.result.data.mtn_node_plan_status;

                    if (mtn_node_plan_status == 0) {
                        w.closeButton.hide();
                        w.saveButton.hide();
                        w.doLayout();
                    }
                },
                failure: function(form, action) {
                    Ext.Msg.alert("Load failed", action.result.errorMessage);
                },
                waitMsg: 'cargando...'
            });
        }
    },
    initComponent: function() {
        var otDocumentos = new Ext.grid.CheckboxSelectionModel({
            singleSelect: true
        });
        this.items = [{
            xtype: 'tabpanel',
            activeTab: 0,
            border: false,
            ref: 'tab',
            items: [{
                xtype: 'form',
                ref: 'form',
                title: App.Language.General.details,
                margins: '5 20 5 5',
                border: false,
                frame: false,
                plugins: [new Ext.ux.OOSubmit()],
                labelWidth: 150,
                padding: 5,
                items: [{
                    xtype: 'hidden',
                    id: 'node_plan_id',
                    fieldLabel: 'mtn_node_plan_id',
                    name: 'mtn_node_plan_id',
                    allowBlank: false
                }, {
                    xtype: 'textfield',
                    fieldLabel: App.Language.General.name,
                    name: 'mtn_node_plan_name',
                    anchor: '100%',
                    allowBlank: false
                }, {
                    xtype: 'textarea',
                    fieldLabel: App.Language.General.description,
                    name: 'mtn_node_plan_description',
                    anchor: '100%'
                }, {
                    xtype: 'datefield',
                    fieldLabel: App.Language.General.start_date,
                    anchor: '100%',
                    name: 'mtn_node_plan_date_begin',
                    allowBlank: false
                }, {
                    xtype: 'datefield',
                    fieldLabel: App.Language.General.end_date,
                    anchor: '100%',
                    name: 'mtn_node_plan_date_finish',
                    allowBlank: false
                }, {
                    xtype: 'displayfield',
                    fieldLabel: App.Language.General.total,
                    anchor: '100%',
                    name: 'mtn_node_plan_total',
                    allowBlank: false
                }],
            }, {
                xtype: 'grid',
                ref: 'tareas',
                id: 'WoTask',
                disabled: true,
                title: App.Language.General.tasks,
                region: 'center',
                collapsible: false,
                collapseMode: 'mini',
                enableTabScroll: true,
                store: App.InfraMtn.NodePlanTask.Store,
                loadMask: true,
                sm: otDocumentos,
                viewConfig: {
                    forceFit: true
                },
                tbar: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function(b) {

                        new App.InfraMtn.NodePlanTask.taskWindow({
                            node_plan_id: Ext.getCmp('node_plan_id').getValue()
                        }).show();
                    }
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {

                        grid = Ext.getCmp('WoTask');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.remove_your_task,
                                function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.InfraMtn.NodePlanTask.Store.remove(record);
                                            App.InfraMtn.NodePlanTask.Store.setBaseParam('mtn_node_plan_id', App.InfraMtn.NodePlan.plan);
                                            App.InfraMtn.NodePlanTask.Store.load();

                                            App.InfraMtn.NodePlan.Store.setBaseParam('mtn_node_plan_id', App.InfraMtn.NodePlan.plan);
                                            App.InfraMtn.NodePlan.Store.load();
                                        });
                                    }
                                });
                        } else {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_select_at_least_one_record_to_delete);
                        }

                    }
                }],
                margins: '0 5 5 5',
                columns: [otDocumentos, {
                    dataIndex: 'mtn_node_task_name',
                    header: App.Language.General.task,
                    width: 20
                }, {
                    dataIndex: 'mtn_node_plan_task_time_job',
                    header: App.Language.General.time,
                    width: 15
                }, {
                    dataIndex: 'mtn_node_plan_task_price',
                    header: App.Language.General.price,
                    width: 15
                }, {
                    dataIndex: 'mtn_node_plan_task_comment',
                    header: App.Language.General.commentary,
                    width: 30
                }]
            }, {
                xtype: 'grid',
                ref: 'regusu',
                id: 'WoReg',
                disabled: true,
                title: App.Language.General.rrecord,
                region: 'center',
                collapsible: false,
                collapseMode: 'mini',
                enableTabScroll: true,
                store: App.InfraMtn.NodePlan.Store,
                loadMask: true,
                sm: otDocumentos,
                viewConfig: {
                    forceFit: true
                },
                margins: '0 5 5 5',
                columns: [otDocumentos, {
                    dataIndex: 'mtn_node_task_name',
                    header: App.Language.General.task,
                    width: 20
                }, {
                    dataIndex: 'mtn_node_plan_task_time_job',
                    header: App.Language.General.time,
                    width: 15
                }, {
                    dataIndex: 'mtn_node_plant_task_price',
                    header: App.Language.General.price,
                    width: 15
                }, {
                    dataIndex: 'mtn_node_plan_task_comment',
                    header: App.Language.General.commentary,
                    width: 30
                }]
            }]
        }];
        this.buttons = [{
            xtype: 'button',
            id: 'App.Mtn.Wo.TbarPrintIcon',
            text: App.Language.General.printer,
            iconCls: 'print_icon',
            handler: function() {
                document.location = 'index.php/inframtn/nodeplan/imprimir/' + App.InfraMtn.NodePlan.plan;
            }
        }, {
            text: App.Language.General.close,
            ref: '../closeButton',
            handler: function(b) {

                b.ownerCt.ownerCt.hide();

            }
        }, {
            text: App.Language.General.save,
            ref: '../saveButton',
            handler: function(b) {
                w = b.ownerCt.ownerCt;
                w.tab.form.getForm().submit({
                    url: 'index.php/inframtn/nodeplan/update',
                    params: {
                        orden_trabajo_estado: 1
                    },
                    success: function() {
                        w.hide();
                        App.InfraMtn.NodePlan.Store.load();
                    },
                    failure: function(fp, o) {
                        alert('Error:\n' + o.result.msg);
                    }
                });
            }
        }];
        App.InfraMtn.NodePlan.Edit.superclass.initComponent.call(this);
    }
});

App.InfraMtn.NodePlanTask.taskWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.new_task,
    width: 400,
    height: 250,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'beforerender': function(w) {
            App.InfraMtn.NodePlan.plan = w.node_plan_id;

        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            id: 'App.NodePlanTask',
            fileUpload: true,
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.task,
                anchor: '95%',
                selecOnFocus: true,
                typeAhead: true,
                forceSelection: true,
                triggerAction: 'all',
                store: App.InfraMtn.Task.Store,
                hiddenName: 'mtn_node_task_id',
                displayField: 'mtn_node_task_name',
                valueField: 'mtn_node_task_id',
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.General.working_time,
                minValue: 0,
                anchor: '100%',
                allowBlank: true,
                name: 'mtn_node_plan_task_time_job'
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.General.price,
                minValue: 0,
                anchor: '100%',
                allowBlank: true,
                name: 'mtn_node_plan_task_price'
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.comment,
                anchor: '100%',
                allowBlank: false,
                name: 'mtn_node_plan_task_comment'
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.save,
                handler: function(b) {
                    form = Ext.getCmp('App.NodePlanTask').getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/inframtn/nodeplantask/add',
                            params: {
                                mtn_node_plan_id: App.InfraMtn.NodePlan.plan
                            },
                            waitMsg: 'Espere...',
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.InfraMtn.NodePlanTask.Store.setBaseParam('mtn_node_plan_id', o.result.mtn_node_plan_id);
                                App.InfraMtn.NodePlanTask.Store.load();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.InfraMtn.NodePlanTask.taskWindow.superclass.initComponent.call(this);
    }
});

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
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
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

App.InfraMtn.NodePriceList.addWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.list_price,
    width: 400,
    height: 250,
    layout: 'fit',
    padding: 1,
    modal: true,
    resizable: false,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            plugins: [new Ext.ux.OOSubmit()],
            padding: 5,
            items: [{
                xtype: 'combo',
                hiddenName: 'node_id',
                fieldLabel: App.Language.General.node,
                store: App.InfraStructure.Node.Store,
                displayField: 'node_name',
                valueField: 'node_id',
                editable: false,
                mode: 'remote',
                minChars: 0,
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textfield',
                name: 'mtn_node_price_list_name',
                fieldLabel: App.Language.General.name,
                anchor: '100%'
            }, {
                xtype: 'textarea',
                name: 'mtn_node_price_list_description',
                fieldLabel: App.Language.General.description,
                anchor: '100%'
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                handler: function(b) {

                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/inframtn/nodepricelist/add',
                            waitMsg: App.Language.General.please_wait,
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.hide();
                                App.InfraMtn.NodePriceList.Store.load();
                                new App.InfraMtn.NodePriceList.editWindow({
                                    mtn_node_price_list_id: o.result.mtn_node_price_list_id
                                }).show();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }

            }]
        }];
        App.InfraMtn.NodePriceList.addWindow.superclass.initComponent.call(this);
    }
});


App.InfraMtn.NodePriceList.editWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.edit_list,
    width: 700,
    height: 550,
    layout: 'fit',
    id: 'addDetailWindow',
    modal: true,
    resizable: false,
    listeners: {
        'beforerender': function(w) {
            w.tab.form.getForm().load({
                url: 'index.php/inframtn/nodepricelist/getOne',
                params: {
                    mtn_node_price_list_id: w.mtn_node_price_list_id
                },
                success: function(form, action) {
                    viatico_id = action.result.data.viatico_id;
                    App.InfraMtn.NodePriceListTask.Store.setBaseParam('mtn_node_price_list_id', w.mtn_node_price_list_id);
                    App.InfraMtn.NodePriceListTask.Store.load();
                },
                failure: function(form, action) {
                    Ext.Msg.alert("Load failed", action.result.errorMessage);
                },
                waitMsg: App.Language.Core.loading
            });
        },
        listeners: {
            'close': function() {
                App.InfraMtn.NodePriceList.setBaseParam('start_date', new Date().add(Date.DAY, -30).format('Y-m-d'));
                App.InfraMtn.NodePriceList.reload();
            }
        },
    },
    initComponent: function() {
        var otDocumentos = new Ext.grid.CheckboxSelectionModel({
            singleSelect: true
        });
        this.items = [{
            xtype: 'tabpanel',
            activeTab: 0,
            border: false,
            ref: 'tab',
            items: [{
                xtype: 'form',
                ref: 'form',
                title: App.Language.General.details,
                margins: '5 20 5 5',
                border: false,
                frame: false,
                plugins: [new Ext.ux.OOSubmit()],
                labelWidth: 150,
                padding: 5,
                items: [{
                    xtype: 'fieldset',
                    title: App.Language.General.general,
                    items: [{
                        xtype: 'hidden',
                        id: 'InfraMtn.NodePriceList.Id',
                        fieldLabel: 'mtn_node_price_list_id',
                        name: 'mtn_node_price_list_id',
                        allowBlank: false
                    }, {
                        xtype: 'combo',
                        fieldLabel: App.Language.General.node,
                        anchor: '100%',
                        selecOnFocus: true,
                        typeAhead: true,
                        forceSelection: true,
                        triggerAction: 'all',
                        store: App.InfraStructure.Node.Store,
                        hiddenName: 'node_id',
                        displayField: 'node_name',
                        valueField: 'node_id',
                        mode: 'remote',
                        minChars: 0
                    }, {
                        xtype: 'textfield',
                        name: 'mtn_node_price_list_name',
                        fieldLabel: App.Language.General.name,
                        anchor: '100%'
                    }]
                }, {
                    xtype: 'fieldset',
                    title: App.Language.General.description,
                    items: [{
                        xtype: 'textarea',
                        name: 'mtn_node_price_list_description',
                        hideLabel: true,
                        anchor: '100%'
                    }]
                }]
            }, {
                xtype: 'grid',
                title: App.Language.General.associated_tasks,
                ref: 'mtn_node_list_task',
                id: 'mtn_node_list_task',
                region: 'center',
                collapsible: false,
                collapseMode: 'mini',
                enableTabScroll: true,
                store: App.InfraMtn.NodePriceListTask.Store,
                loadMask: true,
                sm: otDocumentos,
                viewConfig: {
                    forceFit: true
                },
                tbar: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function(b) {

                        new App.InfraMtn.NodePriceList.addTask({
                            mtn_node_price_list_id: Ext.getCmp('InfraMtn.NodePriceList.Id').getValue()
                        }).show();
                    }
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {

                        grid = Ext.getCmp('mtn_node_list_task');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.remove_your_task,
                                function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.InfraMtn.NodePriceListTask.Store.remove(record);
                                        });
                                    }
                                });
                        } else {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_select_at_least_one_record_to_delete);
                        }

                    }
                }],
                margins: '0 5 5 5',
                columns: [otDocumentos,
                    {
                        dataIndex: 'mtn_node_task_name',
                        header: App.Language.General.name,
                        width: 20
                    }, {
                        dataIndex: 'measure_unit_name',
                        header: App.Language.General.measure,
                        width: 15
                    }, {
                        dataIndex: 'mtn_node_price_list_task_value',
                        header: App.Language.General.price,
                        width: 12
                    }
                ]
            }, {
                xtype: 'grid',
                title: App.Language.General.providers,
                ref: 'mtn_node_prov',
                id: 'mtn_node_prov',
                region: 'center',
                collapsible: false,
                collapseMode: 'mini',
                enableTabScroll: true,
                store: App.InfraMtn.NodePriceListTask.Store,
                loadMask: true,
                sm: otDocumentos,
                viewConfig: {
                    forceFit: true
                },
                tbar: [{
                    text: App.Language.General.add,
                    iconCls: 'add_icon',
                    handler: function(b) {

                        new App.InfraMtn.NodePriceList.addTask({
                            mtn_node_price_list_id: Ext.getCmp('InfraMtn.NodePriceList.Id').getValue()
                        }).show();
                    }
                }, {
                    xtype: 'button',
                    text: App.Language.General.ddelete,
                    iconCls: 'delete_icon',
                    handler: function(b) {
                        grid = Ext.getCmp('mtn_node_list_task');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.remove_your_task,
                                function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.InfraMtn.NodePriceListTask.Store.remove(record);
                                        });
                                    }
                                });
                        } else {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_select_at_least_one_record_to_delete);
                        }

                    }
                }],
                margins: '0 5 5 5',
                columns: [otDocumentos,
                    {
                        dataIndex: 'mtn_node_task_name',
                        header: App.Language.General.name,
                        width: 20
                    }, {
                        dataIndex: 'measure_unit_name',
                        header: App.Language.General.measure,
                        width: 15
                    }, {
                        dataIndex: 'mtn_node_price_list_task_value',
                        header: App.Language.General.price,
                        width: 12
                    }
                ]
            }]
        }];
        this.fbar = [{
            xtype: 'button',
            id: 'App.Mtn.Wo.TbarPrintIcon',
            text: App.Language.General.printer,
            iconCls: 'print_icon',
            handler: function() {
                document.location = 'index.php/inframtn/nodepricelist/imprimir/' + App.InfraMtn.Wo.order;
            }
        }, {
            text: App.Language.General.close,
            ref: '../closeButton',
            handler: function(b) {

                b.ownerCt.ownerCt.hide();

            }
        }, {
            text: App.Language.General.save,
            ref: '../saveButton',
            handler: function(b) {
                w = b.ownerCt.ownerCt;
                w.tab.form.getForm().submit({
                    url: 'index.php/inframtn/nodepricelist/update',
                    success: function() {
                        w.hide();
                        App.InfraMtn.NodePriceList.Store.load();
                    },
                    failure: function(fp, o) {
                        alert('Error:\n' + o.result.msg);
                    }
                });
            }
        }];
        App.InfraMtn.NodePriceList.editWindow.superclass.initComponent.call(this);
    }
});


App.InfraMtn.NodePriceList.addTask = Ext.extend(Ext.Window, {
    title: App.Language.General.associating_tasks,
    width: 400,
    height: 300,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'beforerender': function(w) {
            App.InfraMtn.NodePriceList.Id = w.mtn_node_price_list_id;

        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            id: 'App.InfraMtn.NodePriceList.Form',
            fileUpload: true,
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'combo',
                fieldLabel: App.Language.General.task,
                anchor: '100%',
                selecOnFocus: true,
                typeAhead: true,
                forceSelection: true,
                triggerAction: 'all',
                store: App.InfraMtn.Task.Store,
                hiddenName: 'mtn_node_task_id',
                displayField: 'mtn_node_task_name',
                valueField: 'mtn_node_task_id',
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.measure,
                anchor: '100%',
                selecOnFocus: true,
                typeAhead: true,
                forceSelection: true,
                triggerAction: 'all',
                store: App.Core.MeasureUnit.Store,
                hiddenName: 'measure_unit_id',
                displayField: 'measure_unit_name',
                valueField: 'measure_unit_id',
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.General.price,
                minValue: 0,
                anchor: '100%',
                allowBlank: true,
                name: 'mtn_node_price_list_task_value'
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.save,
                handler: function(b) {
                    form = Ext.getCmp('App.InfraMtn.NodePriceList.Form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/inframtn/nodepricelistTask/add',
                            params: {
                                mtn_node_price_list_id: App.InfraMtn.NodePriceList.Id
                            },
                            waitMsg: App.Language.General.please_wait,
                            success: function(fp, o) {
                                b.ownerCt.ownerCt.ownerCt.close();
                                App.InfraMtn.NodePriceListTask.Store.setBaseParam('mtn_node_price_list_id', App.InfraMtn.NodePriceList.Id);
                                App.InfraMtn.NodePriceListTask.Store.load();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.InfraMtn.NodePriceList.addTask.superclass.initComponent.call(this);
    }
});


App.Maintainers.TypeComponentConfigEditMode = function(record) {
    w = new App.Maintainers.addTypeComponentWindow({
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

App.InfraMtn.Applicant.formWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.new_applicant,
    resizable: false,
    modal: true,
    width: 420,
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
                name: 'applicant_name',
                anchor: '100%',
                minChars: 0,
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
                            url: 'index.php/inframtn/applicant/add',
                            success: function(fp, o) {
                                App.InfraMtn.Applicant.Store.load();
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
        App.InfraMtn.Applicant.formWindow.superclass.initComponent.call(this);
    }
});

App.InfraMtn.Applicant.Edit = function(record) {
    w = new App.InfraMtn.Applicant.formWindow({
        title: App.Language.General.edit_applicant
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            App.InfraMtn.Applicant.Store.load();
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.InfraMtn.Task.formWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.tasks,
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
                name: 'mtn_node_task_name',
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
                            url: 'index.php/inframtn/task/add',
                            success: function(fp, o) {
                                App.InfraMtn.Task.Store.load();
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
        App.InfraMtn.Task.formWindow.superclass.initComponent.call(this);
    }
});

App.InfraMtn.Task.Edit = function(record) {
    w = new App.InfraMtn.Task.formWindow({
        title: App.Language.Maintenance.edit_other_costs
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.InfraMtn.Task.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.InfraMtn.Responsible.FormWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.new_manager,
    resizable: false,
    modal: true,
    width: 400,
    height: 150,
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
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'responsible_name',
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
                            url: 'index.php/inframtn/responsible/add',
                            success: function(fp, o) {
                                App.InfraMtn.Responsible.Store.load();
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
        App.InfraMtn.Responsible.FormWindow.superclass.initComponent.call(this);
    }
});

App.InfraMtn.Responsible.Edit = function(record) {
    w = new App.InfraMtn.Responsible.FormWindow({
        title: App.Language.General.edit_responsible
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.InfraMtn.Responsible.Store.load();
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
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
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