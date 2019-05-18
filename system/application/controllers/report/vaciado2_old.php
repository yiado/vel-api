<?php

/**
 * @package Controller
 * @subpackage ReportController
 */
class vaciado2 extends APP_Controller {

    function vaciado1() {
        parent :: APP_Controller();
    }

    /**
     * 
     * Lista todos los reportes del sistema
     */
    function index() {

        $node_id = $this->input->post('node_id');
        switch ($this->input->post('output_type')) {
            case 'e' :
                $this->exportListExcel($node_id);
                break;
        }
    }

    function exportListExcelOld($node_id = null) {
        $time_start = microtime(true);

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

        if ($node_id != 'root') {

            $node = Doctrine_Core::getTable('Node')->find($node_id);

            $q = Doctrine_Query::create()
                    ->select('n.node_id, n.node_type_id')
                    ->from('Node n')
                    ->innerJoin('n.NodeType nt')
                    ->where('.node_parent_id = ?', $node->node_parent_id)
                    ->andWhere('nt.node_type_category_id = 2')
                    ->andWhere('n.lft > ?', $node->lft)
                    ->andWhere('n.rgt < ?', $node->rgt);

            $resuls = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        } else {
            $resuls = '';
        }

        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query::create()
                ->select('n.node_id, n.node_type_id')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->where('.node_parent_id = ?', $node->node_parent_id)
                ->andWhere('nt.node_type_category_id = 2')
                ->andWhere('n.lft > ?', $node->lft)
                ->andWhere('n.rgt < ?', $node->rgt);

        $resuls = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
//        print_r($resuls);exit();

        if ((!empty($resuls)) && (!is_null($resuls))) {
            $rcont = 1;
            $ccont = 0;
            $lastParentId = null;
            foreach ($resuls as $node) {
                $rcont ++;
                $ccont = 0;
                $nodeTree = Doctrine_Core::getTable('Node')->find($node['node_id'])->getNode();

                if ($nodeTree->hasParent() && $nodeTree->getParent()->node_id != $lastParentId) {
                    if (isset($ancestors))
                        $ancestors->free(true);
                    $ancestors = $nodeTree->getAncestors();
                }

                if ($nodeTree->hasParent()) {
                    $lastParentId = $nodeTree->getParent()->node_id;
                }
                // padres del nodo
                foreach ($ancestors as $ancestor) {
                    // datos otros
                    $attributes[1] = array(354, 364);
                    $attributes[23] = array(346, 348, 350, 351);
                    $attributes[2] = array(308, 310, 311, 312, 313, 314, 315, 316);
                    $attributes[29] = array(346, 348, 350, 351);

                    if (isset($attributes[$ancestor->node_type_id])) {

                        foreach ($attributes[$ancestor->node_type_id] as $att) {

                            $att = Doctrine_Core::getTable('InfraOtherDataAttribute')->find($att);
                            $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($ancestor->node_id, $att->infra_other_data_attribute_id);
                            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont ++);

                            if ($rcont == 2) {
                                $sheet->setCellValue($columnLetter . '1', $att->infra_other_data_attribute_name);
                            }

                            if ($value && $att->infra_other_data_attribute_type == 5 && $value->infra_other_data_option_id != null) {
                                $sheet->setCellValue($columnLetter . $rcont, Doctrine_Core::getTable('InfraOtherDataOption')->find($value->infra_other_data_option_id)->infra_other_data_option_name);
                            } else if ($value && $att->infra_other_data_attribute_type != 5 && $value != '') {
                                $sheet->setCellValue($columnLetter . $rcont, $value->infra_other_data_value_value);
                            } else if (!$value) {
                                $sheet->setCellValue($columnLetter . $rcont, '');
                            }
                        }
                    }

                    // datos infraestructurales
                    $infraConfig[1] = array('infra_info_terrain_area_total', 'infra_info_area_total', 'infra_info_usable_area_total');
                    $infraConfig[23] = array('infra_info_terrain_area', 'infra_info_area_total', 'infra_info_usable_area_total');

                    if (isset($infraConfig[$ancestor->node_type_id])) {

                        $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($ancestor->node_id);
                        foreach ($infraConfig[$ancestor->node_type_id] as $config) {
                            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont ++);
                            if ($rcont == 2) {
                                $sheet->setCellValue($columnLetter . '1', $this->translateTag('Infrastructure', $config));
                            }
                            $sheet->setCellValue($columnLetter . $rcont, (($info) ? $info->{$config} : ''));
                        }
                    }
                }

                // recinto
                // datos otros
                $attributesrecinto = array(6, 13, 15, 17, 25, 26, 27, 28, 29, 30, 31, 32);
                foreach ($attributesrecinto as $att) {

                    $att = Doctrine_Core::getTable('InfraOtherDataAttribute')->find($att);
                    $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node['node_id'], $att->infra_other_data_attribute_id);
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont ++);
                    if ($rcont == 2) {
                        $sheet->setCellValue($columnLetter . '1', $att->infra_other_data_attribute_name);
                    }

                    if ($value && $att->infra_other_data_attribute_type == 5 && $value->infra_other_data_option_id != null) {
                        $sheet->setCellValue($columnLetter . $rcont, Doctrine_Core::getTable('InfraOtherDataOption')->find($value->infra_other_data_option_id)->infra_other_data_option_name);
                    } else if ($value && $att->infra_other_data_attribute_type != 5 && $value != '') {
                        $sheet->setCellValue($columnLetter . $rcont, $value->infra_other_data_value_value);
                    } else if (!$value) {
                        $sheet->setCellValue($columnLetter . $rcont, '');
                    }
                }

                // datos infraestructurales
                $infraConfig = array('infra_info_usable_area');
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node['node_id']);

                foreach ($infraConfig as $config) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont ++);
                    if ($rcont == 2) {
                        $sheet->setCellValue($columnLetter . '1', $this->translateTag('Infrastructure', $config));
                    }

                    $sheet->setCellValue($columnLetter . $rcont, (($info) ? $info->{$config} : ''));
                }
            }

            $sheet->getStyle('A1:' . $columnLetter . '1')->getFont()->applyFromArray(array(
                'bold' => true
            ));

            $sheet->getStyle('A1:' . $columnLetter . '' . $rcont)->getBorders()->applyFromArray(array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '808080'
                    )
                )
            ));

            $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
            $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            echo '{"success": true, "exe_seconds": "' . $time . '", "memory_usage" : "' . memory_get_usage() . '", "file": "' . $this->input->post('file_name') . '.xls"}';
        } else {
            echo '{"success": false, "msg": "Sin información","exe_seconds": "", "memory_usage" : "", "file": ""}';
        }
    }
    
    
        function exportListExcel($node_id = null) {

        $time_start = microtime(true);
        $colorConfig = array(
            28 => 'CD853F',
            1 => 'E0B0FF',
            23 => 'FFFF00',
            2 => '32CD32',
            3 => 'DC143C',
            29 => '04B4AE'
        );

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

//        $node_id = 3528;


        if ($node_id != 'root') {
            //se busca la informacion del nodo
            $node = Doctrine_Core::getTable('Node')->find($node_id);

            $queryChild = $this->db->query("
            SELECT node_id FROM (
            (SELECT (SELECT count(*) FROM node r WHERE r.lft>=nd.lft and r.rgt <=nd.rgt and level=5) as total
            , node_id FROM node nd WHERE lft<=" . $node->lft . " and rgt >=" . $node->rgt . " and level<=5 and level>0 )
            UNION ALL 
            (
            SELECT (SELECT count(*) FROM node r WHERE r.lft>=nd.lft and r.rgt <=nd.rgt and level=5) as total
            , node_id FROM node nd WHERE lft>=" . $node->lft . " and rgt <=" . $node->rgt . " and level<=5 and level>0)
            ) as temp
            where total>0                
            ");

            $data_all = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();
            $flat = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($data_all)), 0);
            $flat = implode(",", $flat);
//            echo "<pre>";
//            var_dump("(SELECT 
//                nd.node_id,
//                nd.node_type_id as node_type,
//                nd.level as level_node, 
//                nd.lft as lft_node ,
//                nd.rgt as rgt_node, 
//                (SELECT t.language_tag_value FROM language_tag t INNER JOIN module m on t.module_id=m.module_id and m.module_namespace='Infrastructure'
//                where t.language_tag_tag=ic.infra_attribute) as infra_attribute,
//                NULL AS infra_other_data_attribute_node_type_order,
//                NULL AS data_option,
//                ic.infra_configuration_id AS infra_info_id,
//                ic.infra_attribute  as value
//                FROM infra_configuration ic 
//                INNER JOIN node_type nt ON nt.node_type_id= ic.node_type_id
//                INNER JOIN node nd ON nd.node_type_id= nt.node_type_id and nd.node_id in (" . $flat . ")
//                where ((nd.node_type_id=1 and ic.infra_attribute in ('infra_info_terrain_area_total', 'infra_info_area_total', 'infra_info_usable_area_total'))
//                or (nd.node_type_id=23 and ic.infra_attribute in ('infra_info_terrain_area', 'infra_info_area_total', 'infra_info_usable_area_total'))
//                or (nt.node_type_category_id=2 and ic.infra_attribute in ('infra_info_usable_area')))
//                )
//                UNION ALL                
//                ( select 
//                nd.node_id, nd.node_type_id as node_type, nd.level as level_node,
//                nd.lft as lft_node,
//		nd.rgt as rgt_node,
//                atb.infra_other_data_attribute_name as infra_attribute,
//                ont.infra_other_data_attribute_node_type_order , dv.infra_other_data_option_id as data_option ,
//                NULL AS infra_info_id,
//                if(atb.infra_other_data_attribute_type = 5, ido.infra_other_data_option_name,dv.infra_other_data_value_value) as value
//                from infra_other_data_attribute_node_type ont
//                inner join node_type nt on nt.node_type_id = ont.node_type_id
//                inner join node nd on nd.node_type_id= nt.node_type_id
//                inner join infra_other_data_attribute atb on atb.infra_other_data_attribute_id= ont.infra_other_data_attribute_id
//                left join infra_other_data_value dv on dv.infra_other_data_attribute_id=atb.infra_other_data_attribute_id 
//              	and dv.node_id=nd.node_id
//              	left join infra_other_data_option ido on ido.infra_other_data_option_id = dv.infra_other_data_option_id
//                where nd.node_id in (" . $flat . ")
//                and ((nd.node_type_id= 1 and atb.infra_other_data_attribute_id in (354, 364)) or 
//                (nd.node_type_id= 23 and atb.infra_other_data_attribute_id in (346, 348, 350, 351)) or 
//                (nd.node_type_id= 2 and atb.infra_other_data_attribute_id in (308, 310, 311, 312, 313, 314, 315, 316)) OR
//              	(nd.node_type_id= 29 and atb.infra_other_data_attribute_id in (346, 348, 350, 351)) or 
//              	(nt.node_type_category_id=2 and atb.infra_other_data_attribute_id in (6, 13, 15, 17, 25, 26, 27, 28, 29, 30, 31, 32) ))
//                )
//                ORDER BY  lft_node ASC ,level_node desc,infra_info_id ASC, infra_other_data_attribute_node_type_order ASC
//                 ");
//              echo "</pre>";
            $queryChild = $this->db->query("
                (SELECT 
                nd.node_id,
                nd.node_type_id as node_type,
                nd.level as level_node, 
                nd.lft as lft_node ,
                nd.rgt as rgt_node, 
                (SELECT t.language_tag_value FROM language_tag t INNER JOIN module m on t.module_id=m.module_id and m.module_namespace='Infrastructure'
                where t.language_tag_tag=ic.infra_attribute) as infra_attribute,
                NULL AS infra_other_data_attribute_node_type_order,
                NULL AS data_option,
                ic.infra_configuration_id AS infra_info_id,
                ic.infra_attribute  as value
                FROM infra_configuration ic 
                INNER JOIN node_type nt ON nt.node_type_id= ic.node_type_id
                INNER JOIN node nd ON nd.node_type_id= nt.node_type_id and nd.node_id in (" . $flat . ")
                where ((nd.node_type_id=1 and ic.infra_attribute in ('infra_info_terrain_area_total', 'infra_info_area_total', 'infra_info_usable_area_total'))
                or (nd.node_type_id=23 and ic.infra_attribute in ('infra_info_terrain_area', 'infra_info_area_total', 'infra_info_usable_area_total'))
                or (nt.node_type_category_id=2 and ic.infra_attribute in ('infra_info_usable_area')))
                )
                UNION ALL                
                ( select 
                nd.node_id, nd.node_type_id as node_type, nd.level as level_node,
                nd.lft as lft_node,
		nd.rgt as rgt_node,
                atb.infra_other_data_attribute_name as infra_attribute,
                ont.infra_other_data_attribute_node_type_order , dv.infra_other_data_option_id as data_option ,
                NULL AS infra_info_id,
                if(atb.infra_other_data_attribute_type = 5, ido.infra_other_data_option_name,dv.infra_other_data_value_value) as value
                from infra_other_data_attribute_node_type ont
                inner join node_type nt on nt.node_type_id = ont.node_type_id
                inner join node nd on nd.node_type_id= nt.node_type_id
                inner join infra_other_data_attribute atb on atb.infra_other_data_attribute_id= ont.infra_other_data_attribute_id
                left join infra_other_data_value dv on dv.infra_other_data_attribute_id=atb.infra_other_data_attribute_id 
              	and dv.node_id=nd.node_id
              	left join infra_other_data_option ido on ido.infra_other_data_option_id = dv.infra_other_data_option_id
                where nd.node_id in (" . $flat . ")
                and ((nd.node_type_id= 1 and atb.infra_other_data_attribute_id in (354, 364)) or 
                (nd.node_type_id= 23 and atb.infra_other_data_attribute_id in (346, 348, 350, 351)) or 
                (nd.node_type_id= 2 and atb.infra_other_data_attribute_id in (308, 310, 311, 312, 313, 314, 315, 316)) OR
              	(nd.node_type_id= 29 and atb.infra_other_data_attribute_id in (346, 348, 350, 351)) or 
              	(nt.node_type_category_id=2 and atb.infra_other_data_attribute_id in (6, 13, 15, 17, 25, 26, 27, 28, 29, 30, 31, 32) ))
                )
                ORDER BY  lft_node ASC ,level_node desc,infra_info_id ASC, infra_other_data_attribute_node_type_order ASC
                ");
            $data_all = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();

//            echo "<pre>";
//            var_dump($data_all);
//            echo "</pre>";
//            exit();

            $reg = $level_ = $ultimo_id = 0;
            $level_t = false;
            $valores = array();


            $queryChild = $this->db->query("select * from infra_info where node_id in (" . $flat . ")");
            $row = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();

            foreach ($row as $data) {
                $valores['ID_' . $data['node_id']] = $data;
            }

            foreach ($data_all as $data) {

                $labelP = $data['infra_attribute'];
                $color = isset($colorConfig[$data['node_type']]) ? $colorConfig[$data['node_type']] : 'd9e5f4';

                if (!isset($label) || !in_array($labelP . "@@" . $color . "@@" . $data['level_node'], $label)) {
                    $label[] = $labelP . '@@' . $color . '@@' . $data['level_node'];
                }

                if ($data['level_node'] == 5 && !$level_t) {
                    $level_t = true;
                    $ultimo_id = $data['node_id'];
                }

                if (!empty($data['value']) && !empty($data['infra_info_id'])) {

                    if (isset($valores['ID_' . $data['node_id']])) {
                        $valor = $valores['ID_' . $data['node_id']][$data['value']];
                    } else {

                        $valor = '';
                    }
                } else {
                    $valor = $data['value'];
                }

//                echo $data['level_node']."<br>"
                if ($level_t && $ultimo_id != $data['node_id']) {
//                  
                    $Data_exel['reg_' . $reg] = $level;
//                    $nodes['lev_' . $data['level_node']] = $data['node_id'];
                    $level['lev_' . $data['level_node']][$labelP] = $valor;
                    $level['lev_' . $data['level_node']]['node_id'] = $data['node_id'];

                    $level_t = false;
                    $reg++;
                } else {
//                    $nodes['lev_' . $data['level_node']] = $data['node_id'];
                    $level['lev_' . $data['level_node']][$labelP] = $valor;
                    $level['lev_' . $data['level_node']]['node_id'] = $data['node_id'];
                    $Data_exel['reg_' . $reg] = $level;
//                    $Data_exel['reg_' . $reg]['nodes'] = $nodes;
                }
            }

//            echo "<pre>";
//            var_dump($label);
//            echo "</pre>";
//            exit();

            $ccont = 0;
            foreach ($label as $key => $data2) {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont++);
                $data = explode('@@', $data2);
                $sheet->setCellValue($columnLetter . '1', $data[0]);
//                $sheet->getStyle($columnLetter . '1')->getFill()->applyFromArray(array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array(
//                        'rgb' => $data[1]
//                    )
//                ));
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
            }



            $fila = 2;
            $level = array();
            foreach ($Data_exel as $reg) {
                $ccont = 0;
                for ($i = 1; $i <= 5; $i++) {

                    if (isset($reg['lev_' . $i])) {
                        $level[] = $reg['lev_' . $i]['node_id'];
                        foreach (preg_grep('/[@@' . $i . ']$/', $label) as $key => $data2) {
                            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont++);
                            $data = explode('@@', $data2);

                            if (isset($reg['lev_' . $i][$data[0]])) {
                                $sheet->setCellValue($columnLetter . $fila, "" . $reg['lev_' . $data[2]][$data[0]]);
                            } else {
                                $sheet->setCellValue($columnLetter . $fila, "");
                            }
                        }
                    }


                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                }
                $fila++;
            }




            $sheet->getStyle('A1:' . $columnLetter . '1')->getFont()->applyFromArray(array(
                'bold' => true
            ));

//            $sheet->getStyle('A1:' . $columnLetter . '' . $rcont)->getBorders()->applyFromArray(array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN,
//                    'color' => array(
//                        'rgb' => '808080'
//                    )
//                )
//            ));

            $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
            $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            echo '{"success": true, "exe_seconds": "' . $time . '", "memory_usage" : "' . memory_get_usage() . '", "file": "' . $this->input->post('file_name') . '.xls"}';

//            exit();
        } else {
            echo '{"success": false, "msg": "Sin información","exe_seconds": "", "memory_usage" : "", "file": ""}';
        }
    }

}
