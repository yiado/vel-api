<?php
error_reporting(0);
/**
 * @package Controller
 * @subpackage ReportController
 */
class informeMineduc extends APP_Controller {

    function informeMineduc() {
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

    function exportListExcel($node_id = null) {
        //EN CASO DE QUE LAS VARIBLES NO HAYAN SIDO CREADAS
        $region = '';
        $ciudad = '';
        $comuna = '';
        $direccion = '';
        $nombre_inmueble = '';
        $situacion_tenencia = '';
        $ano_construccion = '';
        $ano_adquisicion = '';
        $actividad = '';

        //SE DECLARAN LAS VARIABLES A UTILIZAR
        $sum_total_metros_salas = 0;
        $sum_total_metros_auditorios = 0;
        $sum_total_metros_laboratorios = 0;
        $sum_total_metros_talleres = 0;
        $count_salas = 0;
        $count_auditorios = 0;
        $count_laboratorios = 0;
        $count_talleres = 0;
        $capacidad_total_salas = 0;
        $capacidad_total_auditorios = 0;
        $capacidad_total_laboratorios = 0;
        $capacidad_total_talleres = 0;

        if ($node_id != 'root') {
            $nodeRaiz = Doctrine_Core::getTable('Node')->find($node_id);
        } else {
            $nodeRaiz = '';
        }

        if ((!empty($nodeRaiz)) && (!is_null($nodeRaiz))) {

            if ($nodeRaiz->node_type_id == 2) {
                $time_start = microtime(true);
                ini_set('memory_limit', '2048M');
                set_time_limit('60000000');
                $this->load->library('PHPExcel');

                $sheet = $this->phpexcel->setActiveSheetIndex(0);
                $sheet->setTitle('REPORTE');

                $sheet->setCellValue('A1', 'REPORTE MINEDUC')
                        ->setCellValue('A3', 'REGION')
                        ->setCellValue('A4', 'CIUDAD')
                        ->setCellValue('A5', 'COMUNA')
                        ->setCellValue('A6', 'DIRECCIÓN')
                        ->setCellValue('A7', 'NOMBRE INMUEBLE')
                        ->setCellValue('A8', 'SITUACION DE TENENCIA')
                        ->setCellValue('A9', 'AÑO CONSTRUCCIÓN')
                        ->setCellValue('A10', 'AÑO ADQUISICIÓN')
                        ->setCellValue('A11', 'ACTIVIDAD')
                        ->setCellValue('A12', 'Nº SALAS DE CLASES')
                        ->setCellValue('A13', 'CAPACIDAD TOTAL SALA DE CLASES')
                        ->setCellValue('A14', 'M2 SALA DE CLASES')
                        ->setCellValue('A15', 'Nº AUDITORIOS')
                        ->setCellValue('A16', 'CAPACIDAD TOTAL AUDITORIOS')
                        ->setCellValue('A17', 'M2 AUDITORIOS')
                        ->setCellValue('A18', 'Nº LABORATORIOS')
                        ->setCellValue('A19', 'CAPACIDAD TOTAL LABORATORIOS')
                        ->setCellValue('A20', 'M2 LABORATORIOS')
                        ->setCellValue('A21', 'Nº TALLERES')
                        ->setCellValue('A22', 'CAPACIDAD TOTAL TALLERES')
                        ->setCellValue('A23', 'M2 TALLERES');


                $node = $nodeRaiz->getNode();
                $nodePadre = $node->getParent();

               
                $infraGrupoPadre = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposExportar($nodePadre->node_id, $nodePadre->node_type_id);
                
              
                
                foreach ($infraGrupoPadre as $infraInfoPadre) {
                    foreach ($infraInfoPadre->InfraOtherDataAttribute as $InfraOtherDataAttribute) {
                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'REGION') {
                            foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                if ($InfraOtherDataValue->infra_other_data_option_id) {
                                    $region = $InfraOtherDataValue->InfraOtherDataOption->infra_other_data_option_name;
                                }
                            }
                        }
                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'CIUDAD') {
                            foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                if ($InfraOtherDataValue->infra_other_data_option_id) {
                                    $ciudad = $InfraOtherDataValue->InfraOtherDataOption->infra_other_data_option_name;
                                }
                            }
                        }
                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'COMUNA') {
                            foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                if ($InfraOtherDataValue->infra_other_data_option_id) {
                                    $comuna = $InfraOtherDataValue->InfraOtherDataOption->infra_other_data_option_name;
                                }
                            }
                        }
                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'DIRECCION') {
                            foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                $direccion = $InfraOtherDataValue->infra_other_data_value_value;
                            }
                        }
                    }
                }

                
                
                $infraGrupoNode = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposExportar($nodeRaiz->node_id, $nodeRaiz->node_type_id);
                foreach ($infraGrupoNode as $infraInfoNode) {
                    foreach ($infraInfoNode->InfraOtherDataAttribute as $InfraOtherDataAttribute) {
//                        print_r($InfraOtherDataAttribute);
//                        exit();
                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'NOMBRE EDIFICIO') {
                            foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                $nombre_inmueble = $InfraOtherDataValue->infra_other_data_value_value;
                            }
                        }

                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'SITUACIÓN TENENCIA INMUEBLE') {
                            foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {

                                if ($InfraOtherDataValue->infra_other_data_option_id) {
                                    $situacion_tenencia = $InfraOtherDataValue->InfraOtherDataOption->infra_other_data_option_name;
                                }
                            }
                        }


                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'AÑO DE CONSTRUCCION') {
                          
                            foreach (@$InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                $ano_construccion = $InfraOtherDataValue->infra_other_data_value_value;
                              
                                list($anio_construccion, $mes_construccion, $dia_construccion) = explode('-', $ano_construccion);
                                $ano_construccion = $dia_construccion . '-' . $mes_construccion . '-' . $anio_construccion;
                            }
                        }

                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'AÑO DE ADQUISICION') {
                            foreach (@$InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                $ano_adquisicion = @$InfraOtherDataValue->infra_other_data_value_value;
                                list($anio_adquisicion, $mes_adquisicion, $dia_adquisicion) = explode('-', $ano_adquisicion);
                                $ano_adquisicion = $dia_adquisicion . '-' . $mes_adquisicion . '-' . $anio_adquisicion;
                            }
                        }

                        if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'ACTIVIDAD') {
                            foreach (@$InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {

                                if (@$InfraOtherDataValue->infra_other_data_option_id) {
                                    $actividad = @$InfraOtherDataValue->InfraOtherDataOption->infra_other_data_option_name;
                                }
                            }
                        }
                    }
                }

                $nodeActual = Doctrine_Core::getTable('Node')->findById($node_id);

                // DATOS TOTALES SALAS
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->innerJoin('n.InfraOtherDataValue iodv')
                        ->innerJoin('n.InfraInfo ii')
                        ->innerJoin('iodv.InfraOtherDataOption iodo')
                        ->where('node_parent_id = ?', $nodeActual->node_parent_id)
                        ->where('n.lft >= ?', $nodeActual->lft)
                        ->andWhere('n.rgt <= ?', $nodeActual->rgt)
                        ->andWhere('n.node_type_id IN (5)'); // TIPO SALA


                $resultsSala = $q->execute();
                foreach ($resultsSala as $valueSala) {

                    $infraGrupoCapacidad = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposExportar($valueSala->node_id, $valueSala->node_type_id);
                    foreach ($infraGrupoCapacidad as $infraInfoSalaUna) {
                        foreach ($infraInfoSalaUna->InfraOtherDataAttribute as $InfraOtherDataAttribute) {
                            if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'CANTIDAD USUARIOS') {
                                foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                    $capacidad_sala = $InfraOtherDataValue->infra_other_data_value_value;
                                    $capacidad_total_salas = $capacidad_sala + $capacidad_total_salas;
                                }
                            }
                        }
                    }

                    foreach ($valueSala->InfraInfo as $valueSalaInfo) {

                        $count_salas = $count_salas + 1;
                        $sum_metros_cuadrados = $valueSalaInfo->infra_info_usable_area;
                        $sum_total_metros_salas = $sum_total_metros_salas + $sum_metros_cuadrados;
                    }
                }

                // DATOS TOTALES AUDITORIOS
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->innerJoin('n.InfraOtherDataValue iodv')
                        ->innerJoin('n.InfraInfo ii')
                        ->innerJoin('iodv.InfraOtherDataOption iodo')
                        ->where('node_parent_id = ?', $nodeActual->node_parent_id)
                        ->where('n.lft >= ?', $nodeActual->lft)
                        ->andWhere('n.rgt <= ?', $nodeActual->rgt)
                        ->andWhere('n.node_type_id IN (30)'); // TIPO AUDITORIO


                $resultsAuditorio = $q->execute();
                foreach ($resultsAuditorio as $valueAuditorio) {

                    $infraGrupoCapacidadAuditorio = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposExportar($valueAuditorio->node_id, $valueAuditorio->node_type_id);
                    foreach ($infraGrupoCapacidadAuditorio as $infraInfoAuditorioUna) {
                        foreach ($infraInfoAuditorioUna->InfraOtherDataAttribute as $InfraOtherDataAttribute) {
                            if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'CANTIDAD USUARIOS') {
                                foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                    $capacidad_uditorio = $InfraOtherDataValue->infra_other_data_value_value;
                                    $capacidad_total_auditorios = $capacidad_uditorio + $capacidad_total_auditorios;
                                }
                            }
                        }
                    }

                    foreach ($valueAuditorio->InfraInfo as $valueAuditorioInfo) {

                        $count_auditorios = $count_auditorios + 1;
                        $sum_metros_cuadrados = $valueAuditorioInfo->infra_info_usable_area;
                        $sum_total_metros_auditorios = $sum_total_metros_auditorios + $sum_metros_cuadrados;
                    }
                }

                // DATOS TOTALES LABORATORIOS
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->innerJoin('n.InfraOtherDataValue iodv')
                        ->innerJoin('n.InfraInfo ii')
                        ->innerJoin('iodv.InfraOtherDataOption iodo')
                        ->where('node_parent_id = ?', $nodeActual->node_parent_id)
                        ->where('n.lft >= ?', $nodeActual->lft)
                        ->andWhere('n.rgt <= ?', $nodeActual->rgt)
                        ->andWhere('n.node_type_id IN (6)'); // TIPO LABORATORIO


                $resultsLaboratorio = $q->execute();
                foreach ($resultsLaboratorio as $valueLaboratorio) {

                    $infraGrupoCapacidadLaboratorio = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposExportar($valueLaboratorio->node_id, $valueLaboratorio->node_type_id);
                    foreach ($infraGrupoCapacidadLaboratorio as $infraInfoLaboratorioUna) {
                        foreach ($infraInfoLaboratorioUna->InfraOtherDataAttribute as $InfraOtherDataAttribute) {
                            if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'CANTIDAD USUARIOS') {
                                foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                    $capacidad_laboratorio = $InfraOtherDataValue->infra_other_data_value_value;
                                    $capacidad_total_laboratorios = $capacidad_laboratorio + $capacidad_total_laboratorios;
                                }
                            }
                        }
                    }

                    foreach ($valueLaboratorio->InfraInfo as $valueAuditorioInfo) {

                        $count_laboratorios = $count_laboratorios + 1;
                        $sum_metros_cuadrados = $valueAuditorioInfo->infra_info_usable_area;
                        $sum_total_metros_laboratorios = $sum_total_metros_laboratorios + $sum_metros_cuadrados;
                    }
                }

                // DATOS TOTALES TALLER
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->innerJoin('n.InfraOtherDataValue iodv')
                        ->innerJoin('n.InfraInfo ii')
                        ->innerJoin('iodv.InfraOtherDataOption iodo')
                        ->where('node_parent_id = ?', $nodeActual->node_parent_id)
                        ->where('n.lft >= ?', $nodeActual->lft)
                        ->andWhere('n.rgt <= ?', $nodeActual->rgt)
                        ->andWhere('n.node_type_id IN (7)'); // TIPO TALLER


                $resultsTaller = $q->execute();
                foreach ($resultsTaller as $valueTaller) {

                    $infraGrupoCapacidadTaller = Doctrine_Core::getTable('InfraGrupo')->retrieveAllGruposExportar($valueTaller->node_id, $valueTaller->node_type_id);
                    foreach ($infraGrupoCapacidadTaller as $infraInfoTallerUna) {
                        foreach ($infraInfoTallerUna->InfraOtherDataAttribute as $InfraOtherDataAttribute) {
                            if ($InfraOtherDataAttribute->infra_other_data_attribute_name == 'CANTIDAD USUARIOS') {
                                foreach ($InfraOtherDataAttribute->InfraOtherDataValue as $InfraOtherDataValue) {
                                    $capacidad_taller = $InfraOtherDataValue->infra_other_data_value_value;
                                    $capacidad_total_talleres = $capacidad_taller + $capacidad_total_talleres;
                                }
                            }
                        }
                    }

                    foreach ($valueTaller->InfraInfo as $valueAuditorioInfo) {

                        $count_talleres = $count_talleres + 1;
                        $sum_metros_cuadrados = $valueAuditorioInfo->infra_info_usable_area;
                        $sum_total_metros_talleres = $sum_total_metros_talleres + $sum_metros_cuadrados;
                    }
                }

                $sheet->setCellValueExplicit('B3', $region)
                        ->setCellValueExplicit('B4', $ciudad)
                        ->setCellValueExplicit('B5', $comuna)
                        ->setCellValueExplicit('B6', $direccion)
                        ->setCellValueExplicit('B7', $nombre_inmueble)
                        ->setCellValueExplicit('B8', $situacion_tenencia)
                        ->setCellValueExplicit('B9', $ano_construccion)
                        ->setCellValueExplicit('B10', $ano_adquisicion)
                        ->setCellValueExplicit('B11', $actividad)
                        ->setCellValueExplicit('B12', $count_salas)
                        ->setCellValueExplicit('B13', $capacidad_total_salas)
                        ->setCellValueExplicit('B14', $sum_total_metros_salas)
                        ->setCellValueExplicit('B15', $count_auditorios)
                        ->setCellValueExplicit('B16', $capacidad_total_auditorios)
                        ->setCellValueExplicit('B17', $sum_total_metros_auditorios)
                        ->setCellValueExplicit('B18', $count_laboratorios)
                        ->setCellValueExplicit('B19', $capacidad_total_laboratorios)
                        ->setCellValueExplicit('B20', $sum_total_metros_laboratorios)
                        ->setCellValueExplicit('B21', $count_talleres)
                        ->setCellValueExplicit('B22', $capacidad_total_talleres)
                        ->setCellValueExplicit('B23', $sum_total_metros_talleres);

                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);

                $sheet->getStyle('A1:B1')->getFont()->applyFromArray(array(
                    'bold' => true
                ));

                $sheet->getStyle('A3:A23')->getFont()->applyFromArray(array(
                    'bold' => true
                ));

                $sheet->getStyle('A3:A23')->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array(
                        'rgb' => 'd9e5f4'
                    )
                ));

                $sheet->getStyle('A3:B23')->getBorders()->applyFromArray(array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'rgb' => '808080'
                        )
                    )
                ));

                $sheet->mergeCells('A1:B1');
                $sheet->getStyle('A1')->getAlignment()->applyFromArray(
                        array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
                );

                $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
                $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . ' - ' . $nombre_inmueble . '.xls'));
                $time_end = microtime(true);
                $time = $time_end - $time_start;
                echo '{"success": true, "exe_seconds": "' . $time . '", "memory_usage" : "' . memory_get_usage() . '", "file": "' . $this->input->post('file_name') . ' - ' . $nombre_inmueble . '.xls"}';
            } else {

                echo '{"success": false,"msg": "El nodo seleccionado no es un edificio", "exe_seconds": "", "memory_usage" : "", "file": ""}';
            }
        } else {

            echo '{"success": false, "msg": "El nodo seleccionado no es un edificio", "exe_seconds": "", "memory_usage" : "", "file": ""}';
        }
    }

}
