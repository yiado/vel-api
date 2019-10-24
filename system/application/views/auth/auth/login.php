<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>iGeo</title>
    <base href="<?=base_url(); ?>"/>
    <!-- ExtJs Library -->
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>javascript/extjs/resources/css/ext-all.css"/>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/adapter/ext/ext-base.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/ext-all-debug.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/extjs/locale/ext-lang-es.js"></script>
    <!-- ExtJs Library -->

    <!-- Application Library and StyleSheets -->
    <link rel="stylesheet" type="text/css" href="<?=base_url(); ?>style/default/base.css"/>

    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/base.js"></script>
    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/store.js"></script>

    <script type="text/javascript" src="<?=base_url(); ?>javascript/application/auth/login.js"></script>

    <script type="text/javascript">

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
    	
?>

    </script>
    <!-- Application Library and StyleSheets -->
    <style type="text/css">

  		body {
  			background-image:url('style/default/images/login_background.jpg');
  			background-position: center center;
  		}

	</style>


</head>
<body></body>
</html>