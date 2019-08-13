Ext.namespace('App.Iot');

App.General.declareNameSpaces('App.Iot', [
    'Device',
    'Sensors'

]);

App.Iot.moduleActivate = function() {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({ border: false, title: App.Language.Iot.iot });
    }
}