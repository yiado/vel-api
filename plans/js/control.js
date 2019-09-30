var obj;
var originaNodeId = '';

function setOver2(evt) {}
function setOver3(evt) {
//    console.log('22222');
}



function setOver(evt) {
//    console.log('SET');
//    console.log(evt.target.tagName);
    var oo = evt.target;

    var ParenTag = oo.parentNode

    if (evt.target.tagName == 'path' && oo.getAttribute("fill") != 'none' && oo.getAttribute("fill") != null) {

        oo.setAttribute("fill", 'red');
        oo.setAttribute("fill-opacity", '0.5');

//        var gParent = evt.target.parentElement;

//        console.log('>>aqui', evt.target.getAttribute('id'));

//        if (gParent.hasAttribute("id") && gParent.getAttribute('id') != '') {
//            var child;
//            for (var i = 1; i < gParent.children.length - 1; i++)
//            {
////            console.log('>>aqui',gParent.children[i] );
//                child = gParent.children[i];
//                console.log('>>aqui', child.tagName);
////                if (child.tagName == 'path'){
////                    if (!child.hasAttribute("old-stroke")) {
////                        child.setAttribute("old-stroke", child.getAttribute("stroke-width"));
////                    }
////
////                    child.setAttribute("stroke-width", "1.5px");
////                }
////            child.className = 'prueba';
////            childs[i].style.stroke-width= '2px';
//            }
//        }

    }

//    var oo = evt.target;
//    if (parent.App.Plan.ViewConfig.EnableSelect && oo.hasAttribute("id") && !oo.hasAttribute("ostroke")) {
//
//        oo.setAttribute("ostroke", oo.getAttribute("stroke"));
//        oo.setAttribute("over", "1");
//
//        if (oo.getAttribute("stroke") == "blue") {
//            oo.setAttribute("stroke", "red");
//        } else {
//            oo.setAttribute("stroke", "blue");
//        }
//
//    }

}

function setOut(evt) {
    var oo = evt.target;

    if (evt.target.tagName == 'path' && oo.getAttribute("fill") != 'none' && oo.getAttribute("fill") != null) {

        oo.setAttribute("fill", 'white');
        oo.setAttribute("fill-opacity", '0');
    }

//    var gParent = evt.target.parentElement;
//
//
//    var child;
//    for (var i = 1; i < gParent.children.length - 1; i++)
//    {
////            console.log('>>aqui',gParent.children[i] );
//        child = gParent.children[i];
//        if (child.hasAttribute("old-stroke")) {
//            child.setAttribute("stroke-width", child.getAttribute("old-stroke"));
//        }
//
//        child.setAttribute("stroke-width", "1.5px");
//
////            child.className = 'prueba';
////            childs[i].style.stroke-width= '2px';
//    }


//    var oo = evt.target;
//    if (parent.App.Plan.ViewConfig.EnableSelect && oo.hasAttribute("id") && oo.hasAttribute("over")) {
//        oo.setAttribute("stroke", oo.getAttribute("ostroke"));
//        oo.removeAttribute("ostroke");
//        oo.removeAttribute("over");
//    }

}

function setClick(evt) {


    var x = event.clientX;
    var y = event.clientY;
    var oo = evt.target;
    var ParenTag = oo.parentNode

    //  VALIDACION ANTERIOR
    //    if ((parent.App.Plan.ViewConfig.EnableSelect && oo.hasAttribute("id") && evt.button != 2) ) {
    // VALIDACION ACTUAL

    if (evt.button != 2 && !parent.panZoom.isClickZoomEnabled() && !parent.panZoom.isDblclickZoomOut()) {
        if ((oo.nodeName == 'path' && ParenTag.nodeName == 'g' && ParenTag.hasAttribute("id")) || (oo.nodeName == 'path' && oo.hasAttribute("id"))) {


            if (!oo.hasAttribute("ostroke-width")) { // no marc line

                cl = '#' + parent.App.Plan.ViewConfig.Color;
                lw = parent.App.Plan.ViewConfig.Width;

                oo.setAttribute("ostroke-width", oo.getAttribute("stroke-width"));
                oo.setAttribute("stroke-width", oo.getAttribute("stroke-width") * lw);
                oo.setAttribute("ostroke", oo.getAttribute("stroke"));
                oo.setAttribute("stroke", cl);
                
                addHandle(oo.getAttribute("id"));
                if (oo.hasAttribute("over"))
                    oo.removeAttribute("over");

                if (oo.id.length) {
                    //Se verifica que si es una linea asociada se aplique zoom y se actualice la información
                    var handler = 'id_handler=' + oo.id + '&plan_id=' + parent.App.Plan.CurrentPlanId;

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "../index.php/plan/plan/getAssociates");
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send(handler);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var result = JSON.parse(xhr.responseText);
                            if (result.total > 0) {

                                parent.panZoom.zoomAtPoint(2, {x: x, y: y});
                                if (!(originaNodeId.length)) {
                                    originaNodeId = parent.App.Interface.selectedNodeId;
                                }

                                parent.App.Interface.selectedNodeId = result.results[0]['node_id'];
                                parent.App.Interface.refresTabs();

                            }

                        }
                    }
                }

            } else { // line marc

                oo.setAttribute("stroke-width", oo.getAttribute("ostroke-width"));
                oo.setAttribute("stroke", oo.getAttribute("ostroke"));
                oo.removeAttribute("ostroke");
                oo.removeAttribute("ostroke-width");
                removeHandle(oo.getAttribute("id"));
                setOver(evt);

                if (oo.id.length) {
                    //Se verifica que si es una linea asociada se aplique zoom y se actualice la información
                    var handler = 'id_handler=' + oo.id + '&plan_id=' + parent.App.Plan.CurrentPlanId;

                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "../index.php/plan/plan/getAssociates");
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send(handler);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var result = JSON.parse(xhr.responseText);
                            if (result.total > 0) {
                                parent.App.Interface.selectedNodeId = originaNodeId;
                                parent.App.Interface.refresTabs();

                            }

                        }
                    }
                }

            }


        }

        if (obj) {
            if (parent.App.Plan.ViewConfig.EnableSelect) {
//           console.log('>>obj: ',document.getElementById(obj[1]));
                if (obj.length > 2) {

                    var oo = document.getElementById(obj[1]);
                    if (oo.hasAttribute("ostroke-width")) { //  marc line

                        oo.setAttribute("stroke-width", oo.getAttribute("ostroke-width"));
                        oo.setAttribute("stroke", oo.getAttribute("ostroke"));
                        oo.removeAttribute("ostroke");
                        oo.removeAttribute("ostroke-width");
                        removeHandle(oo.getAttribute("id"));
                        setOver(evt);

                    }

                }


            }
        }
    }

}

function StopMenu() {

}

function addHandle(ele) {

    var handles = parent.lastobj;
    if (handles != null) {
        obj = handles.split(",");

        obj[obj.length] = ele;

        if (obj.length) {
            parent.lastobj = obj.join(',');
        }
    }
    
//    parent.Ext.getCmp('App.Plan.Principal').getActiveTab().save_select_tool.enable();

}

function removeHandle(ele) {

    var handles = parent.lastobj;
    if (handles != null) {
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
}


function deleteText($id, $plan_id) {

    parent.deleteText2($id, $plan_id);
}

	