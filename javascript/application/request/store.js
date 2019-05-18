
App.Request.Solicitudes.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read:       'index.php/request/solicitud/get',
            create :    'index.php/request/solicitud/add',
            update: 	'index.php/request/solicitud/update',
        },
        listeners : 
        {
            'exception' : function ( DataProxy, type, action, options, response, arg ) 
            {
                if (type == 'remote') 
                {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            if (type == 'response')
            {
                response = Ext.decode(response.responseText);
                if (response.success == false)
                    alert(response.msg);
            }                                        
            }
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'solicitud_id',
    fields: 
    [
        'solicitud_id', 
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
        'SolicitudEstado',
        'SolicitudType',
        'User'
    ],
    listeners: 
    {
        'save': function()
        {
            this.load();
        }
    }
});


App.Request.SolicitudEstados.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 'index.php/request/estado/get'
        },
        listeners : 
        {
            'exception' : function ( DataProxy, type, action, options, response, arg ) 
            {
                if (type == 'remote') 
                {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'solicitud_estado_id',
    fields: 
    [
        'solicitud_estado_id', 
        'solicitud_estado_nombre',
        'solicitud_estado_comentario'
    ],
    listeners: 
    {
        'save': function()
        {
            this.load();
        }
    }
});


App.Request.SolicitudTipos.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 'index.php/request/tipo/get'
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'solicitud_type_id',
    fields: 
    [
        'solicitud_type_id', 
        'solicitud_type_nombre',
        'solicitud_type_comentario'
    ],
    listeners: 
    {
        'save': function()
        {
            this.load();
        }
    }
});

App.Request.Status.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 'index.php/request/status/get'
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'request_status_id',
    fields: 
    [
        'request_status_id', 
        'request_status_name'
    ]
});

App.Request.Problem.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 	'index.php/request/problem/get',
            create: 	'index.php/request/problem/add',
            update: 	'index.php/request/problem/update',
            destroy: 	'index.php/request/problem/delete'
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'request_problem_id',
    fields: 
    [
        'request_problem_id', 
        'request_problem_name'
    ]
});

App.Request.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read:           'index.php/request/request/get',
            create: 	'index.php/request/request/add',
            update: 	'index.php/request/request/update',
            destroy: 	'index.php/request/request/delete'
        },
        listeners : 
        {
            'exception' : function ( DataProxy, type, action, options, response, arg ) 
            {
                if (type == 'remote') 
                {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'request_id',
    fields: 
    [
        'request_id', 
        'request_folio', 
        'request_mail',
        'request_fono',
        'request_problem_id', 
        'asset_id', 'user_id', 
        {
            type: 'date',
            name: 'request_date_creation',
            dateFormat: 'Y-m-d'
        }, 
        'request_date_resolution', 
        'request_subject', 
        'request_description', 
        'mtn_work_order_id', 
        'RequestStatus', 
        {
            name: 'request_status_name',
            mapping: 'RequestStatus.request_status_name'
        },
        'request_requested_by', 
        'request_requested_by_comment', 
        'RequestProblem', 
        'Asset'
    ],
    listeners: 
    {
        'save': function()
        {
            this.load();
        }
    }
});

App.RequestByNode.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 	'index.php/request/request/getByNode'
        },
        listeners : 
        {
            'exception' : function ( DataProxy, type, action, options, response, arg ) 
            {
                if (type == 'remote') 
                {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'request_id',
    fields: 
    [
        'request_id', 
        'request_folio', 
        'request_mail',
        'request_fono',
        'request_problem_id', 
        'node_id', 
        'node_ruta', 
        'node_name', 
        'user_id', 
        {
            type: 'date',
            name: 'request_date_creation',
            dateFormat: 'Y-m-d'
        }, 
        'request_date_resolution', 
        'request_subject', 
        'request_description', 
        'mtn_work_order_id', 
        'RequestStatus', 
        {
            name: 'request_status_name',
            mapping: 'RequestStatus.request_status_name'
        },

        'request_requested_by', 
        'request_requested_by_comment', 
        'RequestProblem'
    ],
    listeners: 
    {
        'save': function()
        {
            this.load();
        }
    }
});

App.Request.Provider.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 	'index.php/request/request/getProvider',
            create: 	'index.php/request/request/add',
            update: 	'index.php/request/request/update',
            destroy: 	'index.php/request/request/delete'
        }
    }),
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    root: 'results',
    totalProperty: 'total',
    idProperty: 'request_id',
    fields: 
    [
        'request_id', 
        'request_folio', 
        'request_problem_id', 
        'asset_id', 
        'user_id', 
        {
            type: 'date',
            name: 'request_date_creation',
            dateFormat: 'Y-m-d'
        }, 
        'request_date_resolution', 
        'request_subject', 
        'request_description', 
        'mtn_work_order_id', 
        'RequestStatus', 
        {
            name: 'request_status_name',
            mapping: 'RequestStatus.request_status_name'
        },
        'request_requested_by', 
        'request_requested_by_comment', 
        'RequestProblem', 
        'Asset'
    ],
    listeners: 
    {
        'save': function()
        {
            this.load();
        }
    }
});
