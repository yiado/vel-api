<?php

//Directorio temporal para los files generados por el sistema
$config['temp_dir'] = FCPATH . 'temp/';

//Directorio para almacenar los archivos svg de los planos
$config['plan_dir'] = FCPATH . 'plans/';

//App Style Dir
$config['app_style_dir'] = 'style/default/';

//Directorio para almacenar los iconos de los nodos
$config['node_icon_dir'] = FCPATH . 'style/node_icon/';

//Directoriod de los iconos de los nodos (url)
$config['node_icon_url'] = 'style/node_icon/';

//Directoriod del logo de la empresa (url)
$config['company_logo'] = 'style/default/images/escudo_Uchile/escudo-uchile-horizontal-negro-fondo-transp.png';

//Directorio para almacenar los documentos
$config['doc_dir'] = FCPATH . 'docs/';

//Directoriod de los iconos de los nodos (url)
$config['plan_default_layer_color'] = 'FF0000';

// default currency
$config['default_currency'] = 1;

//Parametros autenticacion
$config['auth_require_login'] = true;
$config['auth_engine'] = 'authigeo';
//        $config['auth_engine']  		= 'authldap';

$config['listener_config'] = array(
    'Node' => array(
        'InfraInfoListener'
    ),
    'WorkOrder' => array(
        'AssetMeasurementTriggerListener'
    )
);

//Arreglo de etiquetas para Datos Estructurales
$config['fields_infra_info'] = array(
    'infra_info_usable_area' => array('label' => 'infra_info_usable_area'),
    'infra_info_usable_area_total' => array('label' => 'infra_info_usable_area_total'),
    'infra_info_area' => array('label' => 'infra_info_area'),
    'infra_info_area_total' => array('label' => 'infra_info_area_total'),
    'infra_info_volume' => array('label' => 'infra_info_volume'),
    'infra_info_volume_total' => array('label' => 'infra_info_volume_total'),
    'infra_info_length' => array('label' => 'infra_info_length'),
    'infra_info_width' => array('label' => 'infra_info_width'),
    'infra_info_height' => array('label' => 'infra_info_height'),
    'infra_info_capacity' => array('label' => 'infra_info_capacity'),
    'infra_info_capacity_total' => array('label' => 'infra_info_capacity_total'),
    'infra_info_terrain_area' => array('label' => 'infra_info_terrain_area'),
    'infra_info_terrain_area_total' => array('label' => 'infra_info_terrain_area_total'),
    'infra_info_additional_1' => array('label' => 'infra_info_additional_1'),
    'infra_info_additional_2' => array('label' => 'infra_info_additional_2'),
    'infra_info_additional_3' => array('label' => 'infra_info_additional_3'),
    'infra_info_additional_4' => array('label' => 'infra_info_additional_4'),
    'infra_info_option_id_1' => array('label' => 'infra_info_option_id_1'),
    'infra_info_option_id_2' => array('label' => 'infra_info_option_id_2'),
    'infra_info_option_id_3' => array('label' => 'infra_info_option_id_3'),
    'infra_info_option_id_4' => array('label' => 'infra_info_option_id_4')
);

$config['user_system'] = array(
    'user_id' => 1,
    'username' => 'system'
);
