/* global Ext, App, google, parseFloat, parseFloatoat */

App.InfraStructure.copiedNodes = new Array();
App.InfraStructure.allowRootGui = true;
App.InfraStructure.OtrosDatosComboTotal = 0;
App.InfraStructure.OtrosDatosLoadMask = null;
App.InfraStructure.SearchNodeBranches = null;
App.InfraStructure.pathFotoResumen = null;
App.InfraStructure.latResumen = null;
App.InfraStructure.lonResumen = null;
App.InfraStructure.activeTab = null;

busquedaInterna = null;
var markers = [];

function getMap() {
    markers = [];

    App.InfraStructure.Coordinate.Store.setBaseParam('search_branch', busquedaInterna);
    App.InfraStructure.Coordinate.Store.setBaseParam('active_tab', mapaParaEditar);
    App.InfraStructure.Coordinate.Store.load({
        callback: function(records) {

            if (typeof Ext.getCmp('App.InfraStructure.Principal') !== "undefined") {
                if (Ext.getCmp('App.InfraStructure.Principal').map) {
                    mapRef = mapaParaEditar;
                    if (App.InfraStructure.Coordinate.Store.getTotalCount()) {

                        if (mapRef) {
                            if (App.InfraStructure.activeTab == 'App.InfraStructure.fichaResumen' || App.InfraStructure.activeTab == 'App.InfraStructure.mapTab') {
                                if (mapRef == 'mapTab') {
                                    Ext.get('marker_add').setStyle('display', 'none');
                                    if (busquedaInterna) {
                                        Ext.get('marker_delete').setStyle('display', 'none');
                                    } else {
                                        Ext.get('marker_delete').setStyle('display', 'block');
                                    }

                                }



                                var institution = { lat: Number(records[0].data.node_latitude), lng: Number(records[0].data.node_longitude) };
                                var zoom = ((busquedaInterna) || (App.Interface.flat && mapRef == 'mapResumen')) ? 9 : 15;

                                map = new google.maps.Map(document.getElementById(mapRef), {
                                    center: institution,
                                    zoom: zoom
                                });
                                for (var i = 0; i < records.length; i++) {

                                    if (App.Interface.selectedNodeId == records[i].data.node_id) {
                                        App.InfraStructure.latResumen = records[i].data.node_latitude;
                                        App.InfraStructure.lonResumen = records[i].data.node_longitude;
                                    }
                                    var position = { lat: Number(records[i].data.node_latitude), lng: Number(records[i].data.node_longitude) };
                                    var marker = new google.maps.Marker({
                                        position: position,
                                        map: map,
                                        draggable: ((busquedaInterna) || (App.Interface.flat && mapRef == 'mapResumen')) ? false : true,
                                        title: records[i].json.node_name
                                    });

                                    markers.push(marker);
                                    var infowindow = new google.maps.InfoWindow();


                                    marker.addListener('dragend', function() {
                                        App.InfraStructure.Coordinate.updateCoordinate(marker.getPosition().lat(), marker.getPosition().lng());

                                    });


                                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                        return function() {

                                            var lat = marker.internalPosition.lat();
                                            var lng = marker.internalPosition.lng();

                                            Ext.Ajax.request({
                                                url: 'index.php/infra/infrainfo/get',
                                                params: {
                                                    node_id: App.Interface.selectedNodeId,
                                                    lat: lat,
                                                    lng: lng
                                                },
                                                success: function(response) {

                                                    response = Ext.decode(response.responseText);
                                                    html = '<h1>Información Resumen</h1><hr>'
                                                    html = html + '<div><table style=" padding: 4px; text-align: left;  font: 12 normal;  font: normal 11px tahoma, arial, helvetica, sans-serif;">';
                                                    for (i in response.resultsInfraOtherData) {
                                                        if (typeof response.resultsInfraOtherData[i] === 'object') {
                                                            for (x in response.resultsInfraOtherData[i].InfraOtherDataAttribute) {
                                                                if (x == 0 || x == 2 || x == 3 || x == 5 || x == 16 || x == 17 || x == 18 || x == 19) {
                                                                    record = response.resultsInfraOtherData[i].InfraOtherDataAttribute[x];
                                                                    if (typeof record === 'object') {
                                                                        field = App.InfraStructure.OtrosDatos.fields[Ext.util.Format.trim(record.infra_other_data_attribute_type)];
                                                                        if (record.InfraOtherDataValue[0]) {
                                                                            if (record.infra_other_data_attribute_type == 5) { //Tipo Combo
                                                                                field.value = record.InfraOtherDataValue[0].infra_other_data_option_id;
                                                                            } else {
                                                                                field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : '');
                                                                            }
                                                                        } else {
                                                                            field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : '');
                                                                        }

                                                                        if (field.value == null) {
                                                                            field.value = '';
                                                                        }
                                                                    }

                                                                }

                                                            }
                                                        }
                                                    }

                                                    for (i in response.resultsInfraInfo) {
                                                        record = response.resultsInfraInfo[i];
                                                        if (typeof record === 'object') {
                                                            if (i == 1 || i == 2 || i == 0) {
                                                                html = html + ' <tr><td style="padding-bottom: 5px;padding-right: 3px;">';
                                                                html = html + record.label + ": ";
                                                                html = html + '</td><td style="padding-bottom: 5px;">';
                                                                html = html + record.value;
                                                                html = html + ' </td></tr>';
                                                            }
                                                        }
                                                    }

                                                    html = html + "</div></table>";


                                                    infowindow.setContent(html);

                                                    infowindow.open(map, marker);
                                                }
                                            });
                                        };
                                    })(marker, i));
                                }
                            }

                        } else {

                            Ext.getCmp('App.InfraStructure.Principal').map.onMapReady = function() {
                                for (var i = 0; i < markers.length; i++) {
                                    markers[i].setMap(null);
                                }

                                var zoom = ((busquedaInterna) || (App.Interface.flat && mapRef == 'mapResumen')) ? 9 : 15;
                                map = new google.maps.Map(document.getElementById(mapRef), {
                                    center: institution,
                                    zoom: zoom
                                });
                                for (var i = 0; i < records.length; i++) {


                                    position_busqueda = { lat: Number(records[i].data.node_latitude), lng: Number(records[i].data.node_longitude) };
                                    var marker = new google.maps.Marker({
                                        position: position_busqueda,
                                        map: map,
                                        draggable: ((busquedaInterna) || (App.Interface.flat && mapRef == 'mapResumen')) ? false : true,
                                        title: records[i].json.node_name
                                    });

                                    markers.push(marker);

                                    marker.addListener('dragend', function() {
                                        App.InfraStructure.Coordinate.updateCoordinate(marker.getPosition().lat(), marker.getPosition().lng());

                                    });

                                }
                            };
                        }
                    } else {

                        if (mapRef) {
                            if (App.InfraStructure.activeTab == 'App.InfraStructure.fichaResumen' || App.InfraStructure.activeTab == 'App.InfraStructure.mapTab') {

                                if (mapRef == 'mapTab' && !busquedaInterna) {
                                    Ext.get('marker_add').setStyle('display', 'block');
                                }

                                if (mapRef == 'mapTab' || busquedaInterna) {
                                    Ext.get('marker_delete').setStyle('display', 'none');
                                }

                                var chile = { lat: -33.5533025, lng: -71.1164465 };

                                map = new google.maps.Map(document.getElementById(mapRef), {
                                    center: chile,
                                    zoom: 9
                                });
                            }
                        }


                    }
                }
            }
        }
    });
}

App.InfraStructure.treeSearchToolBar = [{
    xtype: 'textfield',
    id: 'App.InfraStructure.Search.TextBox',
    name: 'node_name',
    enableKeyEvents: true,
    listeners: {
        'keyup': function(tf, e) {
            if (e.keyCode == 13) {

                App.InfraStructure.Search.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                App.InfraStructure.Search.Store.setBaseParam('node_name', tf.getValue());
                App.InfraStructure.Search.Store.load();
                App.InfraStructure.searchWindowObject.form.hide();
                App.InfraStructure.searchWindowObject.resultGrid.show();
                App.InfraStructure.searchWindowObject.resultGrid.doLayout();
                App.InfraStructure.searchWindowObject.show();
            }
        }
    }
}, {
    xtype: 'spacer',
    width: (screen.width < 430) ? 2 : 5
}, {
    xtype: 'button',
    ref: '../filter_button',
    text: App.Language.General.filters,
    iconCls: 'filter_icon',
    handler: function(b) {
        App.InfraStructure.searchWindowObject.show();
    }
}, {
    xtype: 'spacer',
    width: (screen.width < 430) ? 2 : 5
}, {
    xtype: 'button',
    id: 'App.InfraStructure.buttonPath',
    tooltip: 'Establecer Ruta Inicial',
    iconCls: 'keep_add_icon',
    handler: function() {

        Ext.MessageBox.confirm(App.Language.General.confirmation, '¿Desea Marcar Como Ruta Inicial?', function(b) {
            if (b == 'yes') {
                Ext.Ajax.request({
                    waitMsg: App.Language.General.message_generating_file,
                    url: 'index.php/core/permissions/setUserPath',
                    timeout: 10000000000,
                    params: {
                        user_id: App.Security.Session.user_id,
                        user_path: App.Interface.selectedNodeId
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);
                        Ext.FlashMessage.alert(response.msg);
                    },
                    failure: function(response) {
                        Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                    }
                });
            }
        });
    }
}];

App.Interface.addToModuleMenu('infra', {
    text: App.Language.Infrastructure.infrastructure,
    iconCls: 'infrastructure_icon_32',
    module: 'InfraStructure'
});

App.InfraStructure.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    id: 'App.InfraStructure.Principal',
    resizeTabs: true,
    enableTabScroll: true,

    defaults: { autoScroll: true },
    listeners: {

        'tabchange': function(t, p) {
            App.InfraStructure.activeTab = t.activeTab.id;
            p.doLayout();

            App.InfraStructure.Coordinate.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
            if (p.id == 'App.InfraStructure.fichaResumen') {
                mapaParaEditar = 'mapResumen';
                getMap();
            } else if (p.id == 'App.InfraStructure.mapTab') {
                mapaParaEditar = 'mapTab';
                getMap();
            }

        }
    },
    initComponent: function() {
        this.selModel = new Ext.grid.CheckboxSelectionModel({
            checkOnly: false
        });
        this.items = [{
            border: false,
            title: 'Ficha Resumen',
            ref: 'ficha',
            id: 'App.InfraStructure.fichaResumen',
            bodyStyle: 'overflowY: auto',
            autoScroll: true,
            padding: 5,
            defaults: {
                margins: '0 0 5 0'
            },
            tbar: [{
                text: App.Language.General.printer,
                iconCls: 'print_icon',
                handler: function() {
                    document.location = 'index.php/plan/plan/imprimirResumen/' + App.Interface.selectedNodeId + "/" + App.Plan.CurrentPlanId + "/" + App.InfraStructure.pathFotoResumen + "/" + App.InfraStructure.latResumen + "/" + App.InfraStructure.lonResumen;
                }
            }],
            listeners: {
                'afterrender': function(panel) {
                    if ( App.Security.Actions['8015'] ) {
                        panel.add(
                            new App.InfraStructure.QRCode()
                        );
                    }
                    panel.add(
                        new App.InfraStructure.Foto()
                    );
                    panel.add({
                        xtype: 'panel',
                        height: 250,
                        width: 'auto',
                        id: 'App.InfraStructure.mapResumen',
                        cls: 'google_map',
                        ref: 'map2',
                        border: true,
                        style: 'padding: 5 0 5 0',
                        title: App.Language.Infrastructure.map,
                        html: '<div id="mapResumen" class="map_resumen"></div>',
                        listeners: {
                            'render': function() {
                                App.InfraStructure.Coordinate.Store.on('afterload', function() {
                                    new Ext.LoadMask(panel.map2.getEl(), {
                                        msg: App.Language.General.message_loading_information,
                                        store: App.InfraStructure.Coordinate.Store
                                    }).show();
                                });
                            },
                            'destroy': function() {
                                App.InfraStructure.Info.Store.purgeListeners();
                            }
                        }
                    });
                    panel.add(
                        new Ext.Spacer({
                            height: 5
                        })

                    );

                    panel.add({
                        xtype: 'panel',
                        title: 'Plano',
                        height: 'auto',
                        id: 'App.InfraStructure.planoResumen',
                        html: '<div></div>'
                    });
                    panel.add(
                        new Ext.Spacer({
                            height: 5
                        })

                    );
                    panel.add(
                        new App.InfraStructure.OtrosDatosResumen()
                    );
                    panel.ownerCt.doLayout();
                    panel.doLayout();
                }
            }
        }, {
            xtype: 'grid',
            ref: 'grid',
            id: 'App.InfraStructure.Principal.Grid',
            title: App.Language.Infrastructure.infrastructure,
            border: false,
            loadMask: true,
            selModel: this.selModel,
            viewConfig: {
                forceFit: true
            },
            store: App.InfraStructure.Store.Principal,
            listeners: {
                'render': function(g) {
                    g.getStore().load();
                },
                'rowdblclick': function(grid, rowIndex) {

                    if (App.Security.Actions['5003'] && (App.Interface.permits == true)) {
                        App.InfraStructure.editNode(grid.getStore().getAt(rowIndex).data);
                    }
                }
            },
            tbar: {
                xtype: 'toolbar',
                autoScroll: 'auto',
                height: 'auto',
                items: [{
                        text: App.Language.Infrastructure.root,
                        iconCls: 'root_up_icon',
                        handler: function() {
                            Ext.Ajax.request({
                                waitMsg: App.Language.General.message_generating_file,
                                url: 'index.php/core/nodecontroller/expand',
                                timeout: 10000000000,
                                params: {
                                    node: 'root'
                                },
                                success: function(response) {
                                    response = Ext.decode(response.responseText);
                                    Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(response[0]['id']));

                                },
                                failure: function(response) {
                                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                }
                            });

                        }
                    }, {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        text: App.Language.Infrastructure.level_up,
                        iconCls: 'level_up_icon',
                        handler: function() {
                            node = Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId);
                            if (node.parentNode)
                                App.InfraStructure.Principal.expand(node.parentNode.id);
                        }
                    }, {
                        xtype: 'tbseparator',
                        width: 10
                    }, App.ModuleActions[5005],
                    {
                        xtype: 'spacer',
                        width: 5
                    },
                    App.ModuleActions[5003],
                    {
                        xtype: 'spacer',
                        width: 5
                    },
                    App.ModuleActions[5002],
                    {
                        xtype: 'spacer',
                        width: 5
                    }, {
                        xtype: 'tbseparator',
                        width: 10
                    },
                    App.ModuleActions[5008],
                    {
                        xtype: 'spacer',
                        width: 10
                    }, {
                        xtype: 'tbseparator',
                        width: 10
                    }, {
                        xtype: 'spacer',
                        width: 10
                    }, {
                        text: 'Actualizar Datos Dinámicos',
                        iconCls: 'edit_icon',
                        cls: 'permits',
                        id: 'ModuleAction_5004',
                        hidden: (App.Security.Actions[5004] === undefined ? true : false),
                        handler: function(b) {
                            wiadm = new App.InfraStructure.ActualizarDatosMasivoViewWindow();
                            wiadm.show();
                        }
                    }, {
                        xtype: 'spacer',
                        width: 10
                    }, {
                        xtype: 'tbseparator',
                        width: 10
                    }, {
                        xtype: 'spacer',
                        width: 10
                    }, {
                        text: App.Language.General.set_photography_home,
                        iconCls: 'keep_add_icon',
                        handler: function(b) {
                            grid = Ext.getCmp('App.InfraStructure.Principal.Grid');
                            if (grid.getSelectionModel().getCount()) {
                                records = Ext.getCmp('App.InfraStructure.Principal.Grid').getSelectionModel().getSelections();
                                aux = new Array();
                                for (var i = 0; i < records.length; i++) {
                                    aux.push(records[i].data.node_id);
                                }
                                node_id = (aux.join(','));
                                App.Interface.selectedNodeId = node_id;
                                if (i == 1) {
                                    new App.InfraStructure.FotoConfi.formWindow({
                                        node_id: App.Interface.selectedNodeId
                                    }).show();
                                } else {
                                    Ext.FlashMessage.alert(App.Language.General.just_can_select_a_registry_to_set_up_your_photography_home);
                                }
                            } else {
                                Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                            }
                        }
                    }
                ]
            },
            columns: [this.selModel,
                {
                    header: App.Language.General.name,
                    sortable: true,
                    dataIndex: 'node_name',
                    renderer: function(value, metaData, record) {
                        return "<div style='background-image: url(" + record.data.icon + "); background-repeat: no-repeat; height: 16; width: 16; float: left; padding-left: 20; padding-top: 2'><a href='javascript: App.InfraStructure.Principal.expand(" + record.data.node_id + ")'>" + value + "</a></div>";
                    }
                }, {
                    header: App.Language.General.category,
                    sortable: true,
                    dataIndex: 'node_type_category_name'
                }, {
                    header: App.Language.General.type,
                    sortable: true,
                    dataIndex: 'node_type_name'
                }
            ]
        }, {
            xtype: 'form',
            ref: 'otherdata',
            anchor: '100%',
            labelWidth: 300,
            plugins: [new Ext.ux.OOSubmit()],
            title: 'Datos',
            padding: 5,
            border: false,
            autoWidth: true,
            bodyStyle: 'overflowY: auto',
            listeners: {
                'render': function() {
                    App.InfraStructure.OtrosDatos.Store.on('beforeload', function() {
                        Ext.getCmp('App.InfraStructure.Principal').otherdata.getTopToolbar().hide();
                        App.InfraStructure.OtrosDatosLoadMask = new Ext.LoadMask(Ext.getCmp('App.InfraStructure.Principal').otherdata.getEl(), {
                            msg: App.Language.General.message_loading_information,
                            store: App.InfraStructure.OtrosDatos.Store
                        });
                        App.InfraStructure.OtrosDatosLoadMask.show();
                    });
                },
                'destroy': function() {
                    App.InfraStructure.OtrosDatos.Store.purgeListeners();
                }
            },
            tbar: {
                xtype: 'toolbar',
                hidden: true,
                items: [App.ModuleActions[5004]]
            }
        }, {
            xtype: 'panel',
            ref: 'map',
            title: App.Language.Infrastructure.map,
            zoomLevel: parseFloat(infra_default_zoomLevel),
            id: 'App.InfraStructure.mapTab',
            cls: 'google_mapTab',
            html: '<div id="mapTab" class="map_tab"></div>',
            setCenter: {
                geoCodeAddr: infra_default_start
            },
            listeners: {
                'render': function() {
                    App.InfraStructure.Coordinate.Store.on('afterload', function() {
                        new Ext.LoadMask(Ext.getCmp('App.InfraStructure.Principal').map.getEl(), {
                            msg: App.Language.General.message_loading_information,
                            store: App.InfraStructure.Coordinate.Store
                        }).show();
                    });
                },
                'destroy': function() {
                    App.InfraStructure.Info.Store.purgeListeners();
                }
            },
            tbar: App.ModuleActions[5006]
        }, new App.InfraStructure.FotoStandar()];
        App.InfraStructure.Principal.superclass.initComponent.call(this);
    }
});

App.InfraStructure.OtrosDatosResumen = Ext.extend(Ext.grid.GridPanel, {
    title: 'Información Resumen',
    id: 'App.InfraStructure.OtrosDatosResumen',
    store: App.InfraStructure.OtrosDatosResumen.Store,
    loadMask: true,
    region: 'center',
    hideLabel: true,
    height: 600,
    cls: 'myCls',
    updateCarga: function() {

        if (App.Interface.selectedNodeId == App.Interface.nodeRoot) {
            jQuery('.myCls table thead').show();
        } else {
            jQuery('.myCls table thead').hide();
        }
        this.store.setBaseParam('node_id', App.Interface.selectedNodeId);
        this.store.load();
    },
    listeners: {
        'beforerender': function() {

            this.store.setBaseParam('node_id', App.Interface.selectedNodeId);
            this.store.load();
        }

    },
    viewConfig: {
        forceFit: true
    },
    initComponent: function() {
        this.selModel = new Ext.grid.CheckboxSelectionModel({
            checkOnly: false
        });
        this.columns = [{
            header: 'ESTRUCTURA ASOCIADA',
            dataIndex: 'label',
            cls: 'myClsp'
        }, {
            header: 'SUPERFICIE CONSTRIDA TOTAL',
            dataIndex: 'value'
        }];
        App.InfraStructure.OtrosDatosResumen.superclass.initComponent.call(this);
    }
});

App.InfraStructure.ActualizarDatosMasivoViewWindow = Ext.extend(Ext.Window, {
    title: "Subir Excel de actualización de Datos Dinámicos",
    width: (screen.width < 550) ? screen.width - 50 : 550,
    height: 180,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            fileUpload: true,
            labelWidth: 150,
            padding: 5,
            items: [{
                xtype: 'fileuploadfield',
                emptyText: App.Language.Document.select_a_excel,
                fieldLabel: "Documento",
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'documentoExcel',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
            }, {
                xtype: 'spacer',
                height: 10
            }, {
                xtype: 'button',
                text: 'Descarga Formato Excel',
                iconCls: 'add_icon',
                handler: function(b) {
                    document.location = 'index.php/infra/infrainfo/exportarFormato/' + App.Interface.selectedNodeId;
                }
            }],
            buttons: [{
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.close();
                }
            }, {
                text: App.Language.Asset.upload_file,
                ref: '../saveButton',
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: 'index.php/infra/infrainfo/addDatosDinamicosMasivo',
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.General.lloading,
                            success: function(fp, o) {
                                Ext.FlashMessage.alert(o.result.msg);
                                b.ownerCt.ownerCt.ownerCt.close();
                            },
                            failure: function(fp, o) {
                                alert('Error:\n' + o.result.msg);
                            }
                        });
                    }
                }
            }]
        }];
        App.InfraStructure.ActualizarDatosMasivoViewWindow.superclass.initComponent.call(this);
    }
});

App.InfraStructure.FotoConfi.formWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.lis_of_photos,
    width: (screen.width < 1000) ? screen.width - 50 : 1000,
    height: 400,
    id: 'App.Document.VersionImagenWindow',
    layout: 'border',
    border: false,
    tbar: [{
        text: App.Language.General.load_photography_home,
        iconCls: 'save_icon',
        handler: function(b) {
            grid = Ext.getCmp('App.InfraStructure.FotoConfi.GridView');
            if (grid.getSelectionModel().getCount()) {
                records = Ext.getCmp('App.InfraStructure.FotoConfi.GridView').getSelectionModel().getSelections();
                aux = new Array();
                for (var i = 0; i < records.length; i++) {
                    aux.push(records[i].data.doc_document_id);
                }
                doc_document_id = (aux.join(','));
                if (i == 1) {
                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.this_sure_as_home_leave_this_photograph_of_this_venue, function(b) {
                        if (b == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: App.Language.General.message_generating_file,
                                url: 'index.php/doc/document/dejarPortadaNode',
                                timeout: 10000000000,
                                params: {
                                    doc_document_id: doc_document_id,
                                    node_id: App.Interface.selectedNodeId
                                },
                                success: function(response) {
                                    response = Ext.decode(response.responseText);
                                    Ext.FlashMessage.alert(response.msg);
                                    App.InfraStructure.FotoConfi.Store.load();
                                    Ext.getCmp('App.Document.VersionImagenWindow').fireEvent('afterrender', Ext.getCmp('App.Document.VersionImagenWindow'));
                                },
                                failure: function(response) {
                                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                }
                            });
                        }
                    });
                } else {
                    Ext.FlashMessage.alert(App.Language.General.please_select_one_photography);
                }
            } else {
                Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
            }

        }
    }],
    updateImage: function(doc_version_filename, doc_image_web) {
        //ACTUALIZA LA IMAGEN       
        var d = new Date();
        var n = d.getTime();
        this.imagepanel.removeAll();
        this.imagepanel.add(new Ext.Panel({
            layout: 'fit',
            overflowY: 'scroll',
            autoHeight: true,
            html: (doc_image_web == 1 ? '<img width=100% src="docs/' + doc_version_filename + '?id=' + n + '" />' : '<div align="center"><br><br><br><br><br><br><br><br><br><br><br><br><img  src="docs/thumb/not_image_icon.png" /></div>')
        }));
        this.imagepanel.doLayout();
        record = App.InfraStructure.FotoConfi.Store.getAt(App.Document.currentPosition);
        App.InfraStructure.FotoConfi.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        App.InfraStructure.FotoConfi.Store.load();
    },
    listeners: {
        'afterrender': function(w) {
            App.InfraStructure.FotoConfi.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
            App.InfraStructure.FotoConfi.Store.load();
            Ext.Ajax.request({
                waitTitle: App.Language.General.message_please_wait,
                waitMsg: App.Language.General.message_generating_file,
                url: 'index.php/doc/document/getFotoStandar',
                params: {
                    node_id: App.Interface.selectedNodeId
                },
                method: 'POST',
                success: function(response) {
                    response = Ext.decode(response.responseText);
                    if (response.total != 0) {
                        doc_document_default = 1;
                        doc_version_filename = response.results.DocCurrentVersion.doc_version_filename;
                        doc_document_id = response.results.doc_document_id;
                    } else {
                        doc_document_default = 0;
                        doc_version_filename = "";
                        doc_document_id = "";
                    }
                    App.InfraStructure.NodeDocumentIdDefault = doc_document_id;
                    w.updateImage(doc_version_filename, doc_document_default);
                },
                failure: function(response) {
                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                }
            });
        }
    },
    initComponent: function() {
        this.items = [{
            //ESTA ES LA IMAGEN AMPLIADA
            xtype: 'panel',
            id: 'App.Document.PanelImagen',
            style: 'padding: 5 0 5 5',
            region: 'center',
            ref: 'imagepanel',
            autoScroll: true,
            border: false,
            layoutConfig: {
                align: 'middle'
            },
            overflowY: 'scroll',
            layout: 'fit'
        }, {
            xtype: 'grid',
            id: 'App.InfraStructure.FotoConfi.GridView',
            region: 'west',
            layauot: 'fit',
            width: 750,
            style: 'padding: 5 5 5 0',
            loadMask: true,
            store: App.InfraStructure.FotoConfi.Store,
            maskDisabled: false,
            listeners: {
                'rowdblclick': function(grid, rowIndex) {
                    w = grid.ownerCt;
                    w.updateImage(grid.getStore().getAt(rowIndex).data.doc_version_filename, grid.getStore().getAt(rowIndex).data.doc_image_web);
                }
            },
            viewConfig: {
                forceFit: true,
                folderSort: true,
                getRowClass: function(record, index) {
                    var c = record.get('doc_document_id');
                    if (c == App.InfraStructure.NodeDocumentIdDefault) {
                        return 'heavenly-row';
                    }
                }
            },
            columns: [new Ext.grid.CheckboxSelectionModel(),
                {
                    header: App.Language.General.file_name,
                    sortable: true,
                    width: 70,
                    dataIndex: 'doc_document_filename',
                    renderer: function(val, metadata, record) {
                        return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                    }
                }, {
                    dataIndex: 'doc_path',
                    header: App.Language.Core.location,
                    sortable: true,
                    width: 150,
                    renderer: function(doc_path, metadata, record, rowIndex, colIndex, store) {
                        metadata.attr = 'ext:qtip="' + doc_path + '"';
                        return doc_path;
                    }
                }
            ],
            sm: new Ext.grid.CheckboxSelectionModel({ singleSelect: true })
        }];
        App.InfraStructure.FotoConfi.formWindow.superclass.initComponent.call(this);
    }
});

App.InfraStructure.Foto = Ext.extend(Ext.Panel, {
    title: App.Language.General.photography_home,
    id: 'App.InfraStructure.FotoResumen',
    border: false,
    loadMask: true,
    style: 'padding: 5 0 5 0',
    updateImage: function(doc_version_filename, doc_image_web) {

        this.imagepanel2.removeAll();
        this.imagepanel2.add(new Ext.Panel({
            layout: 'fit',
            overflowY: 'scroll',
            autoHeight: true,
            html: (doc_image_web == 1 ? '<img width=100% src="docs/' + doc_version_filename + '" />' : '<div align="center"><br><img  src="docs/thumb/not_image_icon.png" /></div>')
        }));
        this.imagepanel2.doLayout();
    },
    listeners: {
        'afterrender': function(w) {
            Ext.Ajax.request({
                waitTitle: App.Language.General.message_please_wait,
                waitMsg: App.Language.General.message_generating_file,
                url: 'index.php/doc/document/getFotoStandar',
                params: {
                    node_id: App.Interface.selectedNodeId
                },
                method: 'POST',
                success: function(response) {
                    response = Ext.decode(response.responseText);
                    if (response.total != 0) {
                        doc_document_default = 1;
                        doc_version_filename = response.results.DocCurrentVersion.doc_version_filename;
                    } else {
                        doc_document_default = 0;
                        doc_version_filename = "";
                    }
                    w.updateImage(doc_version_filename, doc_document_default);
                },
                failure: function(response) {
                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                }
            });
        }
    },
    initComponent: function() {
        this.items = [{
            //ESTA ES LA IMAGEN AMPLIADA
            xtype: 'panel',
            region: 'center',
            ref: 'imagepanel2',
            autoScroll: true,
            border: false,
            layoutConfig: {
                align: 'middle'
            },
            overflowY: 'scroll',
            layout: 'fit'
        }], App.InfraStructure.Foto.superclass.initComponent.call(this);
    }
});

App.InfraStructure.QRCode = Ext.extend(Ext.Panel, {
    title: 'Código QR',
    id: 'App-InfraStructure-QRCode',
    border: false,
    loadMask: true,
    updateImage: function(qr_file_name) {
        this.imagepanel1.removeAll();
        this.imagepanel1.add(new Ext.Panel({
            layout: 'fit',
            overflowY: 'scroll',
            autoHeight: true,
            autoWidth: true,
            id: 'App-InfraStructure-QRCode-Img',
            html: `<img style="display: block;margin-left: auto;margin-right: auto;" height="200" src="${qr_file_name}" />`,
            data: {
                src: qr_file_name
            }
        }));
        this.imagepanel1.doLayout();
    },
    tbar: [{
        text: 'Imprimir QR',
        iconCls: 'print_icon',
        handler: function () {
            qr = Ext.getCmp('App-InfraStructure-QRCode-Img');
            var printWindow = window.open('', 'Print Window','height=640,width=620');
            printWindow.document.write('<html><head><title>Print Window</title>');
            printWindow.document.write('</head><body ><img src=\'');
            printWindow.document.write(qr.data.src);
            printWindow.document.write('\' /></body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    }],
    listeners: {
        'afterrender': function(w) {
            Ext.Ajax.request({
                waitTitle: App.Language.General.message_please_wait,
                waitMsg: App.Language.General.message_generating_file,
                url: 'index.php/qr/get',
                params: {
                    node_id: App.Interface.selectedNodeId
                },
                method: 'POST',
                success: function(response) {
                    response = Ext.decode(response.responseText);
                    w.updateImage(response);
                },
                failure: function(response) {
                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                }
            });
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'panel',
            style: 'padding: 5 0 5 0',
            region: 'center',
            
            ref: 'imagepanel1',
            autoScroll: true,
            border: false,
            layoutConfig: {
                align: 'middle'
            },
            overflowY: 'scroll',
            layout: 'fit'
        }], App.InfraStructure.QRCode.superclass.initComponent.call(this);
    }
});

App.InfraStructure.FotoConfi.GridView = Ext.extend(Ext.grid.GridPanel, {
    xtype: 'grid',
    id: 'App.InfraStructure.FotoConfi.GridView',
    region: 'center',
    style: 'padding: 5 5 5 5',
    clicksToEdit: 1,
    loadMask: true,
    height: 400,
    store: App.InfraStructure.FotoConfi.Store,
    maskDisabled: false,
    listeners: {
        'rowdblclick': function(grid, rowIndex) {

        },
        'afterrender': function() {
            App.InfraStructure.FotoConfi.Store.setBaseParam('node_id', node_id);
            App.InfraStructure.FotoConfi.Store.load();
        }
    },
    viewConfig: {
        forceFit: true
    },
    columns: [new Ext.grid.CheckboxSelectionModel(),
        {
            header: App.Language.General.file_name,
            sortable: true,
            width: 70,
            dataIndex: 'doc_document_filename',
            renderer: function(val, metadata, record) {
                return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
            }
        }, {
            dataIndex: 'doc_path',
            header: App.Language.Core.location,
            sortable: true,
            width: 150,
            renderer: function(doc_path, metadata, record, rowIndex, colIndex, store) {
                metadata.attr = 'ext:qtip="' + doc_path + '"';
                return doc_path;
            }
        }
    ],
    sm: new Ext.grid.CheckboxSelectionModel()
});

App.InfraStructure.FotoConfi.ThumbView = Ext.extend(Ext.DataView, {
    id: 'App.InfraStructure.Gallery',
    itemSelector: 'div.thumb-wrap',
    style: 'overflow:auto',
    region: 'center',
    multiSelect: true,
    store: App.InfraStructure.FotoConfi.Store,
    tpl: new Ext.XTemplate('<tpl for=".">',
        '<div class="thumb-wrap" id="{doc_document_id}">',
        '<tpl if="values.DocCurrentVersion.doc_image_web == \'0\'">',
        '<div class="thumb"   ><img  src="docs/thumb/not_image_icon.png" ondblclick="App.Document.AbrirImagen({doc_document_id}, {#})" class="thumb-img"/></div>',
        '<span class="thumb-wrap-span">{doc_document_filename}</span></div>',
        '</tpl>',
        '<tpl if="values.DocCurrentVersion.doc_image_web == \'1\'">',
        '<div class="thumb"   ><img src="docs/thumb/{doc_version_filename}?id={[new Date().getTime()]}" ondblclick="App.Document.AbrirImagen({doc_document_id}, {#})" class="thumb-img"/></div>',
        '<span class="thumb-wrap-span">{doc_document_filename}</span></div>',
        '</tpl>',
        '</div>',
        '</tpl>',
        '<div class="x-clear"></div>'),
    listeners: {
        'beforerender': function(w) {
            App.InfraStructure.FotoConfi.Store.load();
        }
    }
});

App.InfraStructure.FotoStandar = Ext.extend(Ext.Panel, {
    title: App.Language.General.photography_home,
    id: 'App.InfraStructure.FotoStandar',
    border: false,
    loadMask: true,
    layout: 'border',
    updateImage: function(doc_version_filename, doc_image_web) {
        var d = new Date();
        var n = d.getTime();
        this.imagepanel.removeAll();
        this.imagepanel.add(new Ext.Panel({
            layout: 'fit',
            overflowY: 'scroll',
            autoHeight: true,
            html: (doc_image_web == 1 ? '<img width=80% src="docs/' + doc_version_filename + '?id=' + n + '" />' : '<div align="center"><br><br><br><br><br><br><br><br><br><br><br><br><img  src="docs/thumb/not_image_icon.png" /></div>')
        }));
        this.imagepanel.doLayout();
    },
    listeners: {
        'activate': function(w) {
            Ext.Ajax.request({
                waitTitle: App.Language.General.message_please_wait,
                waitMsg: App.Language.General.message_generating_file,
                url: 'index.php/doc/document/getFotoStandar',
                params: {
                    node_id: App.Interface.selectedNodeId
                },
                method: 'POST',
                success: function(response) {
                    response = Ext.decode(response.responseText);
                    if (response.total != 0) {
                        doc_document_default = 1;
                        doc_version_filename = response.results.DocCurrentVersion.doc_version_filename;
                    } else {
                        doc_document_default = 0;
                        doc_version_filename = "";
                    }
                    w.updateImage(doc_version_filename, doc_document_default);
                },
                failure: function(response) {
                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                }
            });
        },
        'afterrender': function(w) {
            Ext.Ajax.request({
                waitTitle: App.Language.General.message_please_wait,
                waitMsg: App.Language.General.message_generating_file,
                url: 'index.php/doc/document/getFotoStandar',
                params: {
                    node_id: App.Interface.selectedNodeId
                },
                method: 'POST',
                success: function(response) {
                    response = Ext.decode(response.responseText);
                    if (response.total != 0) {
                        doc_document_default = 1;
                        doc_version_filename = response.results.DocCurrentVersion.doc_version_filename;
                    } else {
                        doc_document_default = 0;
                        doc_version_filename = "";
                    }
                    w.updateImage(doc_version_filename, doc_document_default);
                },
                failure: function(response) {
                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                }
            });
        }
    },
    initComponent: function() {
        this.items = [{
            //ESTA ES LA IMAGEN AMPLIADA
            xtype: 'panel',
            id: 'App.Document.PanelImagen',
            style: 'padding: 5 0 5 5',
            region: 'center',
            ref: 'imagepanel',
            autoScroll: true,
            border: false,
            layoutConfig: {
                align: 'middle'
            },
            overflowY: 'scroll',
            layout: 'fit'
        }], App.InfraStructure.FotoStandar.superclass.initComponent.call(this);
    }
});

App.InfraStructure.Principal.listener = function(node) { //--> ACA ENTRA AL HACER CLICK AL ARBOL <--
    
    //DESABILITA EL BOTON DE RUTA INICAL
    if (App.Security.Session.user_path == App.Interface.selectedNodeId) {
        Ext.getCmp('App.InfraStructure.buttonPath').setDisabled(true);
    } else {
        Ext.getCmp('App.InfraStructure.buttonPath').setDisabled(false);
    }


    // ACTUALIZA EL SVG RESUMEN
    Ext.Ajax.request({
        waitTitle: App.Language.General.message_please_wait,
        waitMsg: App.Language.General.message_generating_file,
        url: 'index.php/plan/plan/getResumen',
        params: {
            node_id: App.Interface.selectedNodeId
        },
        method: 'POST',
        success: function(response) {

            response = Ext.decode(response.responseText);

            if (response.results.plan_filename) {
                Ext.getCmp('App.InfraStructure.planoResumen').show();
                if (response.file_exist) {
                    Ext.getCmp('App.InfraStructure.planoResumen').update('<div id="plan_div2_' + response.results.plan_id + '"><embed  src="plans/' + response.results.plan_filename + '" id="plan_embed2_' + response.results.plan_id + '" width="100%" height="100%" type="image/svg+xml"></div>');
                    App.Plan.CurrentPlanId = response.results.plan_id;
                } else {
                    Ext.getCmp('App.InfraStructure.planoResumen').update('<div id="plan_div2_' + response.results.plan_id + '" align="center" style="padding-bottom: 20px;"><br><img  src="docs/thumb/not_image_icon.png" id="plan_embed2_' + response.results.plan_id + '" /><br></div>');
                    App.Plan.CurrentPlanId = response.results.plan_id;
                }





            } else {

                Ext.getCmp('App.InfraStructure.planoResumen').hide();
            }


        },
        failure: function(response) {
            //AQUI OCULTA EL PLANO
            Ext.getCmp('App.InfraStructure.planoResumen').hide();
        }
    });
    //LLAMA A LA FUNCION updateImage PARA ACTUALIZAR LA FOTO RESUMEN
    Ext.Ajax.request({
        waitTitle: App.Language.General.message_please_wait,
        waitMsg: App.Language.General.message_generating_file,
        url: 'index.php/doc/document/getFotoStandar',
        params: {
            node_id: App.Interface.selectedNodeId
        },
        method: 'POST',
        success: function(response) {
            response = Ext.decode(response.responseText);
            if (response.total != 0) {
                doc_document_default = 1;
                doc_version_filename = response.results.DocCurrentVersion.doc_version_filename;
            } else {
                doc_document_default = 0;
                doc_version_filename = "";
            }

            if (doc_document_default == 0) {
                Ext.getCmp('App.InfraStructure.FotoResumen').hide();
                App.InfraStructure.pathFotoResumen = null;
            } else {
                Ext.getCmp('App.InfraStructure.FotoResumen').show();
                w = Ext.getCmp('App.InfraStructure.FotoResumen');
                w.updateImage(doc_version_filename, doc_document_default);
                App.InfraStructure.pathFotoResumen = doc_version_filename;
            }

        },
        failure: function(response) {
            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
        }
    });
    //ACTUALIZA INFORMACION DE LA FICHA RESUMEN
    w = Ext.getCmp('App.InfraStructure.OtrosDatosResumen');
    w.updateCarga();
    if (node && node.id) {

        App.InfraStructure.Store.Principal.setBaseParam('node', node.id);
        App.InfraStructure.OtrosDatos.Store.setBaseParam('node_id', node.id);
        App.InfraStructure.Coordinate.Store.setBaseParam('node_id', node.id);
    }

    w = Ext.getCmp('App.InfraStructure.FotoStandar');
    Ext.Ajax.request({
        waitTitle: App.Language.General.message_please_wait,
        waitMsg: App.Language.General.message_generating_file,
        url: 'index.php/doc/document/getFotoStandar',
        params: {
            node_id: App.Interface.selectedNodeId
        },
        method: 'POST',
        success: function(response) {
            response = Ext.decode(response.responseText);
            if (response.total != 0) {
                doc_document_default = 1;
                doc_version_filename = response.results.DocCurrentVersion.doc_version_filename;
            } else {
                doc_document_default = 0;
                doc_version_filename = "";
            }
            w.updateImage(doc_version_filename, doc_document_default);
        },
        failure: function(response) {
            Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
        }
    });

    Ext.getCmp('App.InfraStructure.Principal').otherdata.removeAll();
    Ext.getCmp('App.InfraStructure.Principal').otherdata.doLayout();
    App.InfraStructure.Store.Principal.load();
    App.InfraStructure.OtrosDatos.Store.load({
        callback: function() {
            if (typeof Ext.getCmp('App.InfraStructure.Principal') != "undefined") {

                Ext.Ajax.request({
                    url: 'index.php/infra/infrainfo/get',
                    params: {
                        node_id: node.id
                    },
                    success: function(response) {
                        response = Ext.decode(response.responseText);
                        aux = new Ext.form.FieldSet({
                            title: App.Language.Infrastructure.general_data,
                            layout: 'form',
                            collapsible: true,
                            anchor: '100%',
                            labelWidth: 200,
                            bodyCssClass: 'file_style'
                        });
                        
                        /**
                         * infra info estatica (aplica para casos con calculo dinamico)
                         */
                        for (i in response.resultsInfraInfo) {
                            let record = response.resultsInfraInfo[i];
                            if (typeof record === 'object') {
                                field = App.InfraStructure.Info.fields[record.field];
                                if (field.xtype == 'combo' && parseInt(record.value, 10) > 0) {
                                    field.disabled = false;
                                }
                                field.value = record.value;
                                field.width = 'auto';
                                aux.add(field);
                            }
                        }
                        /**
                         * infra info dinamica
                         */
                        if (response.resultsInfraOtherData) {
                            response.resultsInfraOtherData.forEach( function (infra_group){
                                /**
                                 * infra_group_id: 4 (datos generales)
                                 */
                                if (parseInt(infra_group.infra_grupo_id) === 4){
                                    infra_group.InfraOtherDataAttribute.forEach(function(record){
                                        if (typeof record === 'object') {
                                            let field = App.InfraStructure.OtrosDatos.fields[record.infra_other_data_attribute_type];
                                            if (record.InfraOtherDataValue[0]) {
                                                if (parseInt(record.infra_other_data_attribute_type) === 5) {
                                                    //Tipo Combo
                                                    field.value = record.InfraOtherDataValue[0].infra_other_data_option_id;
                                                } else {
                                                    field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : null);
                                                }
                                            } else {
                                                field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : null);
                                            }
                                            field.fieldLabel = record.infra_other_data_attribute_name;
                                            field.name = record.infra_other_data_attribute_id;
                                            field.hiddenName = record.infra_other_data_attribute_id;
                                            field.width = 'auto';
                                            aux.add(field);
                                        }
                                    });
                                }
                            });
                        }
                                                
                        if (node.id != 'root') {
                            if (response.resultsInfraInfo.length) {
                                Ext.getCmp('App.InfraStructure.Principal').otherdata.add(aux);
                                Ext.getCmp('App.InfraStructure.Principal').otherdata.doLayout();
                            }

                        }
                        for (i in response.resultsInfraOtherData) {

                            if (typeof response.resultsInfraOtherData[i] === 'object') {
                                let aux = new Ext.form.FieldSet({
                                    title: response.resultsInfraOtherData[i].infra_grupo_nombre,
                                    layout: 'form',
                                    collapsible: true,
                                    anchor: '100%',
                                    labelWidth: 200,
                                    bodyCssClass: 'file_style'
                                });
                                for (let x in response.resultsInfraOtherData[i].InfraOtherDataAttribute) {
                                    /**
                                     * infra_group_id: 4 (datos generales)
                                     */
                                    if (parseInt(response.resultsInfraOtherData[i].infra_grupo_id) !== 4){
                                        let record = response.resultsInfraOtherData[i].InfraOtherDataAttribute[x];
                                        if (typeof record === 'object') {

                                            let field = App.InfraStructure.OtrosDatos.fields[record.infra_other_data_attribute_type];
                                            if (record.InfraOtherDataValue[0]) {
                                                if (parseInt(record.infra_other_data_attribute_type) === 5) {
                                                    //Tipo Combo
                                                    field.value = record.InfraOtherDataValue[0].infra_other_data_option_id;
                                                } else {
                                                    field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : null);
                                                }
                                            } else {
                                                field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : null);
                                            }

                                            field.fieldLabel = record.infra_other_data_attribute_name;
                                            field.name = record.infra_other_data_attribute_id;
                                            field.hiddenName = record.infra_other_data_attribute_id;
                                            field.width = 'auto';
                                            aux.add(field);
                                        }
                                    } else {
                                        aux.hide();
                                    }
                                }

                                Ext.getCmp('App.InfraStructure.Principal').otherdata.add(aux);
                                Ext.getCmp('App.InfraStructure.Principal').otherdata.doLayout();
                            }
                        }

                        if (response.total > 0) {
                            Ext.getCmp('App.InfraStructure.Principal').otherdata.getTopToolbar().show();
                        }
                    }
                });
            }
        }
    });
    
    if ( App.Security.Actions['8015'] ) {
        Ext.Ajax.request({
            waitTitle: App.Language.General.message_please_wait,
            waitMsg: App.Language.General.message_generating_file,
            url: 'index.php/qr/get',
            params: {
                node_id: App.Interface.selectedNodeId
            },
            method: 'POST',
            success: function(response) {
                response = Ext.decode(response.responseText);
                w = Ext.getCmp('App-InfraStructure-QRCode');
                w.updateImage(response);
            },
            failure: function(response) {
                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
            }
        });
    }
    
    //SI ESTA ACTIVA EL TAB FICHA DE RESUMEN ELIGE EL MAPA SELECCIONADO

    if (Ext.getCmp('App.InfraStructure.fichaResumen').isVisible()) {
        mapaParaEditar = 'mapResumen';
        busquedaInterna = App.InfraStructure.SearchNodeBranches;

        getMap();
    } else {
        mapaParaEditar = 'mapTab';
        busquedaInterna = App.InfraStructure.SearchNodeBranches;

        getMap();
    }

    if (node && node.id) {
        node.expand();
        Ext.getCmp('App.StructureTree.Tree').getSelectionModel().select(node);
        App.StructureTree.Tree.refreshPathBar(node);
    }




};

App.InfraStructure.Principal.expand = function(node_id) {
    App.Interface.selectedNodeId = node_id;
    node = Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id);
    App.Security.checkNodeAccess(node);
};

App.InfraStructure.addNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.add_items,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    autoHeight: true,
    layout: 'accordion',
    padding: 1,
    activeItem: 0,
    modal: true,
    resizable: false,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            title: App.Language.Infrastructure.simple_mode,
            padding: 5,
            autoHeight: true,
            items: [{
                    xtype: 'textfield',
                    fieldLabel: App.Language.General.name,
                    anchor: '100%',
                    editable: false,
                    allowBlank: false,
                    name: 'node_name'
                },
                {
                    xtype: 'combo',
                    fieldLabel: App.Language.General.category,
                    anchor: '100%',
                    triggerAction: 'all',
                    hiddenName: 'node_type_category_id',
                    store: App.NodeTypeCategory.Store,
                    displayField: 'node_type_category_name',
                    valueField: 'node_type_category_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: false,
                    listeners: {
                        'select': function(cb) {
                            App.NodeType.Store.setBaseParam('node_type_category_id', cb.getValue());
                            App.NodeType.Store.load();
                            //App.InfraStructure.Iot.Store.load();
                            if (cb.getValue() == 3) {

                                jQuery('.test_').each(function() {

                                    var t = jQuery(this).attr('id');
                                    var type = Ext.getCmp(t);
                                    type.setValue(33);
                                    type.allowBlank = true;
                                });
                                jQuery('.test_').parents('.x-form-item').addClass('x-hide-label');
                                jQuery('.test_').parent('div').hide();
                                jQuery('.test_').parent('div').addClass('x-hide-display');
                                jQuery('.test_1').each(function() {

                                    var iot_attributes = jQuery(this).attr("id");
                                    var device = Ext.getCmp(iot_attributes);
                                    device.allowBlank = false;
                                });
                                jQuery('.test_1').show();
                                jQuery('.test_1').removeClass('x-hide-display');
                                jQuery('.test_1').parent('div').removeClass('x-hide-display');
                                jQuery('.test_1').parents('.x-form-item').removeClass('x-hide-label');
                                jQuery('.test_1').parent('div').show();
                                $('.test_1').css('width', '100%');
                                jQuery('.test_1 fieldset div').css('width', '100%');
                                jQuery('.test_2').show();
                                jQuery('.test_2').removeClass('x-hide-display');
                                jQuery('.test_2').parent('div').removeClass('x-hide-display');
                                jQuery('.test_2').parents('.x-form-item').removeClass('x-hide-label');
                                jQuery('.test_2').parent('div').show();
                                $('.test_2').css('width', '100%');
                                jQuery('.test_2 fieldset div').css('width', '100%');
                            } else {

                                jQuery('.test_').show();
                                jQuery('.test_').each(function() {

                                    var t = jQuery(this).attr('id');
                                    var type = Ext.getCmp(t);
                                    type.setValue('');
                                    type.allowBlank = false;
                                });
                                jQuery('.test_').removeClass('x-hide-display');
                                jQuery('.test_').parent('div').removeClass('x-hide-display');
                                jQuery('.test_').parents('.x-form-item').removeClass('x-hide-label');
                                jQuery('.test_').parent('div').show();
                                jQuery('.test_').parent('div').css('width', '98%');
                                $('.test_').css('width', '96%');
                                jQuery('.test_1').each(function() {

                                    var iot_attributes = jQuery(this).attr("id");
                                    var device = Ext.getCmp(iot_attributes);
                                    device.allowBlank = true;
                                });
                                jQuery('.test_1').hide();
                                jQuery('.test_1').addClass('x-hide-display');
                                jQuery('.test_1').parent('div').addClass('x-hide-display');
                                jQuery('.test_1').parents('.x-form-item').addClass('x-hide-label');
                                jQuery('.test_1').parent('div').hide();
                                jQuery('.test_2').hide();
                                jQuery('.test_2').addClass('x-hide-display');
                                jQuery('.test_2').parent('div').addClass('x-hide-display');
                                jQuery('.test_2').parents('.x-form-item').addClass('x-hide-label');
                                jQuery('.test_2').parent('div').hide();
                            }
                        }
                    }
                },
                {
                    xtype: 'textfield',
                    cls: 'test_1',
                    hideLabel: true,
                    hidden: true,
                    fieldLabel: 'Nombre Desarrollador',
                    anchor: '100%',
                    editable: false,
                    allowBlank: true,
                    name: 'node_name_developer'
                },
                {
                    xtype: 'combo',
                    cls: 'test_1 anchor_',
                    hiddenName: 'type_comunication',
                    fieldLabel: 'Tipo Comunicación',
                    hideLabel: true,
                    hidden: true,
                    anchor: '71%',
                    triggerAction: 'all',
                    editable: false,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    minChars: 0,
                    allowBlank: true,
                    lazyRender: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields: [
                            'id_type_comunication',
                            'name_type_comunication'
                        ],
                        data: [
                            [1, 'Wifi'],
                            [2, 'LoRa']
                        ]
                    }),
                    valueField: 'id_type_comunication',
                    displayField: 'name_type_comunication'

                },
                {
                    xtype: 'textfield',
                    cls: 'test_2',
                    hideLabel: true,
                    hidden: true,
                    fieldLabel: 'Descripción',
                    anchor: '100%',
                    editable: false,
                    allowBlank: true,
                    name: 'node_description'
                },
                {

                    xtype: 'multiselect',
                    fieldLabel: 'Tipo de sensor',
                    name: 'multiselect',
                    cls: 'test_1',
                    hideLabel: true,
                    hidden: true,
                    width: '100%',
                    autoHeight: true,
                    displayField: 'name',
                    valueField: 'id',
                    allowBlank: true,
                    store: App.InfraStructure.Iot.Store,
                    tbar: [{
                        text: 'Limpiar',
                        handler: function(cb) {
                            App.InfraStructure.Iot.Store.load();;
                        }
                    }],
                    ddReorder: true
                },
                {
                    xtype: 'combo',
                    fieldLabel: App.Language.General.type,
                    hideLabel: true,
                    hidden: true,
                    cls: 'test_',
                    anchor: '100%',
                    store: App.NodeType.Store,
                    hiddenName: 'node_type_id',
                    triggerAction: 'all',
                    displayField: 'node_type_name',
                    valueField: 'node_type_id',
                    editable: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    mode: 'remote',
                    minChars: 0,
                    allowBlank: true,
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
                }
            ]
        }, {
            xtype: 'form',
            title: App.Language.Infrastructure.advanced_mode,
            autoHeight: true,
            bodyStyle: 'padding: 20px',
            padding: 5,
            items: [{
                    xtype: 'textfield',
                    fieldLabel: App.Language.General.code,
                    anchor: '100%',
                    name: 'node_prefix'
                }, {
                    xtype: 'numberfield',
                    fieldLabel: App.Language.General.quantity,
                    allowDecimals: false,
                    anchor: '50%',
                    name: 'node_cantity'
                }, {
                    xtype: 'combo',
                    fieldLabel: App.Language.General.category,
                    anchor: '100%',
                    triggerAction: 'all',
                    hiddenName: 'node_type_category_id',
                    store: App.NodeTypeCategory.Store,
                    displayField: 'node_type_category_name',
                    valueField: 'node_type_category_id',
                    editable: true,
                    mode: 'remote',
                    minChars: 0,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    allowBlank: false,
                    listeners: {
                        'select': function(cb) {
                            App.NodeType.Store.setBaseParam('node_type_category_id', cb.getValue());
                            App.NodeType.Store.load();
                            App.InfraStructure.Iot.Store.load();
                            if (cb.getValue() == 3) {

                                jQuery('.test_ad').each(function() {

                                    var t = jQuery(this).attr('id');
                                    var type = Ext.getCmp(t);
                                    type.setValue(33);
                                    type.allowBlank = true;
                                });
                                jQuery('.test_ad').parents('.x-form-item').addClass('x-hide-label');
                                jQuery('.test_ad').parent('div').hide();
                                jQuery('.test_ad').parent('div').addClass('x-hide-display');
                                jQuery('.test_ad_1').each(function() {

                                    var iot_attributes = jQuery(this).attr("id");
                                    var device = Ext.getCmp(iot_attributes);
                                    device.allowBlank = false;
                                });
                                jQuery('.test_ad_1').show();
                                jQuery('.test_ad_1').removeClass('x-hide-display');
                                jQuery('.test_ad_1').parent('div').removeClass('x-hide-display');
                                jQuery('.test_ad_1').parents('.x-form-item').removeClass('x-hide-label');
                                jQuery('.test_ad_1').parent('div').show();
                                $('.test_ad_1').css('width', '100%');
                                jQuery('.test_ad_1 fieldset div').css('width', '100%');
                                jQuery('.test_ad_2').show();
                                jQuery('.test_ad_2').removeClass('x-hide-display');
                                jQuery('.test_ad_2').parent('div').removeClass('x-hide-display');
                                jQuery('.test_ad_2').parents('.x-form-item').removeClass('x-hide-label');
                                jQuery('.test_ad_2').parent('div').show();
                                $('.test_ad_2').css('width', '100%');
                                jQuery('.test_ad_2 fieldset div').css('width', '100%');
                            } else {

                                jQuery('.test_ad').show();
                                jQuery('.test_ad').each(function() {

                                    var t = jQuery(this).attr('id');
                                    var type = Ext.getCmp(t);
                                    type.setValue('');
                                    type.allowBlank = false;
                                });
                                jQuery('.test_ad').removeClass('x-hide-display');
                                jQuery('.test_ad').parent('div').removeClass('x-hide-display');
                                jQuery('.test_ad').parents('.x-form-item').removeClass('x-hide-label');
                                jQuery('.test_ad').parent('div').show();
                                jQuery('.test_ad').parent('div').css('width', '98%');
                                $('.test_ad').css('width', '96%');
                                jQuery('.test_ad_1').each(function() {

                                    var iot_attributes = jQuery(this).attr("id");
                                    var device = Ext.getCmp(iot_attributes);
                                    device.allowBlank = true;
                                });
                                jQuery('.test_ad_1').hide();
                                jQuery('.test_ad_1').addClass('x-hide-display');
                                jQuery('.test_ad_1').parent('div').addClass('x-hide-display');
                                jQuery('.test_ad_1').parents('.x-form-item').addClass('x-hide-label');
                                jQuery('.test_ad_1').parent('div').hide();
                                jQuery('.test_ad_2').hide();
                                jQuery('.test_ad_2').addClass('x-hide-display');
                                jQuery('.test_ad_2').parent('div').addClass('x-hide-display');
                                jQuery('.test_ad_2').parents('.x-form-item').addClass('x-hide-label');
                                jQuery('.test_ad_2').parent('div').hide();
                            }
                        }
                    }
                },
                {
                    xtype: 'textfield',
                    cls: 'test_ad_1',
                    hideLabel: true,
                    hidden: true,
                    fieldLabel: 'Nombre Desarrollador',
                    anchor: '100%',
                    editable: false,
                    allowBlank: true,
                    name: 'node_name_developer'
                },
                {
                    xtype: 'combo',
                    cls: 'test_ad_1 anchor_',
                    hiddenName: 'type_comunication',
                    fieldLabel: 'Tipo Comunicación',
                    anchor: '71%',
                    hideLabel: true,
                    hidden: true,
                    triggerAction: 'all',
                    editable: false,
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    minChars: 0,
                    allowBlank: true,
                    lazyRender: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields: [
                            'id_type_comunication',
                            'name_type_comunication'
                        ],
                        data: [
                            [1, 'Wifi'],
                            [2, 'LoRa']
                        ]
                    }),
                    valueField: 'id_type_comunication',
                    displayField: 'name_type_comunication'

                },
                {
                    xtype: 'textfield',
                    cls: 'test_ad_2',
                    hideLabel: true,
                    hidden: true,
                    fieldLabel: 'Descripción',
                    anchor: '100%',
                    editable: false,
                    allowBlank: true,
                    name: 'node_description'
                },
                {

                    xtype: 'multiselect',
                    fieldLabel: 'Tipo de sensor',
                    name: 'multiselect',
                    cls: 'test_ad_1',
                    hideLabel: true,
                    hidden: true,
                    width: '100%',
                    autoHeight: true,
                    displayField: 'name',
                    valueField: 'id',
                    allowBlank: true,
                    store: App.InfraStructure.Iot.Store,
                    tbar: [{
                        text: 'Limpiar',
                        handler: function(cb) {
                            App.InfraStructure.Iot.Store.load();;
                        }
                    }],
                    ddReorder: true
                },
                {
                    xtype: 'combo',
                    fieldLabel: App.Language.General.type,
                    hideLabel: true,
                    hidden: true,
                    cls: 'test_ad',
                    anchor: '100%',
                    store: App.NodeType.Store,
                    hiddenName: 'node_type_id',
                    triggerAction: 'all',
                    displayField: 'node_type_name',
                    valueField: 'node_type_id',
                    editable: true,
                    mode: 'remote',
                    typeAhead: true,
                    selectOnFocus: true,
                    forceSelection: true,
                    minChars: 0,
                    allowBlank: false,
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
                }
            ]
        }];
        this.fbar = {
            xtype: 'toolbar',
            items: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.hide();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.add,
                handler: function(b) {
                    w = b.ownerCt.ownerCt;
                    form = w.layout.activeItem.getForm();
                    if (form.isValid()) {

                        form.submit({
                            clientValidation: true,
                            url: 'index.php/core/nodecontroller/addSibling',
                            params: {
                                node_parent_id: App.Interface.selectedNodeId
                            },
                            success: function(form, response) {
                                nodes = response.result.node;
                                for (i = 0; i < nodes.length; i++) {
                                    node = nodes[i];
                                    var newNode = new Ext.tree.TreeNode(node);
                                    Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId).appendChild(newNode);
                                }
                                App.InfraStructure.Principal.expand(App.Interface.selectedNodeId);
                                b.ownerCt.ownerCt.hide();
                            },
                            failure: function(form, action) {
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.FlashMessage.alert(action.result.msg);
                                }
                            }
                        });
                    }
                }
            }]
        };
        App.InfraStructure.addNodeWindow.superclass.initComponent.call(this);
    }
});

App.InfraStructure.editNode = function(node) {
    w = new App.InfraStructure.editNodeWindow({
        node: node
    });
    w.show();
};

App.InfraStructure.exportListWindow = Ext.extend(Ext.Window, {
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
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.file_name,
                anchor: '100%',
                name: 'file_name',
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
                }, {
                    boxLabel: 'PDF',
                    name: 'output_type',
                    inputValue: 'p',
                    height: 25
                }]
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
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
                            waitTitle: App.Language.General.message_please_wait,
                            waitMsg: App.Language.General.message_generating_file,
                            url: 'index.php/infra/infrastructurecontroller/exportList',
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            success: function(form, response) {
                                document.location = 'index.php/app/download/' + response.result.file;
                                b.ownerCt.ownerCt.ownerCt.hide();
                            },
                            failure: function(form, action) {
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, action.result.msg);
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

App.InfraStructure.editNodeWindow = Ext.extend(Ext.Window, {
    title: App.Language.Infrastructure.edit_title_node,
    width: 400,
    height: 250,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            padding: 5,
            items: [{
                xtype: 'textfield',
                fieldLabel: App.Language.General.name,
                anchor: '100%',
                name: 'node_name',
                value: this.node.node_name
            }, {
                xtype: 'hidden',
                name: 'node_id',
                value: this.node.node_id
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                xtype: 'button',
                text: App.Language.General.edit,
                handler: function(b) {
                    form = b.ownerCt.ownerCt.getForm();
                    if (form.isValid()) {
                        form.submit({
                            clientValidation: true,
                            url: 'index.php/core/nodecontroller/edit',
                            params: {
                                action: 'update'
                            },
                            success: function(form, response) {
                                nodes = response.result.node;
                                for (i = 0; i < nodes.length; i++) {
                                    node = nodes[i];
                                    nodeRow = App.InfraStructure.Store.Principal.getById(node.id);
                                    nodeRow.set('node_name', node.text);
                                    nodeRow.commit();
                                    Ext.getCmp('App.StructureTree.Tree').getNodeById(node.id).setText(node.text);
                                    b.ownerCt.ownerCt.ownerCt.hide();
                                }
                            },
                            failure: function(form, action) {
                                switch (action.failureType) {
                                    case Ext.form.Action.CLIENT_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                        break;
                                    case Ext.form.Action.CONNECT_FAILURE:
                                        Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                        break;
                                    case Ext.form.Action.SERVER_INVALID:
                                        Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                }
                            }
                        });
                    }
                }
            }]
        }];
        App.InfraStructure.editNodeWindow.superclass.initComponent.call(this);
    }
});

App.InfraStructure.searchWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.search,
    width: (screen.width < 780) ? screen.width - 50 : 780,
    height: 560,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    closeAction: 'hide',
    listeners: {
        'beforerender': function() {
            App.NodeType.Store.load();
            //Poblamos los filtros de los datos dinamicos 
            App.InfraStructure.DatosDinamicos.SearchStore.load({
                callback: function() {
                    App.InfraStructure.DatosDinamicos.SearchStore.each(function(record) {
                        //Label
                        label = {
                            xtype: 'label',
                            text: record.data.infra_other_data_attribute_name,
                            columnWidth: 0.3
                        };
                        //Separador
                        var spacer = {
                            xtype: 'spacer',
                            columnWidth: 0.05,
                            height: 5
                        };
                        //Field
                        var xtype_object = new App.InfraStructure.OtrosDatos.classFields[record.data.infra_other_data_attribute_type]
                            ({
                                name: record.data.infra_other_data_attribute_id,
                                columnWidth: 0.47
                            });
                        field = xtype_object;
                        //Combo Operadores
                        var combo_operador = {
                            xtype: 'combo',
                            name: record.data.infra_other_data_attribute_id + '_cb',
                            hiddenName: record.data.infra_other_data_attribute_id + '_cb',
                            columnWidth: 0.1,
                            store: App.Core.Operators.Store,
                            editable: false,
                            triggerAction: 'all',
                            displayField: 'value',
                            valueField: 'value',
                            mode: 'local'
                        };
                        if (xtype_object.xtype == 'combo') {
                            combo_operador.value = '=';
                            combo_operador.disabled = true;
                            App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.add({
                                xtype: 'hidden',
                                name: record.data.infra_other_data_attribute_id + '_cb',
                                value: '='
                            });
                        }
                        var tbseparator = {
                            xtype: 'tbseparator',
                            height: 30
                        };
                        App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.add(label);
                        App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.add(combo_operador);
                        App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.add(spacer);
                        App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.add(field);
                        App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.add(tbseparator);
                    });
                    App.InfraStructure.searchWindowObject.form.dinamicDataFilterParent.doLayout();
                }
            });
        }
    },
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            layout: 'border',
            border: true,
            items: [{
                xtype: 'tabpanel',
                border: false,
                ref: 'tabPanel',
                enableTabScroll: true,
                region: 'center',
                activeTab: 0,
                items: [{
                    xtype: 'grid',
                    title: App.Language.Infrastructure.node_type,
                    columnWidth: 1,
                    bbodyStyle: 'padding:20px 40px 10',
                    ref: 'listviewNodeTypes',
                    store: App.NodeType.Store,
                    multiSelect: true,
                    region: 'center',
                    emptyText: App.Language.Infrastructure.node_types_show,
                    anchor: '100%',
                    sm: new Ext.grid.CheckboxSelectionModel(),
                    viewConfig: {
                        forceFit: true
                    },
                    columns: [new Ext.grid.CheckboxSelectionModel(),
                        {
                            header: App.Language.General.type,
                            width: .25,
                            dataIndex: 'node_type_name'
                        }, {
                            header: App.Language.General.category,
                            align: 'center',
                            width: .25,
                            dataIndex: 'node_type_category_name'
                        }
                    ]
                }, {
                    xtype: 'panel',
                    columnWidth: 1,
                    title: App.Language.Infrastructure.general_data,
                    bodyStyle: 'padding:20px 40px 10',
                    autoScroll: true,
                    layout: 'column',
                    bodyCssClass: 'file_style',
                    items: [{
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_usable_area,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_usable_area_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_usable_area_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_usable_area_total,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_usable_area_total_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_usable_area_total_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_area,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_area_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_area_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_area_total,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_area_total_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_area_total_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_volume,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_volume_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_volume_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_volume_total,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_volume_total_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_volume_total_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_length,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_length_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_length_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_width,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_width_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_width_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_height,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_height_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_height_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_capacity,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_capacity_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_capacity_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_capacity_total,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_capacity_total_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'numberfield',
                        name: 'infra_info_capacity_total_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_additional_1,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_additional_1_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'textfield',
                        name: 'infra_info_additional_1_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_additional_2,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_additional_2_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'textfield',
                        name: 'infra_info_additional_3_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_additional_3,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_additional_3_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'textfield',
                        name: 'infra_info_additional_4_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }, {
                        xtype: 'label',
                        text: App.Language.Infrastructure.infra_info_additional_4,
                        columnWidth: 0.3
                    }, {
                        xtype: 'combo',
                        name: 'infra_info_additional_4_cb',
                        columnWidth: 0.1,
                        store: App.Core.Operators.Store,
                        editable: false,
                        triggerAction: 'all',
                        displayField: 'value',
                        valueField: 'value',
                        mode: 'local'
                    }, {
                        xtype: 'spacer',
                        columnWidth: 0.05,
                        height: 5
                    }, {
                        xtype: 'textfield',
                        name: 'infra_info_additional_4_txt',
                        columnWidth: 0.47
                    }, {
                        xtype: 'tbseparator',
                        height: 30
                    }]
                }, {
                    xtype: 'panel',
                    columnWidth: 1,
                    bodyStyle: 'padding:20px 40px 10',
                    ref: '../dinamicDataFilterParent',
                    title: App.Language.Infrastructure.dynamic_data,
                    autoScroll: true,
                    layout: 'column'
                }]
            }, {
                xtype: 'panel',
                region: 'south',
                columnWidth: 1,
                hidden: true,
                autoHeight: true,
                bodyStyle: 'padding:5px 10px 0',
                title: App.Language.Infrastructure.depth,
                layout: 'column',
                items: [{
                    xtype: 'spacer',
                    height: 35
                }, {
                    xtype: 'radio',
                    name: 'depth',
                    anchor: '100%',
                    inputValue: '0',
                    checked: true,
                    columnWidth: 0.02
                }, {
                    xtype: 'label',
                    text: App.Language.Infrastructure.complete_branch_selected_node,
                    columnWidth: 0.48
                }, {
                    xtype: 'radio',
                    name: 'depth',
                    anchor: '100%',
                    inputValue: '1',
                    columnWidth: 0.02
                }, {
                    xtype: 'label',
                    text: App.Language.Infrastructure.direct_nodes,
                    columnWidth: 0.48
                }]
            }],
            buttons: [{
                xtype: 'button',
                text: App.Language.General.close,
                handler: function(b) {
                    b.ownerCt.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.General.search,
                handler: function(b) {
                    form = App.InfraStructure.searchWindowObject.form.getForm();
                    App.InfraStructure.Search.Store.baseParams = form.getFieldValues();
                    var node_name_to_search = Ext.getCmp('App.InfraStructure.Search.TextBox').getValue();
                    App.InfraStructure.Search.Store.setBaseParam('node_name', node_name_to_search);
                    App.InfraStructure.Search.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
                    var node_type_id = [];
                    var selectedItems = App.InfraStructure.searchWindowObject.form.tabPanel.listviewNodeTypes.getSelectionModel().getSelections();
                    Ext.each(selectedItems, function(r) {
                        node_type_id.push(r.data['node_type_id']);
                    });
                    if (node_type_id.length > 0) {
                        App.InfraStructure.Search.Store.setBaseParam('node_type_id', node_type_id.join(','));
                    }
                    App.InfraStructure.Search.Store.load();
                    App.InfraStructure.searchWindowObject.form.hide();
                    App.InfraStructure.searchWindowObject.resultGrid.show();
                    App.InfraStructure.searchWindowObject.resultGrid.doLayout();
                }
            }, {
                text: App.Language.General.clean,
                handler: function(b) {
                    form = App.InfraStructure.searchWindowObject.form.getForm();
                    form.reset();
                    App.InfraStructure.searchWindowObject.form.tabPanel.listviewNodeTypes.getSelectionModel().clearSelections();
                }
            }]
        }, {
            xtype: 'grid',
            store: App.InfraStructure.Search.Store,
            height: 525,
            hidden: true,
            ref: 'resultGrid',
            viewConfig: {
                forceFit: true
            },
            loadMask: true,
            fbar: [{
                text: App.Language.General.back_to_search,
                handler: function() {
                    App.InfraStructure.searchWindowObject.resultGrid.hide();
                    App.InfraStructure.searchWindowObject.form.show();
                    App.InfraStructure.searchWindowObject.resultGrid.doLayout();
                }
            }],
            columns: [{
                header: App.Language.General.name,
                sortable: true,
                width: 40,
                dataIndex: 'node_name',
                renderer: function(value, metaData, record) {
                    return "<div style='background-image: url(" + record.data.icon + "); background-repeat: no-repeat; height: 16; width: 16; float: left; padding-left: 20; padding-top: 2'><a href='javascript: App.InfraStructure.searchWindowObject.hide();App.InfraStructure.expandDeepNode(" + record.data.node_id + ")'>" + value + "</a></div>";
                }
            }, {
                header: App.Language.General.type,
                sortable: true,
                width: 30,
                dataIndex: 'node_type_name'
            }, {
                header: App.Language.Core.location,
                sortable: true,
                dataIndex: 'node_root',
                renderer: function(value, metadata, record, rowIndex, colIndex, store) {
                    metadata.attr = 'ext:qtip="' + value + '"';
                    return value;
                }
            }]
        }];
        App.InfraStructure.searchWindow.superclass.initComponent.call(this);
    }
});

App.InfraStructure.searchWindowObject = new App.InfraStructure.searchWindow();

App.InfraStructure.expandDeepNode = function(node_id) {
    Ext.Ajax.request({
        url: 'index.php/core/nodecontroller/expanddeep',
        params: {
            node_id: node_id
        },
        success: function(response) {
            response = Ext.decode(response.responseText);
            App.InfraStructure.expandDeepNodeCallback(node_id, response);
        }
    });
};

App.InfraStructure.expandDeepNodeCallback = function(node_id, children) {
    for (var i = 0; i < children.length; i++) {
        node = children[i];
        treeNode = Ext.getCmp('App.StructureTree.Tree').getNodeById(node.id);
        if (node.id == node_id) {
            Ext.getCmp('App.StructureTree.Tree').getSelectionModel().select(Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id));
            Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id));
            return;
        }
        if (treeNode && treeNode.isExpanded() == false && node.expanded == true) {

            if (App.Security.Session.user_type == 'A' || App.Security.Session.user_tree_full == 1) {

                treeNode.expand(false, true, function(nd) {
                    nd.removeAll(true);
                    nd.appendChild(children[i].children);
                    Ext.getCmp('App.StructureTree.Tree').getSelectionModel().select(Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id));
                    Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id));
                });
            } else {
                treeNode.expand();
                App.StructureTree.Tree.XML.expanddeep(node_id, treeNode, children[i].children);
            }
            return;
        }
        if (children[i].children) {
            App.InfraStructure.expandDeepNodeCallback(node_id, children[i].children);
        }
    }
};

App.InfraStructure.Info.fields = {
    'node_id': {
        xtype: 'hidden',
        name: 'node_id'
    },
    'infra_info_area': {
        xtype: 'numberfield',
        name: 'infra_info_area',
        fieldLabel: App.Language.Infrastructure.infra_info_area,
        allowNegative: false
    },
    'infra_info_area_total': {
        xtype: 'displayfield',
        name: 'infra_info_area_total',
        fieldLabel: App.Language.Infrastructure.infra_info_area_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_usable_area': {
        xtype: 'numberfield',
        name: 'infra_info_usable_area',
        fieldLabel: App.Language.Infrastructure.infra_info_usable_area,
        allowNegative: false
    },
    'infra_info_usable_area_total': {
        xtype: 'displayfield',
        name: 'infra_info_usable_area_total',
        fieldLabel: App.Language.Infrastructure.infra_info_usable_area_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_volume': {
        xtype: 'numberfield',
        name: 'infra_info_volume',
        fieldLabel: App.Language.Infrastructure.infra_info_volume,
        allowNegative: false
    },
    'infra_info_volume_total': {
        xtype: 'displayfield',
        name: 'infra_info_volume_total',
        fieldLabel: App.Language.Infrastructure.infra_info_volume_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_length': {
        xtype: 'numberfield',
        name: 'infra_info_length',
        fieldLabel: App.Language.Infrastructure.infra_info_length,
        allowNegative: false
    },
    'infra_info_width': {
        xtype: 'numberfield',
        name: 'infra_info_width',
        fieldLabel: App.Language.Infrastructure.infra_info_width,
        allowNegative: false
    },
    'infra_info_height': {
        xtype: 'numberfield',
        name: 'infra_info_height',
        fieldLabel: App.Language.Infrastructure.infra_info_height,
        allowNegative: false
    },
    'infra_info_capacity': {
        xtype: 'numberfield',
        name: 'infra_info_capacity',
        fieldLabel: App.Language.Infrastructure.infra_info_capacity,
        allowNegative: false,
        allowDecimals: false
    },
    'infra_info_capacity_total': {
        xtype: 'displayfield',
        name: 'infra_info_capacity_total',
        fieldLabel: App.Language.Infrastructure.infra_info_capacity_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_terrain_area': {
        xtype: 'numberfield',
        name: 'infra_info_terrain_area',
        fieldLabel: App.Language.Infrastructure.infra_info_terrain_area,
        allowNegative: false
    },
    'infra_info_terrain_area_total': {
        xtype: 'displayfield',
        name: 'infra_info_terrain_area_total',
        fieldLabel: App.Language.Infrastructure.infra_info_terrain_area_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_additional_1': {
        xtype: 'textfield',
        fieldLabel: App.Language.Infrastructure.infra_info_additional_1,
        name: 'infra_info_additional_1'
    },
    'infra_info_additional_2': {
        xtype: 'textfield',
        fieldLabel: App.Language.Infrastructure.infra_info_additional_2,
        name: 'infra_info_additional_2'
    },
    'infra_info_additional_3': {
        xtype: 'textfield',
        fieldLabel: App.Language.Infrastructure.infra_info_additional_3,
        name: 'infra_info_additional_3'
    },
    'infra_info_additional_4': {
        xtype: 'textfield',
        fieldLabel: App.Language.Infrastructure.infra_info_additional_4,
        name: 'infra_info_additional_4'
    },
    'infra_info_option_id_1': {
        xtype: 'combo',
        ref: 'infra_info_option_id_1',
        triggerAction: 'all',
        fieldLabel: App.Language.Infrastructure.infra_info_option_id_1,
        store: App.InfraStructure.InfoOptionCombosAnidados1.Store,
        hiddenName: 'infra_info_option_id_1',
        displayField: 'infra_info_option_name',
        valueField: 'infra_info_option_id',
        editable: true,
        mode: 'remote',
        minChars: 0,
        listeners: {
            'beforerender': function(cb) {
                var value_parent = null;
                cb.getStore().setBaseParam('infra_info_option_parent_id', value_parent);
            },
            'afterrender': function(cb) {
                cb.__value = cb.value;
                cb.setValue('');
                cb.getStore().load();
            },
            'select': function(cb, record) {
                if (cb.ownerCt.infra_info_option_id_2) {
                    cb.ownerCt.infra_info_option_id_2.clearValue();
                    cb.ownerCt.infra_info_option_id_2.enable();
                    var store = cb.ownerCt.infra_info_option_id_2.getStore();
                    store.setBaseParam('infra_info_option_parent_id', cb.getValue());
                    store.load();
                }
                if (cb.ownerCt.infra_info_option_id_3) {
                    cb.ownerCt.infra_info_option_id_3.clearValue();
                    cb.ownerCt.infra_info_option_id_3.disable();
                }
                if (cb.ownerCt.infra_info_option_id_4) {
                    cb.ownerCt.infra_info_option_id_4.clearValue();
                    cb.ownerCt.infra_info_option_id_4.disable();
                }
            }
        }
    },
    'infra_info_option_id_2': {
        xtype: 'combo',
        ref: 'infra_info_option_id_2',
        disabled: true,
        triggerAction: 'all',
        fieldLabel: App.Language.Infrastructure.infra_info_option_id_2,
        store: App.InfraStructure.InfoOptionCombosAnidados2.Store,
        hiddenName: 'infra_info_option_id_2',
        displayField: 'infra_info_option_name',
        valueField: 'infra_info_option_id',
        editable: true,
        mode: 'remote',
        minChars: 0,
        listeners: {
            'beforerender': function(cb) {
                var value_parent = cb.ownerCt.infra_info_option_id_1.__value;
                cb.getStore().setBaseParam('infra_info_option_parent_id', value_parent);
            },
            'afterrender': function(cb) {
                cb.__value = cb.value;
                cb.setValue('');
                cb.getStore().load({
                    callback: function() {
                        cb.setValue(cb.__value);
                    }
                });
            },
            'select': function(cb, record) {
                if (cb.ownerCt.infra_info_option_id_3) {
                    cb.ownerCt.infra_info_option_id_3.clearValue();
                    cb.ownerCt.infra_info_option_id_3.enable();
                    var store = cb.ownerCt.infra_info_option_id_3.getStore();
                    store.setBaseParam('infra_info_option_parent_id', cb.getValue());
                    store.load();
                }
                if (cb.ownerCt.infra_info_option_id_4) {
                    cb.ownerCt.infra_info_option_id_4.clearValue();
                    cb.ownerCt.infra_info_option_id_4.disable();
                }
            }
        }
    },
    'infra_info_option_id_3': {
        xtype: 'combo',
        ref: 'infra_info_option_id_3',
        disabled: true,
        triggerAction: 'all',
        fieldLabel: App.Language.Infrastructure.infra_info_option_id_3,
        store: App.InfraStructure.InfoOptionCombosAnidados3.Store,
        hiddenName: 'infra_info_option_id_3',
        displayField: 'infra_info_option_name',
        valueField: 'infra_info_option_id',
        editable: true,
        mode: 'remote',
        minChars: 0,
        listeners: {
            'beforerender': function(cb) {
                var value_parent = cb.ownerCt.infra_info_option_id_2.__value;
                cb.getStore().setBaseParam('infra_info_option_parent_id', value_parent);
            },
            'afterrender': function(cb) {
                cb.__value = cb.value;
                cb.setValue('');
                cb.getStore().load({
                    callback: function() {
                        cb.setValue(cb.__value);
                    }
                });
            },
            'select': function(cb, record) {
                if (cb.ownerCt.infra_info_option_id_4) {
                    cb.ownerCt.infra_info_option_id_4.clearValue();
                    cb.ownerCt.infra_info_option_id_4.enable();
                    var store = cb.ownerCt.infra_info_option_id_4.getStore();
                    store.setBaseParam('infra_info_option_parent_id', cb.getValue());
                    store.load();
                }
            }
        }
    },
    'infra_info_option_id_4': {
        xtype: 'combo',
        ref: 'infra_info_option_id_4',
        disabled: true,
        triggerAction: 'all',
        fieldLabel: App.Language.Infrastructure.infra_info_option_id_4,
        store: App.InfraStructure.InfoOptionCombosAnidados4.Store,
        hiddenName: 'infra_info_option_id_4',
        displayField: 'infra_info_option_name',
        valueField: 'infra_info_option_id',
        editable: true,
        mode: 'remote',
        minChars: 0,
        listeners: {
            'beforerender': function(cb) {
                var value_parent = cb.ownerCt.infra_info_option_id_3.__value;
                cb.getStore().setBaseParam('infra_info_option_parent_id', value_parent);
            },
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
    },
    'infra_info_terrero_escritura': {
        xtype: 'numberfield',
        name: 'infra_info_terrero_escritura',
        fieldLabel: App.Language.Infrastructure.infra_info_terrero_escritura,
        allowNegative: false
    },
    'infra_info_terrero_escritura_total': {
        xtype: 'displayfield',
        name: 'infra_info_terrero_escritura_total',
        fieldLabel: App.Language.Infrastructure.infra_info_terrero_escritura_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_terreno_cad': {
        xtype: 'numberfield',
        name: 'infra_info_terreno_cad',
        fieldLabel: App.Language.Infrastructure.infra_info_terreno_cad,
        allowNegative: false
    },
    'infra_info_terreno_cad_total': {
        xtype: 'displayfield',
        name: 'infra_info_terreno_cad_total',
        fieldLabel: App.Language.Infrastructure.infra_info_terreno_cad_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_construidos_ogcu': {
        xtype: 'numberfield',
        name: 'infra_info_construidos_ogcu',
        fieldLabel: App.Language.Infrastructure.infra_info_construidos_ogcu,
        allowNegative: false
    },
    'infra_info_construidos_ogcu_total': {
        xtype: 'displayfield',
        name: 'infra_info_construidos_ogcu_total',
        fieldLabel: App.Language.Infrastructure.infra_info_construidos_ogcu_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_uf': {
        xtype: 'numberfield',
        name: 'infra_info_uf',
        fieldLabel: App.Language.Infrastructure.infra_info_uf,
        allowNegative: false
    },
    'infra_info_uf_total': {
        xtype: 'displayfield',
        name: 'infra_info_uf_total',
        fieldLabel: App.Language.Infrastructure.infra_info_uf_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_money': {
        xtype: 'displayfield',
        name: 'infra_info_money',
        fieldLabel: App.Language.Infrastructure.infra_info_money,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_emplazamiento': {
        xtype: 'numberfield',
        name: 'infra_info_emplazamiento',
        fieldLabel: App.Language.Infrastructure.infra_info_emplazamiento,
        allowNegative: false
    },
    'infra_info_emplazamiento_total': {
        xtype: 'displayfield',
        name: 'infra_info_emplazamiento_total',
        fieldLabel: App.Language.Infrastructure.infra_info_emplazamiento_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_emplazamiento_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_emplazamiento_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_emplazamiento_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_calles': {
        xtype: 'numberfield',
        name: 'infra_info_calles',
        fieldLabel: App.Language.Infrastructure.infra_info_calles,
        allowNegative: false
    },
    'infra_info_calles_total': {
        xtype: 'displayfield',
        name: 'infra_info_calles_total',
        fieldLabel: App.Language.Infrastructure.infra_info_calles_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_porcent_calles': {
        xtype: 'displayfield',
        name: 'infra_info_porcent_calles',
        fieldLabel: App.Language.Infrastructure.infra_info_porcent_calles,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_areas_verdes': {
        xtype: 'numberfield',
        name: 'infra_info_areas_verdes',
        fieldLabel: App.Language.Infrastructure.infra_info_areas_verdes,
        allowNegative: false
    },
    'infra_info_areas_verdes_total': {
        xtype: 'displayfield',
        name: 'infra_info_areas_verdes_total',
        fieldLabel: App.Language.Infrastructure.infra_info_areas_verdes_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_areas_verdes_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_areas_verdes_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_areas_verdes_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_areas_manejadas': {
        xtype: 'numberfield',
        name: 'infra_info_areas_manejadas',
        fieldLabel: App.Language.Infrastructure.infra_info_areas_manejadas,
        allowNegative: false
    },
    'infra_info_areas_manejadas_total': {
        xtype: 'displayfield',
        name: 'infra_info_areas_manejadas_total',
        fieldLabel: App.Language.Infrastructure.infra_info_areas_manejadas_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_areas_manejadas_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_areas_manejadas_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_areas_manejadas_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_patios_abiertos': {
        xtype: 'numberfield',
        name: 'infra_info_patios_abiertos',
        fieldLabel: App.Language.Infrastructure.infra_info_patios_abiertos,
        allowNegative: false
    },
    'infra_info_patios_abiertos_total': {
        xtype: 'displayfield',
        name: 'infra_info_patios_abiertos_total',
        fieldLabel: App.Language.Infrastructure.infra_info_patios_abiertos_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_patios_abiertos_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_patios_abiertos_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_patios_abiertos_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_recintos_deportivos': {
        xtype: 'numberfield',
        name: 'infra_info_recintos_deportivos',
        fieldLabel: App.Language.Infrastructure.infra_info_recintos_deportivos,
        allowNegative: false
    },
    'infra_info_recintos_deportivos_total': {
        xtype: 'displayfield',
        name: 'infra_info_recintos_deportivos_total',
        fieldLabel: App.Language.Infrastructure.infra_info_recintos_deportivos_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_recintos_deportivos_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_recintos_deportivos_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_recintos_deportivos_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_circulaciones_abiertas': {
        xtype: 'numberfield',
        name: 'infra_info_circulaciones_abiertas',
        fieldLabel: App.Language.Infrastructure.infra_info_circulaciones_abiertas,
        allowNegative: false
    },
    'infra_info_circulaciones_abiertas_total': {
        xtype: 'displayfield',
        name: 'infra_info_circulaciones_abiertas_total',
        fieldLabel: App.Language.Infrastructure.infra_info_circulaciones_abiertas_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_circulaciones_abiertas_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_circulaciones_abiertas_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_circulaciones_abiertas_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_otras_areas': {
        xtype: 'numberfield',
        name: 'infra_info_otras_areas',
        fieldLabel: App.Language.Infrastructure.infra_info_otras_areas,
        allowNegative: false
    },
    'infra_info_otras_areas_total': {
        xtype: 'displayfield',
        name: 'infra_info_otras_areas_total',
        fieldLabel: App.Language.Infrastructure.infra_info_otras_areas_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_otras_areas_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_otras_areas_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_otras_areas_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_estacionamientos_num': {
        xtype: 'numberfield',
        name: 'infra_info_estacionamientos_num',
        fieldLabel: App.Language.Infrastructure.infra_info_estacionamientos_num,
        allowNegative: false
    },
    'infra_info_estacionamientos_total': {
        xtype: 'displayfield',
        name: 'infra_info_estacionamientos_total',
        fieldLabel: App.Language.Infrastructure.infra_info_estacionamientos_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_estacionamientos': {
        xtype: 'numberfield',
        name: 'infra_info_estacionamientos',
        fieldLabel: App.Language.Infrastructure.infra_info_estacionamientos,
        allowNegative: false
    },
    'infra_info_estacionamientos_total_sector': {
        xtype: 'displayfield',
        name: 'infra_info_estacionamientos_total_sector',
        fieldLabel: App.Language.Infrastructure.infra_info_estacionamientos_total_sector,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_estacionamientos_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_estacionamientos_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_estacionamientos_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    },
    'infra_info_uf_day_value': {
        xtype: 'displayfield',
        name: 'infra_info_uf_day_value',
        fieldLabel: App.Language.Infrastructure.infra_info_uf_day_value,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_sky_floor_height': {
        xtype: 'numberfield',
        name: 'infra_info_sky_floor_height',
        fieldLabel: App.Language.Infrastructure.infra_info_sky_floor_height,
        allowNegative: false
    },
    'infra_info_walls': {
        xtype: 'displayfield',
        name: 'infra_info_walls',
        fieldLabel: App.Language.Infrastructure.infra_info_walls,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_primer_nivel': {
        xtype: 'numberfield',
        name: 'infra_info_primer_nivel',
        fieldLabel: App.Language.Infrastructure.infra_info_primer_nivel,
        allowNegative: false
    },
    'infra_info_primer_nivel_total': {
        xtype: 'displayfield',
        name: 'infra_info_primer_nivel_total',
        fieldLabel: App.Language.Infrastructure.infra_info_primer_nivel_total,
        setValue: function(v) {
            this.setRawValue(numero(v));
            return this;
        }
    },
    'infra_info_primer_nivel_porcent': {
        xtype: 'displayfield',
        name: 'infra_info_primer_nivel_porcent',
        fieldLabel: App.Language.Infrastructure.infra_info_primer_nivel_porcent,
        setValue: function(value) {
            this.setRawValue(porcentaje(value));
            return this;
        }
    }
};

function numero(value){
    value = parseFloat(value).toFixed(1);
    value = parseFloat(value);
    return value.toLocaleString('es-CL', {minimumFractionDigits: 1});
}

function porcentaje(value){
    value = parseFloat(value).toFixed(1);
    value = parseFloat(value);
    value = value.toLocaleString('es-CL', {minimumFractionDigits: 1});
    return `${value} %`;
}

App.InfraStructure.emportListMasiva = Ext.extend(Ext.Window, {
    title: "Carga masiva",
    resizable: false,
    modal: true,
    width: 500,
    height: 140,
    maximizable: true,
    layout: 'fit',
    padding: 1,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            ref: 'form',
            labelWidth: 150,
            fileUpload: true,
            plugins: [new Ext.ux.OOSubmit()],
            padding: 5,
            items: [{
                xtype: 'fileuploadfield',
                emptyText: "Seleccione Archivo",
                fieldLabel: "Archivo Excell",
                anchor: '100%',
                allowBlank: false,
                fileUpload: true,
                name: 'documentoExcel',
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload_icon'
                }
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
                            url: 'index.php/core/node/bulkLoadExcell',
                            timeout: 10000000000,
                            params: {
                                node_id: App.Interface.selectedNodeId
                            },
                            waitMsg: App.Language.General.message_loading_information,
                            success: function(fp, o) {
                                App.InfraStructure.Store.Principal.load();
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
        App.InfraStructure.emportListMasiva.superclass.initComponent.call(this);
    }
});
