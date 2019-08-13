App.Core.MtnMaintainerType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/provider/getTypeMaintainer'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'mtn_maintainer_type_id',
    fields: [
        'mtn_maintainer_type_id',
        'mtn_maintainer_type_name'
    ]
});

App.Core.ProviderTypeAll.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/provider/getAllType'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'provider_id',
    fields: [
        'provider_id',
        'mtn_maintainer_type_id',
        'provider_name'
    ]
});

App.Core.MeasureUnit.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/measureunit/get',
            create: 'index.php/core/measureunit/add',
            update: 'index.php/core/measureunit/update',
            destroy: 'index.php/core/measureunit/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'measure_unit_id',
    fields: [
        'measure_unit_id',
        'measure_unit_name',
        'measure_unit_description',
        {
            name: 'measure_unit_name_and_description',
            convert: function stringMeasure(v, record) {
                return record.measure_unit_name + ' (' + record.measure_unit_description + ')';
            }
        }
    ]
});

App.Core.ProviderType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/providertype/get',
            create: 'index.php/core/providertype/add',
            update: 'index.php/core/providertype/update',
            destroy: 'index.php/core/providertype/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'provider_type_id',
    fields: [
        'provider_type_id',
        'provider_type_name',
        'provider_type_description'
    ]
});
App.Core.ProviderTypeByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/providertype/getByNode',
            create: 'index.php/core/providertype/addByNode',
            update: 'index.php/core/providertype/update',
            destroy: 'index.php/core/providertype/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'provider_type_id',
    fields: [
        'provider_type_id',
        'provider_type_name',
        'provider_type_description'
    ]
});

App.Core.Provider.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/provider/get',
            create: 'index.php/core/provider/add',
            update: 'index.php/core/provider/update',
            destroy: 'index.php/core/provider/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'provider_id',
    fields: [
        'provider_id',
        'provider_type_id',
        'provider_name',
        'provider_contact',
        'provider_phone',
        'provider_fax',
        'provider_email',
        'provider_description',
        {
            name: 'provider_type_name',
            mapping: 'ProviderType.provider_type_name'
        }
    ]
});

App.Core.ProviderByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/provider/getByNode',
            create: 'index.php/core/provider/addByNode',
            update: 'index.php/core/provider/update',
            destroy: 'index.php/core/provider/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'provider_id',
    fields: [
        'provider_id',
        'provider_type_id',
        'provider_name',
        'provider_contact',
        'provider_phone',
        'provider_fax',
        'provider_email',
        'provider_description',
        {
            name: 'provider_type_name',
            mapping: 'ProviderType.provider_type_name'
        }
    ]
});

App.Core.ProviderByNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/provider/getByNode',
            create: 'index.php/core/provider/addByNode',
            update: 'index.php/core/provider/update',
            destroy: 'index.php/core/provider/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'provider_id',
    fields: [
        'provider_id',
        'provider_type_id',
        'provider_name',
        'provider_contact',
        'provider_phone',
        'provider_fax',
        'provider_email',
        'provider_description',
        {
            name: 'provider_type_name',
            mapping: 'ProviderType.provider_type_name'
        }
    ]
});

App.Core.ProviderByType.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/contract/getByProvider'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'contract_id',
    fields: [
        'contract_id',
        'provider_id',
        'contract_description',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }
    ]
});

App.Core.User.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/user/get',
            create: 'index.php/core/user/add',
            update: 'index.php/core/user/update',
            destroy: 'index.php/core/user/status'
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
    idProperty: 'user_id',
    fields: [
        'user_id',
        'user_name',
        'user_username',
        'user_password',
        'user_password',
        'user_email',
        'user_type_name',
        'language_id',
        'user_default_module',
        'user_preference',
        {
            name: 'user_tree_full',
            convert: function strinUserFull(v, record) {
                return (record.user_tree_full == '1' ? true : false);
            }
        }, {
            name: 'user_tree_full_type',
            convert: function strinUserFullType(v, record) {
                return (record.user_tree_full == '1' ? App.Language.General.yes : '');
            }
        }, {
            name: 'user_type',
            convert: function strinUserType(v, record) {
                return (record.user_type == 'A' ? true : false);
            }
        }, {
            name: 'user_provider',
            convert: function strinUserType(v, record) {
                return (record.user_type == 'P' ? true : false);
            }
        }, {
            name: 'user_type_system',
            convert: function strinUserTypeSystem(v, record) {
                return (record.user_type == 'A' ? App.Language.General.yes : '');
            }
        },
        'user_status',
        {
            type: 'date',
            name: 'user_expiration',
            dateFormat: App.General.DefaultSystemDate
        }, {
            name: 'user_string_groups',
            convert: function userJoinGroups(v, record) {
                var groups = [];
                Ext.each(record.UserGroupUser, function(item) {
                    groups[groups.length] = item.UserGroup.user_group_name;
                });
                return groups.join(', ');
            }
        }
    ]
});

App.Core.UserNotification.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/user/getNotification'
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
    idProperty: 'user_id',
    fields: [
        'user_id',
        'user_name',
        'user_username',
        'user_password',
        'user_password',
        'user_email',
        'user_type_name',
        'language_id',
        'user_default_module',
        {
            name: 'user_tree_full',
            convert: function strinUserFull(v, record) {
                return (record.user_tree_full == '1' ? true : false);
            }
        }, {
            name: 'user_tree_full_type',
            convert: function strinUserFullType(v, record) {
                return (record.user_tree_full == '1' ? App.Language.General.yes : '');
            }
        }, {
            name: 'user_type',
            convert: function strinUserType(v, record) {
                return (record.user_type == 'A' ? true : false);
            }
        }, {
            name: 'user_provider',
            convert: function strinUserType(v, record) {
                return (record.user_type == 'P' ? true : false);
            }
        }, {
            name: 'user_type_system',
            convert: function strinUserTypeSystem(v, record) {
                return (record.user_type == 'A' ? App.Language.General.yes : '');
            }
        },
        'user_status',
        {
            type: 'date',
            name: 'user_expiration',
            dateFormat: App.General.DefaultSystemDate
        }, {
            name: 'user_string_groups',
            convert: function userJoinGroups(v, record) {
                var groups = [];
                Ext.each(record.UserGroupUser, function(item) {
                    groups[groups.length] = item.UserGroup.user_group_name;
                });
                return groups.join(', ');
            }
        }
    ]
});


App.Core.User.ModuleStore = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/user/getModules'
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'provider_id',
    fields: [
        'module_id',
        'module_name'
    ]
});

App.Core.Currency.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/currency/get',
            create: 'index.php/core/currency/add',
            update: 'index.php/core/currency/update',
            destroy: 'index.php/core/currency/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'currency_id',
    fields: [
        'currency_id',
        'currency_name',
        'currency_code',
        'currency_equivalence',
        'currency_decimal_character',
        'currency_thousands_character',
        'currency_number_of_decimal'
    ]
});

App.Core.Groups.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/group/get',
            create: 'index.php/core/group/add',
            update: 'index.php/core/group/update',
            destroy: 'index.php/core/group/delete'
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
    idProperty: 'user_group_id',
    totalProperty: 'total',
    fields: [
        'user_group_id',
        'user_group_name'
    ]
});

App.Core.Modules.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/module/get',
            create: 'index.php/core/module/add',
            update: 'index.php/core/module/update',
            destroy: 'index.php/core/module/delete'
        }
    }),
    root: 'results',
    idProperty: 'module_id',
    totalProperty: 'total',
    fields: [
        'module_id',
        'module_name'
    ]
});

App.Core.Modules.Store.Front = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/module/get'
        }
    }),
    baseParams: {
        front: true
    },
    root: 'results',
    idProperty: 'module_id',
    totalProperty: 'total',
    fields: [
        'module_id',
        'module_name'
    ]
});

App.Core.Modules.Actions.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/module/getActionModule'
        }
    }),
    root: 'results',
    idProperty: 'module_action_id',
    totalProperty: 'total',
    fields: [
        'module_action_id',
        {
            name: 'language_tag_value',
            mapping: 'LanguageTag.language_tag_value'
        }
    ]
});

App.Core.Groups.Permissions.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/permissions/get'
        }
    }),
    root: 'results',
    idProperty: 'module_action_id',
    totalProperty: 'total',
    fields: [
        'module_action_id',
        {
            name: 'language_tag_value',
            mapping: 'ModuleAction.LanguageTag.language_tag_value'
        }
    ]
});

App.Core.Groups.Users.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/group/getUsers'
        }
    }),
    root: 'results',
    idProperty: 'user_group_user_id',
    totalProperty: 'total',
    fields: [
        'user_id',
        {
            name: 'user_name',
            mapping: 'User.user_name'
        }, {
            name: 'user_real_name_and_username',
            convert: function strinUserFullName(v, record) {
                return record.User.user_name + ' (' + record.User.user_username + ')';
            }
        }
    ]
});

App.Core.Groups.UsersOutside.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/group/usersOutsideGroup'
        }
    }),
    root: 'results',
    idProperty: 'user_id',
    totalProperty: 'total',
    fields: [
        'user_id',
        {
            name: 'user_real_name_and_username',
            convert: function strinUserFullName(v, record) {
                return record.user_name + ' (' + record.user_username + ')';
            }
        }
    ]
});

App.Core.User.Groups.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/user/groups'
        }
    }),
    root: 'results',
    idProperty: 'user_group_id',
    totalProperty: 'total',
    fields: [
        'user_group_id',
        'user_group_name'
    ]
});

App.Core.User.GroupsOutside.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/group/get'
        }
    }),
    root: 'results',
    idProperty: 'user_group_id',
    totalProperty: 'total',
    fields: [
        'user_group_id',
        'user_group_name'
    ]
});

App.Core.Languages.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/language/get',
            create: 'index.php/core/language/add',
            update: 'index.php/core/language/update',
            destroy: 'index.php/core/language/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'language_id',
    fields: [
        'language_id',
        'language_name',
        'language_default',
        {
            name: 'language_is_default',
            convert: function strinLanguageIsDefault(v, record) {
                return (record.language_default == 0 ? '' : App.Language.General.yes);
            }
        }
    ]
});

App.Core.LanguagesTag.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/languagetag/get',
            update: 'index.php/core/languagetag/update'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'language_tag_id',
    fields: [
        'language_tag_id',
        'language_id',
        'language_tag_value'
    ]
});

App.Core.Operators.Store = new Ext.data.ArrayStore({
    fields: [
        'operator_id',
        'value'
    ],
    data: [
        ['1', '>'],
        ['2', '<'],
        ['3', '='],
        ['4', '<>'],
        ['5', '>='],
        ['6', '<=']
    ]
});

App.Core.Number.Store = new Ext.data.ArrayStore({
    fields: [
        'operator_id',
        'value'
    ],
    data: [
        ['0', '0'],
        ['1', '1'],
        ['2', '2'],
        ['3', '3'],
        ['3', '3'],
        ['4', '4'],
        ['5', '5'],
        ['6', '6'],
        ['7', '7'],
        ['8', '8'],
        ['9', '9'],
        ['10', '10'],
        ['11', '11'],
        ['12', '12'],
        ['13', '13'],
        ['14', '14'],
        ['15', '15']
    ]
});

App.Brand.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/brand/get',
            create: 'index.php/core/brand/add',
            update: 'index.php/core/brand/update',
            destroy: 'index.php/core/brand/delete'
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
    idProperty: 'brand_id',
    fields: [
        'brand_id',
        'brand_name'
    ]
});

App.Core.UserFull.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/user/getAll'
        }
    }),
    root: 'results',
    idProperty: 'user_id',
    totalProperty: 'total',
    fields: [
        'user_id',
        'user_name',
        'user_username	'
    ]
});

App.Core.Contract.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/contract/get',
            create: 'index.php/core/contract/add',
            update: 'index.php/core/contract/update',
            destroy: 'index.php/core/contract/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'contract_id',
    fields: [
        'contract_id',
        'provider_id',
        'contract_description',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }, {
            type: 'date',
            name: 'contract_date_start',
            dateFormat: App.General.DefaultSystemDate
        }, {
            type: 'date',
            name: 'contract_date_finish',
            dateFormat: App.General.DefaultSystemDate
        }
    ]
});

App.Core.ContractNode.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/contract/getNode',
            create: 'index.php/core/contract/addNode'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'contract_id',
    fields: [
        'contract_id',
        'provider_id',
        'contract_description',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }, {
            type: 'date',
            name: 'contract_date_start',
            dateFormat: App.General.DefaultSystemDate
        }, {
            type: 'date',
            name: 'contract_date_finish',
            dateFormat: App.General.DefaultSystemDate
        }
    ]
});

App.Core.ContractAsset.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/contractasset/get',
            create: 'index.php/core/contractasset/add',
            update: 'index.php/core/contractasset/update',
            destroy: 'index.php/core/contractasset/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'contract_asset_id',
    fields: [
        'contract_asset_id',
        'asset_id',
        'contract_id',
        {
            name: 'asset_name',
            mapping: 'Asset.asset_name'
        }, {
            name: 'asset_type_name',
            mapping: 'Asset.AssetType.asset_type_name'
        }, {
            name: 'brand_name',
            mapping: 'Asset.Brand.brand_name'
        }
    ]
});

App.Core.ContractNodeAsociated.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/contractnode/get',
            create: 'index.php/core/contractnode/add',
            update: 'index.php/core/contractnode/update',
            destroy: 'index.php/core/contractnode/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'contract_node_id',
    fields: [
        'contract_node_id',
        'node_id',
        'contract_id',
        'node_name',
        'contract_node_path',
        'node_type_name'
    ]
});

App.Core.UserProvider.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/userprovider/get',
            create: 'index.php/core/userprovider/add',
            destroy: 'index.php/core/userprovider/delete'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'user_provider_id',
    fields: [
        'user_provider_id',
        'Provider',
        'User',
        'mtn_maintainer_type_id',
        {
            name: 'provider_name',
            mapping: 'Provider.provider_name'
        }, {
            name: 'user_name',
            mapping: 'User.user_name'
        }, {
            name: 'mtn_maintainer_type_name',
            mapping: 'Provider.MtnMaintainerType.mtn_maintainer_type_name'
        }
    ]
});


App.Core.SpecialProvider.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/userprovider/getProvider'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg)
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
    idProperty: 'user_id',
    fields: [
        'user_id',
        'user_name'
    ]
});

App.Core.Log.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/log/get'
        }
    }),

    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'log_id',
    fields: [
        'log_id',
        'log_ip',
        'log_description',
        'provider_email',
        'provider_description',
        {
            type: 'date',
            name: 'log_date_time',
            dateFormat: 'Y-m-d H:i:s'
        },
        {
            name: 'log_type_description',
            mapping: 'LogType.log_type_description'
        }, {
            name: 'user_name',
            mapping: 'User.user_name'
        }
    ]
});

App.Core.LogDetail.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/log/getByIdLog'
        }
    }),

    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'log_detail_id',
    fields: [
        'log_detail_id',
        'log_id',
        'log_detail_param',
        'log_detail_value_old',
        'log_detail_value_new'
    ]
});


App.Core.TypeDescription.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/log/getLogType'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'log_type_id',
    fields: [
        'log_type_id',
        'log_type_name',
        'log_type_description',
        'log_type_template'
    ]
});
App.Core.Help.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/core/help/get'
        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote' && response.raw.success == 'false') {
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
    idProperty: 'help_id',
    fields: [
        'help_id',
        'help_sort',
        'help_title',
        'help_url'
    ]
});