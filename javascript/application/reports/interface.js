/* global App, Ext */

App.Report.selectedReportId = null;

App.Report.allowRootGui = true;
App.Interface.addToModuleMenu('report', App.ModuleActions[9000]);

App.Report.Principal = Ext.extend(Ext.Panel, {
    //    title: App.Language.Report.reports,
    id: 'App.Report.Principal',
    border: false,
    loadMask: true,
    layout: 'border',
    tbar: [{
        xtype: 'button',
        text: App.Language.General.report_permissions,
        hidden: (App.Security.Session.user_type === 'A' ? false : true),
        iconCls: 'lock_icon',
        handler: function() {
            grid = Ext.getCmp('App.Report.reportGrid');

            if (grid.getSelectionModel().getCount()) {
                w = new App.InfraStructure.ConfigGroupWindow();
                w.show();
            } else {
                Ext.FlashMessage.alert(App.Language.General.must_select_a_report);
            }
        }
    }],
    initComponent: function() {
        this.items = [{
                xtype: 'grid',
                ref: 'reportGrid',
                id: 'App.Report.reportGrid',
                margins: '5 5 5 5',
                region: 'center',
                height: 600,
                border: true,
                loadMask: true,
                viewConfig: {
                    forceFit: true
                },
                store: App.Report.Store,
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        header: App.Language.Report.name_report,
                        sortable: true,
                        dataIndex: 'report_name'
                    }, {
                        header: App.Language.Core.module,
                        sortable: true,
                        dataIndex: 'module_name'
                    }, {
                        header: App.Language.General.eexport,
                        sortable: true,
                        dataIndex: 'report_url',
                        align: 'center',
                        renderer: function(val, metadata, record) {
                            return "<a href='javascript:App.Report.openExportWindow(" + record.data.report_id + ")'>" + App.Language.General.eexport + "</a>";
                        }
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel({
                    singleSelect: true
                })
            }],
            App.Report.Principal.superclass.initComponent.call(this);
    }
});

App.Report.Principal.listener = function(node) {
    if (node && node.id) {
        App.Report.selectedReportId = App.Report.Store.setBaseParam('node_id', node.id);
        App.Report.Store.load();
    }
};

App.Report.openExportWindow = function(reporte_id) {
    record = App.Report.Store.getById(reporte_id);
    w = new App.Report.exportListWindow({
        url_to_send: record.data.report_url
    });
    w.form.file_name.setValue(record.data.report_name);
    w.show();
};

App.Report.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.eexport_list,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    height: 250,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                anchor: '100%',
                name: 'file_name',
                ref: 'file_name',
                maskRe: /^[a-zA-Z0-9_]/,
                regex: /^[a-zA-Z0-9_]/,
                allowBlank: false
            }, {
                xtype: 'radiogroup',
                fieldLabel: App.Language.General.output_type,
                columns: 1,
                items: [{
                    boxLabel: 'Excel',
                    name: 'output_type',
                    inputValue: 'e',
                    height: 25,
                    checked: true
                }]
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.eexport,
                handler: function(b) {
                    fp = b.ownerCt.ownerCt;
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            clientValidation: true,
                            timeout: 900000000000000,
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.General.message_generating_file,
                            url: b.ownerCt.ownerCt.ownerCt.url_to_send,
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            success: function(form, response) {
                                document.location = 'index.php/app/download/' + response.result.file;
                                b.ownerCt.ownerCt.ownerCt.close();
                            },
                            failure: function(form, action) {

                                switch (action.failureType) {

                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                        b.ownerCt.ownerCt.ownerCt.close();
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                        b.ownerCt.ownerCt.ownerCt.close();
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                        b.ownerCt.ownerCt.ownerCt.close();
                                }
                            }
                        });
                    }
                }
            }]
        }];
        App.InfraStructure.exportListWindow.superclass.initComponent.call(this);
    }
});

App.InfraStructure.ConfigGroupWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.report_permissions,
    resizable: false,
    modal: true,
    border: true,
    width: 752,
    height: 370,
    layout: 'fit',
    padding: 2,
    listeners: {
        'beforerender': function() {
            grid = Ext.getCmp('App.Report.reportGrid').getSelectionModel().getSelections();
            report_id = grid[0].data.report_id;
            App.Report.UserGroup.Store.setBaseParam('report_id', grid[0].data.report_id);
            App.Report.UserGroup.Store.load();
            App.Report.UserGroupPermitted.Store.setBaseParam('report_id', grid[0].data.report_id);
            App.Report.UserGroupPermitted.Store.load();

        }
    },
    initComponent: function() {
        this.items = [{

            height: '100%',
            border: false,
            items: [{
                xtype: 'form',
                ref: 'formPermissionsGroup',
                labelWidth: 150,
                padding: 5,
                items: [{
                    xtype: 'panel',
                    border: false,
                    items: [{
                        xtype: 'itemselector',
                        name: 'permissionsToGroup',
                        imagePath: 'javascript/extjs/ux/images/',
                        drawUpIcon: false,
                        drawDownIcon: false,
                        drawTopIcon: false,
                        drawBotIcon: false,
                        multiselects: [{
                            width: 350,
                            height: 270,
                            store: App.Report.UserGroup.Store,
                            valueField: 'user_group_id',
                            displayField: 'user_group_name',
                            legend: App.Language.General.available_groups
                        }, {
                            width: 350,
                            height: 270,
                            store: App.Report.UserGroupPermitted.Store,
                            valueField: 'user_group_id',
                            maxLength: 255,
                            displayField: 'user_group_name',
                            legend: App.Language.General.groups_associated
                        }]
                    }]
                }],
                buttons: [{
                    text: App.Language.General.close,
                    handler: function(b) {
                        b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                    }
                }, {
                    text: App.Language.General.save,
                    ref: '../saveButton',
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.getForm();
                        if (form.isValid()) {
                            form.submit({

                                url: 'index.php/report/report/add',
                                params: {
                                    report_id: report_id
                                },
                                success: function(fp, o) {
                                    Ext.FlashMessage.alert(o.result.msg);
                                    b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                },
                                failure: function(fp, o) {
                                    Ext.MessageBox.alert(App.Language.General.error, o.result.msg);
                                }
                            });
                        }
                    }
                }]
            }]

        }];
        App.InfraStructure.ConfigGroupWindow.superclass.initComponent.call(this);
    }
});