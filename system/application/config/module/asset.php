<?php

$config['asset_gui_files'] = array(
    '/application/asset/base.js',
    '/application/asset/store.js',
    '/application/asset/interface_otherdata.js',
    '/application/asset/interface_document.js',
    '/application/asset/interface_insurance.js',
    '/application/asset/interface_measurement.js',
    '/application/asset/interface_log.js',
    '/application/asset/interface_inventory.js',
    '/application/asset/interface_masiva_excel.js',
    '/application/asset/interface.js'
);

$config['asset_doc_dir'] = './asset_doc/';

$config['asset_gui_confgs'] = array(
    //'asset_export_plancheta' 	=> 'index.php/asset/assetplancheta/exportPlancheta/'
    'asset_export_plancheta' => 'index.php/asset/assetuchileplancheta/exportPlancheta/',
    'asset_export_plancheta_nivel' => 'index.php/asset/assetuchileplancheta/exportPlanchetaNivel/',
    'asset_export_listado_folio' => 'index.php/asset/assetuchilelistadofoliocontroller/exportListadoFolio/'
);
