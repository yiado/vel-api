/* global App, Ext */

App.Request.Services.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/get',
            create: 'index.php/request/service/add',
            update: 'index.php/request/service/update'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type == 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success == false) {
                        alert(response.msg);
                    }
                }
            }
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
    idProperty: 'service_id',
    fields: [
        'service_id',
        'node_id',
        'user_id',
        'service_type_id',
        'service_status_id',
        'service_date',
        'service_organism',
        'service_phone',
        'service_commentary',
        'Node',
        'User',
        'ServiceType',
        'ServiceStatus'
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});

App.Request.ServicesStatus.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/servicestatus/get'
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
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'service_status_id',
    fields: [
        'service_status_id',
        'service_status_name',
        'service_status_commentary'
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});

App.Request.ServicesType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/servicetype/get'
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
    idProperty: 'service_type_id',
    fields: [
        'service_type_id',
        'service_type_name',
        'service_type_commentary'
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});

App.Request.ServicesLog.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/servicelog/get'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type == 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success == false)
                        alert(response.msg);
                }
            }
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
    idProperty: 'service_log_id',
    fields: [
        'service_log_id',
        'service_id',
        'user_id',
        'service_log_date',
        'service_log_detail',
        'User',
        'Service'
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});