App.PermissionEnableSelect = false;

App.Maintainers.addToModuleMenu('permissions', {
    xtype: 'button',
    text: App.Language.Core.permissions,
    iconCls: 'permission_icon_32',
    scale: 'large',
    iconAlign: 'top',
    module: 'Permission'
});

App.Maintainers.Permission.Principal = Ext.extend(Ext.TabPanel, {
    activeTab: 0,
    border: false,
    initComponent: function() {
        this.items = [{
            xtype: 'form',
            layout: 'fit',
            title: App.Language.Core.access_tree,
            border: false,
            items: [{
                xtype: 'treegrid',
                ref: 'treeGridNodes',
                border: false,
                tbar: [{
                    xtype: 'label',
                    text: App.Language.Core.group
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    xtype: 'combo',
                    ref: '../comboGroups',
                    triggerAction: 'all',
                    fieldLabel: App.Language.Core.group,
                    hiddenName: 'user_group_id',
                    store: App.Core.Groups.Store,
                    displayField: 'user_group_name',
                    valueField: 'user_group_id',
                    mode: 'remote',
                    editable: true,
                    selecOnFocus: true,
                    typeAhead: true,
                    selectOnFocus: true,
                    minChars: 0,
                    allowBlank: false,
                    listeners: {
                        'select': function(cb, record) {

                            cb.ownerCt.ownerCt.getLoader().baseParams = {
                                user_group_id: record.data.user_group_id
                            };
                            cb.ownerCt.ownerCt.getLoader().load(cb.ownerCt.ownerCt.getRootNode());


                        }
                    }
                }, {
                    xtype: 'spacer',
                    width: 10
                }, {
                    text: App.Language.General.maintain_the_configuration,
                    iconCls: 'keep_add_icon',
                    tooltip: App.Language.General.leaving_the_existing_configuration_and_adding_the_change,

                    // hidden: true,
                    enableToggle: true,
                    toggleHandler: function(b, state) {
                        App.PermissionEnableSelect = state;
                    },
                    listeners: {
                        'show': function(b) {
                            if (App.PermissionEnableSelect) {
                                b.toggle(true);
                            }
                        }
                    }
                }, {
                    xtype: 'spacer',
                    width: 5
                }, {
                    text: App.Language.General.save,
                    iconCls: 'save_icon',
                    handler: function(b) {
                        form = b.ownerCt.ownerCt.ownerCt.getForm();
                        if (form.isValid()) {
                            b.setDisabled(true);

                            form.submit({
                                url: 'index.php/core/permissions/setTree',
                                params: {
                                    type_save: App.PermissionEnableSelect

                                },
                                waitTitle: App.Language.General.message_please_wait,
                                waitMsg: App.Language.General.message_guarding_information,
                                success: function(fp, o) {
                                    //Habilitar el boton
                                    b.setDisabled(false);

                                    Ext.FlashMessage.alert(o.result.msg);
                                },
                                failure: function(fp, o) {
                                    alert('Error:\n' + o.result.msg);
                                }
                            });
                        }
                    }
                }],
                loader: {
                    dataUrl: 'index.php/core/permissions/expand',
                    preloadChildren: true,
                    autoLoad: false
                },
                viewConfig: {
                    forceFit: true
                },
                columns: [{
                    dataIndex: 'text',
                    header: App.Language.General.name,
                    sortable: false,
                    width: 300
                }, {
                    dataIndex: 'node_type_category_name',
                    header: App.Language.General.category,
                    sortable: false,
                    width: 250
                }, {
                    dataIndex: 'node_type_name',
                    header: App.Language.General.type,
                    sortable: false,
                    width: 250
                }, {
                    header: App.Language.Core.level,
                    dataIndex: 'checked',
                    align: 'center',
                    tpl: new Ext.XTemplate('<input class="x-tree-node-cb" type="checkbox" name="nodes[]" value="{id}"', '<tpl if="checked_node == true">', ' checked', '</tpl>', '/>'),
                    width: 100
                }, {
                    header: App.Language.Core.branch,
                    dataIndex: 'checked',
                    align: 'center',
                    tpl: '<input class="x-tree-node-cb" type="checkbox" name="branches[]" value="{id}"/>',
                    width: 100
                }]
            }]
        }];
        App.Maintainers.Permission.Principal.superclass.initComponent.call(this);
    }
});