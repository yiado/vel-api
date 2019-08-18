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
        //Obtenemos la conexión actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

        //Iniciamos la transacción
        $conn->beginTransaction();

        try {
            //Insertamos el nuevo documento en la tabla
            $service = new Service();
            $user_id = $this->auth->get_user_data('user_id');
            $service->node_id = $this->input->post('node_id');
            $service->user_id = $user_id;
            $service->service_type_id = $this->input->post('service_type_id');
            $service->service_status_id = 1;
            $service->service_organism = $this->input->post('service_organism');
            $service->service_phone = $this->input->post('service_phone');
            $service->service_commentary = $this->input->post('service_commentary');
            $service->save();

            //Enviar correo de Alerta de creación de Service
            $this->sendNotification($service->service_id);

            $serviceLog = new ServiceLog();
            $serviceLog->user_id = $user_id;
            $serviceLog->service_id = $service->service_id;
            $serviceLog->service_log_detail = 'Creación de Solicitud de Servicio';
            $serviceLog->save();

            //Cagamos la Libreria para Subir Archivos
            $this->load->library('upload');

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');

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

        $node_id = (int) $this->input->post('node_id');

        $service_type_id = $this->input->post('service_type_id');
        $service_status_id = $this->input->post('service_status_id');
        $user_username = $this->input->post('user_username');
        $user_email = $this->input->post('user_email');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $service_phone = $this->input->post('service_phone');
        $service_organism = $this->input->post('service_organism');

        $filters = array(
            'node_id = ?' => $node_id,
            'st.service_type_id = ?' => $service_type_id,
            'se.service_status_id = ?' => $service_status_id,
            'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
            'u.user_email = ?' => $user_email,
            'se.service_phone = ?' => $service_phone,
            'service_date >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'service_date <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'service_organism LIKE ?' => (!empty($service_organism) ? '%' . $service_organism . '%' : NULL)
        );

        if ($user_type !== 'A') {
            $filters['user_id = ?'] = (!empty($user_id) ? $user_id : NULL );
        }

        return $filters;
    }

    function export() {

        $requests = Doctrine_Core::getTable('Service')->retrieveAll($this->filtrosServices());

        $titulos[] = 'Tipo de Servicio';
        $titulos[] = 'Estado';
        $titulos[] = 'Nombre Usuario';
        $titulos[] = 'Email Usuario';
        $titulos[] = 'Telefono';
        $titulos[] = 'Fecha Servicio';
        $titulos[] = 'Organismo';
        $titulos[] = 'Comentario';

        $servicees = array();
        foreach ($requests as $request) {
            $date = new DateTime($request->service_date);
            $fecha = $date->format('d/m/Y H:i');
            $service = array();
            $service[] = $request->ServiceType->service_type_name;
            $service[] = $request->ServiceStatus->service_status_name;
            $service[] = $request->User->user_username;
            $service[] = $request->User->user_email;
            $service[] = $request->service_phone;
            $service[] = $fecha;
            $service[] = $request->service_organism;
            $service[] = $request->service_commentary;
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
        $sheet->getStyle("F2:F{$ultimaFila}")
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
                ->innerJoin('s.ServiceStatus se')
                ->innerJoin('s.ServiceType st')
                ->innerJoin('s.User u')
                ->where('service_id = ?', $service_id);

        $results = $q->fetchOne();

        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['User']['user_email']); //CORREO DESTINATARIO

        $subject = 'Aviso de Creación de service'; //ASUNTO
        //Formatear Fecha
        $date = new DateTime($results['service_date']);
        $fecha = $date->format('d/m/Y H:i');

        $body = 'Tipo de Servicio :' . $results['ServiceType']['service_type_name'] . "\n"; //CUERPO DEL MENSAJE
        $body .= 'Estado del Servicio :' . $results['ServiceStatus']['service_status_name'] . "\n";
        $body .= 'Nombre de Usuario :' . $results['User']['user_username'] . "\n";
        $body .= 'Fecha de Servicio :' . $fecha . "\r\n";
        $body .= 'Teléfono :' . $results['service_phone'] . "\r\n";
        $body .= 'Organismo :' . $results['service_organism'] . "\r\n";
        $body .= 'Comentario de Usuario :' . $results['service_commentary'] . "\r\n";
    }

}
