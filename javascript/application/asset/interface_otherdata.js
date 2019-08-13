App.Asset.OtrosDatos.Panel = Ext.extend(Ext.Panel, {
    title: App.Language.General.other_data,
    ref: 'otherdata',
    autoScroll: true,
    labelWidth: 150,
    padding: 5,
    plugins: [new Ext.ux.OOSubmit()]
});