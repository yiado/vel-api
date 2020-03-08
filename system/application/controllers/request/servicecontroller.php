<?php

/**
 * @package    Controller
 * @subpackage ServiceController
 */
class ServiceController extends APP_Controller {

    function ServiceController() {
        parent::APP_Controller();
        $this->CI = & get_instance();
        $this->CI->load->library('NotificationUser');
    }

    function get() {
        $request = Doctrine_Core::getTable('Service')->retrieveAll($this->filtrosServices(), $this->input->post('start'), $this->input->post('limit'));
        if ($request->count()) {
            $countAllRequest = Doctrine_Core::getTable('Service')->retrieveAll($this->filtrosServices(), false, false, true);
            echo '({"total":"' . $countAllRequest . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function getById() {
        $request = Doctrine_Core::getTable('Service')->findById($this->input->post('service_id'));
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
            $service = new Service();
            $user_id = $this->auth->get_user_data('user_id');
            $service->node_id = $this->input->post('node_id');
            $service->user_id = $user_id;
            $service->service_type_id = $this->input->post('service_type_id');
            $service->service_status_id = 1;
            $service->request_evaluation_id = 1;
            $service->service_organism = $this->input->post('service_organism');
            $service->service_phone = $this->input->post('service_phone');
            $service->service_commentary = $this->input->post('service_commentary');
            $service->save();

            $serviceLog = new ServiceLog();
            $serviceLog->user_id = $user_id;
            $serviceLog->service_id = $service->service_id;
            $serviceLog->service_log_detail = 'Creación de Solicitud de Servicio';
            $serviceLog->save();

            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');

            $conn->commit();

            //Enviar correo de Alerta de creación de Service
            //$service->sendNotificationAdministrador($node);
            $service->sendNotificationRecibido($node);
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
        $user_id = $this->auth->get_user_data('user_id');

        try {
            $service_organism = $this->input->post('service_organism');
            if ($service->service_organism != $service_organism) {
                $serviceLog = new ServiceLog();
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Cambio de organismo:' . $service->service_organism . ' Por  :' . $service_organism;
                $serviceLog->save();

                $service->service_organism = $service_organism;
            }

            $service_phone = $this->input->post('service_phone');
            if ($service->service_phone != $service_phone) {
                $serviceLog = new ServiceLog();
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Cambio de teléfono:' . $service->service_phone . ' Por  :' . $service_phone;
                $serviceLog->save();
                $service->service_phone = $service_phone;
            }

            $service_commentary = $this->input->post('service_commentary');
            if ($service->service_commentary != $service_commentary) {
                $serviceLog = new ServiceLog();
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Cambio de comentario:' . $service->service_commentary . ' Por  :' . $service_commentary;
                $serviceLog->save();
                $service->service_commentary = $service_commentary;
            }

            $service_status_id = $this->input->post('service_status_id');
            $cambio_estado = false;
            if ($service->service_status_id != $service_status_id && isset($service_status_id)) {
                $serviceStatusNew = Doctrine_Core::getTable('ServiceStatus')->find((int) $service_status_id);
                $serviceLog = new ServiceLog();
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = "Cambio de estado: {$service->ServiceStatus->service_status_name} Por  : {$serviceStatusNew->service_status_name}";
                $serviceLog->save();
                $service->service_status_id = $service_status_id;
                $cambio_estado = true;
            }

            $service_reject = $this->input->post('service_reject');
            if ($service_status_id === '6') {
                $serviceLog = new ServiceLog();
                $serviceLog->user_id = $user_id;
                $serviceLog->service_id = $service->service_id;
                $serviceLog->service_log_detail = 'Rechazado por: ' . $service_reject;
                $serviceLog->save();
                $service->service_reject = $service_reject;
            } else {
                $service->service_reject = null;
            }


            $service->save();
            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
            $conn->commit();

            if ($cambio_estado) {
                /**
                 * Estado finalizado
                 */
                if ($service_status_id === '4') {
                    $service->sendEvaluation($serviceStatusNew);
                } else {
                    /**
                     * Estados diferentes de finalizados
                     */
                    $service->sendNotificationUpdate($serviceStatusNew);
                }
            }
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

        $ancestros = Doctrine_Core::getTable('Node')->findOneByNodeId($node_id)->getNode()->getAncestors();

        $request_evaluation_id = $this->input->post('request_evaluation_id');
        $service_type_id = $this->input->post('service_type_id');
        $service_status_id = $this->input->post('service_status_id');
        $user_username = $this->input->post('user_username');
        $user_email = $this->input->post('user_email');

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $service_phone = $this->input->post('service_phone');
        $service_organism = $this->input->post('service_organism');

        $filters = array(
            're.request_evaluation_id = ?' => $request_evaluation_id,
            'st.service_type_id = ?' => $service_type_id,
            'se.service_status_id = ?' => $service_status_id,
            'u.user_username LIKE ?' => (!empty($user_username) ? '%' . $user_username . '%' : NULL),
            'u.user_email = ?' => $user_email,
            'se.service_phone = ?' => $service_phone,
            'service_date >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL ),
            'service_date <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL ),
            'service_organism LIKE ?' => (!empty($service_organism) ? '%' . $service_organism . '%' : NULL)
        );

        if ($this->input->post('date_interval')) {
            $filters['DATE_FORMAT(service_date,\'%m-%Y\') = ?'] = $this->input->post('date_interval');
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

        $requests = Doctrine_Core::getTable('Service')->retrieveAll($this->filtrosServices());

        $titulos[] = 'Nodo';
        $titulos[] = 'Tipo de Servicio';
        $titulos[] = 'Estado';
        $titulos[] = 'Nombre Usuario';
        $titulos[] = 'Email Usuario';
        $titulos[] = 'Teléfono';
        $titulos[] = 'Fecha Servicio';
        $titulos[] = 'Organismo';
        $titulos[] = 'Comentario';
        $titulos[] = 'Motivo Rechazo';
        $titulos[] = 'Evaluación';

        $services = array();
        foreach ($requests as $request) {
            $date = new DateTime($request->service_date);
            $fecha = $date->format('d/m/Y H:i');
            $requestArray = $request->toArray();

            $service = array();
            $service[] = $requestArray['Node']['node_name'];
            $service[] = $request->ServiceType->service_type_name;
            $service[] = $request->ServiceStatus->service_status_name;
            $service[] = $request->User->user_username;
            $service[] = $request->User->user_email;
            $service[] = $request->service_phone;
            $service[] = PHPExcel_Shared_Date::stringToExcel($fecha);
            $service[] = $request->service_organism;
            $service[] = $request->service_commentary;
            $service[] = $request->service_reject;
            $service[] = $request->RequestEvaluation->request_evaluation_name;
            $services[] = $service;
        }

        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle($this->translateTag('Request', 'requests'));
        $sheet->fromArray($titulos, null, "A1");
        $sheet->fromArray($services, null, "A2");

        $dimensionHoja = $sheet->calculateWorksheetDimension();
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn();

        $sheet->setAutoFilter($dimensionHoja);

        /** Formato de tipo de datos en celdas */
        $sheet->getStyle("F2:F{$ultimaFila}")
                ->getNumberFormat()
                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
        $sheet->getStyle("G2:G{$ultimaFila}")
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

    function getServiceStatus() {
        $request = Doctrine_Core::getTable('Service')->groupAllByStatus($this->filtrosServices());
        $this->sendRes($request);
    }

    function getServiceType() {
        $request = Doctrine_Core::getTable('Service')->groupAllByType($this->filtrosServices());
        $this->sendRes($request);
    }

    function getServiceDate() {
        $request = Doctrine_Core::getTable('Service')->groupAllByDate($this->filtrosServices());
        $this->sendRes($request);
    }

    function getServiceOrganism() {
        $request = Doctrine_Core::getTable('Service')->groupAllByOrganism($this->filtrosServices());
        $this->sendRes($request);
    }

    function sendRes($request) {
        if ($request->count()) {
            echo '({"total":"' . $request->count() . '", "results":' . $this->json->encode($request->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

}
