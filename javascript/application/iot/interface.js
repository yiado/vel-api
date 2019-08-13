App.Iot.allowRootGui = true;
//se agrega el modulo al menu
App.Interface.addToModuleMenu('iot', {
    //    xtype: 'button',
    iconCls: 'general_icon_32',
    text: App.Language.Iot.iot,

    //    scale: 'large',
    module: 'Iot',
    //    iconAlign: 'top'
});
//se Crea el tab principal
App.Iot.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [new App.Iot.PrincipalClase()];
        App.Iot.Principal.superclass.initComponent.call(this);
    }
});
App.Iot.Principal.listener = function(node) {
    if (node && node.id) {
        Ext.getCmp('App.Iot.Principal').deviceIot.removeAll();
        Ext.getCmp('App.Iot.Principal').deviceIot.doLayout();
        //        App.Iot.Device.Store.setBaseParam('node_id', node.id);
        //        App.Iot.Device.Store.load();

        if (typeof Ext.getCmp('App.Iot.Principal') != "undefined") {

            Ext.Ajax.request({
                url: 'index.php/iot/iot/getDeviceInfo',
                params: {
                    node_id: node.id
                },
                success: function(response) {

                    response = Ext.decode(response.responseText);
                    //                    console.log('>> response: ', response);
                    if (response.total > 0) {

                        var date_value = response.results[0]['node']['updated_at'].split("T");

                        if (date_value[1] != 'undefined') {
                            date_value[1] = date_value[1].split(".");
                            var value = date_value[0] + ' ' + date_value[1][0];
                        }

                        var date = new Date(value);
                        var d = date.getDate();
                        var m = date.getMonth() + 1; //Month from 0 to 11
                        var y = date.getFullYear();
                        var h = date.getHours();
                        var min = date.getMinutes();
                        var s = date.getSeconds();
                        response.results[0]['node']['updated_at'] = '' + (d <= 9 ? '0' + d : d) + '/' + (m <= 9 ? '0' + m : m) + '/' + y + ' ' + (h <= 9 ? '0' + h : h) + ':' + (min <= 9 ? '0' + min : min) + ':' + (s <= 9 ? '0' + s : s);

                        aux = new Ext.form.FieldSet({
                            title: 'Dispositivo',
                            layout: 'form',
                            collapsible: true,
                            anchor: '100%',
                            labelWidth: 70,
                            items: [{
                                    xtype: 'displayfield',
                                    fieldLabel: App.Language.General.name,
                                    value: response.results[0]['node']['manufacterName']
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: App.Language.Maintenance.model,
                                    value: response.results[0]['node']['modelName']
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: App.Language.General.description,
                                    value: response.results[0]['node']['description']
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'ID Unico',
                                    value: response.results[0]['node']['unique_id'],

                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Estatus',
                                    value: response.results[0]['node']['status'] === false ? 'Inactivo' : 'Activo',

                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Fecha de Actualización',
                                    value: response.results[0]['node']['updated_at']
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Nombre Aplicación',
                                    value: response.results[0]['node']['app_id'],
                                    hideLabel: response.results[0]['node']['node_type'] === 2 ? false : true,
                                    hidden: response.results[0]['node']['node_type'] === 2 ? false : true,
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Modulación',
                                    value: response.results[0]['node']['modulation'],
                                    hideLabel: response.results[0]['node']['node_type'] === 2 ? false : true,
                                    hidden: response.results[0]['node']['node_type'] === 2 ? false : true,
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Frecuencia',
                                    value: response.results[0]['node']['frequency'],
                                    hideLabel: response.results[0]['node']['node_type'] === 2 ? false : true,
                                    hidden: response.results[0]['node']['node_type'] === 2 ? false : true,
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Data Rate',
                                    value: response.results[0]['node']['data_rate'],
                                    hideLabel: response.results[0]['node']['node_type'] === 2 ? false : true,
                                    hidden: response.results[0]['node']['node_type'] === 2 ? false : true,
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Gateway ID',
                                    value: response.results[0]['node']['gateway_id'],
                                    hideLabel: response.results[0]['node']['node_type'] === 2 ? false : true,
                                    hidden: response.results[0]['node']['node_type'] === 2 ? false : true,
                                },
                            ]
                        });
                        if (node.id != 'root') {
                            Ext.getCmp('App.Iot.Principal').deviceIot.add(aux);
                            Ext.getCmp('App.Iot.Principal').deviceIot.doLayout();
                        }


                        if (response.results[0]['sensors'].length) {
                            var sensors = response.results[0]['sensors'];


                            auxDos = new Ext.form.FieldSet({
                                title: 'Sensores Asociados',
                                layout: 'form',
                                collapsible: true,

                            });
                            var tabs = new Ext.TabPanel({

                                activeTab: 0,
                                height: 900,
                                border: true,
                                id: 'App.Iot.Tabs',
                                listeners: {
                                    'tabchange': function(cb) {
                                        //                                                console.log('>>cb: ', cb);
                                        params = cb.activeTab.id.split("_");

                                        App.Iot.Sensors.Store.setBaseParam('element_id', params[1]);
                                        App.Iot.Sensors.Store.setBaseParam('device_id', params[2]);
                                        App.Iot.Sensors.Store.load({
                                            callback: function(records, operation, success, ) {
                                                    if (success == true) {
                                                        // create a checkbox for each task
                                                        var categories = [];
                                                        var data_sensors = [];
                                                        var units = records[0].data.unit;
                                                        var title = records[0].data.sensor_name;

                                                        App.Iot.Sensors.Store.each(function(record) {

                                                            data_sensors.push(record.data.data);
                                                            //                                                            var date_sensors = record.data.created_at.split(" ");
                                                            //                                                    date_sensors[1] = date_sensors[1].split(".");
                                                            categories.push(record.data.created_at);

                                                        });


                                                        Highcharts.chart('container' + params[1], {

                                                            title: {
                                                                text: title.toUpperCase()
                                                            },
                                                            subtitle: {
                                                                text: ''
                                                            },
                                                            yAxis: {
                                                                title: {
                                                                    text: units
                                                                }
                                                            },
                                                            xAxis: {
                                                                categories: categories,

                                                            },
                                                            legend: {
                                                                layout: 'vertical',
                                                                align: 'right',
                                                                verticalAlign: 'middle'
                                                            },
                                                            plotOptions: {
                                                                //                                                            series: {
                                                                //                                                                label: {
                                                                //                                                                    connectorAllowed: false
                                                                //                                                                },
                                                                ////                                                pointStart: 2010
                                                                //                                                            }
                                                            },
                                                            series: [{
                                                                name: 'Data',
                                                                label: false,
                                                                data: data_sensors
                                                            }],
                                                            responsive: {
                                                                rules: [{
                                                                    condition: {
                                                                        maxWidth: 500
                                                                    },
                                                                    chartOptions: {
                                                                        //                                                        legend: {
                                                                        //                                                            layout: 'horizontal',
                                                                        //                                                            align: 'center',
                                                                        //                                                            verticalAlign: 'bottom'
                                                                        //                                                        }
                                                                    }
                                                                }]
                                                            }

                                                        });



                                                    } else {
                                                        // the store didn't load, deal with it
                                                    }
                                                }
                                                // scope: this,
                                        });


                                    }
                                }
                            });

                            if (node.id != 'root') {

                                var categories = [];
                                var data_sensors = [];
                                sensors.forEach(function(element) {

                                    App.Iot.Sensors.Store.setBaseParam('element_id', element.id);
                                    App.Iot.Sensors.Store.setBaseParam('device_id', response.results[0]['node']['id']);
                                    App.Iot.Sensors.Store.load();




                                    tabs.add({
                                        title: element.name,
                                        id: 'tab_' + element.id + '_' + response.results[0]['node']['id'],
                                        //                                        iconCls: 'tabs',
                                        closable: true,

                                        items: [{
                                                xtype: 'spacer',
                                                height: 5
                                            },
                                            {
                                                xtype: 'panel',
                                                //                                                autoScroll: true,
                                                id: 'PanelSensors_' + element.id + '_' + response.results[0]['node']['id'],
                                                autoHeight: true,
                                                style: 'padding:5 10 5 10',
                                                border: true,
                                                title: 'Data',
                                                items: [{
                                                    xtype: 'grid',
                                                    id: 'gridSensors_' + element.id + '_' + response.results[0]['node']['id'],
                                                    height: 230,
                                                    anchor: '100%',
                                                    cls: 'clsGrid',
                                                    //                                                        region: 'center',
                                                    //                                                        layauot: 'fit',
                                                    //                                                        width: '100%',
                                                    //                                                        style: 'padding:5 10 5 10',
                                                    loadMask: true,
                                                    //                                                        maskDisabled: false,
                                                    border: false,
                                                    viewConfig: {
                                                        forceFit: true,
                                                        //                                                                    folderSort: true,

                                                    },
                                                    store: App.Iot.Sensors.Store,

                                                    columns: [{
                                                            header: 'Unidad',
                                                            sortable: true,
                                                            //                                                                width: '20%',
                                                            dataIndex: 'unit',
                                                            //                                                                renderer: function (val, metadata, record) {
                                                            //                                                                    return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                                                            //                                                                }
                                                        }, {
                                                            dataIndex: 'data',
                                                            header: 'Medición',
                                                            sortable: true,
                                                            //                                                                width: 150,
                                                            //                                                                renderer: function (doc_path, metadata, record, rowIndex, colIndex, store) {
                                                            //                                                                    metadata.attr = 'ext:qtip="' + doc_path + '"';
                                                            //                                                                    return doc_path;
                                                            //                                                                }
                                                        },
                                                        {
                                                            dataIndex: 'created_at',
                                                            header: 'Fecha de registro',
                                                            //                                                                sortable: true,

                                                            //                                                                renderer: function (doc_path, metadata, record, rowIndex, colIndex, store) {
                                                            //                                                                    metadata.attr = 'ext:qtip="' + doc_path + '"';
                                                            //                                                                    return doc_path;
                                                            //                                                                }
                                                        }
                                                    ]

                                                }, ]
                                            },
                                            {
                                                xtype: 'spacer',
                                                height: 5
                                            },
                                            {
                                                xtype: 'panel',
                                                autoScroll: true,
                                                border: true,
                                                autoHeight: true,
                                                style: 'padding:5 10 5 10',
                                                //                                                height: 'auto',
                                                title: 'Grafico',
                                                html: '<div id="container' + element.id + '" height="400" width="100%"></div>'

                                            }
                                        ]
                                    }).show();

                                });
                                //
                                //                                    Ext.getCmp('form_sensors').form_sensors.add(tabs);
                                //
                                //                                });
                                auxDos.add(tabs);

                                Ext.getCmp('App.Iot.Principal').deviceIot.add(auxDos);
                                Ext.getCmp('App.Iot.Principal').deviceIot.doLayout();


                            }


                        }

                    }
                }
            });
        }




    }
};
App.Iot.PrincipalClase = Ext.extend(Ext.Panel, {
    title: 'Sensores',
    id: 'App.Iot.Principal',
    border: false,
    loadMask: true,
    layout: 'border',
    //    tbar: App.Iot.TBar,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'deviceIot',
            anchor: '100%',
            width: '100%',
            region: 'center',
            plugins: [new Ext.ux.OOSubmit()],
            //                title: 'Datos',
            padding: 5,
            border: false,
            bodyStyle: 'overflowY: auto',
            listeners: {
                'render': function() {

                    Ext.getCmp('App.Iot.Principal').deviceIot.getEl();
                    //                                App.InfraStructure.OtrosDatos.Store.on('beforeload', function () {
                    //                                    Ext.getCmp('App.InfraStructure.Principal').otherdata.getTopToolbar().hide();
                    //                                    App.InfraStructure.OtrosDatosLoadMask = new Ext.LoadMask(Ext.getCmp('App.InfraStructure.Principal').otherdata.getEl(),
                    //                                            {
                    //                                                msg: App.Language.General.message_loading_information,
                    //                                                store: App.InfraStructure.OtrosDatos.Store
                    //                                            });
                    //                                    App.InfraStructure.OtrosDatosLoadMask.show();
                    //                                });
                },
                'destroy': function() {
                    //                                App.InfraStructure.OtrosDatos.Store.purgeListeners();
                }
            },
            tbar: {
                xtype: 'toolbar',
                hidden: false,
                //                            items: [App.ModuleActions[5004]]
            }
        }, ]
        App.Document.PrincipalClase.superclass.initComponent.call(this);
    }
});