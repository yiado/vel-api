Ext.namespace('App.Iot.Store');

App.Iot.Device.Store = new Ext.data.JsonStore({
    proxy: new Ext.data.HttpProxy({
        api: {
            read: 'index.php/iot/iot/getDeviceInfo',

        },
        listeners: {
            'exception': function(DataProxy, type, action, options, response, arg) {
                if (type == 'remote') {
                    Ext.MessageBox.alert(App.Language.General.oops, response.raw.msg);
                }
            }
        }
    }),
    writer: new Ext.data.JsonWriter({
        encode: true,
        writeAllFields: true,
        encodeDelete: true
    }),
    root: 'results',
    totalProperty: 'total',
    idProperty: 'iot_device_info',
    fields: [

        {
            name: 'manufacterName',
            mapping: 'node.manufacterName'
        },
        {
            name: 'modelName',
            mapping: 'node.modelName'
        },

        {
            name: 'description',
            mapping: 'node.description'
        }
    ]
});

App.Iot.Sensors.Store = new Ext.data.JsonStore({
    url: 'index.php/iot/iot/getSensorsInfo',
    root: 'results',
    idProperty: 'iot_sensors_info',
    fields: [
        'data',
        'unit',
        {
            name: 'created_at',
            convert: function(value) {

                var date_value = value.split("T");

                if (date_value[1] != 'undefined') {
                    date_value[1] = date_value[1].split(".");
                    value = date_value[0] + ' ' + date_value[1][0];
                }


                //                                If you need to quickly format your date using plain JavaScript, use getDate, getMonth + 1, getFullYear, getHours and getMinutes:

                var date = new Date(value);
                var d = date.getDate();
                var m = date.getMonth() + 1; //Month from 0 to 11
                var y = date.getFullYear();
                var h = date.getHours();
                var min = date.getMinutes();
                var s = date.getSeconds();
                return '' + (d <= 9 ? '0' + d : d) + '/' + (m <= 9 ? '0' + m : m) + '/' + y + ' ' + (h <= 9 ? '0' + h : h) + ':' + (min <= 9 ? '0' + min : min) + ':' + (s <= 9 ? '0' + s : s);

                //                               
            }
        },
        'sensor_name'
    ]
});