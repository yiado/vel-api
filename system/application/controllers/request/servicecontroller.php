<?php

/**
 * @package    Controller
 * @subpackage ServiceController
 */
class ServiceController extends APP_Controller {

    function ServiceController() {
        parent::APP_Controller();
    }

    function get() {
        $request = Doctrine_Core::getTable('Service')->retrieveAll($this->filtrosServices());

        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getById() {
        $request = Doctrine_Core::getTable('Service')->findById($this->input->post('service_id'));
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function add() {

        //Recibimos los parametros
        $node_id = $this->input->post('$node_id');
        $service_type_id = $this->input->post('service_type_id');
        $service_factura_numero = $this->input->post('service_factura_numero');
        $service_oc_numero = $this->input->post('service_oc_numero');
        $service_comen_user = $this->input->post('service_comen_user');

        //Obtenemos la conexiï¿½n actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

        //Iniciamos la transacciï¿½n
        $conn->beginTransaction();

        try {
            //Insertamos el nuevo documento en la tabla
            $service = new Service();
            $user_id = $this->auth->get_user_data('user_id');
            $service->user_id = $user_id;
            $service->service_type_id = $service_type_id;
            $service->service_estado_id = 1; //Queda como Solicitado

            $CI = & get_instance();
            $nuevo_folio = Doctrine_Core :: getTable('Service')->lastFolioWo() + 1;
            $service->node_id = $CI->app->generateFolio($node_id);
            $service->service_folio = $CI->app->generateFolio($nuevo_folio);
            $service->service_factura_archivo = $doc_name_factura;
            $service->service_factura_nombre = $file_name_actual_factura . '.' . $file_extension_fatura;
            $service->service_factura_numero = $service_factura_numero;
            $service->service_oc_archivo = $doc_name_oc;
            $service->service_oc_nombre = $file_name_actual_oc . '.' . $file_extension_oc;
            $service->service_oc_numero = $service_oc_numero;
            $service->service_comen_user = $service_comen_user;


            //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
            $service->save();

            //Enviar correo de Alerta de creación de Service
            $this->sendNotification($service->service_id);

            $serviceLog = new ServiceLog();
            $user_id = $this->auth->get_user_data('user_id');
            $serviceLog->user_id = $user_id;
            $serviceLog->service_id = $service->service_id;
            $serviceLog->service_log_detalle = 'Creación de  Service';
            $serviceLog->save();

            //Cagamos la Libreria para Subir Archivos
            $this->load->library('upload');

            // Procedimiento para Subir archivo Factura       
            if (!empty($_FILES['service_factura_nombre']['name'])) {
                // Configuración para la Factura
                $config['upload_path'] = $this->config->item('asset_doc_dir');
                $config['allowed_types'] = $file_extension_fatura;
                $config['file_name'] = $doc_name_factura;

                $this->upload->initialize($config);

                // Subimos archivo de la Factura
                if ($this->upload->do_upload('service_factura_nombre')) {
                    $data = $this->upload->data();
                } else {
                    $success = 'false';
                    $msg = $this->upload->display_errors('-', '\n');
                    throw new Exception($msg);
                }
            }

            // Procedimiento para Subir archivo Orden de Compra
            if (!empty($_FILES['service_oc_nombre']['name'])) {
                // Configuración para de la Orden de Compra
                $config['upload_path'] = $this->config->item('asset_doc_dir');
                $config['allowed_types'] = $file_extension_oc;
                $config['file_name'] = $doc_name_oc;

                // Cargamos la configuración del Archivo 1
                $this->upload->initialize($config);

                // Subimos archivo de la Orden de Compra
                if ($this->upload->do_upload('service_oc_nombre')) {
                    $data = $this->upload->data();
                } else {
                    $success = 'false';
                    $msg = $this->upload->display_errors('-', '\n');
                    throw new Exception($msg);
                }
            }
            //SiTodo OK Sube Archivos
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            // 
            // Si todo OK, commit a la base de datos
            $conn->commit();
        } catch (Exception $e) {
            //Si hay error, rollback de los cambios en la base de datos
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }


        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function update() {
        $service = Doctrine_Core::getTable('Service')->find($this->input->post('service_id'));

        $docExtension = new DocExtension();
        //Para la Fatura
        $file_uploaded_factura = $this->input->file('service_factura_nombre');
        $file_extension_fatura = $this->app->getFileExtension($file_uploaded_factura['name']);
        $file_name_actual_factura = $this->app->getFileName($file_uploaded_factura['name']);


        //Para la Orden de Compra
        $file_uploaded_oc = $this->input->file('service_oc_nombre');
        $file_extension_oc = $this->app->getFileExtension($file_uploaded_oc['name']);
        $file_name_actual_oc = $this->app->getFileName($file_uploaded_oc['name']);


        if ($file_extension_fatura) {
            if ($docExtension->isAllowed($file_extension_fatura) == false) {
                $success = false;
                $msg = $this->translateTag('Documen', 'type_extension_not_allowed');
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            } else {
                $nombre_factura = $file_name_actual_factura . '.' . $file_extension_fatura;
                $nombre_factura_archivo_antes = $service->service_factura_archivo;
            }
        } else {
            $nombre_factura = $service->service_factura_nombre;
        }

        if ($file_extension_oc) {
            if ($docExtension->isAllowed($file_extension_oc) == false) {
                $success = false;
                $msg = $this->translateTag('Documen', 'type_extension_not_allowed');
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            } else {
                $nombre_oc = $file_name_actual_oc . '.' . $file_extension_oc;
                $nombre_oc_archivo_antes = $service->service_oc_archivo;
            }
        } else {
            $nombre_oc = $service->service_oc_nombre;
        }

        if ($nombre_factura == $nombre_oc) {
            $success = false;
            $msg = 'El archivo de la Factura es Igual al archivo de Orden de Compra';
            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
            return;
        } else {

            //Ahora se pueden actualizar los archivos y los datos
            //Obtenemos la conexiï¿½n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciamos la transacciï¿½n
            $conn->beginTransaction();

            try {
                //Cagamos la Libreria para Subir Archivos
                $this->load->library('upload');

                //Actualizar en caso de Cambiar el Archivode Factura 
                if ($file_extension_fatura) {
                    $doc_name_factura = md5($file_name_actual_factura . ' ' . time()) . '.' . $file_extension_fatura;

                    // Procedimiento para Subir archivo Factura       
                    if (!empty($_FILES['service_factura_nombre']['name'])) {
                        $serviceLog = new ServiceLog();
                        $user_id = $this->auth->get_user_data('user_id');
                        $serviceLog->user_id = $user_id;
                        $serviceLog->service_id = $service->service_id;
                        $serviceLog->service_log_detalle = 'Cambio Archivo Factura :' . $service->service_factura_nombre . ' Por  :' . $nombre_factura;
                        $serviceLog->save();

                        $service->service_factura_archivo = $doc_name_factura;
                        $service->service_factura_nombre = $nombre_factura;

                        // Configuración para la Factura
                        $config['upload_path'] = $this->config->item('asset_doc_dir');
                        $config['allowed_types'] = $file_extension_fatura;
                        $config['file_name'] = $doc_name_factura;

                        $this->upload->initialize($config);

                        // Subimos archivo de la Factura
                        if ($this->upload->do_upload('service_factura_nombre')) {
                            $data = $this->upload->data();
                        } else {
                            $success = 'false';
                            $msg = $this->upload->display_errors('-', '\n');
                            throw new Exception($msg);
                        }

                        //Elimina Archivo Antiguo
                        $path = './asset_doc/';
                        $file_full_path = $path . $nombre_factura_archivo_antes;
                        unlink($file_full_path);
                    }
                }

                if ($file_extension_oc) {
                    $doc_name_oc = md5($file_name_actual_oc . ' ' . time()) . '.' . $file_extension_oc;

                    // Procedimiento para Subir archivo Orden de Compra
                    if (!empty($_FILES['service_oc_nombre']['name'])) {
                        $serviceLog = new ServiceLog();
                        $user_id = $this->auth->get_user_data('user_id');
                        $serviceLog->user_id = $user_id;
                        $serviceLog->service_id = $service->service_id;
                        $serviceLog->service_log_detalle = 'Cambio Archivo OC :' . $service->service_oc_nombre . ' Por  :' . $nombre_oc;
                        $serviceLog->save();

                        $service->service_oc_archivo = $doc_name_oc;
                        $service->service_oc_nombre = $nombre_oc;


                        // Configuración para de la Orden de Compra
                        $config['upload_path'] = $this->config->item('asset_doc_dir');
                        $config['allowed_types'] = $file_extension_oc;
                        $config['file_name'] = $doc_name_oc;

                        // Cargamos la configuración del Archivo 1
                        $this->upload->initialize($config);

                        // Subimos archivo de la Orden de Compra
                        if ($this->upload->do_upload('service_oc_nombre')) {
                            $data = $this->upload->data();
                        } else {
                            $success = 'false';
                            $msg = $this->upload->display_errors('-', '\n');
                            throw new Exception($msg);
                        }

                        //Elimina Archivo Antiguo
                        $path = './asset_doc/';
                        $file_full_path = $path . $nombre_oc_archivo_antes;
                        unlink($file_full_path);
                    }
                }




                //Actualizamos datos de la base de datos
                //Recibimos los parametros
                $service_factura_numero = $this->input->post('service_factura_numero');
                $service_oc_numero = $this->input->post('service_oc_numero');
                $service_comen_user = $this->input->post('service_comen_user');


                //Actualizamos Numero de Factura
                if ($service->service_factura_numero != $service_factura_numero) {
                    $serviceLog = new ServiceLog();
                    $user_id = $this->auth->get_user_data('user_id');
                    $serviceLog->user_id = $user_id;
                    $serviceLog->service_id = $service->service_id;
                    $serviceLog->service_log_detalle = 'Cambio Nº de Factura :' . $service->service_factura_numero . ' Por  :' . $service_factura_numero;
                    $serviceLog->save();

                    $service->service_factura_numero = $service_factura_numero;
                }

                //Actualizamos Numero de OC
                if ($service->service_oc_numero != $service_oc_numero) {
                    $serviceLog = new ServiceLog();
                    $user_id = $this->auth->get_user_data('user_id');
                    $serviceLog->user_id = $user_id;
                    $serviceLog->service_id = $service->service_id;
                    $serviceLog->service_log_detalle = 'Cambio Nº de OC :' . $service->service_oc_numero . ' Por  :' . $service_oc_numero;
                    $serviceLog->save();

                    $service->service_oc_numero = $service_oc_numero;
                }

                //Actualizamos Comentario Usuario
                if ($service->service_comen_user != $service_comen_user) {
                    $serviceLog = new ServiceLog();
                    $user_id = $this->auth->get_user_data('user_id');
                    $serviceLog->user_id = $user_id;
                    $serviceLog->service_id = $service->service_id;
                    $serviceLog->service_log_detalle = 'Cambio Comentario :' . $service->service_comen_user . ' Por  :' . $service_comen_user;
                    $serviceLog->save();

                    $service->service_comen_user = $service_comen_user;
                }



                //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
                $service->save();



                //SiTodo OK Sube Archivos
                $success = true;
                $msg = $this->translateTag('General', 'operation_successful');
                // 
                // Si todo OK, commit a la base de datos
                $conn->commit();
            } catch (Exception $e) {
                //Si hay error, rollback de los cambios en la base de datos
                $conn->rollback();
                $success = false;
                $msg = $e->getMessage();
            }


            $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
            echo $json_data;
        }
    }

    function filtrosServices() {
        $user_id = $this->session->userdata('user_id');
        $user_type = $this->session->userdata('user_type');

        $service_type_id = $this->input->post('service_type_id');
        $service_estado_id = $this->input->post('service_estado_id');
        $service_folio = $this->input->post('service_folio');
        $user_username = $this->input->post('user_username');
        $user_email = $this->input->post('user_email');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $service_factura_nombre = $this->input->post('service_factura_nombre');
        $service_factura_numero = $this->input->post('service_factura_numero');

        $service_oc_nombre = $this->input->post('service_oc_nombre');
        $service_oc_numero = $this->input->post('service_oc_numero');

        $filters = array(
            'st.service_type_id = ?' => $service_type_id,
            'se.service_estado_id = ?' => $service_estado_id,
            'service_folio LIKE ?' => (!empty($service_folio) ? '%' . $service_folio . '%' : NULL),
            'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
            'u.user_email = ?' => $user_email,
            'service_fecha >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'service_fecha <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'service_factura_nombre LIKE ?' => (!empty($service_factura_nombre) ? '%' . $service_factura_nombre . '%' : NULL),
            'service_factura_numero LIKE ?' => (!empty($service_factura_numero) ? '%' . $service_factura_numero . '%' : NULL),
            'service_oc_nombre LIKE ?' => (!empty($service_oc_nombre) ? '%' . $service_oc_nombre . '%' : NULL),
            'service_oc_numero LIKE ?' => (!empty($service_oc_numero) ? '%' . $service_oc_numero . '%' : NULL)
        );

        if ($user_type !== 'A') {
            $filters['user_id = ?'] = (!empty($user_id) ? $user_id : NULL );
        }

        return $filters;
    }

    function export() {

        $requests = Doctrine_Core::getTable('Service')->retrieveAll($this->filtrosServices());

        $titulos[] = 'Tipo de Service';
        $titulos[] = 'Nº de Folio';
        $titulos[] = 'Nombre Usuario';
        $titulos[] = 'Email Usuario';
        $titulos[] = 'Fecha Service';
        $titulos[] = 'Nombre Factura';
        $titulos[] = 'Numero de Factura';
        $titulos[] = 'Nombre OC';
        $titulos[] = 'Numero OC';
        $titulos[] = 'Estado Service';
        $titulos[] = 'Comentario';

        $servicees = array();
        foreach ($requests as $request) {
            $date = new DateTime($request->service_fecha);
            $fecha = $date->format('d/m/Y H:i');
            $service = array();
            $service[] = $request->ServiceType->service_type_nombre;
            $service[] = $request->service_folio;
            $service[] = $request->User->user_username;
            $service[] = $request->User->user_email;
            $service[] = $fecha;
            $service[] = $request->service_factura_nombre;
            $service[] = $request->service_factura_numero;
            $service[] = $request->service_oc_nombre;
            $service[] = $request->service_oc_numero;
            $service[] = $request->ServiceEstado->service_estado_nombre;
            $service[] = $request->service_comen_user;
            $servicees[] = $service;
        }

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->fromArray($titulos, null, "A1");
        $sheet->fromArray($servicees, null, "A2");

        $dimensionHoja = $sheet->calculateWorksheetDimension();
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn();

        $sheet->setAutoFilter($dimensionHoja);

        /** Formato de tipo de datos en celdas */
        $sheet->getStyle("B2:B{$ultimaFila}")
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        $sheet->getStyle("E2:E{$ultimaFila}")
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME);

        /** Estilos visuales de celdas */
        $filaTitulos = $sheet->getStyle("A1:{$ultimaColumna}1");
        $filaTitulos->getFont()->applyFromArray(array(
            'bold' => true
        ));
        $filaTitulos->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        /** Aplica para todo el libro */
        $sheet->getStyle($dimensionHoja)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('./temp/' . $this->input->post('file_name') . '.xlsx');

        echo '{"success": true, "file": "temp/' . $this->input->post('file_name') . '.xlsx"}';
    }

    function sendNotification($service_id) {


        $q = Doctrine_Query::create()
                ->from('Service s')
                ->innerJoin('s.ServiceEstado se')
                ->innerJoin('s.ServiceType st')
                ->innerJoin('s.User u')
                ->where('service_id = ?', $service_id);

        $results = $q->fetchOne();

        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['User']['user_email']); //CORREO DESTINATARIO

        $subject = 'Aviso de Creación de service'; //ASUNTO
        //Formatear Fecha
        $date = new DateTime($results['service_fecha']);
        $fecha = $date->format('d/m/Y H:i');

        $body = 'Tipo de Service :' . $results['ServiceType']['service_type_nombre'] . "\n"; //CUERPO DEL MENSAJE
        $body .= 'Estado de Service :' . $results['ServiceEstado']['service_estado_nombre'] . "\n";
        $body .= 'Folio de Service :' . $results['service_folio'] . "\n";
        $body .= 'Nombre de Usuario :' . $results['User']['user_username'] . "\n";
        $body .= 'Fecha de Service :' . $fecha . "\r\n";
        $body .= 'Nombre de Documento de Factura :' . $results['service_factura_nombre'] . "\r\n";
        $body .= 'Numero de Factura :' . $results['service_factura_numero'] . "\r\n";
        $body .= 'Nombre de Documento de Orden de Compra :' . $results['service_oc_nombre'] . "\r\n";
        $body .= 'Numero de Orden de Compra :' . $results['service_oc_numero'] . "\r\n";
        $body .= 'Comentario de Usuario :' . $results['service_comen_user'] . "\r\n";
    }

}
