Ext.namespace('App.Report.Store');
Ext.namespace('App.Report.UserGroup.Store');
Ext.namespace('App.Report.UserGroupPermitted.Store');

App.Report.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/report/report/get'
        }
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'report_id',
    totalProperty: 'total',
    fields: [
        'report_id',
        'report_name',
        'report_url',
        {
            name: 'module_name',
            mapping: 'Module.module_name'
        }
    ]
});


App.Report.UserGroup.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/report/report/getReportUserGroup'
        }
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'report_id',
    totalProperty: 'total',
    fields: [
        'user_group_id',
        'user_group_name'
    ]
});

App.Report.UserGroupPermitted.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/report/report/getReportUserGroupPermitted'
        }
    }),
    root: 'results',
    autoLoad: false,
    idProperty: 'report_id',
    totalProperty: 'total',
    fields: [
        'report_id',
        'user_group_id',
        'user_group_name'
    ]
});