Ext.namespace('App.Interface.*');
Ext.namespace('App.Document.*');
Ext.namespace('App.Language.*');

App.Document.selectedDocumentId = null;
App.Document.CategoryName = null;
App.Document.currentPosition = null;
App.Interface.activeModule = null;
App.Interface.selectedNodeId = 'root';
App.Interface.ModuleMenu = new Array();

App.Interface.addToModuleMenu = function(ns, button) {}

App.Interface.getUserModuleMenu = function() {
    var aux = new Array();
    for (i in App.UserModules) {
        aux[i] = App.Interface.ModuleMenu[App.UserModules[i]];
    }
    return aux;
}

App.Interface.ViewPort = Ext.extend(Ext.Viewport, {
    id: 'App.Principal',
    layout: 'border',
    initComponent: function() {
        this.items = [
            new App.Plan.Principal({
                region: 'center',
                style: 'padding: 5 0 5 0',
                border: true,
                bbar: {
                    autoScroll: true
                }
            }),
            {
                xtype: 'toolbar',
                region: 'north',
                id: 'north',
                height: 27,
                border: false,
                items: App.General.TopMenu
            }, {
                xtype: 'panel',
                region: 'west',
                id: 'App.ModulePanel',
                width: 250,
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
                        tbar: App.InfraStructure.treeSearchToolBar
                    }]
                }]
            },

            {
                xtype: 'panel',
                region: 'east',
                width: 370,
                ref: 'panel_completo',
                id: 'App.Plan.NuevaOpciones',
                style: 'padding: 5 5 5 0',
                closable: false,
                collapseMode: 'mini',
                frame: false,
                layout: 'fit',
                border: true,
                collapsible: false,
                split: true,
                header: false,
                items:
                //PANEL
                    [{
                    xtype: 'tabpanel',
                    activeTab: 0,
                    ref: 'tab_panel',
                    border: false,
                    listeners: {
                        'afterrender': function(p) {
                            if (App.Interface.selectedNodeId == 'root') {
                                p.panel_documentos.getTopToolbar().hide();
                                //                                p.panel_activos.getTopToolbar().hide();
                            }
                        }
                    },
                    items: [{
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
                        },
                        {
                            xtype: 'panel',
                            border: false,
                            loadMask: true,
                            ref: 'panel_documentos',
                            layout: 'border',
                            title: App.Language.General.documents,
                            border: false,
                            tbar: [{
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
                            }],
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
                                                //                                        ,  {
                                                //                                            xtype: 'combo',
                                                //                                            fieldLabel: App.Language.General.category,
                                                //                                            anchor: '95%',
                                                //                                            store: App.Document.Categoria.Store,
                                                //                                            hiddenName: 'doc_category_id',
                                                //                                            triggerAction: 'all',
                                                //                                            displayField: 'doc_category_name',
                                                //                                            valueField: 'doc_category_id',
                                                //                                            editable: true,
                                                //                                            typeAhead: true,
                                                //                                            selectOnFocus: true,
                                                //                                            forceSelection: true,
                                                //                                            mode: 'remote',
                                                //                                            minChars: 0
                                                //                                        }
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
                                //                                    {
                                //                                xtype: 'grid',
                                //                                margins: '5 0 0 0',
                                //                                plugins: [new Ext.ux.OOSubmit()],
                                //                                region: 'center',
                                //                                border: true,
                                //                                loadMask: true,
                                //                                listeners:
                                //                                {
                                //                                    'beforerender': function(w)
                                //                                    {
                                //                                        App.Document.Store.load();
                                //                                    }
                                //                                },
                                //                                viewConfig:
                                //                                {
                                //                                    forceFit: true
                                //                                },
                                //                                store: App.Document.Store,
                                //                                columns: 
                                //                                [{
                                //                                    header: App.Language.General.file_name,
                                //                                    sortable: true,
                                //                                    dataIndex: 'doc_document_filename',
                                //                                    renderer: function(val, metadata, record)
                                //                                    {
                                //                                        return "<a href='index.php/doc/document/download/" + record.data.doc_current_version_id + "'>" + val + "</a>";
                                //                                    }
                                //                                }, {
                                //                                    header: App.Language.General.category,
                                //                                    sortable: true,
                                //                                    dataIndex: 'doc_category_name',
                                //                                    width: 50
                                //                                }]
                                //                            }

                            ]
                        }
                    ]
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
            items: new App.Document.GridView2() //ESTO LLAMA A JAVASCRIPT/DOC/INTERFACE.JS
        }], App.Document.PrincipalClase.superclass.initComponent.call(this);
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

    if ((typeof App.Plan != 'object') || (typeof App.Document != 'object') || (typeof App.InfraStructure != 'object')) {
        Ext.MessageBox.alert('Permisos no suficientes', "Para utilizar esta vista, usted debe tener acceso a los m&oacute;dulos de Infraestructura, Documentos y Planimetria", function(b) {
            document.location = App.BaseUrl + 'index.php/core/user/setDefaultView';
        });
        return;
    }

    vp = new App.Interface.ViewPort();

    //AQUI ABRE SI TIENE UNA RUTA DE INICIO
    if (App.Security.Session.user_path != 'root') {
        App.InfraStructure.expandDeepNode(App.Security.Session.user_path);
    }
    // tree loading mask
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
            fbar = Ext.getCmp('App.Plan.Principal').getBottomToolbar();
            fbar.removeAll();
            aux = new Array();
            do {
                aux.push(node);
                node = node.parentNode;
            } while (node != null);
            for (var i = aux.length - 1; i >= 0; i--) {
                if (aux[i].id == 'root')
                    continue;
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

        App.Plan.getPlan(node);

        App.InfraStructure.Info.Store.setBaseParam('node_id', App.Interface.selectedNodeId);

        App.Document.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        App.Document.Store.load();

        //        App.Asset.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        //        App.Asset.Store.load();

        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.removeAll();
        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.doLayout();
        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.getTopToolbar().hide();
        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_documentos.getTopToolbar().show();
        //        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_activos.getTopToolbar().show();

        Ext.Ajax.request({
            url: 'index.php/infra/infrainfo/get',
            params: {
                node_id: App.Interface.selectedNodeId
            },
            success: function(response) {

                response = Ext.decode(response.responseText);

                aux = new Ext.form.FieldSet({
                    title: App.Language.Infrastructure.structural_data,
                    layout: 'form',
                    collapsible: true,
                    labelWidth: 150
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

                if (response.resultsInfraInfo.length) {
                    Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.add(aux);
                    Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.doLayout();
                }

                for (i in response.resultsInfraOtherData) {

                    if (typeof response.resultsInfraOtherData[i] === 'object') {
                        aux = new Ext.form.FieldSet({
                            title: response.resultsInfraOtherData[i].infra_grupo_nombre,
                            layout: 'form',
                            collapsible: true,
                            labelWidth: 150
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
                        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.add(aux);
                        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.doLayout();
                    }
                }

                if (response.total > 0) {
                    Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.getTopToolbar().show();
                }

            }
        });

    });


    Ext.getCmp('App.StructureTree.Tree').on('click', function(node) {
        App.Security.checkNodeAccess(node);
    });

    Ext.getCmp('App.StructureTree.Tree').on('iconclick', App.Plan.viewNodeLink);

    Ext.getCmp('App.StructureTree.Tree').on('iconclick', function(node) {


        App.Interface.selectedNodeId = node.id;
        App.Interface.selectedNode = node;
        App.StructureTree.Tree.refreshPathBar(node);

        App.InfraStructure.Info.Store.setBaseParam('node_id', App.Interface.selectedNodeId);

        App.Document.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        App.Document.Store.load();

        //        App.Asset.Store.setBaseParam('node_id', App.Interface.selectedNodeId);
        //        App.Asset.Store.load();

        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.removeAll();
        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.doLayout();
        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.getTopToolbar().hide();
        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_documentos.getTopToolbar().show();
        //        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_activos.getTopToolbar().show();

        Ext.Ajax.request({
            url: 'index.php/infra/infrainfo/get',
            params: {
                node_id: App.Interface.selectedNodeId
            },
            success: function(response) {

                response = Ext.decode(response.responseText);

                aux = new Ext.form.FieldSet({
                    title: App.Language.Infrastructure.structural_data,
                    layout: 'form',
                    collapsible: true,
                    labelWidth: 150
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

                if (response.resultsInfraInfo.length) {
                    Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.add(aux);
                    Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.doLayout();
                }

                for (i in response.resultsInfraOtherData) {

                    if (typeof response.resultsInfraOtherData[i] === 'object') {
                        aux = new Ext.form.FieldSet({
                            title: response.resultsInfraOtherData[i].infra_grupo_nombre,
                            layout: 'form',
                            collapsible: true,
                            labelWidth: 150
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
                        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.add(aux);
                        Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.doLayout();
                    }
                }

                if (response.total > 0) {
                    Ext.getCmp('App.Principal').panel_completo.tab_panel.panel_datos.getTopToolbar().show();
                }

            }
        });

    });


    vp.render();

    App.Security.loadActions();

    if (App.Security.Session.user_default_module && Ext.getCmp('App.ModulePanel.Button' + App.Security.Session.user_default_module)) {
        Ext.getCmp('App.ModulePanel.Button' + App.Security.Session.user_default_module).toggle(true);
    }


});