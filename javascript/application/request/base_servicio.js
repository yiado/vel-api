/* global App, Ext */

Ext.namespace('App.Request');
Ext.namespace('App.RequestByNode');

App.General.declareNameSpaces('App.Request', [
    'Services',
    'ServicesStatus',
    'ServicesType',
    'ServicesLog'
]);

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
            Ext.FlashMessage.alert('Debe Seleccionar un Servicio');
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