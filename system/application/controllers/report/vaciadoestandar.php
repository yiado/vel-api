<?php

/**
 * @package Controller
 * @subpackage ReportController
 */
class vaciadoestandar extends APP_Controller
{
    function vaciadoestandar ()
    {
        parent :: APP_Controller ();
    }

    /**
     * exportListExcelBranch
     * 
     * Exporta el nodo y todas sus ramas formato excel
     * 
     */
    function exportListExcelBranch ()
    {
        ini_set('memory_limit', '512M');
        $node_id = $this->input->post ( 'node_id' );
        $colorConfig = array (
            28 => 'CD853F' ,
            1 => 'E0B0FF' ,
            23 => 'FFFF00' ,
            2 => '32CD32' ,
            3 => 'DC143C'
        );

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt );

        $resuls = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_ARRAY );
        $node_type_id = $node->node_type_id;
        $cont = 0;
        $array_data = array ( );
        $array_indexes_ii = array ( );
        $array_indexes_io = array ( );

        foreach ( $resuls as $node )
        {
            $array_node = array ( );
            $array_node[ 'node_name' ] = $node[ 'node_name' ];
            $array_node[ 'node_type_name' ] = $node[ 'NodeType' ][ 'node_type_name' ];

            // traer datos infra
            $info = Doctrine_Core::getTable ( 'InfraInfo' )->findByNodeId ( $node[ 'node_id' ] );
            $infraConfig = Doctrine_Core::getTable ( 'InfraConfiguration' )->findByNodeTypeId ( $node[ 'node_type_id' ] );
            $cont = 0;

            foreach ( $infraConfig as $config )
            {
                if ( ! isset ( $array_indexes_ii[ $config->infra_attribute ] ) )
                    $array_indexes_ii[ $config->infra_attribute ] = count ( $array_indexes_ii );
                $array_node[ $config->infra_attribute ] = ($info) ? $info->{$config->infra_attribute} : NULL;
            }
            
            // traer datos otros
            $attributes = Doctrine_Core::getTable ( 'InfraOtherDataAttributeNodeType' )->retrieveByNodeType ( $node[ 'node_type_id' ] );
            $cont = 0;
            
            foreach ( $attributes as $att )
            {
                if ( ! isset ( $array_indexes_io[ $att->infra_other_data_attribute_id ] ) )
                    $array_indexes_io[ $att->infra_other_data_attribute_id ] = count ( $array_indexes_io );

                $value = Doctrine_Core::getTable ( 'InfraOtherDataValue' )->retrieveByAttributeNode ( $node[ 'node_id' ] , $att->infra_other_data_attribute_id );

                if ( $value && $att->InfraOtherDataAttribute->infra_other_data_attribute_type == 5 && $value->infra_other_data_option_id != null )
                {
                    $___value = @Doctrine_Core::getTable ( 'InfraOtherDataOption' )->find ( @$value->infra_other_data_option_id )->infra_other_data_option_name;
                }
                else if ( $value && $att->InfraOtherDataAttribute->infra_other_data_attribute_type != 5 && $value != '' )
                {
                    $___value = $value->infra_other_data_value_value;
                }
                else if ( ! $value )
                {
                    $___value = '';
                }
                $array_node[ $att->infra_other_data_attribute_id ] = $___value;
            }
            $array_data[ ] = $array_node;
        }

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'name') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'node_type') );

        $ccont = 2;
        
        foreach ( $array_indexes_ii as $ii_k => $ii_i )
        {
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
            $sheet->setCellValue ( $columnLetter . '1' , $this->translateTag ( 'Infrastructure' , $ii_k ) );
        }

        foreach ( $array_indexes_io as $io_k => $io_i )
        {

            $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
            $sheet->setCellValue ( $columnLetter . '1' , Doctrine_Core::getTable ( 'InfraOtherDataAttribute' )->find ( $io_k )->infra_other_data_attribute_name );
        }

        $rcont = 2;
        $ccont = 0;

        // data
        foreach ( $array_data as $node )
        {
            $sheet->setCellValue ( 'A' . $rcont , $node[ 'node_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $node[ 'node_type_name' ] );

            $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
            $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );

            $ccont = 2;

            foreach ( $array_indexes_ii as $ii_k => $ii_i )
            {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
                $sheet->setCellValue ( $columnLetter . $rcont , @$node[ $ii_k ] );
                $sheet->getColumnDimension ( $columnLetter )->setAutoSize ( true );
            }

            foreach ( $array_indexes_io as $io_k => $io_i )
            {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
                $sheet->setCellValue ( $columnLetter . $rcont , @$node[ $io_k ] );
                $sheet->getColumnDimension ( $columnLetter )->setAutoSize ( true );
            }
            $rcont ++;
        }

        $sheet->getStyle ( 'A1:' . $columnLetter . '1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:' . $columnLetter . '1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:' . $columnLetter . '' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
    
    function exportPlanListExcelBranch ()
    {
        ini_set('memory_limit', '512M');
        $node_id = $this->input->post ( 'node_id' );

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 5 );

        $resuls = $q->execute ( array () , Doctrine_Core::HYDRATE_ARRAY );

		$rcont = 1;
		
		
		$sheet->setCellValue('A1' , $this->translateTag('General', 'branch') );
		$sheet->setCellValue('B1' , $this->translateTag('General', 'floor') );
		$sheet->setCellValue('C1' , $this->translateTag('General', 'category') );
		$sheet->setCellValue('D1' , $this->translateTag('General', 'date_time_charge') );
		

        foreach ( $resuls as $node )
        {
            
            $node_id = $node['node_id'];
            
            $plans = Doctrine_Core::getTable ( 'Plan' )->retrieveCurrents ( $node_id );
            
            if ($plans->count()) {
            	
            	foreach ($plans as $plan) {
            		
                    $rcont++;

                    $nodeTree = Doctrine_Core::getTable ( 'Node' )->find ( $node[ 'node_id' ] )->getNode();

                    foreach ($nodeTree->getAncestors() as $ancestor) {

                        if ($ancestor->node_type_id == 4) {
                                $sheet->setCellValue('A' . $rcont , $ancestor->node_name);
                        }

                    }

                    $sheet->setCellValue('B' . $rcont , $node['node_name']);
                    $sheet->setCellValue('C' . $rcont , $plan->PlanCategory->plan_category_name);
                    $sheet->setCellValue('D' . $rcont , $plan->plan_datetime);
            		
            	}
            	
            }
            
        }
        
        $sheet->getColumnDimension ('A')->setAutoSize ( true );
        $sheet->getColumnDimension ('B')->setAutoSize ( true );
        $sheet->getColumnDimension ('C')->setAutoSize ( true );
        $sheet->getColumnDimension ('D')->setAutoSize ( true );
        
            
        $sheet->getStyle ( 'A1:D1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:D1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:D' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
    
    function exportDocumentListExcelBranch () {
        ini_set('memory_limit', '512M');
        $node_id = $this->input->post ( 'node_id' );

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 4 );

        $resuls = $q->execute ( array () , Doctrine_Core::HYDRATE_ARRAY );

		$rcont = 1;
		$sheet->setCellValue('A1' , $this->translateTag('General', 'branch') );
		$sheet->setCellValue('B1' , $this->translateTag('General', 'description') );
		$sheet->setCellValue('C1' , $this->translateTag('General', 'category') );
		$sheet->setCellValue('D1' , $this->translateTag('General', 'date_time_charge') );
		

        foreach ( $resuls as $node )
        {
            
            $node_id = $node['node_id'];
            
            $qdoc = Doctrine_Query::create ()
                    ->from ( 'Node n' )
                    ->innerJoin ( 'n.DocDocument dc' )
                    ->leftJoin ( 'dc.DocCurrentVersion dvc' )
                    ->innerJoin ( 'dc.DocCategory dca' )
                    ->where ( '.node_parent_id = ?' , $node['node_parent_id'] )
                    ->andWhere ( 'n.lft >= ?' , $node['lft'] )
                    ->andWhere ( 'n.rgt <= ?' , $node['rgt'] );

                $docs = $qdoc->execute( array () , Doctrine_Core::HYDRATE_ARRAY );
			
            if (count($docs)) {
                
            	foreach ($docs as $docNode) {
            	
                    foreach ($docNode['DocDocument'] as $doc) {
                        $rcont++;

                        $sheet->setCellValue('A' . $rcont , $node['node_name']);
                        $sheet->setCellValue('B' . $rcont , $doc['doc_document_description']);
                        $sheet->setCellValue('C' . $rcont , $doc['DocCategory']['doc_category_name']);
                        $sheet->setCellValue('D' . $rcont , $doc['DocCurrentVersion']['doc_version_creation']);

                    }
            		
            	}
            	
            }
            
        }
        
        $sheet->getColumnDimension ('A')->setAutoSize ( true );
        $sheet->getColumnDimension ('B')->setAutoSize ( true );
        $sheet->getColumnDimension ('C')->setAutoSize ( true );
        $sheet->getColumnDimension ('D')->setAutoSize ( true );
        
            
        $sheet->getStyle ( 'A1:D1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:D1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:D' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    	
    }
    
}