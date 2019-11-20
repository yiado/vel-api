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
    'infra_info_m_terrero_escritura' => array('label' => 'infra_info_m_terrero_escritura'),
    'infra_info_m_terrero_escritura_total' => array('label' => 'infra_info_m_terrero_escritura_total'),
    'infra_info_m_terreno_cad' => array('label' => 'infra_info_m_terreno_cad'),
    'infra_info_m_terreno_cad_total' => array('label' => 'infra_info_m_terreno_cad_total'),
    'infra_info_m_construidos_ogcu' => array('label' => 'infra_info_m_construidos_ogcu'),
    'infra_info_m_construidos_ogcu_total' => array('label' => 'infra_info_m_construidos_ogcu_total'),
    'infra_info_uf_metros' => array('label' => 'infra_info_uf_metros'),
    'infra_info_uf_m_total' => array('label' => 'infra_info_uf_m_total'),
    'infra_info_m_emplazamiento' => array('label' => 'infra_info_m_emplazamiento'),
    'infra_info_p_m_emplazamiento' => array('label' => 'infra_info_p_m_emplazamiento'),
    'infra_info_m_calles' => array('label' => 'infra_info_m_calles'),
    'infra_info_p_m_calles' => array('label' => 'infra_info_p_m_calles'),
    'infra_info_m_areas_verdes' => array('label' => 'infra_info_m_areas_verdes'),
    'infra_info_p_m_areas_verdes' => array('label' => 'infra_info_p_m_areas_verdes'),
    'infra_info_m_areas_manejadas' => array('label' => 'infra_info_m_areas_manejadas'),
    'infra_info_p_m_areas_manejadas' => array('label' => 'infra_info_p_m_areas_manejadas'),
    'infra_info_m_patios_abiertos' => array('label' => 'infra_info_m_patios_abiertos'),
    'infra_info_p_m_patios_abiertos' => array('label' => 'infra_info_p_m_patios_abiertos'),
    'infra_info_m_recintos_deportivos_abiertos' => array('label' => 'infra_info_m_recintos_deportivos_abiertos'),
    'infra_info_p_m_recintos_deportivos_abiertos' => array('label' => 'infra_info_p_m_recintos_deportivos_abiertos'),
    'infra_info_m_circulaciones_abiertas' => array('label' => 'infra_info_m_circulaciones_abiertas'),
    'infra_info_p_m_circulaciones_abiertas' => array('label' => 'infra_info_p_m_circulaciones_abiertas'),
    'infra_info_m_otras_areas_abiertas' => array('label' => 'infra_info_m_otras_areas_abiertas'),
    'infra_info_p_m_otras_areas_abiertas' => array('label' => 'infra_info_p_m_otras_areas_abiertas'),
    'infra_info_n_estacionamientos' => array('label' => 'infra_info_n_estacionamientos'),
    'infra_info_m_neto_estacionamientos' => array('label' => 'infra_info_m_neto_estacionamientos'),
    'infra_info_m_sector_estacionamientos' => array('label' => 'infra_info_m_sector_estacionamientos'),
    'infra_info_p_m_sector_estacionamientos' => array('label' => 'infra_info_p_m_sector_estacionamientos')
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
