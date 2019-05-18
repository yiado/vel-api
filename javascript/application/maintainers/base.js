Ext.Ajax.disableCaching = false;
Ext.namespace('App.Maintainers');
App.Maintainers.NameSpaces = 
[
    'Infrastructure', 
    'Document', 
    'General', 
    'Planimetry', 
    'Assets', 
    'Permission', 
    'Users', 
    'Language', 
    'Groups', 
    'Maintenance', 
    'Report', 
    'Request',
    'Costs',
    'InfraMtn'
];

App.General.declareNameSpaces('App.Maintainers', App.Maintainers.NameSpaces);
App.Maintainers.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Maintenance.maintenance
    });
}

App.Maintainers.Infrastructure.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Infrastructure.infrastructure
    });
}

App.Maintainers.Planimetry.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Plan.planimetry
    });
}

App.Maintainers.Assets.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Asset.assets
    });
}

App.Maintainers.Document.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.General.documents
    });
}

App.Maintainers.General.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.General.general
    });
}

App.Maintainers.Users.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Core.users
    });
}

App.Maintainers.Language.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Core.language
    });
}

App.Maintainers.Maintenance.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Maintenance.maintenance
    });
}

App.Maintainers.Report.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Report.reports
    });
}

App.Maintainers.Request.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Request.requests
    });
}

App.Maintainers.Costs.moduleActivate = function()
{
    return new Ext.Panel
    ({
        border: false,
        title: App.Language.Costs.costs
    });
}
