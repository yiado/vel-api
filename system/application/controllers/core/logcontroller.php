<?php

/**
 * @package Controller
 * @subpackage logController
 */
class LogController extends APP_Controller {

    function LogController() {
        parent :: APP_Controller();
    }

    /**
     * Lista todos los log del sistema
     * @param string $query (opcional)
     */
    function get() {
        $user_name = $this->input->post('user_name');
        $log_ip = $this->input->post('log_ip');
        $log_type_description = $this->input->post('log_type_description');
        $log_description = $this->input->post('log_description');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $filters = array(
            'u.user_name LIKE ?' => (!empty($user_name) ? $user_name . '%' : NULL),
            'log_ip LIKE ?' => (!empty($log_ip) ? $log_ip . '%' : NULL),
            'lt.log_type_description LIKE ?' => (!empty($log_type_description) ? $log_type_description . '%' : NULL),
            'log_description LIKE ?' => (!empty($log_description) ? $log_description . '%' : NULL),
            'log_date_time >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL),
            'log_date_time <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL));


        $log = Doctrine_Core :: getTable('Log')->retrieveAll($filters);

        echo '({"total":"' . $log->count() . '", "results":' . $this->json->encode($log->toArray()) . '})';
    }

    /**
     *  Lista todos los detalles de los Log del sistema
     * @param integer $log_id
     */
    function getByIdLog() {
        $log_id = $this->input->post('log_id');
        $listaDetailLogTable = Doctrine_Core::getTable('LogDetail');
        $listaDetailLog = $listaDetailLogTable->checkLogId($log_id);

        if ($listaDetailLog->count()) {
            echo '({"total":"' . $listaDetailLog->count() . '", "results":' . $this->json->encode($listaDetailLog->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    /**
     * 
     * Lista todos los log_type_description
     * @param string $query (text para la busqueda con autocompletar)
     * 
     */
    function getLogType() {
        $text_autocomplete = $this->input->post('query');
        $logTypeTable = Doctrine_Core::getTable('LogType');
        $logType = $logTypeTable->retrieveAll($text_autocomplete);

        //Output 
        $json_data = $this->json->encode(array('total' => $logType->count(), 'results' => $logType->toArray()));
        echo $json_data;
    }

    function export() {
        //--- HEADER EXCEL--
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        
        $sheet->setTitle($this->translateTag('General', 'transaction_management'));
        $sheet->setCellValue('A1', $this->translateTag('General', 'user'))
                ->setCellValue('B1', $this->translateTag('General', 'type_of_action'))
                ->setCellValue('C1', $this->translateTag('General', 'description'))
                ->setCellValue('D1', $this->translateTag('General', 'creation_date'))
                ->setCellValue('E1', $this->translateTag('General', 'ip'))
                ->setCellValue('F1', $this->translateTag('General', 'field_name'))
                ->setCellValue('G1', $this->translateTag('General', 'before'))
                ->setCellValue('H1', $this->translateTag('General', 'after'));

        //-----FIN HEADER--------
        //-----BODY EXCEL----
        $user_name = $this->input->post('user_name');
        $log_ip = $this->input->post('log_ip');
        $log_type_description = $this->input->post('log_type_description');
        $log_description = $this->input->post('log_description');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        list($start_date) = explode("T", $start_date);
        list($end_date) = explode("T", $end_date);
        $filters = array(
            'u.user_name LIKE ?' => (!empty($user_name) ? $user_name . '%' : NULL),
            'log_ip LIKE ?' => (!empty($log_ip) ? $log_ip . '%' : NULL),
            'lt.log_type_description LIKE ?' => (!empty($log_type_description) ? $log_type_description . '%' : NULL),
            'log_description LIKE ?' => (!empty($log_description) ? $log_description . '%' : NULL),
            'log_date_time >= ?' => (!empty($start_date) ? $start_date . ' 00:00:00' : NULL),
            'log_date_time <= ?' => (!empty($end_date) ? $end_date . ' 23:59:59' : NULL));

        $logs = Doctrine_Core :: getTable('Log')->retrieveAllExport($filters);
        $rcount = 1;
        foreach ($logs as $log) {

            $rcount++;
            $sheet->setCellValue('A' . $rcount, $log['u_user_name'])
                    ->setCellValue('B' . $rcount, $log['lt_log_type_description'])
                    ->setCellValue('C' . $rcount, $log['l_log_description'])
                    ->setCellValue('D' . $rcount, $log['l_log_date_time'])
                    ->setCellValue('E' . $rcount, $log['l_log_ip'])
                    ->setCellValue('F' . $rcount, $log['ld_log_detail_param'])
                    ->setCellValue('G' . $rcount, $log['ld_log_detail_value_old'])
                    ->setCellValue('H' . $rcount, $log['ld_log_detail_value_new']);
                 
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

        $sheet->getStyle('A1:H1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:H1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:H' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));


        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));

        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';
    }

}