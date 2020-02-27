<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>iGeo</title>
    <base href="<?=base_url(); ?>"/>
    <!-- ExtJs Library -->
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/resources/css/ext-all.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/treegrid/treegrid.css" rel="stylesheet" />

    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/adapter/ext/ext-base-debug.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ext-all-debug.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/locale/ext-lang-es.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/FileUploadField.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/ItemSelector.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/MultiSelect.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/XmlTreeLoader.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/treegrid/TreeGridSorter.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/treegrid/TreeGridColumnResizer.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/treegrid/TreeGridNodeUI.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/treegrid/TreeGridLoader.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/treegrid/TreeGridColumns.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/treegrid/TreeGrid.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/Spinner.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/SpinnerField.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/Window.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/CheckColumn.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/ColorPaletteField.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/FlashMessage.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/GroupSummary.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/RowEditor.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/DataView-more.js"></script>
    
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/extensible-all-debug.js"></script>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/calendar/resources/css/extensible-all.css"/>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/calendar/ext-lang-es.js"></script>
    
    <!-- ExtJs Library -->

    <!-- Application Library and StyleSheets -->
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/base.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/document.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/fileuploadfield.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/MultiSelect.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/Spinner.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/RowEditor.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/FlashMessage.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/GroupSummary.css" />
    <script type="text/javascript" src="<?= base_url(); ?>javascript/svg-pan-zoom.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/svgcontrol.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/base.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/store.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/language.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/preference.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/security.js"></script>
    <script language="JavaScript" type="text/javascript">
<?

		// language tags
    	$modules = array();
    	$tags = array();
    	foreach ($language as $tag) {

    		$modules[$tag->Module->module_namespace] = $tag->Module->module_namespace;

    		$tags[] = 'App.Language.' . $tag->Module->module_namespace . '.' . $tag->language_tag_tag . ' = ' . "'" . $tag->language_tag_value . "';";
    	}

    	foreach ($modules as $module) {

    		echo "    Ext.namespace('App.Language." . $module . "');" . PHP_EOL;
    	}

    	foreach ($tags as $tag) {

    		echo '    ' . $tag . PHP_EOL;
    	}

		// language tags
    	foreach ($user_modules as $i => $module) {

    		echo '    App.UserModules[' . $i . '] = ' . "'" . $module . "';" . PHP_EOL;
    	}

//    	echo '    App.UserModules[0] = ' . "'infra';" . PHP_EOL;
    	echo "    App.BaseUrl = '" . base_url() . "';" . PHP_EOL;
    	echo "    App.BaseIndex = '" . base_url() . index_page() . "';" . PHP_EOL;


    	echo "App.Security.Session.user_username = '" . $session['user_username'] .  "';" . PHP_EOL;

?>
    </script>

    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/security.js"></script>
    <script language="JavaScript" type="text/javascript">
<?
		// user actions
    	foreach ($user_actions as $i => $action) {

    		echo "    App.Security.Actions['" . $action['module_action_id'] . "'] = " . "'" . $action['module_action_id'] . "';" . PHP_EOL;
    	}

?>

	</script>

	<!-- Dynamic JS Variable -->
    <script language="JavaScript" type="text/javascript">

        App.Security.Session.xml_permissions_file = '<? echo @$session['xml_permissions_file']; ?>';
        App.Security.Session.user_type = '<? echo $session['user_type']; ?>';
        App.Security.Session.user_id = <? echo $session['user_id']; ?>;
        App.Security.Session.user_tree_full = <? echo $session['user_tree_full']; ?>;
        App.Security.Session.user_path = '<?=(@$session['user_path'] ? $session['user_path'] : 'root'); ?>';
        App.Security.Session.user_default_module = '<?=(@$session['user_default_module'] ? $session['user_default_module'] : 'NULL'); ?>';
        App.Security.Session.system_current_date = null;


    </script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/node.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/nodetype.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/nodetypecategory.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/structuretree.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/topmenu.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/gui/interface2.js"></script>
    <script defer  src="<?= base_url(); ?>javascript/application/font/fontawesome-all.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>javascript/pdfkit.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>javascript/blobstream.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>javascript/source.js"></script>

	<!-- Dynamic JS Variable -->
	
<?
		// user actions
    	foreach ($gui_files as $module => $module_gui_files) {

			foreach ($module_gui_files as $file) {
				
				if (strpos($file, 'css') === false) {
					if (strpos($file, 'http') === false) {
						echo '    <script type="text/javascript" src="' . base_url() . 'javascript' . $file . '"></script>' . PHP_EOL;
					} else {
						echo '    <script type="text/javascript" src="' . $file . '"></script>' . PHP_EOL;
					}
				} else {
					echo '    <link rel="stylesheet" type="text/css" href="' . base_url() . 'javascript/application' . $file . '"/>' . PHP_EOL;
				}
			}
			
    	}

?>
<script  type="text/javascript" src="<?= base_url(); ?>javascript/jquery-3.3.1.min.js" ></script>
    <script language="JavaScript" type="text/javascript">
<?
		// user actions
    	foreach ($gui_cfgs as $cfg => $cfg_value) {

    		echo "    " . $cfg . " = " . "'" . $cfg_value . "';" . PHP_EOL;
    	}

?>

	</script>
	
    <!-- Application Library and StyleSheets -->
    
<style type="text/css">
.x-grid33-cell-inner {
overflow: visible;
white-space: normal !important;
word-wrap: break-word; 
word-break: break-all;
}
</style>



      
        <script language="JavaScript" type="text/javascript">
            jQuery(function () {
                jQuery('.container_map').on('click', 'g', function (evt) {

                    var e = evt.target;
                    var dim = e.getBoundingClientRect();
                    var x = evt.clientX - dim.left;
                    var y = evt.clientY - dim.top;
                    // console.log("x: " + x + " y:" + y);
                });
            });
            var constante = 1;
            var panZoom2 = '';
            var panZoom = '';
            var interval = '';
            var clase = true;
            var clickEnable = '';
            var zomm_m = function (id_, msg) {

                var time = 0;
                var idElement = 'plan_embed_' + id_;
                jQuery('.controls').hide();
                jQuery('.print_icon').hide();
                clearInterval(interval);

                interval = setInterval(function () {
                    if (jQuery('#' + idElement)) {

                        var embElement = document.getElementById(idElement);
                        if (embElement != null) {
                            
//                             embElement.setAttribute('style', 'visibility: hidden');
//                            if (App.Plan.idPlan.indexOf(idElement) === -1) {
                            
//                            }
                            
//                            var route = embElement.getAttribute('src');
//                            var num = route.split('?');
//                            embElement.setAttribute('src', num[0] + "?" + Math.floor((Math.random() * 100) + Number(num[1])));
//                            
                           
                          
                            setTimeout(function () {
                                panZoom = svgPanZoom('#' + idElement, {zoomEnabled: true, controlIconsEnabled: false});
                                panZoom.setWidth();
                                panZoom.disablePositionZoom();
                                restoreLupa(id_);
                                restoreText(id_);

                                if (document.getElementById('id_embed')) {
                                    document.getElementById('id_embed').value = id_;
                                }

                                var ZoomL = document.getElementById('lupa_zoom_' + id_);
                                var clase = true;
                                if (ZoomL) {
                                    ZoomL.removeAttribute('style');
                                }

                                embElement.setAttribute('style', 'visibility: visible');
                                if (App.Plan.idPlan.indexOf(idElement) === -1) {
                                   
                                    App.Plan.idPlan.push(idElement);
                                } else {
                                    
                                    if(!!window.chrome && !!window.chrome.webstore){
                                         time= 2000;
                                    }else{
                                         time =500;
                                    }
                                    setTimeout(function () {
                                 
                                    jQuery('.print_icon').show();
                                    jQuery('.controls').show();
                                      }, time);
                                 
                                }



                            }, 1000);

                        }

                        clearInterval(interval);
                    }
                }, 1000);

//                return;
//
//
//
//
//
//                jQuery('.controls').hide();
//                if (/*@cc_on!@*/false || !!document.documentMode) {
//                    console.log('dsd');
//                    var msg = Ext.MessageBox.wait("", "Cargando...");
//                }
//
//
//                App.Plan.idPlan = [];
//                interval = setInterval(
//                        function () {
//                            if (jQuery('.test_')) {
//
//                                jQuery('.test_').each(function () {
//
//
//                                    if (embElement != null) {
//                                        embElement.setAttribute('style', "visibility = hidden;");
//
//                                        var svgElement = embElement.getSVGDocument().documentElement;
//                                    }
//
//                                    var res = idElement.split("_");
//
//                                    console.log('--->', idElement);
//
//
//
//                                    if (App.Plan.idPlan.indexOf(res[2]) === -1) {
//                                        var msg = Ext.MessageBox.wait("", "Cargando...");
//                                        var t = embElement.getAttribute('src');
//                                        var num = t.split('?');
//                                        embElement.setAttribute('src', num[0] + "?" + Math.floor((Math.random() * 100) + Number(num[1])));
//                                        App.Plan.idPlan.push(res[2]);
//
//                                        jQuery('.print_icon').hide();
//                                    }
//
//
//                                    embElement.addEventListener('load', function ()
//                                    {
//                                        console.log('aqui', jQuery('#' + $(this).attr('id')));
//                                        embElement.setAttribute('style', "visibility = hidden;");
//                                        if (jQuery('#' + $(this).attr('id'))) {
//                                            panZoom = svgPanZoom('#' + $(this).attr('id'), {zoomEnabled: true, controlIconsEnabled: false});
//                                            panZoom.setWidth();
//                                            panZoom.disablePositionZoom();
//
//                                            restoreLupa(res[2]);
//                                            restoreText(res[2]);
//
//                                            if (document.getElementById('id_embed')) {
//                                                document.getElementById('id_embed').value = res[2];
//                                            }
//
//                                            var ZoomL = document.getElementById('lupa_zoom_' + res[2]);
//                                            var clase = true;
//                                            if (ZoomL) {
//                                                ZoomL.removeAttribute('style');
//                                            }
//                                            console.log('>>pp: ', idElement);
//                                            jQuery('.controls').show();
//                                            if (msg) {
//                                                msg.hide();
//                                            }
//                                            jQuery('.print_icon').show();
//                                            // embElement.setAttribute('style', "visibility = visible;");
//                                        }
//
//                                    });
//
//                                    if (/*@cc_on!@*/false || !!document.documentMode) {
//                                        if (jQuery('#' + $(this).attr('id'))) {
//                                            panZoom = svgPanZoom('#' + $(this).attr('id'), {zoomEnabled: true, controlIconsEnabled: false});
//                                            panZoom.setWidth();
//                                            panZoom.disablePositionZoom();
//
//                                            var res = $(this).attr('id').split("_");
//                                            restoreLupa(res[2]);
//                                            restoreText(res[2]);
//
//
//                                            console.log(idElement);
//                                            if (msg) {
//                                                msg.hide();
//                                            }
//                                            jQuery('.print_icon').show();
//                                            jQuery('.controls').show();
//                                        }
//                                    }
//
//
//                                });
//                                clearInterval(interval);
//
//                            }
//                        }
//                , 2000);

            }




 

        </script>

</head>
<body></body>
</html>