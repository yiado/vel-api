<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>iGeo</title>
        <base href="<?= base_url(); ?>"/>
        <!-- ExtJs Library -->
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/resources/css/ext-all.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/treegrid/treegrid.css" rel="stylesheet" />

        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/adapter/ext/ext-base-debug.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ext-all-debug.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/locale/ext-lang-es.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/FileUploadField.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/ItemSelector.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/MultiSelect.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/XmlTreeLoader.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridSorter.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridColumnResizer.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridNodeUI.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridLoader.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridColumns.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGrid.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/Spinner.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/SpinnerField.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/Window.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/CheckColumn.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/ColorPaletteField.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/FlashMessage.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/GroupSummary.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/RowEditor.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/DataView-more.js"></script>

        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/extensible-all-debug.js"></script>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/calendar/resources/css/extensible-all.css"/>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/calendar/ext-lang-es.js"></script>


        <!-- ExtJs Library -->

        <!-- Application Library and StyleSheets -->
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style/default/base.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style/default/document.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style/default/fileuploadfield.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/MultiSelect.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/Spinner.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/RowEditor.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/FlashMessage.css"/>
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/GroupSummary.css" />
        <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style/modules/request/style.css"/>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/svg-pan-zoom.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/svgcontrol.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/base.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/store.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/language.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/preference.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/security.js"></script>
        <script language="JavaScript" type="text/javascript">
            <?php
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

                clearstatcache();
                echo "    App.BaseUrl = '" . base_url() . "';" . PHP_EOL;
                echo "    App.BaseIndex = '" . base_url() . index_page() . "';" . PHP_EOL;


                echo "App.Security.Session.user_username = '" . $session['user_username'] . "';" . PHP_EOL;
                echo "App.Security.Session.user_email = '" . $session['user_email'] . "';" . PHP_EOL;
            ?>
        </script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/security.js"></script>
        <script language="JavaScript" type="text/javascript">
            <?php
            // user actions
            foreach ($user_actions as $i => $action) {
                echo "    App.Security.Actions['" . $action['module_action_id'] . "'] = " . "'" . $action['module_action_id'] . "';" . PHP_EOL;
            }
            ?>
        </script>

        <!-- Dynamic JS Variable -->
        <script language="JavaScript" type="text/javascript">
            App.Security.Session.xml_permissions_file = '<?= @$session['xml_permissions_file']; ?>';
            App.Security.Session.user_type = '<?= $session['user_type']; ?>';
            App.Security.Session.user_id = <?= $session['user_id']; ?>;
            App.Security.Session.user_tree_full = <?= $session['user_tree_full']; ?>;
            App.Security.Session.user_path = '<?= (@$session['user_path'] ? $session['user_path'] : 'root'); ?>';
            App.Security.Session.user_default_module = '<?= (@$session['user_default_module'] ? $session['user_default_module'] : 'NULL'); ?>';
            App.Security.Session.system_current_date = null;
            App.ModuleSelect = '<?= (@$_SESSION['module'] ? $_SESSION['module'] : ''); ?>';
            App.Module = '<?= (@$_SESSION['moduleName'] ? $_SESSION['moduleName'] : ''); ?>';
        </script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/node.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/nodetype.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/nodetypecategory.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/structuretree.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/topmenu.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/gui/interface1.js"></script>
        <script defer  src="<?= base_url(); ?>javascript/application/font/fontawesome-all.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/pdfkit.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/blobstream.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/source.js"></script>
        <!-- Dynamic JS Variable -->

        <?php
            // user actions
            foreach ($gui_files as $module => $module_gui_files) {
            foreach ($module_gui_files as $file) {
                if (strpos($file, 'css') === false) {
                    if (strpos($file, 'http://') === false) {
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
        <?php
            // user actions
            foreach ($gui_cfgs as $cfg => $cfg_value) {
                echo "    " . $cfg . " = " . "'" . $cfg_value . "';" . PHP_EOL;
            }
        ?>

        </script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <!-- script src="https://code.highcharts.com/modules/export-data.js"></script>-->
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

                            var attributeEmb = embElement.getAttribute("src");
                            if (attributeEmb == 'plans/not_image_icon.png') {

                            }

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

                                if (App.Plan.HandlerZoom.length) {
                                    panZoom.pan({x: 0, y: 0});
                                    var svgElement = embElement.getSVGDocument();
                                    var path = svgElement.getElementById(App.Plan.HandlerZoom);
                                    var position = jQuery(path).offset();
                                    var parentOffset = jQuery('#' + idElement).offset();

                                    position.top -= parentOffset.top;
                                    position.left -= parentOffset.left;

                                    let width = jQuery('#' + idElement).width();
                                    let height = jQuery('#' + idElement).height();

                                    path.setAttribute("stroke", '#' + App.Plan.colorSection);
                                    path.setAttribute("stroke-width", '2px');

                                    panZoom.zoomAtPoint(1.5, {x: position.left, y: position.top + (height / 2)});
                                }



                            }, 2000);

                        }

                        clearInterval(interval);
                    }
                }, 2000);
            };

            $(document).ready(function () {

            intervalc = setInterval(function () {


                if (jQuery('.x-tree-ec-icon.x-tree-elbow-end-plus').attr('class') != '') {
                    jQuery('.x-tree-ec-icon.x-tree-elbow-end-plus').trigger('click');
                    clearInterval(intervalc);
                }
            }, 1000);

            $('.x-tool-expand-west').click(function () {

                if (App.Interface.activeModule == 'Iot') {


                    window.dispatchEvent(new Event('resize'));
                }
            });

            $('.x-tool-collapse-west').click(function () {

                if (App.Interface.activeModule == 'Iot') {


                    window.dispatchEvent(new Event('resize'));
                }
            });
        });
        </script>
    </head>
    <body></body>
</html>