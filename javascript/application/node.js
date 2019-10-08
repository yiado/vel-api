/* global Ext, App */

Ext.namespace('App.Node');

App.Node.CopyProxy = function(node_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/core/nodecontroller/edit',
        params: {
            node_id: node_id,
            action: 'copy'
        },
        success: successCallBack
    });
};

App.Node.CutProxy = function(node_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/core/nodecontroller/edit',
        params: {
            node_id: node_id,
            action: 'cut'
        },
        success: successCallBack
    });
};

App.Node.PasteProxy = function(node_parent_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/core/nodecontroller/edit',
        params: {
            node_parent_id: node_parent_id,
            action: 'paste'
        },
        success: successCallBack
    });
};

App.Node.DeleteProxy = function(node_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/core/nodecontroller/edit',
        params: {
            node_id: node_id,
            action: 'delete'
        },
        success: successCallBack
    });
};

App.Node.MoveProxy = function(node_id, node_parent_id, successCallBack) {
    Ext.Ajax.request({
        url: 'index.php/core/nodecontroller/edit',
        params: {
            node_id: node_id,
            node_parent_id: node_parent_id,
            action: 'move'
        },
        success: successCallBack
    });
};