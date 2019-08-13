App.Mtn.Wo.Triggers = Ext.extend(Ext.Panel, {
    title: App.Language.Maintenance.readings_settings,
    disabled: (App.Security.Actions[7005] === undefined ? true : false),
    border: false,
    layout: 'border',
    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            border: false,
            loadMask: true,
            region: 'center',
            id: 'App.Mtn.ConfigurationMaintenanceGrid',
            store: App.Asset.ConfigMeasurement.Store,
            viewConfig: {
                forceFit: true
            },
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    record = grid.getStore().getAt(rowIndex);
                    App.Mtn.MeasurenceOpenEditMode(record);
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'asset_type_name',
                    header: App.Language.Asset.asset_type,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'intervalo',
                    header: App.Language.Maintenance.range,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'notificacion_type',
                    header: App.Language.Maintenance.notification_type,
                    sortable: true,
                    width: 100
                }, {
                    xtype: 'gridcolumn',
                    dataIndex: 'asset_trigger_measurement_config_notificacion_mails',
                    header: App.Language.Asset.addressees,
                    sortable: true,
                    width: 100
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel(),
            tbar: {
                xtype: 'toolbar',
                items: [{
                    text: App.Language.Asset.new_configuration,
                    iconCls: 'add_icon',
                    handler: function() {
                        w = new App.Mtn.addConfigurationMeasureWindow();
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
                        grid = Ext.getCmp('App.Mtn.ConfigurationMaintenanceGrid');
                        if (grid.getSelectionModel().getCount()) {
                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete_option, function(b) {
                                if (b == 'yes') {
                                    grid.getSelectionModel().each(function(record) {
                                        App.Asset.ConfigMeasurement.Store.remove(record);
                                    });
                                }
                            });
                        } else {
                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                        }
                    }
                }]
            }
        }];
        App.Mtn.Wo.Triggers.superclass.initComponent.call(this);
    }
});

App.Mtn.addConfigurationMeasureWindow = Ext.extend(Ext.Window, {
    title: App.Language.Maintenance.add_configuration_reading,
    resizable: false,
    modal: true,
    padding: 1,
    layout: 'fit',
    width: 670,
    height: 400,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            height: 445,
            bodyStyle: 'padding:5px 10px 5',
            items: [{
                xtype: 'panel',
                height: 70,
                border: false,
                bodyStyle: 'padding:10px 20px 5',
                columnWidth: 1,
                layout: 'column',
                items: [{
                    xtype: 'label',
                    text: App.Language.Asset.asset_type,
                    columnWidth: 0.15
                }, {
                    xtype: 'spacer',
                    columnWidth: 0.05,
                    height: 2
                }, {
                    xtype: 'combo',
                    anchor: '80%',
                    store: App.Asset.Type.Store,
                    hiddenName: 'asset_type_id',
                    displayField: 'asset_type_name',
                    valueField: 'asset_type_id',
                    triggerAction: 'all',
                    id: 'App.Maintenance.AssetType',
                    columnWidth: 0.7,
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    allowBlank: false,
                    disabled: false,
                    mode: 'remote',
                    minChars: 0
                }]
            }, {
                xtype: 'fieldset',
                title: App.Language.Asset.when_registering_a_measurement,
                bodyStyle: 'padding:10px 20px 10',
                height: 80,
                items: [{
                    xtype: 'panel',
                    columnWidth: 1,
                    flex: 1,
                    border: false,
                    layout: 'column',
                    items: [{
                        xtype: 'label',
                        text: App.Language.Asset.operating_out_of_range,
                        columnWidth: 0.3
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.02,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        allowBlank: false,
                        name: 'rango1',
                        id: 'App.Maintenance.Rango1',
                        columnWidth: 0.1
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.01,
                        height: 5
                    }, {
                        xtype: 'label',
                        text: '--',
                        columnWidth: 0.02
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.01,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        allowBlank: false,
                        name: 'rango2',
                        id: 'App.Maintenance.Rango2',
                        columnWidth: 0.1
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.04,
                        height: 5
                    }, {
                        xtype: 'combo',
                        anchor: '100%',
                        id: 'App.Maintenance.MeasurementUnit',
                        store: App.Core.MeasureUnit.Store,
                        emptyText: App.Language.General.measurement_unit,
                        hiddenName: 'measure_unit_id',
                        triggerAction: 'all',
                        setReadOnly: true,
                        displayField: 'measure_unit_name_and_description',
                        valueField: 'measure_unit_id',
                        selecOnFocus: true,
                        typeAhead: true,
                        editable: true,
                        columnWidth: 0.4,
                        allowBlank: false,
                        mode: 'remote',
                        minChars: 0,
                        listeners: {
                            'afterrender': function(cb) {
                                cb.__value = cb.value;
                                cb.setValue('');
                                cb.getStore().load({
                                    callback: function() {
                                        cb.setValue(cb.__value);
                                    }
                                });
                            }
                        }
                    }]
                }]
            }, {
                xtype: 'fieldset',
                title: App.Language.Core.notification,
                ref: 'fieldset',
                bodyStyle: 'padding: 10 20px 10',
                height: 150,
                items: [{
                    xtype: 'panel',
                    ref: 'panel_central',
                    border: false,
                    items: [{
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'panel',
                        columnWidth: 1,
                        border: false,
                        ref: 'panel_mail',
                        layout: 'column',
                        items: [{
                            xtype: 'label',
                            text: App.Language.Asset.mail_addresses_separated_by_commas,
                            anchor: '100%',
                            columnWidth: 0.44,
                            name: 'node_name'
                        }, {
                            xtype: 'spacer',
                            columnWidth: 0.01,
                            height: 5
                        }, {
                            xtype: 'textfield',
                            name: 'asset_trigger_measurement_config_notificacion_mails',
                            ref: 'target',
                            allowBlank: false,
                            disabled: true,
                            columnWidth: 0.55
                        }]
                    }, {
                        xtype: 'spacer',
                        height: 20
                    }, {
                        xtype: 'checkbox',
                        boxLabel: App.Language.Asset.send_email,
                        anchor: '100%',
                        triggerAction: 'all',
                        ref: 'mail_checkbox',
                        name: 'type_config_mail',
                        inputValue: '1',
                        columnWidth: 0.4,
                        listeners: {
                            'check': function(rb, status) {
                                var chk_mail = rb.ownerCt.sms_checkbox.getValue();
                                if (chk_mail == false && status == false) {
                                    status = true;
                                } else {
                                    status = false;
                                }
                                rb.ownerCt.panel_mail.target.setDisabled(status);
                            }
                        }
                    }, {
                        xtype: 'spacer',
                        height: 10
                    }, {
                        xtype: 'checkbox',
                        boxLabel: App.Language.Asset.send_text_messages_to_cell,
                        anchor: '100%',
                        ref: 'sms_checkbox',
                        name: 'type_config_sms',
                        inputValue: '2',
                        columnWidth: 0.4,
                        listeners: {
                            'check': function(rb, status) {
                                var chk_sms = rb.ownerCt.mail_checkbox.getValue();
                                if (chk_sms == false && status == false) {
                                    status = true;
                                } else {
                                    status = false;
                                }
                                rb.ownerCt.panel_mail.target.setDisabled(status);
                            }
                        }
                    }]
                }]
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
                            url: 'index.php/asset/assettriggermeasurementconfig/add',
                            success: function(fp, o) {
                                App.Asset.ConfigMeasurement.Store.load();
                                b.ownerCt.ownerCt.ownerCt.close();
                                Ext.MessageBox.alert(App.Language.Core.notification, o.result.msg);
                            },
                            failure: function(fp, o) {
                                Ext.MessageBox.alert(App.Language.Core.notification, o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.Mtn.addConfigurationMeasureWindow.superclass.initComponent.call(this);
    }
});

App.Mtn.MeasurenceOpenEditMode = function(record) {
    w = new App.Mtn.addConfigurationMeasureWindow({
        title: App.Language.Asset.editing_the_configuration_of_reading
    });
    w.form.saveButton.setText(App.Language.General.edit);
    if (record.data.asset_trigger_measurement_config_notificacion_method == 1) {
        w.form.fieldset.panel_central.mail_checkbox.setValue(true);
        w.form.fieldset.panel_central.panel_mail.target.setDisabled(false);
    } else if (record.data.asset_trigger_measurement_config_notificacion_method == 2) {
        w.form.fieldset.panel_central.sms_checkbox.setValue(true);
        w.form.fieldset.panel_central.panel_mail.target.setDisabled(false);
    } else if (record.data.asset_trigger_measurement_config_notificacion_method == 3) {
        w.form.fieldset.panel_central.mail_checkbox.setValue(true);
        w.form.fieldset.panel_central.sms_checkbox.setValue(true);
        w.form.fieldset.panel_central.panel_mail.target.setDisabled(false);
    }
    w.form.getForm().loadRecord(record);
    w.form.record = record;
    Ext.getCmp('App.Maintenance.AssetType').setDisabled(true);
    Ext.getCmp('App.Maintenance.MeasurementUnit').setDisabled(true);
    Ext.getCmp('App.Maintenance.Rango1').setValue(record.data.asset_trigger_measurement_config_start);
    Ext.getCmp('App.Maintenance.Rango2').setValue(record.data.asset_trigger_measurement_config_end);
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.submit({
                url: 'index.php/asset/assettriggermeasurementconfig/update',
                params: {
                    asset_trigger_measurement_config_id: record.data.asset_trigger_measurement_config_id,
                    asset_type_id: record.data.asset_type_id,
                    measure_unit_id: record.data.measure_unit_id
                },
                waitMsg: App.Language.General.message_guarding_information,
                success: function(form, response) {
                    App.Asset.ConfigMeasurement.Store.load();
                    w.form.ownerCt.close();
                },
                failure: function(fp, o) {
                    alert('Error:\n' + o.result.msg);
                }
            });
        }
    };
    w.show();
}