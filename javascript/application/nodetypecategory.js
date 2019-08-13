Ext.namespace('App.NodeTypeCategory');

App.NodeTypeCategory.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/nodetypecategory/getList',
            create: 'index.php/core/nodetypecategory/add',
            update: 'index.php/core/nodetypecategory/update',
            destroy: 'index.php/core/nodetypecategory/delete'
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
    idProperty: 'node_type_category_id',
    fields: [
        'node_type_category_id',
        'node_type_category_name'
    ]
});