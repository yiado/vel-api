App.Asset.Measurement.GridPanel = Ext.extend(Ext.grid.GridPanel, {
    title: App.Language.Asset.measurement,
    id: 'App.Asset.Measurement.Grid',
    store: App.Asset.Measurement.Store,
    loadMask: true,
    listeners: {
        'render': function() {
            this.store.setBaseParam('asset_id', App.Asset.selectedAssetId);
            this.store.load();
        },
        'rowdblclick': function(grid, rowIndex) {
            record = grid.getStore().getAt(rowIndex);
            App.Asset.AssetMeasurementEditMode(record);
        }
    },
    viewConfig: {
        forceFit: true
    },
    tbar: {
        xtype: 'toolbar',
        items: [{
            text: App.Language.General.add,
            iconCls: 'add_icon',
            handler: function(b) {
                w = new App.Asset.Measurement.formWindow({
                    title: App.Language.Asset.add_measurement
                });
                w.form.saveButton.setText(App.Language.General.add);
                w.form.saveButton.handler = function(bb) {
                    form = w.form.getForm();
                    if (form.isValid()) {
                        var u = new App.Asset.Measurement.Store.recordType(w.form.getForm().getSubmitValues());
                        u.set('asset_id', App.Asset.selectedAssetId);
                        App.Asset.Measurement.Store.insert(0, u);
                        bb.ownerCt.ownerCt.ownerCt.close();
                        Ext.getCmp('App.Asset.Measurement.Grid').fireEvent('render', Ext.getCmp('App.Asset.Measurement.Grid'));

                    } else {
                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_required_fields);
                    }
                };
                w.show();
            }
        }, {
            xtype: 'spacer',
            width: 5
        }, {
            text: App.Language.General.ddelete,
            iconCls: 'delete_icon',
            handler: function(b) {
                grid = b.ownerCt.ownerCt;
                if (grid.getSelectionModel().getCount()) {
                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete,
                        function(b) {
                            if (b == 'yes') {
                                grid.getSelectionModel().each(function(record) {
                                    Ext.Ajax.request({
                                        url: 'index.php/asset/assetmeasurement/delete',
                                        params: {
                                            asset_measurement_id: record.data.asset_measurement_id
                                        },
                                        success: function(response) {
                                            App.Asset.Measurement.Store.load();
                                            Ext.getCmp('App.Asset.Measurement.Grid').fireEvent('render', Ext.getCmp('App.Asset.Measurement.Grid'));
                                        }
                                    });


                                });
                            }
                        });
                } else {
                    Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                }
            }
        }]
    },
    initComponent: function() {
        this.selModel = new Ext.grid.CheckboxSelectionModel({
            checkOnly: false
        });
        this.columns = [
            this.selModel,
            {
                header: App.Language.Asset.measurement,
                dataIndex: 'asset_measurement_cantity',
                renderer: function(value, metaData, record) {
                    return value + ' ' + record.data.measure_unit_name;
                }
            }, {
                xtype: 'datecolumn',
                header: App.Language.General.date,
                dataIndex: 'asset_measurement_date',
                format: App.General.DefaultDateFormat
            }, {
                header: App.Language.General.comment,
                dataIndex: 'asset_measurement_comments'
            }
        ];
        App.Asset.Measurement.GridPanel.superclass.initComponent.call(this);
    }
});

App.Asset.Measurement.formWindow = Ext.extend(Ext.Window, {
    width: 400,
    height: 250,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            padding: 5,
            plugins: [new Ext.ux.OOSubmit()],
            items: [{
                xtype: 'hidden',
                name: 'asset_measurement_id'
            }, {
                xtype: 'numberfield',
                fieldLabel: App.Language.General.value,
                name: 'asset_measurement_cantity',
                allowBlank: false,
                anchor: '100%'
            }, {
                xtype: 'combo',
                fieldLabel: App.Language.General.unit,
                anchor: '100%',
                triggerAction: 'all',
                store: App.Core.MeasureUnit.Store,
                hiddenName: 'measure_unit_id',
                displayField: 'measure_unit_name_and_description',
                valueField: 'measure_unit_id',
                editable: true,
                typeAhead: true,
                selectOnFocus: true,
                forceSelection: true,
                allowBlank: false,
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'datefield',
                fieldLabel: App.Language.General.date,
                name: 'asset_measurement_date',
                anchor: '100%',
                allowBlank: false
            }, {
                xtype: 'textarea',
                anchor: '100%',
                fieldLabel: App.Language.General.comment,
                name: 'asset_measurement_comments'
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                xtype: 'button',
                ref: '../saveButton'
            }]
        }];
        App.Asset.Measurement.formWindow.superclass.initComponent.call(this);
    }
});



App.Asset.AssetMeasurementEditMode = function(record) {
    w = new App.Asset.Measurement.formWindow({
        title: App.Language.Asset.edit_measurement_title
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.saveButton.handler = function() {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Asset.Measurement.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}