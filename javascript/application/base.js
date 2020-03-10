Ext.Ajax.disableCaching = false;
Ext.namespace('App');
Ext.namespace('App.Localization');
Ext.namespace('App.General');
Ext.namespace('App.Core');
Ext.namespace('App.Core.MtnMaintainerType');
Ext.namespace('App.Core.MeasureUnit');
Ext.namespace('App.Core.ProviderType');
Ext.namespace('App.Core.ProviderTypeByNode');
Ext.namespace('App.Core.Provider');
Ext.namespace('App.Core.ProviderByType');
Ext.namespace('App.Core.ProviderTypeAll');
Ext.namespace('App.Core.ProviderByNode');
Ext.namespace('App.Core.User');
Ext.namespace('App.Core.UserNotification');
Ext.namespace('App.Core.UserFull');
Ext.namespace('App.Core.User.Groups');
Ext.namespace('App.Core.User.GroupsOutside');
Ext.namespace('App.Core.User.CheckAccessNode.Store');
Ext.namespace('App.Core.Groups');
Ext.namespace('App.Core.Groups.Users');
Ext.namespace('App.Core.Groups.UsersOutside');
Ext.namespace('App.Core.Groups.Permissions');
Ext.namespace('App.Core.Currency');
Ext.namespace('App.Core.Modules');
Ext.namespace('App.Core.Languages');
Ext.namespace('App.Core.LanguagesTag');
Ext.namespace('App.Core.Modules.Actions');
Ext.namespace('App.Core.Operators');
Ext.namespace('App.Core.Number');
Ext.namespace('App.BaseUrl');
Ext.namespace('App.Preferences.UsersWindow');
Ext.namespace('App.Help.HelpWindow');
Ext.namespace('App.Core.Preference');
Ext.namespace('App.Brand');
Ext.namespace('App.Core.Contract');
Ext.namespace('App.Core.ContractNode');
Ext.namespace('App.Core.ContractNodeAsociated');
Ext.namespace('App.Core.ContractAsset');
Ext.namespace('App.Core.ContractAssetAll');
Ext.namespace('App.Core.UserProvider');
Ext.namespace('App.Core.SpecialProvider');
Ext.namespace('App.Core.Log');
Ext.namespace('App.Core.LogDetail');
Ext.namespace('App.Core.TypeDescription');
Ext.namespace('App.Core.Help.Store');

App.GridLimitNumOT = 25;

App.GridLimit = 20;
App.GridLimitAsset = 30;
App.GridLimitTrasladados = 20;

function url_encode(str) {
    var hex_chars = "0123456789ABCDEF";
    var noEncode = /^([a-zA-Z0-9\_\-\.])$/;
    var n, strCode, hex1, hex2, strEncode = "";

    for (n = 0; n < str.length; n++) {
        if (noEncode.test(str.charAt(n))) {
            strEncode += str.charAt(n);
        } else {
            strCode = str.charCodeAt(n);
            hex1 = hex_chars.charAt(Math.floor(strCode / 16));
            hex2 = hex_chars.charAt(strCode % 16);
            strEncode += "%" + (hex1 + hex2);
        }
    }
    return strEncode;
}

App.UserModules = new Array();
App.UserModulesMenu = new Array();
App.ModuleSelect = '';
App.Module = '';

App.General.LoadMask = Ext.extend(Ext.LoadMask, {});

Ext.reg('apploadmask', App.General.LoadMask);

App.General.declareNameSpaces = function(parent, ns) {
    for (k in ns) {
        Ext.namespace(parent + '.' + ns[k]);
    }
};

App.General.DatPatterns = {
    SortableDateTime: "Y-m-d G:i:s",
    SortableDate: "Y-m-d",
    HumanDateTime: "d/m/Y H:i",
    HumanDate: "d/m/Y",
    HumanTime: "H:i"
};

App.Localization = {
    decimalSeparator: ',',
    decimalPrecision: 3,
    groupingSeparator: '',
    groupingSize: 3,
    currencySymbol: ''
};

App.General.DefaultDateFormat = App.General.DatPatterns.HumanDate;
App.General.DefaultDateTimeFormat = App.General.DatPatterns.HumanDateTime;

App.General.DefaultSystemDate = App.General.DatPatterns.SortableDate;
App.General.DefaultSystemDateTime = App.General.DatPatterns.SortableDateTime;

App.DateField = Ext.extend(Ext.form.DateField, {
    format: App.General.DefaultDateFormat,
    editable: false
});

Ext.reg('datefield', App.DateField);

App.NumberField = Ext.extend(Ext.form.NumberField,
    Ext.apply(App.Localization, {
        getSubmitValue: function() {
            var v = this.getValue();
            return v;
        }
    })
);

Ext.reg('numberfield', App.NumberField);

App.General.CurrencyFormatPatterns = {
    usMoney: "$0,000.00",
    clMoney: "$0.000/i"
};

App.General.DefaultSystemCurrencyFormatMoney = App.General.CurrencyFormatPatterns.clMoney;


function include(arr, obj) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] == obj)
            return true;
    }
}

Ext.apply(Ext.util.Format, {
    numberFormat: {},
    formatNumber: function(value, numberFormat) {
        if (value == '' || value == null) return '';
        var format = Ext.apply(Ext.apply({}, Ext.util.Format.numberFormat), numberFormat);
        if (typeof value !== 'number') {
            value = String(value);
            if (format.currencySymbol) {
                value = value.replace(format.currencySymbol, '');
            }
            if (format.groupingSeparator) {
                value = value.replace(new RegExp(format.groupingSeparator, 'g'), '');
            }
            if (format.decimalSeparator !== '.') {
                value = value.replace(format.decimalSeparator, '.');
            }
            value = parseFloat(value);
        }
        var neg = value < 0;
        value = Math.abs(value).toFixed(format.decimalPrecision);
        var i = value.indexOf('.');
        if (i >= 0) {
            if (format.decimalSeparator !== '.') {
                value = value.slice(0, i) + format.decimalSeparator + value.slice(i + 1);
            }
        } else {
            i = value.length;
        }
        if (format.groupingSeparator) {
            while (i > format.groupingSize) {
                i -= format.groupingSize;
                value = value.slice(0, i) + format.groupingSeparator + value.slice(i);
            }
        }
        if (format.currencySymbol) {
            value = format.currencySymbol + value;
        }
        if (neg) {
            value = '-' + value;
        }
        return value;
    }
});

Ext.namespace("Ext.ux");

/**
 * This submit action is basically the same as the normal submit action,
 * only that it uses the fields getSubmitValue() to compose the values to submit,
 * instead of looping over the input-tags in the form-tag of the form.
 *
 * To use it, just use the OOSubmit-plugin on either a FormPanel or a BasicForm,
 * or explicitly call form.doAction('oosubmit');
 *
 * @param {Object} form
 * @param {Object} options
 */
Ext.ux.OOSubmitAction = function(form, options) {
    Ext.ux.OOSubmitAction.superclass.constructor.call(this, form, options);
};

Ext.extend(Ext.ux.OOSubmitAction, Ext.form.Action.Submit, {
    /**
     * @cfg {boolean} clientValidation Determines whether a Form's fields are validated
     * in a final call to {@link Ext.form.BasicForm#isValid isValid} prior to submission.
     * Pass <tt>false</tt> in the Form's submit options to prevent this. If not defined, pre-submission field validation
     * is performed.
     */
    type: 'oosubmit',

    // private
    /**
     * This is nearly a copy of the original submit action run method
     */
    run: function() {
        var o = this.options;
        var method = this.getMethod();
        var isPost = method == 'POST';
        var params = this.options.params || {};
        if (isPost) Ext.applyIf(params, this.form.baseParams);

        //now add the form parameters
        this.form.items.each(function(field) {
            if (!field.disabled) {
                //check if the form item provides a specialized getSubmitValue() and use that if available
                if (typeof field.getSubmitValue == "function")
                    params[field.getName()] = field.getSubmitValue();
                else
                    params[field.getName()] = field.getValue();
            }
        });
        //convert params to get style if we are not post
        if (!isPost) params = Ext.urlEncode(params);

        if (o.clientValidation === false || this.form.isValid()) {
            if (this.form.fileUpload == true) {
                Ext.Ajax.request(Ext.apply(this.createCallback(o), {
                    form: this.form.el.dom,
                    url: this.getUrl(!isPost),
                    method: method,
                    params: params, //add our values
                    isUpload: this.form.fileUpload
                }));
            } else {
                Ext.Ajax.request(Ext.apply(this.createCallback(o), {
                    url: this.getUrl(!isPost),
                    method: method,
                    params: params, //add our values
                    isUpload: this.form.fileUpload
                }));
            }
        } else if (o.clientValidation !== false) { // client validation failed
            this.failureType = Ext.form.Action.CLIENT_INVALID;
            this.form.afterAction(this, false);
        }
    }
});

//add our action to the registry of known actions
Ext.form.Action.ACTION_TYPES['oosubmit'] = Ext.ux.OOSubmitAction;

/**
 * This plugin can be either used on BasicForm or FormPanel.
 * In both cases it changes the behaviour of submit() to use
 * the 'oosubmit' action instead of the 'submit' action.
 */
Ext.ux.OOSubmit = function() {
    this.init = function(_object) {
        var form = null;
        if (typeof _object.form == "object") { //we are a formpanel:
            form = _object.form;
        } else form = _object;

        //Save the old submit method:
        form.oldSubmit = form.submit;

        //create a new submit method which calls the oosubmit action per default:
        form.submit = function(options) {
            this.doAction('oosubmit', options);
            return this;
        };
    };
};

Ext.form.DateField.prototype.getSubmitValue = function() {
    var v = this.getValue();
    if (v !== '') {
        var date = new Date(v);
        return date.format("Y-m-d");
    }
    return v;
};

Ext.form.BasicForm.prototype.getSubmitValues = function(asString) {
    var fs = Ext.lib.Ajax.serializeForm(this.el.dom);
    if (asString === true) {
        return fs;
    }
    fields = Ext.urlDecode(fs);
    for (i in fields) {
        field = this.findField(i);
        if (!field.disabled) {
            //check if the form item provides a specialized getSubmitValue() and use that if available
            if (typeof field.getSubmitValue == "function")
                fields[i] = field.getSubmitValue();
        }
    }
    return fields;
};

Ext.data.JsonWriter.prototype.render = function(params, baseParams, data) {
    if (this.encode === true) {
        // Encode here now.
        Ext.apply(params, baseParams);
        Ext.apply(params, data);
    } else {
        var jdata = Ext.apply({}, baseParams);
        jdata[this.meta.root] = data;
        params.jsonData = jdata;
    }
};

Ext.form.BasicForm.prototype.setValues = function(values) {
    if (Ext.isArray(values)) { // array of objects
        for (var i = 0, len = values.length; i < len; i++) {
            var v = values[i];
            var f = this.findField(v.id);
            if (f) {
                f.setValue(v.value);
                if (this.trackResetOnLoad) {
                    f.originalValue = f.getValue();
                }
            }
        }
    } else { // object hash
        var field, id;
        for (id in values) {
            if (!Ext.isFunction(values[id]) && (field = this.findField(id))) {
                field.setValue(values[id]);
                if (this.trackResetOnLoad) {
                    field.originalValue = field.getValue();
                }
            }
            if (Ext.isObject(values[id])) {
                this.setValues(values[id]);
            }
        }
    }
    return this;
}


App.General.CustomEvents = Ext.extend(Ext.util.Observable, {
    events: {
        nodeItemClick: true
    }
});

customEvents = new App.General.CustomEvents();

customEvents.on('nodeItemClick', function(node, callback) {
    if (node) {
        Ext.Ajax.request({
            url: 'index.php/core/user/checkaccessnode',
            success: function(response) {
                response = Ext.decode(response.responseText);
                if (response.success == false) {
                    Ext.getCmp('App.PrincipalPanel').removeAll();
                    Ext.getCmp('App.PrincipalPanel').doLayout();
                } else {
                    App.Interface.ViewPort.displayModuleGui(node);
                }
            },
            failure: function() {

            },
            params: {
                node_id: node.id
            }
        });
    }
});

Ext.apply(Ext.form.VTypes, {
    /** 
     * The function used to validated multiple email addresses on a single line 
     * @param {String} value The email addresses - separated by a comma or semi-colon 
     */
    multiemail: function(v) {
        var array = v.split(',');
        var valid = true;
        Ext.each(array, function(value) {
                if (!this.email(value)) {
                    valid = false;
                    return false;
                }
            },
            this);
        return valid;
    },
    /** 
     * The error text to display when the multi email validation function returns false 
     * @type String 
     */
    multiemailText: 'This field should be an e-mail address, or a list of email addresses separated by commas(,) in the format "user@domain.com,test@test.com"',
    /** 
     * The keystroke filter mask to be applied on multi email input 
     * @type RegExp 
     */
    multiemailMask: /[a-z0-9_\.\-@\,]/i
});

Ext.tree.TreeNodeUI.prototype.onClick = function(e) {
    if (this.dropping) {
        e.stopEvent();
        return;
    }
    if (this.fireEvent("beforeclick", this.node, e) !== false) {
        var a = e.getTarget('a');
        var img = e.getTarget('img');
        if (!this.disabled && this.node.attributes.href && a) {
            this.fireEvent("click", this.node, e);
            return;
        } else
        if (a && e.ctrlKey) {
            e.stopEvent();
        }
        e.preventDefault();
        if (this.disabled) {
            return;
        }
        if (this.node.attributes.singleClickExpand && !this.animating && this.node.isExpandable()) {
            this.node.toggle();
        }
        if (img) {
            this.fireEvent("iconclick", this.node, e);
        } else {
            this.fireEvent("click", this.node, e);
        }
    } else {
        e.stopEvent();
    }
};

Ext.tree.TreeNodeUI.prototype.renderElements = function(n, a, targetNode, bulkRender) {
    // add some indent caching, this helps performance when rendering a large tree
    this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

    var cb = Ext.isBoolean(a.checked),
        nel,
        href = a.href ? a.href : Ext.isGecko ? "" : "#",
        buf = ['<li class="x-tree-node"><div ext:tree-node-id="', n.id, '" class="x-tree-node-el x-tree-node-leaf x-unselectable ', '', '" unselectable="on">',
            '<span class="x-tree-node-indent">', this.indentMarkup, "</span>",
            '<img src="', this.emptyIcon, '" class="x-tree-ec-icon x-tree-elbow" />',
            '<img src="', a.icon || this.emptyIcon, '" class="x-tree-node-icon', (a.icon ? " x-tree-node-inline-icon" : ""), (a.iconCls ? " " + a.iconCls : ""), '" unselectable="on" />',
            cb ? ('<input class="x-tree-node-cb" type="checkbox" ' + (a.checked ? 'checked="checked" />' : '/>')) : '',
            '<a hidefocus="on" class="x-tree-node-anchor" href="', href, '" tabIndex="1" ',
            a.hrefTarget ? ' target="' + a.hrefTarget + '"' : "", '><span class="' + a.cls + '" unselectable="on">', n.text, "</span></a></div>",
            '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>"
        ].join('');

    if (bulkRender !== true && n.nextSibling && (nel = n.nextSibling.ui.getEl())) {
        this.wrap = Ext.DomHelper.insertHtml("beforeBegin", nel, buf);
    } else {
        this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf);
    }

    this.elNode = this.wrap.childNodes[0];
    this.ctNode = this.wrap.childNodes[1];
    var cs = this.elNode.childNodes;
    this.indentNode = cs[0];
    this.ecNode = cs[1];
    this.iconNode = cs[2];
    var index = 3;
    if (cb) {
        this.checkbox = cs[3];
        // fix for IE6
        this.checkbox.defaultChecked = this.checkbox.checked;
        index++;
    }
    this.anchor = cs[index];
    this.textNode = cs[index].firstChild;
};