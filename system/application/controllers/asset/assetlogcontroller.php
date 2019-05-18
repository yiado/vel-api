<?php

/**
 * @package    Controller
 * @subpackage AssetLogController
 */
class AssetLogController extends APP_Controller {

    function AssetLogController() {

        parent::APP_Controller();
    }

    function get() {
        $assetLog = Doctrine_Core::getTable('AssetLog')->findByAssetId($this->input->post('asset_id'));
        if ($assetLog->count()) {
            echo '({"total":"' . $assetLog->count() . '", "results":' . $this->json->encode($assetLog->toArray()) . '})';
        } else {
            echo '({"total":"0", "results":[]})';
        }
    }

    function export() {


        //--- HEADER EXCEL--
        $this->load->library('PHPExcel');
        $sheetIndex = 0;
        $sheet = $this->phpexcel->setActiveSheetIndex($sheetIndex);
        $sheet->setTitle($this->translateTag('Asset', 'tracking'));
        $sheet->setCellValue('A1', $this->translateTag('General', 'action'))
                ->setCellValue('B1', $this->translateTag('General', 'date_time'))
                ->setCellValue('C1', $this->translateTag('General', 'details'))
                ->setCellValue('D1', $this->translateTag('General', 'user'));
        //-----FIN HEADER--------
        //-----BODY EXCEL--------
        $AssetLogs = Doctrine_Core::getTable('AssetLog')->findByAssetId($this->input->post('asset_id'));


        $rcount = 1;
        foreach ($AssetLogs as $AssetLog) {
            
            $asset_log_type = '';
            if ($AssetLog['asset_log_type'] == 'asset_log_creation') {
                $asset_log_type = 'creacion';
            }
            if ($AssetLog['asset_log_type'] == 'asset_log_move') {

                $asset_log_type = 'mover';
            }

            $rcount++;
            $sheet->setCellValue('A' . $rcount, $asset_log_type)
                    ->setCellValue('B' . $rcount, $AssetLog['asset_log_datetime'])
                    ->setCellValue('C' . $rcount, $AssetLog['asset_log_detail'])
                    ->setCellValue('D' . $rcount, $AssetLog['User']['user_name']);
        }

        //---FOOTER DEL EXCEL--
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);


        $sheet->getStyle('A1:D1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:D' . $rcount)->getBorders()->applyFromArray(array(
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
