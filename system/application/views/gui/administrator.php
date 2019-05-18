<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>iGeo</title>
	<base href="<?= base_url(); ?>"/>
	<!-- ExtJs Library -->
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/resources/css/ext-all.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/treegrid/treegrid.css" rel="stylesheet" />

	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/adapter/ext/ext-base.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ext-all-debug.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/locale/ext-lang-es.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/FileUploadField.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/ItemSelector.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/MultiSelect.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridSorter.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridColumnResizer.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridNodeUI.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridLoader.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGridColumns.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/treegrid/TreeGrid.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/extjs/ux/FlashMessage.js"></script>

	<!-- ExtJs Library -->

	<!-- Application Library and StyleSheets -->
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style/default/base.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style/default/fileuploadfield.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/MultiSelect.css"/>
	<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>javascript/extjs/ux/FlashMessage.css"/>

	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/base.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/store.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/language.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/preference.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/security.js"></script>
        <script  type="text/javascript" src="<?= base_url(); ?>javascript/jquery-3.3.1.min.js" ></script>
	<script language="JavaScript" type="text/javascript">



<?
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

foreach ($user_modules as $i => $module) {

    echo '    App.UserModules[' . $i . '] = ' . "'" . $module . "';" . PHP_EOL;

    $last_module_position = $i;
}

$modules = array();
foreach ($user_modules_gui as $module) {

    $modules[$module->module_namespace] = $module->module_namespace;

}



//Modulos del core
//No se incluye el core: ya que está compuesto por; language, users, permissions.
$last_module_position++;
echo "    App.UserModules[" . $last_module_position . "] = 'language';" . PHP_EOL;
$last_module_position++;
echo "    App.UserModules[" . $last_module_position . "] = 'users';" . PHP_EOL;
$last_module_position++;
echo "    App.UserModules[" . $last_module_position . "] = 'permissions';" . PHP_EOL;
$last_module_position++;
echo "    App.UserModules[" . $last_module_position . "] = 'general';" . PHP_EOL;

echo "App.BaseUrl = '" . base_url() . "';" . PHP_EOL;

echo "App.Security.Session.user_username = '" . $session['user_username'] . "';" . PHP_EOL;



foreach ($user_modules_menu as $i => $moduleMenu) {

    		echo '    App.UserModulesMenu[' . $i . '] = ' . "'" . $moduleMenu . "';" . PHP_EOL;
    	}
?>



</script>


	<script language="JavaScript" type="text/javascript">

	    App.Security.Session.xml_permissions_file = '<? echo @$session['xml_permissions_file']; ?>';
	    App.Security.Session.user_type = '<? echo $session['user_type']; ?>';
	    App.Security.Session.user_id = <? echo $session['user_id']; ?>;

	</script>

	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/topmenu.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/nodetypecategory.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/nodetype.js"></script>

	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/base.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface.js"></script>

	<?php if (!empty($modules['Plan'])) : ?>

    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/plan/base.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/plan/store.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_planimetry.js"></script>

	<?php endif;

	if (!empty($modules['Asset'])) : ?>

    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/asset/base.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/asset/store.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_assets.js"></script>

	<?php endif;

	if (!empty($modules['Infrastructure'])) : ?>

    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/infrastructure/base.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/infrastructure/store.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_infrastructure.js"></script>

	<?php endif;

	if (!empty($modules['Maintenance'])) : ?>

    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/mtn/base.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/mtn/store.js"></script>
            <script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_maintenance.js"></script>

	<?php endif;

	if (!empty($modules['Document'])) : ?>

    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/doc/base.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/doc/store.js"></script>
    	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_document.js"></script>

	<?php endif;

	if( !empty($modules['Request'])) : ?>
	
		<script type="text/javascript" src="<?= base_url(); ?>javascript/application/request/base.js"></script>
		<script type="text/javascript" src="<?= base_url(); ?>javascript/application/request/store.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_request.js"></script>

	<?php endif; 
        
    if( !empty($modules['Costs'])) : ?>
	
		<script type="text/javascript" src="<?= base_url(); ?>javascript/application/costs/base.js"></script>
		<script type="text/javascript" src="<?= base_url(); ?>javascript/application/costs/store.js"></script>
                <script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_costs.js"></script>

	<?php endif;

    if( !empty($modules['InfraMaintenance'])) : ?>
		<script type="text/javascript" src="<?= base_url(); ?>javascript/application/inframtn/base.js"></script>
		<script type="text/javascript" src="<?= base_url(); ?>javascript/application/inframtn/store.js"></script>
        <script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_inframtn.js"></script>

	<?php endif; ?>

	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_report.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_permission.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_general.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_users.js"></script>
	<script type="text/javascript" src="<?= base_url(); ?>javascript/application/maintainers/interface_language.js"></script>

	<!-- Application Library and StyleSheets -->

        
        <?
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
    </head>
    <body></body>
</html>