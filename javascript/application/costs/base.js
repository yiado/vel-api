Ext.namespace('App.Costs');
App.Costs.NameSpaces = ['CostsType', 'CostsMonth'];

App.General.declareNameSpaces('App.Costs', App.Costs.NameSpaces);
App.Costs.moduleActivate = function()
{
    if (App.Interface.selectedNodeId > 0) 
    {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel
        ({
            border: false,
            title: App.Language.Costs.costs
        });
    }
}

App.ModuleActions[10001] =
{
    xtype: 'button',
    text:  App.Language.Costs.costs,
    iconCls: 'costs_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Costs'
}