Ext.namespace('App.Mtn');
App.Mtn.NameSpaces = 
[
    'Task', 
    'PossibleStatus', 
    'PossibleStatusByNode', 
    'StateAssigned', 
    'Other', 
    'AssetType', 
    'Wo',
    'WoNode',
    'WoProvider', 
    'WoNodeProvider',
    'WoTypes', 
    'OtherCosts', 
    'OtherCostsByNode', 
    'Task', 
    'TaskByNode', 
    'WoTask', 
    'OtherCostsWo', 
    'WoTaskComponent', 
    'Component', 
    'ComponentByNode', 
    'TypesComponent', 
    'TypesComponentByNode', 
    'PriceListComponent',
    'PriceListComponentNode',
    'FlowWo', 
    'Plan', 
    'PlanByNode', 
    'PlanTask', 
    'WoTypesAll', 
    'WoTypesAllByNode',
    'WoTypesAllByNodeSolo',
    'WoTypesAllByAssetSolo',
    'ComponentType', 
    'ComponentTypeByNode', 
    'PriceListComponentAll', 
    'PriceList', 
    'PriceListByNode', 
    'WoTypesPreventive', 
    'WoPreventive',
    'WoPreventiveByNode',
    'ConfigStateDisponibles',
    'ConfigStateDisponiblesByNode',
    'ConfigStateAsociados',
    'ConfigStateAsociadosAll',
    'MovStateUp',
    'MovStateDown',
    'Log',
    'LogDetail',
    'WoStateForm'
];

App.General.declareNameSpaces('App.Mtn', App.Mtn.NameSpaces);

App.Mtn.moduleActivate = function()
{
    if (App.Interface.selectedNodeId > 0) 
    {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel
        ({
            border: false,
            title: App.Language.Maintenance.maintenance
        });
    }
}

App.ModuleActions[7000] =
{
    xtype: 'button',
    text: App.Language.Maintenance.maintenance,
    iconCls: 'maintain_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Mtn'
}

App.ModuleActions[7001] =
{
    text: App.Language.Maintenance.generate_work_order,
    iconCls: 'add_icon',
    id: 'ModuleAction_7001',
    hidden: true,
    handler: function()
    {
        w = new App.Mtn.generateWorkOrderWindow();
//        Ext.getCmp('App.Mtn.HiddenPanel').setVisible(false);
//        Ext.getCmp('App.Mtn.PanelTotales').setVisible(false);
        App.Mtn.Wo.EditModeFromGrid = false;
        App.Mtn.WoTypes.Store.setBaseParam('show_predictive_ot', 0);
        w.show();
//        w.setHeight(270);
        w.setWidth(780);
        w.center();
    }
}

App.ModuleActions[7003] =
{
    text: App.Language.Maintenance.change_dates,
    iconCls: 'ico_fecha',
    id: 'ModuleAction_7003',
    hidden: true,
    handler: function(b)
    {
        grid = Ext.getCmp('App.Mtn.Wo.Grid');
        if (grid.getSelectionModel().getCount()) 
        {
            records = Ext.getCmp('App.Mtn.Wo.Grid').getSelectionModel().getSelections();
            aux = new Array();
            aux_mtn_work_order_id = new Array();
            for (var i = 0; i < records.length; i++) 
            {
                aux.push(records[i].data.mtn_work_order_id);
            }
            aux_mtn_work_order_id = aux.join(',');
            w = new App.Mtn.Wo.DateWO();
            w.show();
        } else {
            Ext.FlashMessage.alert(App.Language.General.you_have_to_select_an_item_to_set);
        }
    }
}
	
	
