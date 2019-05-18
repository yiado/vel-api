Ext.namespace('App.Asset.Store');
Ext.namespace('App.Asset.Asset');
Ext.namespace('App.Asset.Papelera.Store');
Ext.namespace('App.Asset.Trasladados.Store');

App.Asset.AssetLoad.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :     'index.php/asset/assetload/get',
            destroy :  'index.php/asset/assetload/delete'
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
    totalProperty: "total",
    root: 'results',
    idProperty: 'asset_load_id',
    fields: 
    [
        'asset_load_id', 
        'user_id', 
        'asset_load_folio',
        'asset_load_comment',
        'User', 
        {
            type: 'date',
            name: 'asset_load_date',
            dateFormat: 'Y-m-d'
        }
    ]
});

App.Asset.AssetLoadId.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :     'index.php/asset/assetload/getId'
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
    totalProperty: "total",
    root: 'results',
    idProperty: 'asset_id',
    fields: 
    [
        'asset_id', 
        'asset_name', 
        'asset_num_serie_intern',
        'asset_path'
    ],
    listeners: {
        'save': function() {
            this.load({params: {start: 0, limit: App.GridLimit}});
        }
    }
});


App.Asset.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :     'index.php/asset/asset/get',
            create :   'index.php/asset/asset/add',
            update :   'index.php/asset/asset/update'
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
    totalProperty: "total",
    root: 'results',
    idProperty: 'asset_id',
    fields: 
    [
        'asset_id', 
        'node_id', 
        'asset_type_id',
        'asset_status_id',
        'asset_condition_id',
        'asset_name',
        'AssetStatus',
        'AssetCondition',
        'AssetLoad',
        'start_date',
        'brand_id',
        'end_date',
        'asset_num_serie',
        'asset_num_serie_intern',
        'asset_num_factura',
        'asset_document_count',
        'asset_current_cost',
        'asset_cost',
        'asset_lifetime',
        'asset_path',
        'asset_last_inventory',
        {
            type: 'date',
            name: 'asset_expiration_date_lifetime',
            dateFormat: 'Y-m-d'
        },
        {
            type: 'date',
            name: 'asset_purchase_date',
            dateFormat: 'Y-m-d'
        },
        'asset_description',
        {
            name: 'asset_type_name',
            mapping: 'AssetType.asset_type_name'
        }, {
            name: 'brand_name',
            mapping: 'Brand.brand_name'
        },
        'AssetType',
        'asset_estate',
        {
            name: 'asset_with_type',
            convert: function assetWithType(v, record)
            {
                return record.asset_name + ' / ' + record.AssetType.asset_type_name;
            }

        }
    ],
    sortInfo: {
        field: 'asset_name'
    },
    listeners: 
    {
        'update' : function ( store ) 
        {
            store.sort('asset_name', 'ASC')
        },
        'save' : function () 
        {
            this.load({params: {start: 0, limit: App.GridLimitAsset}});
        }
    }
});

App.Asset.Trasladados.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api:
        {
            read: 'index.php/asset/assetinventory/getAllTrasladados'
        },
        listeners:
        {
            'exception': function(DataProxy, type, action, options, response, arg)
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_inventory_auxiliar_proceso_id',
    fields:
    [
        'asset_inventory_auxiliar_proceso_id',
        'asset_name',
        'brand_name',
        'asset_num_serie_intern',
        'codigo_auge',
        'original_location',
        'departamento_original',
        'nombre_subrecinto_original',
        'location_transfer',
        'departamento_transfer',
        'nombre_subrecinto_transfer',
        'situacion'
    ],
    sortInfo:
    {
        field: 'asset_inventory_auxiliar_proceso_id'
    },
    listeners: {
        'save': function() {
            this.load({params: {start: 0, limit: App.GridLimitTrasladado}});
        }
    }
});

App.Asset.Papelera.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :     'index.php/asset/asset/getBin',
            destroy :  'index.php/asset/asset/delete'
        },
        listeners : 
        {
            'exception' : function ( DataProxy, type, action, options, response, arg ) 
            {
                if (type == 'remote') 
                {
                    Ext.FlashMessage.alert(response.raw.msg);
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
    totalProperty: "total",
    root: 'results',
    idProperty: 'asset_id',
    fields: 
    [
        'asset_id', 
        'node_id', 
        'asset_type_id',
        'asset_status_id',
        'asset_condition_id',
        'asset_name',
        'AssetStatus',
        'AssetCondition',
        'start_date',
        'brand_id',
        'end_date',
        'asset_num_serie',
        'asset_num_serie_intern',
        'asset_num_factura',
        'asset_current_cost',
        'asset_cost',
        'asset_lifetime',
        'asset_path',
        'asset_last_inventory',
        {
            type: 'date',
            name: 'asset_expiration_date_lifetime',
            dateFormat: 'Y-m-d'
        },
        {
            type: 'date',
            name: 'asset_purchase_date',
            dateFormat: 'Y-m-d'
        },
        'asset_description',
        {
            name: 'asset_type_name',
            mapping: 'AssetType.asset_type_name'
        }, {
            name: 'brand_name',
            mapping: 'Brand.brand_name'
        },
        'AssetType',
        'asset_estate',
        {
            name: 'asset_with_type',
            convert: function assetWithType(v, record)
            {
                return record.asset_name + ' / ' + record.AssetType.asset_type_name;
            }

        }
    ],
    sortInfo: {
        field: 'asset_name'
    },
    listeners: 
    {
        'update' : function ( store ) 
        {
            store.sort('asset_name', 'ASC')
        },
        'save' : function () 
        {
            this.load();
        }
    }
});

App.Asset.Type.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assettype/get',
            create :  'index.php/asset/assettype/add',
            update :  'index.php/asset/assettype/update',
            destroy : 'index.php/asset/assettype/delete'
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_type_id',
    fields: 
    [
        'asset_type_id',
        'asset_type_name'
    ]
});

App.Asset.Inventory.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :	'index.php/asset/assetinventory/get',
            create :   	'index.php/asset/assetinventory/add',
            update :  	'index.php/asset/assetinventory/update',
            destroy : 	'index.php/asset/assetinventory/delete'
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_inventory_id',
    fields: 
    [
        'asset_inventory_id',
        'node_id',
        'asset_id',
        'Asset',
        {
            type: 'date',
            name: 'asset_inventory_datetime',
            dateFormat: 'Y-m-d H:i:s'
        }
    ]
});

App.Asset.Status.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assetstatus/get',
            create :  'index.php/asset/assetstatus/add',
            update :  'index.php/asset/assetstatus/update',
            destroy : 'index.php/asset/assetstatus/delete'
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_status_id',
    fields: 
    [
        'asset_status_id',
        'asset_status_name',
        'asset_status_description'
    ]
});

App.Asset.Condition.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assetcondition/get',
            create :  'index.php/asset/assetcondition/add',
            update :  'index.php/asset/assetcondition/update',
            destroy : 'index.php/asset/assetcondition/delete'
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_condition_id',
    fields: 
    [
        'asset_condition_id',
        'asset_condition_name',
        'asset_condition_description'
    ]
});

App.Asset.Insurance.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assetinsurance/get',
            create :  'index.php/asset/assetinsurance/add',
            update :  'index.php/asset/assetinsurance/update',
            destroy : 'index.php/asset/assetinsurance/delete'
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
    root: 'results',
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    totalProperty: 'total',
    idProperty: 'asset_insurance_id',
    fields: 
    [
        'asset_insurance_id',
        'asset_id',
        {
            name: 'asset_insurance_status_name',
            convert: function warrantyStatus(v, record)
            {
                return ExpirationState(record.asset_insurance_begin_date, record.asset_insurance_expiration_date);
            }
        },
        'ProviderType',
        'provider_type_name',
        'Provider',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        },
        'provider_id',
        {
            name: 'provider_type_id',
            mapping: 'Provider.provider_type_id'
        }, {
            type: 'date',
            name: 'asset_insurance_begin_date',
            dateFormat: 'Y-m-d'
        }, {
            type: 'date',
            name: 'asset_insurance_expiration_date',
            dateFormat: 'Y-m-d'
        },
        'asset_insurance_description'
    ],
    listeners: 
    {
        'save' : 		
        function () 
        {
            this.load();
        }
    }	
});

App.Asset.Measurement.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assetmeasurement/get',
            create :  'index.php/asset/assetmeasurement/add',
            update :  'index.php/asset/assetmeasurement/update',
            destroy : 'index.php/asset/assetmeasurement/delete'
        }
    }),
    root: 'results',
    writer: new Ext.data.JsonWriter
    ({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    autoSave: true,
    totalProperty: 'total',
    idProperty: 'asset_measurement_id',
    fields: 
    [
        'asset_measurement_id',
        'asset_id',
        'measure_unit_id',
        {
            type: 'date',
            name: 'asset_measurement_date',
            dateFormat: 'Y-m-d'
        },
        'asset_measurement_cantity',
        'asset_measurement_comments',
        {
            name: 'measure_unit_name',
            mapping: 'MeasureUnit.measure_unit_name'
        }
    ],
    listeners: 
    {
        'save' : function () 
        {
            this.load();
        }
    }
});

App.Asset.Document.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assetdocument/get',
            create :  'index.php/asset/assetdocument/add',
            update:   'index.php/asset/assetdocument/update',
            destroy : 'index.php/asset/assetdocument/delete'	
        },
        listeners: 
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
    totalProperty: "total",
    root: 'results',
    idProperty: 'asset_document_id',
    fields: 
    [
        'asset_id', 
        'asset_document_id', 
        'asset_document_filename', 
        'asset_document_description',
        'asset_document_comments', 
        {
            name: 'user_name',
            mapping: 'User.user_name'
        }
    ]
});

App.Asset.ConfigMeasurement.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assettriggermeasurementconfig/get',
            create :  'index.php/asset/assettriggermeasurementconfig/add',
            update :  'index.php/asset/assettriggermeasurementconfig/update',
            destroy : 'index.php/asset/assettriggermeasurementconfig/delete'
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_trigger_measurement_config_id',
    fields: 
    [
        'asset_type_id',
        'asset_trigger_measurement_config_id',
        'measure_unit_id',
        {
            name: 'asset_type_name',
            mapping: 'AssetType.asset_type_name'
        },
        'mtn_plan_id',
        'asset_trigger_measurement_config_start',
        'asset_trigger_measurement_config_end',
        {
            name: 'intervalo',
            convert: function rangeOrInterval(v, record)
            {
                var stringCriterio;
                if (record.asset_trigger_measurement_config_end == null) 
                {
                    stringCriterio = record.asset_trigger_measurement_config_start;
                } else {
                    stringCriterio = record.asset_trigger_measurement_config_start + '  ' + ' - ' + '  ' + record.asset_trigger_measurement_config_end;
                }
                return stringCriterio + '    ' + record.MeasureUnit.measure_unit_name;
            }
        },		
        {
            name: 'notificacion_type',
            convert: function strinFieldType(v, record)
            {
                var stringTypeCamp;
                if (record.asset_trigger_measurement_config_notificacion_method == 0) 
                {
                    stringTypeCamp = App.Language.Asset.not_notification;
                }
                else if (record.asset_trigger_measurement_config_notificacion_method == 1)
                {
                    stringTypeCamp = App.Language.Asset.mail;
                }
                else if (record.asset_trigger_measurement_config_notificacion_method == 2)
                {
                    stringTypeCamp = App.Language.Asset.sms;
                }
                else
                {
                    stringTypeCamp = App.Language.Asset.mail_and_sms;
                }
                return stringTypeCamp;
            }
        },
        'asset_trigger_measurement_config_notificacion_mails',
        'asset_trigger_measurement_config_notificacion_method'
    ]
});

ExpirationState = function(asset_insurance_begin_date, asset_insurance_expiration_date)
{
    var f_begin = new Date();
    var f_expiration = new Date();
    var f_now = new Date();
    f_begin = Date.parseDate(asset_insurance_begin_date, App.General.DefaultSystemDate);
    f_expiration = Date.parseDate(asset_insurance_expiration_date, App.General.DefaultSystemDate);
	
    var status = App.Language.Asset.expired;
    if (f_begin < f_now && f_expiration > f_now) 
    {
        status = App.Language.Asset.in_progress;
    }
    return status;
}

//Usado para el grid de los attributos
App.Asset.DatosDinamicos.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :	'index.php/asset/assetotherdataattribute/get',
            create :	'index.php/asset/assetotherdataattribute/add',
            update :	'index.php/asset/assetotherdataattribute/update',
            destroy :	'index.php/asset/assetotherdataattribute/delete'
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
    idProperty: 'asset_other_data_attribute_id',
    fields: 
    [
        'asset_other_data_attribute_id',
        'asset_other_data_attribute_name'
    ]
});


App.Asset.DatosDinamicosDisponibles.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :	'index.php/asset/assetotherdataattribute/get'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_other_data_attribute_id',
    fields: 
    [
        'asset_other_data_attribute_id',
        'asset_other_data_attribute_name'	
    ]
});

App.Asset.DatosDinamicosAsociados.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :	'index.php/asset/assetotherdataattributeassettype/get',
            create :	'index.php/asset/assetotherdataattributeassettype/add',
            update :	'index.php/asset/assetotherdataattributeassettype/update',
            destroy:	'index.php/asset/assetotherdataattributeassettype/delete'
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
    root: 'results',
    totalProperty: 'total',
    idProperty: 'asset_other_data_attribute_id',
    fields: 
    [
        'asset_other_data_attribute_id',
        {
            name: 'asset_other_data_attribute_name',
            mapping: 'AssetOtherDataAttribute.asset_other_data_attribute_name'				
        }
    ]
});

App.Asset.OtrosDatos.Store = new Ext.data.JsonStore
({
    url: 'index.php/asset/assetotherdatavalue/get',
    root: 'results',
    totalProperty: 'total',
    idProperty:'asset_other_data_attribute_id',
    fields: 
    [
        'asset_other_data_attribute_id',
        'value', 
        'label'
    ]
});

App.Asset.Log.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({ 
        api: 
        {
            read :    'index.php/asset/assetlog/get'
        },
        listeners: 
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
    totalProperty: "total",
    root: 'results',
    idProperty: 'asset_log_id',
    fields: 
    [
        'asset_log_id', 
        'user_id',
        'User',
        {
            type: 'date',
            name: 'asset_log_datetime',
            dateFormat: 'Y-m-d H:i:s'
        }, 
        'asset_log_type',
        'asset_log_detail' ,
        'asset_log_type_name'
    ]
});

App.Asset.MovProxy = function ( asset_id ) 
{
    Ext.Ajax.request
    ({
        url: 'index.php/asset/assetcontroller/edit',
        params: 
        {
            asset_id: asset_id,
            action: 'move'
        }
    });
};

App.Asset.PasteProxy = function ( node_id, successCallBack )
{
    Ext.Ajax.request
    ({
        url: 'index.php/asset/assetcontroller/edit',
        params: {
            node_id: node_id,
            action: 'paste'
        },
        success: function(response)
        {
             App.Asset.Store.load();
            response = Ext.decode(response.responseText);            
            Ext.FlashMessage.alert(response.msg);
        },
        failure: function(response)
        {
            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
        }
       
    })
};

App.Core.ContractAssetAll.Store = new Ext.data.JsonStore
({
    proxy: new Ext.data.HttpProxy
    ({
        api: 
        {
            read: 	'index.php/core/contractasset/getAll'
        }
    }),
    root: 'results',
    idProperty: 'asset_id',
    totalProperty: 'total',
    fields: 
    [
        'asset_id',
        'asset_type_id',
        'brand_id',
        'node_id',
        'asset_name',
        'asset_path',
        {
            name: 'asset_type_name',
            mapping: 'AssetType.asset_type_name'
        },
        {
            name: 'brand_name',
            mapping: 'Brand.brand_name'
        }
    ]
});
