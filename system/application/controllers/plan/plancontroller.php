<?php

/** @package    Controller
 *  @subpackage PlanController
 */
class PlanController extends APP_Controller {

    function PlanController() {
        parent::APP_Controller();
        $this->load->library('svg');
    }

    /**
     * get
     *
     * Lista las ultimas versiones de planos por categoria
     *
     * @post int node_id
     */
    function get() {


        $node_id = $this->input->post('node_id');
        $plans = Doctrine_Core::getTable('Plan')->retrieveCurrents($node_id);

        //ESTA INTRUCCION SOLO SE OCUPA SI SE ESTA EN UN SERVODOR WINDOWS
        $dir = str_replace("\\", "/", $this->config->item('plan_dir'));
        // si posee algun plano asociado
        if ($plans->count()) {
            $size = $plans->count();
            $plans = $plans->toArray();

            $ruta = 'plans/' . $plans[0]['plan_filename'];

            if (!file_exists($ruta)) {

                $plans[0]['plan_filename'] = "not_image_icon.png";
            }

            echo '({"total":"' . $size . '", "plan_dir": "' . $dir . '", "results":' . $this->json->encode($plans) . '})';
        } else {

            //El caso de no poseer plano se busca si posee linea asociada
            $line = Doctrine_Core::getTable('PlanNode')->findPlanNode($node_id);

            if ($line->count()) {
                $line = $line->toArray();
                $nodeLine = intval($line[0]['nodeLine']);
                $associatedLine = Doctrine_Core::getTable('Plan')->retrieveCurrents($nodeLine);
                if ($associatedLine->count()) {
                    $associatedLine = $associatedLine->toArray();
                    $associatedLine[0]['handler'] = $line[0]['handler'];
                    $associatedLine[0]['plan_node_id'] = $line[0]['plan_node_id'];
                    $associatedLine[0]['plan_section_id'] = $line[0]['plan_section_id'];

                    echo '({"total":"' . $plans->count() . '", "plan_dir": "' . $dir . '", "results":' . $this->json->encode($associatedLine) . '})';
                } else {
                    echo '({"total":"0", "results":[]})';
                }
            }
        }
    }

    function getAssociates() {


        $id_handler = $_POST['id_handler'];
        $plan_id = $_POST['plan_id'];

        $associatedLine = Doctrine_Core::getTable('PlanNode')->findByHandler($id_handler, $plan_id);
        if ($associatedLine->count()) {
            echo '{"total":"' . $associatedLine->count() . '", "results":' . $this->json->encode($associatedLine->toArray()) . '}';
        } else {
            echo '{"total":"0", "results":[]}';
        }
    }

    function getNode() {
        $node_id = $this->input->post('node_id');
        $planNode = Doctrine_Core::getTable('Node')->findByNodeId($node_id);

        if ($planNode->count()) {
            echo '({"total": "' . $planNode->count() . '", "results":' . $this->json->encode($planNode->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getSection() {
        $idSection = $this->input->post('id_section');

        $planSection = Doctrine_Core::getTable('PlanSection')->finBySectionId($idSection);

        if ($planSection->count()) {
            echo '({"total": "' . $planSection->count() . '", "results":' . $this->json->encode($planSection->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * getAll
     *
     * Lista todos las versiones de planos de un nodo y considera el filtro de las categorias
     * Aclaración;
     *    Si plan_current_version = 1 devolverá todas las versiones para los planos
     *    Si plan_current_version = 0 solo devolverá las versiones actuales para los planos
     *
     * @param integer $node_id;
     * @param integer $doc_category_id
     * @param string $plan_description
     * @param string $star_date
     * @param string $end_date
     * @param integer $user_id
     * @param integer $plan_current_version
     * @return json data
     */
    function getAll() {
        //caso especial para la descripción por el comodin usado en el like.
        $plan_description = $this->input->post('plan_description');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $plan_current_version = $this->input->post('plan_current_version');
        $filters = array(
            'node_id = ?' => $this->input->post('node_id'),
            'plan_description LIKE ?' => (!empty($plan_description) ? '%' . $plan_description . '%' : NULL),
            'plan_category_id = ?' => $this->input->post('plan_category_id'),
            'plan_datetime >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'plan_datetime <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'user_id = ?' => $this->input->post('user_id'),
            'plan_current_version = ?' => (!empty($plan_current_version) ? NULL : 1)
        );
        $plans = Doctrine_Core::getTable('Plan')->retrieveByNode($filters);
        echo '({"total":"' . $plans->count() . '", "results":' . $this->json->encode($plans->toArray()) . '})';
    }

    function getResumen() {

        $Node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        if ($this->input->post('node_id') == 'root') {
            //SIEMPRE SE CARGA LA PRIMERA VEZ CON ROOT LUEGO EL PUNTO CORRECTO
            echo '({"total":"' . 0 . '", "results":[]})';
        } else {

            $NodeType = Doctrine_Core::getTable('NodeType')->find($Node->node_type_id);
            $filters = array(
                'p.node_id = ?' => $this->input->post('node_id')
            );

            if ($NodeType) {
                if ((!is_null($NodeType->plan_category_id)) && (!empty($NodeType->plan_category_id))) {
                    $filters['nt.plan_category_id = ?'] = $NodeType->plan_category_id;
                }
            }

            $plans = Doctrine_Core::getTable('Plan')->retrieveByNodeResumen($filters);
            if ($plans) {
                $ruta = 'plans/' . $plans->plan_filename;

                if (file_exists($ruta)) {
                    echo '({"total":"' . $plans->count() . '", "results":' . $this->json->encode($plans->toArray()) . ', "file_exist":true})';
                } else {
                    echo '({"total":"' . $plans->count() . '", "results":' . $this->json->encode($plans->toArray()) . ', "file_exist":false})';
                }
            } else {

                echo '({"total":"' . 0 . '", "results":[]})';
            }
        }
    }

    function imprimirResumen($node_id, $plan_id, $foto_resumen, $lat, $lon) {


        //CONFIGURACION DEL PDF  
        $this->load->library('pdf');
        $this->pdf->SetFont('helvetica', '', 8);
        if ($node_id != 'root') {
            $node = Doctrine_Core::getTable('Node')->find($node_id);
            $node_name = str_replace(' ', '', $node->node_name);
        } else {
            $node_name = '';
        }



        //MUETRA LA FOTO RESUMEN
        if ($foto_resumen != 'null') {
            $this->pdf->AddPage();
            $image = $this->config->item('doc_dir') . 'thumb/' . $foto_resumen;
            $this->pdf->Image($image, '', '', 100, 100, '', '', 'C', false, 300, 'C', false, false, 1, false, false, false);
        }
        //MAPA GOOGLE
        if ($lat != 'null') {
            $this->pdf->AddPage();
            $url = 'http://maps.google.com/maps/api/staticmap?center=' . $lat . ',' . $lon . '&zoom=16&markers=' . $lat . ',' . $lon . '&size=500x300&sensor=TRUE&key=AIzaSyBN6C_ynpkC6Tq4QquMgOo7c-mJ7SOF0ZY';
            $this->pdf->Image($url, 18, '', '', '', '', false, 600, 'C');
        }

        //MUESTRA EL PLAN
        if ($plan_id != 'null') {
            $plan = Doctrine_Core::getTable('Plan')->find($plan_id);
            $this->pdf->ImageSVG($this->config->item('plan_dir') . $plan->plan_filename, $x = 0, $y = 20, $w = '200', $h = '300', $link = '', $align = 'C', $palign = 'C', $border = 0, $fitonpage = true);
        }

        #### MUESTRA INFORMACION DE RESUMEN ####
        if ($node_id != 'root') {

            $this->load->library('TreeNodes');
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $nodes = $treeObject->fetchRoots();

            if ($nodes[0]->node_id == $node_id) {
                $result = Doctrine_Core::getTable('InfraCoordinate')->nodeChildData($node_id);
                $cont = count($result);


                if ($cont >= 1) {
                    $html = '<table width="100%" border="1" cellpadding="2" cellspacing="0">';
                    $html .= '<tr>';
                    $html .= '<td bgcolor="#cccccc"><div align="center"><strong>ESTRUCTURA ASOCIADA</strong></div></td>';
                    $html .= '<td bgcolor="#cccccc"><div align="center"><strong>SUPERFICIE CONSTRUIDA TOTAL</strong></div></td>';
                    $html .= '</tr>';
                    foreach ($result as $item) {

                        $html .= '<tr>';
                        $html .= '<td><div align="center">' . @$item['label'] . '</div></td>';
                        $html .= '<td><div align="center">' . @$item['value'] . '</div></td>';
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                }
            } else {
                $nodeType = Doctrine_Core::getTable('Node')->find($node_id)->NodeType;
                $info = Doctrine_Core::getTable('InfraInfo')->findByNodeId($node_id);

                $result = array();
                $cont = 0;

                //INFORMACION DE INFRAESTRUCTURA
                $infraConfig = Doctrine_Core::getTable('InfraConfiguration')->findByNodeTypeIdConfig($nodeType->node_type_id);

                if ($infraConfig->count() >= 1) {

                    foreach ($infraConfig as $config) {
                        $result[$cont] = array();
                        $result[$cont]['value'] = ($info) ? $info->{$config->infra_attribute} : NULL;
                        $result[$cont]['label'] = $this->translateTag('Infrastructure', $config->infra_attribute);
                        $cont++;
                    }
                }

                //INFORMACION DE OTROS DATOS
                $attributes = Doctrine_Core::getTable('InfraOtherDataAttributeNodeType')->retrieveByNodeTypeFichaResumen($nodeType->node_type_id);


                foreach ($attributes as $att) {
                    $value = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, $att->infra_other_data_attribute_id);

                    if ($value) {//OJO Q AVECES NO ESTA CREADO EL CAMPO EN LA BASE 
                        if ($att->InfraOtherDataAttribute->infra_other_data_attribute_type == 5) {//SI ES TIPO SELECCION TRAE EL NOMBRE DEL CAMPO                      
                            $valorDelCampo = Doctrine_Core::getTable('InfraOtherDataOption')->retrieveByOptionAndAtribute($value->infra_other_data_option_id, $att->InfraOtherDataAttribute->infra_other_data_attribute_id);
                            $result[$cont]['value'] = @$valorDelCampo->infra_other_data_option_name;
                        } else {
                            $result[$cont]['value'] = @$value->infra_other_data_value_value;
                        }
                    } else {
                        //SI NO ESTA CREADO EL CAMPO EN LA BASE DE DATOS LO PONE EN BLANCO
                        $result[$cont]['value'] = '';
                    }
                    $result[$cont]['label'] = $att->InfraOtherDataAttribute->infra_other_data_attribute_name;
                    $cont++;
                }

                if (!empty($result)) {
                    $html = '<table width="100%" border="1" cellpadding="2" cellspacing="0">';
                    foreach ($result as $item) {
                        $html .= '<tr>';
                        $html .= '<td bgcolor="#cccccc"><div align="center"><strong>' . @$item['label'] . '</strong></div></td>';
                        $html .= '<td><div align="center">' . @$item['value'] . '</div></td>';
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                }
            }

            $this->pdf->writeHTML($html, true, false, true, false, '');
        }
        #### FIN ####



        $this->pdf->Output('Ficha Resumen ' . $node_name . '.pdf', 'D');
    }

    /**
     * Guarda y realiza el upload de los archivos de los planos para el nodo y
     * establece la versión actual del plano en la categoria para el nodo correspondiente.
     * @method POST
     * @param integer $node_id
     * @param integer $plan_version
     * @param integer $plan_category_id
     * @param string $plan_comments
     * @param string $plan_description
     * @return mixed array(error => 0|1, 'msg' => '')
     */
    function add() {
        //Recibimos los parametros
        $node_id = $this->input->post('node_id');
        $plan_version = $this->input->post('plan_version');
        $plan_category_id = $this->input->post('plan_category_id');
        $plan_description = $this->input->post('plan_description');
        $plan_comments = $this->input->post('plan_comments');

        /*         * ********************************************* */
        $user_id = $this->auth->get_user_data('user_id');
        /*         * ********************************************* */

        //Obtenemos la conexión actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
        try {
            //Iniciamos la transacción
            $conn->beginTransaction();

            //Insertamos el nuevo SVG del plano en la tabla
            $plan = new Plan();
            $plan->node_id = $node_id;
            $plan->plan_category_id = $plan_category_id;
            $plan->user_id = $user_id;
            $plan->plan_current_version = 1; //Seteado para que sea la versión actual
            $plan->plan_filename = NULL; //En las lineas posteriores se realiza el update para setear este valor
            $plan->plan_version = $plan_version;
            $plan->plan_comments = $plan_comments;
            $plan->plan_description = $plan_description;

            $plan->save();


            //
            //Rescatamos el id
            $plan_last_id = $plan->plan_id;

            if ($plan_category_id != 4) {
                $extension = 'svg';
            } else {
                $extension = 'ifc';
            }

            //Creamos el nombre para el nuevo svg
            $plan_name = 'base_' . $plan_last_id . '.' . $extension;
            $config['upload_path'] = $this->config->item('plan_dir');
            $config['allowed_types'] = $extension;
            $config['file_name'] = $plan_name;

            $node = Doctrine::getTable('Node')->find($node_id);
            $plan_category = Doctrine::getTable('PlanCategory')->find($plan->plan_category_id);

            $this->syslog->register('add_plan', array(
                $plan_name,
                $plan_description,
                $plan_category->plan_category_name,
                $node->getPath()
            )); // registering log
            //Carga de la libreria para el upload
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('documento')) {
                $msg = $this->upload->display_errors('-', '\n');
                throw new Exception($msg);
            } else {

                //Update al nombre del file del plano en la tupla
                $plan->plan_filename = $plan_name;
                $plan->save();


                //Apagar el flag de la categoria actual.
                Doctrine_Core::getTable('Plan')->changeCurrentCategory($node_id, $plan_last_id, $plan_category_id);

                if ($plan_category_id != 4) {

                    // Agregar secciones del plano
                    $this->addPlanSetion($plan_last_id);

                    // Generar svg para igeo
                    $this->generateSVG($plan_last_id);

                    // Si todo OK, commit a la base de datos
                    $conn->commit();

                    $success = true;
                    $msg = $this->translateTag('General', 'operation_successful');
                    $url = '';
                } else {
                    $service_url = $this->config->config['bimapi']['base_url'];
                    $token = $this->getTokenBimApi();
                    $ch = curl_init($service_url);

                    $bim = $this->makeCurlFile($this->config->config['plan_dir'] . 'base_' . $plan_last_id . '.ifc');
                    $data = array('bim' => $bim, 'node_id' => $node_id, 'version' => $plan_version);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        "Authorization: {$token}"
                    ));

                    $result = curl_exec($ch);
                    if (curl_error($ch)) {
                        $result = curl_error($ch);
                    }
                    curl_close($ch);

                    $decoded = json_decode($result, true);

                    if ($decoded['status'] != 200) {
                        $success = false;
                        $msg = $decoded['status'] . '-' . $decoded['msg'];
                        $url = '';
                        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'plan_id' => $plan->plan_id, 'plan_category_id' => $plan->plan_category_id, 'plan_filename' => $plan_name, 'url' => $url));
                        echo $json_data;
                        exit;
                    } elseif ($decoded['status'] == 200) {
                        $conn->commit();
                        $success = true;
                        $msg = $this->translateTag('General', 'operation_successful');
                        $url = $decoded['url'];
                    } else {
                        $success = false;
                        $msg = 'Error al consultar API';
                        $url = '';
                        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'plan_id' => $plan->plan_id, 'plan_category_id' => $plan->plan_category_id, 'plan_filename' => $plan_name, 'url' => $url));
                        echo $json_data;
                        exit;
                    }
                }
            }
        } catch (Exception $e) {
            //Si hay error, rollback de los cambios en la base de datos
            $conn->rollback();
            $success = false;
            $url = '';
            $msg = $e->getMessage();
        }
        //Output
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'plan_id' => $plan->plan_id, 'plan_category_id' => $plan->plan_category_id, 'plan_filename' => $plan_name, 'url' => $url));
        echo $json_data;
    }

    function makeCurlFile($file) {
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile($file, $mime, $name);
        return $output;
    }

    function generateSVG($plan_id) {
        $this->load->helper('file');
        $svg_xml = simplexml_load_file($this->config->config['plan_dir'] . 'base_' . $plan_id . '.svg');
        $layers = $this->svg->getLayers($svg_xml);
        $svg = $this->load->view('svg/svgbase', array('svg_tags' => $this->svg->parseSVG($svg_xml, $layers)), true);
        write_file($this->config->config['plan_dir'] . 'base_' . $plan_id . '.svg', $svg);
    }

    private function addPlanSetion($plan_id) {
        $this->load->helper('file');
        $svg_xml = simplexml_load_file($this->config->config['plan_dir'] . 'base_' . $plan_id . '.svg');

        foreach ($this->svg->getLayers($svg_xml) as $layer) {
            $planSection = new PlanSection();
            $planSection->plan_id = $plan_id;
            $planSection->plan_section_name = $layer;
            $planSection->plan_section_color = $this->config->config['plan_default_layer_color'];
            $planSection->save();
        }
    }

    function getLayers($obj) {

        $buf = array();
        foreach ($obj as $k => $el) {
            if ($k == "CADConverterDwgEntity" && $el->Layer != "") {
                $buf[] = (string) $el->Layer;
            }
        }

        $buf2 = array_unique($buf);
        return $buf2;
    }

    function getgraph1() {
        $this->load->library('graph');

        // Browser usage statistics, %  
        $data = array(29, 21, 18, 18, 4, 10);
        $legends = array('Crome', 'IE', 'Firefox', 'Opera', 'Safari');

        // Creating a new graphic   
        $this->graph = new PieGraph(600, 450);
        $this->graph->SetShadow();

        // Naming the graphic  
        $this->graph->title->Set('Browser usage statistics');
        $this->graph->title->SetFont(FF_VERDANA, FS_BOLD, 14);

        // Legend positioning (%/100)   
        $this->graph->legend->Pos(0.1, 0.2);

        // Creating a 3D pie graphic   
        $p1 = new PiePlot3D($data);

        // Setting the graphic center (%/100)   
        $p1->SetCenter(0.45, 0.5);

        // Setting the ancle   
        $p1->SetAngle(30);

        // Choosing the type   
        $p1->value->SetFont(FF_ARIAL, FS_NORMAL, 12);

        // Setting legends for graphic segments  
        $p1->SetLegends($legends);

        // Adding the diagram to the graphic  

        $this->graph->Add($p1);
        // Showing graphic  

        $this->graph->Stroke();
    }

    function getgraph() {

        //VALIDACIONES
        $dir = str_replace("\\", "/", $this->config->item('temp_dir'));

        $plan = str_replace("\\", "/", $this->config->item('plan_dir'));

        //RESCATA LAS SECCIONES
        $this->load->helper('file');
        $svg_xml = simplexml_load_file($plan . 'base_493.svg');


        foreach ($svg_xml->children() as $child) {

            $role = $child->attributes();
            if ($role->id[0] == "GENERAL") {

                foreach ($child->children() as $child_hijos) {
                    echo '<pre>';
                    print_r($child_hijos->attributes()->fill[0]);
                    echo '</pre>';
                }
            }
        }

        exit;
    }

    /**
     * Determina el metodo que debe ser invocado dependiendo del tipo de documento a exportar.
     * @method POST
     * @param integer $node_id
     * @param string $output_type
     */
    function exportList() {
        $node_id = $this->input->post('node_id');
        $plan_category_id = $this->input->post('plan_category_id');
        $output_type = $this->input->post('output_type');
        $file_name = $this->_factoryDocument($node_id, $plan_category_id, $output_type);
        $json_data = $this->json->encode(array('success' => true, 'file' => $file_name));
        echo $json_data;
    }

    /**
     * Exporta a excel la lista con los planos del nodo
     * @param integer $node_id
     */
    function _factoryDocument($node_id, $plan_category_id, $output_type = 'p') {
        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');
        $planTable = Doctrine_Core::getTable('Plan');
        $plans = $planTable->retrieveByNodeExport($node_id);

        $sheet->setCellValue('A1', $this->translateTag('General', 'version'))
                ->setCellValue('B1', $this->translateTag('General', 'description'))
                ->setCellValue('C1', $this->translateTag('General', 'category'))
                ->setCellValue('D1', $this->translateTag('General', 'charger'))
                ->setCellValue('E1', $this->translateTag('General', 'creation'))
                ->setCellValue('F1', $this->translateTag('General', 'commentary'));


        $rcount = 1;

        foreach ($plans as $plan) {
            $rcount++;
            $sheet->setCellValueExplicit('A' . $rcount, $plan->plan_version, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('B' . $rcount, $plan->plan_description, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('C' . $rcount, $plan->PlanCategory->plan_category_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('D' . $rcount, $plan->User->user_name, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('E' . $rcount, $plan->plan_datetime, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValueExplicit('F' . $rcount, $plan->plan_comments, PHPExcel_Cell_DataType::TYPE_STRING);
        }

        $sheet->getStyle('A1:F1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:F1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:F' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        if ($output_type == 'e') {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);

            $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
            $extension = '.xls';
        } else {
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(25);
            $sheet->getColumnDimension('E')->setWidth(35);
            $sheet->getColumnDimension('F')->setWidth(25);

            $this->phpexcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'PDF');
            $extension = '.pdf';
        }
        $file_name = $this->input->post('file_name') . $extension;
        $objWriter->save($this->app->getTempFileDir($file_name));


        $this->syslog->register('export_list_plan', array(
            $file_name
        )); // registering log

        return $file_name;
    }

    function printPDFOld($plan_id) {
        $plan = Doctrine_Core::getTable('Plan')->find($plan_id);
        $node = Doctrine_Core::getTable('Node')->find($plan->node_id);
        $node_name = str_replace(' ', '', $node->node_name);

        $this->load->library('pdf');
        $this->pdf->setPageOrientation('l'); // PDF_PAGE_ORIENTATION---> 'l' or 'p
        $this->pdf->SetFont('helvetica', '', 8);

        $this->pdf->ImageSVG($this->config->item('plan_dir') . $plan->plan_filename, $x = 5, $y = 20, $w = '', $h = '', $link = '', $align = '', $palign = '', $border = 0, $fitonpage = false);
        $this->pdf->Output($node_name . '.pdf', 'D');
    }

    function printPDF() {

        $plan_id = $this->input->post('plan_id');
        $plan = Doctrine_Core::getTable('Plan')->find($plan_id);
        $node = Doctrine_Core::getTable('Node')->find($plan->node_id);
        $node_name = str_replace(' ', '', $node->node_name);

        $data[0] = htmlentities(utf8_encode($node_name), 0, 'UTF-8');
        $data['success'] = true;
        echo json_encode($data);
    }

    function download($plan_id) {
        $this->load->helper('download');
        $plan = Doctrine_Core::getTable('Plan')->find($plan_id);
        $data = file_get_contents($this->config->item('plan_dir') . $plan->plan_filename); // Read the file's contents
        force_download($plan->plan_filename, $data);
    }

    function changePlan() {

        try {

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            $params = json_decode($this->input->post('json_params'));
        } catch (Exception $e) {
            //Si hay error, rollback de los cambios en la base de datos
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg, 'plan_id' => $params->plan_id));
        echo $json_data;
    }

    function getDataPlan() {


        if ($this->input->post('plan_name')) {
            $dataPlan = Doctrine_Core::getTable('Plan')->findByPlanName($this->input->post('plan_name'));


            if ($dataPlan) {

                echo '({"total":"' . $dataPlan->count() . '", "results":' . $this->json->encode($dataPlan->toArray()) . '})';
            } else {

                echo '({"total":"' . 0 . '", "results":[]})';
            }
        } else {
            echo '({"total":"' . 0 . '", "results":[]})';
        }
    }

    function setPlanPortada() {


        $plan_id = $this->input->post('plan_id');


        $PlanSelected = Doctrine_Core::getTable('Plan')->find($plan_id);


        $filters = array(
            'node_id = ?' => $PlanSelected->node_id,
            'plan_cover = ?' => 1
        );

        $plans = Doctrine_Core::getTable('Plan')->retrieveByNode($filters);
        echo 'plans: ';
        print_r($plans);
        exit;
        if ($plans->count()) {
            foreach ($plans as $Plan) {
                $Plan->plan_cover = null;
                $Plan->save();
            }
        }

        $PlanSelected->plan_cover = 1;
        $PlanSelected->save();
    }

    function getTokenBimApi() {
        $serviceUrl = "{$this->config->config['bimapi']['base_url']}authenticate";
        $ch = curl_init($serviceUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->config->config['bimapi']['credenciales']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_error($ch)) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        $decoded = json_decode($result, 1);
        return $decoded['token'];
    }

    function getBim() {
        $node_id = $this->input->post('node_id');
        $plan_category_id = $this->input->post('plan_category_id');

        $PlanTable = Doctrine_Core::getTable('Plan')->retrieveCurrentBIM($node_id, $plan_category_id);


        if ($PlanTable) {

            $version = $PlanTable->plan_version;
            $fileName = $PlanTable->plan_filename;

            $url = "{$this->config->config['bimapi']['base_url']}" . $node_id . '/' . $version . '/' . $fileName;
            $token = $this->getTokenBimApi();
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: {$token}"
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_response = curl_exec($curl);
            if ($curl_response === false) {
                $info = curl_getinfo($curl);

                echo '({"total":"0", "results":[]})';
                exit;
            }
            curl_close($curl);
            $decoded = json_decode($curl_response);

            if (isset($decoded[0]->status) != 200) {
                echo '({"total":"0", "results":[]})';
            } else {
                echo '({"total":"' . count($decoded) . '", "results":' . $this->json->encode($decoded) . '})';
            }
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getBimVersion() {

        $plan_id = $this->input->post('plan_id');


        $PlanTable = Doctrine_Core::getTable('Plan')->find($plan_id);

        if ($PlanTable) {

            $version = $PlanTable->plan_version;
            $fileName = $PlanTable->plan_filename;
            $node_id = $PlanTable->node_id;

            $url = "{$this->config->config['bimapi']['base_url']}" . $node_id . '/' . $version . '/' . $fileName;

            $token = $this->getTokenBimApi();
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: {$token}"
            ));

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $curl_response = curl_exec($curl);
            if ($curl_response === false) {
                $info = curl_getinfo($curl);
                echo '({"total":"0", "results":[]})';
                exit;
            }
            curl_close($curl);
            $decoded = json_decode($curl_response);

            if (isset($decoded[0]->status) != 200) {
                echo '({"total":"0", "results":[]})';
            } else {
                echo '({"total":"' . count($decoded) . '", "results":' . $this->json->encode($decoded) . '})';
            }
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
