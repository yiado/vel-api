/* global Ext, App */

Ext.namespace('App.StructureTree');

App.StructureTree.NameSpaces = [
    'Tree',
    'Tree.XML',
    'Tree.JSON'
];

App.General.declareNameSpaces('App.StructureTree', App.StructureTree.NameSpaces);

App.StructureTree.Tree.XML = Ext.extend(Ext.tree.TreePanel, {
    xtype: 'treepanel',
    autoScroll: true,
    rootVisible: false,
    initComponent: function() {
        this.root = new Ext.tree.AsyncTreeNode({
            text: App.Language.Core.root_node,
            id: 'root'
        });
        this.loader = new Ext.ux.tree.XmlTreeLoader({
            dataUrl: App.Security.Session.xml_permissions_file,
            processAttributes: function(attr) {
                // configuracion del texto del nodo
                attr.text = attr.name;
                // configuracion del icono del nodo
                attr.iconCls = attr.icon;
                // configuraciones de estado delnodo
                attr.loaded = true;
                attr.expanded = false;
            }
        });
        App.StructureTree.Tree.XML.superclass.initComponent.call(this);
        jQuery('.x-tree-ec-icon.x-tree-elbow-end-plus').trigger('click');
    }
});

App.StructureTree.Tree.XML.expanddeep = function(node_id, treeNode, children) {

    for (var i = 0; i < children.length; i++) {

        if (children[i].children) {
            Ext.getCmp('App.StructureTree.Tree').getNodeById(children[i].id).expand();
            App.StructureTree.Tree.XML.expanddeep(node_id, treeNode, children[i].children);
        }

        if (children[i].id == node_id) {
            Ext.getCmp('App.StructureTree.Tree').getSelectionModel().select(Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id));
            Ext.getCmp('App.StructureTree.Tree').fireEvent('click', Ext.getCmp('App.StructureTree.Tree').getNodeById(node_id));
        }

    }
};

App.StructureTree.Tree.JSON = Ext.extend(Ext.tree.TreePanel, {
    xtype: 'treepanel',
    autoScroll: true,
    rootVisible: false,
    enableDD: false,
    containerScroll: true,

    initComponent: function() {
        this.root = {
            nodeType: 'async',
            text: App.Language.Core.root_node,
            id: 'root'
        };
        this.loader = {
            dataUrl: 'index.php/core/nodecontroller/expand',
            preloadChildren: true,
            collapseFirst: false
        };
        App.StructureTree.Tree.JSON.superclass.initComponent.call(this);
        jQuery('.x-tree-ec-icon.x-tree-elbow-end-plus').trigger('click');
    }
});

Ext.reg('apptree.XML', App.StructureTree.Tree.XML);
Ext.reg('apptree.JSON', App.StructureTree.Tree.JSON);

App.StructureTree.Tree.getUserTree = function() {
    if (App.Security.Session.user_type == 'A' || App.Security.Session.user_tree_full == 1) {
        return 'apptree.JSON';
    } else {
        return 'apptree.XML';
    }
};