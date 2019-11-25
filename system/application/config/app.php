<?php

//Path QR Code
$config['qr_dir'] = 'temp/qrcode/';

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
    'infra_info_terrero_escritura' => array('label' => 'infra_info_terrero_escritura'),
    'infra_info_terrero_escritura_total' => array('label' => 'infra_info_terrero_escritura_total'),
    'infra_info_terreno_cad' => array('label' => 'infra_info_terreno_cad'),
    'infra_info_terreno_cad_total' => array('label' => 'infra_info_terreno_cad_total'),
    'infra_info_construidos_ogcu' => array('label' => 'infra_info_construidos_ogcu'),
    'infra_info_construidos_ogcu_total' => array('label' => 'infra_info_construidos_ogcu_total'),
    'infra_info_uf' => array('label' => 'infra_info_uf'),
    'infra_info_uf_total' => array('label' => 'infra_info_uf_total'),
    'infra_info_emplazamiento' => array('label' => 'infra_info_emplazamiento'),
    'infra_info_emplazamiento_total' => array('label' => 'infra_info_emplazamiento_total'),
    'infra_info_emplazamiento_porcent' => array('label' => 'infra_info_emplazamiento_porcent'),
    'infra_info_calles' => array('label' => 'infra_info_calles'),
    'infra_info_calles_total' => array('label' => 'infra_info_calles_total'),
    'infra_info_porcent_calles' => array('label' => 'infra_info_porcent_calles'),
    'infra_info_areas_verdes' => array('label' => 'infra_info_areas_verdes'),
    'infra_info_areas_verdes_total' => array('label' => 'infra_info_areas_verdes_total'),
    'infra_info_areas_verdes_porcent' => array('label' => 'infra_info_areas_verdes_porcent'),
    'infra_info_areas_manejadas' => array('label' => 'infra_info_areas_manejadas'),
    'infra_info_areas_manejadas_total' => array('label' => 'infra_info_areas_manejadas_total'),
    'infra_info_areas_manejadas_porcent' => array('label' => 'infra_info_areas_manejadas_porcent'),
    'infra_info_patios_abiertos' => array('label' => 'infra_info_patios_abiertos'),
    'infra_info_patios_abiertos_total' => array('label' => 'infra_info_patios_abiertos_total'),
    'infra_info_patios_abiertos_porcent' => array('label' => 'infra_info_patios_abiertos_porcent'),
    'infra_info_recintos_deportivos' => array('label' => 'infra_info_recintos_deportivos'),
    'infra_info_recintos_deportivos_total' => array('label' => 'infra_info_recintos_deportivos_total'),
    'infra_info_recintos_deportivos_porcent' => array('label' => 'infra_info_recintos_deportivos_porcent'),
    'infra_info_circulaciones_abiertas' => array('label' => 'infra_info_circulaciones_abiertas'),
    'infra_info_circulaciones_abiertas_total' => array('label' => 'infra_info_circulaciones_abiertas_total'),
    'infra_info_circulaciones_abiertas_porcent' => array('label' => 'infra_info_circulaciones_abiertas_porcent'),
    'infra_info_otras_areas' => array('label' => 'infra_info_otras_areas'),
    'infra_info_otras_areas_total' => array('label' => 'infra_info_otras_areas_total'),
    'infra_info_otras_areas_porcent' => array('label' => 'infra_info_otras_areas_porcent'),
    'infra_info_estacionamientos_num' => array('label' => 'infra_info_estacionamientos_num'),
    'infra_info_estacionamientos_total' => array('label' => 'infra_info_estacionamientos_total'),
    'infra_info_estacionamientos' => array('label' => 'infra_info_estacionamientos'),
    'infra_info_estacionamientos_total_sector' => array('label' => 'infra_info_estacionamientos_total_sector'),
    'infra_info_estacionamientos_porcent' => array('label' => 'infra_info_estacionamientos_porcent')
);

$config['user_system'] = array(
    'user_id' => 1,
    'username' => 'system'
);

$config['bimapi'] = array(
    'base_url' => 'https://bimapi.velociti.cl/',
    'credenciales' => array(
        'user' => 'bim_igeo',
        'password' => 'wjsNDHE2h8k9tFvd'
    )
);

$config['api_uf_sbif'] = array(
    'base_url' => 'http://api.sbif.cl/api-sbifv3/recursos_api/uf/posteriores',
    'key' => 'f5be4d9b0a11b3941c2083ba4e6321fc789eba3e',
    'formato' => 'json'
);
