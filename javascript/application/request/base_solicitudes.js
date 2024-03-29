/* global Ext, App */

Ext.namespace('App.Request');

App.General.declareNameSpaces('App.Request', [
    'Solicitudes',
    'SolicitudEstados',
    'SolicitudTipos',
    'SolicitudLog',
    'Services',
    'ServicesStatus',
    'ServicesType',
    'ServicesLog',
    'ServicesStatusChart',
    'ServicesTypeChart',
    'ServicesDateChart',
    'ServicesOrganismChart',
    'Information',
    'InformationStatus',
    'InformationLog',
    'RequestEvaluation'
]);

App.Request.moduleActivate = function() {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({ border: false, title: App.Language.Requests.requests });
    }
};

App.ModuleActions[8000] = {
    iconCls: 'request_icon_32',
    text: App.Language.Request.requests,
    module: 'Request'
};


/**
 * Activos
 */
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
};

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
                App.Request.Solicitud_id = records[i].data.solicitud_id;
                Ext.getCmp('App.Request.Folio').setValue(records[i].data.solicitud_folio);
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
            Ext.FlashMessage.alert('Debe seleccionar una Solicitud');
        }
    }
};

App.ModuleActions[8003] = {
    text: App.Language.Request.reject,
    iconCls: 'delete_icon',
    id: 'ModuleAction_8003',
    hidden: true,
    handler: function(b) {
        let grid = Ext.getCmp('App.Request.Grid');
        if (grid.getSelectionModel().getCount()) {
            let w = new App.Request.addRechazarWindow({ title: 'Rechazar Solicitud' });
            w.show();
            let records = grid.getSelectionModel().getSelections();
            let aux = new Array();
            for (var i = 0; i < records.length; i++) {
                App.Request.Solicitud_id = records[i].data.solicitud_id;
                Ext.getCmp('App.RequestRechazar.Folio').setValue(records[i].data.solicitud_folio);
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
            Ext.FlashMessage.alert('Debe seleccionar una Solicitud');
        }
    }
};

App.ModuleActions[8004] = {
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    id: 'ModuleAction_8004',
    hidden: true,
    handler: function() {
        w = new App.Request.exportListByNodeWindow();
        w.show();
    }
};

App.ModuleActions[8005] = {
    text: App.Language.Request.history,
    id: 'ModuleAction_8005',
    hidden: true,
    iconCls: 'config_icon',
    handler: function(b) {
        grid = Ext.getCmp('App.Request.Grid');
        if (grid.getSelectionModel().getCount()) {
            records = grid.getSelectionModel().getSelections();
            aux = new Array();
            for (var i = 0; i < records.length; i++) {
                App.Request.Solicitud_id = records[i].data.solicitud_id;
                folio = records[i].data.solicitud_folio;
                App.Request.SolicitudLog.Store.setBaseParam('solicitud_id', App.Request.Solicitud_id);
                App.Request.SolicitudLog.Store.load();
            }

            w = new App.Request.historialWindow({ title: 'Historial Solicitud Folio =>' + folio });
            w.show();


        } else {
            Ext.FlashMessage.alert('Debe Seleccionar una Solicitud');
        }

    }
};

App.ModuleActions[8006] = {
    id: 'ModuleAction_8006',
    hidden: true,
    text: App.Language.General.search,
    iconCls: 'search_icon_16',
    enableToggle: true,
    handler: function(b) {
        if (b.ownerCt.ownerCt.form.isVisible()) {
            b.ownerCt.ownerCt.form.hide();
        } else {
            b.ownerCt.ownerCt.form.show();
        }
        b.ownerCt.ownerCt.doLayout();
    }
};


/**
 * Servicios
 */
App.ModuleActions[8009] = {
    text: App.Language.General.add,
    iconCls: 'add_icon',
    id: 'ModuleAction_8009',
    hidden: true,
    handler: function() {
        w = new App.Request.addRequestServiceByNodeWindow({
            title: App.Language.Request.add_service
        });
        w.show();
    }
};

App.ModuleActions[8010] = {
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    id: 'ModuleAction_8010',
    hidden: true,
    handler: function() {
        w = new App.Request.exportServiceListByNodeWindow();
        w.show();
    }
};

App.ModuleActions[8011] = {
    text: App.Language.Request.history_service,
    id: 'ModuleAction_8011',
    hidden: true,
    iconCls: 'config_icon',
    handler: function(b) {
        grid = Ext.getCmp('App.Request.Service.Grid');
        if (grid.getSelectionModel().getCount()) {
            records = grid.getSelectionModel().getSelections();
            aux = new Array();
            
            records.forEach(function (serviceHistory){
                App.Request.Service_id = serviceHistory.data.service_id;
                App.Request.ServicesLog.Store.setBaseParam('service_id', App.Request.Service_id);
                App.Request.ServicesLog.Store.load();
            });
            w = new App.Request.historialServiceWindow({ title: 'Historial Servicio' });
            w.show();
        } else {
            Ext.FlashMessage.alert('Debe Seleccionar un registro');
        }
    }
};

App.ModuleActions[8012] = {
    id: 'ModuleAction_8012',
    hidden: true,
    text: App.Language.General.search,
    iconCls: 'search_icon_16',
    enableToggle: true,
    handler: function(b) {
        if (b.ownerCt.ownerCt.form.isVisible()) {
            b.ownerCt.ownerCt.form.hide();
        } else {
            b.ownerCt.ownerCt.form.show();
        }
        b.ownerCt.ownerCt.doLayout();
    }
};

var type = null;
App.ModuleActions[8013] = {
    xtype: 'splitbutton',
    text: 'Estados',
    hidden: true,
    iconCls: 'edit_icon',
    id: 'btn-service-status',
    menu: [],
    listeners: {
        beforerender: function(){
            let btn = Ext.getCmp('btn-service-status');            
            App.Request.ServicesStatus.Store.data.items.forEach(function(serviceStatus){
                if (serviceStatus.id === '1') {
                    return;
                }
                btn.menu.add({
                    text:  serviceStatus.data.service_status_name,
                    iconCls: 'add_icon',
                    handler: function() {
                        let record = Ext.getCmp('App.Request.Service.Grid').getSelectionModel().getSelected();
                        if ( record ) {
                            let data = record.data;
                            if (data.service_status_id === '4') {
                                Ext.FlashMessage.alert(`El servicio esta ${data.ServiceStatus.service_status_name}`);
                                return;
                            } else if (serviceStatus.data.service_status_id === data.service_status_id) {
                                Ext.FlashMessage.alert(`El servicio ya esta ${data.ServiceStatus.service_status_name}`);
                                return;
                            }
                            w = new App.Request.changeServiceStatusWindow({ title: 'Cambio estado de requerimiento' });
                            w.show();
                            App.Request.Service_id = data.service_id;
                            Ext.getCmp('App.Service.Request.btnChangeServiceStatusWindow').setText(serviceStatus.data.service_status_name);
                            Ext.getCmp('App.Request.Service.Usuario').setValue(data.User.user_username);
                            Ext.getCmp('App.Request.Service.Email').setValue(data.User.user_email);
                            Ext.getCmp('App.Request.Service.Phone').setValue(data.service_phone);
                            Ext.getCmp('App.Request.Service.Organism').setValue(data.service_organism);
                            Ext.getCmp('App.Request.Service.ServiceType').setValue(data.ServiceType.service_type_id).setDisabled(true);
                            Ext.getCmp('App.Request.Service.ServiceStatus').setValue(data.ServiceStatus.service_status_id).setDisabled(true);
                            Ext.getCmp('App.Request.Service.ServiceStatusNew').setValue(serviceStatus.data.service_status_id).setDisabled(true);
                            Ext.getCmp('App.Request.Service.Commentary').setValue(data.service_commentary);
                            
                            let comentary = Ext.getCmp("App.Request.Service.Reject.Comentary");
                            let comentary_box = Ext.getCmp("App.Request.Service.Reject.Comentary.Box");
                            if (serviceStatus.data.service_status_id === '6') {
                                comentary.show();
                                comentary_box.allowBlank = false;
                            } else {
                                comentary.hide();
                                comentary_box.allowBlank = true;
                            }
                        } else {
                            Ext.FlashMessage.alert('Debe seleccionar un registro');
                        }
                    }
                });
            });
        }
    }
};

App.ModuleActions[8014] = {
    text: 'Estadísticas',
    id: 'ModuleAction_8014',
    hidden: true,
    iconCls: 'filter_icon',
    handler: function(b) {
        w = new App.Request.statistics({
            title: 'Estadísticas'
        });
        w.show();
    }
};



/**
 * Rdi
 */
App.ModuleActions[8016] = {
    text: App.Language.General.add,
    iconCls: 'add_icon',
    id: 'ModuleAction_8016',
    hidden: true,
    handler: function() {
        w = new App.Request.addRdiByNodeWindow({
            title: App.Language.Request.rdi_add
        });
        w.show();
    }
};

App.ModuleActions[8017] = {
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    id: 'ModuleAction_8017',
    hidden: true,
    handler: function() {
        w = new App.Request.exportRdiListByNodeWindow();
        w.show();
    }
};

App.ModuleActions[8018] = {
    text: App.Language.Request.rdi_history,
    id: 'ModuleAction_8018',
    hidden: true,
    iconCls: 'config_icon',
    handler: function(b) {
        grid = Ext.getCmp('App.Request.Rdi.Grid');
        if (grid.getSelectionModel().getCount()) {
            records = grid.getSelectionModel().getSelections();
            aux = new Array();
            
            records.forEach(function (rdiHistory){
                App.Request.Rdi_id = rdiHistory.data.rdi_id;
                App.Request.InformationLog.Store.setBaseParam('rdi_id', App.Request.Rdi_id);
                App.Request.InformationLog.Store.load();
            });
            w = new App.Request.historialRdiWindow({ title: App.Language.Request.rdi_history });
            w.show();
        } else {
            Ext.FlashMessage.alert('Debe seleccionar un registro');
        }
    }
};

App.ModuleActions[8019] = {
    xtype: 'splitbutton',
    text: App.Language.Request.rdi_status,
    hidden: true,
    iconCls: 'edit_icon',
    id: 'btn-rdi-status',
    menu: [],
    listeners: {
        beforerender: function(){
            let btn = Ext.getCmp('btn-rdi-status');            
            App.Request.InformationStatus.Store.data.items.forEach(function(rdiStatus){
                if (rdiStatus.id === '1') {
                    return;
                }
                btn.menu.add({
                    text:  rdiStatus.data.rdi_status_name,
                    iconCls: 'add_icon',
                    handler: function() {
                        let record = Ext.getCmp('App.Request.Rdi.Grid').getSelectionModel().getSelected();
                        if ( record ) {
                            let data = record.data;
                            if (data.rdi_status_id === '4') {
                                Ext.FlashMessage.alert(`El requerimiento esta en estado ${data.RdiStatus.rdi_status_name}`);
                                return;
                            } else if (rdiStatus.data.rdi_status_id === data.rdi_status_id) {
                                Ext.FlashMessage.alert(`El requerimiento ya esta en estado ${data.RdiStatus.rdi_status_name}`);
                                return;
                            }
                            w = new App.Request.changeRdiStatusWindow({ title: 'Cambio de estado' });
                            w.show();
                            App.Request.Rdi_id = data.rdi_id;
                            Ext.getCmp('App.Rdi.Request.btnChangeRdiStatusWindow').setText(rdiStatus.data.rdi_status_name);
                            Ext.getCmp('App.Request.Rdi.Usuario').setValue(data.User.user_username);
                            Ext.getCmp('App.Request.Rdi.Phone').setValue(data.rdi_phone);
                            Ext.getCmp('App.Request.Rdi.Organism').setValue(data.rdi_organism);
                            Ext.getCmp('App.Request.Rdi.Email').setValue(data.User.user_email);
                            Ext.getCmp('App.Request.Rdi.RdiStatus').setValue(data.RdiStatus.rdi_status_id).setDisabled(true);
                            Ext.getCmp('App.Request.Rdi.RdiStatusNew').setValue(rdiStatus.data.rdi_status_id).setDisabled(true);
                            Ext.getCmp('App.Request.Rdi.Description').setValue(data.rdi_description);
                            let comentary = Ext.getCmp("App.Request.Rdi.Reject.Comentary");
                            let comentary_box = Ext.getCmp("App.Request.Rdi.Reject.Comentary.Box");
                            if (rdiStatus.data.rdi_status_id == '2') {
                                comentary.show();
                                comentary_box.allowBlank = false;
                            } else {
                                comentary.hide();
                                comentary_box.allowBlank = true;
                            }
                        } else {
                            Ext.FlashMessage.alert('Debe seleccionar un registro');
                        }
                    }
                });
            });
        }
    }
};

App.ModuleActions[8020] = {
    id: 'ModuleAction_8020',
    hidden: true,
    text: App.Language.General.search,
    iconCls: 'search_icon_16',
    enableToggle: true,
    handler: function(b) {
        if (b.ownerCt.ownerCt.form.isVisible()) {
            b.ownerCt.ownerCt.form.hide();
        } else {
            b.ownerCt.ownerCt.form.show();
        }
        b.ownerCt.ownerCt.doLayout();
    }
};