/* global Ext, App */

Ext.namespace('App.Report');
App.Report.NameSpaces = [];

App.General.declareNameSpaces('App.Report', App.Report.NameSpaces);
App.Report.moduleActivate = function() {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({
            border: false,
            title: App.Language.Report.reports
        });
    }
};

App.ModuleActions[9000] = {
    iconCls: 'reports_icon_32',
    text: App.Language.Report.reports,
    module: 'Report'
};