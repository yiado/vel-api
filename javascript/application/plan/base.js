/* global Ext, App */

Ext.namespace('App.Plan');

App.General.declareNameSpaces('App.Plan', [
    'Version',
    'Category',
    'Section',
    'DeleteVersion',
    'Coordinate',
    'Node',
    'PlanNode',
    'Config'
]);

App.Plan.moduleActivate = function() {
    if (App.Interface.selectedNodeId > 0) {
        App.Interface.ViewPort.displayModuleGui();

    } else {
        return new Ext.Panel({
            border: false,
            title: App.Language.Plan.planimetry
        });
    }
};

App.ModuleActions[3000] = {};

App.ModuleActions[3001] = {
    text: App.Language.General.add,
    id: 'ModuleAction_3001',
    hidden: true,
    iconCls: 'add_icon',
    handler: function() {
        w = new App.Plan.addPlanWindow();
        w.form.saveButton.handler = function(b) {
            form = b.ownerCt.ownerCt.getForm();
            if (form.isValid()) {
                form.submit({
                    url: 'index.php/plan/plan/add',
                    params: {
                        node_id: App.Interface.selectedNodeId
                    },
                    waitMsg: App.Language.General.message_up_document,
                    success: function(fp, o) {

                        App.Plan.Store.load({
                            callback: function() {
                                App.Plan.Store.AllVersions.load();
                            }
                        });
                        b.ownerCt.ownerCt.ownerCt.hide();
                    },
                    failure: function(fp, o) {
                        alert('Error:\n' + o.result.msg);
                    }
                });
            }
        };
        w.show();
    }
};

App.ModuleActions[3002] = {
    text: App.Language.General.new_version,
    iconCls: 'copy_icon',
    hidden: true,
    handler: function(b) {

        w = new App.Plan.addPlanWindow({
            title: App.Language.Plan.add_plan_version_title
        });
        w.form.categoria.setReadOnly(true);
        w.form.plan_last_version.hidden = false;
        w.form.plan_last_version.hideLabel = false;
        w.form.plan_description.setReadOnly(true);
        w.form.saveButton.handler = function(bb) {
            form = bb.ownerCt.ownerCt.getForm();
            if (form.isValid()) {
                form.submit({
                    url: 'index.php/plan/plan/add',
                    params: {
                        node_id: App.Interface.selectedNodeId
                    },
                    waitMsg: App.Language.General.message_up_document,
                    success: function(fp, o) {

                        panel = b.ownerCt.ownerCt;

                        panel.__value = o.result.plan_id;
                        panel.plan_id = o.result.plan_id;
                        panel.fireEvent('render', panel);
                        App.Plan.CurrentPlanId = o.result.plan_id;
                        if (o.result.plan_category_id != 4) {
                            panel.tpl.overwrite(panel.body, {
                                plan_filename: o.result.plan_filename,
                                plan_id: o.result.plan_id
                            });
                            App.Plan.Store.AllVersions.load();

                            var idElement = 'plan_embed_' + o.result.plan_id;
                            var msg = '';
                            var embElement = document.getElementById(idElement);
                            embElement.addEventListener("load", function() {

                                setTimeout(function() {
                                    jQuery('.print_icon').show();
                                    jQuery('.controls').show();
                                    if (msg) {
                                        msg.hide();
                                    }
                                }, 1000);
                            }, false);

                            zomm_m(o.result.plan_id, msg);
                        } else {
                            panel.tpl.overwrite(panel.body, {
                                urlBim: o.result.url,
                                plan_id: o.result.plan_id
                            });
                            App.Plan.Store.AllVersions.load();
                        }
                        w.hide();
                    },
                    failure: function(fp, o) {
                        alert('Error:\n' + o.result.msg);
                    }
                });
            }
        };
        Ext.Ajax.request({
            url: 'index.php/plan/version/getLastVersion',
            params: {
                plan_category_id: b.ownerCt.ownerCt.plan_category_id,
                node_id: b.ownerCt.ownerCt.node_id
            },
            success: function(response) {
                response = Ext.decode(response.responseText);
                w.form.plan_description.setValue(response.results[0].plan_description);
                w.form.plan_last_version.setValue(response.results[0].plan_version);
                w.form.categoria.setValue(response.results[0].plan_category_id);
                w.form.categoria.getStore().load({
                    callback: function() {
                        w.show();
                    }
                });
            }
        });
    }
};

App.ModuleActions[3004] = {
    text: App.Language.General.eexport,
    iconCls: 'export_icon',
    hidden: true,
    handler: function() {
        w = new App.Plan.exportListWindow();
        w.show();
    }
};

App.ModuleActions[3005] = {
    iconCls: 'config_icon',
    hiiden: (App.ModuleActions[3001] ? false : true),
    tooltip: "Establecer como Plano Portada en Ficha Resumen",
    text: "Plano Portada",
    handler: function(b) {
        grid = Ext.getCmp('App.Plan.AllVersionsGridAll');
        if (grid.getSelectionModel().getCount() == 1) {
            records = Ext.getCmp('App.Plan.AllVersionsGridAll').getSelectionModel().getSelections();
            plan_id = records[0].data.plan_id;
            Ext.Ajax.request({
                url: 'index.php/plan/plan/setPlanPortada',
                params: { plan_id: plan_id },
                success: function() {
                    App.Plan.Store.AllVersions.reload();
                }
            });

        } else {
            Ext.FlashMessage.alert("Seleccionar un plano.");
        }
    }
};