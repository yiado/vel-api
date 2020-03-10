Ext.namespace('App.Maintainers.*');
Ext.namespace('App.Interface.*');

App.Maintainers.activeModule = null;
App.Maintainers.ModuleMenu = new Array();
App.Interface.ModuleMenu = new Array();

App.Maintainers.addToModuleMenu = function(ns, button) {
    App.Maintainers.ModuleMenu[ns] = button;
}

App.Maintainers.getUserModuleMenu = function() {
    var aux = new Array();
    for (i in App.UserModules) {
        aux[i] = App.Maintainers.ModuleMenu[App.UserModules[i]];
    }
    return aux;
}

App.Interface.addToModuleMenu = function(ns, button) {

    App.Interface.ModuleMenu[ns] = button;
    App.Interface.ModuleMenu[ns].id = 'App.ModulePanel.Button' + ns;
}

App.Maintainers.getUserModuleMenuBar = function() {
    App.UserModulesMenu.push('paneladmin');
    var aux = new Array();
    for (i in App.UserModulesMenu) {
        aux[i] = App.Interface.ModuleMenu[App.UserModulesMenu[i]];
    }

    return aux;
};

App.Interface.panel = {
    iconCls: 'people_icon_32',
    text: 'Panel de Administración',
    module: 'panelAdmin',
    hidden: (App.Security.Session.user_type == 'N' || App.Security.Session.user_type == 'P' ? true : false)
}
App.Interface.addToModuleMenu('paneladmin', App.Interface.panel);


App.Maintainers.ViewPort = Ext.extend(Ext.Viewport, {
    layout: 'border',
    initComponent: function() {
        this.items = [{
            xtype: 'panel',
            region: 'center',
            animCollapse: false,
            header: false,
            layout: 'border',
            border: false,
            items: [{
                xtype: 'panel',
                id: 'App.Maintainers.PrincipalPanel',
                region: 'center',
                collapsible: false,
                layout: 'fit',
                closable: true,
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
            width: 106,
            padding: '',
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
                    html: App.Language.Core.modules
                }
            }, {
                xtype: 'panel',
                region: 'center',
                width: 100,
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
                    autoScroll: true,
                    toggleHandler: function(btn, state) {
                        if (state == false) {
                            return;
                        }
                        App.Maintainers.activeModule = btn.module;
                        Ext.getCmp('App.Maintainers.PrincipalPanel').removeAll();
                        Ext.getCmp('App.Maintainers.PrincipalPanel').add(eval("new App.Maintainers." + App.Maintainers.activeModule + ".Principal();"));
                        Ext.getCmp('App.Maintainers.PrincipalPanel').doLayout();
                    }
                },
                baseCls: 'app-module-panel-admin',
                items: App.Maintainers.getUserModuleMenu()
            }]
        }];
        App.Maintainers.ViewPort.superclass.initComponent.call(this);
    }
});

Ext.onReady(function() {
    Ext.QuickTips.init();

    App.General.TopMenu.splice(1, 0, {
        text: 'Menú',
        iconCls: 'list_icon',
        ref: '../btn_menu',
        id: 'btn_menu',
        menu: {
            xtype: 'menu',
            plain: true,
            cls: 'menu_class',
            items: App.Maintainers.getUserModuleMenuBar(),
            defaults: {
                handler: function(btn, state) {
                    if (btn.module != 'panelAdmin') {
                        Ext.Ajax.request({
                            url: 'index.php/gui/administrator/getModule',
                            params: {
                                module: btn.module,
                                moduleName: btn.text
                            },
                            success: function(response) {
                                document.location = App.BaseUrl
                            }
                        });

                    } else {
                        document.location = App.BaseUrl + 'index.php/administrator';
                    }

                }
            }
        }



    });
    vp = new App.Maintainers.ViewPort();
});