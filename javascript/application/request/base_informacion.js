Ext.namespace('App.Request');
Ext.namespace('App.RequestByNode');

App.General.declareNameSpaces('App.Request', [
    'Information'
]);

App.Request.moduleActivate = function () {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({
            border: false
        });
    }
}