<?php

class document extends APP_Controller {
    
    function document () {
        parent :: APP_Controller ();
    }

    function standard () {
    	
    	$node_id = $this->input->post('node_id');
    	
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query::create()
                ->from('DocVersion dvc')
                ->innerJoin('dvc.DocDocument dc ON dc.doc_current_version_id = dvc.doc_version_id')
                ->innerJoin('dc.Node n')
                ->innerJoin('dvc.User us')
                ->innerJoin('dc.DocExtension de')
                ->innerJoin('dc.DocCategory dca')
                ->where('n.node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft >= ?', $node->lft)
                ->andWhere('n.rgt <= ?', $node->rgt);

        $results = $q->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        
        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);
        $sheet->setTitle('Results');
        
        $sheet->setCellValueExplicit('A1', $this->translateTag('General' ,'file_name'))
        	  ->setCellValueExplicit('B1', $this->translateTag('General' ,'version'))
        	  ->setCellValueExplicit('C1', $this->translateTag('Document' ,'document_type'))
        	  ->setCellValueExplicit('D1', $this->translateTag('General' ,'category'))
        	  ->setCellValueExplicit('E1', $this->translateTag('General' ,'creation_date'))
        	  ->setCellValueExplicit('F1', $this->translateTag('General' ,'expiration_date'))
        	  ->setCellValueExplicit('G1', $this->translateTag('Core' ,'location'));
        
		$rcount = 1;
        foreach ($results as $document) {
        	
        	$documentNode = Doctrine_Core::getTable('Node')->find($document['DocDocument']['Node']['node_id']);
        	
            $rcount ++;
            $sheet->setCellValueExplicit('A' . $rcount, $document['DocDocument']['doc_document_filename'])
            	  ->setCellValueExplicit('B' . $rcount, $document['doc_version_code_client'])
            	  ->setCellValueExplicit('C' . $rcount, $document['DocDocument']['DocExtension']['doc_extension_name'])
            	  ->setCellValueExplicit('D' . $rcount, $document['DocDocument']['DocCategory']['doc_category_name'])
            	  ->setCellValueExplicit('E' . $rcount, $document['DocDocument']['doc_document_creation'])
            	  ->setCellValueExplicit('F' . $rcount, $document['doc_version_expiration'])
            	  ->setCellValueExplicit('G' . $rcount, $documentNode->getPath());
        	
        }
        
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        
        $sheet->getStyle('A1:G1')->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:G1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:G' . $rcount )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel , 'Excel5');
        $objWriter->save($this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post('file_name') . '.xls"}';
    	
    }
    
}