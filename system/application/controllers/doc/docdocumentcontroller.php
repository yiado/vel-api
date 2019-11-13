<?php

/**
 * @package Controller
 * @subpackage DocDocumentController
 * 
 */
class DocDocumentController extends APP_Controller {

    function DocDocumentController() {
        parent::APP_Controller();
    }

    function getDocumentoVencido() {
        $cout = 0;
        $docs = Doctrine_Core::getTable('DocDocument')->retrieveByDocumentVencido();
        foreach ($docs->toArray() as $key => $doc) {
            list($anio, $mes, $dia) = explode('-', $doc['doc_version_expiration']);
            $fecha_exiration = date('Y-m-d', mktime(0, 0, 0, $mes, $dia - $doc['doc_version_alert'], $anio));
            if (date("Y-m-d") >= $fecha_exiration) {
                $final[] = $doc;
                $cout = $cout + 1;
            }
        }
        $json_data = $this->json->encode(array('total' => $cout, 'results' => $final));
        echo $json_data;
    }

    function edit() {

        $action = $this->input->post('action');

        if (method_exists($this, $action)) {
            $this->$action();
        } else {
            echo '{"success": false}';
        }
    }

    function cut() {
        $node_id = $this->input->post('node_id');
        $doc_document_id = $this->input->post('doc_document_id');
        $this->session->set_userdata('copy_node_id', $node_id);
        $this->session->set_userdata('copy_doc_document_id', $doc_document_id);
        $this->session->set_userdata('copy_type', 'cut');
    }

    function comprobacion() {
        $array_documento_nombre = array();

        $parentNodeAntiguo = Doctrine::getTable('Node')->find($this->input->post('node_parent_id'));

        $this->load->library('TreeNodes');
        $treeNodes = new TreeNodes();
        $parent_id = ($this->input->post('node_parent_id') == 'root' ? NULL : $this->input->post('node_parent_id'));
        $doc_document_ids = explode(',', $this->session->userdata('copy_doc_document_id'));
        $type = $this->session->userdata('copy_type');

        if (($type != 'cut' ) || !count($doc_document_ids)) {
            echo '{"success": false}';
            return;
        }
        $parentNode = Doctrine::getTable('Node')->find($parent_id);

        if ($type == 'cut') {
            foreach ($doc_document_ids as $id) {

                $DocDocument = Doctrine::getTable('DocDocument')->find($id);
                $node = Doctrine::getTable('Node')->find($DocDocument->node_id);

                $compare = Doctrine_Core::getTable('DocDocument')->compFileName($DocDocument->doc_document_filename, $this->input->post('node_parent_id'));

                //Preguntar si el nombre del documento ya existe en la base de datos si existe retornar mensaje "Debe ingresarlo como Version"
                if ($compare === true) {
                    $array_documento_nombre[] = $DocDocument->doc_document_filename;
                }
            }

            if ($array_documento_nombre) {// Entra cuando hay nombres repetidos en la nueva ubicacion
                $json_data = $this->json->encode(array('success' => 'true', 'nombre' => $array_documento_nombre));
                echo $json_data;
                return;
            } else {
                $json_data = $this->json->encode(array('success' => 'false', 'nombre' => ""));
                echo $json_data;
                return;
            }
        }
    }

    function paste() {
        $parentNodeAntiguo = Doctrine::getTable('Node')->find($this->input->post('node_parent_id'));

        $this->load->library('TreeNodes');
        $treeNodes = new TreeNodes();
        $parent_id = ($this->input->post('node_parent_id') == 'root' ? NULL : $this->input->post('node_parent_id'));
        $doc_document_ids = explode(',', $this->session->userdata('copy_doc_document_id'));
        $type = $this->session->userdata('copy_type');

        if (($type != 'cut' ) || !count($doc_document_ids)) {
            echo '{"success": false}';
            return;
        }
        $parentNode = Doctrine::getTable('Node')->find($parent_id);

        if ($type == 'cut') {
            foreach ($doc_document_ids as $id) {
                $DocDocument = Doctrine::getTable('DocDocument')->find($id);
                $node = Doctrine::getTable('Node')->find($DocDocument->node_id);

                $compare = Doctrine_Core::getTable('DocDocument')->compFileName($DocDocument->doc_document_filename, $this->input->post('node_parent_id'));

                //Entra solo cuando el documento no existe en la nueva ubicacion
                if ($compare === false) {
                    $this->syslog->register('cut_paste_document', array(
                        $DocDocument->doc_document_filename,
                        $node->getPath(),
                        $parentNode->getPath()
                    )); // registering log

                    $DocDocument->node_id = $parent_id;
                    $DocDocument->save();
//                    $msg = $this->translateTag('General', 'operation_successful');
                    $success = true;
                } else {
                    $success = false;
                }
            }
            $json_data = $this->json->encode(array(
                'success' => $success,
                'msg' => $this->translateTag('General', 'operation_successful')
            ));
            echo $json_data;
        }
    }

    /**
     * get
     *
     * Lista la documentacion del Nodo
     */
    function get() {

//casos especiales para la descripciÃƒÂ³n por el comodin usado en el like.
        $doc_document_filename = $this->input->post('doc_document_filename');
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $doc_document_description = $this->input->post('doc_document_description');
        $doc_version_keyword = $this->input->post('doc_version_keyword');
        $doc_version_comments = $this->input->post('doc_version_comments');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $start_date_interna = $this->input->post('start_date_interna');
        $end_date_interna = $this->input->post('end_date_interna');

        $start_date_exp = $this->input->post('start_date_exp');
        $end_date_exp = $this->input->post('end_date_exp');

        $filters = array(
            'doc_document_filename LIKE ?' => (!empty($doc_document_filename) ? '%' . $doc_document_filename . '%' : NULL),
            'doc_document_description LIKE ?' => (!empty($doc_document_description) ? '%' . $doc_document_description . '%' : NULL),
            'dvc.doc_version_keyword LIKE ?' => (!empty($doc_version_keyword) ? '%' . $doc_version_keyword . '%' : NULL),
            'dvc.doc_version_comments LIKE ?' => (!empty($doc_version_comments) ? '%' . $doc_version_comments . '%' : NULL),
            'doc_category_id = ?' => $this->input->post('doc_category_id'),
            'doc_extension_id = ?' => $this->input->post('doc_extension_id'),
            'doc_document_creation >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'doc_document_creation <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'dvc.doc_version_internal >= ?' => (!empty($start_date_interna) ? $start_date_interna : NULL ),
            'dvc.doc_version_internal <= ?' => (!empty($end_date_interna) ? $end_date_interna : NULL ),
            'dvc.doc_version_expiration >= ?' => (!empty($start_date_exp) ? $start_date_exp . ' 00:00:00' : NULL ),
            'dvc.doc_version_expiration <= ?' => (!empty($end_date_exp) ? $end_date_exp . ' 23:59:59' : NULL ),
            'us.user_id = ?' => $this->input->post('user_id')
        );

        $docsTable = Doctrine_Core::getTable('DocDocument');
        $docs = $docsTable->retrieveByNode($filters, $node_id, $search_branch);
        $result = count($docs);

        $json_data = $this->json->encode(array('total' => $result, 'results' => $docs));
        echo $json_data;
    }

    /**
     * exportListExcel
     *
     * Exporta el listado actual en formato excel
     *
     */
    function exportList($node_id = null) {

        $this->load->library('PHPExcel');

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Resultados');

        $doc_document_filename = $this->input->post('doc_document_filename');
        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $doc_document_description = $this->input->post('doc_document_description');
        $doc_version_keyword = $this->input->post('doc_version_keyword');
        $doc_version_comments = $this->input->post('doc_version_comments');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $start_date_interna = $this->input->post('start_date_interna');
        $end_date_interna = $this->input->post('end_date_interna');

        $start_date_exp = $this->input->post('start_date_exp');
        $end_date_exp = $this->input->post('end_date_exp');

        $filters = array(
            'doc_document_filename LIKE ?' => (!empty($doc_document_filename) ? '%' . $doc_document_filename . '%' : NULL),
            'doc_document_description LIKE ?' => (!empty($doc_document_description) ? '%' . $doc_document_description . '%' : NULL),
            'dvc.doc_version_keyword LIKE ?' => (!empty($doc_version_keyword) ? '%' . $doc_version_keyword . '%' : NULL),
            'dvc.doc_version_comments LIKE ?' => (!empty($doc_version_comments) ? '%' . $doc_version_comments . '%' : NULL),
            'doc_category_id = ?' => $this->input->post('doc_category_id'),
            'doc_extension_id = ?' => $this->input->post('doc_extension_id'),
            'doc_document_creation >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'doc_document_creation <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'dvc.doc_version_internal >= ?' => (!empty($start_date_interna) ? $start_date_interna : NULL ),
            'dvc.doc_version_internal <= ?' => (!empty($end_date_interna) ? $end_date_interna : NULL ),
            'dvc.doc_version_expiration >= ?' => (!empty($start_date_exp) ? $start_date_exp . ' 00:00:00' : NULL ),
            'dvc.doc_version_expiration <= ?' => (!empty($end_date_exp) ? $end_date_exp . ' 23:59:59' : NULL ),
            'us.user_id = ?' => $this->input->post('user_id')
        );

        $docsTable = Doctrine_Core::getTable('DocDocument');
        $docs = $docsTable->retrieveByNode($filters, $node_id, $search_branch);
        $result = count($docs);

        $sheet->setCellValue('A1', $this->translateTag('General', 'file_name'))
                ->setCellValue('B1', $this->translateTag('General', 'version'))
                ->setCellValue('C1', $this->translateTag('Document', 'document_type'))
                ->setCellValue('D1', $this->translateTag('General', 'category'))
                ->setCellValue('E1', $this->translateTag('Document', 'keywords'))
                ->setCellValue('F1', $this->translateTag('General', 'comment'))
                ->setCellValue('G1', 'Fecha Documento')
                ->setCellValue('H1', $this->translateTag('Plan', 'upload_date'))
                ->setCellValue('I1', $this->translateTag('General', 'expiration_date'))
                ->setCellValue('J1', $this->translateTag('General', 'user_magazine'))
                ->setCellValue('K1', $this->translateTag('Core', 'location'));

        $rcount = 1;
        foreach ($docs as $doc) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $doc['doc_document_filename'])
                    ->setCellValueExplicit('B' . $rcount, $doc['DocCurrentVersion']['doc_version_code_client'], PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $doc['DocExtension']['doc_extension_name'])
                    ->setCellValue('D' . $rcount, $doc['DocCategory']['doc_category_name'])
                    ->setCellValue('E' . $rcount, $doc['DocCurrentVersion']['doc_version_keyword'])
                    ->setCellValue('F' . $rcount, $doc['DocCurrentVersion']['doc_version_comments'])
                    ->setCellValue('G' . $rcount, $doc['DocCurrentVersion']['doc_version_internal'])
                    ->setCellValue('H' . $rcount, $doc['DocCurrentVersion']['doc_version_creation'])
                    ->setCellValue('I' . $rcount, $doc['DocCurrentVersion']['doc_version_expiration'])
                    ->setCellValue('J' . $rcount, $doc['DocCurrentVersion']['User']['user_name'])
                    ->setCellValue('K' . $rcount, $doc['doc_path']);
        }

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

        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));

        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';
    }

    function getDocument() {

        $this->load->library('RowNodes');

        $node_id =  $this->input->post('node_id');
        $nodePadre = Doctrine_Core::getTable('Node')->find($node_id);


//        $node = Doctrine_Core::getTable('Node')->find($node_id)->getNode();
//        $nodesCantity = $node->getNumberChildren();
//        $nodes = $node->getChildren();

        $rowNodes = new RowNodes();

        $fotos = array();
        $result = 0;
        $defecto = 0;


        $docs = Doctrine_Core::getTable('DocDocument')->findByNodeId($node_id);

        foreach ($docs as $doc) {
//                    echo 'aqui dos';
//                    echo 'Documento: '.$doc->doc_document_id;
            $doc_unico = Doctrine_Core::getTable('DocDocument')->findByDocumentId($doc->doc_document_id);
            if ($doc_unico) {
               
                if ($doc_unico->doc_document_id == $nodePadre->node_document_id_default) {
                   
                    $defecto = $doc_unico->doc_document_id;
                }
                $fotos[] = $doc_unico->toArray();
            }
          
           
            $result = $result + 1;
        }
     
        $node = Doctrine_Core::getTable('Node')->find($node_id)->getNode();
        $nodesCantity = $node->getNumberChildren();
        $nodes = $node->getChildren();
      
        if ($nodesCantity) {
         
            foreach ($nodes as $node) {
                
                $docs = Doctrine_Core::getTable('DocDocument')->findByNodeId($node->node_id);
                foreach ($docs as $doc) {
                   
                    $doc_unico = Doctrine_Core::getTable('DocDocument')->findByDocumentId($doc->doc_document_id);
                    if($doc_unico){
                    
                    if ($doc_unico->doc_document_id == $nodePadre->node_document_id_default) {
                        $defecto = $doc_unico->doc_document_id;
                    }
                    $fotos[] = $doc_unico->toArray();
                    $result = $result + 1;
                    }
                }
            }
        }
      

        $json_data = $this->json->encode(array('defecto' => $defecto, 'total' => $result, 'results' => $fotos));
        echo $json_data;
    }

    function getFotoStandar() {
        $node_id = $this->input->post('node_id');
        
        if ($node_id == '' OR $node_id == 'root') {
            echo '({"total":"0", "results":[]})';
            return;
        }

        $findGrId = Doctrine_Core::getTable('Node')->find($node_id);

        if ($findGrId) {
            $doc_document_id = (int) $findGrId->node_document_id_default;
        } else {
            $doc_document_id = null;
        }

        $docsTable = Doctrine_Core::getTable('DocDocument')->findByNodeDefault($doc_document_id);
//        print_r($docsTable); exit();
        if ($docsTable) {
            if ($docsTable->count()) {
                echo '({"total":"' . $docsTable->count() . '", "results":' . $this->json->encode($docsTable->toArray()) . '})';
            } else {
                echo '({"total":"0", "results":[]})';
            }
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function dejarPortadaNode() {
        $doc_document_id = $this->input->post('doc_document_id');
        $node_id = $this->input->post('node_id');

        $findGrId = Doctrine_Core::getTable('Node')->find($node_id);
        $findGrId->node_document_id_default = $doc_document_id;
        $findGrId->save();

        $success = true;
        $msg = $this->translateTag('General', 'selection_made_successfully');

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    /**
     * getBin
     *
     * Lista Todos los Documentos Inhabilitados
     */
    function getBin() {

//casos especiales para la descripciÃƒÂ³n por el comodin usado en el like.
        $doc_document_filename = $this->input->post('doc_document_filename');
//        $node_id = $this->input->post('node_id');
        $search_branch = $this->input->post('search_branch');
        $doc_document_description = $this->input->post('doc_document_description');
        $doc_version_keyword = $this->input->post('doc_version_keyword');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $start_date_exp = $this->input->post('start_date_exp');
        $end_date_exp = $this->input->post('end_date_exp');

        $filters = array(
            'doc_document_filename LIKE ?' => (!empty($doc_document_filename) ? '%' . $doc_document_filename . '%' : NULL),
            'doc_document_description LIKE ?' => (!empty($doc_document_description) ? '%' . $doc_document_description . '%' : NULL),
            'dvc.doc_version_keyword LIKE ?' => (!empty($doc_version_keyword) ? '%' . $doc_version_keyword . '%' : NULL),
            'doc_category_id = ?' => $this->input->post('doc_category_id'),
            'doc_extension_id = ?' => $this->input->post('doc_extension_id'),
            'doc_document_creation >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'doc_document_creation <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'dvc.doc_version_expiration >= ?' => (!empty($start_date_exp) ? $start_date_exp . ' 00:00:00' : NULL ),
            'dvc.doc_version_expiration <= ?' => (!empty($end_date_exp) ? $end_date_exp . ' 23:59:59' : NULL ),
            'us.user_id = ?' => $this->input->post('user_id')
        );

        $docsTable = Doctrine_Core::getTable('DocDocument');
        $docs = $docsTable->retrieveByNodeBin($filters, $search_branch);
        $result = count($docs);

        $json_data = $this->json->encode(array('total' => $result, 'results' => $docs));
        echo $json_data;
    }

    /**
     * enviarPapelera
     *
     * Envia a la Papelera todos los documentos seleccionados
     */
    function enviarPapelera() {
//Recibimos los parametros
        $doc_document_ids = $this->input->post('doc_document_id');

        try {
            $doc_document_ids = explode(",", $doc_document_ids);

            foreach ($doc_document_ids as $doc_document_id) {
                $findGrId = Doctrine_Core::getTable('DocDocument')->find($doc_document_id);
                $findGrId->doc_status_id = 1;
                $findGrId->save();
                $success = true;
                $msg = $this->translateTag('General', 'sent_to_the_trash_registration_successfully');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    /**
     * sacarPapelera
     *
     * Restaura todos los documentos seleccionados de la Papelera
     */
    function sacarPapelera() {
//Recibimos los parametros
        $doc_document_ids = $this->input->post('doc_document_id');

        try {
            $doc_document_ids = explode(",", $doc_document_ids);

            foreach ($doc_document_ids as $doc_document_id) {
                $findGrId = Doctrine_Core::getTable('DocDocument')->find($doc_document_id);
                $findGrId->doc_status_id = 0;
                $findGrId->save();
                $success = true;
                $msg = $this->translateTag('General', 'successfully_restored_record');
            }
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    function addThumb($doc_name, $file_extension) {


        $extensiones_permitidas = $this->config->item('doc_image_web');
        if (in_array(strtoupper($file_extension), $extensiones_permitidas)) {

            $this->load->library('image_lib');

            $img_cfg['image_library'] = 'gd2';
            $img_cfg['source_image'] = $this->config->item('doc_dir') . $doc_name;
            $img_cfg['maintain_ratio'] = FALSE;
            $config['create_thumb'] = TRUE;
            $img_cfg['new_image'] = $this->config->item('doc_dir') . 'thumb/' . $doc_name;
            $img_cfg['width'] = 150;
            $img_cfg['quality'] = 100;
            $img_cfg['height'] = 150;

            $this->image_lib->initialize($img_cfg);
            $this->image_lib->resize();
        }
    }

    function vuelveFile() {

        $doc_document_id = $this->input->post('doc_document_id');

        $docDocument = Doctrine_Core::getTable('DocDocument')->findVersionDoc($doc_document_id);
        echo '({"success": true, "data":' . $this->json->encode($docDocument->toArray()) . '})';
    }

    function rotacion1() {

        $doc_version_filename = $this->input->post('doc_version_filename');
        $pieces = explode(".", $doc_version_filename, 2);

        $extensiones_permitidas = $this->config->item('doc_image_web');
        if (in_array(strtoupper($pieces[1]), $extensiones_permitidas)) {
            clearstatcache();
            $this->load->library('image_lib');

            $config['image_library'] = 'gd2';
            $config['library_path'] = '/usr/bin/';
            $config['source_image'] = $this->config->item('doc_dir') . $doc_version_filename;
            $config['rotation_angle'] = '90';

            $this->image_lib->initialize($config);



            if (!$this->image_lib->rotate()) {
                echo $this->image_lib->display_errors();
            }

            //Actualizar miniatura
            $this->addThumb($doc_version_filename, $pieces[1]);

            $docVersion = Doctrine_Core::getTable('DocVersion')->findOneBy('doc_version_filename', $doc_version_filename);
            $docDocument = Doctrine_Core::getTable('DocDocument')->findById($docVersion->doc_document_id);
            echo '({"success": true, "data":' . $this->json->encode($docDocument->toArray()) . '})';
        }
    }

    function rotacion2() {

        $doc_version_filename = $this->input->post('doc_version_filename');
        $pieces = explode(".", $doc_version_filename, 2);

        $extensiones_permitidas = $this->config->item('doc_image_web');
        if (in_array(strtoupper($pieces[1]), $extensiones_permitidas)) {
            clearstatcache();
            $this->load->library('image_lib');

            $config['image_library'] = 'gd2';
            $config['library_path'] = '/usr/bin/';
            $config['source_image'] = $this->config->item('doc_dir') . $doc_version_filename;
            $config['rotation_angle'] = '270';

            $this->image_lib->initialize($config);



            if (!$this->image_lib->rotate()) {
                echo $this->image_lib->display_errors();
            }

            //Actualizar miniatura
            $this->addThumb($doc_version_filename, $pieces[1]);

            $docVersion = Doctrine_Core::getTable('DocVersion')->findOneBy('doc_version_filename', $doc_version_filename);
            $docDocument = Doctrine_Core::getTable('DocDocument')->findById($docVersion->doc_document_id);
            echo '({"success": true, "data":' . $this->json->encode($docDocument->toArray()) . '})';
        }
    }

    function add() {
        $file_uploaded = $this->input->file('documento');
        $file_extension = $this->app->getFileExtension($file_uploaded['name']);
        $file_name_actual = $this->app->getFileName($file_uploaded['name']);
        $docExtension = new DocExtension();
        $file_input = $file_uploaded['name'];
        $compare = Doctrine_Core::getTable('DocDocument')->compFileName($file_input, $this->input->post('node_id'));

//Preguntar si el nombre del documento ya existe en la base de datos si existe retornar mensaje "Debe ingresarlo como Version"
        if ($compare === true) {
            $success = false;
            $msg = $this->translateTag('Document', 'version_should_be_entered_as');
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
            return;
        }

        if (($extension_id = $docExtension->isAllowed($file_extension)) !== false) {
//Recibimos los parametros
            $node_id = $this->input->post('node_id');
            $doc_category_id = $this->input->post('doc_category_id');
            $doc_document_description = $this->input->post('doc_document_description');
            $doc_version_comments = $this->input->post('doc_version_comments');
            $doc_version_internal = $this->input->post('doc_version_internal');
            $doc_version_expiration = $this->input->post('doc_version_expiration');
            $doc_version_code_client = $this->input->post('doc_version_code_client');
            $doc_version_keyword = $this->input->post('doc_version_keyword');
            $doc_version_alert = $this->input->post('doc_version_alert');
            $doc_version_alert_email = $this->input->post('doc_version_alert_email');
            $doc_version_notification_email = $this->input->post('doc_version_notification_email');

//Preguntar si el nombre del documento ya existe en la base de datos si existe retornar mensaje "Debe ingresarlo como Version"
//Insertamos el nuevo Documento en la tabla
            $doc = new DocDocument();
            $doc->node_id = $node_id;
            $doc->doc_category_id = $doc_category_id;
            $doc->doc_extension_id = $extension_id;
            $doc->doc_document_description = $doc_document_description;
            $doc->doc_status_id = 0;
            $doc->doc_document_filename = $file_name_actual . '.' . $file_extension;
            $doc->doc_current_version_id = NULL;
            $doc->save();

            $doc_name = md5(time()) . '.' . $file_extension;

//Obtenemos la conexiÃ¯Â¿Â½n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

//Iniciamos la transacciÃ¯Â¿Â½n
            $conn->beginTransaction();

            try {
//Insertamos la nueva Version en la tabla
                $version_number = 1;
                $version = new DocVersion();
                $version->doc_version_code = $version_number;
                $version->doc_version_code_client = $doc_version_code_client;
                $version->doc_version_filename = $doc_name;
                $version->doc_version_comments = $doc_version_comments;
                $version->doc_version_internal = $doc_version_internal;
                $version->doc_version_expiration = $doc_version_expiration;
                $version->doc_version_keyword = $doc_version_keyword;
                $version->doc_version_alert = $doc_version_alert;
                $version->doc_version_alert_email = $doc_version_alert_email;
                $version->doc_version_notification_email = $doc_version_notification_email;
                $user_id = $this->auth->get_user_data('user_id');
                $version->user_id = $user_id;
                $version->doc_document_id = $doc->doc_document_id;
                $version->save();
                $doc->doc_current_version_id = $version->doc_version_id;
                $doc->save();

                $node = Doctrine::getTable('Node')->find($node_id);
                $docCategory = Doctrine::getTable('DocCategory')->find($doc->doc_category_id);

                $this->syslog->register('add_document', array(
                    $doc->doc_document_filename,
                    $version->doc_version_code,
                    $docCategory->doc_category_name,
                    $node->getPath()
                )); // registering log
//
                //Rescatamos el id
                $doc_last_id = $doc->doc_document_id;


//Creamos el nombre para el nuevo documento
                $config['upload_path'] = $this->config->item('doc_dir');
                $config['allowed_types'] = strtolower($file_extension);
                $config['file_name'] = $doc_name;

//Restringuir tamaÃ¯Â¿Â½o y peso?
//Carga de la libreria para el upload
                $this->load->library('upload', $config);

                if (!$this->upload->do_upload('documento')) {
                    $success = 'false';
                    $msg = $this->upload->display_errors('-', '\n');
                    throw new Exception($msg);
                } else {
                    $this->addThumb($doc_name, $file_extension);
                    $doc_version_id = $version->doc_version_id;
//                    $this->sendNotification($doc_version_id);
                    $success = 'true';
                    $msg = $this->translateTag('General', 'operation_successful');



// Si todo OK, commit a la base de datos
                    $conn->commit();
                }
            } catch (Exception $e) {
//Si hay error, rollback de los cambios en la base de datos
                $conn->rollback();
                $success = 'false';
                $msg = $e->getMessage();
            }
        } else {
            $success = 'false';
            $msg = $this->translateTag('Document', 'type_extension_not_allowed');
        }
        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
    }

    function updateCategory() {
        $doc_document_id = $this->input->post('doc_document_id');
        $doc_category_id = $this->input->post('doc_category_id');

        $doc_document = Doctrine_Core::getTable('DocDocument')->find($doc_document_id);
        $doc_document->doc_category_id = $doc_category_id;
        $doc_document->save();

        $success = true;
        $msg = $this->translateTag('General', 'operation_successful');

        $json_data = $this->json->encode(array(
            'success' => $success,
            'msg' => $msg
        ));
        echo $json_data;
    }

    function download($doc_version_id) {
        $this->load->helper('download');
        $version = Doctrine_Core::getTable('DocVersion')->find($doc_version_id);
        $file_name = $version->doc_document_id;
        $file_extension = $version->doc_version_code_client;
        $pieces = explode(".", $file_extension);
        $extension = end($pieces);
        $file_name_document = Doctrine_Core::getTable('DocDocument')->find($file_name);
        $file_name_document_name = $file_name_document->doc_document_filename;
        $data = file_get_contents($this->config->item('doc_dir') . $version->doc_version_filename); // Read the file's contents
        $file_name_complete = $file_name_document_name;
        force_download($file_name_complete, $data);
    }

    function downloadThumb($doc_version_id) {
        $this->load->helper('download');
        $version = Doctrine_Core::getTable('DocVersion')->find($doc_version_id);
        $file_name = $version->doc_document_id;
        $file_extension = $version->doc_version_code_client;
        $pieces = explode(".", $file_extension);
        $extension = end($pieces);
        $file_name_document = Doctrine_Core::getTable('DocDocument')->find($file_name);
        $file_name_document_name = $file_name_document->doc_document_filename;
        $data = file_get_contents($this->config->item('doc_dir') . 'thumb/' . $version->doc_version_filename); // Read the file's contents
        $file_name_complete = $file_name_document_name;
        force_download($file_name_complete, $data);
    }

    function downloadImage($doc_version_id) {
        $this->load->helper('download');
        $version = Doctrine_Core::getTable('DocVersion')->find($doc_version_id);

        $data = file_get_contents($this->config->item('doc_dir') . $version->doc_version_filename); // Read the file's contents
        $file_name_complete = $version->doc_version_code_client;
        force_download($file_name_complete, $data);
    }

    /**
     * delete
     *
     * Elimina un documento y todas sus versiones
     *
     * @param integer $doc_document_id
     */
    function delete() {

        $document = Doctrine::getTable('DocDocument')->find($this->input->post('doc_document_id'));
        $node = Doctrine::getTable('Node')->find($document->node_id);

        if ($document && $document->delete()) {
//echo '{"success": true}';

            $this->syslog->register('delete_document', array(
                $document->doc_document_filename,
                $node->getPath()
            )); // registering log

            $success = 'true';
            $msg = $this->translateTag('General', 'operation_successful');
        } else {
//echo '{"success": false}';
            $success = 'false';
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function sendNotification($doc_version_id) {

        $q = Doctrine_Query::create()
                ->from('DocVersion dv')
                ->innerJoin('dv.DocDocument dd')
                ->innerJoin('dd.DocCategory dc')
                ->Where('dv.doc_version_id = ?', $doc_version_id);


        $results = $q->fetchOne();

        $node = Doctrine_Core::getTable('Node')->find($results['DocDocument']['node_id']);
        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['doc_version_notification_email']); //CORREO DESTINATARIO

        $subject = $this->translateTag('Document', 'contract_expiration_warning_notice'); //ASUNTO

        $body = $this->translateTag('Document', 'mail_document_name') . $results['DocDocument']['doc_document_filename'] . "\n"; //CUERPO DEL MENSAJE
        $body .= $this->translateTag('Document', 'mail_category') . $results['DocDocument']['DocCategory']['doc_category_name'] . "\n";
        $body .= $this->translateTag('Document', 'mail_version') . $results['doc_version_code_client'] . "\n";
        $body .= $this->translateTag('Document', 'mail_description') . $results['DocDocument']['doc_document_description'] . "\n";
        $body .= $this->translateTag('Document', 'mail_expiration_date') . $results['doc_version_expiration'] . "\r\n";
        $body .= $this->translateTag('Document', 'mail_location') . $node->getPath() . "\r\n";

        copy($this->config->item('doc_dir') . $results['doc_version_filename'], $this->config->item('temp_dir') . $results['DocDocument']['doc_document_filename']);

        $CI->notificationuser->mail($to, $subject, $body, array($this->config->item('temp_dir') . $results['DocDocument']['doc_document_filename']));
        $body = '';

        unlink($this->config->item('temp_dir') . $results['DocDocument']['doc_document_filename']);
    }

    function addToZip($array_doc_document_id = null) {

        $this->load->library('zip');
        $array_doc_completo = explode("-", $array_doc_document_id);

        $array_doc = str_replace('-', ',', $array_doc_completo);
        $node_id = end($array_doc);
        $fruit = array_pop($array_doc);

        $datos = array();
        foreach ($array_doc as $doc_document_id) {

            $DocVersion = Doctrine_Core::getTable('DocVersion')->lastVersionDocument2($doc_document_id);
            $DocDocument = Doctrine_Core::getTable('DocDocument')->find($DocVersion['doc_document_id']);
            $nameDocument = $DocDocument['doc_document_filename'];
            $piecesdoc = explode(".", $nameDocument);
            $nameDocument = $piecesdoc[0];
            $data = file_get_contents($this->config->item('doc_dir') . $DocVersion['doc_version_filename']);
            $pieces = explode(".", $DocVersion['doc_version_filename']);
            $extension = end($pieces);
            $datos += array($nameDocument . '_' . $DocVersion['doc_version_code_client'] . '.' . $extension => $data);

            $DocDocumentName = Doctrine_Core::getTable('DocDocument')->find($doc_document_id);

            $node = Doctrine::getTable('Node')->find($node_id);
            $this->syslog->register('add_to_zip_document', array(
                $DocDocumentName->doc_document_filename,
                $node->getPath()
            )); // registering log
        }

        $this->zip->add_data($datos);
        $this->zip->download('ZipDocument.zip');
    }

    function addMasive() {
        //BORRA ARCHIVOS DE DATA ANTES QUE TODO
        $dir = $this->config->item('doc_dir') . 'data/*';
        foreach (glob($dir) as $path) {
            if (!is_dir($path)) { //SOLO SI ES ARCHIVO      
                unlink($path); //BORRA ARCHIVO
            }
        }

        //RESCATA EL ARCHIVO ZIP QUE SE DESEA SUBIR
        $file_uploaded = $this->input->file('documento');
        $file_extension = $this->app->getFileExtension($file_uploaded['name']);
        $file_name_actual = $this->app->getFileName($file_uploaded['name']);

        if ($file_extension != 'zip') {
            $success = 'false';
            $msg = $this->translateTag('Document', 'the_file_has_to_be_zip');
        } else {

            $doc_name = md5(time()) . '.' . $file_extension; //CREA UN NOMBRE Y CONCATENA LA EXTENSION
            //SE CONFIGURA EL ZIP A SUBIR
            $config = array();
            $config['upload_path'] = $this->config->item('temp_dir'); //RUTA DE DESTINO /docs
            $config['allowed_types'] = 'zip'; //EXTENSION SACADA DEL ARCHIVO SUBIDO
            $config['file_name'] = $doc_name; //NOMBRE CON EL QUE SE GUARDA EN LA RUTA INDICADA
            //SUBE EL ARCHIVO
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('documento')) {
                $success = 'false';
                $msg = $this->upload->display_errors('-', '\n');
                throw new Exception($msg);
            } else {

                $success = 'true';
                $msg = $this->translateTag('General', 'operation_successful');
            }

            //----LIBRERIA QUE DESCOMPRIME EL ZIP-----
            $rutayarchivo = $this->config->item('temp_dir') . $doc_name;
            chmod($rutayarchivo, 0755);

            //BORRA EL CONTENIDO DE LA CARPETA 'DOCS'
            $zip = new ZipArchive;
            if ($zip->open($rutayarchivo) === TRUE) {
                $zip->extractTo($this->config->item('doc_dir') . 'data/');
                $zip->close();
                $success = true;
            } else {
                $success = false;
                $msg = $this->translateTag('Document', 'can_not_open_the_zip_file');
                echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                return;
            }



            //-------------------------------------------------------------------------------
            //----------AQUI RECORRE LA RUTA Y CREA LOS INSERTS POR CADA ARCHIVO EN EL PATH
            //-------------------------------------------------------------------------------

            try {

                $dir = $this->config->item('doc_dir') . 'data/*';
                foreach (glob($dir) as $path) {

                    if (!is_dir($path)) {// ENTRA SOLO SI ES ARCHIVO
                        $file_extension = substr(strrchr($path, "."), 1); //EXTENSION
                        $nom_archivo = array_pop(explode("/", $path)); //NOMBRE+PUNTO+EXTENSION
                        list($file_name_actual) = (explode(".", $nom_archivo)); //SOLO NOMBRE


                        $docExtension = new DocExtension();
                        $compare = Doctrine_Core::getTable('DocDocument')->compFileName($nom_archivo, $this->input->post('node_id'));

                        //Preguntar si el nombre del documento ya existe en la base de datos si existe retornar mensaje "Debe ingresarlo como Version"
                        if ($compare === true) {
                            $success = false;
                            $msg = $this->translateTag('Document', 'version_should_be_entered_as');
                            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                            echo $json_data;
                            return;
                        }

                        //PREGUNTA POR UNA EXTENSION  VALIDA OJO Q DEVUELVE EL ID NO EL NOMBRE DE LA EXTENSION
                        if (($extension_id = $docExtension->isAllowed($file_extension)) !== false) {
                            //RECIVE LOS POST                        
                            $node_id = $this->input->post('node_id');
                            $doc_category_id = $this->input->post('doc_category_id');
                            $doc_document_description = $this->input->post('doc_document_description');
                            $doc_version_comments = $this->input->post('doc_version_comments');
                            $doc_version_internal = $this->input->post('doc_version_internal');
                            $doc_version_expiration = $this->input->post('doc_version_expiration');
                            $doc_version_code_client = $this->input->post('doc_version_code_client');
                            $doc_version_keyword = $this->input->post('doc_version_keyword');
                            $doc_version_alert = $this->input->post('doc_version_alert');
                            $doc_version_alert_email = $this->input->post('doc_version_alert_email');
                            $doc_version_notification_email = $this->input->post('doc_version_notification_email');

                            //INSERT DOCUMENTO
                            $doc = new DocDocument();
                            $doc->node_id = $node_id;
                            $doc->doc_category_id = $doc_category_id;
                            $doc->doc_extension_id = $extension_id; //ojo este es el id de donde encuentra el id
                            $doc->doc_document_description = $doc_document_description;
                            $doc->doc_status_id = 0;
                            $doc->doc_document_filename = $file_name_actual . '.' . $file_extension;
                            $doc->doc_current_version_id = NULL;
                            $doc->save();

                            //SE CREA UN NOMBRE FALSO CON FORMATO MD5
                            $doc_name = md5(time() . ' ' . $file_name_actual) . '.' . $file_extension;


                            //INSERT VERSION DEL DOCUMENTO
                            $version_number = 1;
                            $version = new DocVersion();
                            $version->doc_version_code = $version_number;
                            $version->doc_version_code_client = $doc_version_code_client;
                            $version->doc_version_filename = $doc_name;
                            $version->doc_version_comments = $doc_version_comments;
                            $version->doc_version_internal = $doc_version_internal;
                            $version->doc_version_expiration = $doc_version_expiration;
                            $version->doc_version_keyword = $doc_version_keyword;
                            $version->doc_version_alert = $doc_version_alert;
                            $version->doc_version_alert_email = $doc_version_alert_email;
                            $version->doc_version_notification_email = $doc_version_notification_email;
                            $user_id = $this->auth->get_user_data('user_id');
                            $version->user_id = $user_id;
                            $version->doc_document_id = $doc->doc_document_id;
                            $version->save();

                            $doc->doc_current_version_id = $version->doc_version_id;
                            $doc->save();



                            //SE CREA UN LOG DE REGISTRO
                            $node = Doctrine::getTable('Node')->find($node_id);
                            $docCategory = Doctrine::getTable('DocCategory')->find($doc->doc_category_id);

                            $this->syslog->register('add_document', array(
                                $doc->doc_document_filename,
                                $version->doc_version_code,
                                $docCategory->doc_category_name,
                                $node->getPath()
                            ));

                            //SE MUEVE EL ARCHIVO DE DOCS->DATA A DOCS
                            copy($this->config->item('doc_dir') . 'data/' . $nom_archivo, $this->config->item('doc_dir') . $doc_name);

                            $this->addThumb($doc_name, $file_extension);
                        } else {
                            $success = 'false';
                            $msg = $this->translateTag('Document', 'type_extension_not_allowed');
                        }
                    }//FIN DEL IF
                }//FIN DEL FOR 
            } catch (Exception $e) {
                $success = 'false';
                $msg = $e->getMessage();
            }
        }



        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
    }

    function addZipExcel() {
        try {
//---------SUBE LOS DOCUMENTOS EN ZIP (.ZIP)----------
            $documentoZip = $this->input->file('documentoZip');
            $documentoExtension = $this->app->getFileExtension($documentoZip['name']);
            $documentoName = $this->app->getFileName($documentoZip['name']);
//CREA UN NOMBRE Y CONCATENA LA EXTENSION            
            $nombreMD5 = md5(time()) . '.' . $documentoExtension;
            $nombreMD5carpeta = md5(time());
//SE CONFIGURA EL ZIP A SUBIR
            $config['upload_path'] = $this->config->item('temp_dir');
            $config['allowed_types'] = 'zip';
            $config['file_name'] = $nombreMD5;
            $this->load->library('upload', $config);
//SUBE EL ARCHIVO
            if (!$this->upload->do_upload('documentoZip')) {
                $success = 'false';
                $msg = $this->upload->display_errors('-', '\n');
                echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                return;
            }
//EXTRAE LOS DOCUMENTOS DEL ARCHIVO MD5.ZIP -> CARPETA_MD5
            $zip = new ZipArchive;
            if ($zip->open($this->config->item('temp_dir') . $nombreMD5) === TRUE) {
                $zip->extractTo($this->config->item('temp_dir') . $nombreMD5carpeta . '/');
                $zip->close();
            } else {
                $success = 'false';
                $msg = $this->translateTag('Document', 'can_not_open_the_zip_file');
                echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                return;
            }
//CUENTA LOS ARCHIVOS VALIDOS   
            $docExtension = new DocExtension();
            $countMD5carpeta = 0;
            foreach (glob($this->config->item('temp_dir') . $nombreMD5carpeta . '/*') as $path) {
//SOLO SI ES ARCHIVO  
                if (!is_dir($path)) {
                    $ruta = explode('.', $path);
                    $file_extension = end($ruta);
//BUSCA LOS ARCHIVOS PERMITIDOS  
                    if (($extension_id = $docExtension->isAllowed($file_extension)) !== false) {
//CUENTA SOLO LAS PERMITIDAS
                        $countMD5carpeta++;
                    } else {
//SI HAY POR LO MENOS UN ARCHIVO NO PERMITIDO NO SIGUE
                        $success = 'false';
                        $msg = $this->translateTag('Document', 'extensions_not_allowed_in_zip_file');
                        echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                        return;
                    }
                }
            }

//---------SUBE EL EXCEL (.XLS)----------------
            $documentoExcel = $this->input->file('documentoExcel');
            $documentoExcel = $documentoExcel['tmp_name'];
            $this->load->library('PHPExcel');
            $sheetIndex = 0;
            if ($documentoExcel) {
                $objPHPExcel = PHPExcel_IOFactory :: load($documentoExcel);
                $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
                $objWorksheet = $objPHPExcel->getActiveSheet();

//VALIDA QUE EL  NUMERO DE LINEAS DEL EXCEL Y DE LOS DOCUMENTOS EN EL ZIP
                $rowsCount = $objWorksheet->getHighestRow();
                //SE RESTA UNO POR LAS CABACERAS
                $rowsCountAux = 0;
                for ($rowIndex = 2; $rowIndex <= $rowsCount; $rowIndex++) {

                    if (trim($objWorksheet->getCell('A' . $rowIndex)->getCalculatedValue()) != null && trim($objWorksheet->getCell('A' . $rowIndex)->getCalculatedValue()) != '') {
                        $rowsCountAux++;
                    }
                }

                if ($rowsCountAux != $countMD5carpeta) {
                    $success = 'false';
//                     $msg = $this->translateTag('Document', 'the_amount_of_zip_files_is_different_the_line_valid_file_excel');
//                     $msg = "El archivo no coincide";
                    $msg = $this->translateTag('Document', 'the_amount_of_zip');
                    echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                    return;
                }

                $rowsCount = $rowsCountAux;

//VALIDA QUE LOS NOMBRES DEL EXCEL ESTEN COMO ARCHIVOS EN EL ZIP

                for ($rowIndex = 2; $rowIndex <= $rowsCount; $rowIndex++) {
                    $nombreDocExcel = trim($objWorksheet->getCell('A' . $rowIndex)->getCalculatedValue());
                    $nombreCategoria = trim($objWorksheet->getCell('D' . $rowIndex)->getCalculatedValue());
                    //VALIDA SI LA CATEGORIA EXISTE
                    $DocCategory = Doctrine::getTable('DocCategory')->finbByNomCategory($nombreCategoria);
                    if (!$DocCategory) {
                        $success = 'false';
                        $msg = $nombreDocExcel . ' - ' . $this->translateTag('Document', 'category_not_valid');
                        echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                        return;
                    }

                    $flag = 0;
                    foreach (glob($this->config->item('temp_dir') . $nombreMD5carpeta . '/*') as $path) {
                        //SOLO SI ES ARCHIVO  
                        if (!is_dir($path)) {
                            $ruta = explode('/', $path);
                            $file_name = end($ruta);
                            $file_name = trim($file_name);
                            if ($nombreDocExcel == $file_name) {
                                $flag = 1;
                            }
                            //-VAL 2.--VALIDA EL NOMBRE DOCUMENTO SEA UNICO SINO MENSAJE DE INSERTAR COMO VERSION
                            $compare = Doctrine_Core::getTable('DocDocument')->compFileName($nombreDocExcel, $this->input->post('node_id'));

                            //Preguntar si el nombre del documento ya existe en la base de datos si existe retornar mensaje "Debe ingresarlo como Version"
                            if ($compare === true) {
                                $success = 'false';
                                $msg = $nombreDocExcel . ' ' . $this->translateTag('Document', 'version_should_be_entered_as');
                                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                                echo $json_data;
                                return;
                            }
                            //FIN -VAL 2.-
                        }
                    }
                    //VALIDA QUE EL ARCHIVO ESTE EN LA RUTA
                    if ($flag == 0) {
                        $success = 'false';
                        $msg = $nombreDocExcel . ' - ' . $this->translateTag('Document', 'file_not_found');
                        echo $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                        return;
                    }
                }

//---------CREA REGISTROS DEL LOS DOCUMENTOS 
                $rowsCount = $objWorksheet->getHighestRow();

                for ($rowIndex = 2; $rowIndex <= $rowsCount; $rowIndex++) {

                    $nombreDocExcel = trim($objWorksheet->getCell('A' . $rowIndex)->getCalculatedValue());
                    foreach (glob($this->config->item('temp_dir') . $nombreMD5carpeta . '/*') as $path) {
                        //SOLO SI ES ARCHIVO  
                        if (!is_dir($path)) {
                            $ruta = explode('/', $path);
                            $file_name = end($ruta);
                            $file_name = trim($file_name);

                            $file_extension = explode('.', $file_name);
                            $file_extension = end($file_extension);
                            //CUANDO ENCUENTRA EL NOMBRE DEL ARCHIVO EN LA CARPETA 
                            if ($nombreDocExcel == $file_name) {

                                if (($extension_id = $docExtension->isAllowed($file_extension)) !== false) {

                                    $node_id = $this->input->post('node_id');

                                    $doc_document_description = trim($objWorksheet->getCell('B' . $rowIndex)->getCalculatedValue());
                                    $doc_version_code_client = trim($objWorksheet->getCell('C' . $rowIndex)->getCalculatedValue());
                                    $nomCategoria = trim($objWorksheet->getCell('D' . $rowIndex)->getCalculatedValue());
                                    $doc_version_keyword = trim($objWorksheet->getCell('E' . $rowIndex)->getCalculatedValue());
                                    $doc_version_internal = trim($objWorksheet->getCell('F' . $rowIndex)->getCalculatedValue());
                                    $doc_version_internal = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($doc_version_internal));
                                    $doc_version_expiration = trim($objWorksheet->getCell('G' . $rowIndex)->getCalculatedValue());
                                    $doc_version_expiration = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($doc_version_expiration));
                                    $doc_version_alert = trim($objWorksheet->getCell('H' . $rowIndex)->getCalculatedValue());
                                    $doc_version_alert_email = trim($objWorksheet->getCell('I' . $rowIndex)->getCalculatedValue());
                                    $doc_version_notification_email = trim($objWorksheet->getCell('J' . $rowIndex)->getCalculatedValue());
                                    $doc_version_comments = trim($objWorksheet->getCell('K' . $rowIndex)->getCalculatedValue());

                                    //BUSCA LA CATEGORIA POR NOMBRE 
                                    $DocCategory = Doctrine::getTable('DocCategory')->finbByNomCategory($nomCategoria);
                                    $doc_category_id = $DocCategory->doc_category_id;



                                    //INSERT DOCUMENTO
                                    $doc = new DocDocument();
                                    $doc->node_id = $node_id;
                                    $doc->doc_category_id = $doc_category_id;
                                    $doc->doc_extension_id = $extension_id; //ojo este es el id de donde encuentra el id
                                    $doc->doc_document_description = $doc_document_description;
                                    $doc->doc_status_id = 0;
                                    $doc->doc_document_filename = $nombreDocExcel;
                                    $doc->doc_current_version_id = NULL;
                                    $doc->save();

                                    //SE CREA UN NOMBRE FALSO CON FORMATO MD5
                                    $doc_name = md5(time() . ' ' . $nombreDocExcel) . '.' . $file_extension;


                                    //INSERT VERSION DEL DOCUMENTO
                                    $version_number = 1;
                                    $version = new DocVersion();
                                    $version->doc_version_code = $version_number;
                                    $version->doc_version_code_client = $doc_version_code_client;
                                    $version->doc_version_filename = $doc_name;
                                    $version->doc_version_comments = $doc_version_comments;
                                    $version->doc_version_internal = $doc_version_internal;
                                    $version->doc_version_expiration = $doc_version_expiration;
                                    $version->doc_version_keyword = $doc_version_keyword;
                                    $version->doc_version_alert = $doc_version_alert;
                                    $version->doc_version_alert_email = $doc_version_alert_email;
                                    $version->doc_version_notification_email = $doc_version_notification_email;
                                    $user_id = $this->auth->get_user_data('user_id');
                                    $version->user_id = $user_id;
                                    $version->doc_document_id = $doc->doc_document_id;
                                    $version->save();

                                    $doc->doc_current_version_id = $version->doc_version_id;
                                    $doc->save();
                                    $doc_version_id = $version->doc_version_id;

                                    //SE MUEVE EL ARCHIVO DE DOCS->DATA A DOCS
                                    copy($this->config->item('temp_dir') . $nombreMD5carpeta . '/' . $nombreDocExcel, $this->config->item('doc_dir') . $doc_name);
                                    $this->addThumb($doc_name, $file_extension);
                                    unlink($this->config->item('temp_dir') . $nombreMD5carpeta . '/' . $nombreDocExcel);


//                                    $this->sendNotification($doc_version_id); // ENVIAR NOTIFICACION
                                }
                            }
                        }
                    }
                }

//BORRA EL ZIP Y LA CARPETA
                unlink($this->config->item('temp_dir') . $nombreMD5);


                $success = 'true';
                $msg = $this->translateTag('General', 'operation_successful');
            }
        } catch (Exception $e) {
            $success = 'false';
            $msg = $e->getMessage();
        }

        echo '{"success": ' . $success . ', "msg":"' . $msg . '"}';
    }

    function formatoExcelDetalle() {
        //--- HEADER EXCEL-- 
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Document', 'detail_excel_format'));
        $sheet->setCellValue('A1', $this->translateTag('General', 'file_name'))
                ->setCellValue('B1', $this->translateTag('General', 'description'))
                ->setCellValue('C1', $this->translateTag('General', 'version'))
                ->setCellValue('D1', $this->translateTag('General', 'category'))
                ->setCellValue('E1', $this->translateTag('Document', 'keywords'))
                ->setCellValue('F1', 'Fecha Documento' . ' (YYYY-MM-DD)')
                ->setCellValue('G1', $this->translateTag('General', 'expiration_date') . ' (YYYY-MM-DD)')
                ->setCellValue('H1', $this->translateTag('Document', 'alert_days'))
                ->setCellValue('I1', $this->translateTag('General', 'expiration_mail_alert'))
                ->setCellValue('J1', $this->translateTag('General', 'mail_notification'))
                ->setCellValue('K1', $this->translateTag('General', 'comment'));


        //-----FIN HEADER--------
        //-----BODY EXCEL----
        $rcount = 1;

        //---FOOTER DEL EXCEL--
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
        $sheet->getColumnDimension('k')->setAutoSize(true);

        $sheet->getStyle('A1:K1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:K1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:K' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

//CREA LA SEGUNDA HOJA
        $this->phpexcel->createSheet();
        $sheet = $this->phpexcel->setActiveSheetIndex(1);
        $sheet->setTitle($this->translateTag('General', 'category'));
        $sheet->setCellValue('A1', $this->translateTag('General', 'file_name'));

        $DocCategorys = Doctrine_Core::getTable('DocCategory')->findAll();
        $rcount2 = 0;
        foreach ($DocCategorys as $DocCategory) {

            $rcount2++;
            $sheet->setCellValueExplicit('A' . $rcount2, $DocCategory->doc_category_name, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);

        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save('./temp/FormatoExcelDetalle' . '.xls');
        echo '{"success": true, "file": "temp/FormatoExcelDetalle' . '.xls"}';
    }

    function createThumb($offset, $limit = 10) {

        $q1 = Doctrine_Query :: create()
                ->from('DocVersion dv')
                ->limit($limit)
                ->offset($offset);

        $results1 = $q1->execute();

        foreach ($results1 as $result) {

            $file_extension = substr(strrchr($result->doc_version_filename, "."), 1); //EXTENSION

            $this->addThumb($result->doc_version_filename, $file_extension);
        }
    }

}
