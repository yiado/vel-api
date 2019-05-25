
function setOver(evt) {

    var oo = evt.target;
    if (parent.App.Plan.ViewConfig.EnableSelect && oo.hasAttribute("id") && !oo.hasAttribute("ostroke")) {

        oo.setAttribute("ostroke", oo.getAttribute("stroke"));
        oo.setAttribute("over", "1");

        if (oo.getAttribute("stroke") == "blue") {
            oo.setAttribute("stroke", "red");
        } else {
            oo.setAttribute("stroke", "blue");
        }

    }

}

function setOut(evt) {

    var oo = evt.target;
    if (parent.App.Plan.ViewConfig.EnableSelect && oo.hasAttribute("id") && oo.hasAttribute("over")) {
        oo.setAttribute("stroke", oo.getAttribute("ostroke"));
        oo.removeAttribute("ostroke");
        oo.removeAttribute("over");
    }

}

function setClick(evt) {

    var oo = evt.target;
    if (parent.App.Plan.ViewConfig.EnableSelect && oo.hasAttribute("id") && evt.button != 2) {

        if (!oo.hasAttribute("ostroke-width")) { // no marc line

            cl = '#' + parent.App.Plan.ViewConfig.Color;
            lw = parent.App.Plan.ViewConfig.Width;

            oo.setAttribute("ostroke-width", oo.getAttribute("stroke-width"));
            oo.setAttribute("stroke-width", oo.getAttribute("stroke-width") * lw);
            oo.setAttribute("stroke", cl);
            addHandle(oo.getAttribute("id"));
            if (oo.hasAttribute("over"))
                oo.removeAttribute("over");

        } else { // line marc

            oo.setAttribute("stroke-width", oo.getAttribute("ostroke-width"));
            oo.setAttribute("stroke", oo.getAttribute("ostroke"));
            oo.removeAttribute("ostroke");
            oo.removeAttribute("ostroke-width");
            removeHandle(oo.getAttribute("id"));
            setOver(evt);

        }

    }

}

function StopMenu() {

}

function addHandle(ele) {

    var handles = parent.lastobj;
    obj = handles.split(",");

    obj[obj.length] = ele;

    parent.lastobj = obj.join(',');

    parent.Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.enable();

}

function removeHandle(ele) {

    var handles = parent.lastobj;
    obj = handles.split(",");
    tmp = new Array();
    var cnt = 0;

    for (var i = 0; i < obj.length; i++) {
        if (obj[i] != ele) {
            tmp[cnt] = obj[i];
            cnt++;
        }
    }

    parent.lastobj = tmp.join(',');

}


function deleteText($id) {

    parent.deleteText2($id);
}

	