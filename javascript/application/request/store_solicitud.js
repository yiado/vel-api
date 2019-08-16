App.Request.Solicitudes.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/solicitud/get',
            create: 'index.php/request/solicitud/add',
            update: 'index.php/request/solicitud/update'
        },
        listeners: {
            'exception': function (DataProxy, type, action, options, response, arg) {
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
    idProperty: 'solicitud_id',
    fields: [
        'solicitud_id',
        'node_id',
        'user_id',
        'solicitud_type_id',
        'solicitud_estado_id',
        'solicitud_fecha',
        'solicitud_folio',
        'solicitud_factura_nombre',
        'solicitud_factura_numero',
        'solicitud_oc_nombre',
        'solicitud_oc_numero',
        'solicitud_comen_user',
        'solicitud_comen_admin',
        'Node',
        'SolicitudEstado',
        'SolicitudType',
        'User'
    ],
    listeners: {
        'save': function ()
        {
            this.load();
        }
    }
});

App.Request.SolicitudEstados.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/estado/get'
        },
        listeners: {
            'exception': function (DataProxy, type, action, options, response, arg) {
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
    idProperty: 'solicitud_estado_id',
    fields: [
        'solicitud_estado_id',
        'solicitud_estado_nombre',
        'solicitud_estado_comentario'
    ],
    listeners: {
        'save': function () {
            this.load();
        }
    }
});

App.Request.SolicitudTipos.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/tipo/get'
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
    idProperty: 'solicitud_type_id',
    fields: [
        'solicitud_type_id',
        'solicitud_type_nombre',
        'solicitud_type_comentario'
    ],
    listeners: {
        'save': function ()
        {
            this.load();
        }
    }
});

App.Request.SolicitudLog.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/log/get'
        },
        listeners: {
            'exception': function (DataProxy, type, action, options, response, arg) {
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
    idProperty: 'solicitud_log_id',
    fields: [
        'solicitud_log_id',
        'solicitud_id',
        'user_id',
        'solicitud_log_fecha',
        'solicitud_log_detalle',
        'User',
        'Solicitud'
    ],
    listeners: {
        'save': function () {
            this.load();
        }
    }
});