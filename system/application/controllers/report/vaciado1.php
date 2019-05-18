<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Style;

//phpinfo();
//exit();
error_reporting(E_ALL);
ini_set('display_errors', 1);

//ini_set('upload_max_filesize', '1024M');
//ini_set('post_max_size', '1024M');
//ini_set('max_input_time', 50000);
//ini_set('max_execution_time', 50000);
//require_once 'vendor/autoload.php';
//use PhpOffice\PhpSpreadsheet\Writer;

/**
 * @package Controller
 * @subpackage ReportController
 */
class vaciado1 extends APP_Controller {

    function vaciado1() {
        parent :: APP_Controller();
    }

    /**
     * 
     * Lista todos los reportes del sistema
     */
    function index() {
//        $node_id = 1;

        $node_id = $this->input->post('node_id');
//        $this->exportListExcel($node_id);
////         echo "---";
//
        switch ($this->input->post('output_type')) {
            case 'e' :
                $this->exportListExcel($node_id);
                break;
        }
    }

    function exportListExcel($node_id = null) {


        $time_start = microtime(true);
        //Configuración de colores
        $colorConfig = array(
            28 => 'CD853F',
            1 => 'E0B0FF',
            23 => 'FFFF00',
            2 => '32CD32',
            3 => 'DC143C',
            29 => '04B4AE'
        );

        if ($node_id != 'root') {
            //se busca la informacion del nodo
            $queryChild = $this->db->query("Select * from node where node_id=" . $node_id);

            //Se obtine el resultado en forma de arreglo
            $node = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();

            //Se buscan los padres y los hijos del nodo seleccionado en la tabla tbl_ids que contiene todos los id de los nodos con la cantidad de recintos se traen solo los que tengan recintos
            $queryChild = $this->db->query("SELECT node_id from tbl_ids WHERE lft<=" . $node[0]['lft'] . " and rgt >=" . $node[0]['rgt'] . " and total >0 
                                            UNION ALL
                                            SELECT node_id from tbl_ids WHERE lft>" . $node[0]['lft'] . " and rgt <=" . $node[0]['rgt'] . " and total >0 ");

            //Se obtine el resultado en forma de arreglo
            $data_all = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();
            unset($node);



            //Ordena el array
            $flat = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($data_all)), 0);
            //Se separa por coma
            $flat = implode(",", $flat);


//            echo 'flat: '.$flat;exit();
            //Query en vista para traer la información asociada a los nodos
            $queryChild = $this->db->query("SELECT * FROM tbl_reporte where node_id in (" . $flat . ") "
                    . " ORDER BY  lft_node ASC ,level_node desc,infra_info_id ASC, infra_other_data_attribute_node_type_order ASC");

            $data_all = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();

            $queryChild = $this->db->query("select max(level_node) as max_level from tbl_reporte where node_id in (" . $flat . ") and node_type_category_id=2");
            $maxLevel = $queryChild->row();
            $queryChild->free_result();
            $this->db->close();

            $maxLevel = (isset($maxLevel)) ? $maxLevel->max_level : '';


            //Se inician las banderas
            $reg = $level_ = $ultimo_id = 0;
            $level_t = false;
            $valores = array();

            //Query que trae los datos estaticos que corresponden a los nodos padres e hijos
            $queryChild = $this->db->query("select * from infra_info where node_id in (" . $flat . ")");
            $row = $queryChild->result_array();
            $queryChild->free_result();
            $this->db->close();
            unset($flat);

            //Se almacena en un arreglo asociado al id del nodo
            foreach ($row as $data) {
                $valores['ID_' . $data['node_id']] = $data;
            }
            unset($row);

//            echo '<pre>';
//            print_r($data_all);
//            exit();
//            echo '</pre>';
            //Se recorre el resultado de la primera consulta
            $flat2 = false;
            foreach ($data_all as $data) {
                // Se captura el nombre del atributo
                $labelP = $data['infra_attribute'];

                //se guarda el color de acuerdo al tipo de nodo
                $color = isset($colorConfig[$data['node_type']]) ? $colorConfig[$data['node_type']] : 'd9e5f4';

                //Se verifica que todos los recintos esten en el mismo nivel                
                if (($data['node_type_category_id'] == 2) && ($data['level_node'] != $maxLevel)) {
                    $level_label = $maxLevel;
                    $new_level = true;

                    if (!isset($labelRecinto[$level_label]) || !in_array($labelP . "@@" . $color . "@@" . $level_label . '@@' . $data['node_type_category_id'], $labelRecinto[$level_label])) {
                        $labelRecinto[$level_label][] = $labelP . '@@' . $color . '@@' . $level_label . '@@' . $data['node_type_category_id'];
                    }
                } else {
                    $level_label = $data['level_node'];
                    $new_level = false;

                    //Si el label no existe el el arreglo $label lo almacena relacionado con el color y el nivel
                    if (!isset($label[$level_label]) || !in_array($labelP . "@@" . $color . "@@" . $level_label . '@@' . $data['node_type_category_id'], $label[$level_label])) {
                        $label[$level_label][] = $labelP . '@@' . $color . '@@' . $level_label . '@@' . $data['node_type_category_id'];
                    }
                }

                //Si es nivel recinto y la varible level_t es false, es la primera vez que llega al nivel de los recintos
                if (($data['level_node'] == $maxLevel && !$level_t) || ($new_level == true && !$level_t)) {
                    //Se convierte en true
                    $level_t = true;
                    //Se almacena el node_id
                    $ultimo_id = $data['node_id'];
                }

                //Si es diferente de vacio el valor y tiene el identificador infre_info_id se busca el valor en el arreglo valores
                if (!empty($data['value']) && !empty($data['infra_info_id'])) {
                    //Si existe el registro del nodo se captura el valor de acuerdo al nombre del atributo
                    if (isset($valores['ID_' . $data['node_id']])) {
                        $valor = $valores['ID_' . $data['node_id']][$data['value']];
                    } else {
                        $valor = '';
                    }
                } else {
                    // en caso de no tener infra_info se asigna el valor de la consulta
                    $valor = $data['value'];
                }

                //Si existe un nivel previo al actual y el actual es un recinto se borra ya que el recinto se asignara al nivel correspondiente
                if ($new_level) {
                    if (isset($level['lev_' . $data['level_node']])) {
                        unset($level['lev_' . $data['level_node']]);
                    }

                    //Se asigna el nivel correspondiente
                    $data['level_node'] = $maxLevel;
                }
//                echo $data['level_node']."<br>"
//                se arma el aray con todos los valores por level
                if ($level_t && $ultimo_id != $data['node_id']) {

                    $Data_exel['reg_' . $reg] = $level;
                    $level['lev_' . $data['level_node']][$labelP] = $valor;
                    $level['lev_' . $data['level_node']]['node_id'] = $data['node_id'];

                    $level_t = false;
                    $reg++;
                    $flat2 = true;
                } else {

                    //Si es un nuevo nivel borra en caso de que ya exista un registro con recinto de esta manera se ordene de manera correcta
                    if (isset($level['lev_' . $maxLevel]) && $flat2) {

                        unset($level['lev_' . $maxLevel]);
                        $flat2 = false;
                    }
                    $level['lev_' . $data['level_node']][$labelP] = $valor;
                    $level['lev_' . $data['level_node']]['node_id'] = $data['node_id'];
                    $Data_exel['reg_' . $reg] = $level;
                }
            }

//            echo '<pre>';
//            print_r($label);
//            exit();
//            var_dump($labelRecinto);
//            exit();
            
            //Se verifica que los labels del recinto que es de diferente nivel se encuentre en los atributos correspondientes a recintos sino se agrega
            if (isset($labelRecinto) and count($labelRecinto) > 0) {
                foreach ($labelRecinto as $key => $labels) {
                    foreach ($labels as $key => $labels2) {
                        if (!in_array($labels2, $label[$maxLevel])) {
                            $label[$maxLevel][] = $labels2;
                        }
                    }
                }
            }
//            exit();

            unset($data_all);
            unset($level);
//            echo'<pre>';
//            print_r($Data_exel);
//            exit();
//            fputcsv($file, $label2, ';');
//
//            $fila = 2;
//            //Se recorre el arreglo armado 
//            foreach ($Data_exel as $reg) {
//                $ccont = 0;
//                $data_tmp = array();
//                foreach ($label as $key => $data2) {
////                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($ccont++);
//                    $data = explode('@@', $data2);
//
//                    if (isset($reg['lev_' . $data[2]][$data[0]])) {
//                        $data_tmp[] = $reg['lev_' . $data[2]][$data[0]];
////                        $sheet->setCellValue($columnLetter . $fila, "" . $reg['lev_' . $data[2]][$data[0]]);
//                    } else {
//                        $data_tmp[] = "";
//                    }
//                }
//                fputcsv($file, $data_tmp, ';');
//                unset($reg['lev_' . $data[2]]);
////                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
//                $fila++;
//            }
//
//
//            fclose($file);
//            
//                       $ccont = 0;
//
//
//            exit();
//             #####################################
            //Se carga la libreria PHPExcel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->setActiveSheetIndex(0);
//            $this->load->library('PHPExcel');
//            $sheet = $this->phpexcel->setActiveSheetIndex(0);
            $sheet->setTitle('Results');

//            $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
//            $cacheSettings = array('memoryCacheSize ' => '1024MB', 'cacheTime' => 6000);
//            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
//            echo '</pre>';
//            print_r($label);exit();

            $ccont = 0;
            //se escribe cabezera
            foreach ($label as $key => $data1) {
                foreach ($data1 as $key => $data2) {
                    $ccont++;
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ccont);
//                    echo'<pre>';
////                    var_dump($data2);
//                    echo $data2;
                    $data = explode('@@', $data2);
                    $label3[] = $data2;
//
                    $sheet->setCellValue($columnLetter . '1', $data[0]);
////             
                    $styleArray = [
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => [
                                'argb' => $data[1],
                            ]
                        ],
                    ];
                    $sheet->getStyle($columnLetter . '1')->applyFromArray($styleArray);

                    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                }

//                echo'<pre>';
//                var_dump($data2);
//                
            }

//            exit();
            //se comienza a escribir desde la fila dos
            $fila = 2;

            //Se recorre el arreglo armado 
            foreach ($Data_exel as $reg) {
                $ccont = 0;
                foreach ($label3 as $key => $data2) {
                    $ccont++;
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ccont);
                    $data = explode('@@', $data2);

                    if (isset($reg['lev_' . $data[2]][$data[0]])) {
                        $sheet->setCellValue($columnLetter . $fila, "" . $reg['lev_' . $data[2]][$data[0]]);
                    } else {
                        $sheet->setCellValue($columnLetter . $fila, "");
                    }
                }
                unset($reg['lev_' . $data[2]]);
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                $fila++;
            }

            unset($label);
            unset($Data_exel);
//            echo "PASO";


            $sheet->getStyle('A1:' . $columnLetter . '1')->getFont()->applyFromArray(array(
                'bold' => true
            ));

//            $styleArray = [
//                'borders' => [
//                    'allBorders' => [
//                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
//                        'color' => ['argb' => '#000000'],
//                    ],
//                ],
//            ];
//
//            $rcont = $fila - 1;
//            $sheet->getStyle('A1:'.$columnLetter.$rcont)->applyFromArray($styleArray);
//            $sheet->getStyle('A1:A6')->applyFromArray($styleBoder);

            $time_end = microtime(true);
            $time = $time_end - $time_start;

//            \PHPExcel_Settings::setPdfRendererName(PHPExcel_Settings::PDF_RENDERER_MPDF);
//            \PHPExcel_Settings::setPdfRenderer($somePath);
//            $writer = \PHPExcel_IOFactory::createWriter($spreadsheet, 'PDF');

            $writer = new Xlsx($spreadsheet);
            $writer->save($this->app->getTempFileDir($this->input->post('file_name') . '.xlsx'));

//            $objWriter = IOFactory::createWriter($this->phpexcel, 'Excel5');
//            $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));

            echo '{"success": true, "exe_seconds": "' . $time . '", "memory_usage" : "' . memory_get_usage() . '", "file": "' . $this->input->post('file_name') . '.xlsx"}';
//            $this->phpexcel->disconnectWorksheets();
            unset($writer, $sheet);
//            unset($this->phpexcel);
//            ob_end_clean();
//            return;
        } else {
            echo '{"success": false, "msg": "Sin información","exe_seconds": "", "memory_usage" : "", "file": ""}';
        }
//        ob_end_clean();
    }

}
