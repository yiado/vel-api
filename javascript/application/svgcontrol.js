var svgDocument = null;
var svgRoot = null;
var cnt = 1;
var handles_update = null;
var lastobjfill = "";
var lastobj = "";
var lastSection = "";
var lastObjSvg = "";
var zl = true;
var original = false;
var lastOriginal = "";

function InitHTML() {
    svgDocument = null;
    var embed = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    try {
        svgDocument = embed.getSVGDocument();
    } catch (exception) {
    }
    if (svgDocument && svgDocument.defaultView) {
        svgWindow = svgDocument.defaultView;
    } else {
        try {
            svgDocument = embed.getWindow();
        } catch (exception) {
        }
    }

    if (svgDocument) {
        svgRoot = svgDocument.documentElement;
    }

    if (App.Plan.Handler) {
        dchangeStroke(App.Plan.Handler);
        App.Plan.Handler = null;
    }
}

function dchangeStroke(handles, zll) {

    var view = new Array();
    var fact = 20;
    view[1] = 1000000.0;
    view[2] = 1000000.0;
    view[3] = 0;
    view[4] = 0;
    var node_id = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    if (node_id && svgDocument != null) {
        if (lastobj != '' && lastobj != null) {
            lobj = lastobj.split(",");
            for (var i = 0; i < lobj.length; i++) {
                if (svgDocument && svgDocument.getElementById(lobj[i])) {
                    var targetElement = svgDocument.getElementById(lobj[i]);

                    if (targetElement && handles_update == null) {

                        if (targetElement.hasAttributeNS(null, 'ostroke')) {
                            targetElement.setAttributeNS(null, "stroke", targetElement.getAttributeNS(null, "ostroke"));
                            targetElement.removeAttributeNS(null, "ostroke");
                        }
                        if (targetElement.hasAttributeNS(null, 'ostroke-width')) {
                            targetElement.setAttributeNS(null, "stroke-width", targetElement.getAttributeNS(null, "ostroke-width"));
                            targetElement.removeAttributeNS(null, "ostroke-width");
                        }
                    }
                }
            }
        }

        obj = handles.split(",");
        cl = '#' + App.Plan.ViewConfig.Color;
        lw = App.Plan.ViewConfig.Width;
        d = new Array();
        for (var i = 0; i < obj.length; i++) {
            if (svgDocument.getElementById(obj[i])) {
                var targetElement = svgDocument.getElementById(obj[i]);
                targetElement.setAttributeNS(null, "ostroke-width", targetElement.getAttributeNS(null, "stroke-width"));
                targetElement.setAttributeNS(null, "ostroke", targetElement.getAttributeNS(null, "stroke"));
                targetElement.setAttributeNS(null, "stroke-width", targetElement.getAttributeNS(null, "stroke-width") * lw);
                targetElement.setAttributeNS(null, "stroke", cl);
                d = targetElement.getAttributeNS(null, "d").split("L");
                if (handles && App.Plan.ViewConfig.ZoomLoc) {
                    for (var e = 0; e < d.length; e++) {
                        _aux = new Array();
                        _aux = d[e].replace(/[a-zA-Z]/, "").split(" ");
                        if (parseInt(_aux[0]) < view[1]) {
                            view[1] = parseInt(_aux[0]);
                        }
                        if (parseInt(_aux[1]) < view[2]) {
                            view[2] = parseInt(_aux[_aux.length - 1]);
                        }
                        if (parseInt(_aux[0]) > view[3]) {
                            view[3] = parseInt(_aux[0]);
                        }
                        if (parseInt(_aux[1]) > view[4]) {
                            view[4] = parseInt(_aux[_aux.length - 1]);
                        }
                    }
                }
            }
        }
        if (zll)
            return;
        if (handles && App.Plan.ViewConfig.ZoomLoc) {
            svgRoot.currentScale = 1;
            svgRoot.currentTranslate.x = 1;
            svgRoot.currentTranslate.y = 1;
            if (view[1] != 1000000.0)
                svgRoot.setAttributeNS(null, "viewBox", (view[1] - 10) + " " + (view[2] - 10) + " " + (view[3] - view[1] + fact) + " " + (view[4] - view[2] + fact));
        }
        lastobj = handles;
    }
}

function zoomfit() {
    var node_id = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    if (node_id && svgDocument != null) {
        svgRoot.currentScale = 1;
        svgRoot.currentTranslate.x = 1;
        svgRoot.currentTranslate.y = 1;
        svgRoot.setAttributeNS(null, "viewBox", "0 0 800.000 600.000");
    }
}

function refreshWMode(tp) {
    var embed = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    //App.Plan.Handler = lastobj;
    //dchangeStroke('');
    if (embed.wmode == 'window' && tp == 'out') {
        fix_flash('transparent');
    } else if (embed.wmode == 'transparent' && tp == 'in') {
        fix_flash('window');
    }
    //dchangeStroke(App.Plan.Handler, true);
}

function getLayersOld() {
    var layers = new Array();
    var node_id = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    if (node_id && svgDocument != null) {
        var child = svgRoot.firstChild;
        while (child != null) {
            if (child.nodeName == "g" && child.hasChildNodes()) {
                if (child.firstChild.nodeType == 3 && url_encode(child.getAttributeNS(null, 'id')) != "copy") {
                    var estate = 1;
                    if (child.getAttributeNS(null, 'style')) {
                        estate = 0;
                    }
                    layers[layers.length] = [url_encode(child.getAttributeNS(null, 'id')), child.getAttributeNS(null, 'id'), estate];
                }
            }
            child = child.nextSibling;
        }
    }
    return layers;
}

function getLayers() {
    var layers = new Array();
    var node_id = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    if (node_id && svgDocument != null) {
        var originalSvg = svgRoot.getElementById('originalSVG');

        for (var i = 0; i < originalSvg.children.length; i++) {
            var svgLayer = originalSvg.children[i];

            if ((svgLayer.nodeName) == "g" && (svgLayer.children.length > 0)) {
                if (svgLayer.firstChild.nodeType == 3 && url_encode(svgLayer.getAttributeNS(null, 'id')) != "copy") {
                    var estate = 1;
                    if (svgLayer.getAttributeNS(null, 'style')) {
                        estate = 0;
                    }



                    layers[layers.length] = [url_encode(svgLayer.getAttributeNS(null, 'id')), svgLayer.getAttributeNS(null, 'id'), estate];
                }
            }

        }
    }
    return layers;
}



function changeFillOld(section, cl, clear_last) {
    var node_id = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    var section_layer = null;
    if (node_id) {
        if (lastobjfill.length > 0 && clear_last != false) {
            var cp = svgRoot.getElementById("copy");
            cp.parentNode.replaceChild(cp.cloneNode(false), cp);
        }
        if (section == 0)
            return;
        var child = svgRoot.firstChild;
        while (child != null) {
            if (child.nodeName == "g" && child.hasChildNodes() && child.getAttributeNS(null, 'id') == section) {
                section_layer = child;
            }
            child = child.nextSibling;
        }
        if (section_layer != null) {
            child = section_layer.firstChild;
            while (child != null) {
                if (child.nodeName == "path") {
                    var poly;
                    d = new Array();
                    d = child.getAttributeNS(null, 'd').split(" ");
                    _aux = new Array();
                    _aux[_aux.length] = d[0] + " " + d[d.length - 1];
                    for (var e = 2; e < d.length; e++) {
                        if (d[e].indexOf("M") != -1) {
                            tmp = d[e].split("M");
                            _aux[_aux.length] = "L" + tmp[1] + " " + tmp[0];
                        }
                    }
                    _aux[_aux.length] = d[0].replace("M", "L") + " " + d[d.length - 1] + " Z";
                    poly = svgDocument.createElementNS(null, 'path');
                    poly.setAttributeNS(null, "d", _aux.join(' '));
                    poly.setAttributeNS(null, "fill", '#' + cl);
                    poly.setAttributeNS(null, "id", "ODD");
                    svgDocument.getElementById("copy").appendChild(poly);
                }
                child = child.nextSibling;
            }
        }
        lastobjfill = section;
    }
}

function cleanAll() {
    lastObjSvg = '';
    lastSection = '';
    original = false;
}

function changeFill(section, cl, clear_last = true) {
    var node_id = document.getElementById('plan_embed_' + App.Plan.CurrentPlanId);
    var section_layer = null;

    if (node_id) {
        //


        if (((lastObjSvg != '') && (lastSection.length > 0)) || ((lastObjSvg != '') && (lastSection.length > 0) && (cl == ''))) {

            var svgCode = svgRoot.getElementById('originalSVG')
            var lastSectionSvg = svgRoot.getElementById(lastSection);
            if (lastSectionSvg) {
                svgCode.removeChild(lastSectionSvg);
                svgCode.appendChild(lastObjSvg);
            }
        }

        if ((!original) && (!clear_last)) {
            var node = svgRoot.getElementById('originalSVG')
            lastOriginal = node.cloneNode(true);
            original = true;
        } else if ((original) && (clear_last)) {

            var ParentSvg = svgRoot.children[0];
            var svgCode = svgRoot.getElementById('originalSVG');
            ParentSvg.removeChild(svgCode);
            ParentSvg.appendChild(lastOriginal);
            original = false;

        }
        if ((section == 0) || (cl == '')) {
            lastObjSvg = '';
            lastSection = '';
            return;
        }

        var tagSection = svgRoot.getElementById(section);
        lastSection = (clear_last) ? section : '';

        if (tagSection != null) {
            if (tagSection.nodeName == "g" && (tagSection.children.length > 0)) {

                lastObjSvg = (clear_last) ? tagSection.cloneNode(true) : '';


                for (var i = 0; i < tagSection.children.length; i++) {
                    tagChild = tagSection.children[i];

                    if (tagChild.nodeName == "path") {
                        var poly;
                        d = new Array();

                        tagChild.setAttribute("stroke", '#' + cl);
                        tagChild.setAttribute("stroke-width", '1.5px');

                    }

                }
            }
        }

    }


}


function updateVisibility(layer, estate) {

    var originalSvg = svgRoot.getElementById('originalSVG');

    for (var i = 0; i < originalSvg.children.length; i++) {
        var svgLayer = originalSvg.children[i];

        if ((svgLayer.nodeName) == "g" && (svgLayer.children.length > 0)) {
            if (svgLayer.firstChild.nodeType == 3 && url_encode(svgLayer.getAttributeNS(null, 'id')) != "copy" && url_encode(svgLayer.getAttributeNS(null, 'id')) == layer) {
                if (estate == 1) {
                    if (svgLayer.hasAttributeNS(null, 'style')) {
                        svgLayer.removeAttributeNS(null, 'style');
                    }
                } else {
                    svgLayer.setAttributeNS(null, 'style', 'display: none');
                }
            }
        }

    }

}

function updateVisibilityOld(layer, estate) {
    var child = svgRoot.firstChild;
    var control = 0;
    while (child != null) {
        if (child.nodeName == "g" && child.hasChildNodes()) {
            if (child.firstChild.nodeType == 3 && url_encode(child.getAttributeNS(null, 'id')) != "copy" && url_encode(child.getAttributeNS(null, 'id')) == layer) {
                if (estate == 1) {
                    if (child.hasAttributeNS(null, 'style')) {
                        child.removeAttributeNS(null, 'style');
                    }
                } else {
                    child.setAttributeNS(null, 'style', 'display: none');
                }
            }
        }
        child = child.nextSibling;
    }
}