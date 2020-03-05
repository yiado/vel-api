Ext.namespace('App.Interface.*');
Ext.namespace('App.Document.*');
App.Interface.activeModule = null;
App.Interface.selectedNodeId = 'root';
App.Interface.nodeRoot = null;
App.Interface.flat = false;
App.Interface.ModuleMenu = new Array();
App.Interface.Aux = '';
App.Interface.title = '';
App.Interface.permits = false;


App.Interface.addToModuleMenu = function(ns, button) {

    App.Interface.ModuleMenu[ns] = button;
    App.Interface.ModuleMenu[ns].id = 'App.ModulePanel.Button' + ns;
}



App.Interface.getUserModuleMenu = function() {

    App.UserModules.push('paneladmin');
    var aux = new Array();
    for (i in App.UserModules) {
        aux[i] = App.Interface.ModuleMenu[App.UserModules[i]];
    }

    return aux;
};

App.Interface.refresTabs = function() {


    if (App.Security.Actions['5000'] != undefined) {

        App.InfraStructure.Info.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.doLayout();
        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.getTopToolbar().hide();
        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.removeAll();
    }

    if ((App.Security.Actions['2006'] != undefined)) {
        App.Document.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        App.Document.Store.load();
        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_documentos.getTopToolbar().show();
    }

    //        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_activos.getTopToolbar().show();
    if ((App.Security.Actions['2006'] != undefined) || (App.Security.Actions['5000'] != undefined)) {
        if (Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.activeTab.refName != 'panel_documentos') {
            var myMask = new Ext.LoadMask(Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.body, { msg: "Cargando" });
            myMask.show();
        }
    }

    if (App.Security.Actions['5000'] != undefined) {
        Ext.Ajax.request({
            url: 'index.php/infra/infrainfo/get',
            params: {
                node_id: App.Interface.selectedNodeId
            },
            before: function() {


            },
            success: function(response) {

                response = Ext.decode(response.responseText);
                aux = new Ext.form.FieldSet({
                    title: App.Language.Infrastructure.structural_data,
                    layout: 'form',
                    collapsible: true,
                    labelWidth: 150,
                    bodyCssClass: 'file_style'
                });
                for (i in response.resultsInfraInfo) {

                    record = response.resultsInfraInfo[i];
                    if (typeof record === 'object') {

                        field = App.InfraStructure.Info.fields[record.field];
                        //El fieldLabel se setea en la variable field(infraestructure/base.js).
                        if (field.xtype == 'combo' && parseInt(record.value, 10) > 0) {
                            field.disabled = false;
                        }
                        field.value = record.value;
                        aux.add(field);
                    }
                }

                if (response.resultsInfraInfo) {
                    if (response.resultsInfraInfo.length) {
                        App.Interface.Aux = aux;
                        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.add(aux);
                        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.doLayout();
                    }
                }

                for (i in response.resultsInfraOtherData) {

                    if (typeof response.resultsInfraOtherData[i] === 'object') {
                        aux = new Ext.form.FieldSet({
                            title: response.resultsInfraOtherData[i].infra_grupo_nombre,
                            layout: 'form',
                            collapsible: true,
                            labelWidth: 150,
                            bodyCssClass: 'file_style'
                        });
                        for (x in response.resultsInfraOtherData[i].InfraOtherDataAttribute) {

                            record = response.resultsInfraOtherData[i].InfraOtherDataAttribute[x];
                            if (typeof record === 'object') {

                                field = App.InfraStructure.OtrosDatos.fields[record.infra_other_data_attribute_type];
                                if (record.InfraOtherDataValue[0]) {
                                    if (record.InfraOtherDataValue[0].infra_other_data_option_id != null) { //Tipo Combo
                                        field.value = record.InfraOtherDataValue[0].infra_other_data_option_id;
                                    } else {
                                        field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : null);
                                    }
                                } else {
                                    field.value = (record.InfraOtherDataValue[0] ? record.InfraOtherDataValue[0].infra_other_data_value_value : null);
                                }

                                field.fieldLabel = record.infra_other_data_attribute_name;
                                field.name = record.infra_other_data_attribute_id;
                                field.hiddenName = record.infra_other_data_attribute_id;
                                aux.add(field);
                            }

                        }
                        App.Interface.Aux = aux;
                        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.add(aux);
                        Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.doLayout();
                    }
                }

                if (response.total > 0) {
                    Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.panel_datos.getTopToolbar().show();
                }
                if (Ext.getCmp('App.Plan.NuevaOpciones').tab_panel.activeTab.refName != 'panel_documentos') {
                    myMask.hide();
                }
            }
        });
    }
};
App.Interface.ViewPort = Ext.extend(Ext.Viewport, {
    layout: 'border',
    initComponent: function() {
        this.items = [

            {
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
                    enableTabScroll: true,
                    header: true,
                    headerAsText: true,

                }]
            },
            {
                xtype: 'toolbar',
                region: 'north',
                id: 'north',
                height: 27,
                border: false,
                items: App.General.TopMenu
            },
            {
                xtype: 'panel',
                region: 'west',
                id: 'App.ModulePanel',
                width: 240,
                style: 'padding: 5 0 5 5',
                //                closable: false,
                //                collapseMode: 'mini',
                layout: 'border',
                border: false,
                collapsible: true,
                split: true,
                //                header: false,

                //                    cls: 'x-panel-header',
                title: App.Language.Infrastructure.infrastructure,
                items: [{
                    xtype: 'panel',
                    region: 'center',
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
                        collapseFirst: false,

                    }, {
                        xtype: 'panel',
                        id: 'App.Panel.StructureTree.Search',
                        hidden: true,
                        layout: 'fit',
                        border: false
                    }]
                }]
            },
            {
                xtype: 'panel',
                region: 'east',
                width: (screen.width < 400) ? screen.width - 100 : 332,
                title: 'Datos',
                ref: 'panel_completo',
                id: 'App.Plan.NuevaOpciones',
                style: 'padding: 5 5 5 0',
                frame: false,
                layout: 'fit',
                border: true,
                collapsible: true,
                split: true,
                items: [{
                    xtype: 'tabpanel',
                    activeTab: 0,
                    ref: 'tab_panel',
                    border: false,
                    listeners: {
                        'afterrender': function(p) {

                            if (App.Security.Actions['5000'] != undefined) {
                                p.add({
                                    xtype: 'form',
                                    ref: 'panel_datos',
                                    padding: 5,
                                    plugins: [new Ext.ux.OOSubmit()],
                                    title: App.Language.Infrastructure.infrastructure,
                                    border: false,
                                    bodyStyle: 'overflowY: auto',
                                    tbar: {
                                        xtype: 'toolbar',

                                        hidden: true,
                                        listeners: {
                                            'beforeshow': function(p) {
                                                p.removeAll();
                                                p.add(App.ModuleActions[5004]);
                                            }
                                        }
                                    }
                                });
                            }

                            if ((App.Security.Actions['2006'] != undefined)) {
                                p.add({
                                    xtype: 'panel',
                                    border: false,
                                    loadMask: true,
                                    ref: 'panel_documentos',
                                    layout: 'border',
                                    title: App.Language.General.documents,

                                    tbar: {
                                        xtype: 'toolbar',
                                        autoScroll: 'auto',
                                        items: [{
                                            text: App.Language.General.add,
                                            hidden: (App.Security.Actions[2001] === undefined ? true : false),
                                            iconCls: 'add_icon',
                                            handler: function() {
                                                w = new App.Document.addDocumentWindow();
                                                w.show();
                                            }
                                        }, {
                                            xtype: 'tbseparator',
                                            width: 10
                                        }, {
                                            text: App.Language.General.bin,
                                            iconCls: 'bin_icon',
                                            handler: function(b) {

                                                grid = Ext.getCmp('App.Document.GridDoc2');
                                                if (grid.getSelectionModel().getCount()) {

                                                    records = Ext.getCmp('App.Document.GridDoc2').getSelectionModel().getSelections();
                                                    aux = new Array();
                                                    for (var i = 0; i < records.length; i++) {
                                                        aux.push(records[i].data.doc_document_id);
                                                    }
                                                    doc_document_id = (aux.join(','));
                                                    Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.this_insurance_send_to_the_trash_or_document, function(b) {
                                                        if (b == 'yes') {
                                                            Ext.Ajax.request({
                                                                waitMsg: App.Language.General.message_generating_file,
                                                                url: 'index.php/doc/document/enviarPapelera',
                                                                timeout: 10000000000,
                                                                params: {
                                                                    doc_document_id: doc_document_id
                                                                },
                                                                success: function(response) {
                                                                    response = Ext.decode(response.responseText);
                                                                    Ext.getCmp('App.Document.GridDoc2').fireEvent('beforerender', Ext.getCmp('App.Document.GridDoc2'));
                                                                    Ext.FlashMessage.alert(response.msg);
                                                                },
                                                                failure: function(response) {
                                                                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                                                                }
                                                            });
                                                        }
                                                    });
                                                } else {
                                                    Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                                                }
                                            }
                                        }, {
                                            xtype: 'tbseparator',
                                            width: 10
                                        }, {
                                            text: App.Language.General.search,
                                            iconCls: 'search_icon_16',
                                            enableToggle: true,
                                            handler: function(b) {
                                                if (b.ownerCt.ownerCt.formchico.isVisible()) {
                                                    b.ownerCt.ownerCt.formchico.hide();
                                                } else {
                                                    b.ownerCt.ownerCt.formchico.show();
                                                }
                                                b.ownerCt.ownerCt.doLayout();
                                            }
                                        }, '->', {
                                            //text: 'grilla',
                                            iconCls: 'list_icon',
                                            handler: function() {
                                                Ext.getCmp('App.Document.Principal2').Principal.removeAll();
                                                Ext.getCmp('App.Document.Principal2').Principal.add(new App.Document.GridView2());
                                                Ext.getCmp('App.Document.Principal2').Principal.doLayout();
                                            }
                                        }, {
                                            //  text: 'thumb',
                                            iconCls: 'miniature_icon',
                                            handler: function() {
                                                Ext.getCmp('App.Document.Principal2').Principal.removeAll();
                                                Ext.getCmp('App.Document.Principal2').Principal.add(new App.Document.ThumbView());
                                                Ext.getCmp('App.Document.Principal2').Principal.doLayout();
                                            }
                                        }]
                                    },
                                    items: [{
                                            xtype: 'form',
                                            region: 'north',
                                            frame: true,
                                            ref: 'formchico',
                                            hidden: true,
                                            height: 120,
                                            margins: '5 0 0 0',
                                            border: false,
                                            fbar: [{
                                                text: App.Language.General.search,
                                                handler: function(b) {
                                                    formchico = b.ownerCt.ownerCt.getForm();
                                                    node_id = App.Interface.selectedNodeId;
                                                    App.Document.Store.baseParams = formchico.getSubmitValues();
                                                    App.Document.Store.setBaseParam('node_id', node_id);
                                                    App.Document.Store.load();
                                                }
                                            }, {
                                                text: App.Language.General.clean,
                                                handler: function(b) {
                                                    formchico = b.ownerCt.ownerCt.getForm();
                                                    node_id = App.Interface.selectedNodeId;
                                                    formchico.reset();
                                                    App.Document.Store.baseParams = {};
                                                    App.Document.Store.setBaseParam('node_id', node_id);
                                                    App.Document.Store.load();
                                                }
                                            }],
                                            items: [{
                                                layout: 'column',
                                                id: 'column_form_column_start_date',
                                                items: [{
                                                    columnWidth: .99,
                                                    layout: 'form',
                                                    items: [{
                                                            xtype: 'textfield',
                                                            fieldLabel: App.Language.General.document,
                                                            anchor: '95%',
                                                            name: 'doc_document_filename'
                                                        }

                                                        , {
                                                            xtype: 'checkbox',
                                                            hideLabel: true,
                                                            boxLabel: App.Language.General.perform_internal_search,
                                                            name: 'search_branch',
                                                            inputValue: 1
                                                        }
                                                    ]
                                                }]
                                            }]
                                        },
                                        new App.Document.PrincipalClase2()

                                    ]
                                });


                                if (App.Interface.selectedNodeId == 'root') {

                                    p.panel_documentos.getTopToolbar().hide();

                                }
                            }

                            p.setActiveTab(0);
                        },

                        'tabchange': function(tp, p) {
                            App.Interface.refresTabs();
                        }
                    },
                }]

            }
        ];
        App.Interface.ViewPort.superclass.initComponent.call(this);
    }

});


App.Document.PrincipalClase2 = Ext.extend(Ext.Panel, {
    id: 'App.Document.Principal2',
    border: false,
    loadMask: true,
    layout: 'border',
    region: 'center',
    initComponent: function() {
        this.items = [{
                xtype: 'panel',
                ref: 'Principal',
                region: 'center',
                layout: 'fit',
                border: false,
                margins: '5 5 5 5',
                items: new App.Document.GridView2()
                    //ESTO LLAMA A JAVASCRIPT/DOC/INTERFACE.JS
            }],
            App.Document.PrincipalClase2.superclass.initComponent.call(this);
    }
});

App.Interface.ViewPort.displayModuleGui = function(node) {

    if (App.Interface.activeModule == null) {

        return;
    }
    Ext.getCmp('App.PrincipalPanel').removeAll();
    Ext.getCmp('App.PrincipalPanel').add(eval("new App." + App.Interface.activeModule + ".Principal()"));
    Ext.getCmp('App.PrincipalPanel').doLayout();
    //AQUI ABRE SI TIENE UNA RUTA DE INICIO
    if (App.Security.Session.user_path != 'root') {
        App.InfraStructure.expandDeepNode(App.Security.Session.user_path);
    }

    if (App.Interface.activeModule == 'Plan' && screen.width > 700 && ((App.Security.Actions['2006'] != undefined) || (App.Security.Actions['5000'] != undefined))) {

        Ext.getCmp('App.Plan.NuevaOpciones').collapsed ? Ext.getCmp('App.Plan.NuevaOpciones').toggleCollapse(true) : '';
    } else {
        !Ext.getCmp('App.Plan.NuevaOpciones').collapsed ? Ext.getCmp('App.Plan.NuevaOpciones').toggleCollapse(false) : '';
    }

};
App.Interface.panel = {
    //    xtype: 'button',
    iconCls: 'people_icon_32',
    text: 'Panel de Administración',

    //    scale: 'large',
    //    iconAlign: 'top',
    module: 'panelAdmin',
    hidden: (App.Security.Session.user_type == 'N' || App.Security.Session.user_type == 'P' ? true : false)
}
App.Interface.addToModuleMenu('paneladmin', App.Interface.panel);

Ext.onReady(function() {
    Ext.QuickTips.init();
    var scrollMenu = new Ext.menu.Menu();
    //    for (var i = 0; i < 50; ++i) {
    scrollMenu.add({
        text: 'Radio Options',
        menu: { // <-- submenu by nested config object
            items: App.Interface.getUserModuleMenu()
        }
    });
    //    }
    // scrollable menu
    //    tb.add({
    //        icon: 'preview.png',
    //        cls: 'x-btn-text-icon',
    //        text: 'Scrolling Menu',
    //        menu: scrollMenu
    //    });





    App.General.TopMenu.splice(1, 0, {
        text: 'Menú',
        iconCls: 'list_icon',
        ref: '../btn_menu',
        id: 'btn_menu',
        menu: { // <-- submenu by nested config object
            xtype: 'menu',
            plain: true,
            cls: 'menu_class',
            items: App.Interface.getUserModuleMenu(),
            defaults: {
                handler: function(btn, state) {
                    if (state == false) {
                        return;
                    }

                    if (btn.module != 'panelAdmin') {
                        Ext.getCmp('App.PrincipalPanel').setTitle(btn.text);
                        App.Interface.title = btn.text;

                        Ext.getCmp('App.PrincipalPanel').removeAll();
                        App.Interface.activeModule = btn.module;
                        //if (App.Interface.selectedNodeId != 'root') {
                        Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId));
                        //}

                        // tree toolbar search
                        Ext.getCmp('App.StructureTree.ToolBarSearch').removeAll();
                        Ext.getCmp('App.StructureTree.ToolBarSearch').hide();
                        if (eval("App." + App.Interface.activeModule + ".treeSearchToolBar")) {
                            Ext.getCmp('App.StructureTree.ToolBarSearch').add(eval("App." + App.Interface.activeModule + ".treeSearchToolBar"));
                            Ext.getCmp('App.StructureTree.ToolBarSearch').show();
                            Ext.getCmp('App.StructureTree.ToolBarSearch').doLayout();
                        }

                    } else {
                        document.location = App.BaseUrl + 'index.php/administrator';
                    }

                }
            }
        }



    });


    vp = new App.Interface.ViewPort();
    // tree loading mask

    Ext.getCmp('App.Plan.NuevaOpciones').toggleCollapse(false);
    if (App.ModuleSelect.length) {

        Ext.getCmp('App.PrincipalPanel').removeAll();
        App.Interface.activeModule = App.ModuleSelect;
        Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(App.Interface.selectedNodeId));

        // tree toolbar search
        Ext.getCmp('App.StructureTree.ToolBarSearch').removeAll();
        Ext.getCmp('App.StructureTree.ToolBarSearch').hide();
        if (eval("App." + App.Interface.activeModule + ".treeSearchToolBar")) {
            Ext.getCmp('App.StructureTree.ToolBarSearch').add(eval("App." + App.Interface.activeModule + ".treeSearchToolBar"));
            Ext.getCmp('App.StructureTree.ToolBarSearch').show();
            Ext.getCmp('App.StructureTree.ToolBarSearch').doLayout();
        }
        Ext.getCmp('App.PrincipalPanel').setTitle(App.Module);
        App.Interface.title = App.Module;

        Ext.Ajax.request({
            url: 'index.php/gui/administrator/getModule',
            params: {
                module: '',
                moduleName: ''
            },
            success: function(response) {
                App.ModuleSelect = '';
                App.Module = '';

            }
        });


    }
    App.StructureTree.treeLoadMask = new Ext.LoadMask(Ext.getCmp('App.StructureTree.Tree').getEl(), {
        msg: App.Language.General.message_loading_information,
        store: Ext.getCmp('App.StructureTree.Tree').getLoader()
    });
    Ext.getCmp('App.StructureTree.Tree').on('movenode', function(tree, node, oldParent, newParent) {
        App.Node.MoveProxy(node.id, newParent.id);
    });
    new Ext.tree.TreeSorter(Ext.getCmp('App.StructureTree.Tree'), {
        folderSort: true,
        dir: "ASC"
    });

    jQuery('.x-tree-ec-icon.x-tree-elbow-end-plus').trigger('click');
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
    App.StructureTree.Tree.refreshPathBar = function(node) {
        if (node && node.id) {
            fbar = Ext.getCmp('App.PrincipalPanel').getBottomToolbar();
            fbar.removeAll();
            aux = new Array();
            do {
                aux.push(node);
                node = node.parentNode;
            } while (node != null);
            for (var i = aux.length - 1; i >= 0; i--) {
                if (aux[i].id == 'root') {
                    continue;
                }
                fbar.add({
                    xtype: 'button',
                    listeners: {
                        'mouseout': function(b) {
                            b.addClass('x-btn-over');
                        }
                    },
                    cls: 'x-btn-over',
                    text: aux[i].text,
                    node_id: aux[i].id,
                    handler: function(node) {
                        treeNode = Ext.getCmp('App.StructureTree.Tree').getNodeById(node.node_id);
                        Ext.getCmp('App.StructureTree.Tree').getSelectionModel().select(treeNode);
                        App.StructureTree.Tree.refreshPathBar(treeNode);
                    }
                });
                fbar.add({
                    xtype: 'spacer',
                    width: 5
                });
            }
            fbar.doLayout();
        }
    }

    Ext.getCmp('App.StructureTree.Tree').on('click', function(node) {

        App.Interface.selectedNodeId = node.id;
        App.Interface.selectedNode = node;
        App.StructureTree.Tree.refreshPathBar(node);


        Ext.Ajax.request({

            url: 'index.php/core/nodecontroller/groupasset',

            params: {
                node: App.Interface.selectedNodeId,
                module: App.Interface.activeModule
            },
            success: function(response) {
                response = Ext.decode(response.responseText);
                App.Interface.permits = response.permits;


                if (App.Interface.permits) {

                    jQuery('.permits').css('display', 'block');
                } else {

                    jQuery('.permits').css('display', 'none');
                }

            },
            failure: function(response) {
                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
            }
        });

        Ext.Ajax.request({

            url: 'index.php/core/nodecontroller/expand',

            params: {
                node: 'root'
            },
            success: function(response) {
                response = Ext.decode(response.responseText);
                App.Interface.nodeRoot = response[0]['id'];
                App.Interface.flat = (App.Interface.nodeRoot == App.Interface.selectedNodeId) ? true : false;


            },
            failure: function(response) {
                Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
            }
        });

        App.Interface.refresTabs();


    });
    Ext.getCmp('App.StructureTree.Tree').on('iconclick', function(node) {


        App.Interface.selectedNodeId = node.id;
        App.Interface.selectedNode = node;
        App.StructureTree.Tree.refreshPathBar(node);

        App.Interface.refresTabs();

    });
    Ext.getCmp('App.StructureTree.Tree').on('click', function(node) {

        App.Security.checkNodeAccess(node);
    });
    vp.render();
    App.Security.loadActions();
    if (App.Security.Session.user_default_module && Ext.getCmp('App.ModulePanel.Button' + App.Security.Session.user_default_module)) {
        Ext.getCmp('App.ModulePanel.Button' + App.Security.Session.user_default_module).toggle(true);
    }


});