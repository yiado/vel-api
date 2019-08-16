Ext.namespace('App.Request');
Ext.namespace('App.RequestByNode');

App.General.declareNameSpaces('App.Request', [
    'Problem',
    'Status',
    'InfraEstructura',
    'Asset',
    'Provider',
    'Estados',
    'Solicitudes',
    'SolicitudEstados',
    'SolicitudTipos'
]);

App.Request.moduleActivate = function() {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({
            border: false
        });
    }
}

App.ModuleActions[8000] = {
    iconCls: 'request_icon_32',
    text: App.Language.Request.requests,
    module: 'Request'
}

App.ModuleActions[8001] = {
    text: App.Language.General.add,
    iconCls: 'add_icon',
    id: 'ModuleAction_8001',
    hidden: true,
    handler: function() {
        w = new App.Request.addRequestByNodeWindow({
            title: App.Language.Request.add_request
        });
        w.show();
    }
}

//App.ModuleActions[8001] =
//{
//    text: App.Language.General.add,
//    iconCls: 'add_icon',
//    id: 'ModuleAction_8001',
//    hidden: true,
//    handler: function()
////    {
////        if (App.Interface.selectedNodeId > 0) 
////        {
////            App.Asset.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
////            App.Asset.Store.load();
////            w = new App.Request.addRequestWindow
////            ({
////                title: App.Language.Request.add_request
////            });
////            w.show();
////        } else {
////            Ext.FlashMessage.alert(App.Language.Request.please_select_node);
////        }
////    }
//    {
//        if (App.Interface.selectedNodeId != 'root') {
//            Ext.Ajax.request({
//                waitMsg: App.Language.General.message_generating_file,
//                url: 'index.php/core/nodecontroller/getById',
//                timeout: 10000000000,
//                params: {
//                    node_id: App.Interface.selectedNodeId
//                },
//                success: function(response) {
//                    response = Ext.decode(response.responseText);
//
//
//                    if (response.success == "true") {
//                        w = new App.Request.addRequestByNodeWindow({
//                            title: App.Language.Request.add_request
//                        });
//                        w.show();
//
//                    } else {
//                        //ESTO ES PARA EL BANCO SOLAMENTE   
//                        Ext.MessageBox.alert(App.Language.Core.notification, App.Language.Maintenance.must_be_within_the_folder_locations_to_create_work_order);
//
//                    }
//                },
//                failure: function(response) {
//                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
//                }
//            });
//
//        } else {
//            Ext.FlashMessage.alert(App.Language.Request.please_select_node);
//        }
//    }
//}

App.ModuleActions[8002] = {
        text: App.Language.Request.approve,
        iconCls: 'approve_icon',
        id: 'ModuleAction_8002',
        hidden: true,
        handler: function(b) {
            grid = Ext.getCmp('App.Request.Grid');
            if (grid.getSelectionModel().getCount()) {
                w = new App.Request.addAprobarWindow({ title: 'Aprobar Solicitud' });
                w.show();
                records = grid.getSelectionModel().getSelections();
                aux = new Array();
                for (var i = 0; i < records.length; i++) {

                    Ext.getCmp('App.Request.TipoSolicitud').setValue(records[i].data.SolicitudType.solicitud_type_nombre);
                    Ext.getCmp('App.Request.Usuario').setValue(App.Security.Session.user_username);
                    Ext.getCmp('App.Request.Email').setValue(App.Security.Session.user_email);

                    var iso_date = Date.parseDate(records[i].data.solicitud_fecha, "Y-m-d H:i:s");
                    Ext.getCmp('App.Request.Fecha').setValue(iso_date.format("d/m/Y H:i"));

                    Ext.getCmp('App.Request.FacturaNombre').setValue(records[i].data.solicitud_factura_nombre);
                    Ext.getCmp('App.Request.FacturaNumero').setValue(records[i].data.solicitud_factura_numero);
                    Ext.getCmp('App.Request.OCNombre').setValue(records[i].data.solicitud_oc_nombre);
                    Ext.getCmp('App.Request.OCNumero').setValue(records[i].data.solicitud_oc_numero);
                    Ext.getCmp('App.Request.Comentario').setValue(records[i].data.solicitud_comen_user);
                }
            } else {
                Ext.FlashMessage.alert('Debe Seleccionar una Solicitud');
            }
        }
    }
    //App.ModuleActions[8002] =
    //{
    //    text: App.Language.Request.approve,
    //    iconCls: 'approve_icon',
    //    id: 'ModuleAction_8002',
    //    hidden: true,
    //    handler: function(b)
    ////    {
    ////        grid = Ext.getCmp('App.Request.Grid');
    ////        if (grid.getSelectionModel().getCount()) 
    ////        {
    ////            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.Request.do_you_want_to_Approve_the_request, function(b)
    ////            {
    ////                if (b == 'yes') 
    ////                {   
    ////                    records = Ext.getCmp('App.Request.Grid').getSelectionModel().getSelections();
    ////                    aux = new Array();
    ////                    record_array = new Array();
    ////                    for (var i = 0; i < records.length; i++) 
    ////                    {
    ////                        aux.push(records[i].data.request_id);
    ////                    }
    ////                    record_array = aux.join(',');
    ////                    App.Request.RequestAproved(2, record_array);
    ////                    App.Request.Store.load();
    ////                }
    ////            });
    ////                    
    ////        } else {
    ////            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
    ////        }
    ////    }
    //    {
    //        grid = Ext.getCmp('App.RequestByNode.Grid');
    //        if (grid.getSelectionModel().getCount())
    //        {
    //            w = new App.Request.ApprovedByNodeWindow();
    //            w.show();
    //        } else {
    //            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
    //        }
    //    }
    //}

App.ModuleActions[8003] = {
    text: App.Language.Request.reject,
    iconCls: 'delete_icon',
    id: 'ModuleAction_8003',
    hidden: true,
    handler: function(b) {
        grid = Ext.getCmp('App.Request.Grid');
        if (grid.getSelectionModel().getCount()) {
            w = new App.Request.addRechazarWindow({ title: 'Rechazar Solicitud' });
            w.show();
            records = grid.getSelectionModel().getSelections();
            aux = new Array();
            for (var i = 0; i < records.length; i++) {

                Ext.getCmp('App.RequestRechazar.TipoSolicitud').setValue(records[i].data.SolicitudType.solicitud_type_nombre);
                Ext.getCmp('App.RequestRechazar.Usuario').setValue(App.Security.Session.user_username);
                Ext.getCmp('App.RequestRechazar.Email').setValue(App.Security.Session.user_email);

                var iso_date = Date.parseDate(records[i].data.solicitud_fecha, "Y-m-d H:i:s");
                Ext.getCmp('App.RequestRechazar.Fecha').setValue(iso_date.format("d/m/Y H:i"));

                Ext.getCmp('App.RequestRechazar.FacturaNombre').setValue(records[i].data.solicitud_factura_nombre);
                Ext.getCmp('App.RequestRechazar.FacturaNumero').setValue(records[i].data.solicitud_factura_numero);
                Ext.getCmp('App.RequestRechazar.OCNombre').setValue(records[i].data.solicitud_oc_nombre);
                Ext.getCmp('App.RequestRechazar.OCNumero').setValue(records[i].data.solicitud_oc_numero);
                Ext.getCmp('App.RequestRechazar.Comentario').setValue(records[i].data.solicitud_comen_user);
            }
        } else {
            Ext.FlashMessage.alert('Debe Seleccionar una Solicitud');
        }
    }
}

App.ModuleActions[8004] = {
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    id: 'ModuleAction_8004',
    hidden: true,
    handler: function() {
        w = new App.Request.exportListByNodeWindow();
        w.show();
    }
}