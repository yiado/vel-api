<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>iGeo</title>
    <base href="<?=base_url(); ?>"/>
    <!-- ExtJs Library -->
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/resources/css/ext-all.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/treegrid/treegrid.css" rel="stylesheet" />

    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/adapter/ext/ext-base.js"></script>
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
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ux/DataView-more.js"></script>
    <!-- ExtJs Library -->

    <!-- Application Library and StyleSheets -->
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/base.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/document.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/fileuploadfield.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/MultiSelect.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/Spinner.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/FlashMessage.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/ux/GroupSummary.css" />

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
        App.Security.Session.system_current_date = null;


    </script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/node.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/nodetype.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/nodetypecategory.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/structuretree.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/topmenu.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/gui/interface_provider.js"></script>

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
    <script language="JavaScript" type="text/javascript">
<?
		// user actions
    	foreach ($gui_cfgs as $cfg => $cfg_value) {

    		echo "    " . $cfg . " = " . "'" . $cfg_value . "';" . PHP_EOL;
    	}

?>

	</script>

    <!-- Application Library and StyleSheets -->

</head>
<body></body>
</html>