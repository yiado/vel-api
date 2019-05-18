Ext.ux.form.ColorPaletteField = Ext.extend(Ext.form.TriggerField, {
    triggerClass : 'x-form-color-trigger',

    colors : [
        '000000', '222222', '444444', '666666', '999999', 'BBBBBB', 'DDDDDD', 'FFFFFF',
        '660000', '663300', '996633', '003300', '003399', '000066', '330066', '660066',
        '990000', '993300', 'CC9900', '006600', '0033FF', '000099', '660099', '990066',
        'CC0000', 'CC3300', 'FFCC00', '009900', '0066FF', '0000CC', '663399', 'CC0099',
        'FF0000', 'FF3300', 'FFFF00', '00CC00', '0099FF', '0000FF', '9900CC', 'FF0099',        
        'CC3333', 'FF6600', 'FFFF33', '00FF00', '00CCFF', '3366FF', '9933FF', 'FF00FF',
        'FF6666', 'FF6633', 'FFFF66', '66FF66', '00FFFF', '3399FF', '9966FF', 'FF66FF',
        'FF9999', 'FF9966', 'FFFF99', '99FF99', '99FFFF', '66CCFF', '9999FF', 'FF99FF',
        'FFCCCC', 'FFCC99', 'FFFFCC', 'CCFFCC', 'CCFFFF', '99CCFF', 'CCCCFF', 'FFCCFF',                         
    ],

    
    // whether or not the field background, text, or triggerbackgroud are set to the selected color
    colorizeFieldBackgroud: true,
    colorizeFieldText: true,
    colorizeTrigger: false,
    
    // by default don't let user enter its own string value
    editable: false,

    initComponent : function(){
        Ext.ux.form.ColorPaletteField.superclass.initComponent.call(this);

        this.addEvents(            
            'select'
        );
    },

    // private
    validateBlur : function(){
        return !this.menu || !this.menu.isVisible();
    },

    setValue : function(color){
        if (this.colorizeFieldBackgroud) this.el.applyStyles('background: #' + color  + ';');
        if (this.colorizeFieldText) this.el.applyStyles('color: #' + color  + ';');
        if (this.colorizeTrigger) this.trigger.applyStyles('background-color: #' + color  + ';');        
        return Ext.ux.form.ColorPaletteField.superclass.setValue.call(this, color);        
    },

    // private
    onDestroy : function(){
        Ext.destroy(this.menu);
        Ext.ux.form.ColorPaletteField.superclass.onDestroy.call(this);
    },
    
    // private
    onTriggerClick : function(){
        if(this.disabled){
            return;
        }
        if(this.menu == null){
            this.menu = new Ext.menu.ColorMenu({
                hideOnClick: false
            });
        }
        this.onFocus();
        
        Ext.apply(this.menu.palette,  {
            colors : this.colors
        });        
        
        this.menu.show(this.el, "tl-bl?");
        if (this.menu.palette.el.child("a.color-" + this.getValue())) // only select the color if part of the paletter
          this.menu.palette.select(this.getValue());        
        this.menuEvents('on');
    },
    
    //private
    menuEvents: function(method){
        this.menu[method]('select', this.onSelect, this);
        this.menu[method]('hide', this.onMenuHide, this);
        this.menu[method]('show', this.onFocus, this);
    },
    
    onSelect: function(m, d){
        this.setValue(d);
        this.fireEvent('select', this, d);
        this.menu.hide();
    },
    
    onMenuHide: function(){
        this.focus(false, 60);
        this.menuEvents('un');
    }
    
});

Ext.reg('colorpaletteField', Ext.ux.form.ColorPaletteField);