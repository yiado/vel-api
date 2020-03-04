<?php

/**
 * @package    Controller
 * @subpackage RdiController
 */
class RdiController extends APP_Controller {

    function RdiController() {
        parent::APP_Controller();
        $this->CI = & get_instance();
        $this->CI->load->library('NotificationUser');
    }

    function get() {        
        $request = Doctrine_Core::getTable('Rdi')->retrieveAll($this->filtrosRdi(),$this->input->post('start'), $this->input->post('limit'));
        if ($request->count()) {
            $countAllRequest = Doctrine_Core::getTable('Rdi')->retrieveAll($this->filtrosRdi(), false, false, true);
            echo '({"total":"' . $countAllRequest . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getById() {
        $request = Doctrine_Core::getTable('Rdi')->findById($this->input->post('rdi_id'));
        $this->sendRes($request);
    }

    function add() {
        $node = Doctrine_Core::getTable('Node')->find((int) $this->input->post('node_id'));

        //Obtenemos la conexión actual
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();

        //Iniciamos la transacción
        $conn->beginTransaction();

        try {
            //Insertamos el nuevo documento en la tabla
            $rdi = new Rdi();
            $user_id = $this->auth->get_user_data('user_id');
            $rdi->node_id = $this->input->post('node_id');
            $rdi->user_id = $user_id;
            $rdi->rdi_status_id = 1;
            $rdi->rdi_description = $this->input->post('rdi_description');
            $rdi->save();

            $rdiLog = new RdiLog();
            $rdiLog->user_id = $user_id;
            $rdiLog->rdi_id = $rdi->rdi_id;
            $rdiLog->rdi_log_detail = 'Creación de Requerimiento de Información';
            $rdiLog->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');

            $conn->commit();

            //Enviar correo de Alerta de creación de Rdi
            //$this->sendNotificationAdministrador($rdi, $node);
            //$this->sendNotificationRecibido($rdi);
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
        $rdi = Doctrine_Core::getTable('Rdi')->find($this->input->post('rdi_id'));
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
        $conn->beginTransaction();
        try {
            
            $rdi_description = $this->input->post('rdi_description');
            if ($rdi->rdi_description !== $rdi_description) {
                $rdiLog = new RdiLog();
                $user_id = $this->auth->get_user_data('user_id');
                $rdiLog->user_id = $user_id;
                $rdiLog->rdi_id = $rdi->rdi_id;
                $rdiLog->rdi_log_detail = 'Cambio de descripción:' . $rdi->rdi_description . ' Por  :' . $rdi_description;
                $rdiLog->save();
                $rdi->rdi_description = $rdi_description;
            }


            $rdi_status_id = $this->input->post('rdi_status_id');
            $cambio_estado = false;
            if ($rdi->rdi_status_id != $rdi_status_id && isset($rdi_status_id)) {
                $rdiStatusNew = Doctrine_Core::getTable('RdiStatus')->find((int) $rdi_status_id);
                $rdiLog = new RdiLog();
                $user_id = $this->auth->get_user_data('user_id');
                $rdiLog->user_id = $user_id;
                $rdiLog->rdi_id = $rdi->rdi_id;
                $rdiLog->rdi_log_detail = "Cambio de estado: {$rdi->RdiStatus->rdi_status_name} Por  : {$rdiStatusNew->rdi_status_name}";
                $rdiLog->save();
                $rdi->rdi_status_id = $rdi_status_id;
                $cambio_estado = true;
            }

            $rdi->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            $conn->commit();
            
            if ($cambio_estado) {
                //$this->sendNotificationUpdate($rdi);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function filtrosRdi() {
        $user_id = $this->session->userdata('user_id');
        $user_type = $this->session->userdata('user_type');

        $node_id = (int) $this->input->post('node_id');
        
        $ancestros = Doctrine_Core::getTable('Node')->findOneByNodeId($node_id)->getNode()->getAncestors();

        $rdi_status_id = $this->input->post('rdi_status_id');
        $user_username = $this->input->post('user_username');
        $user_email = $this->input->post('user_email');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        
        $start_updated_at = $this->input->post('start_updated_at');
        $end_updated_at = $this->input->post('end_updated_at');

        $filters = array(            
            'rs.rdi_status_id = ?' => $rdi_status_id,
            'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
            'u.user_email = ?' => $user_email,
            'rdi_created_at >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'rdi_created_at <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'rdi_updated_at >= ?' => (!empty($start_updated_at) ? $start_updated_at . ' 00:00:00' : NULL ),
            'rdi_updated_at <= ?' => (!empty($end_updated_at) ? $end_updated_at . ' 23:59:59' : NULL ),
        );
        
        if ($this->input->post('date_interval')) {
            $filters['DATE_FORMAT(rdi_created_at,\'%m-%Y\') = ?'] = $this->input->post('date_interval');
        }
            
        if ($ancestros) {
            $filters['node_id = ?'] = $node_id;
        }

        if ($user_type !== 'A') {
            $filters['user_id = ?'] = (!empty($user_id) ? $user_id : NULL );
        }

        return $filters;
    }

    function export() {
        $this->load->library('PHPExcel');

        $requests = Doctrine_Core::getTable('Rdi')->retrieveAll($this->filtrosRdi());

        $titulos[] = 'Estado';
        $titulos[] = 'Nombre Usuario';
        $titulos[] = 'Email Usuario';
        $titulos[] = 'Descripción';
        $titulos[] = 'Fecha Creación';
        $titulos[] = 'Fecha Modificación';

        $rdis = array();
        foreach ($requests as $request) {
            
            $date = new DateTime($request->rdi_created_at);
            $created_date = $date->format('d/m/Y H:i');
            
            $date2 = new DateTime($request->rdi_updated_at);
            $updated_date = $date2->format('d/m/Y H:i');
            
            $rdi = array();
            $rdi[] = $request->RdiStatus->rdi_status_name;
            $rdi[] = $request->User->user_username;
            $rdi[] = $request->User->user_email;
            $rdi[] = $request->rdi_description;
            $rdi[] = PHPExcel_Shared_Date::stringToExcel($created_date);
            $rdi[] = PHPExcel_Shared_Date::stringToExcel($updated_date);
            $rdis[] = $rdi;
        }

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->fromArray($titulos, null, "A1");
        $sheet->fromArray($rdis, null, "A2");

        $dimensionHoja = $sheet->calculateWorksheetDimension();
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn();

        $sheet->setAutoFilter($dimensionHoja);

        /** Formato de tipo de datos en celdas */
        $sheet->getStyle("E2:F{$ultimaFila}")
                ->getNumberFormat()
                ->setFormatCode('dd/mm/yyyy hh:mm');

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

    function sendNotificationRecibido($rdi) {
        $body = "La solicitud [{$rdi->rdi_description}] ha sido recibida.";
        $this->CI->notificationuser->mail($rdi->User->user_email, 'Solicitud de servicio recibida', $body);
    }

    function sendNotificationAdministrador($rdi, $node) {
        $date = new DateTime($rdi->rdi_date);
        $fecha = $date->format('d/m/Y H:i');
        $body = '';
        $nodos_ancestros = array();

        if ($node->getNode()->getLevel()) {
            foreach ($node->getNode()->getAncestors()->toArray() as $nodo) {
                $nodos_ancestros[] = $nodo['node_name'];
            }
            $nodos_ancestros[] = $node->toArray()['node_name'];
        }

        $body .= "Nodo: " . implode(' => ', $nodos_ancestros) . "<br>";
        $body .= "Tipo de Servicio: {$rdi->RdiType->rdi_type_name}<br>";
        $body .= "Estado del Servicio: {$rdi->RdiStatus->rdi_status_name}<br>";
        $body .= "Nombre de Usuario: {$rdi->User->user_username}<br>";
        $body .= "Fecha de Servicio: {$fecha}<br>";
        $body .= "Teléfono: {$rdi->rdi_phone}<br>";
        $body .= "Organismo: {$rdi->rdi_organism}<br>";
        $body .= "Requerimiento: {$rdi->rdi_description}<br>";

        $this->CI->notificationuser->mail($rdi->RdiType->User->user_email, 'Nueva Solicitud de Servicio', $body);
    }

    function sendNotificationUpdate($rdi) {
        $date = new DateTime($rdi->rdi_date);
        $fecha = $date->format('d/m/Y H:i');
        $body = "Tipo de Servicio: {$rdi->RdiType->rdi_type_name}<br>";
        $body .= "Estado del Servicio: {$rdi->RdiStatus->rdi_status_name}<br>";
        $body .= "Nombre de Usuario: {$rdi->User->user_username}<br>";
        $body .= "Fecha de Servicio: {$fecha}<br>";
        $body .= "Teléfono: {$rdi->rdi_phone}<br>";
        $body .= "Organismo: {$rdi->rdi_organism}<br>";
        $body .= "Requerimiento: {$rdi->rdi_description}<br>";

        $CI = & get_instance();
        $CI->load->library('NotificationUser');
        $CI->notificationuser->mail($rdi->RdiType->User->user_email, 'Cambio estado de servicio', $body);
    }
    
    function getRdiStatus() {
        $request = Doctrine_Core::getTable('Rdi')->groupAllByStatus($this->filtrosRdi());
        $this->sendRes($request);
    }
    
    function getRdiDate() {
        $request = Doctrine_Core::getTable('Rdi')->groupAllByDate($this->filtrosRdi());
        $this->sendRes($request);
    }
    
    function sendRes ($request) {
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
