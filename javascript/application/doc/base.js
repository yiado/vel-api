Ext.namespace('App.Document');
App.General.declareNameSpaces('App.Document', 
[
    'Version',
    'Categoria',
    'Extension',
    'PrincipalClase',
    'Papelera',
    'Vencido',
    'FotoStandar',
    'CutProxy',
    'PasteProxy'
]);

App.Document.moduleActivate = function () 
{
    if (App.Interface.selectedNodeId > 0) 
    {
        App.Interface.ViewPort.displayModuleGui();
    } else {
        return new Ext.Panel({
            border: false, 
            title: App.Language.General.documents
        });
    }
}

App.ModuleActions[2008] = 
{
    text: App.Language.General.edit,
    iconCls: 'edit_icon',
    hidden: true,
    handler: function(b)
    {
        grid = Ext.getCmp('App.Document.GridDoc');

        if (grid === undefined) {
            //ENTRA CUANDO ES XTEMPLATE
            gallery = Ext.getCmp('App.Document.Gallery');
            records = gallery.getSelectedRecords();

            if (records.length >=1) 
            {
                Ext.getCmp('App.Document.EditCate').enable();
                b.menu.items.get(0).enable();
            }else {
//                Ext.getCmp('App.Document.EditCate').enable();
                b.menu.items.get(0).disable();
            }
        } else {//ENTRA CUANDO ES GRILLA
            checkCount = grid.getSelectionModel().getCount();
            for (i = 0; i < b.menu.items.length - 1; i++) 
            {
                if (checkCount) 
                {
//                    Ext.getCmp('App.Document.EditCate').enable();
                    b.menu.items.get(i).enable();
                }else {
//                    Ext.getCmp('App.Document.EditCate').enable();
                    b.menu.items.get(i).disable();
                }
            }
        }
    },
    menu: 
    [{
        text: 'Editar Categoría',
        iconCls: 'edit_icon',
        id:'App.Document.EditCate',
        handler: function()
        {
            grid = Ext.getCmp('App.Document.GridDoc');
            if (grid === undefined) {
                //ENTRA CUANDO ES XTEMPLATE
    
            } else {//ENTRA CUANDO ES GRILLA
                if (grid.getSelectionModel().getCount()) {
                    records = Ext.getCmp('App.Document.GridDoc').getSelectionModel().getSelections();
                    aux = new Array();
//                    App.InfraStructure.copiedNodes = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.doc_document_id);
//                        App.InfraStructure.copiedNodes.push(Ext.getCmp('App.StructureTree.Tree').getNodeById(records[i].data.node_id));
                    }
                    doc_document_id = (aux.join(','));
                    
                    if ( aux.length == 1 ){
                        w = new App.Document.updateCategoryWindow();
                        w.show();
                    } else {
                        Ext.FlashMessage.alert('Solo se puede cambiar 1 categoría a la vez');
                    }

//                    App.Document.CutProxy(doc_document_id, function(){});
                } else {
                    Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                }
            }
            
        }
    }, {
        text: App.Language.General.cut,
        iconCls: 'cut_icon',
        handler: function()
        {
            grid = Ext.getCmp('App.Document.GridDoc');
            if (grid === undefined) {
                //ENTRA CUANDO ES XTEMPLATE
                gallery = Ext.getCmp('App.Document.Gallery');
                records = gallery.getSelectedRecords();

                aux = new Array();
                App.InfraStructure.copiedNodes = new Array();
                for (var i = 0; i < records.length; i++) {
                    aux.push(records[i].data.doc_document_id);
                    App.InfraStructure.copiedNodes.push(Ext.getCmp('App.StructureTree.Tree').getNodeById(records[i].data.node_id));
                }
                doc_document_id = (aux.join(','));
                App.Document.CutProxy(doc_document_id, function(){});

            } else {//ENTRA CUANDO ES GRILLA
                if (grid.getSelectionModel().getCount()) {
                    records = Ext.getCmp('App.Document.GridDoc').getSelectionModel().getSelections();
                    aux = new Array();
                    App.InfraStructure.copiedNodes = new Array();
                    for (var i = 0; i < records.length; i++) {
                        aux.push(records[i].data.doc_document_id);
                        App.InfraStructure.copiedNodes.push(Ext.getCmp('App.StructureTree.Tree').getNodeById(records[i].data.node_id));
                    }
                    doc_document_id = (aux.join(','));

                    App.Document.CutProxy(doc_document_id, function(){});
                } else {
                    Ext.FlashMessage.alert(App.Language.General.you_must_select_at_least_one_record);
                }
            }
        }
    }, {
        text: App.Language.General.paste,
        iconCls: 'paste_icon',
        handler: function()
        {
            Ext.Ajax.request({
                waitMsg: App.Language.General.message_generating_file,
                url: 'index.php/doc/document/comprobacion',
                timeout: 10000000000,
                params: {
                    node_parent_id: App.Interface.selectedNodeId
                },
                success: function(response) {
                    response = Ext.decode(response.responseText);
                    if (response.success == 'true'){
                        Ext.MessageBox.confirm( App.Language.General.confirmation, App.Language.Document.documents_are_repeated + "<br>" + response.nombre + "<br>" + App.Language.Document.do_you_want_to_move_documents_that_are_not_repeated,
                        function (b) 
                        {
                            if (b == 'yes') 
                            {
                                App.Document.PasteProxy(App.Interface.selectedNodeId, function(response, opts)
                                {
                                    App.Document.Store.load();
                                    var obj1 = Ext.decode(response.responseText);
                                    Ext.FlashMessage.alert(obj1.msg);
                                });
                            }
                        });
                    } else {
                        App.Document.PasteProxy(App.Interface.selectedNodeId, function(response, opts)
                        {
                            App.Document.Store.load();
                            var obj2 = Ext.decode(response.responseText);
                            Ext.FlashMessage.alert(obj2.msg);
                        });
                    }
                },
                failure: function(response) {
                    Ext.MessageBox.alert(App.Language.General.error, App.Language.General.please_retry_general_error);
                }
            });
        }
    }]
};
	
App.ModuleActions[2001] = 
{
    xtype: 'splitbutton',
    text:  App.Language.General.add,
    hidden: true,
    iconCls: 'add_icon',
    menu: 
    [{
        text: App.Language.Document.bulk_upload_zip,  
        iconCls: 'masive_zip_icon',
        handler: function()
        {

            w = new App.Document.addMasiveDocumentWindow();
            w.show();
        }            
    }, {
        text: App.Language.Document.bulk_upload_zip_excel,
        iconCls: 'masive_zip_icon',
        handler: function()
        {                
            w = new App.Document.addMasiveZipExcelWindow();
            w.show();
        }
    }],
    handler: function () 
    {
        w = new App.Document.addDocumentWindow();
        w.show();
    }
}

App.ModuleActions[2002] = 
{
    text: App.Language.General.ddelete,
    hidden: true,
    iconCls: 'delete_icon',
    handler: function (b) 
    {
        grid = b.ownerCt.ownerCt.ownerCt.docsGridPapelera;
        if (grid.getSelectionModel().getCount()) 
        {
            Ext.MessageBox.confirm( App.Language.General.confirmation, App.Language.General.message_really_want_delete,
                function (b) 
                {
                    if (b == 'yes') 
                    {
                      
                        grid.getSelectionModel().each(function (record) 
                        {
                            App.Document.Papelera.Store.remove(record);
                        });
                    }
                });
        } else {
            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
        }
    }
}

App.ModuleActions[2003] = 
{
    text: App.Language.General.new_version,
    iconCls: 'copy_icon',
    hidden: true,
    handler: function () 
    {
        w = new App.Document.addVersionDocumentWindow();
        w.show();
    }
}

/*
  //La lógica de esta acción está en la function App.Document.editionNewVersion
  App.ModuleActions[2004] = {}

 */ 
	
App.ModuleActions[2005] = 
{
    text: App.Language.General.delete_version,
    iconCls: 'delete_icon',
    hidden: true,
    handler: function ( b ) 
    {
        grid = b.ownerCt.ownerCt;
        if (grid.getSelectionModel().getCount()) 
        {
            Ext.MessageBox.confirm(App.Language.General.confirmation, App.Language.General.message_really_want_delete,
                function(b)
                {
                    if (b == 'yes') 
                    {
                        grid.getSelectionModel().each(function (record) 
                        { 
                            App.Document.Version.Store.remove(record);
                        });
                    }
                    App.Document.Version.Store.load();
                });
        } else {
            Ext.FlashMessage.alert(App.Language.General.message_delete_items);
        }
    }
}

App.ModuleActions[2006] =
{
//    xtype: 'button',
    iconCls: 'document_icon_32',
    text: App.Language.General.documents,
    
//    scale: 'large',
//    iconAlign: 'top',
    module: 'Document'
}
 
App.ModuleActions[2007] =
{
    text: App.Language.Document.download_zip,
    iconCls: 'zip_icon',
    hidden: true,
    handler: function()
    {
        grid = Ext.getCmp('App.Document.GridDoc');
        if (grid.getSelectionModel().getCount()) 
        {
            records = Ext.getCmp('App.Document.GridDoc').getSelectionModel().getSelections();
            aux = new Array();
            for (var i = 0; i < records.length; i++) {
                aux.push(records[i].data.doc_document_id);
            }
            array_doc_document_id = aux.join('-');
            
            node_id = App.Interface.selectedNodeId;
            
            document.location = addDocZip + array_doc_document_id + '-' + node_id;

     
        } else {
            Ext.FlashMessage.alert(App.Language.Document.message_download_items);
        }
    }
}
