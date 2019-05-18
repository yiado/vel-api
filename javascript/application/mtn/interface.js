App.Interface.addToModuleMenu('mtn', App.ModuleActions[7000]);
App.Mtn.allowRootGui = true;

App.Mtn.Principal = Ext.extend(Ext.TabPanel, 
{
    activeTab: 0,
    border: false,
    initComponent: function()
    {
        this.items = 
        [{
            xtype: 'tabpanel',
            activeTab: 0,
            title: App.Language.Infrastructure.infrastructure,
            items:[
                new App.Mtn.Wo.InterfaceNode(),
//                new App.Mtn.Wo.PlanningNode(),
                new App.Mtn.Wo.ContractNode()
            ]
            
        }
//        , {
//            xtype: 'tabpanel',
//            activeTab: 0,
//            title: App.Language.Asset.assets,
//            items:[
//                new App.Mtn.Wo.Interface(),
//                new App.Mtn.Wo.Planning(),
//                new App.Mtn.Wo.Triggers(),
//                new App.Mtn.Wo.Contract()
//            ]
//        }
        ];
        App.Mtn.Principal.superclass.initComponent.call(this);
    }
});

App.Mtn.Principal.listener = function(node)
{
    if (node.id == 'root') 
    { // seleccionar primer nodo del arbol
    }
    
    if (node && node.id) 
    {
        var parent_path_string = node.getPath('text');
        var tmp_path_string = parent_path_string.replace('/', '').replace('/', '*'); //Quitamos el primer slash de la cadena
        var tmp_ruta_string = tmp_path_string.split('*'); //Separamos el node root del resto del path
//        Ext.getCmp('App.Mtn.SearchForm').path_search.setValue(tmp_ruta_string[1]);
        App.Mtn.Wo.Store.setBaseParam('node_id', node.id);
        App.Mtn.Wo.Store.load();
    }
}
