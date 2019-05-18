<?php

class puc extends APP_Controller
{
    function puc ()
    {
        parent :: APP_Controller ();
    }

    function areaPorEdificio () {
    	
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q1 = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->andWhere ( 'node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 71 );

        $results1 = $q1->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('Infrastructure', 'campus'));
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'building'));
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'area'));
        $sheet->setCellValue ( 'D1' , $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        $ccont = 0;
        
        foreach ( $results1 as $result1 ) {
	
            $sucursal = $result1->getNode ()->getParent ();

            $q2 = Doctrine_Query::create ()
                    ->select ( 'SUM(infra_info_usable_area) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                    ->from ( 'Node n' )
                    ->innerJoin ( 'n.InfraInfo ii' )
                    ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                    ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                    ->innerJoin ( 'n.NodeType nt' )
                    ->where ( '.node_parent_id = ?' , $result1->node_parent_id )
                    ->andWhere ( 'n.lft > ?' , $result1->lft )
                    ->andWhere ( 'n.rgt < ?' , $result1->rgt )
                    ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 1 )
                    ->groupBy ( 'iodv.infra_other_data_option_id' );

            $results2 = $q2->execute( array ( ) , Doctrine_Core::HYDRATE_SCALAR );

            foreach ($results2 as $result2) {

                $rcont ++;
                $sheet->setCellValue ( 'A' . $rcont , $sucursal->node_name);
                $sheet->setCellValue ( 'B' . $rcont , $result1->node_name);
                $sheet->setCellValue ( 'C' . $rcont , $result2[ 'iodo_infra_other_data_option_name' ]);
                $sheet->setCellValue ( 'D' . $rcont , $result2[ 'n_total' ]);

            }
			
        }
        
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:D1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:D1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
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
        $objWriter = PHPExcel_IOFactory :: createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    
    }
    
    function superficiePorEdificio () {
    	
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q1 = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->andWhere ( 'node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 71 );

        $results1 = $q1->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('Infrastructure', 'campus'));
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'building'));
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        $ccont = 0;
        
        foreach ( $results1 as $result1 ) {
	
            $sucursal = $result1->getNode ()->getParent ();

            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , $sucursal->node_name);
            $sheet->setCellValue ( 'B' . $rcont , $result1->node_name);
            $sheet->setCellValue ( 'C' . $rcont , $result1->InfraInfo[0]->infra_info_usable_area_total);
			
        }
        
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:C1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:C1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:C' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );
        $objWriter = PHPExcel_IOFactory :: createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    
    }
    
    function superficiePorEdificioEE () {
    	
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q1 = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->andWhere ( 'node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 71 );

        $results1 = $q1->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('Infrastructure', 'campus'));
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'building'));
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        $ccont = 0;
        
        foreach ( $results1 as $result1 ) {
	
            $sucursal = $result1->getNode ()->getParent ();

            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , $sucursal->node_name);
            $sheet->setCellValue ( 'B' . $rcont , $result1->node_name);
            $sheet->setCellValue ( 'C' . $rcont , $result1->InfraInfo[0]->infra_info_usable_area_total);
			
        }
        
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:C1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:C1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:C' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );
        $objWriter = PHPExcel_IOFactory :: createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    
    }
    
    function superficiePorAreaFacultad () {
    	
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q1 = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->andWhere ( 'node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 71 );

        $results1 = $q1->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('Infrastructure', 'campus'));
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'building'));
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'area'));
        $sheet->setCellValue ( 'D1' , $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        $ccont = 0;
        
        foreach ( $results1 as $result1 ) {
	
            $sucursal = $result1->getNode ()->getParent ();

            $q2 = Doctrine_Query::create ()
                    ->select ( 'SUM(infra_info_usable_area) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                    ->from ( 'Node n' )
                    ->innerJoin ( 'n.InfraInfo ii' )
                    ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                    ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                    ->innerJoin ( 'n.InfraOtherDataValue iodv2' )
                    ->innerJoin ( 'iodv.InfraOtherDataOption iodo2' )
                    ->innerJoin ( 'n.NodeType nt' )
                    ->where ( '.node_parent_id = ?' , $result1->node_parent_id )
                    ->andWhere ( 'n.lft > ?' , $result1->lft )
                    ->andWhere ( 'n.rgt < ?' , $result1->rgt )
                    ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 1 )
                    ->andWhere ( 'iodv2.infra_other_data_attribute_id = ?' , 8 )
                    ->groupBy ( 'iodv.infra_other_data_option_id, iodv2.infra_other_data_option_id' );

            $results2 = $q2->execute( array ( ) , Doctrine_Core::HYDRATE_SCALAR );

            foreach ($results2 as $result2) {

                    $rcont ++;
                    $sheet->setCellValue ( 'A' . $rcont , $sucursal->node_name);
                    $sheet->setCellValue ( 'B' . $rcont , $result1->node_name);
                    $sheet->setCellValue ( 'C' . $rcont , $result2[ 'iodo_infra_other_data_option_name' ]);
                    $sheet->setCellValue ( 'D' . $rcont , $result2[ 'n_total' ]);

            }
			
        }
        
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:D1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:D1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
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
        $objWriter = PHPExcel_IOFactory :: createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    
    }
    
}