Ext.namespace('App.Security');
Ext.namespace('App.Security.Session');
Ext.namespace('App.Security.Actions');

App.ModuleActions = new Array();

App.Security.Actions = new Array();

App.Security.isSessionAlive = function() {
    Ext.Ajax.request({
        url: 'index.php/core/auth/isLoggedIn',
        success: function(response) {
            response = Ext.decode(response.responseText);
            if (response.success == false) {
                Ext.MessageBox.show({
                    title: App.Language.General.error,
                    msg: App.Language.General.section_was_completed,
                    buttons: Ext.MessageBox.OK,
                    animEl: 'mb9',
                    fn: function() {
                        document.location = 'index.php';
                    },
                    icon: Ext.MessageBox.WARNING
                });
            } else {
                //Update de la fecha del servidor
                App.Security.Session.system_current_date = response.system_server_date;
            }
        },
        failure: function() {
            document.location = 'index.php';
        }
    });
};

Ext.TaskMgr.start({
    run: App.Security.isSessionAlive,
    interval: 600000 // 10 minutos
});

App.Security.loadActions = function() {
    for (i in App.Security.Actions) {
        var action_id = App.Security.Actions[i];
        if (App.ModuleActions[action_id]) {
            App.ModuleActions[action_id].hidden = false;
        }
    }
};

App.Security.checkNodeAccess = function(node) {
    Ext.Ajax.request({
        url: 'index.php/core/user/checkaccessnode',
        success: function(response) {
            response = Ext.decode(response.responseText);
            if (response.success == false) {
                Ext.getCmp('App.PrincipalPanel').removeAll();
                Ext.getCmp('App.PrincipalPanel').doLayout();
            } else {
                if (App.Interface.activeModule == null) {
                    return;
                }
                if (node.id == 'root' && eval("App." + App.Interface.activeModule + ".allowRootGui")) {
                    App.Interface.ViewPort.displayModuleGui(node);
                    eval("App." + App.Interface.activeModule + ".Principal.listener(node)");
                    return;
                }
                if (Ext.getCmp('App.PrincipalPanel').items.getCount() > 0) {
                    eval("App." + App.Interface.activeModule + ".Principal.listener(node)");
                } else if (node.id != 'root') {
                    App.Interface.ViewPort.displayModuleGui(node);
                    eval("App." + App.Interface.activeModule + ".Principal.listener(node)");
                }
            }
        },
        failure: function() {},
        params: {
            node_id: node.id
        }
    });
}