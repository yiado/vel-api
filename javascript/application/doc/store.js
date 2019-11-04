/* global App, Ext */

App.Document.CutProxy = function(doc_document_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/doc/document/edit',
        params: {
            node_id: App.Interface.selectedNodeId,
            doc_document_id: doc_document_id,
            action: 'cut'
        },
        success: successCallBack
    });
};

App.Document.PasteProxy = function(node_parent_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/doc/document/edit',
        params: {
            node_parent_id: node_parent_id,
            action: 'paste'
        },
        success: successCallBack
    })
};

App.Document.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/document/get',
            create: 'index.php/doc/document/add',
            update: 'index.php/doc/document/update'
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
            name: 'doc_version_internal',
            mapping: 'DocCurrentVersion.doc_version_internal',
            dateFormat: 'Y-m-d'
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
        }, {
            name: 'doc_version_keyword',
            mapping: 'DocCurrentVersion.doc_version_keyword'
        }, {
            name: 'doc_version_comments',
            mapping: 'DocCurrentVersion.doc_version_comments'
        }, {
            name: 'user_name',
            mapping: 'DocCurrentVersion.User.user_name'
        },
        'doc_path'
    ],
    listeners: {
        'save': function() {
            this.load();
        }
    }
});

App.Document.Papelera.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/document/getBin',
            destroy: 'index.php/doc/document/delete'
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
    ]
});

App.Document.Vencido.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/document/getDocumentoVencido'
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
    idProperty: 'doc_version_id',
    fields: [
        'doc_version_id',
        'user_id',
        'doc_document_id',
        'doc_version_code',
        'doc_version_code_client',
        'doc_current_version_id',
        'doc_version_alert',
        {
            name: 'doc_extension_name',
            mapping: 'DocDocument.DocExtension.doc_extension_name'
        },
        {
            name: 'doc_document_filename',
            mapping: 'DocDocument.doc_document_filename'
        },
        {
            name: 'doc_category_name',
            mapping: 'DocDocument.DocCategory.doc_category_name'
        }, {
            type: 'date',
            name: 'doc_document_creation',
            mapping: 'DocDocument.doc_document_creation',
            dateFormat: 'Y-m-d H:i:s'
        }, {
            type: 'date',
            name: 'doc_version_expiration',
            dateFormat: 'Y-m-d'
        }, {
            name: 'doc_path',
            mapping: 'DocDocument.doc_path'
        }
    ]
});

App.Document.Version.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/docversion/get',
            create: 'index.php/doc/docversion/add',
            update: 'index.php/doc/docversion/update',
            destroy: 'index.php/doc/docversion/delete'
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
        //encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'doc_version_id',
    fields: [
        'doc_version_id',
        'DocDocument',
        'doc_version_code',
        'doc_version_filename',
        'doc_version_code_client',
        'doc_version_comments',
        'doc_image_web',
        'doc_version_keyword',
        'doc_version_alert',
        'doc_version_alert_email',
        'doc_version_notification_email',
        {
            type: 'date',
            name: 'doc_version_internal',
            dateFormat: App.General.DefaultSystemDate
        }, {
            type: 'date',
            name: 'doc_version_expiration',
            dateFormat: App.General.DefaultSystemDate
        }, {
            type: 'date',
            name: 'doc_version_creation',
            dateFormat: App.General.DefaultSystemDateTime
        }, {
            name: 'user_name',
            mapping: 'User.user_name'
        }, {
            name: 'doc_document_description',
            mapping: 'DocDocument.doc_document_description'
        }
    ]
});

App.Document.Extension.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/docextension/get',
            create: 'index.php/doc/docextension/add',
            update: 'index.php/doc/docextension/update',
            destroy: 'index.php/doc/docextension/delete'
        }
    }),
    root: 'results',
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    totalProperty: 'total',
    idProperty: 'doc_extension_id',
    fields: [
        'doc_extension_id',
        'doc_extension_name',
        'doc_extension_extension'
    ]
});

App.Document.Categoria.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/doc/doccategory/get',
            create: 'index.php/doc/doccategory/add',
            update: 'index.php/doc/doccategory/update',
            destroy: 'index.php/doc/doccategory/delete'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'doc_category_id',
    fields: [
        'doc_category_id',
        'doc_category_name',
        'doc_category_description'
    ]
});