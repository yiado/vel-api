Ext.namespace('App.Interface');

App.Interface.activeModule = null;
App.Interface.selectedNodeId = 'root';
App.Interface.ModuleMenu = new Array();

App.Interface.addToModuleMenu = function(ns, button) {
    App.Interface.ModuleMenu[App.Interface.ModuleMenu.length] = button;
}

App.Interface.getUserModuleMenu = function() {
    return App.Interface.ModuleMenu;
}

App.Interface.ViewPort = Ext.extend(Ext.Viewport, {
    layout: 'border',
    initComponent: function() {
        this.items = [{
            xtype: 'panel',
            region: 'center',
            animCollapse: false,
            header: false,
            headerAsText: false,
            maskDisabled: false,
            layout: 'border',
            border: false,
            items: [{
                xtype: 'panel',
                id: 'App.PrincipalPanel',
                region: 'center',
                bbar: {
                    autoScroll: true
                },
                collapsible: false,
                layout: 'fit',
                style: 'padding: 5 5 5 0',
                collapseMode: 'mini',
                enableTabScroll: true
            }]
        }, {
            xtype: 'toolbar',
            region: 'north',
            id: 'north',
            height: 27,
            border: false,
            items: App.General.TopMenu
        }, {
            xtype: 'panel',
            region: 'west',
            width: 120,
            style: 'padding: 5 0 5 5',
            closable: false,
            collapseMode: 'mini',
            layout: 'border',
            border: false,
            collapsible: true,
            split: true,
            header: false,
            items: [{
                xtype: 'panel',
                region: 'north',
                height: 26,
                bodyCfg: {
                    cls: 'x-panel-header',
                    html: App.Language.Infrastructure.infrastructure
                }
            }, {
                xtype: 'panel',
                region: 'center',
                autoScroll: true,
                layout: {
                    type: 'vbox',
                    padding: '5',
                    align: 'stretch'
                },
                defaults: {
                    margins: '0 0 5 0',
                    pressed: false,
                    toggleGroup: 'App.ActiveModule',
                    allowDepress: false,
                    toggleHandler: function(btn, state) {
                        if (state == false) {
                            return;
                        }

                        Ext.getCmp('App.PrincipalPanel').removeAll();
                        App.Interface.activeModule = btn.module;
                        App.Interface.ViewPort.displayModuleGui(null);

                        // tree toolbar search
                        Ext.getCmp('App.StructureTree.ToolBarSearch').removeAll();
                        Ext.getCmp('App.StructureTree.ToolBarSearch').hide();
                        if (eval("App." + App.Interface.activeModule + ".treeSearchToolBar")) {
                            Ext.getCmp('App.StructureTree.ToolBarSearch').add(eval("App." + App.Interface.activeModule + ".treeSearchToolBar"));
                            Ext.getCmp('App.StructureTree.ToolBarSearch').show();
                            Ext.getCmp('App.StructureTree.ToolBarSearch').doLayout();
                        }

                    }
                },
                baseCls: 'app-module-panel-admin',
                items: App.Interface.getUserModuleMenu()
            }, {
                xtype: 'panel',
                hidden: true,
                id: 'App.StructureTree.TreeContainer',
                baseCls: 'app-module-infra',
                border: false,
                layout: 'fit',
                tbar: {
                    xtype: 'toolbar',
                    id: 'App.StructureTree.ToolBarSearch',
                    hidden: true
                },
                items: [{
                    xtype: App.StructureTree.Tree.getUserTree(),
                    id: 'App.StructureTree.Tree',
                    border: false,
                    hidden: true
                }, {
                    xtype: 'panel',
                    id: 'App.Panel.StructureTree.Search',
                    hidden: true,
                    layout: 'fit',
                    border: false
                }]
            }]
        }];
        App.Interface.ViewPort.superclass.initComponent.call(this);
    }
});

App.Interface.ViewPort.displayModuleGui = function(node) {
    if (App.Interface.activeModule == null)
        return;

    Ext.getCmp('App.PrincipalPanel').removeAll();
    Ext.getCmp('App.PrincipalPanel').add(eval("new App." + App.Interface.activeModule + ".Principal()"));
    Ext.getCmp('App.PrincipalPanel').doLayout();

};

Ext.onReady(function() {
    Ext.QuickTips.init();
    vp = new App.Interface.ViewPort();
    // tree loading mask

    var arrayTB = [{
        text: App.Language.General.set_vista,
        iconCls: 'zoomfit_icon',
        handler: function(b) {
            alert(b.ownerCt.ownerCt.title);
        }
    }, {
        xtype: 'spacer',
        width: 5
    }, {
        text: App.Language.General.zoom,
        iconCls: 'zoomloc_icon'
    }, {
        xtype: 'spacer',
        width: 5
    }, '-', {
        xtype: 'spacer',
        width: 5
    }, {
        text: App.Language.General.layers,
        iconCls: 'layer_icon'
    }];

    vp.render();

    App.Security.loadActions();

    /*App.Mtn.Wo.OpenEditMode(547);*/

});