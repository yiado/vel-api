<?php

/**
 * @package    Controller
 * @subpackage AssetLoadController
 */
class AssetLoadController extends APP_Controller {

    function AssetLoadController() {
        parent::APP_Controller();
    }

    function get() {
        $asset_load_folio = $this->input->post('asset_load_folio');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $user_name = $this->input->post('user_name');
        $asset_load_comment = $this->input->post('asset_load_comment');
        $user_id = $this->session->userdata('user_id');
        $user_type = $this->session->userdata('user_type');

        if (!empty($asset_load_folio)) {
            $CI = & get_instance();
            $asset_load_folio = $CI->app->generateFolio($asset_load_folio);
        }


        if ($user_type == 'A') {// ADMINISTRADOR
            $filters = array(
                'asset_load_folio = ?' => (!empty($asset_load_folio) ? $asset_load_folio : NULL),
                'asset_load_comment LIKE ?' => (!empty($asset_load_comment) ? '%' . $asset_load_comment . '%' : NULL),
                'u.user_name LIKE ?' => (!empty($user_name) ? '%' . $user_name . '%' : NULL),
                'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
                'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL )
            );
        } else { //OTRO TIPO DE USUARIO
            $filters = array(
                'asset_load_folio = ?' => (!empty($asset_load_folio) ? $asset_load_folio : NULL),
                'asset_load_comment LIKE ?' => (!empty($asset_load_comment) ? '%' . $asset_load_comment . '%' : NULL),
                'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
                'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
                'user_id = ?' => (!empty($user_id) ? $user_id : NULL )
            );
        }

        $AssetLoadTable = Doctrine_Core::getTable('AssetLoad')->findByFilters($filters);
        if ($AssetLoadTable) {
            echo '({"success": true, "results":' . $this->json->encode($AssetLoadTable->toArray()) . '})';
        } else {
            echo '({"success": false, "results":{}})';
        }
    }

    function getId() {
        $asset_load_id = $this->input->post('asset_load_id');

        $asset_load_id = $this->input->post('asset_load_id');
        $AssetLoadTable = Doctrine_Core::getTable('AssetLoad');

        $assetLoad = $AssetLoadTable->findAllId($asset_load_id, $this->input->post('start'), $this->input->post('limit'));
        if ($assetLoad->count()) {
            echo '({"total":"' . $AssetLoadTable->findAllId($asset_load_id, null, null, true) . '", "results":' . $this->json->encode($assetLoad->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function exportarFormato() {
        $this->load->library('PHPExcel');




############        
## HOJA 2 ##
############

        $sheet2 = $this->phpexcel->createSheet(); //CREA LA NUEVA HOJA
        $sheet2 = $this->phpexcel->setActiveSheetIndex(1); //SEGUNDA HOJA
        $sheet2->setTitle('BASE DE DATOS');

        //TITULOS
        $sheet2->setCellValue('A1', 'MARCA')
                ->setCellValue('B1', 'TIPO DE ACTIVO')
                ->setCellValue('C1', 'ESTADO')
                ->setCellValue('D1', 'CONDICION');

        //MARCAS
        $Brands = Doctrine_Core::getTable('Brand')->findAll();
        $rcount = 1;
        foreach ($Brands as $Brand) {
            $rcount++;
            $sheet2->setCellValueExplicit('A' . $rcount, $Brand->brand_name);
        }

        //TIPO DE ACTIVO
        $AssetTypes = Doctrine_Core::getTable('AssetType')->findAll();
        $rcount = 1;
        foreach ($AssetTypes as $AssetType) {
            $rcount++;
            $sheet2->setCellValueExplicit('B' . $rcount, $AssetType->asset_type_name);
        }

        //ESTADO
        $AssetStatuss = Doctrine_Core::getTable('AssetStatus')->findAll();
        $rcount = 1;
        foreach ($AssetStatuss as $AssetStatus) {
            $rcount++;
            $sheet2->setCellValueExplicit('C' . $rcount, $AssetStatus->asset_status_name);
        }

        //CONDICION
        $AssetConditions = Doctrine_Core::getTable('AssetCondition')->findAll();
        $rcount = 1;
        foreach ($AssetConditions as $AssetCondition) {
            $rcount++;
            $sheet2->setCellValueExplicit('D' . $rcount, $AssetCondition->asset_condition_name);
        }


        //ESTILOS
        $sheet2->getStyle('A1:D1')->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet2->getStyle('A' . 1 . ':D' . 1)->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet2->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));
        $sheet2->getStyle('A1:D' . 1)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $sheet2->getColumnDimension('A')->setAutoSize(true);
        $sheet2->getColumnDimension('B')->setAutoSize(true);
        $sheet2->getColumnDimension('C')->setAutoSize(true);
        $sheet2->getColumnDimension('D')->setAutoSize(true);
############
## HOJA 1 ##
############

        $sheet = $this->phpexcel->setActiveSheetIndex(0); // PRIMERA HOJA
        $sheet->setTitle('FORMATO CARGA MASIVA');


        $sheet->setCellValueExplicit('A1', 'NOMBRE ACTIVO(*)')
                ->setCellValue('B1', 'SERIE(*)')
                ->setCellValue('C1', 'NUMERO INTERNO(*)')
                ->setCellValue('D1', 'MARCA(*)')
                ->setCellValue('E1', 'TIPO DE ACTIVO(*)')
                ->setCellValue('F1', 'ESTADO(*)')
                ->setCellValue('G1', 'CONDICION(*)')
                ->setCellValue('H1', 'FACTURA')
                ->setCellValue('I1', 'DESCRIPCION')
                ->setCellValue('J1', 'CODIGO RECINTO(*)')
                ->setCellValue('K1', 'AUGE');
//        $sheet->setCellValueByColumnAndRow('A', ,PHPExcel_Cell_DataType::TYPE_STRING);
//                setCellValueByColumnAndRow($col, $row, $value);
        //ESTILOS
        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet->getStyle('A' . 1 . ':K' . 1)->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));
        $sheet->getStyle('A1:K' . 1)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);



        ## GUARDA Y DESCARGA ##
        $file = 'FormatoCargaMasiva';
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($file . '.xls'));

        $this->load->helper('download');
        $data = file_get_contents($this->app->getTempFileDir($file . '.xls')); // Read the file's contents
        force_download($file . '.xls', $data);
    }

    function addAssetMasivo() {
        ini_set('memory_limit', '2048M');
        $this->load->library('PHPExcel');
        $documentoExcel = $this->input->file('documentoExcel');
        $documentoExcel = $documentoExcel['tmp_name'];

        $sheetIndex = 0;
        $cont = 0;

        if ($documentoExcel) {
            $objPHPExcel2 = PHPExcel_IOFactory :: load($documentoExcel);
            $rowIterator2 = $objPHPExcel2->getActiveSheet()->getRowIterator();
            $objWorksheet2 = $objPHPExcel2->getActiveSheet();

            $rowsCount2 = $objWorksheet2->getHighestRow(); // cantidad de lineas en el excel  
            //------------------------------------
            //Valida Todos los datos a Insertar
            //----------------------------------
            foreach ($rowIterator2 as $row2) {

                $rowIndex2 = $row2->getRowIndex();

                if ($rowIndex2 > 1) {

                    $nombre_activo = $objWorksheet2->getCell('A' . $rowIndex2)->getCalculatedValue();
                    $numero_serie = (string)$objWorksheet2->getCell('B' . $rowIndex2)->getFormattedValue();
                    $numero_interno = (string)$objWorksheet2->getCell('C' . $rowIndex2)->getFormattedValue();
                    $marca = $objWorksheet2->getCell('D' . $rowIndex2)->getCalculatedValue();
                    $tipo_activo = $objWorksheet2->getCell('E' . $rowIndex2)->getCalculatedValue();
                    $estado = $objWorksheet2->getCell('F' . $rowIndex2)->getCalculatedValue();
                    $condicion = $objWorksheet2->getCell('G' . $rowIndex2)->getCalculatedValue();
                    $numero_factura = $objWorksheet2->getCell('H' . $rowIndex2)->getCalculatedValue();
                    $descripcion = $objWorksheet2->getCell('I' . $rowIndex2)->getCalculatedValue();
                    $codigo_recinto = $objWorksheet2->getCell('J' . $rowIndex2)->getCalculatedValue();
                    $codigo_auge = $objWorksheet2->getCell('K' . $rowIndex2)->getCalculatedValue();


                    $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($codigo_recinto));
                    $AssetType = Doctrine_Core :: getTable('AssetType')->assetTypeInName($tipo_activo);
//                     $AssetType = Doctrine_Core :: getTable('AssetType')->findOneBy('asset_type_name', $tipo_activo);
                    $Brand = Doctrine_Core :: getTable('Brand')->assetBrandInName($marca);
//                    $Brand = Doctrine_Core :: getTable('Brand')->findOneBy('brand_name', $marca);
                    $AssetStatus = Doctrine_Core :: getTable('AssetStatus')->assetAssetStatusInName($estado);
//                    $AssetStatus = Doctrine_Core :: getTable('AssetStatus')->findOneBy('asset_status_name', $estado);
                    $AssetCondition = Doctrine_Core :: getTable('AssetCondition')->assetAssetConditionInName($condicion);
//                    $AssetCondition = Doctrine_Core :: getTable('AssetCondition')->findOneBy('asset_condition_name', $condicion);


                    if (!$nodeOtherData) {
                        $success = 'false';
                        $msg = $this->translateTag('Infrastructure', 'no_campus') . $codigo_recinto . ' Celda : J' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }

                    if (!$AssetType) {
                        $success = 'false';
                        $msg = $this->translateTag('Plan', 'there_is_the_type_of_asset') . $tipo_activo . ' Celda : E' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }

                    if (!$Brand) {
                        $success = 'false';
                        $msg = $this->translateTag('Plan', 'there_brand') . $marca . ' Celda : D' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }

                    if (!$AssetStatus) {
                        $success = 'false';
                        $msg = $this->translateTag('Plan', 'there_the_state') . $estado . ' Celda : F' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }

                    if (!$AssetCondition) {
                        $success = 'false';
                        $msg = $this->translateTag('Plan', 'no_condition') . $condicion . ' Celda : G' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }

                    $findNumInt = Doctrine_Core::getTable('Asset')->findNumInt($numero_interno);

                    if ($findNumInt) {
                        $success = 'false';
                        $msg = $this->translateTag('Plan', 'there_is_the_serial_number') . $numero_interno . ' Celda : C' . $rowIndex2;
                        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
                        exit;
                    }
                }
            }

            ###########################
            ## AQUI INSERTA LO VALIDADO
            ###########################
            try {
                $assetload = new AssetLoad();
                $assetload->user_id = $this->auth->get_user_data('user_id');
                $assetload->asset_load_date = date("Y-m-d");
                $CI = & get_instance();
                $nuevo_folio = Doctrine_Core :: getTable('AssetLoad')->lastFolioWo() + 1;
                $assetload->asset_load_folio = $CI->app->generateFolio($nuevo_folio);
                $assetload->asset_load_comment = $this->input->post('asset_load_comment');
                $assetload->asset_load_foot_signature1 = $this->input->post('asset_load_foot_signature1');
                $assetload->asset_load_foot_signature2 = $this->input->post('asset_load_foot_signature2');
                $assetload->asset_load_foot_signature3 = $this->input->post('asset_load_foot_signature3');

                $assetload->save();

                //Realiza los Insert Correspondientes
                foreach ($rowIterator2 as $row2) {

                    $rowIndex2 = $row2->getRowIndex();

                    if ($rowIndex2 > 1) {



                        $nombre_activo = $objWorksheet2->getCell('A' . $rowIndex2)->getCalculatedValue();
                        $numero_serie = (string)$objWorksheet2->getCell('B' . $rowIndex2)->getFormattedValue();
                        $numero_interno = (string)$objWorksheet2->getCell('C' . $rowIndex2)->getFormattedValue();
                        $marca = $objWorksheet2->getCell('D' . $rowIndex2)->getCalculatedValue();
                        $tipo_activo = $objWorksheet2->getCell('E' . $rowIndex2)->getCalculatedValue();
                        $estado = $objWorksheet2->getCell('F' . $rowIndex2)->getCalculatedValue();
                        $condicion = $objWorksheet2->getCell('G' . $rowIndex2)->getCalculatedValue();
                        $numero_factura = $objWorksheet2->getCell('H' . $rowIndex2)->getCalculatedValue();
                        $descripcion = $objWorksheet2->getCell('I' . $rowIndex2)->getCalculatedValue();
                        $codigo_recinto = $objWorksheet2->getCell('J' . $rowIndex2)->getCalculatedValue();
                        $codigo_auge = $objWorksheet2->getCell('K' . $rowIndex2)->getCalculatedValue();



                        $nodeOtherData = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByIdAttribute(46, trim($codigo_recinto));
                        $AssetType = Doctrine_Core :: getTable('AssetType')->assetTypeInName($tipo_activo);
//                        $AssetType = Doctrine_Core :: getTable('AssetType')->findOneBy('asset_type_name', $tipo_activo);
                        $Brand = Doctrine_Core :: getTable('Brand')->assetBrandInName($marca);
//                        $Brand = Doctrine_Core :: getTable('Brand')->findOneBy('brand_name', $marca);
                        $AssetStatus = Doctrine_Core :: getTable('AssetStatus')->assetAssetStatusInName($estado);
//                        $AssetStatus = Doctrine_Core :: getTable('AssetStatus')->findOneBy('asset_status_name', $estado);
                        $AssetCondition = Doctrine_Core :: getTable('AssetCondition')->assetAssetConditionInName($condicion);
//                        $AssetCondition = Doctrine_Core :: getTable('AssetCondition')->findOneBy('asset_condition_name', $condicion);

                        if ($nodeOtherData) {
                            $node_id = $nodeOtherData->node_id;
                        }

                        if ($AssetType) {
                            $asset_type_id = $AssetType->asset_type_id;
                        }

                        if ($Brand) {
                            $brand_id = $Brand->brand_id;
                        }

                        if ($AssetStatus) {
                            $asset_status_id = $AssetStatus->asset_status_id;
                        }

                        if ($AssetCondition) {
                            $asset_condition_id = $AssetCondition->asset_condition_id;
                        }

                        $asset = new Asset();
                        $asset->node_id = $node_id;
                        $asset->asset_type_id = $asset_type_id;
                        $asset->brand_id = $brand_id;
                        $asset->asset_status_id = $asset_status_id;
                        $asset->asset_condition_id = $asset_condition_id;
                        $asset->asset_load_id = $assetload->asset_load_id;
                        $asset->asset_name = $nombre_activo;
                        $asset->asset_num_serie = $numero_serie;
                        $asset->asset_num_serie_intern = $numero_interno;
//                    $asset->asset_cost = $valor_compra;
//                    $asset->asset_current_cost = $costo_actual;
//                    $asset->asset_purchase_date = $fecha_compra_carga;
                        $asset->asset_document_count = 0;
//                    $asset->asset_lifetime = $anos_util;
                        $asset->asset_num_factura = $numero_factura;

                        date_default_timezone_set('UTC');
                        $asset->asset_load_date = date("Y-m-d");
                        $asset->asset_estate = 0;
                        $asset->asset_description = $descripcion;

                        $asset->save();
                        $node = $asset->node_id;
                        $user_id = $this->session->userdata('user_id');

                        //Trae el valor del activo que se esta creando
                        $selected_asset_ids = $asset->asset_id;

                        //BUSCAR LA RUTA DE ORIGEN DEL ASSET
                        $node = Doctrine_Core::getTable('Node')->find($node);
                        $asset_log_detail = $node->getPath();
                        $asset_log_type = 'asset_log_creation';

                        $log_id = $this->syslog->register('add_asset', array(
                            $asset->asset_name,
                            $node->getPath()
                        )); // registering log


                        $value = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAttributeAsset($asset->asset_id, 3);

                        if ($value === false) {
                            $value = new AssetOtherDataValue();
                        }

                        $asset_value = $value->asset_other_data_value_value;

                        $value->asset_other_data_attribute_id = 3;
                        $value->asset_id = $asset->asset_id;
                        $value->asset_other_data_value_value = $codigo_auge;
                        $value->save();

                        $other_data_attribute = Doctrine_Core::getTable('AssetOtherDataAttribute')->find($value->asset_other_data_attribute_id);
                        $asset_other_data_attribute_name = $other_data_attribute->asset_other_data_attribute_name;

                        Doctrine_Core::getTable('AssetLog')->logMoveAsset($selected_asset_ids, $user_id, $asset_log_type, $asset_log_detail);
                        $success = true;
                        $msg = $this->translateTag('General', 'operation_successful');
                    }
                }
            } catch (Exception $e) {
                $success = false;
                $msg = $e->getMessage();
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
            }


            $json_data = $this->json->encode(array('success' => 'true', 'msg' => "Carga Completa", 'asset_load_folio' => $assetload->asset_load_folio, 'asset_load_id' => $assetload->asset_load_id));
            echo $json_data;
        }
    }

    function getFootSignature() {
        $AssetLoad = Doctrine_Core::getTable('AssetLoad')->retrieveLastFolio();
        if ($AssetLoad) {
            $final['asset_load_foot_signature1'] = $AssetLoad->asset_load_foot_signature1;
            $final['asset_load_foot_signature2'] = $AssetLoad->asset_load_foot_signature2;
            $final['asset_load_foot_signature3'] = $AssetLoad->asset_load_foot_signature3;

            $msg = $this->translateTag('General', 'operation_successful');
            $success = 'true';
        } else {// CUANDO NO ENCUENTRA NADA
            $final = array();
        }
        echo '({"success":"' . 'true' . '", "results":' . $this->json->encode($final) . '})';
    }

    function validateDate($date, $format = 'd-m-Y') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function delete() {
        $asset_load_id = $this->input->post('asset_load_id');

        try {

            $asset_load = Doctrine::getTable('AssetLoad')->find($asset_load_id);
            if ($asset_load->delete()) {

                $success = true;
                $msg = $this->translateTag('General', 'operation_successful');
            } else {
                $success = false;
                $msg = $this->translateTag('General', 'error');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    
    function exportListado(){
        
        ini_set('memory_limit', '2048M');
        set_time_limit('60000000'); 
        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');
        
        $asset_load_folio = $this->input->post('asset_load_folio');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $user_name = $this->input->post('user_name');
        $asset_load_comment = $this->input->post('asset_load_comment');
        $user_id = $this->session->userdata('user_id');
        $user_type = $this->session->userdata('user_type');

        if (!empty($asset_load_folio)) {
            $CI = & get_instance();
            $asset_load_folio = $CI->app->generateFolio($asset_load_folio);
        }


        if ($user_type == 'A') {// ADMINISTRADOR
            $filters = array(
                'asset_load_folio = ?' => (!empty($asset_load_folio) ? $asset_load_folio : NULL),
                'asset_load_comment LIKE ?' => (!empty($asset_load_comment) ? '%' . $asset_load_comment . '%' : NULL),
                'u.user_name LIKE ?' => (!empty($user_name) ? '%' . $user_name . '%' : NULL),
                'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
                'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL )
            );
        } else { //OTRO TIPO DE USUARIO
            $filters = array(
                'asset_load_folio = ?' => (!empty($asset_load_folio) ? $asset_load_folio : NULL),
                'asset_load_comment LIKE ?' => (!empty($asset_load_comment) ? '%' . $asset_load_comment . '%' : NULL),
                'asset_load_date >= ?' => (!empty($start_date) ? $start_date : NULL ),
                'asset_load_date <= ?' => (!empty($end_date) ? $end_date : NULL ),
                'user_id = ?' => (!empty($user_id) ? $user_id : NULL )
            );
        }

        $AssetLoadTable = Doctrine_Core::getTable('AssetLoad')->findByFilters($filters);
        
        $sheet->setCellValue('A1', 'Folio de Carga')
                ->setCellValue('B1', 'Usuario Cargador')
                ->setCellValue('C1', 'Fecha de Carga')
                ->setCellValue('D1', 'Comentario')
                ->setCellValue('E1', 'Jefe de Servicio')
                ->setCellValue('F1', 'Conservador de Inventario')
                ->setCellValue('G1', 'Encargado de Activo Fijo');
        

        $rcount = 1;
        foreach ($AssetLoadTable as $asset) {
            
            $date = date_create($asset->asset_load_date);
            $fecha_formateada = date_format($date,'d/m/Y');
            
            
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $asset->asset_load_folio, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $asset->User->user_name)
                    ->setCellValueExplicit('C' . $rcount, $fecha_formateada)
                    ->setCellValueExplicit('D' . $rcount, $asset->asset_load_comment)
                    ->setCellValueExplicit('E' . $rcount, $asset->asset_load_foot_signature1)
                    ->setCellValueExplicit('F' . $rcount, $asset->asset_load_foot_signature2)
                    ->setCellValueExplicit('G' . $rcount, $asset->asset_load_foot_signature3);
            
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
     
        
        $sheet->getStyle('A1:G1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:G1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:G' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));
        
         ## GUARDA Y DESCARGA ##
        $file = 'Listado Completo Carga Masiva';
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($file . '.xls'));

        $this->load->helper('download');
        $data = file_get_contents($this->app->getTempFileDir($file . '.xls')); // Read the file's contents
        force_download($file . '.xls', $data);
    }

}
