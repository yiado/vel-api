App.Plan.Config.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/plan/category/getList'
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
    idProperty: 'node_type_id',
    fields: [
        'node_type_id',
        'node_type_name',
        'node_type_location',
        'node_type_category_id',
        'PlanCategory',
        {
            name: 'node_type_category_name',
            mapping: 'NodeTypeCategory.node_type_category_name'
        }, {
            name: 'node_type_state',
            convert: function strinUserFullType(v, record) {
                return (record.node_type_location == '1' ? App.Language.General.yes : 'No');
            }
        }
    ]
});


App.Plan.Category.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/plan/category/get',
            create: 'index.php/plan/category/add',
            update: 'index.php/plan/category/update',
            destroy: 'index.php/plan/category/delete'
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
    idProperty: 'plan_category_id',
    fields: [
        'plan_category_id',
        'plan_category_name',
        'plan_category_description',
        'plan_category_default',
        {
            name: 'plan_category_is_default',
            convert: function stringCategoryIsDefault(v, record) {
                return (record.plan_category_default == 0 ? '' : App.Language.General.yes);
            }
        }
    ]
});

App.Plan.Store = new Ext.data.JsonStore({
    url: 'index.php/plan/plan/get',
    root: 'results',
    totalProperty: 'total',
    idProperty: 'plan_id',
    fields: [
        'plan_id',
        'node_id',
        'plan_version',
        'plan_category_id',
        'plan_current_version',
        'plan_filename',
        'plan_comments',
        'plan_description',
        'plan_datetime',
        'PlanCategory',
        'User',
        {
            type: 'date',
            name: 'plan_datetime',
            dateFormat: 'Y-m-d H:i:s'
        },
        'handler',
        'plan_node_id',
        'plan_section_id'
    ]
});

App.Plan.Store.AllVersions = new Ext.data.JsonStore({
    url: 'index.php/plan/plan/getAll',
    root: 'results',
    totalProperty: 'total',
    idProperty: 'plan_id',
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    fields: [
        'plan_id',
        'node_id',
        'plan_version',
        'plan_category_id',
        'plan_current_version',
        'plan_filename',
        'plan_comments',
        'plan_description',
        'plan_datetime',
        'star_data',
        'end_data',
        'PlanCategory',
        'User',
        {
            type: 'date',
            name: 'plan_datetime',
            dateFormat: 'Y-m-d H:i:s'
        }
    ]
});


App.Plan.Version.Store = Ext.extend(Ext.data.Store, {
    url: 'index.php/plan/version/get',
    reader: new Ext.data.JsonReader({
        root: 'results',
        totalProperty: 'total',
        idProperty: 'plan_id'
    }, [
        'plan_id',
        'node_id',
        'plan_version',
        'plan_category_id',
        'plan_current_version',
        'plan_filename',
        'plan_comments',
        'plan_description',
        'plan_datetime',
        'PlanCategory',
        'User',
        {
            name: 'user_name',
            mapping: 'User.user_name'
        }, {
            name: 'plan_category_name',
            mapping: 'PlanCategory.plan_category_name'
        }, {
            type: 'date',
            name: 'plan_datetime',
            dateFormat: 'Y-m-d H:i:s'
        }, {
            name: 'plan_datetime_formated',
            mapping: 'plan_datetime',
            convert: function(v) {
                return Date.parseDate(v, 'Y-m-d H:i:s').format(App.General.DatPatterns.HumanDateTime);
            }
        }
    ])
});
Ext.reg('App.Plan.Version.Store', App.Plan.Version.Store);

App.Plan.getNodeHandler = new Ext.data.JsonStore({
    url: 'index.php/plan/node/get',
    root: 'results',
    totalProperty: 'total',
    idProperty: 'plan_node_id',
    fields: [
        'plan_node_id',
        'plan_id',
        'node_id',
        'handler',
        'Plan'
    ]
});

App.Plan.Section.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/plan/section/get',
            create: 'index.php/plan/section/add',
            update: 'index.php/plan/section/update',
            destroy: 'index.php/plan/section/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'plan_section_id',
    fields: [
        'plan_section_id',
        'plan_id',
        'plan_section_name',
        'plan_section_color',
        {
            name: 'plan_section_status',
            convert: function(v) {
                return (v == 1 ? true : false);
            }
        }
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});

App.Plan.Section.StoreFiltered = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/plan/section/getFiltered'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'plan_section_id',
    fields: [
        'plan_section_id',
        'plan_id',
        'plan_section_name',
        'plan_section_color',
        'plan_section_status',
        'infra_info_usable_area',
        'infra_info_usable_area_total',
        'infra_info_usable_area_total_p'
    ]
});

App.Plan.Section.StoreBimVersion = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/plan/plan/getBimVersion'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'filename',
    fields: [
        'filename',
        'node',
        'url',
        'version',

    ]
});



App.Plan.PlanNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/plan/section/getByNode'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'node_id',
    fields: [
        'plan_id',
        'node_name',
        'node_id',
        'node_type_name',
        'plan_section_name',
        'handler'
    ],
    listeners: {
        'update': function(store) {
            store.sort('plan_section_name', 'ASC')
        }
    }
});

App.Plan.saveHandler = function(handler, node_id, plan_id, plan_section_id, successCallBack) {

    Ext.Ajax.request({
        url: 'index.php/plan/node/save',
        params: {
            handler: handler,
            node_id: node_id,
            plan_id: plan_id,
            plan_section_id: plan_section_id
        },
        success: successCallBack
    });
};

App.Plan.saveHandlerForm = function(node_id, plan_id, plan_section_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/plan/node/saveForm',
        params: {
            node_id: node_id,
            plan_id: plan_id,
            plan_section_id: plan_section_id
        },
        success: successCallBack
    });
};


App.Plan.planLayers = new Ext.data.ArrayStore({
    idProperty: 'layer_id',
    fields: [
        'layer_id',
        'layer_name',
        'layer_status'
    ]
});


App.Plan.DeleteVersion = function(plan_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/plan/version/delete',
        params: {
            plan_id: plan_id
        },
        success: successCallBack
    });
};

App.Plan.Node.Store = new Ext.data.JsonStore({
    url: 'index.php/plan/plan/getNode',
    root: 'results',
    totalProperty: 'total',
    idProperty: 'node_id',
    fields: [
        'node_id',
        'node_type_id',
        'node_name',
        {
            name: 'node_type_category_id',
            mapping: 'NodeType.node_type_category_id'
        },
        {
            name: 'node_type_name',
            mapping: 'NodeType.node_type_name'
        },
        {
            name: 'node_type_location',
            mapping: 'NodeType.node_type_location'
        }
    ]
});