App.Plan.SelectedCategoryId = null;
App.Plan.SelectedNodeId = null;
App.Plan.SelectedLinkNodeId = null;
App.Plan.CurrentPlanId = null;
App.Plan.PlanVersion = null;
App.Plan.PlanDateTime = null;
App.Plan.DefaultTabPanel = 0;
App.Plan.Handler = null;
App.Plan.lastobj = 0;
App.Plan.EnableZoomToObject = false;
App.Plan.forcePlanTabLoad = false;
App.Plan.allowRootGui = true;
//App.Plan.location = null;
App.Plan.NodeId = 0;
NodeIds = 0;
App.Plan.idPlan = [];
App.Plan.HandlerZoom = '';
App.Plan.NodeZoom = '';
App.Plan.colorSection = '';
App.Plan.NodePlan = 0;
App.Plan.tabId = 0;
App.Plan.OriginalNodeSection = '';
App.Plan.TextSelect = '';


App.Plan.ViewConfig = {
    Color: 'FF0000',
    Width: 5,
    ZoomLoc: true,
    EnableSelect: false
};

App.Plan.treeSearchToolBar = [{
        iconCls: 'zoomloc_icon',
        tooltip: App.Language.Plan.showing_approximate_relation_node_level,
        enableToggle: true,
        listeners: {
            'render': function (b) {
                b.toggle(App.Plan.ViewConfig.ZoomLoc);
            }
        },
        toggleHandler: function (b, state) {
            App.Plan.ViewConfig.ZoomLoc = state;
        }
    }];

App.Interface.addToModuleMenu('plan', {
    iconCls: 'plane_icon_32',
//    xtype: 'button',
    text: App.Language.Plan.planimetry,
    iconCls: 'plane_icon_32',
//    scale: 'large',
//    iconAlign: 'top',
    module: 'Plan',
    listeners: {
        'toggle': function () {
            Ext.getCmp('App.StructureTree.Tree').on('iconclick', App.Plan.viewNodeLink);
        }
    }
});

App.Plan.AllVersionsGrid = Ext.extend(Ext.Panel, {
    title: App.Language.Plan.plans,
    layout: 'border',
    border: false,
    loadMask: true,
    tbar: [App.ModuleActions[3001], {
            xtype: 'tbseparator',
            width: 10
        },
//        App.ModuleActions[3005], {
//            xtype: 'tbseparator',
//            width: 10
//        },        
        App.ModuleActions[3004], {
            xtype: 'tbseparator',
            width: 10
        }, {
            text: App.Language.General.search,
            iconCls: 'search_icon_16',
            enableToggle: true,
            bodyStyle: 'padding:5px 5px 0',
            handler: function (b) {
                if (b.ownerCt.ownerCt.form.isVisible()) {
                    b.ownerCt.ownerCt.form.hide();
                } else {
                    b.ownerCt.ownerCt.form.show();
                }
                b.ownerCt.ownerCt.doLayout();
            }
        }],
    initComponent: function () {
        this.items = [{
                xtype: 'grid',
                margins: '5 5 5 5',
                id: 'App.Plan.AllVersionsGridAll',
                region: 'center',
                height: 600,
                border: true,
                loadMask: true,
                listeners: {
                    'beforerender': function (grid) {
                        grid.getStore().load();
                    },
                    'rowdblclick': function (grid, rowIndex) {
                        w = new App.Plan.Version.Window();
                        w.plan_category_id = grid.getStore().getAt(rowIndex).data.plan_category_id;
                        w.node_id = App.Interface.selectedNodeId;
                        w.show();
                    }
                },
                viewConfig: {
                    forceFit: true
                },
                store: App.Plan.Store.AllVersions,
                columns: [{
                        xtype: 'gridcolumn',
                        header: App.Language.General.description,
                        dataIndex: 'plan_description',
                        sortable: true,
                        align: 'left'
                    }, {
                        xtype: 'gridcolumn',
                        header: App.Language.General.category,
                        dataIndex: 'PlanCategory',
                        renderer: function (PlanCategory) {
                            return PlanCategory.plan_category_name;
                        },
                        sortable: true
                    }, {
                        xtype: 'gridcolumn',
                        header: App.Language.General.current_version,
                        dataIndex: 'plan_version',
                        sortable: true
                    }, {
                        xtype: 'datecolumn',
                        header: App.Language.General.creation_date,
                        dataIndex: 'plan_datetime',
                        sortable: true,
                        format: App.General.DefaultDateTimeFormat,
                        align: 'center'
                    }, {
                        xtype: 'gridcolumn',
                        header: App.Language.General.uploaded_by,
                        dataIndex: 'User',
                        renderer: function (User) {
                            return User.user_name;
                        },
                        sortable: true
                    }]
            }, {
                xtype: 'form',
                region: 'north',
                ref: 'form',
                id: 'App.Plan.SearchForm',
                plugins: [new Ext.ux.OOSubmit()],
                title: App.Language.General.searching,
                frame: true,
                hidden: true,
                cls: 'formCls',
                height: 'auto',
                margins: '5 5 0 5',
                autoScroll: true,
                padding: '5 5 5 5',
                border: true,
                fbar: [{
                        text: App.Language.General.search,
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            App.Plan.Store.AllVersions.baseParams = form.getSubmitValues();
                            App.Plan.Store.AllVersions.setBaseParam('node_id', null);
                            App.Plan.Store.AllVersions.load();
                        }
                    }, {
                        text: App.Language.General.clean,
                        handler: function (b) {
                            form = b.ownerCt.ownerCt.getForm();
                            form.reset();
                            Ext.getCmp('Start_Date').update();
                            App.Plan.Store.AllVersions.setBaseParam([]);
                            App.Plan.Store.AllVersions.setBaseParam('node_id', App.Interface.selectedNodeId);
                            App.Plan.Store.AllVersions.load();
                        }
                    }],
                items:
                        [{
                                layout: 'column',

                                items:
                                        [{
                                                columnWidth: .5,
                                                layout: 'form',
                                                items: [{
                                                        xtype: 'textfield',
                                                        fieldLabel: App.Language.General.description,
                                                        anchor: '80%',
                                                        name: 'plan_description',
                                                        checked: true
                                                    }, {
                                                        xtype: 'spacer',
                                                        height: 5
                                                    }, {
                                                        xtype: 'combo',
                                                        fieldLabel: App.Language.General.category,
                                                        anchor: '80%',
                                                        store: App.Plan.Category.Store,
                                                        hiddenName: 'plan_category_id',
                                                        triggerAction: 'all',
                                                        displayField: 'plan_category_name',
                                                        valueField: 'plan_category_id',
                                                        editable: false,
                                                        mode: 'remote',
                                                        minChars: 0,
                                                        listeners: {
                                                            'afterrender': function (cb) {
                                                                cb.__value = cb.value;
                                                                cb.setValue('');
                                                                cb.getStore().load({
                                                                    callback: function () {
                                                                        if (cb.store) {
                                                                            cb.setValue(cb.__value);
                                                                        }
                                                                    }
                                                                });
                                                            },
                                                            'beforedestroy': function (cb) {
                                                                cb.purgeListeners();
                                                            }
                                                        }
                                                    }, {
                                                        xtype: 'checkbox',
                                                        name: 'plan_current_version',
                                                        fieldLabel: App.Language.General.message_show_all_versions,
                                                        checked: false,
                                                        inputValue: 1
                                                    }]
                                            }, {
                                                columnWidth: .5,
                                                layout: 'form',
                                                items: [{
                                                        columnWidth: .2,
                                                        layout: 'form',
                                                        items: [{
                                                                xtype: 'label',
                                                                text: App.Language.General.select_date_range_plane
                                                            }]
                                                    }, {
                                                        columnWidth: .4,
                                                        layout: 'column',
                                                        frame: true,
                                                        items: [{
                                                                columnWidth: .5,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'datefield',
                                                                        ref: '../start_date',
                                                                        id: 'Start_Date',
                                                                        fieldLabel: App.Language.General.start_date,
                                                                        name: 'start_date',
                                                                        anchor: '95%',
                                                                        listeners: {
                                                                            'select': function (fd, date) {
                                                                                fd.ownerCt.ownerCt.end_date.setMinValue(date);
                                                                            }
                                                                        }
                                                                    }]
                                                            }, {
                                                                columnWidth: .5,
                                                                layout: 'form',
                                                                items: [{
                                                                        xtype: 'datefield',
                                                                        ref: '../end_date',
                                                                        fieldLabel: App.Language.General.end_date,
                                                                        name: 'end_date',
                                                                        anchor: '95%',
                                                                        listeners: {
                                                                            'select': function (fd, date) {
                                                                                fd.ownerCt.ownerCt.start_date.setMaxValue(date);
                                                                            }
                                                                        }
                                                                    }]
                                                            }]
                                                    }, {
                                                        columnWidth: .4,
                                                        layout: 'form',
                                                        items: [{
                                                                xtype: 'spacer',
                                                                height: 15
                                                            }, {
                                                                xtype: 'combo',
                                                                fieldLabel: App.Language.General.uploaded_by,
                                                                hiddenName: 'user_id',
                                                                store: App.Core.User.Store,
                                                                displayField: 'user_name',
                                                                triggerAction: 'all',
                                                                valueField: 'user_id',
                                                                mode: 'remote',
                                                                minChars: 0,
                                                                anchor: '100%'
                                                            }]
                                                    }]
                                            }]
                            }]
            }], App.Plan.AllVersionsGrid.superclass.initComponent.call(this);
    }
});

App.Plan.addPlanWindow = Ext.extend(Ext.Window, {
    title: App.Language.Plan.add_plan_title,
    resizable: false,
    modal: true,
    width: (screen.width < 380) ? screen.width - 50 : 380,
    height: 340,
    layout: 'fit',
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                fileUpload: true,
                padding: 5,
                items: [{
                        xtype: 'textfield',
                        ref: 'plan_description',
                        fieldLabel: App.Language.General.description,
                        name: 'plan_description',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'fileuploadfield',
                        emptyText: 'Seleccione un Plano',
                        fieldLabel: App.Language.Plan.plan,
                        ref: 'archivo',
                        anchor: '100%',
                        name: 'documento',
                        buttonText: '',
                        buttonCfg: {
                            iconCls: 'upload_icon'
                        },
                        allowBlank: false
                    }, {
                        xtype: 'displayfield',
                        hideLabel: true,
                        hidden: true,
                        ref: 'plan_last_version',
                        fieldLabel: App.Language.Plan.last_version,
                        anchor: '100%'
                    }, {
                        xtype: 'textfield',
                        fieldLabel: App.Language.General.version,
                        name: 'plan_version',
                        anchor: '100%',
                        allowBlank: false
                    }, {
                        xtype: 'combo',
                        ref: 'categoria',
                        fieldLabel: App.Language.General.category,
                        anchor: '100%',
                        store: App.Plan.Category.Store,
                        hiddenName: 'plan_category_id',
                        displayField: 'plan_category_name',
                        valueField: 'plan_category_id',
                        editable: true,
                        triggerAction: 'all',
                        typeAhead: true,
                        selectOnFocus: true,
                        forceSelection: true,
                        mode: 'remote',
                        minChars: 0,
                        allowBlank: false
                    }, {
                        xtype: 'textarea',
                        fieldLabel: App.Language.General.comment,
                        name: 'plan_comments',
                        anchor: '100%',
                        height: 100
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.hide();
                        }
                    }, {
                        text: App.Language.General.add,
                        ref: '../saveButton'
                    }]
            }];
        App.Plan.addPlanWindow.superclass.initComponent.call(this);
    }
});

App.Plan.Tab = Ext.extend(Ext.Panel, {
    xtype: 'panel',
    id: App.Plan.tabId,
    listeners: {
        'render': function (panel) {
            panel.__value = panel.version;
            panel.combo_version.setValue('');
            panel.combo_version.getStore().setBaseParam('node_id', panel.node_id);
            panel.combo_version.getStore().setBaseParam('plan_category_id', panel.plan_category_id);
            panel.combo_version.getStore().load({
                callback: function (r) {
                    if (panel.combo_version_2) {
                        aux = [];
                        var cont = 0;
                        class_version = '';
                        panel.combo_version.getStore().each(function (record) {

                            if (cont == 0) {

                                class_version = record.data.plan_id;
                                cont++;
                            }
                            aux.push('<option value="' + record.data.plan_id + '">' + record.data.plan_version + '</option>');
                        });

                        var t = new Ext.XTemplate('<select  class="combo_version ' + class_version + '" onchange="this.setAttribute(\'val-select\', this.value); App.Plan.loadVersionPlan(this.value,' + panel.plan_category_id + ');">' +
                                aux.join('') +
                                '</select>');
                        t.overwrite(panel.combo_version_2.el);
                    }
                }
            });
            if (App.Plan.HandlerZoom.length) {
                panel.version_label.hide(true);
                panel.combo_version_2.hide(true);
                panel.select_tool.hide(true);
                panel.save_select_tool.hide(true);
            }
            if (panel.combo_section) {
                App.Plan.loadSectionCombo(panel.plan_id, panel.plan_category_id);
            }
        },
        'beforedestroy': function (panel) {
            panel.purgeListeners();
            lastobj = null;
        }
    },
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        height: 45,
        items: [App.ModuleActions[3002], {
                xtype: 'tbspacer',
                width: 5
            }, {
                xtype: 'tbseparator',
                width: 10
            }, {
                text: App.Language.General.version,
                xtype: 'label',
                ref: '../version_label',
                hidden: false

            }, {
                xtype: 'tbspacer',
                width: 5
            }, {
                xtype: 'combo',
                ref: '../combo_version',
                hidden: true,
                store: {
                    xtype: 'App.Plan.Version.Store'
                },
                displayField: 'plan_version',
                valueField: 'plan_id',
                width: 100,
                triggerAction: 'all',
                editable: false,
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'box',
                ref: '../combo_version_2'
            }, {
                xtype: 'tbspacer',
                width: 5
            }, {
                text: App.Language.General.details,
                iconCls: 'information_icon',
                handler: function (b) {

                    cb = b.ownerCt.ownerCt.combo_version;
                    record = cb.getStore().getById(App.Plan.CurrentPlanId);
                    w = new App.Plan.DetailPlanWindow();
                    w.plan_id = record.data.plan_id;
                    w.form.getForm().loadRecord(record);
                    w.show();
                    w.toFront(true);
                }
            }, {
                xtype: 'tbspacer',
                width: 5
            }, {
                text: App.Language.General.printer,
                iconCls: 'print_icon',
                handler: function (b) {
//                panel = b.ownerCt.ownerCt;
//                document.location = 'index.php/plan/plan/printPDF/' + panel.plan_id;

                    var msg = Ext.MessageBox.wait("", "Generando PDF...");
                    panel = b.ownerCt.ownerCt;

                    var inf = jQuery('.' + panel.plan_id).attr('val-select');
                    var id = (inf) ? inf : panel.plan_id;

                    Ext.Ajax.request({
                        url: 'index.php/plan/plan/printPDF',
                        params: {
                            plan_id: id
                        },

                        success: function (response) {
                            setTimeout(function () {
                                response = Ext.decode(response.responseText);
////                            document.location = 'index.php/app/download/' + response.file;
//
///////////////////////////////////////////////////nuevo codigo //////////////////////////////////////////

                                panZoom.resetZoom();
//                                panZoom.center();
                                panZoom.updateBBox();
                                panZoom.fit();



                                var embElement = document.getElementById('plan_embed_' + id);
                                var svgElement = embElement.getSVGDocument().documentElement;

                                var svgSize = svgElement.childNodes[0];

                                if ('transform' in svgSize) {
                                    var transformSVG = svgSize.getAttribute("transform");
                                    var styleSvg = svgSize.getAttribute("style");
                                    svgSize.setAttribute("transform", "matrix(0.875,0,0,0.875,152,0)");
                                    svgSize.setAttribute("style", "transform: matrix(0.875, 0, 0, 0.875, 152, 0);");

//                            var pp= svgElement.childNodes[0];
//                            pp.replaceWith(pp.childNodes); 


                                    let compress = false,
                                            pagewidth = parseFloat(660),
                                            pageheight = parseFloat(422),
                                            showViewport = false,
                                            x = -60.5,
                                            y = 15;
                                    let options = {
                                        'page-size': 'A4',
                                        'margin-top': '0.75in',
                                        'margin-right': '0.75in',
                                        'margin-bottom': '0.75in',
                                        'margin-left': '0.75in',
                                        useCSS: false,
                                        assumePt: false,
                                        preserveAspectRatio: '',
                                        width: '',
                                        height: ''
                                    };
                                    let doc = new PDFDocument({compress: compress, size: [pagewidth || 612, pageheight || 792]});


//                             doc.text(name_building,10,390);
                                    if (showViewport) {
                                        doc.rect(x || 0, y || 0, options.width || doc.page.width, options.height || doc.page.height)
                                                .lineWidth(8).dash([8, 4]).strokeColor('green').stroke();
                                    }
                                    if (options.useCSS) {
                                        let hiddenDiv = document.getElementById('hidden-div');
                                        hiddenDiv.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' + svgElement.trim() + '</svg>';
                                        SVGtoPDF(doc, hiddenDiv.firstChild.firstChild, x, y, options);
                                    } else {
                                        SVGtoPDF(doc, svgElement, x, y, options);
                                    }


//                                    var tags = ['Nombre Edificio:', 'Direcci\u00F3n:', 'Regi\u00F3n:', 'Ciudad:', 'Nivel:', 'Fecha de Actualizaci\u00F3n:', 'Superficie \u00FAtil:', 'Superfice constru\u00EDda:'];
//                                    doc.fontSize(4);
//                                    var x1 = 20, x2 = 55, y1 = 60;
//                                    for (var i = 0, len = tags.length; i < len; i++) {
//
//
//
//                                        doc.text(tags[i], x1, doc.page.height - y1, {
//                                            lineBreak: false
//                                        });
//                                        doc.text(response[i], x2, doc.page.height - y1, {
//                                            lineBreak: false
//                                        });
//                                        if ((i != 3)) {
//                                            y1 = y1 - 10;
//                                        } else if (i == 3) {
//                                            y1 = 60;
//                                            x1 = 400;
//                                            x2 = 448;
//                                        }
//                                    }

                                    let stream = doc.pipe(blobStream());
                                    stream.on('finish', function () {

                                        let blob = stream.toBlob('application/pdf');
//                                    
                                        if (navigator.msSaveOrOpenBlob) {
                                            navigator.msSaveOrOpenBlob(blob, response[0] + '.pdf');
                                        } else {
                                            var a = document.createElement("a");
                                            document.body.appendChild(a);
                                            a.style = "display: none";

                                            var url = window.URL.createObjectURL(blob);
                                            a.href = url;
                                            a.download = response[0] + '.pdf';
                                            a.click();
                                            window.URL.revokeObjectURL(url);

//                                        window.open(URL.createObjectURL(blob), '_blank', 'toolbar=0,location=0,menubar=0');
                                            //                                        var win = window.open(URL.createObjectURL(blob), '_blank');
//                                        win.focus();
//                                        window.open(URL.createObjectURL(blob));
                                            msg.hide();
//                                    msg.hide();
                                            //          window.URL.revokeObjectURL(URL.createObjectURL(blob));
                                            //      document.getElementById('pdf-file').contentWindow.location.replace(URL.createObjectURL(blob));
                                        }
                                    });
                                    doc.end();
                                    svgSize.setAttribute("transform", transformSVG);
                                    svgSize.setAttribute("style", styleSvg);
//                                     panZoom.center();
////
                                    msg.hide();
                                } else {
                                    msg.hide();
                                    Ext.FlashMessage.alert("Error al generar archivo. Intente nuevamente");
                                }
                            }, 2000);

                        },
                        callback: function () {


//                            msg.hide();
                        }


                    });

                }
            }, {
                xtype: 'tbseparator',
                width: 10
            }, {
                text: App.Language.General.section,
                xtype: 'label'
            }, {
                xtype: 'tbspacer',
                width: 5
            }, {
                xtype: 'box',
                ref: '../combo_section'
            }, {
                xtype: 'tbspacer',
                width: 5
            }, {
                iconCls: 'edit_icon',
                handler: function (b) {
                    panel = b.ownerCt.ownerCt;
                    App.Plan.Section.Store.setBaseParam('plan_id', panel.plan_id);
                    App.Plan.Section.Store.load({
                        callback: function () {
                            new App.Plan.sectionWindow({
                                listeners: {
                                    'hide': function () {
                                        App.Plan.loadSectionCombo(panel.plan_id, panel.plan_category_id);
                                        changeFill(App.Language.General.selection, "");
                                    }
                                }
                            }).show();
                        }
                    });
                }
            }, {
                xtype: 'tbseparator',
                width: 10
            }, {
                text: App.Language.Plan.view_selected,
//            iconCls: 'edit_icon',
                handler: function (b) {
                    cb = b.ownerCt.ownerCt.combo_version;
                    record = cb.getStore().getById(App.Plan.CurrentPlanId);
                    if (record.data) {
                        App.Plan.PlanVersion = record.data.plan_version;
                        App.Plan.PlanDateTime = record.data.plan_datetime_formated;
                    }

                    panel = b.ownerCt.ownerCt;
                    App.Plan.Section.Store.setBaseParam('plan_id', panel.plan_id);

                    App.Plan.Section.Store.load({
                        callback: function () {
                            new App.Plan.sectionVistaSeleccionWindow().show();
                        }
                    });


                }
            }, {
                xtype: 'tbseparator',
                width: 10
            },
//             {
//                text: App.Language.Plan.set_vista,
//                iconCls: 'zoomfit_icon',
//                handler: function () {
//                    zoomfit();
//                }
//            }, 
            {
                xtype: 'spacer',
                width: 5
            }, {
                text: App.Language.Plan.color_line,
                handler: function () {
                    new App.Plan.ViewConfigWindow().show();
                }
            }, {
                xtype: 'spacer',
                width: 5
            }, {
                text: App.Language.General.layers,
                handler: function () {
                    App.Plan.planLayers.loadData(getLayers());
                    new App.Plan.layerSelector().show();
                }
            }, {
                xtype: 'tbseparator',
                width: 10
            }, {
                iconCls: 'clean_icon',
                handler: function () {
                    dchangeStroke('');
                }
            }, {
                xtype: 'spacer',
                width: 5
            }, {
                iconCls: 'select_icon',
                tooltip: App.Language.Plan.enable_selection_of_flat_objects,
                ref: '../select_tool',
                hidden: true,
                enableToggle: true,
                toggleHandler: function (b, state) {
                    // al hacer click sobre el boton se limpia la sectorización
                    dchangeStroke('');
                    App.Plan.ViewConfig.EnableSelect = state;
                    //nuevo codigo
                    if (state) {
//                        panZoom.resetZoom();
//                        panZoom.enablelClickAssociation()
                    } else {
//                        panZoom.resetZoom();
//                        panZoom.disableClickAssociation()
                    }
                },
                listeners: {
                    'show': function (b) {
//                        console.log('>>toggle: ', b);
                        if (App.Plan.ViewConfig.EnableSelect) {

                            b.toggle(true);
                        }
                    }
                }
            }, {
                xtype: 'spacer',
                width: 5
            }, {
                text: App.Language.Plan.associating_lines,
                iconCls: 'save_icon',
                ref: '../save_select_tool',
                hidden: true,
                disabled: true,
                handler: function (b) {
//                    console.log('>> lastobj: ', lastobj);
                    if (App.Plan.SelectedLinkNodeId == null) {
                        Ext.FlashMessage.alert("Debe Seleccionar un Nodo para realizar la relaciÃ³n");
                    } else {
                        Ext.Ajax.request({
                            url: 'index.php/plan/section/nombreNodAndPlan',
                            params: {
                                plan_id: App.Plan.CurrentPlanId,
                                node_id: App.Plan.SelectedLinkNodeId
                            },
                            success: function (response) {

                                if (lastobj) {
                                    App.Plan.lastobj = lastobj;
//                                console.log(App.Plan.lastobj);
                                    App.Plan.Section.Store.setBaseParam('plan_id', App.Plan.CurrentPlanId);
                                    nps = new App.Plan.AddToNodePlanSeccion();
                                    nps.show();

                                    response = Ext.decode(response.responseText);
                                    record = response.results;
                                    Ext.getCmp('App.Plan.handler').setValue("SI");
                                    Ext.getCmp('App.Plan.nodeName').setValue(record.node_name);
//                                Ext.getCmp('App.Plan.planName').setValue(record.plan_name);

                                } else {
                                    Ext.FlashMessage.alert("Debe realizar bien la Seleccion de Linea con Recinto para poder Aplicarla");
                                }

                            }
                        });


                    }
                }
            }]
    },
    tpl: '<div id="plan_div_{plan_id}" class="container_map">\n\
<embed wmode="window" src="plans/{plan_filename}"  class="test_" id="plan_embed_{plan_id}"  width="100%" height="100%" type="image/svg+xml">\n\
<div class="controls">\n\
<button class="button_map" id="zoom-in" onclick=" restoreLupa({plan_id});restoreText({plan_id}); panZoom.zoomIn();">\n\
<i class="fas fa-plus color_map"></i></button>\n\
<button class="button_map disable_lupa" id="zoom-out" onclick="restoreLupa({plan_id});restoreText({plan_id}); panZoom.zoomOut();">\n\
<i class="fas fa-minus color_map"></i></button>\n\
<button class="button_map" id="lupa_zoom_{plan_id}" onclick="restoreText({plan_id});changeLupa({plan_id});">\n\
<i class="fas fa-search"></i></button>\n\
<button class="button_map" id="text_zoom_{plan_id}" onclick="restoreLupa({plan_id});getText({plan_id});">\n\
<i class="fas fa-text-height"></i></button>\n\
<div class="ty" id="configuracion_text_{plan_id}" style="display:  none;"><div id="two" class="dialog2">\n\
<div class="opcion" ><label>Letra</label>\n\
<select onchange="changeText({plan_id})" id="text_font_{plan_id}">\n\
<option value="Agency FB">Agency FB</option>\n\
<option value="Antiqua">Antiqua</option>\n\
<option value="Arial" >Arial</option>\n\
<option value="Calibri">Calibri</option>\n\
<option value="Comic Sans">Comic Sans</option>\n\
<option value="Monoespaciado">Monoespaciado</option>\n\
<option value="sans-serif" selected >sans-serif</option>\n\
<option value="Times New Roman">Times New Roman</option>\n\
<option value="Verdana">Verdana</option>\n\
</select></div>\n\
<div class="opcion" ><label>Tamano</label>\n\
<select onchange="changeText({plan_id})" id="text_size_{plan_id}">\n\
<option value="4" >4</option>\n\
<option value="8" >8</option>\n\
<option value="9">9</option>\n\
<option value="10" selected>10</option>\n\
<option value="11" >11</option>\n\
<option value="12" >12</option>\n\
<option value="14">14</option>\n\
<option value="16">16</option>\n\
<option value="18">18</option>\n\
<option value="20" >20</option>\n\
<option value="22">22</option>\n\
<option value="24">24</option>\n\
<option value="28">28</option>\n\
<option value="32">32</option>\n\
<option value="36">36</option>\n\
<option value="125"  >125</option>\n\
</select></div>\n\
<div class="opcion"><label>Color</label>\n\
<input name="color" type="color" onchange="changeText({plan_id})"  id="text_color_{plan_id}" value="#f00a0a"/>\n\
</div>\n\
<div class="opcion" id="text_div_{plan_id}"><label>Texto</label>\n\
<textarea rows="4" cols="50" class="text_svg"  onkeyup="changeText({plan_id}, event)" id="text_content_{plan_id}" disabled>\n\
</textarea>\n\
</div>\n\
<input type="hidden" id="rect_hidden_{plan_id}" value="">\n\
<input type="hidden" id="id_embed" value="{plan_id}">\n\
<div class="opcion" id="btn_text_{plan_id}">\n\
<button type="button" class="x-btn-mc border_but" id="save_btn_{plan_id}" onclick="saveText({plan_id})" style="margin-left: 8px;cursor: auto;">Guardar</button>\n\
<button type="button" class="x-btn-mc border_but" id="delete_btn_{plan_id}" style="display:none;" onclick="textRemove({plan_id})" style="cursor: auto;">Eliminar</button>\n\
<button type="button" class="x-btn-mc border_but" onclick="getText({plan_id})" style="cursor: auto;">Cancelar</button>\n\
</div>\n\
</div>\n\
<div><i class="fa fa-play color_buttons" aria-hidden="true"></i></div></div><button class="button_map reset_button" id="reset" onclick="restoreLupa({plan_id});restoreText({plan_id}); panZoom.resetWidth();">\n\
<i class="fas fa-compress"></i></button></div>\n\
</div>'

// <div id="thumbViewContainer"><svg id="scopeContainer" class="thumbViewClass"><g><rect id="scope" fill="red" fill-opacity="0.1" stroke="red" stroke-width="2px" x="0" y="0" width="0" height="0"/><line id="line1" stroke="red" stroke-width="2px" x1="0" y1="0" x2="0" y2="0"/><line id="line2" stroke="red" stroke-width="2px" x1="0" y1="0" x2="0" y2="0"/></g></svg><embed id="thumbView" type="image/svg+xml" src="plans/{plan_filename}" class="thumbViewClass"/></div>{prueba("plan_div_{plan_id}")}'
});



function restoreLupa(id) {

    var idElement = 'plan_embed_' + id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;
    var ZoomL = document.getElementById('lupa_zoom_' + id);
    if (ZoomL.hasAttribute("style")) {

        ZoomL.removeAttribute('style');
    }

    svgElement.setAttribute('style', 'cursor: pointer;');
    svgElement.setAttribute('class', 'default');
    panZoom.disableClickZoom();
    panZoom.disablelDblclickZoomOut();
}




function changeLupa(id) {

    var idElement = 'plan_embed_' + id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;
    var ZoomL = document.getElementById('lupa_zoom_' + id);
    //console.log('clase: ' + clase);
    if (clase) {
        svgElement.setAttribute('class', 'default');
        clase = false;
    }

    if (svgElement.getAttribute('class') == 'change-cursor') {
        if (ZoomL.hasAttribute("style")) {

            ZoomL.removeAttribute('style');
        }
        // ZoomL.setAttribute('style', 'background-color: #d3e1f1');
        svgElement.setAttribute('style', 'cursor: pointer;');
        svgElement.setAttribute('class', 'default');
        panZoom.disableClickZoom();
        panZoom.resetWidth();
        //panZoom.resetZoom

    } else {

        //se indica que esta activa la lupa 
        ZoomL.setAttribute('style', 'background-color: #deecfd');
        //cambiar diseño del cursor 
        svgElement.setAttribute('style', 'cursor: zoom-in');
        //agregarle la classe para que sea el identificador
        svgElement.setAttribute('class', 'change-cursor');
        //se configura para permitir la opción zoomClick
        panZoom.enablelClickZoom();
    }
}

function restoreText(id) {

    var idElement = 'plan_embed_' + id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;
    var textZoom = document.getElementById('text_zoom_' + id);

    if (hasClass(textZoom, 'act')) {

        svgElement.setAttribute('style', 'cursor: pointer;');
        document.getElementById('configuracion_text_' + id).setAttribute('style', 'display: none;');
        textZoom.removeAttribute('style');
        textZoom.classList.remove("act");
        var textArea = document.getElementById('text_content_' + id);
        textArea.value = '';
        textArea.disabled = true;
        panZoom.disableClickText();

        var btnDelete = document.getElementById('delete_btn_' + id).style.display;

        if (btnDelete === 'inline-block') {

            var save_btn = document.getElementById('save_btn_' + id);
            save_btn.setAttribute('style', 'display: inline-block;');
            document.getElementById('delete_btn_' + id).setAttribute('style', 'display: none;');
            var save_btn = document.getElementById('save_btn_' + id);
            save_btn.setAttribute('style', 'display: inline-block;');
            document.getElementById('text_div_' + id).setAttribute('style', 'display:inline-block;');
            document.getElementById('rect_hidden_' + id).value = "";

        } else {

            var idText = document.getElementById('rect_hidden_' + id);
            var text = svgElement.getElementById(idText.value);
            if (text) {
                var polyLine = text.previousSibling;
                var circle = polyLine.previousSibling;
                var parent = circle.parentNode;
                parent.removeChild(circle);

                var parent = polyLine.parentNode;
                parent.removeChild(polyLine);

                var parent = text.parentNode;
                parent.removeChild(text);

            }
        }
    }

}

function getText(id) {

    var textZoom = document.getElementById('text_zoom_' + id);


    var idElement = 'plan_embed_' + id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;

    if (hasClass(textZoom, 'act')) {

        svgElement.setAttribute('style', 'cursor: pointer;');
        document.getElementById('configuracion_text_' + id).setAttribute('style', 'display: none;');
        textZoom.removeAttribute('style');
        textZoom.classList.remove("act");

        var textArea = document.getElementById('text_content_' + id);
        textArea.value = '';
        textArea.disabled = true;
        panZoom.disableClickText();

        var btnDelete = document.getElementById('delete_btn_' + id).style.display;

        if (btnDelete === 'inline-block') {

            var save_btn = document.getElementById('save_btn_' + id);
            save_btn.setAttribute('style', 'display: inline-block;');
            document.getElementById('delete_btn_' + id).setAttribute('style', 'display: none;');
            document.getElementById('text_div_' + id).setAttribute('style', 'display:inline-block;');
            var save_btn = document.getElementById('save_btn_' + id);
            save_btn.setAttribute('style', 'display: inline-block;');
            document.getElementById('text_div_' + id).setAttribute('style', 'display:inline-block;');
            document.getElementById('rect_hidden_' + id).value = "";

        } else {
            var idText = document.getElementById('rect_hidden_' + id);
            var text = svgElement.getElementById(idText.value);

            if (text) {
                var polyLine = text.previousSibling;
                var circle = polyLine.previousSibling;
                var parent = circle.parentNode;
                parent.removeChild(circle);

                var parent = polyLine.parentNode;
                parent.removeChild(polyLine);

                var parent = text.parentNode;
                parent.removeChild(text);
            }

        }
    } else {
        textZoom.classList.add("act");
        svgElement.setAttribute('style', 'cursor: crosshair;');
        document.getElementById('configuracion_text_' + id).setAttribute('style', 'display:inline-block;');
        textZoom.setAttribute('style', 'background-color: #deecfd;');
        panZoom.enablelClickText();
        //  panZoom.enablelIdText(constante);


    }

}

function hasClass(element, className) {
    return element.className && new RegExp("(^|\\s)" + className + "(\\s|$)").test(element.className);
}
function changeText(id, event) {
    
     App.Plan.TextSelect= '';
    //se obtiene el id del campo hidden
    var idText = document.getElementById('rect_hidden_' + id);
    var textFont = document.getElementById('text_font_' + id);
    var textSize = document.getElementById('text_size_' + id);
    var textColor = document.getElementById('text_color_' + id);
    var enterText = document.getElementById('text_content_' + id);



    var idElement = 'plan_embed_' + id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;
    var text = svgElement.getElementById(idText.value);


    if (enterText.value.length == 0) {
        var allText = svgElement.getElementById(idText.value).children;
       
        for (i = 0; i < allText.length; i++)
        {
            App.Plan.TextSelect += allText[i].textContent;
            if(i != (allText.length-1)){
                App.Plan.TextSelect += '\n';
            }    
       
        }
        
        
        
    } else {
        App.Plan.TextSelect = enterText.value;
        
    }

    text.setAttribute("font-family", textFont.value);
    text.setAttribute("font-size", textSize.value);
    text.setAttribute("fill", textColor.value);
    var textContent = document.createTextNode(App.Plan.TextSelect);


    var leng = Number(text.getBoundingClientRect().width) + (Number(textSize.value));
//    console.log('>>leng', leng)


//    console.log('Recuadro--->', text.getBoundingClientRect());
//    console.log('Recuadro--->', text.getBoundingClientRect().inverse());

//SVGMatrix mat = ((SVGLocatable) tag).getScreenCTM();
//      SVGMatrix imat = mat.inverse();
//      SVGPoint cPt = ((SVGDocument) doc).getRootElement().createSVGPoint();
//      cPt.setX(gnme.getClientX());
//      cPt.setY(gnme.getClientY());
//      return cPt.matrixTransform(imat);


    var polyline = text.previousSibling
    var leng_polyline = polyline.getAttribute('points').split(' ');
    var var_polyline = leng_polyline[2].split(',');
    var var_polyline2 = leng_polyline[0].split(',');
    var var_polyline3 = leng_polyline[1].split(',');


    text.setAttribute("y", parseFloat(var_polyline3[1]) - 5);



    if (text.childNodes[0]) {

        while (text.hasChildNodes()) {
            text.removeChild(text.lastChild);
        }


    }

    array_text = App.Plan.TextSelect.split('\n');
    var inicio = 0;
    var max_leng = 0;
    array_text.forEach(function (entry) {

        if (inicio == 0) {
            if (array_text.length === 1) {
                tamano_text = 0;
            } else {
                tamano_text = parseInt(textSize.value) * (-Number(array_text.length - 1));
            }

            inicio = 1;
        } else {
            tamano_text = parseInt(textSize.value);
        }

        var svgtspan = document.createElementNS("http://www.w3.org/2000/svg", "tspan");
        svgtspan.setAttribute("x", text.getAttribute('x'));
        svgtspan.setAttribute("dy", tamano_text);

//        
        svgtspan.appendChild(document.createTextNode(entry));

        text.appendChild(svgtspan);
        var long = svgtspan.getComputedTextLength();
        
        if (Number(long) > max_leng) {
            max_leng = long;
        }
    });



//    console.log('>>max length', max_leng);





//    var long = text.getComputedTextLength();

    if (App.Plan.TextSelect.length === 0) {
        polyline.setAttribute("points", leng_polyline[0] + " " + leng_polyline[1] + " " + (parseFloat(var_polyline2[0]) + parseFloat(30)) + "," + var_polyline[1]);
    } else {
        polyline.setAttribute("points", leng_polyline[0] + " " + leng_polyline[1] + " " + (parseFloat(var_polyline3[0]) + Number(max_leng)) + "," + var_polyline[1]);
    }




}

function saveText(plan_id) {

//    console.log(App.Interface.selectedNodeId);



    var textFont = document.getElementById('text_font_' + plan_id);
    var textSize = document.getElementById('text_size_' + plan_id);
    var textColor = document.getElementById('text_color_' + plan_id);
    var enterText = document.getElementById('text_content_' + plan_id);
    var idText = document.getElementById('rect_hidden_' + plan_id);


    var idElement = 'plan_embed_' + plan_id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;
    var archivo = embElement.getAttribute('src').substring(6);
    archivo = archivo.split('?');
    archivo = archivo[0];

    var text = svgElement.getElementById(idText.value);
    var textX = text.getAttribute("x");
    var textY = text.getAttribute("y");



    if (text.getComputedTextLength() > 0) {
        var polyLine = text.previousSibling;
        var circle = polyLine.previousSibling;


        positionX = circle.getAttribute("cx");
        positionY = circle.getAttribute("cy");


        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xhr.open('POST', 'index.php/plan/plan/changePlan');
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        params = "json_params=" + JSON.stringify({plan_id: plan_id, textFont: textFont.value, textSize: textSize.value, textColor: textColor.value, enterText: enterText.value, textX: textX, textY: textY, archivo: archivo, node_id: App.Interface.selectedNodeId, positionX: positionX, positionY: positionY, textLength: Number(text.getBoundingClientRect().width), eliminar: false});
        xhr.send(params);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    //   console.log("xhr done successfully");
                    var resp = xhr.responseText;
                    var respJson = JSON.parse(resp);
                    if (respJson.success) {
                        panZoom.cleanPosition();
                        document.getElementById('rect_hidden_' + plan_id).value = "";
                        restoreText(plan_id);

                        //let archivo = embElement.getAttribute('src');
//                        var elem = document.getElementById(idElement);
//                        elem.parentNode.removeChild(elem);

//                        var tab_id = $('.x-tab-strip-active').attr('id');
//                        var r = document.getElementById(tab_id);

//                        App.Plan.Store.load
//                                ({
//                                    callback: function ()
//                                    {
//
//                                        App.Plan.Store.AllVersions.load();
////                                        
////                                        r.childNodes[1].click();
//                                    }
//                                });
//                        r.childNodes[1].click();
//                        tab_id = r.childNodes[1].getAttribute('id');
//                        console.log('#' + tab_id + " span");
//                        $('#' + tab_id + " span").click();

                    } else {
                        alert('Error:\n' + respJson.msg);
                    }
                } else {
//                    console.log("xhr failed");
                }
            } else {
//                console.log("xhr processing going on");
            }
        }
    }

}
function deleteText2(id, plan_id) {

    var save_btn = document.getElementById('save_btn_' + plan_id);
    save_btn.setAttribute('style', 'display: none;');
    document.getElementById('two').setAttribute('style', 'bottom: -9px;');
    document.getElementById('btn_text_' + plan_id).setAttribute('style', 'padding-left: 11px;');
    document.getElementById('delete_btn_' + plan_id).setAttribute('style', 'display: inline-block;');
    document.getElementById('text_div_' + plan_id).setAttribute('style', 'display:none;');
    document.getElementById('configuracion_text_' + plan_id).setAttribute('style', 'display:inline-block;');
    document.getElementById('rect_hidden_' + plan_id).value = id;

    var textZoom = document.getElementById('text_zoom_' + plan_id);
    textZoom.setAttribute('style', 'background-color: #deecfd;');
    textZoom.classList.add("act");






}

function textRemove(id) {

    var idElement = 'plan_embed_' + id;
    var embElement = document.getElementById(idElement);
    var svgElement = embElement.getSVGDocument().documentElement;
    var archivo = embElement.getAttribute('src').substring(6);
    archivo = archivo.split('?');
    archivo = archivo[0];

    var idText = document.getElementById('rect_hidden_' + id);
    var text = svgElement.getElementById(idText.value);
    var polyLine = text.previousSibling;
    var circle = polyLine.previousSibling;


    document.getElementById('configuracion_text_' + id).setAttribute('style', 'display:none;');
    var save_btn = document.getElementById('save_btn_' + id);
    save_btn.setAttribute('style', 'display: inline-block;');
    document.getElementById('delete_btn_' + id).setAttribute('style', 'display: none;');
    document.getElementById('text_div_' + id).setAttribute('style', 'display:inline-block;');

    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xhr.open('POST', 'index.php/plan/plan/changePlan');
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    params = "json_params=" + JSON.stringify({plan_id: id, archivo: archivo, node_id: App.Interface.selectedNodeId, eliminar: true, idText: idText.value});
    xhr.send(params);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
//                console.log("xhr done successfully");
                var resp = xhr.responseText;
                var respJson = JSON.parse(resp);
                if (respJson.success) {

                    id2 = idText.value.split("text_");
                    panZoom.deletePosition(id2[1]);
                    panZoom.cleanPosition();
                    document.getElementById('rect_hidden_' + id).value = "";
                    restoreText(id);



                } else {
                    alert('Error:\n' + respJson.msg);
                }
            } else {
//                console.log("xhr failed");
            }
        } else {
//            console.log("xhr processing going on");
        }
    }


}

App.Plan.AddToNodePlanSeccion = Ext.extend(Ext.Window,
        {
            title: App.Language.Plan.income_layer,
            width: 380,
            height: 200,
            layout: 'fit',
            modal: true,
            resizable: false,
            padding: 1,
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'form',
                                labelWidth: 150,
                                padding: 5,
                                items:
                                        [{
                                                xtype: 'displayfield',
                                                fieldLabel: App.Language.Plan.linkeado,
                                                id: 'App.Plan.handler',
                                                anchor: '100%'
                                            }, {
                                                xtype: 'displayfield',
                                                fieldLabel: App.Language.General.venue_name,
                                                id: 'App.Plan.nodeName',
                                                anchor: '100%'
                                            }, {
                                                xtype: 'spacer',
                                                height: 5
                                            }, {
                                                xtype: 'combo',
                                                fieldLabel: App.Language.Plan.income_layer,
                                                anchor: '100%',
                                                id: 'App.Plan.Seccion.Id',
                                                store: App.Plan.Section.Store,
                                                hiddenName: 'plan_section_id',
                                                triggerAction: 'all',
                                                displayField: 'plan_section_name',
                                                valueField: 'plan_section_id',
                                                editable: true,
                                                typeAhead: true,
                                                selectOnFocus: true,
                                                forceSelection: true
                                            }],
                                buttons:
                                        [{
                                                xtype: 'button',
                                                text: App.Language.General.close,
                                                handler: function (b)
                                                {
                                                    b.ownerCt.ownerCt.ownerCt.close();
                                                }
                                            }, {
                                                xtype: 'button',
                                                text: App.Language.General.add,
                                                handler: function (b)
                                                {
                                                    if (Ext.getCmp('App.Plan.Seccion.Id').getValue() == '' || Ext.getCmp('App.Plan.Seccion.Id').getValue() == null) {
                                                        Ext.FlashMessage.alert(App.Language.Plan.you_must_select_a_layer_to_relate);

                                                    } else {
//                                                        console.log('Aqui');
                                                        App.Plan.saveHandler(App.Plan.lastobj, App.Plan.SelectedLinkNodeId, App.Plan.CurrentPlanId, Ext.getCmp('App.Plan.Seccion.Id').getValue(), function () {
                                                            dchangeStroke('');
                                                            Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(3525));
                                                            Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.setDisabled(true);
                                                            Ext.FlashMessage.alert(App.Language.Plan.successfully_associated_lines);
                                                        });
                                                        b.ownerCt.ownerCt.ownerCt.close();
                                                    }
                                                }
                                            }]
                            }];
                App.Plan.AddToNodePlanSeccion.superclass.initComponent.call(this);
            }
        });


App.Plan.Store.on('load', function () {

    if (Ext.getCmp('App.Plan.Principal')) {
        Ext.getCmp('App.Plan.Principal').removeAll(true);
        if (App.Plan.Store.baseParams.node_id != 'root') {
            Ext.getCmp('App.Plan.Principal').add(new App.Plan.AllVersionsGrid({
                id: '0'
            }));
        }
        App.Plan.Store.each(function (record) {

            var n = record.data.handler.search(",");

            if (n != -1) {
                record.data.handler = record.data.handler.replace(",", "");
            }


            App.Plan.HandlerZoom = ((record.data.handler !== 'undefined') && (record.data.handler.length)) ? record.data.handler : '';
            App.Plan.NodeZoom = ((record.data.plan_node_id !== 'undefined') && (record.data.plan_node_id.length)) ? record.data.plan_node_id : '';
            if (App.Plan.HandlerZoom.length) {

                Ext.Ajax.request({
                    url: 'index.php/plan/plan/getSection',

                    params: {id_section: record.data.plan_section_id},
                    success: function (response) {

                        response = Ext.decode(response.responseText);
                        results = response.results;
                        if (results.length) {
                            if (results[0].plan_section_color.length) {
                                App.Plan.colorSection = results[0].plan_section_color;
                            } else {
                                App.Plan.colorSection = "FF0000";
                            }
                        } else {
                            App.Plan.colorSection = "FF0000";
                        }

                    }


                });

            }



            App.Plan.tabId++;


            if (record.data.PlanCategory.plan_category_id != 4) {
                var tab = new App.Plan.Tab({
                    id: record.data.PlanCategory.plan_category_id,
                    plan_category_id: record.data.PlanCategory.plan_category_id,
                    version: record.data.plan_id,
                    node_id: record.data.node_id,
                    plan_id: record.data.plan_id,
                    title: record.data.PlanCategory.plan_category_name,
                    data: {
                        //plano buscado
                        plan_filename: record.data.plan_filename,
                        plan_id: record.data.plan_id
                    }
                });

                if ((App.Security.Actions['3003']) && (record.data.PlanCategory.plan_category_default == 1) && (App.Interface.permits === true)) {
                    tab.select_tool.show();
                    tab.save_select_tool.show();
                }
                if (record.data.PlanCategory.plan_category_default == 1) {
                    App.Plan.DefaultTabPanel = record.data.PlanCategory.plan_category_id;
                }
                Ext.getCmp('App.Plan.Principal').add(tab);
            } else {
               
                Ext.Ajax.request({
                    url: 'index.php/plan/plan/getBim',

                    params: {
                        node_id: record.data.node_id,
                        plan_category_id: record.data.plan_category_id
                    },
                    success: function (response) {

                        response = Ext.decode(response.responseText);
                        results = response.results;


                        var position = results.length - 1;


                        if (results[position].url.length > 0) {

                            var tabBim = new App.Plan.TabBim({
                                id: record.data.PlanCategory.plan_category_id,
                                title: record.data.PlanCategory.plan_category_name,
                                plan_category_id: record.data.PlanCategory.plan_category_id,
                                version: record.data.plan_id,
                                node_id: record.data.node_id,
                                plan_id: record.data.plan_id,
                                data: {
                                    //plano buscado
                                    urlBim: results[position].url,
                                    plan_id: record.data.plan_id,

                                }


                            });

                            Ext.getCmp('App.Plan.Principal').add(tabBim);

                        }

                    }


                });
            }


        });

        Ext.getCmp('App.Plan.Principal').doLayout();
        Ext.getCmp('App.Plan.Principal').activate(App.Plan.DefaultTabPanel);
        App.Plan.DefaultTabPanel = 0;
    }
});

App.Plan.TabBim = Ext.extend(Ext.Panel, {
    xtype: 'panel',
    id: App.Plan.tabId,
    listeners: {
        'render': function (panel) {
            panel.__value = panel.version;
            panel.combo_version.setValue('');
            panel.combo_version.getStore().setBaseParam('node_id', panel.node_id);
            panel.combo_version.getStore().setBaseParam('plan_category_id', panel.plan_category_id);
            panel.combo_version.getStore().load({
                callback: function (r) {
                    if (panel.combo_version_2) {
                        aux = [];
                        var cont = 0;
                        class_version = '';
                        panel.combo_version.getStore().each(function (record) {

                            if (cont == 0) {

                                class_version = record.data.plan_id;
                                cont++;
                            }
                            aux.push('<option value="' + record.data.plan_id + '">' + record.data.plan_version + '</option>');
                        });

                        var t = new Ext.XTemplate('<select  class="combo_version ' + class_version + '" onchange="this.setAttribute(\'val-select\', this.value); App.Plan.loadVersionPlan(this.value,' + panel.plan_category_id + ');">' +
                                aux.join('') +
                                '</select>');
                        t.overwrite(panel.combo_version_2.el);
                    }
                }
            });

        },
        'beforedestroy': function (panel) {
            panel.purgeListeners();
            lastobj = null;
        }
    },
    tbar: {
        xtype: 'toolbar',
        autoScroll: 'auto',
        height: 30,
        items: [
            App.ModuleActions[3002],
            {
                xtype: 'tbspacer',
                width: 5
            }, {
                xtype: 'tbseparator',
                width: 10
            }, {
                text: App.Language.General.version,
                xtype: 'label',
                ref: '../version_label',
                hidden: false

            }, {
                xtype: 'tbspacer',
                width: 5
            }, {
                xtype: 'combo',
                ref: '../combo_version',
                hidden: true,
                store: {
                    xtype: 'App.Plan.Version.Store'
                },
                displayField: 'plan_version',
                valueField: 'plan_id',
                width: 100,
                triggerAction: 'all',
                editable: false,
                mode: 'remote',
                minChars: 0
            }, {
                xtype: 'box',
                ref: '../combo_version_2'
            },
            {
                xtype: 'tbspacer',
                width: 5
            }, {
                text: App.Language.General.details,
                iconCls: 'information_icon',
                handler: function (b) {

                    cb = b.ownerCt.ownerCt.combo_version;
                    record = cb.getStore().getById(App.Plan.CurrentPlanId);
                    w = new App.Plan.DetailPlanWindow();
                    w.plan_id = record.data.plan_id;
                    w.form.getForm().loadRecord(record);
                    w.show();
                    w.toFront(true);
                }
            }
        ]

    },
    tpl: '<div id="{plan_id}" class="">\n\
<embed wmode="window" src="{urlBim}"  class="test_" id="plan_embed_{plan_id}"  width="100%" height="100%" ></embed></div>'


});

App.Plan.Principal = Ext.extend(Ext.TabPanel, {
    border: false,
    id: 'App.Plan.Principal',
    enableTabScroll: true,
    listeners: {
        'destroy': function () {

            App.Plan.SelectedCategoryId = null;
            App.Plan.SelectedNodeId = null;
            App.Plan.location = null;
            App.Plan.Node.Store.removeAll();
//            console.log('>> App.Plan.ViewConfig.EnableSelect destroy');
            Ext.getCmp('App.StructureTree.Tree').removeListener('iconclick', App.Plan.viewNodeLink);
        },
        'handler': function () {

        },
        'tabchange': function (tp, p) {

            if (p) {


                var inf = jQuery('.' + p.version).attr('val-select');

                var id = (inf) ? inf : p.plan_id;
                var category = p.plan_category_id;

                if (category != 4) {
                    App.Plan.loadSectionCombo(id, category);
                    cleanAll();
                    var msg = '';
                    if (id) {

                        var idElement = 'plan_embed_' + id;

                        var embElement = document.getElementById(idElement);
                        var attributeEmb = embElement.getAttribute("src");

                        if (attributeEmb != 'plans/not_image_icon.png') {
                            jQuery('#' + idElement).removeClass('no_img');
                            embElement.addEventListener("load", function () {

                                setTimeout(function () {

                                    jQuery('.print_icon').show();
                                    jQuery('.controls').show();



                                    if (msg) {
                                        msg.hide();
                                    }

                                }, 1000);
                            }, false);
                        } else {
                            jQuery('#' + idElement).addClass('no_img');

                        }
                        if (embElement != null) {
                            if (App.Plan.idPlan.indexOf(idElement) === -1) {
//                            msg = Ext.MessageBox.wait("", "Cargando...");
                            }
                        }
                        zomm_m(id, msg);



                    }
                } else {
                    App.Plan.loadBim(id, category);

                }

            }

            if (p)
                App.Plan.SelectedCategoryId = p.id;

            if (p) {

                var inf = jQuery('.' + p.version).attr('val-select');

                App.Plan.CurrentPlanId = (inf) ? inf : p.plan_id;
            }


        }
    }
});

App.Plan.getPlan = function (node) {

    if (node && node.id && App.Plan.ViewConfig.EnableSelect == false) {

        Ext.getCmp('App.Plan.Principal').removeAll(true);
        App.Plan.SelectedNodeId = node.id;
        App.Plan.Store.setBaseParam('node_id', node.id);
        App.Plan.Store.load();
        //App.Plan.Coordinate.Store.setBaseParam('node_id', node.id);        
        App.Plan.Store.AllVersions.setBaseParam('plan_category_id', null);
        App.Plan.Store.AllVersions.setBaseParam('plan_current_version', 0);
        App.Plan.Store.AllVersions.setBaseParam('node_id', node.id);
        App.Plan.Store.AllVersions.load();
    } else {
        if (App.Plan.ViewConfig.EnableSelect == true) {
            App.Plan.viewNodeLink(node);
        }
    }
}

App.Plan.viewNodeLink = function (node) {

//    console.log('>>   App.Plan.Id: ', App.Plan.idPlan);


    App.Plan.SelectedLinkNodeId
    App.Plan.SelectedLinkNodeId = node.id;
    App.Plan.getNodeHandler.setBaseParam('node_id', node.id);
    App.Plan.getNodeHandler.load({
        callback: function (record, options, success) {
//            App.Plan.getNodeHandler.each(function (record) {
            // activate tab and zooming
//                console.log('>>record Categoria: ', record.data.Plan.plan_category_id);
//                console.log('>>App.Plan.SelectedCategoryId: ', App.Plan.SelectedCategoryId);
//            console.log('>>record: ', record.length);
//                console.log('>>App.Plan.SelectedNodeId: ', App.Plan.SelectedNodeId);
//

            if (!record.length) {
                var plan = document.getElementsByTagName("embed")[0].src.split('/');
                var long = plan.length - 1;


                Ext.Ajax.request({
                    url: 'index.php/plan/plan/getDataPlan',
                    params: {
                        plan_name: plan[long],

                    },
                    success: function (response) {
                        response = Ext.decode(response.responseText);
                        results = response.results
                        App.Plan.NodePlan = results.node_id;
                    }
                });
            }

//            console.log(record[0].id);
//            console.log(record[0].data);
//            console.log(record[0].data.Plan);

            if (record.length) {
                if (App.Plan.SelectedLinkNodeId != record[0].data.Plan.node_id) {
                    Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.enable();
                } else {
                    Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.setDisabled(true);
                }
            } else {
                if (App.Plan.SelectedLinkNodeId != App.Plan.NodePlan) {
                    Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.enable();
                } else {
                    Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.setDisabled(true);
                }
            }
            //Cuando se selecciona el nodo donde se encuentra el plano
//               if (App.Plan.SelectedCategoryId != record.data.Plan.plan_category_id && App.Plan.SelectedNodeId == record.data.Plan.node_id) {
//             if ( App.Plan.SelectedNodeId == record.data.Plan.node_id) {
//                    App.Plan.Handler = record.data.handler;
//                    Ext.getCmp('App.Plan.Principal').activate(record.data.Plan.plan_category_id);
//                    Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.enable();
//                    console.log('Aqui 1: ');
//                    return;
//                }
//                // load tab's and zooming - call listener to load tab's
//                if (App.Plan.SelectedCategoryId != record.data.Plan.plan_category_id || App.Plan.SelectedNodeId != record.data.Plan.node_id) {
//                    App.Plan.DefaultTabPanel = record.data.Plan.plan_category_id;
//                    App.Plan.Handler = record.data.handler;
//                    node = Ext.getCmp('App.StructureTree.Tree').getNodeById(record.data.Plan.node_id);
//                    App.Plan.getPlan(node);
//                    console.log('Aqui 2: ');
//                    return;
//                    
//                }
//                // just zooming
//                dchangeStroke(record.data.handler);
//                Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.enable();
//            });
            App.Plan.forcePlanTabLoad = false;
        }
    });
    return;
}

App.Plan.Principal.listener = function (node) {

    //    if (node && node.id)
    //    {
    //        App.Plan.NodeId = node.id;
    //        App.Plan.Node.Store.setBaseParam('node_id',node.id);   
    //        App.Plan.Node.Store.load();
    //        App.Plan.Coordinate.Store.setBaseParam('node_id', node.id);                   
    //        App.Plan.Coordinate.Store.load();       
    //    }
//    console.log('>> Listener: ', node);
    App.Plan.getPlan(node);

    App.Plan.Node.Store.load({
        callback: function (record, options, success) {
//            console.log('>>record', App.Plan.location);
            App.Plan.Node.Store.each(function (record) {
                App.Plan.location = record.data.node_type_location;
//                console.log('>>Location', App.Plan.location);
            });
        }
    });
}

App.Plan.DetailPlanWindow = Ext.extend(Ext.Window, {
    title: App.Language.Plan.detail_plan,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    height: 300,
    layout: 'fit',
    padding: 1,
    resizable: false,
    modal: true,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                ref: 'form',
                padding: 5,
                items: [{
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.description,
                        autoScroll: true,
                        name: 'plan_description',
                        anchor: '100%'
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.version,
                        name: 'plan_version',
                        anchor: '100%'
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.category,
                        name: 'plan_category_name',
                        anchor: '100%'
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.user_magazine,
                        name: 'user_name',
                        anchor: '100%'
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.Plan.upload_date,
                        name: 'plan_datetime_formated',
                        anchor: '100%'
                    }, {
                        xtype: 'displayfield',
                        fieldLabel: App.Language.General.comment,
                        autoScroll: true,
                        height: 110,
                        name: 'plan_comments',
                        anchor: '100%'
                    }],
                buttons: [{
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.hide();
                        }
                    }]
            }];
        App.Plan.DetailPlanWindow.superclass.initComponent.call(this);
    }
});

App.Plan.exportListWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.eexport_list,
    width: (screen.width < 400) ? screen.width - 50 : 400,
    height: 250,
    layout: 'fit',
    modal: true,
    resizable: false,
    padding: 1,
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                padding: 5,
                items: [{
                        xtype: 'textfield',
                        fieldLabel: App.Language.General.file_name,
                        anchor: '100%',
                        name: 'file_name',
                        maskRe: /^[a-zA-Z0-9_]/,
                        regex: /^[a-zA-Z0-9_]/,
                        allowBlank: false
                    }, {
                        xtype: 'radiogroup',
                        fieldLabel: App.Language.General.output_type,
                        columns: 1,
                        items: [{
                                boxLabel: 'Excel',
                                name: 'output_type',
                                inputValue: 'e',
                                height: 25,
                                checked: true
                            }, {
                                boxLabel: 'PDF',
                                name: 'output_type',
                                inputValue: 'p',
                                height: 25
                            }]
                    }],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.hide();
                        }
                    }, {
                        xtype: 'button',
                        text: App.Language.General.eexport,
                        handler: function (b) {
                            fp = b.ownerCt.ownerCt;
                            form = b.ownerCt.ownerCt.getForm();
                            if (form.isValid()) {
                                form.submit({
                                    clientValidation: true,
                                    waitTitle: App.Language.General.message_please_wait,
                                    waitMsg: App.Language.General.message_generating_file,
                                    url: 'index.php/plan/plan/exportList',
                                    params: App.Plan.Store.AllVersions.baseParams,
                                    success: function (form, response) {
                                        document.location = 'index.php/app/download/' + response.result.file;
                                        b.ownerCt.ownerCt.ownerCt.hide();
                                    },
                                    failure: function (form, action) {
                                        switch (action.failureType) {
                                            case Ext.form.Action.CLIENT_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_client_invalid);
                                                break;
                                            case Ext.form.Action.CONNECT_FAILURE:
                                                Ext.Msg.alert(App.Language.General.error, App.Language.General.message_extjs_failed_connection);
                                                break;
                                            case Ext.form.Action.SERVER_INVALID:
                                                Ext.Msg.alert(App.Language.General.error, action.result.msg);
                                        }
                                    }
                                });
                            }
                        }
                    }]
            }];
        App.Plan.exportListWindow.superclass.initComponent.call(this);
    }
});

App.Plan.Version.Window = Ext.extend(Ext.Window, {
    title: App.Language.General.versions,
    id: 'App.Plan.PrincipalVersion',
    ref: 'principal',
    resizable: false,
    modal: true,
    border: true,
    width: 800,
    height: 465,
    layout: 'fit',
    padding: 1,
    listeners: {
        'beforerender': function (w) {
            w.item.grid.getStore().setBaseParam('plan_category_id', w.plan_category_id);
            w.item.grid.getStore().setBaseParam('plan_id', w.plan_id);
            w.item.grid.getStore().setBaseParam('node_id', w.node_id);
            w.item.grid.getStore().load();
        }
    },
    initComponent: function () {
        this.items = [{
                ref: 'item',
                border: true,
                items: [{
                        border: false,
                        xtype: 'grid',
                        ref: 'grid',
                        store: new App.Plan.Version.Store(),
                        height: 350,
                        viewConfig: {
                            forceFit: true
                        },
                        columns: [new Ext.grid.CheckboxSelectionModel(), {
                                header: App.Language.General.description,
                                sortable: true,
                                dataIndex: 'plan_description'
                            }, {
                                header: App.Language.General.version,
                                sortable: true,
                                dataIndex: 'plan_version'
                            }, {
                                header: App.Language.General.uploaded_by,
                                sortable: true,
                                dataIndex: 'User',
                                renderer: function (User) {
                                    return User.user_name;
                                }
                            }, {
                                xtype: 'datecolumn',
                                sortable: true,
                                header: App.Language.General.creation_date,
                                dataIndex: 'plan_datetime',
                                format: App.General.DefaultDateTimeFormat
                            }],
                        sm: new Ext.grid.CheckboxSelectionModel(),
                        tbar: {
                            xtype: 'toolbar',
                            items: [{
                                    xtype: 'button',
                                    text: App.Language.General.ddelete,
                                    hidden: (((App.Security.Actions[3005] === undefined) || (App.Interface.permits === false)) ? true : false),
                                    iconCls: 'delete_icon',
                                    handler: function (b) {
                                        grid = b.ownerCt.ownerCt;

                                        if (grid.getSelectionModel().getCount()) {
                                            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.are_you_sure_you_want_to_delete, function (b) {
                                                if (b == 'yes') {
                                                    records = grid.getSelectionModel().getSelections();
                                                    aux = new Array();
                                                    for (var i = 0; i < records.length; i++) {
                                                        aux.push(records[i].data.plan_id);
                                                    }
                                                    App.Plan.DeleteVersion(aux.join(','), function () {
                                                        Ext.getCmp('App.Plan.PrincipalVersion').fireEvent('beforerender', Ext.getCmp('App.Plan.PrincipalVersion'));
                                                        Ext.getCmp('App.Plan.AllVersionsGridAll').fireEvent('beforerender', Ext.getCmp('App.Plan.AllVersionsGridAll'));
                                                    });

                                                    App.Plan.Store.load
                                                            ({
                                                                callback: function ()
                                                                {
                                                                    App.Plan.Store.AllVersions.load();
                                                                }
                                                            });
                                                }
                                            });
                                        } else {
                                            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
                                        }
                                    }
                                }]
                        }
                    }],
                buttons: [{
                        xtype: 'button',
                        text: App.Language.General.close,
                        handler: function (b) {
                            b.ownerCt.ownerCt.ownerCt.close();
                        }
                    }]
            }];
        App.Plan.Version.Window.superclass.initComponent.call(this);
    }
});

App.Plan.editionNewVersion = function (record) {
    w = new App.Plan.addPlanWindow({
        title: App.Language.Plan.edit_version_plan_title
    });
    w.form.saveButton.setText(App.Language.General.edit);
    w.form.record = record;
    w.form.categoria.hideLabel = true;
    w.form.categoria.hide();
    w.form.archivo.setDisabled(true);
    w.form.saveButton.handler = function () {
        form = w.form.getForm();
        if (form.isValid()) {
            form.updateRecord(w.form.record);
            w.close();
            App.Plan.Version.Store.load();
        }
    };
    w.form.getForm().loadRecord(record);
    w.show();
}

App.Plan.layerSelector = Ext.extend(Ext.Window, {
    title: App.Language.General.layers,
    width: (screen.width < 450) ? screen.width - 50 : 450,
    height: 450,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    tbar: [{
            text: App.Language.Plan.show_all,
            handler: function () {
                App.Plan.planLayers.each(function (record) {
                    record.set('layer_status', 1);
                });
            }
        }, {
            xtype: 'spacer',
            width: 5
        }, {
            text: App.Language.Plan.hide_all,
            handler: function () {
                App.Plan.planLayers.each(function (record) {
                    record.set('layer_status', 0);
                });
            }
        }],
    initComponent: function () {
        this.checkColumn = new Ext.grid.CheckColumn({
            header: App.Language.Plan.visible_sign,
            dataIndex: 'layer_status',
            width: 55
        });
        this.items = [{
                xtype: 'editorgrid',
                ref: 'grid',
                store: App.Plan.planLayers,
                loadMask: true,
                viewConfig: {
                    forceFit: true
                },
                plugins: this.checkColumn,
                columns: [{
                        dataIndex: 'layer_name',
                        header: App.Language.Plan.layer,
                        sortable: true,
                        width: 200
                    }, this.checkColumn]
            }];
        this.fbar = [{
                text: App.Language.General.close,
                handler: function (b) {
                    b.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.Plan.apply,
                handler: function (b) {
                    b.ownerCt.ownerCt.grid.getStore().each(function (record) {
                        updateVisibility(record.data.layer_id, record.data.layer_status)
                    });
                    b.ownerCt.ownerCt.hide();
                }
            }]
        App.Plan.layerSelector.superclass.initComponent.call(this);
    }
});

App.Plan.loadVersionPlan = function (plan_id, plan_category_id) {


    record = Ext.getCmp('App.Plan.Principal').get(plan_category_id).combo_version.getStore().getById(plan_id);

    if (Ext.getCmp('App.Plan.Principal').get(plan_category_id).plan_category_id != 4) {
        Ext.getCmp('App.Plan.Principal').get(plan_category_id).tpl.overwrite(Ext.getCmp('App.Plan.Principal').get(plan_category_id).body, {
            plan_filename: record.data.plan_filename,
            plan_id: record.data.plan_id
        });

        var id = record.data.plan_id;
        var msg = '';

        if (id) {
            var idElement = 'plan_embed_' + id;

            var embElement = document.getElementById(idElement);
            var attributeEmb = embElement.getAttribute("src");

            if (attributeEmb != 'plans/not_image_icon.png') {
                jQuery('#' + idElement).removeClass('no_img');
                embElement.addEventListener("load", function () {

                    setTimeout(function () {
                        jQuery('.print_icon').show();
                        jQuery('.controls').show();
                        if (msg) {
                            msg.hide();
                        }
                    }, 1000);
                }, false);
            } else {
                jQuery('#' + idElement).addClass('no_img');

            }
            if (embElement != null) {
                if (App.Plan.idPlan.indexOf(idElement) === -1) {
//                            msg = Ext.MessageBox.wait("", "Cargando...");
                }
            }
            zomm_m(id, msg);

        }


        App.Plan.loadSectionCombo(plan_id, plan_category_id);
        cleanAll();

    } else {

        App.Plan.loadBim(plan_id, plan_category_id);
    }

    App.Plan.CurrentPlanId = record.data.plan_id;


}


App.Plan.loadBim = function (plan_id, plan_category_id) {

    if (plan_category_id == 4) {
        App.Plan.Section.StoreBimVersion.setBaseParam('plan_id', plan_id);
        App.Plan.Section.StoreBimVersion.load({
            callback: function () {

                App.Plan.Section.StoreBimVersion.each(function (record) {

                    if (record.data.url.length > 0) {

                        Ext.getCmp('App.Plan.Principal').get(plan_category_id).tpl.overwrite(Ext.getCmp('App.Plan.Principal').get(plan_category_id).body, {
                            urlBim: record.data.url,
                            plan_id: plan_id

                        });

                    }

                });
            }
        });
    }
}


App.Plan.loadSectionCombo = function (plan_id, plan_category_id) {
    if (Ext.getCmp('App.Plan.Principal').get(plan_category_id)) {
        App.Plan.Section.StoreFiltered.setBaseParam('plan_id', plan_id);
        App.Plan.Section.StoreFiltered.load({
            callback: function () {
                aux = [];
                aux.push('<option value="">' + App.Language.General.selection + '</option>');
                aux.push('<option value="all_sections">' + App.Language.General.all + '</option>');
                App.Plan.Section.StoreFiltered.each(function (record) {
                    aux.push('<option value="' + record.data.plan_section_color + '">' + record.data.plan_section_name + '</option>');
                });
                var t = new Ext.XTemplate('<select onchange="App.Plan.fillSection(this);">' +
                        aux.join('') +
                        '</select>');
                if (Ext.getCmp('App.Plan.Principal').get(plan_category_id) && Ext.getCmp('App.Plan.Principal').get(plan_category_id).combo_section.el) {
                    t.overwrite(Ext.getCmp('App.Plan.Principal').get(plan_category_id).combo_section.el);
                }
            }
        });
    }
}

App.Plan.fillSection = function (cb) {

    if (cb.options[cb.selectedIndex].text) {
        if (cb.value == 'all_sections') {
            for (var i = 2; i < cb.options.length; i++) {

                changeFill(cb.options[i].text, cb.options[i].value, false);
            }
        } else {
            changeFill(cb.options[cb.selectedIndex].text, cb.value);
        }
    } else {
        changeFill('');
    }
}

App.Plan.ViewConfigWindow = Ext.extend(Ext.Window, {
    title: App.Language.Plan.view_settings,
    width: 220,
    height: 220,
    modal: true,
    resizable: false,
    layout: 'fit',
    padding: 1,
    listeners: {
        'show': function (w) {
            w.form.color.setValue(App.Plan.ViewConfig.Color);
            w.form.line_width.setValue(App.Plan.ViewConfig.Width);
        }
    },
    initComponent: function () {
        this.items = [{
                xtype: 'form',
                labelAlign: 'top',
                ref: 'form',
                padding: 5,
                items: [{
                        xtype: 'colorpaletteField',
                        ref: 'color',
                        fieldLabel: App.Language.Plan.color,
                        name: 'color',
                        listeners: {
                            'select': function (f) {
                                App.Plan.ViewConfig.Color = f.getValue();
                                dchangeStroke(lastobj, true)
                            }
                        }
                    }, {
                        xtype: 'spinnerfield',
                        ref: 'line_width',
                        fieldLabel: App.Language.Plan.line_width,
                        width: 50,
                        minValue: 1,
                        maxValue: 20,
                        allowDecimals: false,
                        incrementValue: 1,
                        alternateIncrementValue: 5,
                        value: 5,
                        accelerate: true,
                        listeners: {
                            'spinchange': function (o) {
                                App.Plan.ViewConfig.Width = o.field.getValue();
                                dchangeStroke(lastobj, true)
                            }
                        }
                    }]
            }];
        this.fbar = [{
                text: App.Language.General.close,
                handler: function (b) {
                    b.ownerCt.ownerCt.hide();
                }
            }, {
                text: App.Language.Plan.apply,
                handler: function (b) {
                    App.Plan.ViewConfig.Color = b.ownerCt.ownerCt.form.color.getValue();
                    App.Plan.ViewConfig.Width = b.ownerCt.ownerCt.form.line_width.getValue();
                    dchangeStroke(lastobj, true)
                    b.ownerCt.ownerCt.hide();
                }
            }]
        App.Plan.ViewConfigWindow.superclass.initComponent.call(this);
    }
});

App.Plan.sectionWindow = Ext.extend(Ext.Window, {
    title: App.Language.General.section,
    width: (screen.width < 450) ? screen.width - 50 : 450,
    height: 450,
    layout: 'fit',
    padding: 1,
    modal: true,
    resizable: false,
    tbar: [{
            text: App.Language.Plan.show_all,
            handler: function () {
                Ext.Ajax.request({
                    url: 'index.php/plan/section/updateAll',
                    params: {
                        plan_id: App.Plan.Section.Store.baseParams.plan_id,
                        plan_section_status: 1
                    },
                    success: function (response) {
                        App.Plan.Section.Store.load();
                    }
                });
            }
        }, {
            xtype: 'spacer',
            width: 5
        }, {
            text: App.Language.Plan.hide_all,
            handler: function () {
                Ext.Ajax.request({
                    url: 'index.php/plan/section/updateAll',
                    params: {
                        plan_id: App.Plan.Section.Store.baseParams.plan_id,
                        plan_section_status: 0
                    },
                    success: function (response) {
                        App.Plan.Section.Store.load();
                    }
                });
            }
        }],
    initComponent: function () {
        this.checkColumn = new Ext.grid.CheckColumn({
            header: App.Language.Plan.visible_sign,
            dataIndex: 'plan_section_status',
            width: 50
        });
        this.items = [{
                xtype: 'editorgrid',
                clicksToEdit: 1,
                store: App.Plan.Section.Store,
                loadMask: true,
                viewConfig: {
                    forceFit: true
                },
                plugins: this.checkColumn,
                columns: [{
                        dataIndex: 'plan_section_name',
                        header: App.Language.Plan.layer,
                        sortable: true,
                        editable: false
                    }, this.checkColumn, {
                        dataIndex: 'plan_section_color',
                        header: App.Language.Plan.color,
                        width: 50,
                        sortable: true,
                        editor: new Ext.ux.form.ColorPaletteField({
                            colorizeFieldBackgroud: false,
                            colorizeFieldText: false,
                            colorizeTrigger: false,
                            editable: false
                        }),
                        renderer: function (v, metaData) {
                            metaData.attr = 'style="background-color:#' + v + ';"';
                            return '';
                        }
                    }]
            }];
        this.fbar = [{
                text: App.Language.General.close,
                handler: function (b) {
                    b.ownerCt.ownerCt.hide();
                }
            }]
        App.Plan.sectionWindow.superclass.initComponent.call(this);
    }
});

App.Plan.sectionVistaSeleccionWindow = Ext.extend(Ext.Window,
        {
            title: App.Language.Plan.view_selected,
            width: (screen.width < 750) ? screen.width - 50 : 750,
            height: 500,
            layout: 'fit',
            padding: 1,
            modal: true,
            resizable: false,
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'tabpanel',
                                ref: 'tabpanel',
                                activeTab: 0,
                                border: false,
                                enableTabScroll: true,
                                padding: 1,
                                items:
                                        [{
                                                xtype: 'panel',
                                                title: App.Language.Plan.select_layers,
                                                items:
                                                        [{
                                                                xtype: 'grid',
                                                                id: 'App.Plan.VistaSeleccion.Grid',
                                                                margins: '5 5 5 5',
                                                                height: 400,
                                                                loadMask: true,
                                                                region: 'center',
                                                                viewConfig:
                                                                        {
                                                                            forceFit: true
                                                                        },
                                                                store: App.Plan.Section.StoreFiltered,
                                                                columns: [new Ext.grid.CheckboxSelectionModel(),
                                                                    {
                                                                        dataIndex: 'plan_section_name',
                                                                        header: App.Language.Plan.layer,
                                                                        width: 100,
                                                                        sortable: true
                                                                    }, {
                                                                        dataIndex: 'infra_info_usable_area',
                                                                        header: App.Language.Infrastructure.infra_info_usable_area + " (M2)",
                                                                        width: 80,
                                                                        aling: 'center',
                                                                        sortable: true
                                                                    }, {
                                                                        dataIndex: 'infra_info_usable_area_total',
                                                                        header: App.Language.Infrastructure.infra_info_usable_area_total + " (M2)",
                                                                        width: 90,
                                                                        aling: 'center',
                                                                        sortable: true
                                                                    }, {
                                                                        dataIndex: 'infra_info_usable_area_total_p',
                                                                        header: App.Language.Infrastructure.load_factor_percentage,
                                                                        width: 200,
                                                                        aling: 'center',
                                                                        sortable: true

                                                                    }],
                                                                sm: new Ext.grid.CheckboxSelectionModel()
                                                            }],
                                                fbar:
                                                        [{
                                                                text: App.Language.Plan.print_summary,
                                                                handler: function (b) {
                                                                    grid = Ext.getCmp('App.Plan.VistaSeleccion.Grid');
//                                                                    console.log('>> grid: ', grid);
//                                                                     console.log('>> grid.count: ', grid.getSelectionModel().getCount());
//                                                                    
                                                                    if (grid.getSelectionModel().getCount())
                                                                    {
                                                                        //                            changeFill(0, 0, true);
                                                                        records = grid.getSelectionModel().getSelections();
                                                                        aux = new Array();
                                                                        for (var i = 0; i < records.length; i++)
                                                                        {
                                                                            aux.push(records[i].data.plan_section_id);
                                                                            changeFill(records[i].data.plan_section_name, records[i].data.plan_section_color, false);
                                                                        }
                                                                        plan_section_id = (aux.join('.'));
//                                                                        console.log('plan_section_id: ', plan_section_id);
                                                                        document.location = 'index.php/plan/section/getgraphCompleto/' + plan_section_id + "-" + App.Plan.CurrentPlanId;

                                                                    } else {
                                                                        Ext.FlashMessage.alert(App.Language.Plan.you_must_have_applied_for_vista_print_summary);
                                                                    }
                                                                }
                                                            }, {
                                                                text: App.Language.Plan.apply_vista,
                                                                handler: function (b) {
                                                                    grid = Ext.getCmp('App.Plan.VistaSeleccion.Grid');
                                                                    if (grid.getSelectionModel().getCount())
                                                                    {
                                                                        changeFill(0, 0, true);
                                                                        records = grid.getSelectionModel().getSelections();
                                                                        aux = new Array();
                                                                        for (var i = 0; i < records.length; i++)
                                                                        {
                                                                            aux.push(records[i].data.plan_section_id);
                                                                            changeFill(records[i].data.plan_section_name, records[i].data.plan_section_color, false);
                                                                        }
                                                                        plan_section_id = (aux.join(','));
                                                                        Ext.Ajax.request({
                                                                            url: 'index.php/plan/section/getSumTotal',
                                                                            params: {
                                                                                plan_section_ids: plan_section_id,
                                                                                plan_id: App.Plan.CurrentPlanId
                                                                            },
                                                                            success: function (response) {
                                                                                if (Ext.getCmp('App.Plan.RelacionNodo.Grid')) {
                                                                                    Ext.getCmp('App.Plan.RelacionNodo.Grid').fireEvent('beforerender', Ext.getCmp('App.Plan.RelacionNodo.Grid'));
                                                                                }

                                                                                response = Ext.decode(response.responseText);
                                                                                record = response.results;
                                                                                NodeIds = record.node_name;
                                                                                Ext.getCmp('App.Plan.ruta').setValue(record.ruta);
                                                                                Ext.getCmp('App.Plan.version').setValue(App.Plan.PlanVersion);
                                                                                Ext.getCmp('App.Plan.plan_datetime_formated').setValue(App.Plan.PlanDateTime);
                                                                                Ext.getCmp('App.Plan.infra_info_usable_area_total').setValue(record.infra_info_usable_area_total + " (M2)");
                                                                                Ext.getCmp('App.Plan.infra_info_usable_area_total_p').setValue(record.infra_info_usable_area_total_p + " %");
                                                                                Ext.getCmp('App.Plan.infra_info_usable_area').setValue(record.infra_info_usable_area + "(M2)");
                                                                                App.Plan.Section.StoreFiltered.load({params: {plan_id: App.Plan.CurrentPlanId, plan_section_ids: plan_section_id}});
                                                                            }
                                                                        });
                                                                    } else {
                                                                        Ext.FlashMessage.alert(App.Language.Plan.you_must_check_one_of_the_layers_to_display_selected_view);
                                                                    }
                                                                }
                                                            }, {
                                                                text: App.Language.General.close,
                                                                handler: function (b)
                                                                {
                                                                    App.Plan.Section.StoreFiltered.load({params: {plan_id: App.Plan.CurrentPlanId, plan_section_ids: 0}});
                                                                    NodeIds = 0;
                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                }
                                                            }]
                                            }, {
                                                xtype: 'panel',
                                                title: App.Language.Plan.nodes_relationship_layers,
                                                items:
                                                        [{
                                                                xtype: 'grid',
                                                                id: 'App.Plan.RelacionNodo.Grid',
                                                                margins: '5 5 5 5',
                                                                height: 400,
                                                                loadMask: true,

                                                                listeners: {
                                                                    'beforerender': function ()
                                                                    {
                                                                        Ext.getCmp('App.Plan.RelacionNodo.Grid').removeAll(true);
                                                                        App.Plan.PlanNode.Store.baseParams = {};
                                                                        App.Plan.PlanNode.Store.setBaseParam('plan_id', App.Plan.CurrentPlanId);
                                                                        App.Plan.PlanNode.Store.load();
                                                                    },
                                                                    'rowdblclick': function (grid, rowIndex) {
                                                                        record = grid.getStore().getAt(rowIndex);
                                                                        App.Plan.SelectedLinkNodeId = record.data.node_id;
                                                                        pnps = new App.Plan.AddToNodePlanSeccionForm();
                                                                        pnps.show();
//                                                                        image(grid.getStore().getAt(rowIndex).data.doc_version_filename, grid.getStore().getAt(rowIndex).data.doc_image_web);
                                                                    }
                                                                },
                                                                viewConfig:
                                                                        {
                                                                            forceFit: true,
                                                                            getRowClass: function (record, index)
                                                                            {
                                                                                node_id = record.get('node_id');
                                                                                for (var i = 0; i < NodeIds.length; i++) {
                                                                                    if (node_id == NodeIds[i])
                                                                                    {
                                                                                        return 'green-ligth';
                                                                                    }
                                                                                }
                                                                            }
                                                                        },
                                                                store: App.Plan.PlanNode.Store,
                                                                columns: [new Ext.grid.CheckboxSelectionModel(),
                                                                    {
                                                                        dataIndex: 'node_name',
                                                                        header: App.Language.General.venue_name,
                                                                        sortable: true
                                                                    }, {
                                                                        dataIndex: 'plan_section_name',
                                                                        header: App.Language.Plan.layer,
                                                                        sortable: true
                                                                    }, {
                                                                        dataIndex: 'handler',
                                                                        header: App.Language.Plan.linkeado,
                                                                        align: 'center',
                                                                        sortable: true,
                                                                        renderer: function (value, metaData, record) {
                                                                            if (record.data.handler == "" || record.data.handler == null) {
                                                                                return "";
                                                                            } else {
                                                                                return "Si";
                                                                            }

                                                                        }
                                                                    }],
                                                                sm: new Ext.grid.CheckboxSelectionModel()
                                                            }],
                                                fbar:
                                                        [{
                                                                text: App.Language.General.close,
                                                                handler: function (b)
                                                                {
                                                                    App.Plan.Section.StoreFiltered.load({params: {plan_id: App.Plan.CurrentPlanId, plan_section_ids: 0}});
                                                                    NodeIds = 0;
                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                }
                                                            }]
                                            }, {
                                                xtype: 'form',
                                                ref: 'form',
                                                title: App.Language.Plan.detail_spaces,
                                                margins: '5 5 5 5',
                                                border: true,
                                                height: 400,
                                                plugins: [new Ext.ux.OOSubmit()],
                                                style: 'padding: 10 10 10 10',
                                                labelWidth: 300,
                                                padding: 10,
                                                items:
                                                        [{
                                                                xtype: 'displayfield',
                                                                fieldLabel: App.Language.General.trade_route,
                                                                id: 'App.Plan.ruta',
                                                                name: 'ruta',
                                                                anchor: '100%'
                                                            }, {
                                                                xtype: 'spacer',
                                                                height: 5
                                                            }, {
                                                                xtype: 'displayfield',
                                                                fieldLabel: App.Language.Plan.version_plano,
                                                                id: 'App.Plan.version',
                                                                name: 'plan_filename',
                                                                anchor: '100%'
                                                            }, {
                                                                xtype: 'spacer',
                                                                height: 5
                                                            }, {
                                                                xtype: 'displayfield',
                                                                fieldLabel: App.Language.Plan.upload_date,
                                                                id: 'App.Plan.plan_datetime_formated',
                                                                name: 'plan_filename',
                                                                anchor: '100%'
                                                            }, {
                                                                xtype: 'spacer',
                                                                height: 30
                                                            }, {
                                                                xtype: 'displayfield',
                                                                fieldLabel: App.Language.Infrastructure.infra_info_usable_area_total + ' (100%)',
                                                                id: 'App.Plan.infra_info_usable_area_total',
                                                                name: 'infra_info_usable_area_total',
                                                                anchor: '100%'
                                                            }, {
                                                                xtype: 'spacer',
                                                                height: 5
                                                            }, {
                                                                xtype: 'displayfield',
                                                                fieldLabel: App.Language.Infrastructure.viewed_m2_selected,
                                                                id: 'App.Plan.infra_info_usable_area',
                                                                name: 'infra_info_usable_area',
                                                                anchor: '100%'
                                                            }, {
                                                                xtype: 'spacer',
                                                                height: 5
                                                            }, {
                                                                xtype: 'displayfield',
                                                                fieldLabel: App.Language.Infrastructure.load_factor_percentage,
                                                                id: 'App.Plan.infra_info_usable_area_total_p',
                                                                name: 'infra_info_usable_area_total_p',
                                                                anchor: '100%'
                                                            }],
                                                fbar:
                                                        [{
                                                                text: App.Language.General.close,
                                                                handler: function (b)
                                                                {
                                                                    App.Plan.Section.StoreFiltered.load({params: {plan_id: App.Plan.CurrentPlanId, plan_section_ids: 0}});
                                                                    NodeIds = 0;
                                                                    b.ownerCt.ownerCt.ownerCt.ownerCt.close();
                                                                }
                                                            }]
                                            }]
                            }];
                App.Plan.sectionVistaSeleccionWindow.superclass.initComponent.call(this);
            }
        });

App.Plan.AddToNodePlanSeccionForm = Ext.extend(Ext.Window,
        {
            title: App.Language.Plan.income_layer,
            width: 350,
            height: 160,
            layout: 'fit',
            modal: true,
            resizable: false,
            padding: 1,
            initComponent: function ()
            {
                this.items =
                        [{
                                xtype: 'form',
                                labelWidth: 130,
                                padding: 5,
                                items:
                                        [{
                                                xtype: 'combo',
                                                fieldLabel: App.Language.Plan.income_layer,
                                                anchor: '90%',
                                                id: 'App.Plan.Seccion.Id',
                                                store: App.Plan.Section.Store,
                                                hiddenName: 'plan_section_id',
                                                triggerAction: 'all',
                                                displayField: 'plan_section_name',
                                                valueField: 'plan_section_id',
                                                editable: true,
                                                typeAhead: true,
                                                selectOnFocus: true,
                                                forceSelection: true
                                            }],
                                buttons:
                                        [{
                                                xtype: 'button',
                                                text: App.Language.General.close,
                                                handler: function (b)
                                                {
                                                    b.ownerCt.ownerCt.ownerCt.close();
                                                }
                                            }, {
                                                xtype: 'button',
                                                text: App.Language.General.add,
                                                handler: function (b)
                                                {
                                                    if (Ext.getCmp('App.Plan.Seccion.Id').getValue() == '' || Ext.getCmp('App.Plan.Seccion.Id').getValue() == null) {
                                                        Ext.FlashMessage.alert(App.Language.Plan.you_must_select_a_layer_to_relate);

                                                    } else {
                                                        App.Plan.saveHandlerForm(App.Plan.SelectedLinkNodeId, App.Plan.CurrentPlanId, Ext.getCmp('App.Plan.Seccion.Id').getValue(), function () {
                                                            dchangeStroke('');
                                                            Ext.FlashMessage.alert(App.Language.Plan.successfully_associated_lines);
                                                        });

                                                        Ext.getCmp('App.Plan.RelacionNodo.Grid').fireEvent('beforerender', Ext.getCmp('App.Plan.RelacionNodo.Grid'));
                                                        b.ownerCt.ownerCt.ownerCt.close();
                                                    }
                                                }
                                            }]
                            }];
                App.Plan.AddToNodePlanSeccionForm.superclass.initComponent.call(this);
            }
        });

