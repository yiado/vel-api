Ext.namespace('App.Costs.Store');

App.Costs.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/costs/costs/get',
            create: 'index.php/costs/costs/add',
            update: 'index.php/costs/costs/update',
            destroy: 'index.php/costs/costs/delete'
        }
    }),

    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    autoload: false,
    totalProperty: 'total',
    idProperty: 'costs_id',
    fields: [
        'costs_id',
        'node_id',
        'costs_number_ticket',
        'costs_value',
        'costs_detail',
        'costs_anio',
        'costs_type_id',
        'costs_month_id',
        {
            name: 'costs_type_name',
            mapping: 'CostsType.costs_type_name'
        }, {
            name: 'costs_month_name',
            mapping: 'CostsMonth.mes_traducido'
        }
    ]
});

App.Costs.CostsType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/costs/coststype/get',
            create: 'index.php/costs/coststype/add',
            update: 'index.php/costs/coststype/update',
            destroy: 'index.php/costs/coststype/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.FlashMessage.alert(response.raw.msg);
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
    autoload: false,
    totalProperty: 'total',
    idProperty: 'costs_type_id',
    fields: [
        'costs_type_id',
        'costs_type_name'
    ]
});

App.Costs.CostsMonth.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/costs/costsmonth/getCostsMonth'
        }
    }),

    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    autoload: false,
    totalProperty: 'total',
    idProperty: 'costs_month_id',
    fields: [
        'costs_month_id',
        {
            name: 'costs_month_name',
            mapping: 'mes_traducido'
        }
    ]
});