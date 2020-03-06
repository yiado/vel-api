Ext.namespace('App.InfraStructure.Store');

App.InfraStructure.FotoConfi.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/document/getDocument'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.FlashMessage.alert(response.raw.msg);
                }
            }
        }
    }),
    autoSave: true,
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'doc_document_id',
    fields: [
        'node_id',
        'doc_document_id',
        'doc_document_filename',
        'doc_document_description',
        'doc_extension_id',
        {
            name: 'doc_extension_id',
            mapping: 'DocExtension.doc_extension_name'
        },
        'doc_category_id',
        {
            name: 'doc_category_name',
            mapping: 'DocCategory.doc_category_name'
        }, {
            type: 'date',
            name: 'doc_document_creation',
            dateFormat: 'Y-m-d H:i:s'
        }, {
            type: 'date',
            name: 'doc_version_expiration',
            mapping: 'DocCurrentVersion.doc_version_expiration',
            dateFormat: 'Y-m-d'
        },
        'doc_current_version_id',
        {
            name: 'user_name',
            mapping: 'DocCurrentVersion.User.user_name'
        }, {
            name: 'doc_extension_name',
            mapping: 'DocExtension.doc_extension_name'
        },
        'DocCurrentVersion',
        {
            name: 'doc_image_web',
            mapping: 'DocCurrentVersion.doc_image_web'
        }, {
            name: 'doc_version_filename',
            mapping: 'DocCurrentVersion.doc_version_filename'
        }, {
            name: 'doc_version_alert_email',
            mapping: 'DocCurrentVersion.doc_version_alert_email'
        }, {
            name: 'doc_version_notification_email',
            mapping: 'DocCurrentVersion.doc_version_notification_email'
        }, {
            name: 'doc_version_comments',
            mapping: 'DocCurrentVersion.doc_version_comments'
        }, {
            name: 'doc_version_code_client',
            mapping: 'DocCurrentVersion.doc_version_code_client'
        },
        'doc_path'
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});

App.InfraStructure.Store.Principal = new Ext.data.JsonStore({
    url: 'index.php/core/nodecontroller/getSibling',
    root: 'results',
    idProperty: 'node_id',
    fields: [
        'node_id',
        'node_name',
        'icon',
        'node_type_name',
        'node_type_category_name'
    ],
    sortInfo: {
        field: 'node_name'
    },
    listeners: {
        'update': function(store) {
            store.sort('node_name', 'ASC');
        }
    }
});

App.InfraStructure.MovStateUp = function(infra_grupo_id) {
    Ext.Ajax.request({
        url: 'index.php/infra/infragrupo/moveUp',
        params: {
            infra_grupo_id: infra_grupo_id
        }
    });
};

App.InfraStructure.MovStateDown = function(infra_grupo_id) {
    Ext.Ajax.request({
        url: 'index.php/infra/infragrupo/moveDown',
        params: {
            infra_grupo_id: infra_grupo_id
        }
    });
};

App.InfraStructure.Grupos.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infragrupo/get',
            create: 'index.php/infra/infragrupo/add',
            destroy: 'index.php/infra/infragrupo/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'infra_grupo_id',
    totalProperty: 'total',
    fields: [
        'infra_grupo_id',
        'infra_grupo_nombre'
    ]
});

App.InfraStructure.GruposById.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infragrupo/getByGrupos'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'infra_other_data_attribute_id',
    totalProperty: 'total',
    fields: [
        'infra_grupo_id',
        'infra_other_data_attribute_id',
        'infra_other_data_attribute_name',
        'infra_other_data_attribute_type'
    ]
});


App.InfraStructure.Node.Store = new Ext.data.JsonStore({
    url: 'index.php/core/nodecontroller/get',
    root: 'results',
    idProperty: 'node_id',
    fields: [
        'node_id',
        'node_name',
        'level',
    ],
    sortInfo: {
        field: 'node_name'
    },
    listeners: {
        'update': function(store) {
            store.sort('node_name', 'ASC');
        }
    }
});

App.InfraStructure.Info.Store = new Ext.data.JsonStore({
    url: 'index.php/infra/infrainfo/get',
    root: 'resultsInfraInfo',
    idProperty: 'field',
    fields: [
        'field',
        'label',
        'value'
    ]
});

App.InfraStructure.InfoConfigNuevo.Store = new Ext.data.JsonStore({
    url: 'index.php/infra/infrainfo/getConfi',
    root: 'resultsInfraInfo',
    idProperty: 'field',
    fields: [
        'field',
        {
            name: 'label',
            convert: function stringMeasure(v, record) {
                if (record.sumary == 1) {
                    msg = ' <b>(Ver en Ficha)</b>'
                } else {
                    msg = ''
                }
                return record.label + msg;
            }
        },
        'value'
    ]
});

App.InfraStructure.InfoConfig.Store = new Ext.data.JsonStore({
    url: 'index.php/infra/infrainfoconfig/get',
    root: 'results',
    idProperty: 'field',
    fields: [
        'field',
        'label'
    ]
});

App.InfraStructure.InfoOption.Store = Ext.extend(Ext.data.Store, {
    url: 'index.php/infra/infrainfooption/get',
    reader: new Ext.data.JsonReader({
        root: 'results',
        totalProperty: 'total'
    }, [
        'infra_info_option_id',
        'infra_info_option_name'
    ])
});
Ext.reg('App.InfraStructure.InfoOption.Store', App.InfraStructure.InfoOption.Store);

App.InfraStructure.OtrosDatos.Store = new Ext.data.JsonStore({
    url: 'index.php/infra/infraotherdatavalue/get',
    root: 'results',
    totalProperty: 'total',
    idProperty: 'infra_other_data_attribute_id',
    fields: [
        'infra_other_data_attribute_id',
        'infra_other_data_attribute_type',
        'value',
        'label'
    ]
});
App.InfraStructure.OtrosDatosResumen.Store = new Ext.data.JsonStore({

    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infraotherdatavalue/getResumen'

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
    fields: [
        'value',
        'label'


    ],
    sortInfo: {
        field: 'label',
        direction: 'ASC' // or 'DESC' (case sensitive for local sorting)
    }
});

App.InfraStructure.OtrosDatosInfoOption.Store = Ext.extend(Ext.data.Store, {
    url: 'index.php/infra/infraotherdataoption/get',
    reader: new Ext.data.JsonReader({
        root: 'results',
        totalProperty: 'total'
    }, [
        'infra_other_data_option_id',
        'infra_other_data_option_name'
    ])
});
Ext.reg('App.InfraStructure.OtrosDatosInfoOption.Store', App.InfraStructure.OtrosDatosInfoOption.Store);

//Usado para el grid de los attributos
App.InfraStructure.DatosDinamicos.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infraotherdataattribute/get',
            create: 'index.php/infra/infraotherdataattribute/add',
            update: 'index.php/infra/infraotherdataattribute/update',
            destroy: 'index.php/infra/infraotherdataattribute/delete'
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
    idProperty: 'infra_other_data_attribute_id',
    fields: [
        'infra_other_data_attribute_id',
        'infra_other_data_attribute_name',
        'infra_other_data_attribute_type',
        'infra_grupo_id',
        'InfraGrupo',
        {
            name: 'infra_other_data_attribute_type_text',
            convert: function attributeType(v, record) {
                var types = {
                    "1": App.Language.General.text,
                    "2": App.Language.General.number,
                    "3": App.Language.General.decimal,
                    "4": App.Language.General.date,
                    "5": App.Language.General.selection,
                    "6": App.Language.General.not_editable,
                    "7": App.Language.General.checkbox
                };
                return types[record.infra_other_data_attribute_type];
            }
        }
    ]
});

App.InfraStructure.DatosDinamicos.SearchStore = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infraotherdataattribute/getsearch'
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
    idProperty: 'infra_other_data_attribute_id',
    fields: [
        'infra_other_data_attribute_id',
        'infra_other_data_attribute_name',
        'infra_other_data_attribute_type',
        {
            name: 'infra_other_data_attribute_type_text',
            convert: function attributeType(v, record) {
                var types = {
                    "1": App.Language.General.text,
                    "2": App.Language.General.number,
                    "3": App.Language.General.decimal,
                    "4": App.Language.General.date,
                    "5": App.Language.General.selection
                };
                return types[record.infra_other_data_attribute_type];
            }
        }
    ]
});

App.InfraStructure.DatosDinamicosDisponibles.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infraotherdataattribute/get'
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'infra_other_data_attribute_id',
    fields: [
        'infra_other_data_attribute_id',
        'infra_other_data_attribute_name'
    ]
});

App.InfraStructure.DatosDinamicosAsociados.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infraotherdataattributenodetype/get',
            create: 'index.php/infra/infraotherdataattributenodetype/add',
            update: 'index.php/infra/infraotherdataattributenodetype/update',
            destroy: 'index.php/infra/infraotherdataattributenodetype/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'infra_other_data_attribute_id',
    fields: [
        'infra_other_data_attribute_id',
        {
            name: 'infra_other_data_attribute_name',
            convert: function stringMeasure(v, record) {

                if (record.infra_other_data_attribute_node_type_the_sumary == 1) {
                    msg = ' <b>(Ver en Ficha)</b>';
                } else {
                    msg = '';
                }
                return record.InfraOtherDataAttribute.infra_other_data_attribute_name + msg;
            }
        }


    ]
});

App.InfraStructure.DatosDinamicosSeleccion.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infraotherdataoption/get',
            create: 'index.php/infra/infraotherdataoption/add',
            update: 'index.php/infra/infraotherdataoption/update',
            destroy: 'index.php/infra/infraotherdataoption/delete'
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
    idProperty: 'infra_other_data_option_id',
    fields: [
        'infra_other_data_option_id',
        'infra_other_data_attribute_id',
        'infra_other_data_option_name'
    ]
});

App.InfraStructure.Search.Store = new Ext.data.JsonStore({
    url: 'index.php/core/nodecontroller/search',
    root: 'results',
    idProperty: 'node_id',
    baseParams: {
        depth: 0
    },
    fields: [
        'node_id',
        'node_name',
        'icon',
        'node_type_name',
        'node_type_category_name',
        'node_root'
    ]
});

App.InfraStructure.InfoOptionCombosAnidados.Store = Ext.extend(Ext.data.Store, {
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infrainfooption/get',
            update: 'index.php/infra/infrainfooption/update',
            destroy: 'index.php/infra/infrainfooption/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    reader: new Ext.data.JsonReader({
        root: 'results',
        totalProperty: 'total',
        idProperty: 'infra_info_option_id'
    }, [
        'infra_info_option_id',
        'infra_info_option_name'
    ])
});

App.InfraStructure.Coordinate.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/infra/infracoordinate/get',
            create: 'index.php/infra/infracoordinate/edit',
            update: 'index.php/infra/infracoordinate/edit',
            destroy: 'index.php/infra/infracoordinate/edit'
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
    idProperty: 'node_id',
    fields: [
        'node_id',
        'node_longitude',
        'node_latitude'
    ]
});

App.InfraStructure.Iot.Store = new Ext.data.JsonStore({
    url: 'index.php/iot/iot/getDevice',
    root: 'results',
    idProperty: 'infra_iot_device',
    fields: [

        'id',
        'name'
    ]
});



App.InfraStructure.Coordinate.updateCoordinate = function(lat, lng) {
    App.InfraStructure.Coordinate.Store.setBaseParam('action', 'update');
    record = App.InfraStructure.Coordinate.Store.getAt(0);
    record.beginEdit();
    record.set('node_latitude', lat);
    record.set('node_longitude', lng);
    record.endEdit();
}

App.InfraStructure.Coordinate.addCoordinate = function(lat, lng) {
    var defaultData = {
        node_latitude: lat,
        node_longitude: lng,
        node_id: App.Interface.selectedNodeId
    }
    App.InfraStructure.Coordinate.Store.setBaseParam('action', 'add');
    var u = new App.InfraStructure.Coordinate.Store.recordType(defaultData);
    App.InfraStructure.Coordinate.Store.insert(0, u);
}




App.InfraStructure.InfoOptionCombosAnidados1.Store = new App.InfraStructure.InfoOptionCombosAnidados.Store;
App.InfraStructure.InfoOptionCombosAnidados2.Store = new App.InfraStructure.InfoOptionCombosAnidados.Store;
App.InfraStructure.InfoOptionCombosAnidados3.Store = new App.InfraStructure.InfoOptionCombosAnidados.Store;
App.InfraStructure.InfoOptionCombosAnidados4.Store = new App.InfraStructure.InfoOptionCombosAnidados.Store;