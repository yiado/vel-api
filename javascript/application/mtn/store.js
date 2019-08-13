App.Mtn.WoNodeProvider.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/node/getByNodeProviderTotal'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'provider_id',
    fields: [
        'provider_id',
        'provider_name'
    ]
});

App.Mtn.WoNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wo/getNode',
            create: 'index.php/mtn/wo/addCorrectiveNode',
            update: 'index.php/mtn/wo/updateNode',
            destroy: 'index.php/mtn/wo/deleteNode'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_id',
    fields: [
        'mtn_work_order_id',
        'mtn_work_order_folio',
        'mtn_work_order_requested_by',
        'mtn_work_order_type_id',
        'asset_id',
        'total_task',
        'total_other_costs',
        'total_work_order',
        'provider_id',
        'mtn_work_order_comment',
        'mtn_work_order_status',
        'node_ruta',
        {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        }, {
            type: 'date',
            name: 'mtn_work_order_date',
            dateFormat: 'Y-m-d'
        }, {
            name: 'mtn_work_order_type_name',
            mapping: 'MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name'
        }, {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }
    ],
    listeners: {
        'save': function() {
            this.load({ params: { start: 0, limit: App.GridLimitNumOT } });
        }
    }
});

App.Mtn.Wo.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wo/get',
            create: 'index.php/mtn/wo/addCorrective',
            update: 'index.php/mtn/wo/update',
            destroy: 'index.php/mtn/wo/delete'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_id',
    fields: [
        'mtn_work_order_id',
        'mtn_work_order_folio',
        'mtn_work_order_requested_by',
        'mtn_work_order_type_id',
        'asset_id',
        'total_task',
        'total_other_costs',
        'total_work_order',
        'provider_id',
        'mtn_work_order_comment',
        'mtn_work_order_status',
        'Asset',
        {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        }, {
            type: 'date',
            name: 'mtn_work_order_date',
            dateFormat: 'Y-m-d'
        }, {
            name: 'asset_name',
            mapping: 'Asset.asset_name'
        }, {
            name: 'asset_path',
            mapping: 'Asset.asset_path'
        }, {
            name: 'mtn_work_order_type_name',
            mapping: 'MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name'
        }, {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }
    ]
});

App.Mtn.WoStateForm.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            update: 'index.php/mtn/wo/updateState'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_id',
    fields: [
        'mtn_work_order_id',
        'mtn_status_log_comments',
        'mtn_system_work_order_status_name',
        'mtn_work_order_type_name'
    ]
});

App.Mtn.WoProvider.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wo/getProvider'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_id',
    fields: [
        'mtn_work_order_id',
        'mtn_work_order_folio',
        'mtn_work_order_requested_by',
        'mtn_work_order_type_id',
        'node_ruta',
        'total_task',
        'total_other_costs',
        'total_work_order',
        'provider_id',
        'mtn_work_order_comment',
        'mtn_work_order_status',
        //        'Asset', 
        {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        },
        {
            type: 'date',
            name: 'mtn_work_order_date',
            dateFormat: 'Y-m-d'
        }, {
            type: 'date',
            name: 'mtn_date_finish',
            dateFormat: 'Y-m-d'
        },
        //        {
        //            name: 'asset_name',
        //            mapping: 'Asset.asset_name'
        //        }, 
        //        {
        //            name: 'asset_path',
        //            mapping: 'Asset.asset_path'
        //        }, 
        {
            name: 'mtn_work_order_type_name',
            mapping: 'MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name'
        }, {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }
    ]
});

App.Mtn.PossibleStatus.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/posstatus/get',
            create: 'index.php/mtn/posstatus/add',
            update: 'index.php/mtn/posstatus/update',
            destroy: 'index.php/mtn/posstatus/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    idProperty: 'mtn_system_work_order_status_id',
    totalProperty: 'total',
    fields: [
        'mtn_system_work_order_status_id',
        'mtn_system_work_order_status_name'
    ]
});

App.Mtn.PossibleStatusByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/posstatus/getByNode',
            create: 'index.php/mtn/posstatus/addByNode',
            update: 'index.php/mtn/posstatus/update',
            destroy: 'index.php/mtn/posstatus/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'mtn_system_work_order_status_id',
    totalProperty: 'total',
    fields: [
        'mtn_system_work_order_status_id',
        'mtn_system_work_order_status_name'
    ]
});

App.Mtn.StateAssigned.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/posstatus/stateAssigned'
        }
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'mtn_system_work_order_status_id',
    totalProperty: 'total',
    fields: [
        'mtn_system_work_order_status_id',
        {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        }
    ]
});

App.Mtn.WoTypes.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotype/get',
            create: 'index.php/mtn/wotype/add',
            update: 'index.php/mtn/wotype/update',
            destroy: 'index.php/mtn/wotype/delete'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_type_id',
    fields: [
        'mtn_work_order_type_id',
        'mtn_work_order_type_name',
        'mtn_work_order_type_duration'
    ]
});


App.Mtn.WoTypesPreventive.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotype/getPreventive'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_type_id',
    fields: [
        'mtn_work_order_type_id',
        'mtn_work_order_type_name',
        'mtn_work_order_type_duration'
    ]
});

App.Mtn.OtherCosts.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/othercosts/get',
            create: 'index.php/mtn/othercosts/add',
            update: 'index.php/mtn/othercosts/update',
            destroy: 'index.php/mtn/othercosts/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_other_costs_id',
    fields: [
        'mtn_other_costs_id',
        'mtn_other_costs_name'
    ]
});

App.Mtn.OtherCostsByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/othercosts/getByNode',
            create: 'index.php/mtn/othercosts/addByNode',
            update: 'index.php/mtn/othercosts/update',
            destroy: 'index.php/mtn/othercosts/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_other_costs_id',
    fields: [
        'mtn_other_costs_id',
        'mtn_other_costs_name'
    ]
});

App.Mtn.OtherCostsWo.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/woothercosts/get',
            create: 'index.php/mtn/woothercosts/add',
            update: 'index.php/mtn/woothercosts/update',
            destroy: 'index.php/mtn/woothercosts/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    listeners: {
        'beforeload': function() {
            App.Mtn.OtherCosts.Total = 0;
        }
    },
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_other_costs_id',
    fields: [
        'mtn_work_order_other_costs_id',
        'mtn_work_order_other_costs_costs',
        'mtn_work_order_other_costs_comment',
        'mtn_other_costs_id',
        {
            name: 'mtn_other_costs_name',
            mapping: 'MtnOtherCosts.mtn_other_costs_name'
        }
    ],
    sortInfo: {
        field: 'mtn_work_order_other_costs_costs',
        direction: 'DESC'
    }
});

App.Mtn.Task.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/task/get',
            create: 'index.php/mtn/task/add',
            update: 'index.php/mtn/task/update',
            destroy: 'index.php/mtn/task/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_task_id',
    fields: [
        'mtn_task_id',
        'mtn_task_time',
        'mtn_task_name'
    ]
});

App.Mtn.Task.StoreGrid = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/task/get',
            create: 'index.php/mtn/task/add',
            update: 'index.php/mtn/task/update',
            destroy: 'index.php/mtn/task/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_task_id',
    fields: [
        'mtn_task_id',
        'mtn_task_time',
        'mtn_task_name'
    ]
});

App.Mtn.TaskByNode.StoreGrid = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/task/getByNode',
            create: 'index.php/mtn/task/addByNode',
            update: 'index.php/mtn/task/update',
            destroy: 'index.php/mtn/task/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_task_id',
    fields: [
        'mtn_task_id',
        'mtn_task_time',
        'mtn_task_name'
    ]
});

App.Mtn.WoTask.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotask/get',
            create: 'index.php/mtn/wotask/add',
            update: 'index.php/mtn/wotask/update',
            destroy: 'index.php/mtn/wotask/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    listeners: {
        'beforeload': function() {
            App.Mtn.WoTask.Total = 0;
        },
        'datachanged': function() {
            App.Mtn.WoTask.Total = 0;
        }
    },
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_task_id',
    fields: [
        'mtn_work_order_task_id',
        'mtn_work_order_task_time_job',
        'mtn_work_order_task_comment',
        'mtn_amount_component_in_task',
        'mtn_task_id',
        'MtnTask',
        'Currency',
        'currency_id',
        'MtnWorkOrderTaskComponent',
        'mtn_work_order_component_price',
        'mtn_work_order_task_price',
        'mtn_costos_component_in_task'
    ],
    sortInfo: {
        field: 'mtn_work_order_task_price',
        direction: 'DESC'
    }
});

App.Mtn.WoTaskComponent.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotaskcomponent/get',
            create: 'index.php/mtn/wotaskcomponent/add',
            update: 'index.php/mtn/wotaskcomponent/update',
            destroy: 'index.php/mtn/wotaskcomponent/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_task_component_id',
    fields: [
        'mtn_work_order_task_component_id',
        'mtn_work_order_component_amount',
        {
            name: 'mtn_component_name',
            convert: function componentNameWithType(v, record) {
                //                return (record.MtnPriceListComponent != null ? record.MtnPriceListComponent.MtnComponent.mtn_component_name + ' / ' + record.MtnPriceListComponent.MtnComponent.MtnComponentType.mtn_component_type_name : record.MtnComponent.mtn_component_name + ' / ' + record.MtnComponent.MtnComponentType.mtn_component_type_name);
                return (record.MtnPriceListComponent != null ? record.MtnPriceListComponent.MtnComponent.mtn_component_name + ' -> ' + record.MtnPriceListComponent.MtnComponent.MeasureUnit.measure_unit_name + ' (' + record.MtnPriceListComponent.MtnComponent.MeasureUnit.measure_unit_description + ')' + ' / ' + record.MtnPriceListComponent.MtnComponent.MtnComponentType.mtn_component_type_name : record.MtnComponent.mtn_component_name + ' -> ' + record.MtnComponent.MeasureUnit.measure_unit_name + ' (' + record.MtnComponent.MeasureUnit.measure_unit_description + ')' + ' / ' + record.MtnComponent.MtnComponentType.mtn_component_type_name);
            }
        }, {
            name: 'mtn_work_order_task_component_price',
            convert: function componentPrice(v, record) {
                return (record.MtnPriceListComponent != null ? record.MtnPriceListComponent.mtn_price_list_component_price : record.mtn_work_order_component_price);
            }
        }
    ]
});

App.Mtn.Component.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/component/get',
            create: 'index.php/mtn/component/add',
            update: 'index.php/mtn/component/update',
            destroy: 'index.php/mtn/component/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_id',
    fields: [
        'mtn_component_id',
        'mtn_component_type_id',
        {
            name: 'mtn_component_type_name',
            mapping: 'MtnComponentType.mtn_component_type_name'
        },
        'brand_id',
        'Brand',
        'mtn_component_name',
        'mtn_component_weight',
        'mtn_component_model',
        'mtn_component_manufacturer',
        'mtn_component_comment'
    ]
});

App.Mtn.ComponentByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/component/getByNode',
            create: 'index.php/mtn/component/addByNode',
            update: 'index.php/mtn/component/update',
            destroy: 'index.php/mtn/component/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_id',
    fields: [
        'mtn_component_id',
        'mtn_component_type_id',
        {
            name: 'mtn_component_type_name',
            mapping: 'MtnComponentType.mtn_component_type_name'
        },
        'brand_id',
        'Brand',
        'MeasureUnit',
        {
            name: 'measure_unit_name_and_description',
            convert: function stringMeasure(v, record) {
                return record.MeasureUnit.measure_unit_name + ' (' + record.MeasureUnit.measure_unit_description + ')';
            }
        },
        {
            name: 'component_name_and_description',
            convert: function stringComponentAndUnit(v, record) {
                return record.mtn_component_name + ' -> ' + record.MeasureUnit.measure_unit_name + ' (' + record.MeasureUnit.measure_unit_description + ')';
            }
        },
        'measure_unit_id',
        'mtn_component_name',
        'mtn_component_weight',
        'mtn_component_model',
        'mtn_component_manufacturer',
        'mtn_component_comment'
    ]
});

App.Mtn.TypesComponent.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/typecomponent/get',
            create: 'index.php/mtn/typecomponent/add',
            update: 'index.php/mtn/typecomponent/update',
            destroy: 'index.php/mtn/typecomponent/delete'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_type_id',
    fields: [
        'mtn_component_type_id',
        'mtn_component_type_name'
    ]
});

App.Mtn.TypesComponentByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/typecomponent/getByNode',
            create: 'index.php/mtn/typecomponent/add',
            update: 'index.php/mtn/typecomponent/update',
            destroy: 'index.php/mtn/typecomponent/delete'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_type_id',
    fields: [
        'mtn_component_type_id',
        'mtn_component_type_name'
    ]
});

App.Mtn.PriceListComponent.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/pricelistcomponent/get',
            create: 'index.php/mtn/pricelistcomponent/add',
            update: 'index.php/mtn/pricelistcomponent/update',
            destroy: 'index.php/mtn/pricelistcomponent/delete'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_id',
    fields: [
        'mtn_component_id',
        'mtn_component_name',
        'MtnPriceListComponent',
        {
            name: 'mtn_price_list_component_id',
            convert: function priceListId(v, record) {
                return (record.MtnPriceListComponent[0] == null ? record.mtn_component_id : record.MtnPriceListComponent[0].mtn_price_list_component_id);
            }
        }, {
            name: 'mtn_component_with_type',
            convert: function componentWithType(v, record) {
                return record.mtn_component_name + ' / ' + record.MtnComponentType.mtn_component_type_name;
            }
        }
    ]
});

App.Mtn.FlowWo.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/woflow/get',
            create: 'index.php/mtn/woflow/add',
            update: 'index.php/mtn/woflow/update',
            destroy: 'index.php/mtn/woflow/delete'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_status_id',
    fields: [
        'mtn_work_order_status_id',
        'mtn_work_order_status_name',
        {
            type: 'date',
            name: 'mtn_work_order_status_date_start',
            dateFormat: 'Y-m-d'
        }, {
            type: 'date',
            name: 'mtn_work_order_status_date_finish',
            dateFormat: 'Y-m-d'
        },
        'mtn_work_order_status_status'
    ]
});

App.Mtn.Plan.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/plan/get',
            create: 'index.php/mtn/plan/add',
            update: 'index.php/mtn/plan/update',
            destroy: 'index.php/mtn/plan/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_plan_id',
    fields: [
        'mtn_plan_id',
        'mtn_plan_name',
        'mtn_plan_description'
    ]
});

App.Mtn.PlanByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/plan/getByNode',
            create: 'index.php/mtn/plan/addByNode',
            update: 'index.php/mtn/plan/update',
            destroy: 'index.php/mtn/plan/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_plan_id',
    fields: [
        'mtn_plan_id',
        'mtn_plan_name',
        'mtn_plan_description'
    ]
});

App.Mtn.PlanTask.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/plantask/get',
            create: 'index.php/mtn/plantask/add',
            update: 'index.php/mtn/plantask/update',
            destroy: 'index.php/mtn/plantask/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_plan_task_id',
    fields: [
        'mtn_plan_task_id',
        'mtn_plan_id',
        'mtn_task_id',
        {
            name: 'mtn_task_name',
            mapping: 'MtnTask.mtn_task_name'
        },
        'mtn_plan_task_interval'
    ]
});

App.Mtn.WoTypesAll.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotype/getAll',
            create: 'index.php/mtn/wotype/add',
            update: 'index.php/mtn/wotype/update',
            destroy: 'index.php/mtn/wotype/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_type_id',
    fields: [
        'mtn_work_order_type_id',
        'mtn_work_order_type_name'
    ]
});

App.Mtn.WoTypesAllByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotype/getAllByNode',
            create: 'index.php/mtn/wotype/addByNode',
            update: 'index.php/mtn/wotype/update',
            destroy: 'index.php/mtn/wotype/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_type_id',
    fields: [
        'mtn_work_order_type_id',
        'mtn_work_order_type_name'
    ]
});

App.Mtn.WoTypesAllByNodeSolo.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotype/getAllByNodeSolo'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_type_id',
    fields: [
        'mtn_work_order_type_id',
        'mtn_work_order_type_name'
    ]
});

App.Mtn.WoTypesAllByAssetSolo.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wotype/getAllByAssetSolo'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_work_order_type_id',
    fields: [
        'mtn_work_order_type_id',
        'mtn_work_order_type_name'
    ]
});

App.Mtn.ComponentType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/componenttype/get',
            create: 'index.php/mtn/componenttype/add',
            update: 'index.php/mtn/componenttype/update',
            destroy: 'index.php/mtn/componenttype/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_type_id',
    fields: [
        'mtn_component_type_id',
        'mtn_component_type_name'
    ]
});

App.Mtn.ComponentTypeByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/componenttype/getByNode',
            create: 'index.php/mtn/componenttype/addByNode',
            update: 'index.php/mtn/componenttype/update',
            destroy: 'index.php/mtn/componenttype/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_type_id',
    fields: [
        'mtn_component_type_id',
        'mtn_component_type_name'
    ]
});

App.Mtn.PriceList.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/pricelist/get',
            create: 'index.php/mtn/pricelist/add',
            update: 'index.php/mtn/pricelist/update',
            destroy: 'index.php/mtn/pricelist/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_price_list_id',
    fields: [
        'mtn_price_list_id',
        'provider_id',
        'currency_id',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }, {
            name: 'currency_name',
            mapping: 'Currency.currency_name'
        }, {
            type: 'date',
            name: 'mtn_price_list_date_validity_start',
            dateFormat: 'Y-m-d'
        }, {
            type: 'date',
            name: 'mtn_price_list_date_validity_finish',
            dateFormat: 'Y-m-d'
        }
    ]
});

App.Mtn.PriceListByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/pricelist/getByNode',
            create: 'index.php/mtn/pricelist/addByNode',
            update: 'index.php/mtn/pricelist/update',
            destroy: 'index.php/mtn/pricelist/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_price_list_id',
    fields: [
        'mtn_price_list_id',
        'provider_id',
        'currency_id',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }, {
            name: 'currency_name',
            mapping: 'Currency.currency_name'
        }, {
            type: 'date',
            name: 'mtn_price_list_date_validity_start',
            dateFormat: 'Y-m-d'
        }, {
            type: 'date',
            name: 'mtn_price_list_date_validity_finish',
            dateFormat: 'Y-m-d'
        }
    ]
});

App.Mtn.PriceListComponentNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/pricelistcomponent/getByNode'
                //    ,
                //            create: 	'index.php/mtn/pricelistcomponent/add',
                //            update: 	'index.php/mtn/pricelistcomponent/update',
                //            destroy: 	'index.php/mtn/pricelistcomponent/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_component_id',
    fields: [
        'mtn_component_id',
        'mtn_component_name',
        'MtnPriceListComponent',
        {
            name: 'mtn_price_list_component_id',
            convert: function priceListId(v, record) {
                return (record.MtnPriceListComponent[0] == null ? record.mtn_component_id : record.MtnPriceListComponent[0].mtn_price_list_component_id);
            }
        }, {
            name: 'mtn_component_with_type',
            convert: function componentWithType(v, record) {
                return record.mtn_component_name + ' -> ' + record.MeasureUnit.measure_unit_name + ' (' + record.MeasureUnit.measure_unit_description + ')' + ' / ' + record.MtnComponentType.mtn_component_type_name;
            }
        }
    ]
});


App.Mtn.PriceListComponentAll.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/pricelistcomponent/getByIdList',
            create: 'index.php/mtn/pricelistcomponent/add',
            update: 'index.php/mtn/pricelistcomponent/update',
            destroy: 'index.php/mtn/pricelistcomponent/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_price_list_component_id',
    fields: [
        'mtn_price_list_component_id',
        'mtn_price_list_id', 'mtn_component_id',
        {
            name: 'mtn_component_name',
            mapping: 'MtnComponent.mtn_component_name'
        },
        'mtn_price_list_component_price',
        {
            name: 'component_name_and_description',
            convert: function stringComponentAndUnit(v, record) {
                return record.MtnComponent.mtn_component_name + ' -> ' + record.MtnComponent.MeasureUnit.measure_unit_name + ' (' + record.MtnComponent.MeasureUnit.measure_unit_description + ')';
            }
        },
    ]
});


App.Mtn.WoPreventive.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wo/getPreventive',
            create: 'index.php/mtn/wo/addPreventive'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_id',
    fields: [
        'asset_id',
        'asset_type_id',
        'brand_id',
        'node_id',
        'asset_name',
        'asset_path',
        {
            name: 'asset_type_name',
            mapping: 'AssetType.asset_type_name'
        }, {
            name: 'brand_name',
            mapping: 'Brand.brand_name'
        }
    ]
});

App.Mtn.WoPreventiveByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wo/getPreventiveByNode',
            create: 'index.php/mtn/wo/addPreventiveByNode'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'node_id',
    fields: [
        'node_id',
        'node_name',
        'node_ruta',
        {
            name: 'node_type_name',
            mapping: 'NodeType.node_type_name'
        }
    ]
});


App.Mtn.ConfigStateDisponibles.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/configstate/get'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_system_work_order_status_id',
    fields: [
        'mtn_system_work_order_status_id',
        'mtn_system_work_order_status_name'
    ]
});

App.Mtn.ConfigStateDisponiblesByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/configstate/getByNode'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_system_work_order_status_id',
    fields: [
        'mtn_system_work_order_status_id',
        'mtn_system_work_order_status_name'
    ]
});

App.Mtn.ConfigStateAsociados.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/configstate/getAssociated',
            create: 'index.php/mtn/configstate/add',
            update: 'index.php/mtn/configstate/update',
            destroy: 'index.php/mtn/configstate/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_config_state_id',
    fields: [
        'mtn_config_state_id',
        'mtn_config_state_duration',
        {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        }, {
            name: 'mtn_config_state_access_user',
            convert: function strinUserSystemType(v, record) {
                return (record.mtn_config_state_access_user == '1' ? true : false);
            }
        }, {
            name: 'mtn_config_state_access_provider',
            convert: function strinProviderSystemType(v, record) {
                return (record.mtn_config_state_access_provider == '1' ? true : false);
            }
        }, {
            name: 'mtn_config_state_access_user_type',
            convert: function strinUserSystem(v, record) {
                return (record.mtn_config_state_access_user == '1' ? App.Language.General.yes : '');
            }
        }, {
            name: 'mtn_config_state_access_provider_type',
            convert: function strinProviderSystem(v, record) {
                return (record.mtn_config_state_access_provider == '1' ? App.Language.General.yes : '');
            }
        }
    ]
});

App.Mtn.ConfigStateAsociadosAll.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/configstate/getAssociatedAll',
            create: 'index.php/mtn/configstate/add',
            update: 'index.php/mtn/configstate/update',
            destroy: 'index.php/mtn/configstate/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_config_state_id',
    fields: [
        'mtn_config_state_id',
        'mtn_config_state_duration',
        {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        }, {
            name: 'mtn_config_state_access_user',
            convert: function strinUserSystemType(v, record) {
                return (record.mtn_config_state_access_user == '1' ? true : false);
            }
        }, {
            name: 'mtn_config_state_access_provider',
            convert: function strinProviderSystemType(v, record) {
                return (record.mtn_config_state_access_provider == '1' ? true : false);
            }
        }, {
            name: 'mtn_config_state_access_user_type',
            convert: function strinUserSystem(v, record) {
                return (record.mtn_config_state_access_user == '1' ? App.Language.General.yes : '');
            }
        }, {
            name: 'mtn_config_state_access_provider_type',
            convert: function strinProviderSystem(v, record) {
                return (record.mtn_config_state_access_provider == '1' ? App.Language.General.yes : '');
            }
        }
    ]
});

App.Mtn.MovStateUp = function(mtn_config_state_id) {
    Ext.Ajax.request({
        url: 'index.php/mtn/configstate/moveUp',
        params: {
            mtn_config_state_id: mtn_config_state_id
        }
    });
};

App.Mtn.MovStateDown = function(mtn_config_state_id) {
    Ext.Ajax.request({
        url: 'index.php/mtn/configstate/moveDown',
        params: {
            mtn_config_state_id: mtn_config_state_id
        }
    });
};

App.Mtn.Log.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/mtn/wo/getLogWorkOrder'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'mtn_status_log_id',
    fields: [
        'mtn_status_log_id',
        'mtn_work_order_id',
        {
            type: 'date',
            name: 'mtn_status_log_datetime',
            dateFormat: 'Y-m-d H:i:s'
        },
        'mtn_status_log_comments',
        {
            name: 'user_name',
            mapping: 'User.user_name'
        }, {
            name: 'mtn_work_order_type_name',
            mapping: 'MtnConfigState.MtnWorkOrderType.mtn_work_order_type_name'
        }, {
            name: 'mtn_system_work_order_status_name',
            mapping: 'MtnConfigState.MtnSystemWorkOrderStatus.mtn_system_work_order_status_name'
        }
    ]
});