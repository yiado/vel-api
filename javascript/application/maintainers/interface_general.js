/* global App, Ext */

App.Maintainers.addToModuleMenu('general', {
    xtype: 'button',
    text: App.Language.General.general,
    iconCls: 'general_icon_32',
    scale: 'large',
    module: 'General',
    iconAlign: 'top'
});

App.Maintainers.General.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    title: App.Language.General.general,
    initComponent: function() {
        this.items = [{
                xtype: 'panel',
                title: App.Language.General.transaction_management,
                border: false,
                layout: 'border',
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.eexport,
                        iconCls: 'export_icon',
                        handler: function() {
                            w = new App.Maintainers.exportListWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'tbseparator',
                        width: 10
                    }, {
                        text: App.Language.General.search,
                        iconCls: 'search_icon_16',
                        enableToggle: true,
                        bodyStyle: 'padding:5px 5px 0',
                        handler: function(b) {
                            if (b.ownerCt.ownerCt.form.isVisible()) {
                                b.ownerCt.ownerCt.form.hide();
                            } else {
                                b.ownerCt.ownerCt.form.show();
                            }
                            b.ownerCt.ownerCt.doLayout();
                        }
                    }]
                },
                items: [{
                    xtype: 'form',
                    labelWidth: 150,
                    region: 'north',
                    margins: '5 5 0 5',
                    plugins: [new Ext.ux.OOSubmit()],
                    title: App.Language.General.searching,
                    frame: true,
                    ref: 'form',
                    hidden: true,
                    height: 180,
                    fbar: [{
                        text: App.Language.General.search,
                        handler: function(b) {
                            form = b.ownerCt.ownerCt.getForm();
                            App.Core.Log.Store.baseParams = form.getSubmitValues();
                            App.Core.Log.Store.load();
                        }
                    }, {
                        text: App.Language.General.clean,
                        handler: function(b) {
                            form = b.ownerCt.ownerCt.getForm();
                            form.reset();
                            App.Core.Log.Store.setBaseParam([]);
                            App.Core.Log.Store.load();
                        }
                    }],
                    items: [{
                        layout: 'column',
                        items: [{
                            columnWidth: .5,
                            layout: 'form',
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: App.Language.General.user,
                                id: 'App.Log.form.user_name',
                                anchor: '80%',
                                name: 'user_name'
                            }, {
                                xtype: 'combo',
                                fieldLabel: App.Language.General.type_of_action,
                                id: 'App.Log.form.log_type_description',
                                store: App.Core.TypeDescription.Store,
                                hiddenName: 'log_type_description',
                                triggerAction: 'all',
                                displayField: 'log_type_description',
                                valueField: 'log_type_description',
                                editable: true,
                                selecOnFocus: true,
                                typeAhead: true,
                                anchor: '80%',
                                selectOnFocus: true,
                                mode: 'remote',
                                minChars: 0
                            }, {
                                xtype: 'textfield',
                                fieldLabel: App.Language.General.description,
                                id: 'App.Log.form.log_description',
                                anchor: '80%',
                                name: 'log_description'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: App.Language.General.ip,
                                id: 'App.Log.form.ip',
                                anchor: '80%',
                                name: 'log_ip'
                            }]
                        }, {
                            columnWidth: .5,
                            layout: 'form',
                            items: [{
                                columnWidth: .2,
                                layout: 'form',
                                items: [{
                                    xtype: 'label',
                                    text: App.Language.General.select_date_range_for_which_to_search
                                }]
                            }, {
                                columnWidth: 1,
                                layout: 'column',
                                frame: true,
                                items: [{
                                    bodyStyle: 'margin-right: 50px;',
                                    layout: 'form',
                                    items: [{
                                        xtype: 'datefield',
                                        ref: '../start_date',
                                        id: 'start_date',
                                        fieldLabel: App.Language.General.start_date,
                                        value: new Date().add(Date.DAY, -5),
                                        name: 'start_date',
                                        anchor: '95%',
                                        listeners: {
                                            'select': function(fd, date) {
                                                fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                            }
                                        }
                                    }]
                                }, {
                                    layout: 'form',
                                    items: [{
                                        xtype: 'datefield',
                                        ref: '../end_date',
                                        id: 'end_date',
                                        fieldLabel: App.Language.General.end_date,
                                        value: new Date().add(Date.DAY, 0),
                                        name: 'end_date',
                                        anchor: '95%',
                                        listeners: {
                                            'select': function(fd, date) {
                                                fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }]
                    }]
                }, {
                    xtype: 'grid',
                    loadMask: true,
                    store: App.Core.Log.Store,
                    plugins: [new Ext.ux.OOSubmit()],
                    region: 'center',
                    margins: '5 5 5 5',
                    viewConfig: {
                        forceFit: true
                    },
                    listeners: {
                        'rowdblclick': function(grid) {
                            log_id = grid.getSelectionModel().getSelected().id;
                            App.Core.LogDetail.Store.setBaseParam('log_id', log_id);
                            w = new App.Maintainers.LogDetail();
                            w.show();
                        }
                    },
                    columns: [{
                        dataIndex: 'user_name',
                        header: App.Language.General.user,
                        sortable: true,
                        width: 130
                    }, {
                        dataIndex: 'log_type_description',
                        header: App.Language.General.type_of_action,
                        sortable: true,
                        width: 170
                    }, {
                        dataIndex: 'log_description',
                        header: App.Language.General.description,
                        sortable: true,
                        width: 345,
                        renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                            metadata.attr = 'ext:qtip="' + value + '"';
                            return value;
                        }
                    }, {
                        xtype: 'datecolumn',
                        header: App.Language.General.date,
                        align: 'center',
                        sortable: true,
                        dataIndex: 'log_date_time',
                        width: 70,
                        format: App.General.DefaultDateTimeFormat
                    }, {
                        dataIndex: 'log_ip',
                        header: App.Language.General.ip,
                        align: 'center',
                        sortable: true,
                        width: 65
                    }]
                }]
            }, {
                xtype: 'grid',
                title: App.Language.General.type_currency,
                store: App.Core.Currency.Store,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.General.OpenEditModeCurrency(record);
                    },
                    'beforerender': function(grid) {
                        grid.store.load();
                    }
                },
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        xtype: 'button',
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            w = new App.Maintainers.General.AddCurrencyWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'tbseparator'
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = b.ownerCt.ownerCt;
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.Core.Currency.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                            }
                        }
                    }]
                },
                columns: [
                    new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'currency_name',
                        header: App.Language.General.name,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'currency_code',
                        header: App.Language.General.code,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'currency_equivalence',
                        header: App.Language.General.equivalence,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }, {
                xtype: 'grid',
                title: App.Language.General.measurement_unit,
                store: App.Core.MeasureUnit.Store,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.Maintainers.General.OpenEditModeMeasureUnit(record);
                    },
                    'beforerender': function(grid) {
                        grid.store.load();
                    }
                },
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        xtype: 'button',
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            w = new App.Maintainers.General.AddMeasureUnitWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'tbseparator'
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = b.ownerCt.ownerCt;
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.Core.MeasureUnit.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                            }
                        }
                    }]
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'measure_unit_name',
                        header: App.Language.General.name,
                        sortable: true,
                        width: 100
                    }, {
                        xtype: 'gridcolumn',
                        dataIndex: 'measure_unit_description',
                        header: App.Language.General.description,
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel()
            }, {
                xtype: 'grid',
                title: App.Language.General.brand,
                id: 'App.BrandGrid',
                store: App.Brand.Store,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'rowdblclick': function(grid, rowIndex) {
                        record = grid.getStore().getAt(rowIndex);
                        App.BrandEditMode(record);
                    },
                    'beforerender': function() {
                        App.Brand.Store.load();
                    }
                },
                columns: [new Ext.grid.CheckboxSelectionModel(),
                    {
                        xtype: 'gridcolumn',
                        header: App.Language.General.brand,
                        dataIndex: 'brand_name',
                        sortable: true,
                        width: 100
                    }
                ],
                sm: new Ext.grid.CheckboxSelectionModel(),
                tbar: {
                    xtype: 'toolbar',
                    items: [{
                        text: App.Language.General.add,
                        iconCls: 'add_icon',
                        handler: function() {
                            w = new App.addBrandWindow();
                            w.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 10
                    }, {
                        xtype: 'button',
                        text: App.Language.General.ddelete,
                        iconCls: 'delete_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.BrandGrid');
                            if (grid.getSelectionModel().getCount()) {
                                Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function(b) {
                                    if (b == 'yes') {
                                        grid.getSelectionModel().each(function(record) {
                                            App.Brand.Store.remove(record);
                                        });
                                    }
                                });
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                            }
                        }
                    }]
                }
            }
        ];
        App.Maintainers.General.Principal.superclass.initComponent.call(this);
    }
});

App.Maintainers.General.AddCurrencyWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_currency,
    resizable: false,
    modal: true,
    width: 450,
    height: 260,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'currency_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.code,
                anchor: '100%',
                name: 'currency_code'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.equivalence,
                anchor: '100%',
                name: 'currency_equivalence'
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.character_decimal,
                anchor: '100%',
                name: 'currency_decimal_character',
                minChars: 0,
                maxLength: 1,
                allowBlank: false
            }, {
                xtype: 'textfield',
                fieldLabel: App.Language.General.character_thousands,
                anchor: '100%',
                name: 'currency_thousands_character',
                minChars: 0,
                maxLength: 1,
                allowBlank: false
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.General.number_decimal,
                anchor: '100%',
                name: 'currency_number_of_decimal',
                minChars: 0,
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/core/currency/add',
                            success: function(fp, o) {
                                App.Core.Currency.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.General.AddCurrencyWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.General.OpenEditModeCurrency = function(record) {
    w = new App.Maintainers.General.AddCurrencyWindow({
        title: App.Language.General.edit_type_currency
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Maintainers.General.AddMeasureUnitWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add_type_measurement,
    resizable: false,
    modal: true,
    width: 450,
    height: 180,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'measure_unit_name',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }, {
                xtype: 'textarea',
                fieldLabel: App.Language.General.description,
                name: 'measure_unit_description',
                anchor: '100%',
                minChars: 0,
                allowBlank: false
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/core/measureunit/add',
                            success: function(fp, o) {
                                App.Core.MeasureUnit.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Maintainers.General.AddMeasureUnitWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.General.OpenEditModeMeasureUnit = function(record) {
    w = new App.Maintainers.General.AddMeasureUnitWindow({
        title: App.Language.General.edit_type_measurement
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Core.MeasureUnit.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.BrandEditMode = function(record) {
    w = new App.addBrandWindow({
        title: App.Language.Asset.edit_brand
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Brand.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.addBrandWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.add,
    resizable: false,
    modal: true,
    width: 380,
    height: 150,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                name: 'brand_name',
                anchor: '100%',
                allowBlank: false

            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.add,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/core/brand/add',
                            success: function(fp, o) {
                                App.Brand.Store.load();
                                b.ownerCt.ownerCt.ownerCt.hide();
                                Ext.FlashMessage.alert(o.result.msg);
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.addBrandWindow.superclass.initComponent.call(this);
    }
});

App.Maintainers.LogDetail = Ext.extend(Ext.Window, {
    title: App.Language.General.log_details,
    resizable: false,
    modal: true,
    border: true,
    width: 550,
    height: 300,
    layout: 'fit',
    padding: 2,
    initComponent: function() {
        this.items = [{
            border: true,
            items: [{
                border: false,
                xtype: 'grid',
                store: App.Core.LogDetail.Store,
                height: 350,
                viewConfig: {
                    forceFit: true
                },
                listeners: {
                    'beforerender': function() {
                        App.Core.LogDetail.Store.load();
                    }
                },
                columns: [{
                    xtype: 'gridcolumn',
                    dataIndex: 'log_detail_param',
                    header: App.Language.General.field_name,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'log_detail_value_old',
                    header: App.Language.General.before,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'log_detail_value_new',
                    header: App.Language.General.after,
                    sortable: true,
                    width: 100
                }]
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }]
        }];
        App.Maintainers.LogDetail.superclass.initComponent.call(this);
    }
});

App.Maintainers.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.eexport,
    width: 400,
    height: 150,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            labelWidth: 130,
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                id: 'App.Log.form.file_name',
                anchor: '100%',
                name: 'file_name',
                maskRe: /^[a-zA-Z0-9_]/,
                regex: /^[a-zA-Z0-9_]/,
                allowBlank: false
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
                    Ext.Ajax.request({
                        waitMsg: App.Language.General.message_generating_file,
                        url: 'index.php/core/log/export',
                        method: 'POST',
                        params: {
                            file_name: Ext.getCmp('App.Log.form.file_name').getValue(),
                            user_name: Ext.getCmp('App.Log.form.user_name').getValue(),
                            log_ip: Ext.getCmp('App.Log.form.ip').getValue(),
                            log_type_description: Ext.getCmp('App.Log.form.log_type_description').getValue(),
                            log_description: Ext.getCmp('App.Log.form.log_description').getValue(),
                            start_date: Ext.getCmp('start_date').getValue(),
                            end_date: Ext.getCmp('end_date').getValue()
                        },
                        success: function(response) {
                            response = Ext.decode(response.responseText);
                            document.location = App.BaseUrl + 'index.php/app/download/' + response.file;
                            b.ownerCt.ownerCt.ownerCt.close();
                        },
                        failure: function(response) {
                            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                        }
                    });
                }
            }]
        }];
        App.Maintainers.exportListWindow.superclass.initComponent.call(this);
    }
});