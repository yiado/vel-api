/* global Ext, App */

/**
 * 
 * Activos
 */
App.Request.Solicitudes.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/solicitud/get',
            create: 'index.php/request/solicitud/add',
            update: 'index.php/request/solicitud/update'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false) {
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
        'save': function() {
            this.load();
        }
    }
});

App.Request.SolicitudEstados.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/estado/get',
            update: 'index.php/request/estado/update',
            destroy: 'index.php/request/estado/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
        'save': function() {
            this.load();
        }
    }
});

App.Request.SolicitudTipos.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/tipo/get',
            update: 'index.php/request/tipo/update',
            destroy: 'index.php/request/tipo/delete'
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
        'save': function() {
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
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false)
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
        'save': function() {
            this.load();
        }
    }
});


/**
 * Servicios
 */
App.Request.Services.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/get',
            create: 'index.php/request/service/add',
            update: 'index.php/request/service/update'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false) {
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
            this.load({ params: { node_id: App.Interface.selectedNodeId,start: 0, limit: App.GridLimit } });
        }
    }
});

App.Request.ServicesStatus.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/servicestatus/get',
            update: 'index.php/request/servicestatus/update',
            destroy: 'index.php/request/servicestatus/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
            read: 'index.php/request/servicetype/get',
            update: 'index.php/request/servicetype/update',
            destroy: 'index.php/request/servicetype/delete'
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
        'service_type_commentary',
        'user_id',
        'User'
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
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false)
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

App.Request.ServicesStatusChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceStatus'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'ServiceStatus'
    ]
});

App.Request.ServicesTypeChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceType'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'ServiceType'
    ]
});

App.Request.ServicesDateChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceDate'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'month_year',
        'ServiceType'
    ]
});

App.Request.ServicesOrganismChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceOrganism'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'service_organism'
    ]
});


/**
 * Servicios
 */
App.Request.Services.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/get',
            create: 'index.php/request/service/add',
            update: 'index.php/request/service/update'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false) {
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
            this.load({ params: { node_id: App.Interface.selectedNodeId,start: 0, limit: App.GridLimit } });
        }
    }
});

App.Request.ServicesStatus.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/servicestatus/get',
            update: 'index.php/request/servicestatus/update',
            destroy: 'index.php/request/servicestatus/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
            read: 'index.php/request/servicetype/get',
            update: 'index.php/request/servicetype/update',
            destroy: 'index.php/request/servicetype/delete'
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
        'service_type_commentary',
        'user_id',
        'User'
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
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false)
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

App.Request.ServicesStatusChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceStatus'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'ServiceStatus'
    ]
});

App.Request.ServicesTypeChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceType'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'ServiceType'
    ]
});

App.Request.ServicesDateChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceDate'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'month_year',
        'ServiceType'
    ]
});

App.Request.ServicesOrganismChart.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/service/getServiceOrganism'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    fields: [
        'count',
        'service_organism'
    ]
});

/**
 * 
 * Rdi
 */
App.Request.Information.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/rdi/get',
            create: 'index.php/request/rdi/add',
            update: 'index.php/request/rdi/update'
        },
        listeners: {
            'exception': function (DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false) {
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
    idProperty: 'rdi_id',
    fields: [
        'rdi_id',
        'node_id',
        'user_id',
        'rdi_description',
        'rdi_status_id',
        'rdi_created_at',
        'rdi_updated_at',
        'Node',
        'RdiStatus',
        'User'
    ],
    listeners: {
        'save': function () {
            this.load();
        }
    }
});

App.Request.InformationStatus.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/rdistatus/get',
            update: 'index.php/request/rdistatus/update',
            destroy: 'index.php/request/rdistatus/delete'
        },
        listeners: {
            'exception': function (DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
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
    idProperty: 'rdi_status_id',
    fields: [
        'rdi_status_id',
        'rdi_status_name'
    ],
    listeners: {
        'save': function () {
            this.load();
        }
    }
});

App.Request.InformationLog.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/request/rdilog/get'
        },
        listeners: {
            'exception': function (DataProxy, type, action, options, response, arg) {
                if (type === 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                } else if (type === 'response') {
                    response = Ext.decode(response.responseText);
                    if (response.success === false)
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
    idProperty: 'rdi_log_id',
    fields: [
        'rdi_log_id',
        'rdi_id',
        'user_id',
        'rdi_log_date',
        'rdi_log_detail',
        'User',
        'Rdi'
    ],
    listeners: {
        'save': function () {
            this.load();
        }
    }
});