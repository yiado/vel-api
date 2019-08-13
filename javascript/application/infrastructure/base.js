Ext.namespace('App.InfraStructure');

App.General.declareNameSpaces('App.InfraStructure', [
    'Info',
    'Coordinate',
    'InfoConfig',
    'InfoOption',
    'InfoConfigNuevo',
    'InfoOptionCombosAnidados',
    'InfoOptionCombosAnidados1',
    'InfoOptionCombosAnidados2',
    'InfoOptionCombosAnidados3',
    'InfoOptionCombosAnidados4',
    'OtrosDatos',
    'OtrosDatosResumen',
    'OtrosDatosInfoOption',
    'DatosDinamicos',
    'DatosDinamicosDisponibles',
    'DatosDinamicosAsociados',
    'DatosDinamicosSeleccion',
    'Search',
    'Node',
    'Grupos',
    'GruposById',
    'MovStateUp',
    'MovStateDown',
    'FotoStandar',
    'FotoConfi',
    'Iot'
]);

App.InfraStructure.moduleActivate = function() {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.StructureTreeListener();
    } else {
        return new Ext.Panel({ border: false, title: App.Language.Infrastructure.infrastructure });
    }
}

App.InfraStructure.OtrosDatos.fields = {
    '1': {
        xtype: 'textfield',
        anchor: '80%'
    },
    '2': {
        xtype: 'numberfield',
        allowDecimals: false,
        anchor: '80%'
    },
    '3': {
        xtype: 'numberfield',
        decimalPrecision: 3,
        anchor: '80%'
    },
    '4': {
        xtype: 'datefield',
        anchor: '80%'
    },
    '5': {
        xtype: 'combo',
        store: {
            xtype: 'App.InfraStructure.OtrosDatosInfoOption.Store'
        },
        displayField: 'infra_other_data_option_name',
        valueField: 'infra_other_data_option_id',
        editable: true,
        typeAhead: true,
        selectOnFocus: true,
        forceSelection: true,
        anchor: '80%',
        triggerAction: 'all',
        mode: 'remote',
        minChars: 0,
        listeners: {
            'beforedestroy': function(cb) {
                cb.purgeListeners();
            },
            'afterrender': function(cb) {
                cb.__value = cb.value;
                cb.setValue('');
                cb.getStore().setBaseParam('infra_other_data_attribute_id', cb.name);
                cb.getStore().load({
                    callback: function() {
                        if (cb && cb.store) {
                            cb.setValue(cb.__value);
                        }
                    }
                });
            }
        }
    },
    '6': {
        xtype: 'textfield',
        width: 250,
        disabled: true
    },
    '7': {
        xtype: 'checkbox',
        // checked:true
        listeners: {
            'afterrender': function(combo) {
                if (combo.value == 1) {
                    combo.setValue(true);
                } else {
                    combo.setValue(false);
                }
            }
        }
    }
}

App.InfraStructure.OtrosDatos.classFields = {
    '1': Ext.extend(Ext.form.TextField, App.InfraStructure.OtrosDatos.fields[1]),
    '2': Ext.extend(Ext.form.NumberField, App.InfraStructure.OtrosDatos.fields[2]),
    '3': Ext.extend(Ext.form.NumberField, App.InfraStructure.OtrosDatos.fields[3]),
    '4': Ext.extend(Ext.form.DateField, App.InfraStructure.OtrosDatos.fields[4]),
    '5': Ext.extend(Ext.form.ComboBox, App.InfraStructure.OtrosDatos.fields[5]),
    '6': Ext.extend(Ext.form.TextField, App.InfraStructure.OtrosDatos.fields[6]),
    '7': Ext.extend(Ext.form.TextField, App.InfraStructure.OtrosDatos.fields[7])

}

App.ModuleActions[5000] = {};

App.ModuleActions[5001] = {
    text: App.Language.General.save,
    hidden: true,
    iconCls: 'save_icon',
    handler: function(b) {
        form = b.ownerCt.ownerCt.getForm();
        form.submit({
            waitTitle: App.Language.General.message_please_wait,
            waitMsg: App.Language.General.message_generating_file,
            url: 'index.php/infra/infraotherdatavalue/add'
        });
    }
};

App.ModuleActions[5002] = {
    text: App.Language.General.ddelete,
    hidden: true,
    iconCls: 'delete_icon',
    handler: function(b) {
        grid = b.ownerCt.ownerCt;
        if (grid.getSelectionModel().getCount()) {
            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Plan.sure_to_remove_all_information_associated_with_the_node,
                function(b) {
                    if (b == 'yes') {
                        nodeParent = Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId);
                        grid.getSelectionModel().each(function(record) {
                            App.Node.DeleteProxy(record.data.node_id, function() {
                                nodeParent.removeChild(Ext.getCmp('App.StructureTree.Tree').getNodeById(record.data.node_id));
                                App.InfraStructure.Store.Principal.remove(record);
                            });
                        });
                    }
                });
        } else {
            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
        }
    }
};

App.ModuleActions[5003] = {
    text: App.Language.General.edit,
    hidden: true,
    iconCls: 'edit_icon',
    handler: function(b) {
        checkCount = Ext.getCmp('App.InfraStructure.Principal').grid.getSelectionModel().getCount();
        for (i = 0; i < b.menu.items.length - 1; i++) {
            if (checkCount) {
                b.menu.items.get(i).enable();
            } else {
                b.menu.items.get(i).disable();
            }
        }
    },
    menu: [{
        text: App.Language.General.edit_name,
        iconCls: 'edit_icon',
        handler: function() {
            if (Ext.getCmp('App.InfraStructure.Principal').grid.getSelectionModel().getCount()) {
                App.InfraStructure.editNode(Ext.getCmp('App.InfraStructure.Principal').grid.getSelectionModel().getSelected().data);
            }
        }
    }, {
        text: App.Language.General.cut,
        iconCls: 'cut_icon',
        handler: function() {
            records = Ext.getCmp('App.InfraStructure.Principal').grid.getSelectionModel().getSelections();
            aux = new Array();
            App.InfraStructure.copiedNodes = new Array();
            for (var i = 0; i < records.length; i++) {
                aux.push(records[i].data.node_id);
                App.InfraStructure.copiedNodes.push(Ext.getCmp('App.StructureTree.Tree').getNodeById(records[i].data.node_id));
            }
            App.Node.CutProxy(aux.join(','), function() {});
        }
    }, {
        text: App.Language.General.copy,
        iconCls: 'copy_icon',
        handler: function() {
            records = Ext.getCmp('App.InfraStructure.Principal').grid.getSelectionModel().getSelections();
            aux = new Array();
            for (var i = 0; i < records.length; i++) {
                aux.push(records[i].data.node_id);
            }
            App.Node.CopyProxy(aux.join(','), function() {});
        }
    }, {
        text: App.Language.General.paste,
        iconCls: 'paste_icon',
        handler: function() {
            App.Node.PasteProxy(App.Interface.selectedNodeId, function(response, opts) {
                var obj = Ext.decode(response.responseText);
                nodes = obj.node;
                for (i = 0; i < nodes.length; i++) {
                    node = nodes[i];
                    if (Ext.getCmp('App.StructureTree.Tree').getNodeById(node.id)) {
                        Ext.getCmp('App.StructureTree.Tree').getNodeById(node.id).remove();
                    }
                    var newNode = new Ext.tree.TreeNode(node);
                    Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId).appendChild(newNode);
                    App.InfraStructure.Principal.expand(App.Interface.selectedNodeId);
                }
                App.InfraStructure.Principal.expand(App.Interface.selectedNodeId);
            });
        }
    }]
};

App.ModuleActions[5004] = {
    text: App.Language.General.save,
    hidden: true,
    iconCls: 'save_icon',
    handler: function(b) {
        form = b.ownerCt.ownerCt.getForm();
        form.submit({
            params: {
                node_id: App.Interface.selectedNodeId
            },
            waitTitle: App.Language.General.message_please_wait,
            waitMsg: App.Language.General.message_guarding_information,
            url: 'index.php/infra/infrainfo/add',
            //                            success: function (form, action) {
            //
            //                                App.InfraStructure.Principal.listener(App.Interface.selectedNode);
            //
            //                            }

            success: function(form, response) {
                App.InfraStructure.Principal.listener(App.Interface.selectedNode);
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
};

App.ModuleActions[5005] = {
    text: App.Language.General.add,
    hidden: true,
    iconCls: 'add_icon',
    handler: function() {
        w = new App.InfraStructure.addNodeWindow();
        w.show();
    }
};

App.ModuleActions[5006] = {
    xtype: 'toolbar',
    hidden: true,
    items: [{
        text: App.Language.General.add,
        hidden: false,
        iconCls: 'add_icon',
        id: 'marker_add',
        handler: function() {
            var position = { lat: map.getCenter().lat(), lng: map.getCenter().lng() };
            var marker = new google.maps.Marker({
                position: position,
                map: map,
                draggable: true,

            });
            markers.push(marker);

            var cont = 0;

            marker.addListener('dragend', function() {
                if (cont == 0) {
                    App.InfraStructure.Coordinate.addCoordinate(marker.getPosition().lat(), marker.getPosition().lng());
                    cont++;
                } else {
                    App.InfraStructure.Coordinate.updateCoordinate(marker.getPosition().lat(), marker.getPosition().lng());
                }


            });


            Ext.get('marker_add').setStyle('display', 'none');
            Ext.get('marker_delete').setStyle('display', 'block');
        }
    }, {
        xtype: 'spacer',
        width: 5
    }, {
        text: App.Language.General.ddelete,
        hidden: false,
        id: 'marker_delete',
        iconCls: 'delete_icon',
        handler: function() {
            record = App.InfraStructure.Coordinate.Store.getAt(0);
            if (record) {
                record.phantom = false;
                record.id = record.data.node_id;
            }
            App.InfraStructure.Coordinate.Store.setBaseParam('action', 'delete');
            App.InfraStructure.Coordinate.Store.remove(record);
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];

            Ext.get('marker_add').setStyle('display', 'block');
            Ext.get('marker_delete').setStyle('display', 'none');

            if (Ext.getCmp('App.Plan.Planimetria')) {
                mapRef = Ext.getCmp('App.Plan.Planimetria').map;
                mapRef.geoCodeLookup(infra_default_start);
            }
        }
    }, {
        text: 'Busqueda Interna',
        hidden: false,
        iconCls: 'search_icon_16',
        enableToggle: true,
        toggleHandler: function(btn, state) //ESTE TIPO MANEJA EL ESTADO VA CAMBIANDO CADA VEZ Q SE PRESIONA EL BOTON
            {
                var zoom = (state) ? 9 : 15;
                if (state == false) {
                    //HACE LA BUSQUEDA SOLO DEL NIVEL
                    App.InfraStructure.SearchNodeBranches = null;
                    busquedaInterna = null;

                } else {
                    //HACE LA BUSQUEDA INTERNA
                    App.InfraStructure.SearchNodeBranches = 1;
                    busquedaInterna = App.InfraStructure.SearchNodeBranches;
                }
                //ESTE TROZO SE COPIA DE INTERFACE.JS LINEA 756
                App.InfraStructure.Coordinate.Store.setBaseParam('search_branch', App.InfraStructure.SearchNodeBranches);
                App.InfraStructure.Coordinate.Store.load({
                    callback: function(records) {
                        if (typeof Ext.getCmp('App.InfraStructure.Principal') !== "undefined") { //Fix
                            if (Ext.getCmp('App.InfraStructure.Principal').map) { //Fix
                                mapRef = 'mapTab';

                                if (App.InfraStructure.Coordinate.Store.getTotalCount()) {
                                    Ext.get('marker_add').setStyle('display', 'none');
                                    (state == false) ? Ext.get('marker_delete').setStyle('display', 'block'): Ext.get('marker_delete').setStyle('display', 'none');

                                    if (mapRef) {
                                        //BORRA LOS PUNTOS ANTES DE MARCAR
                                        for (var i = 0; i < markers.length; i++) {
                                            markers[i].setMap(null);
                                        }


                                        for (var i = 0; i < records.length; i++) {
                                            position_busqueda = { lat: Number(records[i].data.node_latitude), lng: Number(records[i].data.node_longitude) };
                                            var marker = new google.maps.Marker({
                                                position: position_busqueda,
                                                map: map,
                                                draggable: state ? false : true,
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

                                                            //                                                                console.log(response.resultsInfraOtherData[0].InfraOtherDataAttribute);
                                                            html = '<h1>Informaci&oacute;n Resumen</h1><hr>'
                                                            html = html + '<div><table style=" padding: 4px; text-align: left;  font: 12 normal;  font: normal 11px tahoma, arial, helvetica, sans-serif;">';
                                                            //                                                             
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

                                                                                if (field.value == null)
                                                                                    field.value = '';

                                                                                //                                                                            field.fieldLabel = record.infra_other_data_attribute_name;
                                                                                //                                                                            html = html + '<tr><td style="padding-bottom: 5px;padding-right: 3px;"><label>';
                                                                                //                                                                            html = html + field.fieldLabel + " : ";
                                                                                //                                                                            html = html + '</label></td><td style="padding-bottom: 5px;">';
                                                                                //                                                                            html = html + field.value;
                                                                                //                                                                            html = html + ' </td></tr>';


                                                                                //
                                                                                //                                                                                    console.log(field.fieldLabel);
                                                                                //                                                                                    console.log(field.value);

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
                                                                        //
                                                                        //                                                                            console.log(record.field);
                                                                        //                                                                            console.log(record.value);
                                                                    }
                                                                }
                                                            }

                                                            html = html + "</div></table>";


                                                            infowindow.setContent(html);

                                                            infowindow.open(map, marker);
                                                        }
                                                    });
                                                }
                                            })(marker, i));
                                        }

                                        map.setCenter(position_busqueda);
                                        map.setZoom(zoom);

                                    } else {
                                        Ext.getCmp('App.InfraStructure.Principal').map.onMapReady = function() {
                                            //BORRA LOS PUNTOS ANTES DE MARCAR
                                            for (var i = 0; i < markers.length; i++) {
                                                markers[i].setMap(null);
                                            }


                                            for (var i = 0; i < records.length; i++) {

                                                position_busqueda = { lat: Number(records[i].data.node_latitude), lng: Number(records[i].data.node_longitude) };
                                                var marker = new google.maps.Marker({
                                                    position: position_busqueda,
                                                    map: map,
                                                    draggable: state ? false : true,
                                                    title: records[i].json.node_name
                                                });

                                                markers.push(marker);
                                            }

                                            map.setCenter(position_busqueda);
                                            map.setZoom(zoom);

                                        }
                                    }
                                } else {

                                    (state == false) ? Ext.get('marker_add').setStyle('display', 'block'): Ext.get('marker_add').setStyle('display', 'none');
                                    Ext.get('marker_delete').setStyle('display', 'none');

                                    if (mapRef) {

                                        for (var i = 0; i < markers.length; i++) {

                                            markers[i].setMap(null);
                                        }

                                        var chile = { lat: -33.5533025, lng: -71.1164465 };

                                        map = new google.maps.Map(document.getElementById(mapRef), {
                                            center: chile,
                                            zoom: 9
                                        });
                                        //                                                               
                                    }
                                }
                            }
                        }
                    }
                });



            } //FIN toggleHandler

    }]
}

App.ModuleActions[5008] = {
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    hidden: true,
    handler: function() {
        w = new App.InfraStructure.exportListWindow();
        w.show();
    }
}