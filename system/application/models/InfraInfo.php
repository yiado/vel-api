<?php

/**
 * InfraInfo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 */
class InfraInfo extends BaseInfraInfo {

    var $allowListener = false;
    var $ufDelDia = null;

    function getUfDelDia() {
        $uf_del_dia = Doctrine_Core::getTable('Uf')->retrieveByDate(date("Y-m-d"));
        $this->ufDelDia = $uf_del_dia->uf_value;
    }

    function postInsert() {
        $this->postUpdate();
    }

    function postDelete() {
        $this->postUpdate();
    }

    function postUpdate() {
        if (!$this->allowListener) {
            return;
        }

        $this->getUfDelDia();

        $fieldMapping = array(
            'infra_info_area' => array(
                'formula' => 'SUM(infra_info_area +  infra_info_area_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_area_total'
            ),
            'infra_info_usable_area' => array(
                'formula' => 'SUM(infra_info_usable_area +  infra_info_usable_area_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_usable_area_total'
            ),
            'infra_info_volume' => array(
                'formula' => 'SUM(infra_info_volume +  infra_info_volume_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_volume_total'
            ),
            'infra_info_capacity' => array(
                'formula' => 'SUM(infra_info_capacity +  infra_info_capacity_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_capacity_total'
            ),
            'infra_info_terrain_area' => array(
                'formula' => 'SUM(infra_info_terrain_area +  infra_info_terrain_area_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_terrain_area_total'
            ),
            'infra_info_terrero_escritura' => array(
                'formula' => 'SUM(infra_info_terrero_escritura + infra_info_terrero_escritura_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_terrero_escritura_total'
            ),
            'infra_info_terreno_cad' => array(
                'formula' => 'SUM(infra_info_terreno_cad + infra_info_terreno_cad_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_terreno_cad_total'
            ),
            'infra_info_construidos_ogcu' => array(
                'formula' => 'SUM(infra_info_construidos_ogcu + infra_info_construidos_ogcu_total)',
                'accion' => 'suma',
                'campo_db' => 'infra_info_construidos_ogcu_total'
            ),
            'infra_info_uf' => array(
                'formula1' => "SUM((infra_info_terreno_cad * infra_info_uf) + infra_info_uf_total)",
                'formula2' => "SUM({$this->ufDelDia} * infra_info_uf * infra_info_terreno_cad)",
                'accion' => 'suma',
                'campo_db' => array(
                    0 => 'infra_info_uf_total',
                    1 => 'infra_info_money'
                )
            ),
            'infra_info_emplazamiento' => array(
                'formula' => 'SUM(infra_info_emplazamiento + infra_info_emplazamiento_total)',
                'formula2' => "SUM(infra_info_emplazamiento_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_emplazamiento_total',
                'campo_db2' => 'infra_info_emplazamiento_porcent'
            ),
            'infra_info_calles' => array(
                'formula' => 'SUM(infra_info_calles + infra_info_calles_total)',
                'formula2' => "SUM(infra_info_calles_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_calles_total',
                'campo_db2' => 'infra_info_porcent_calles'
            ),
            'infra_info_areas_verdes' => array(
                'formula' => "SUM(infra_info_areas_verdes + infra_info_areas_verdes_total)",
                'formula2' => "SUM(infra_info_areas_verdes_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_areas_verdes_total',
                'campo_db2' => 'infra_info_areas_verdes_porcent'
            ),
            'infra_info_areas_manejadas' => array(
                'formula' => "SUM(infra_info_areas_manejadas + infra_info_areas_manejadas_total)",
                'formula2' => "SUM(infra_info_areas_manejadas_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_areas_manejadas_total',
                'campo_db2' => 'infra_info_areas_manejadas_porcent'
            ),
            'infra_info_patios_abiertos' => array(
                'formula' => "SUM(infra_info_patios_abiertos + infra_info_patios_abiertos_total)",
                'formula2' => "SUM(infra_info_patios_abiertos_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_patios_abiertos_total',
                'campo_db2' => 'infra_info_patios_abiertos_porcent'
            ),
            'infra_info_recintos_deportivos' => array(
                'formula' => "SUM(infra_info_recintos_deportivos + infra_info_recintos_deportivos_total)",
                'formula2' => "SUM(infra_info_recintos_deportivos_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_recintos_deportivos_total',
                'campo_db2' => 'infra_info_recintos_deportivos_porcent'
            ),
            'infra_info_circulaciones_abiertas' => array(
                'formula' => "SUM(infra_info_circulaciones_abiertas + infra_info_circulaciones_abiertas_total)",
                'formula2' => "SUM(infra_info_circulaciones_abiertas_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_circulaciones_abiertas_total',
                'campo_db2' => 'infra_info_circulaciones_abiertas_porcent'
            ),
            'infra_info_otras_areas' => array(
                'formula' => "SUM(infra_info_otras_areas + infra_info_otras_areas_total)",
                'formula2' => "SUM(infra_info_otras_areas_total / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_otras_areas_total',
                'campo_db2' => 'infra_info_otras_areas_porcent'
            ),
            'infra_info_estacionamientos' => array(
                'formula' => "SUM(infra_info_estacionamientos + infra_info_estacionamientos_total_sector)",
                'formula2' => "SUM(infra_info_estacionamientos_total_sector / infra_info_terreno_cad_total * 100)",
                'accion' => 'porcentaje',
                'campo_db' => 'infra_info_estacionamientos_total_sector',
                'campo_db2' => 'infra_info_estacionamientos_porcent'
            ),
            'infra_info_estacionamientos_num' => array(
                'formula' => "SUM( (2 * 5 * infra_info_estacionamientos_num) + infra_info_estacionamientos_total)",
                'accion' => 'suma',
                'campo_db' => 'infra_info_estacionamientos_total'
            )
        );

        $this->guardarCambios($fieldMapping);
    }

    function guardarCambios($fieldMapping) {
        $node = Doctrine_Core::getTable('Node')->find($this->node_id)->getNode();
        $ancestros = array_reverse($node->getAncestors()->toArray());

        if ($node->hasParent()) {
            foreach ($ancestros as $ancestor) {
                $ancestorInfo = Doctrine_Core::getTable('InfraInfo')->findByNodeId($ancestor['node_id']);
                if ($ancestorInfo === false) {
                    $ancestorInfo = new InfraInfo();
                    $ancestorInfo->node_id = $ancestor['node_id'];
                }

                foreach ($fieldMapping as $val) {
                    if (is_array($val['campo_db'])) {
                        $ancestorInfo->{$val['campo_db'][0]} = Doctrine_Core::getTable('InfraInfo')->getSumatoria($ancestor['node_id'], $val['formula1']);
                        $ancestorInfo->{$val['campo_db'][1]} = Doctrine_Core::getTable('InfraInfo')->getSumatoria($ancestor['node_id'], $val['formula2']);
                    } else {
                        $ancestorInfo->{$val['campo_db']} = Doctrine_Core::getTable('InfraInfo')->getSumatoria($ancestor['node_id'], $val['formula']);
                    }
                    $ancestorInfo->save();
                    if ($val['accion'] === 'porcentaje') {
                        $ancestorInfo->{$val['campo_db2']} = Doctrine_Core::getTable('InfraInfo')->getPorcentaje($ancestor['node_id'], $val['formula2']);
                        $ancestorInfo->save();
                    }
                }
            }
        }
    }

    function actualizarValorNodoUTFSM() {
        $this->getUfDelDia();

        $fieldMapping = array(
            'formula1' => "SUM((infra_info_terreno_cad * infra_info_uf) + infra_info_uf_total)",
            'formula2' => "SUM({$this->ufDelDia} * infra_info_uf * infra_info_terreno_cad)",
            'accion' => 'suma',
            'campo_db' => array(
                0 => 'infra_info_uf_total',
                1 => 'infra_info_money'
            )
        );
        $this->guardarCambios($fieldMapping);
    }

}
