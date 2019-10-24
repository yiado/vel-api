/* global Ext, App */

Ext.namespace('App.Language');
App.Language.Tags = new Array();

App.Language.tag = function(module, tag) {
    alert(eval('App.Language.' + module + '.' + tag));
};