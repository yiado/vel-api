Ext.namespace('App.Request');
Ext.namespace('App.RequestByNode');

App.General.declareNameSpaces('App.Request', 
[
    'Solicitudes',
    'SolicitudEstados',
    'SolicitudTipos',
    'SolicitudLog'
]);

App.Request.moduleActivate = function()
{
    if (App.Interface.selectedNodeId > 0) 
    {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel
        ({
            border: false
        });
    }
}

App.ModuleActions[8000] =
{
//    xtype: 'button',
    iconCls: 'request_icon_32',
    text: App.Language.Request.requests,
    
//    scale: 'large',
//    iconAlign: 'top',
    module: 'Request'
}

App.ModuleActions[8001] =
{
    text: App.Language.General.add,
    iconCls: 'add_icon',
    id: 'ModuleAction_8001',
    hidden: true,
    handler: function()
    {
        w = new App.Request.addRequestByNodeWindow({
            title: App.Language.Request.add_request
        });
        w.show();
    }
}

App.ModuleActions[8002] =
{
    text: App.Language.Request.approve,
    iconCls: 'approve_icon',
    id: 'ModuleAction_8002',
    hidden: true,
    handler: function(b)
    {
        grid = Ext.getCmp('App.Request.Grid');
        if (grid.getSelectionModel().getCount())
        {
            w = new App.Request.addAprobarWindow({ title: 'Aprobar Solicitud'});
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
            Ext.FlashMessage.alert('Debe Seleccionar una Solicitud');
        }
    }
}

App.ModuleActions[8003] =
{
    text: App.Language.Request.reject,
    iconCls: 'delete_icon',
    id: 'ModuleAction_8003',
    hidden: true,
    handler: function(b)
    {
        grid = Ext.getCmp('App.Request.Grid');
        if (grid.getSelectionModel().getCount())
        {
            w = new App.Request.addRechazarWindow({ title: 'Rechazar Solicitud'});
            w.show();
            records = grid.getSelectionModel().getSelections();
            aux = new Array();
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
            Ext.FlashMessage.alert('Debe Seleccionar una Solicitud');
        }
    }
}

App.ModuleActions[8004] =
{
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    id: 'ModuleAction_8004',
    hidden: true,
    handler: function()
    {
        w = new App.Request.exportListByNodeWindow();
        w.show();
    }
}

App.ModuleActions[8005] =
{
    text: App.Language.Request.history,
    id: 'ModuleAction_8005',
    hidden: true,
    iconCls: 'config_icon',
    handler: function(b) {
        grid = Ext.getCmp('App.Request.Grid');
        if (grid.getSelectionModel().getCount())
        {
            records = grid.getSelectionModel().getSelections();
            aux = new Array();
            for (var i = 0; i < records.length; i++) {
                App.Request.Solicitud_id = records[i].data.solicitud_id;
                folio = records[i].data.solicitud_folio;
                App.Request.SolicitudLog.Store.setBaseParam('solicitud_id', App.Request.Solicitud_id);
                App.Request.SolicitudLog.Store.load();
            }

            w = new App.Request.historialWindow({ title: 'Historial Solicitud Folio =>' +  folio});
            w.show();


        } else {
            Ext.FlashMessage.alert('Debe Seleccionar una Solicitud');
        }

    }
}

App.ModuleActions[8006] =
{
    id: 'ModuleAction_8006',
    hidden: true,
    text: App.Language.General.search,
    iconCls: 'search_icon_16',
    enableToggle: true,
    handler: function(b) {
        if (b.ownerCt.ownerCt.form.isVisible())
        {
            b.ownerCt.ownerCt.form.hide();
        } else {
            b.ownerCt.ownerCt.form.show();
        }
        b.ownerCt.ownerCt.doLayout();
    }
}
