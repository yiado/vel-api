Ext.namespace('App.Asset');
App.Asset.NameSpaces =
        [
            'Type',
            'Condition',
            'Status',
            'OtrosDatos',
            'Log',
            'Insurance',
            'Document',
            'InsuranceStatus',
            'Measurement',
            'ConfigMeasurement',
            'DatosDinamicos',
            'DatosDinamicosDisponibles',
            'DatosDinamicosAsociados',
            'MovProxy',
            'PasteProxy',
            'Inventory',
            'Papelera',
            'CargaMasiva',
            'AssetLoad',
            'AssetLoadId'
        ];

App.General.declareNameSpaces('App.Asset', App.Asset.NameSpaces);
App.Asset.moduleActivate = function ()
{
    if (App.Interface.selectedNodeId > 0)
    {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({border: false, title: App.Language.Asset.assets});
    }
}

App.ModuleActions[4000] =
        {
//    xtype: 'button',
            iconCls: 'equip_icon_32',
            text: App.Language.Asset.assets,

//    scale: 'large',
//    iconAlign: 'top',
            module: 'Asset'
        }

