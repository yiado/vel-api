Ext.namespace('App.NodeType');

App.NodeType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/nodetype/getList',
            create: 'index.php/core/nodetype/add',
            update: 'index.php/core/nodetype/update',
            destroy: 'index.php/core/nodetype/delete'
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
        {
            name: 'node_type_category_name',
            mapping: 'NodeTypeCategory.node_type_category_name'
        },
        {
            name: 'node_type_state',
            convert: function strinUserFullType(v, record) {
                return (record.node_type_location == '1' ? App.Language.General.yes : 'No');
            }
        }
    ]
});