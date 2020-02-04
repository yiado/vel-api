<?php

/**
 * @package    Controller
 * @subpackage infraestructureController
 */
class infrastructureController extends APP_Controller
{

    function infrastructureController()
    {
        parent::APP_Controller();
    }

    /** exportList
     * 
     * Rediccciona al metodo dependiendo del tipo de salida (pdf, excel)
     * 
     */
    function exportList()
    {
        $node_id = $this->input->post('node_id');



        switch ($this->input->post('output_type'))
        {
            case 'e':
                $this->exportListExcel($node_id);
                break;

            case 'p':
                $this->exportListPDF($node_id);
                break;
        }
    }

    /**
     * exportListExcel
     * 
     * Exporta el listado actual en formato excel
     * 
     */
    function exportListExcel($node_id = null)
    {
        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');



        if ($this->input->post('node_id') && is_numeric($this->input->post('node_id')))
        { // si el un nodo con id
            $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'))->getNode();
            $nodesCantity = $node->getNumberChildren();
            $nodes = $node->getChildren();
        } else
        { // nodos raices...no tiene id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $nodes = $treeObject->fetchRoots();
            $nodesCantity = count($nodes);
        }

        $sheet->setCellValue('A1', $this->translateTag('General', 'name'))
                ->setCellValue('B1', $this->translateTag('General', 'type'))
                ->setCellValue('C1', $this->translateTag('General', 'category'));


        $rcount = 1;

        if ($nodesCantity)
        {
            foreach ($nodes as $node)
            {
                $rcount++;
                $sheet->setCellValueExplicit('A' . $rcount, $node->node_name)
                        ->setCellValueExplicit('B' . $rcount, $node->NodeType->node_type_name)
                        ->setCellValueExplicit('C' . $rcount, $node->NodeType->NodeTypeCategory->node_type_category_name);
            }
        }
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $sheet->getStyle('A1:C1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:C1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:C' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel5');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';

        $node_all = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        if (is_numeric($this->input->post('node_id'))) {
            $this->syslog->register('export_list_nodes', array(
                $this->input->post('file_name'),
                $node_all->getPath()
            )); // registering log
        }
    }

    /**
     * exportListPDF
     * 
     * Exporta el listado actual en formato pdf
     * 
     */
    function exportListPDF($node_id = null)
    {
        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');

        if ($this->input->post('node_id') && is_numeric($this->input->post('node_id')))
        { // si el un nodo con id
            $node = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'))->getNode();
            $nodesCantity = $node->getNumberChildren();
            $nodes = $node->getChildren();
        } else
        { // nodos raices...no tiene id
            $treeObject = Doctrine_Core::getTable('Node')->getTree();
            $nodes = $treeObject->fetchRoots();
            $nodesCantity = count($nodes);
        }

        $sheet->setCellValue('A1', $this->translateTag('General', 'name'))
                ->setCellValue('B1', $this->translateTag('General', 'type'))
                ->setCellValue('C1', $this->translateTag('General', 'category'));

        $rcount = 1;

        if ($nodesCantity)
        {
            foreach ($nodes as $node)
            {
                $rcount++;
                $sheet->setCellValue('A' . $rcount, $node->node_name)
                        ->setCellValue('B' . $rcount, $node->NodeType->node_type_name)
                        ->setCellValue('C' . $rcount, $node->NodeType->NodeTypeCategory->node_type_category_name);
            }
        }

        $sheet->getStyle('A1:C1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:C1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array(
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:C1' . $rcount)->getFont()->applyFromArray(array(
            'name' => 'Arial',
            'size' => 8
        ));

        $sheet->getStyle('A1:C' . $rcount)->getBorders()->applyFromArray(array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array(
                    'rgb' => '808080'
                )
            )
        ));

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);

        $this->phpexcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'PDF');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.pdf'));

        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.pdf"}';

        $node_all = Doctrine_Core::getTable('Node')->find($this->input->post('node_id'));
        $this->syslog->register('export_list_nodes', array(
            $this->input->post('file_name'),
            $node_all->getPath()
        )); // registering log
    }

}
