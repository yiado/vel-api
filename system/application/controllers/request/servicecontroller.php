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
        $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
        $conn->beginTransaction();
        try {

            $service_organism = $this->input->post('service_organism');
            if ($service->service_organism != $service_organism) {
                $serviceLog = new ServiceLog();
                $user_id = $this->auth->get_user_data('user_id');
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Cambio de organismo:' . $service->service_organism . ' Por  :' . $service_organism;
                $serviceLog->save();

                $service->service_organism = $service_organism;
            }
            
            $service_phone = $this->input->post('service_phone');
            if ($service->service_phone != $service_phone) {
                $serviceLog = new ServiceLog();
                $user_id = $this->auth->get_user_data('user_id');
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Cambio de teléfono:' . $service->service_phone . ' Por  :' . $service_phone;
                $serviceLog->save();
                $service->service_phone = $service_phone;
            }
            
            $service_commentary = $this->input->post('service_commentary');
            if ($service->service_commentary != $service_commentary) {
                $serviceLog = new ServiceLog();
                $user_id = $this->auth->get_user_data('user_id');
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Cambio de comentario:' . $service->service_commentary . ' Por  :' . $service_commentary;
                $serviceLog->save();
                $service->service_commentary = $service_commentary;
            }
            
            $service->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
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
        $titulos[] = 'Teléfono';
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
        $sheet->getStyle("E2:E{$ultimaFila}")
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
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

        $to = trim($results['User']['user_email']);

        $subject = 'Aviso de Creación de service';
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
