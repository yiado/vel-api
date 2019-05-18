<?php

/**
 * @package Controller
 * @subpackage CostsController
 */
class CostsController extends APP_Controller {

    function CostsController() {
        parent :: APP_Controller();
    }

    /**
     * Lista todos los log del sistema
     * @param string $query (opcional)
     */
    function get() {
        $node_id = $this->input->post('node_id');
        $costs_anio = (int) $this->input->post('costs_anio');
        $costs_number_ticket = $this->input->post('costs_number_ticket');
        $costs_value = (int) $this->input->post('costs_value');
        $costs_detail = $this->input->post('costs_detail');

        $filters = array(
            'c.costs_id = ?' => $this->input->post('costs_id'),
            'c.costs_type_id = ?' => $this->input->post('costs_type_id'),
            'c.costs_month_id = ?' => $this->input->post('costs_month_id'),
            'c.costs_anio LIKE ?' => (!empty($costs_anio) ? $costs_anio . '%' : NULL),
            'c.costs_number_ticket LIKE ?' => (!empty($costs_number_ticket) ? $costs_number_ticket . '%' : NULL),
            'c.costs_value LIKE ?' => (!empty($costs_value) ? $costs_value . '%' : NULL),
            'c.costs_detail LIKE ?' => (!empty($costs_detail) ? $costs_detail . '%' : NULL));

        $costs = Doctrine_Core::getTable('Costs')->retrieveAll($filters, $node_id, $this->input->post('search_branch'));
        $result = count($costs);

        $json_data = $this->json->encode(array('total' => $result, 'results' => $costs));
        echo $json_data;
    }

    /**
     * get
     * 
     * Lista todos los meses existentes
     */
    function getCostsMonth() {


        $january = $this->translateTag('General', 'january');
        $february = $this->translateTag('General', 'february');
        $march = $this->translateTag('General', 'march');
        $april = $this->translateTag('General', 'april');
        $may = $this->translateTag('General', 'may');
        $june = $this->translateTag('General', 'june');
        $july = $this->translateTag('General', 'july');
        $august = $this->translateTag('General', 'august');
        $september = $this->translateTag('General', 'september');
        $october = $this->translateTag('General', 'october');
        $november = $this->translateTag('General', 'november');
        $december = $this->translateTag('General', 'december');


        $meses = "
        {\"costs_month_id\":\"1\",\"costs_month_name\":\" $january \"},
        {\"costs_month_id\":\"2\",\"costs_month_name\":\" $february \"},
        {\"costs_month_id\":\"3\",\"costs_month_name\":\" $march \"},
        {\"costs_month_id\":\"4\",\"costs_month_name\":\" $april \"},
        {\"costs_month_id\":\"5\",\"costs_month_name\":\" $may \"},
        {\"costs_month_id\":\"6\",\"costs_month_name\":\" $june \"},
        {\"costs_month_id\":\"7\",\"costs_month_name\":\" $july \"},
        {\"costs_month_id\":\"8\",\"costs_month_name\":\" $august \"},
        {\"costs_month_id\":\"9\",\"costs_month_name\":\" $september \"},
        {\"costs_month_id\":\"10\",\"costs_month_name\":\" $october \"},
        {\"costs_month_id\":\"11\",\"costs_month_name\":\" $november \"},
        {\"costs_month_id\":\"12\",\"costs_month_name\":\" $december \"}
        ";

        echo '({"total":"' . 12 . '", "results":[' . $meses . ']})';
    }

    /**
     * add
     *
     * Agrega un nuevo costo asociado a un nodo
     *
     * @post int node_id
     * @post int costs_type_id
     * @post int costs_month_id
     * @post int costs_value
     * @post string costs_number_ticket
     * @post string costs_detail
     */
    function add() {
        $costs = new Costs();
        $costs->node_id = $this->input->post('node_id');
        $costs->costs_type_id = $this->input->post('costs_type_id');
        $costs->costs_month_id = $this->input->post('costs_month_id');
        $costs->costs_anio = $this->input->post('costs_anio');
        $costs->costs_value = $this->input->post('costs_value');
        $costs->costs_number_ticket = $this->input->post('costs_number_ticket');
        $costs->costs_detail = $this->input->post('costs_detail');

        try {
            $costs->save();

            //BUSCAR LA RUTA DE ORIGEN DEL ASSET
            $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
            $costsType = Doctrine_Core::getTable('CostsType')->find($this->input->post('costs_type_id'));


            $this->syslog->register('add_costs', array(
                $costsType->costs_type_name,
                $this->input->post('costs_number_ticket'),
                $node->getPath()
            )); // registering log



            $success = true;
            $msg = $this->translateTag('General', 'operation_successful');
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * update
     *
     * Modifica un determinado costo de un nodo
     *
     * @post int costs_id
     * @post int node_id
     * @post int costs_type_id
     * @post int costs_month_id
     * @post int costs_value
     * @post string costs_number_ticket
     * @post string costs_detail
     */
    function update() {
        $costs = Doctrine_Core::getTable('Costs')->find($this->input->post('costs_id'));
        $costs->node_id = $this->input->post('node_id');

        $costoType_antes = $costs->costs_type_id;
        $costs->costs_type_id = $this->input->post('costs_type_id');
        $costoType_despues = $costs->costs_type_id;

        $costsMonth_antes = $costs->costs_month_id;
        $costs->costs_month_id = $this->input->post('costs_month_id');
        $costsMonth_despues = $costs->costs_month_id;

        $costs_anio_antes = $costs->costs_anio;
        $costs->costs_anio = $this->input->post('costs_anio');
        $costs_anio_despues = $costs->costs_anio;

        $costs_value_antes = $costs->costs_value;
        $costs->costs_value = $this->input->post('costs_value');
        $costs_value_despues = $costs->costs_value;

        $costs_number_ticket_antes = $costs->costs_number_ticket;
        $costs->costs_number_ticket = $this->input->post('costs_number_ticket');
        $costs_number_ticket_despues = $costs->costs_number_ticket;

        $costs_detail_antes = $costs->costs_detail;
        $costs->costs_detail = $this->input->post('costs_detail');
        $costs_detail_despues = $costs->costs_detail;

        $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        $costsTypeAntes = Doctrine_Core::getTable('CostsType')->find($costoType_antes);
        $costsTypeDespues = Doctrine_Core::getTable('CostsType')->find($costoType_despues);

        $costsMonthAntes = Doctrine_Core::getTable('CostsMonth')->find($costsMonth_antes);
        $costsMonthDespues = Doctrine_Core::getTable('CostsMonth')->find($costsMonth_despues);


        try {

            $costs->save();

            $log_id = $this->syslog->register('update_costs', array(
                $costsTypeAntes->costs_type_name,
                $node->getPath()
                    )); // registering log

            if ($costsTypeAntes->costs_type_name != $costsTypeDespues->costs_type_name) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Costs', 'concept');
                    $logDetail->log_detail_value_old = $costsTypeAntes->costs_type_name;
                    $logDetail->log_detail_value_new = $costsTypeDespues->costs_type_name;
                    $logDetail->save();
                }
            }

            if (trim($costsMonthAntes->mes_traducido) != trim($costsMonthDespues->mes_traducido)) {
                if ($log_id) {

                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'month');
                    $logDetail->log_detail_value_old = $costsMonthAntes->mes_traducido;
                    $logDetail->log_detail_value_new = $costsMonthDespues->mes_traducido;
                    $logDetail->save();
                }
            }

            if ($costs_anio_antes != $costs_anio_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'year');
                    $logDetail->log_detail_value_old = $costs_anio_antes;
                    $logDetail->log_detail_value_new = $costs_anio_despues;
                    $logDetail->save();
                }
            }

            if ($costs_value_antes != $costs_value_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'value');
                    $logDetail->log_detail_value_old = $costs_value_antes;
                    $logDetail->log_detail_value_new = $costs_value_despues;
                    $logDetail->save();
                }
            }

            if ($costs_number_ticket_antes != $costs_number_ticket_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('Costs', 'ballot_number_or_invoice');
                    $logDetail->log_detail_value_old = $costs_number_ticket_antes;
                    $logDetail->log_detail_value_new = $costs_number_ticket_despues;
                    $logDetail->save();
                }
            }

            if ($costs_detail_antes != $costs_detail_despues) {
                if ($log_id) {
                    $logDetail = new LogDetail();
                    $logDetail->log_id = $log_id;
                    $logDetail->log_detail_param = $this->translateTag('General', 'comment');
                    $logDetail->log_detail_value_old = $costs_detail_antes;
                    $logDetail->log_detail_value_new = $costs_detail_despues;
                    $logDetail->save();
                }
            }

            $msg = $this->translateTag('General', 'operation_successful');
            $success = true;
        } catch (Exception $e) {
            $success = false;
            $msg = $e->getMessage();
        }
        $json_data = $this->json->encode(array('success' => $success, 'msg' => $msg));
        echo $json_data;
    }

    /**
     * delete
     *
     * Elimina un Costo del equipo
     *
     * @post int costs_id
     */
    function delete() {
        $costs = Doctrine::getTable('Costs')->find($this->input->post('costs_id'));
        $costsType = Doctrine_Core::getTable('CostsType')->find($costs->costs_type_id);
        $node = Doctrine::getTable('Node')->find($costs->node_id);

        $this->syslog->register('delete_costs', array(
            $costsType->costs_type_name,
            $costs->costs_number_ticket,
            $costs->costs_value,
            $node->getPath()
        )); // registering log


        try {
            $costs->delete();
            $msg = $this->translateTag('General', 'operation_successful');
            $success = true;
        } catch (Execption $e) {
            $msg = $e->getMessage();
            $success = false;
        }

        $json_data = $this->json->encode(array('sucess' => $success, 'msg' => $msg));
        echo $json_data;
    }

    function export() {
        //--- HEADER EXCEL--
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Costs', 'costs'));
        $sheet->setCellValue('A1', $this->translateTag('Costs', 'concept'))
                ->setCellValue('B1', $this->translateTag('General', 'month'))
                ->setCellValue('C1', $this->translateTag('General', 'year'))
                ->setCellValue('D1', $this->translateTag('Costs', 'ballot_number_or_invoice'))
                ->setCellValue('E1', $this->translateTag('General', 'value'))
                ->setCellValue('F1', $this->translateTag('General', 'details'));
        //-----FIN HEADER--------
        //-----BODY EXCEL--------

        $node_id = $this->input->post('node_id');
        $costs_anio = (int) $this->input->post('costs_anio');
        $costs_number_ticket = $this->input->post('costs_number_ticket');
        $costs_value = (int) $this->input->post('costs_value');
        $costs_detail = $this->input->post('costs_detail');

        $filters = array(
            'c.costs_type_id = ?' => $this->input->post('costs_type_id'),
            'c.costs_month_id = ?' => $this->input->post('costs_month_id'),
            'c.costs_anio LIKE ?' => (!empty($costs_anio) ? $costs_anio . '%' : NULL),
            'c.costs_number_ticket LIKE ?' => (!empty($costs_number_ticket) ? $costs_number_ticket . '%' : NULL),
            'c.costs_value LIKE ?' => (!empty($costs_value) ? $costs_value . '%' : NULL),
            'c.costs_detail LIKE ?' => (!empty($costs_detail) ? $costs_detail . '%' : NULL));

        $search_branch = $this->input->post('search_branch');
        if ($search_branch == 'false') {
            $costs = Doctrine_Core::getTable('Costs')->retrieveAll($filters, $node_id, $search_branch = false);
        } else {
            $costs = Doctrine_Core::getTable('Costs')->retrieveAll($filters, $node_id, $this->input->post('search_branch'));
        }

        $rcount = 1;
        foreach ($costs as $cost) {

            $rcount++;
            $sheet->setCellValue('A' . $rcount, $cost['CostsType']['costs_type_name'])
                    ->setCellValue('B' . $rcount, $cost['CostsMonth']['costs_month_name'])
                    ->setCellValue('C' . $rcount, $cost['costs_anio'])
                    ->setCellValue('D' . $rcount, $cost['costs_number_ticket'])
                    ->setCellValue('E' . $rcount, $cost['costs_value'])
                    ->setCellValue('F' . $rcount, $cost['costs_detail']);
        }

        //---FOOTER DEL EXCEL--
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);

        $sheet->getStyle('A1:F1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:F1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
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

        $this->phpexcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');

        $objWriter->save('./temp/' . $this->input->post('file_name') . '.xls');
        echo '{"success": true, "file": "temp/' . $this->input->post('file_name') . '.xls"}';
    }

}