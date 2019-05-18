<?php

/**
 * @package    Controller
 * @subpackage SolicitudController
 */
class SolicitudController extends APP_Controller
{
    function SolicitudController ()
    {
        parent::APP_Controller ();
    }

    function get ()
    {
        $user_id = $this->session->userdata('user_id');
        $user_type = $this->session->userdata('user_type');
        
        $solicitud_type_id = $this->input->post('solicitud_type_id');
        $solicitud_estado_id = $this->input->post('solicitud_estado_id');
        $solicitud_folio = $this->input->post('solicitud_folio');
        $user_username = $this->input->post('user_username');
        $user_email = $this->input->post('user_email');
        
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $solicitud_factura_nombre = $this->input->post('solicitud_factura_nombre');
        $solicitud_factura_numero = $this->input->post('solicitud_factura_numero');
        
        $solicitud_oc_nombre = $this->input->post('solicitud_oc_nombre');
        $solicitud_oc_numero = $this->input->post('solicitud_oc_numero');
        
        if ($user_type == 'A') {
            $filters = array(
                'st.solicitud_type_id = ?' => $solicitud_type_id,
                'se.solicitud_estado_id = ?' => $solicitud_estado_id,
                'solicitud_folio LIKE ?' => (!empty($solicitud_folio) ? '%' . $solicitud_folio . '%' : NULL),
                'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
                'u.user_email = ?' => $user_email,
                'solicitud_fecha >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
                'solicitud_fecha <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
                'solicitud_factura_nombre LIKE ?' => (!empty($solicitud_factura_nombre) ? '%' . $solicitud_factura_nombre . '%' : NULL),
                'solicitud_factura_numero LIKE ?' => (!empty($solicitud_factura_numero) ? '%' . $solicitud_factura_numero . '%' : NULL),
                'solicitud_oc_nombre LIKE ?' => (!empty($solicitud_oc_nombre) ? '%' . $solicitud_oc_nombre . '%' : NULL),
                'solicitud_oc_numero LIKE ?' => (!empty($solicitud_oc_numero) ? '%' . $solicitud_oc_numero . '%' : NULL)
            );
        } else {
            $filters = array(
                'st.solicitud_type_id = ?' => $solicitud_type_id,
                'se.solicitud_estado_id = ?' => $solicitud_estado_id,
                'solicitud_folio LIKE ?' => (!empty($solicitud_folio) ? '%' . $solicitud_folio . '%' : NULL),
                'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
                'u.user_email = ?' => $user_email,
                'solicitud_fecha >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
                'solicitud_fecha <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
                'solicitud_factura_nombre LIKE ?' => (!empty($solicitud_factura_nombre) ? '%' . $solicitud_factura_nombre . '%' : NULL),
                'solicitud_factura_numero LIKE ?' => (!empty($solicitud_factura_numero) ? '%' . $solicitud_factura_numero . '%' : NULL),
                'solicitud_oc_nombre LIKE ?' => (!empty($solicitud_oc_nombre) ? '%' . $solicitud_oc_nombre . '%' : NULL),
                'solicitud_oc_numero LIKE ?' => (!empty($solicitud_oc_numero) ? '%' . $solicitud_oc_numero . '%' : NULL),
                'user_id = ?' => (!empty($user_id) ? $user_id : NULL )
            );
        }
        
            
        
        
        $request = Doctrine_Core::getTable('Solicitud')->retrieveAll($filters);
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
        
    }
    
    function getById ()
    {
        $solicitud_id = $this->input->post('solicitud_id');
                
        $request = Doctrine_Core::getTable('Solicitud')->findById($solicitud_id);
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
        
    }

    function add ()
    {
        
        $docExtension = new DocExtension();
        //Para la Fatura
        $file_uploaded_factura = $this->input->file('solicitud_factura_nombre');
        $file_extension_fatura = $this->app->getFileExtension($file_uploaded_factura['name']);
        $file_name_actual_factura = $this->app->getFileName($file_uploaded_factura['name']);
        
        
        //Para la Orden de Compra
        $file_uploaded_oc = $this->input->file('solicitud_oc_nombre');
        $file_extension_oc = $this->app->getFileExtension($file_uploaded_oc['name']);
        $file_name_actual_oc = $this->app->getFileName($file_uploaded_oc['name']);
        
        if (($docExtension->isAllowed($file_extension_fatura)) !== false && ($docExtension->isAllowed($file_extension_oc)) !== false) {
            //Recibimos los parametros
            $solicitud_type_id= $this->input->post('solicitud_type_id');
            $solicitud_factura_numero = $this->input->post('solicitud_factura_numero');
            $solicitud_oc_numero = $this->input->post('solicitud_oc_numero');
            $solicitud_comen_user = $this->input->post('solicitud_comen_user');

            $doc_name_factura = md5($file_name_actual_factura . ' ' . time()) . '.' . $file_extension_fatura;
            $doc_name_oc = md5($file_name_actual_oc . ' ' . time()) . '.' . $file_extension_oc;
            

            //Obtenemos la conexiï¿½n actual
            $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

            //Iniciamos la transacciï¿½n
            $conn->beginTransaction();

            try {
                //Insertamos el nuevo documento en la tabla
                $solicitud = new Solicitud();
                $user_id = $this->auth->get_user_data('user_id');
                $solicitud->user_id = $user_id;
                $solicitud->solicitud_type_id = $solicitud_type_id;
                $solicitud->solicitud_estado_id = 1; //Queda como Solicitado
                
                $CI = & get_instance();
                $nuevo_folio = Doctrine_Core :: getTable('Solicitud')->lastFolioWo() + 1;
                $solicitud->solicitud_folio = $CI->app->generateFolio($nuevo_folio);;
                $solicitud->solicitud_factura_archivo = $doc_name_factura;
                $solicitud->solicitud_factura_nombre = $file_name_actual_factura . '.' . $file_extension_fatura;
                $solicitud->solicitud_factura_numero = $solicitud_factura_numero;
                $solicitud->solicitud_oc_archivo = $doc_name_oc;
                $solicitud->solicitud_oc_nombre = $file_name_actual_oc . '.' . $file_extension_oc;
                $solicitud->solicitud_oc_numero = $solicitud_oc_numero;
                $solicitud->solicitud_comen_user = $solicitud_comen_user;

              
                //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
                $solicitud->save();
                
                //Enviar correo de Alerta de creación de Solicitud
                $this->sendNotification($solicitud->solicitud_id);
                
                $solicitudLog = new SolicitudLog();
                $user_id = $this->auth->get_user_data('user_id');
                $solicitudLog->user_id = $user_id;
                $solicitudLog->solicitud_id = $solicitud->solicitud_id;
                $solicitudLog->solicitud_log_detalle = 'Creación de  Solicitud';
                $solicitudLog->save();

                //Cagamos la Libreria para Subir Archivos
                $this->load->library('upload');
                
                // Procedimiento para Subir archivo Factura       
                if (!empty($_FILES['solicitud_factura_nombre']['name']))
                {
                    // Configuración para la Factura
                    $config['upload_path'] = $this->config->item('asset_doc_dir');
                    $config['allowed_types'] = $file_extension_fatura;
                    $config['file_name'] = $doc_name_factura;

                    $this->upload->initialize($config);

                    // Subimos archivo de la Factura
                    if ($this->upload->do_upload('solicitud_factura_nombre'))
                    {
                        $data = $this->upload->data();
                    }
                    else
                    {
                        $success = 'false';
                        $msg = $this->upload->display_errors('-', '\n');
                        throw new Exception($msg);
                    }

                }   
                
                // Procedimiento para Subir archivo Orden de Compra
                if (!empty($_FILES['solicitud_oc_nombre']['name']))
                {
                    // Configuración para de la Orden de Compra
                    $config['upload_path'] = $this->config->item('asset_doc_dir');
                    $config['allowed_types'] = $file_extension_oc;
                    $config['file_name'] = $doc_name_oc;

                    // Cargamos la configuración del Archivo 1
                    $this->upload->initialize($config);

                    // Subimos archivo de la Orden de Compra
                    if ($this->upload->do_upload('solicitud_oc_nombre'))
                    {
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
        } else {
            $success = false;
            $msg = $this->translateTag('Documen', 'type_extension_not_allowed');
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
        
    }

//    function update ()
//    {
//        $solicitud = Doctrine_Core::getTable('Solicitud')->find($this->input->post('solicitud_id'));
//        
//        //Obtenemos la conexiï¿½n actual
//        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
//
//        //Iniciamos la transacciï¿½n
//        $conn->beginTransaction();
//
//        try {
//            //Cagamos la Libreria para Subir Archivos
//            $this->load->library('upload');
//
//            //Actualizamos datos de la base de datos
//
//            //Recibimos los parametros
//            $solicitud_factura_numero = $this->input->post('solicitud_factura_numero');
//            $solicitud_oc_numero = $this->input->post('solicitud_oc_numero');
//            $solicitud_comen_user = $this->input->post('solicitud_comen_user');
//            $solicitud_comen_admin = $this->input->post('solicitud_comen_admin');
//
//
//            //Actualizamos Numero de Factura
//            if ($solicitud->solicitud_factura_numero != $solicitud_factura_numero){
//                $solicitud->solicitud_factura_numero = $solicitud_factura_numero;
//            }
//
//            //Actualizamos Numero de OC
//            if ($solicitud->solicitud_oc_numero != $solicitud_oc_numero){
//                $solicitud->solicitud_oc_numero = $solicitud_oc_numero;
//            }
//
//            //Actualizamos Comentario Usuario
//            if ($solicitud->solicitud_comen_user != $solicitud_comen_user){
//                $solicitud->solicitud_comen_user = $solicitud_comen_user;
//            }
//
//            //Actualizamos Comentario Administrador
//            if ($solicitud->solicitud_comen_admin != $solicitud_comen_admin){
//                $solicitud->solicitud_comen_admin = $solicitud_comen_admin;
//            }
//
//            //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
//            $solicitud->save();
//
//            //SiTodo OK Sube Archivos
//            $success = true;
//            $msg = $this->translateTag('General', 'operation_successful');
//            // 
//            // Si todo OK, commit a la base de datos
//            $conn->commit();
//        } catch (Exception $e) {
//            //Si hay error, rollback de los cambios en la base de datos
//            $conn->rollback();
//            $success = false;
//            $msg = $e->getMessage();
//        }
//        
//
//        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
//        echo $json_data;
//        
//    }
    
    function update ()
    {
        $solicitud = Doctrine_Core::getTable('Solicitud')->find($this->input->post('solicitud_id'));
        
        $docExtension = new DocExtension();
        //Para la Fatura
        $file_uploaded_factura = $this->input->file('solicitud_factura_nombre');
        $file_extension_fatura = $this->app->getFileExtension($file_uploaded_factura['name']);
        $file_name_actual_factura = $this->app->getFileName($file_uploaded_factura['name']);
        
        
        //Para la Orden de Compra
        $file_uploaded_oc = $this->input->file('solicitud_oc_nombre');
        $file_extension_oc = $this->app->getFileExtension($file_uploaded_oc['name']);
        $file_name_actual_oc = $this->app->getFileName($file_uploaded_oc['name']);
        
        
        if ($file_extension_fatura){
            if($docExtension->isAllowed($file_extension_fatura) == false){
                $success = false;
                $msg = $this->translateTag('Documen', 'type_extension_not_allowed');
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            } else {
                $nombre_factura = $file_name_actual_factura . '.' . $file_extension_fatura;
                $nombre_factura_archivo_antes = $solicitud->solicitud_factura_archivo;
            }
        } else {
            $nombre_factura = $solicitud->solicitud_factura_nombre;
        }
        
        if ($file_extension_oc){
            if($docExtension->isAllowed($file_extension_oc) == false){
                $success = false;
                $msg = $this->translateTag('Documen', 'type_extension_not_allowed');
                $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
                echo $json_data;
                return;
            } else {
                $nombre_oc = $file_name_actual_oc . '.' . $file_extension_oc;
                $nombre_oc_archivo_antes = $solicitud->solicitud_oc_archivo;
            }
        } else {
            $nombre_oc = $solicitud->solicitud_oc_nombre;
        }
        
        if ($nombre_factura == $nombre_oc){
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
                if ($file_extension_fatura){
                    $doc_name_factura = md5($file_name_actual_factura . ' ' . time()) . '.' . $file_extension_fatura;
                    
                    // Procedimiento para Subir archivo Factura       
                    if (!empty($_FILES['solicitud_factura_nombre']['name']))
                    {
                        $solicitudLog = new SolicitudLog();
                        $user_id = $this->auth->get_user_data('user_id');
                        $solicitudLog->user_id = $user_id;
                        $solicitudLog->solicitud_id = $solicitud->solicitud_id;
                        $solicitudLog->solicitud_log_detalle = 'Cambio Archivo Factura :' . $solicitud->solicitud_factura_nombre . ' Por  :' .  $nombre_factura;
                        $solicitudLog->save();
                        
                        $solicitud->solicitud_factura_archivo = $doc_name_factura;
                        $solicitud->solicitud_factura_nombre = $nombre_factura;
                        
                        // Configuración para la Factura
                        $config['upload_path'] = $this->config->item('asset_doc_dir');
                        $config['allowed_types'] = $file_extension_fatura;
                        $config['file_name'] = $doc_name_factura;

                        $this->upload->initialize($config);

                        // Subimos archivo de la Factura
                        if ($this->upload->do_upload('solicitud_factura_nombre'))
                        {
                            $data = $this->upload->data();
                        }
                        else
                        {
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
                
                if ($file_extension_oc){
                    $doc_name_oc = md5($file_name_actual_oc . ' ' . time()) . '.' . $file_extension_oc;
                    
                    // Procedimiento para Subir archivo Orden de Compra
                    if (!empty($_FILES['solicitud_oc_nombre']['name']))
                    {
                        $solicitudLog = new SolicitudLog();
                        $user_id = $this->auth->get_user_data('user_id');
                        $solicitudLog->user_id = $user_id;
                        $solicitudLog->solicitud_id = $solicitud->solicitud_id;
                        $solicitudLog->solicitud_log_detalle = 'Cambio Archivo OC :' . $solicitud->solicitud_oc_nombre . ' Por  :' .  $nombre_oc;
                        $solicitudLog->save();
                        
                        $solicitud->solicitud_oc_archivo = $doc_name_oc;
                        $solicitud->solicitud_oc_nombre = $nombre_oc;
                        
                
                        // Configuración para de la Orden de Compra
                        $config['upload_path'] = $this->config->item('asset_doc_dir');
                        $config['allowed_types'] = $file_extension_oc;
                        $config['file_name'] = $doc_name_oc;

                        // Cargamos la configuración del Archivo 1
                        $this->upload->initialize($config);

                        // Subimos archivo de la Orden de Compra
                        if ($this->upload->do_upload('solicitud_oc_nombre'))
                        {
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
                $solicitud_factura_numero = $this->input->post('solicitud_factura_numero');
                $solicitud_oc_numero = $this->input->post('solicitud_oc_numero');
                $solicitud_comen_user = $this->input->post('solicitud_comen_user');


                //Actualizamos Numero de Factura
                if ($solicitud->solicitud_factura_numero != $solicitud_factura_numero){
                    $solicitudLog = new SolicitudLog();
                    $user_id = $this->auth->get_user_data('user_id');
                    $solicitudLog->user_id = $user_id;
                    $solicitudLog->solicitud_id = $solicitud->solicitud_id;
                    $solicitudLog->solicitud_log_detalle = 'Cambio Nº de Factura :' . $solicitud->solicitud_factura_numero . ' Por  :' .  $solicitud_factura_numero;
                    $solicitudLog->save();
                    
                    $solicitud->solicitud_factura_numero = $solicitud_factura_numero;
                }

                //Actualizamos Numero de OC
                if ($solicitud->solicitud_oc_numero != $solicitud_oc_numero){
                    $solicitudLog = new SolicitudLog();
                    $user_id = $this->auth->get_user_data('user_id');
                    $solicitudLog->user_id = $user_id;
                    $solicitudLog->solicitud_id = $solicitud->solicitud_id;
                    $solicitudLog->solicitud_log_detalle = 'Cambio Nº de OC :' . $solicitud->solicitud_oc_numero . ' Por  :' .  $solicitud_oc_numero;
                    $solicitudLog->save();
                    
                    $solicitud->solicitud_oc_numero = $solicitud_oc_numero;
                }

                //Actualizamos Comentario Usuario
                if ($solicitud->solicitud_comen_user != $solicitud_comen_user){
                    $solicitudLog = new SolicitudLog();
                    $user_id = $this->auth->get_user_data('user_id');
                    $solicitudLog->user_id = $user_id;
                    $solicitudLog->solicitud_id = $solicitud->solicitud_id;
                    $solicitudLog->solicitud_log_detalle = 'Cambio Comentario :' . $solicitud->solicitud_comen_user . ' Por  :' .  $solicitud_comen_user;
                    $solicitudLog->save();
                    
                    $solicitud->solicitud_comen_user = $solicitud_comen_user;
                }



                //ESTO AUMENTA LA CUENTA DE LOS ARCHIVOS DEL ACTIVO
                $solicitud->save();
                
                

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

    function delete ()
    {
        
    }
    
    function downloadFactura($solicitud_id) {
        $this->load->helper('download');
        $solicitud_document = Doctrine_Core::getTable('Solicitud')->find($solicitud_id);
        $file_name = $solicitud_document->solicitud_factura_nombre;
        $data = file_get_contents($this->config->item('asset_doc_dir') . $solicitud_document->solicitud_factura_archivo); // Read the file's contents
        force_download($file_name, $data);
    }
    
    function downloadOC($solicitud_id) {
        $this->load->helper('download');
        $solicitud_document = Doctrine_Core::getTable('Solicitud')->find($solicitud_id);
        $file_name = $solicitud_document->solicitud_oc_nombre;
        $data = file_get_contents($this->config->item('asset_doc_dir') . $solicitud_document->solicitud_oc_archivo); // Read the file's contents
        force_download($file_name, $data);
    }
    
    function export() {
        //--- HEADER EXCEL--
        $solicitud_type_id = $this->input->post('solicitud_type_id');
        $solicitud_estado_id = $this->input->post('solicitud_estado_id');
        $solicitud_folio = $this->input->post('solicitud_folio');
        $user_username = $this->input->post('user_username');
        $user_email = $this->input->post('user_email');
        
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $solicitud_factura_nombre = $this->input->post('solicitud_factura_nombre');
        $solicitud_factura_numero = $this->input->post('solicitud_factura_numero');
        
        $solicitud_oc_nombre = $this->input->post('solicitud_oc_nombre');
        $solicitud_oc_numero = $this->input->post('solicitud_oc_numero');
        
        $filters = array(
            'st.solicitud_type_id = ?' => $solicitud_type_id,
            'se.solicitud_estado_id = ?' => $solicitud_estado_id,
            'solicitud_folio LIKE ?' => (!empty($solicitud_folio) ? '%' . $solicitud_folio . '%' : NULL),
            'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
            'u.user_email = ?' => $user_email,
            'solicitud_fecha >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'solicitud_fecha <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'solicitud_factura_nombre LIKE ?' => (!empty($solicitud_factura_nombre) ? '%' . $solicitud_factura_nombre . '%' : NULL),
            'solicitud_factura_numero LIKE ?' => (!empty($solicitud_factura_numero) ? '%' . $solicitud_factura_numero . '%' : NULL),
            'solicitud_oc_nombre LIKE ?' => (!empty($solicitud_oc_nombre) ? '%' . $solicitud_oc_nombre . '%' : NULL),
            'solicitud_oc_numero LIKE ?' => (!empty($solicitud_oc_numero) ? '%' . $solicitud_oc_numero . '%' : NULL)
        );
        
        $requests = Doctrine_Core::getTable('Solicitud')->retrieveAll($filters);
       
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->setCellValue('A1', 'Tipo de Solicitud')
                ->setCellValue('B1', 'Nº de Folio')
                ->setCellValue('C1', 'Nombre Usuario')
                ->setCellValue('D1', 'Email Usuario')
                ->setCellValue('E1', 'Fecha Solicitud')
                ->setCellValue('F1', 'Nombre Factura')
                ->setCellValue('G1', 'Numero de Factura')
                ->setCellValue('H1', 'Nombre OC')
                ->setCellValue('I1', 'Numero OC')
                ->setCellValue('J1', 'Estado Solicitud')
                ->setCellValue('K1', 'Comentario');

        //-----FIN HEADER--------
        $rcount = 1;
        
        foreach ($requests as  $request) {
            $rcount++;
            
            $date = new DateTime($request->solicitud_fecha);
            $fecha = $date->format('d/m/Y H:i'); 
            
            $sheet->setCellValue('A' . $rcount, $request->SolicitudType->solicitud_type_nombre)
                    ->setCellValueExplicit('B' . $rcount, $request->solicitud_folio, PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('C' . $rcount, $request->User->user_username)
                    ->setCellValue('D' . $rcount, $request->User->user_email)
                    ->setCellValue('E' . $rcount, $fecha)
                    ->setCellValue('F' . $rcount, $request->solicitud_factura_nombre)
                    ->setCellValue('G' . $rcount, $request->solicitud_factura_numero)
                    ->setCellValue('H' . $rcount, $request->solicitud_oc_nombre)
                    ->setCellValue('I' . $rcount, $request->solicitud_oc_numero)
                    ->setCellValue('J' . $rcount, $request->SolicitudEstado->solicitud_estado_nombre)
                    ->setCellValue('K' . $rcount, $request->solicitud_comen_user);
        }

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
        $sheet->getColumnDimension('K')->setAutoSize(true);

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

        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save('./temp/' . $this->input->post('file_name') . '.xls');
        echo '{"success": true, "file": "temp/' . $this->input->post('file_name') . '.xls"}';

//        $this->syslog->register('export_list_request', array(
//            $this->input->post('file_name') . '.xls'
//        )); // registering log
    }
    
    function approve ()
    {
        $request = Doctrine_Core::getTable('Solicitud')->find($this->input->post('solicitud_id'));
        $request->solicitud_estado_id = 2; //Queda como Aprobado
        $request->save();      
        $solicitudLog = new SolicitudLog();
        $user_id = $this->auth->get_user_data('user_id');
        $solicitudLog->user_id = $user_id;
        $solicitudLog->solicitud_id = $request->solicitud_id;
        $solicitudLog->solicitud_log_detalle = 'Aprueba Solicitud';
        $solicitudLog->save();
        $success = true;
        $msg = $this->translateTag('General', 'operation_successful');
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    
    function rejects ()
    {
        $request = Doctrine_Core::getTable('Solicitud')->find($this->input->post('solicitud_id'));
        $request->solicitud_estado_id = 3; //Queda como Aprobado
        $request->solicitud_comen_admin = $this->input->post('solicitud_comen_admin');
        $request->save();   
        $solicitudLog = new SolicitudLog();
        $user_id = $this->auth->get_user_data('user_id');
        $solicitudLog->user_id = $user_id;
        $solicitudLog->solicitud_id = $request->solicitud_id;
        $solicitudLog->solicitud_log_detalle = 'Rechaza Solicitud';
        $solicitudLog->save();
        $success = true;
        $msg = $this->translateTag('General', 'operation_successful');
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }
    
    function sendNotification($solicitud_id) {
 
        
        $q = Doctrine_Query::create()
                ->from('Solicitud s')
                ->innerJoin ( 's.SolicitudEstado se' )
                ->innerJoin ( 's.SolicitudType st' )
                ->innerJoin ( 's.User u' )
                ->where('solicitud_id = ?', $solicitud_id);

        $results = $q->fetchOne();

        $CI = & get_instance();
        $CI->load->library('NotificationUser');

        $to = trim($results['User']['user_email']); //CORREO DESTINATARIO

        $subject = 'Aviso de Creación de solicitud'; //ASUNTO
        
        //Formatear Fecha
        $date = new DateTime($results['solicitud_fecha']);
        $fecha = $date->format('d/m/Y H:i'); 

        $body = 'Tipo de Solicitud :' . $results['SolicitudType']['solicitud_type_nombre'] . "\n"; //CUERPO DEL MENSAJE
        $body .= 'Estado de Solicitud :' . $results['SolicitudEstado']['solicitud_estado_nombre'] . "\n";
        $body .= 'Folio de Solicitud :' . $results['solicitud_folio'] . "\n";
        $body .= 'Nombre de Usuario :' . $results['User']['user_username'] . "\n";
        $body .= 'Fecha de Solicitud :' . $fecha . "\r\n";
        $body .= 'Nombre de Documento de Factura :' . $results['solicitud_factura_nombre'] . "\r\n";
        $body .= 'Numero de Factura :' . $results['solicitud_factura_numero'] . "\r\n";
        $body .= 'Nombre de Documento de Orden de Compra :' . $results['solicitud_oc_nombre'] . "\r\n";
        $body .= 'Numero de Orden de Compra :' . $results['solicitud_oc_numero'] . "\r\n";
        $body .= 'Comentario de Usuario :' . $results['solicitud_comen_user'] . "\r\n";
        
    }
}