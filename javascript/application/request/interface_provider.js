//App.Interface.addToModuleMenu('request', 
//{
//    xtype: 'button',
//    text: App.Language.Request.requests,
//    iconCls: 'request_icon_32',
//    scale: 'large',
//    iconAlign: 'top',
//    module: 'Request',
//    listeners: 
//    {
//        'beforerender': function()
//        {
//            App.Request.Provider.Store.load();
//        }
//    }
//});
//App.Request.allowRootGui = true;
//
//App.Request.Principal = Ext.extend(Ext.Panel, 
//{
//    title: App.Language.Request.requests,
//    border: false,
//    loadMask: true,
//    layout: 'border',
//    tbar: 
//    [{
//        xtype: 'spacer',
//        width: 5
//    }, {
//        text: App.Language.General.search,
//        iconCls: 'search_icon_16',
//        enableToggle: true,
//        handler: function(b)
//        {
//            if (b.ownerCt.ownerCt.form.isVisible()) 
//            {
//                b.ownerCt.ownerCt.form.hide();
//            } else {
//                b.ownerCt.ownerCt.form.show();
//            }
//            b.ownerCt.ownerCt.doLayout();
//        }
//    }, {
//        xtype: 'tbseparator',
//        width: 10
//    }, {
//        text: App.Language.Request.approve,
//        iconCls: 'approve_icon',
//        handler: function(b)
//        {
//            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Request.do_you_want_to_Approve_the_request, function(b)
//            {
//                if (b == 'yes') 
//                {
//                    grid = Ext.getCmp('App.Request.Grid');
//                    if (grid.getSelectionModel().getCount()) 
//                    {
//                        records = Ext.getCmp('App.Request.Grid').getSelectionModel().getSelections();
//                        aux = new Array();
//                        record_array = new Array();
//                        for (var i = 0; i < records.length; i++) 
//                        {
//                            aux.push(records[i].data.request_id);
//                        }
//                        record_array = aux.join(',');
//                        App.Request.RequestAproved(2, record_array);
//                    } else {
//                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
//                    }
//                }
//            });
//        }
//    }, {
//        xtype: 'spacer',
//        width: 10
//    }, {
//        text: App.Language.Request.reject,
//        iconCls: 'delete_icon',
//        handler: function(b)
//        {
//            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Request.do_you_want_to_reject_the_request, function(b)
//            {
//                if (b == 'yes') 
//                {
//                    grid = Ext.getCmp('App.Request.Grid');
//                    if (grid.getSelectionModel().getCount()) 
//                    {
//                        records = Ext.getCmp('App.Request.Grid').getSelectionModel().getSelections();
//                        aux = new Array();
//                        record_array = new Array();
//                        for (var i = 0; i < records.length; i++) 
//                        {
//                            aux.push(records[i].data.request_id);
//                        }
//                        record_array = aux.join(',');
//                        App.Request.RequestReject(3, record_array);
//                    } else {
//                        Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
//                    }
//                }
//            });
//        }
//    }, {
//        xtype: 'spacer',
//        width: 10
//    }, {
//        text: App.Language.General.eexport,
//        iconCls: 'export_icon',
//        handler: function()
//        {
//            Ext.Ajax.request
//            ({
//                waitMsg: App.Language.General.message_generating_file,
//                url: 'index.php/request/request/exportProvider',
//                method: 'POST',
//                params: 
//                {
//                    request_status_id: Ext.getCmp('App.Request.form.request_status_id').getValue(),
//                    request_problem_id: Ext.getCmp('App.Request.form.request_problem_id').getValue(),
//                    start_date: Ext.getCmp('start_date').getValue(),
//                    end_date: Ext.getCmp('end_date').getValue()
//                },
//                success: function(response)
//                {
//                    response = Ext.decode(response.responseText);
//                    document.location = response.file;
//                },
//                failure: function(response)
//                {
//                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
//                }
//            });
//        }
//    }],
//    initComponent: function()
//    {
//        this.items = 
//        [{
//            xtype: 'form',
//            region: 'north',
//            id: 'App.Request.FormCentral',
//            plugins: [new Ext.ux.OOSubmit()],
//            title: App.Language.General.searching,
//            frame: true,
//            ref: 'form',
//            hidden: true,
//            height: 150,
//            margins: '5 5 0 5',
//            padding: '5 5 5 5',
//            border: true,
//            fbar: 
//            [{
//                text: App.Language.General.search,
//                handler: function(b)
//                {
//                    form = b.ownerCt.ownerCt.getForm();
//                    node_id = App.Request.Provider.Store.baseParams.node_id;
//                    App.Request.Provider.Store.baseParams = form.getSubmitValues();
//                    App.Request.Provider.Store.setBaseParam('node_id', node_id);
//                    App.Request.Provider.Store.load();
//                }
//            }, {
//                text: App.Language.General.clean,
//                handler: function(b)
//                {
//                    form = b.ownerCt.ownerCt.getForm();
//                    node_id = App.Request.Provider.Store.baseParams.node_id;
//                    form.reset();
//                    App.Request.Provider.Store.setBaseParam([]);
//                    App.Request.Provider.Store.setBaseParam('node_id', node_id);
//                    App.Request.Provider.Store.load();
//                }
//            }],
//            items: 
//            [{
//                layout: 'column',
//                /*-------------COMBOS-------------*/
//                //id: 'column_form_column_start_date',
//                items: 
//                [{
//                    columnWidth: .5,
//                    layout: 'form',
//                    items: 
//                    [{
//                        xtype: 'combo',
//                        fieldLabel: App.Language.General.state,
//                        triggerAction: 'all',
//                        anchor: '95%',
//                        store: App.Request.Status.Store,
//                        hiddenName: 'request_status_id',
//                        id: 'App.Request.form.request_status_id',
//                        displayField: 'request_status_name',
//                        valueField: 'request_status_id',
//                        editable: false,
//                        mode: 'remote',
//                        minChars: 0,
//                        listeners: 
//                        {
//                            'afterrender': function(cb)
//                            {
//                                cb.__value = cb.value;
//                                cb.setValue('');
//                                cb.getStore().load
//                                ({
//                                    callback: function()
//                                    {
//                                        cb.setValue(cb.__value);
//                                    }
//                                });
//                            }
//                        }
//                    }, {
//                        xtype: 'combo',
//                        fieldLabel: App.Language.Request.problem,
//                        triggerAction: 'all',
//                        anchor: '95%',
//                        store: App.Request.Problem.Store,
//                        hiddenName: 'request_problem_id',
//                        displayField: 'request_problem_name',
//                        id: 'App.Request.form.request_problem_id',
//                        valueField: 'request_problem_id',
//                        editable: false,
//                        mode: 'remote',
//                        minChars: 0,
//                        listeners: 
//                        {
//                            'afterrender': function(cb)
//                            {
//                                cb.__value = cb.value;
//                                cb.setValue('');
//                                cb.getStore().load
//                                ({
//                                    callback: function()
//                                    {
//                                        cb.setValue(cb.__value);
//                                    }
//                                });
//                            }
//                        }
//                    }]
//                }, {
//                    columnWidth: .5,
//                    layout: 'form',
//                    id: 'form_column_start_date',
//                    items: 
//                    [{
//                        columnWidth: .2,
//                        layout: 'form',
//                        items:
//                        [{
//                            xtype: 'label',
//                            text: App.Language.Request.select_a_date_range_to_for_the_request
//                        }]
//                    }, {
//                        columnWidth: .4,
//                        layout: 'column',
//                        id: 'column_start_date',
//                        frame: true,
//                        items: 
//                        [{
//                            columnWidth: .5,
//                            layout: 'form',
//                            id: 'column_start_date1',
//                            items: 
//                            [{
//                                xtype: 'datefield',
//                                id: 'start_date',
//                                ref: '../start_date',
//                                fieldLabel: App.Language.General.start_date,
//                                name: 'start_date',
//                                anchor: '95%',
//                                listeners: 
//                                {
//                                    'select': function(fd, date)
//                                    {
//                                        fd.ownerCt.ownerCt.end_date.setMinValue(date);
//                                    }
//                                }
//                            }]
//                        }, {
//                            columnWidth: .5,
//                            layout: 'form',
//                            items: 
//                            [{
//                                xtype: 'datefield',
//                                id: 'end_date',
//                                ref: '../end_date',
//                                fieldLabel: App.Language.General.end_date,
//                                name: 'end_date',
//                                anchor: '95%',
//                                listeners: 
//                                {
//                                    'select': function(fd, date)
//                                    {
//                                        fd.ownerCt.ownerCt.start_date.setMaxValue(date);
//                                    }
//                                }
//                            }]
//                        }]
//                    }, {
//                        xtype: 'spacer',
//                        height: 15
//                    }]
//                }]
//            }]
//        }, {
//            xtype: 'grid',
//            id: 'App.Request.Grid',
//            margins: '5 5 5 5',
//            plugins: [new Ext.ux.OOSubmit()],
//            region: 'center',
//            border: true,
//            loadMask: true,
//            listeners: 
//            {
//                'rowdblclick': function(grid, rowIndex)
//                {
//                    w = new App.Request.editRequestWindow
//                    ({
//                        title: App.Language.General.details
//                    });
//                    w.form.record = grid.getStore().getAt(rowIndex);
//                    w.form.asset_name.setValue(w.form.record.data.Asset.asset_name);
//                    w.form.request_problem_name.setValue(w.form.record.data.RequestProblem.request_problem_name);
//                    w.form.getForm().loadRecord(grid.getStore().getAt(rowIndex));
//                    w.show();
//                }
//            },
//            viewConfig: 
//            {
//                forceFit: true
//            },
//            store: App.Request.Provider.Store,
//            columns: [new Ext.grid.CheckboxSelectionModel(), 
//            {
//                dataIndex: 'request_folio',
//                header: App.Language.Request.request_n,
//                sortable: true,
//                width: 100,
//                renderer: function(request_folio, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + request_folio + '"';
//                    return request_folio;
//                }
//            }, {
//                dataIndex: 'Asset',
//                header: App.Language.General.asset,
//                sortable: true,
//                width: 100,
//                renderer: function(Asset, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + Asset.asset_name + '"';
//                    return Asset.asset_name;
//                }
//            }, {
//                dataIndex: 'Asset',
//                header: App.Language.Core.location,
//                sortable: true,
//                width: 100,
//                renderer: function(Asset, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + Asset.asset_path + '"';
//                    return Asset.asset_path;
//                }
//            }, {
//                dataIndex: 'RequestProblem',
//                header: App.Language.Request.problem,
//                sortable: true,
//                width: 100,
//                renderer: function(RequestProblem, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + RequestProblem.request_problem_name + '"';
//                    return RequestProblem.request_problem_name;
//                }
//            }, {
//                dataIndex: 'request_subject',
//                header: App.Language.Request.subject,
//                sortable: true,
//                width: 100,
//                renderer: function(request_subject, metadata, record, rowIndex, colIndex, store)
//                {
//                    metadata.attr = 'ext:qtip="' + request_subject + '"';
//                    return request_subject;
//                }
//            }, {
//                xtype: 'datecolumn',
//                header: App.Language.General.creation_date,
//                dataIndex: 'request_date_creation',
//                sortable: true,
//                format: App.General.DefaultDateFormat,
//                align: 'center'
//            }, {
//                dataIndex: 'RequestStatus',
//                header: App.Language.General.state,
//                sortable: true,
//                width: 100,
//                renderer: function(RequestStatus)
//                {
//                    return RequestStatus.request_status_name;
//                }
//            }],
//            sm: new Ext.grid.CheckboxSelectionModel({
//                singleSelect: true
//            })
//        }], 
//        App.Request.Principal.superclass.initComponent.call(this);
//    }
//});
//
//App.Request.Principal.listener = function(node)
//{
//    if (node && node.id) 
//    {
//        App.Request.Provider.Store.setBaseParam('node_id', node.id);
//        App.Request.Provider.Store.load();
//    }
//};
//
//App.Request.editRequestWindow = Ext.extend(Ext.Window, 
//{
//    width: 450,
//    height: 400,
//    modal: true,
//    resizable: false,
//    layout: 'fit',
//    padding: 1,
//    initComponent: function()
//    {
//        this.items = 
//        [{
//            xtype: 'form',
//            ref: 'form',
//            padding: 5,
//            items: 
//            [{
//                xtype: 'fieldset',
//                title: App.Language.General.asset,
//                items: 
//                [{
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.Request.request_n,
//                    name: 'request_folio',
//                    anchor: '100%',
//                    allowBlank: false
//                }, {
//                    xtype: 'hidden',
//                    name: 'asset_id',
//                    ref: '../asset_id'
//                }, {
//                    xtype: 'displayfield',
//                    ref: '../asset_name',
//                    fieldLabel: App.Language.General.asset,
//                    anchor: '100%'
//                }, {
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.Request.subject,
//                    name: 'request_subject',
//                    anchor: '100%',
//                    allowBlank: false
//                }, {
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.General.description,
//                    name: 'request_description',
//                    anchor: '100%',
//                    allowBlank: false
//                }, {
//                    xtype: 'hidden',
//                    name: 'request_problem_id',
//                    ref: '../request_problem_id'
//                }, {
//                    xtype: 'displayfield',
//                    ref: '../request_problem_name',
//                    fieldLabel: App.Language.Request.failure,
//                    anchor: '100%'
//                }]
//            }, {
//                xtype: 'fieldset',
//                title: App.Language.Request.applicant_details,
//                items: 
//                [{
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.General.requested_by,
//                    name: 'request_requested_by',
//                    anchor: '100%',
//                    allowBlank: false
//                }, {
//                    xtype: 'displayfield',
//                    fieldLabel: App.Language.General.contact,
//                    name: 'request_requested_by_comment',
//                    anchor: '100%',
//                    allowBlank: false
//                }]
//            }],
//            buttons: 
//            [{
//                text: App.Language.General.close,
//                handler: function(b)
//                {
//                    b.ownerCt.ownerCt.ownerCt.hide();
//                }
//            }]
//        }];
//        App.Request.editRequestWindow.superclass.initComponent.call(this);
//    }
//});
//
//App.Request.RequestAproved = function(request_status_id, record_array)
//{
//    Ext.Ajax.request
//    ({
//        url: 'index.php/request/request/update',
//        params: 
//        {
//            request_id: record_array,
//            request_status_id: request_status_id
//        },
//        method: 'POST',
//        success: function(result, request)
//        {
//            json = Ext.decode(result.responseText);
//            if (json.success == 'true') 
//            {
//                Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
//            } else {
//                Ext.MessageBox.alert(App.Language.General.warning, json.msg);
//            }
//            App.Request.Provider.Store.load(); //--store--
//        },
//        failure: function(result, request)
//        {
//            Ext.MessageBox.alert(App.Language.General.error, result.msg);
//        }
//    });
//}
//
//App.Request.RequestReject = function(request_status_id, record_array)
//{
//    Ext.Ajax.request
//    ({
//        url: 'index.php/request/request/update',
//        params: 
//        {
//            request_id: record_array,
//            request_status_id: request_status_id
//        },
//        method: 'POST',
//        success: function(result, request)
//        {
//            json = Ext.decode(result.responseText);
//            if (json.success == 'true') 
//            {
//                Ext.MessageBox.alert(App.Language.General.message_success, json.msg);
//            } else {
//                Ext.MessageBox.alert(App.Language.General.warning, json.msg);
//            }
//            App.Request.Provider.Store.load();//--store--
//        },
//        failure: function(result, request)
//        {
//            Ext.MessageBox.alert(App.Language.General.error, result.msg);
//        }
//    });
//}
