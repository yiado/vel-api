<?php

class bech extends APP_Controller
{
    function bech ()
    {
        parent :: APP_Controller ();
    }

    function indexuo ()
    {
        $node_id = $this->input->post ( 'node_id' );
        ini_set ( "memory_limit" , "2000M" );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );
        
        $q = Doctrine_Query :: create ()
                ->select ( 'n.node_id, sum(ii.infra_info_usable_area) as total, iodv.infra_other_data_value_value' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 8 )
                ->groupBy ( 'iodv.infra_other_data_value_value' );

        $results = $q->execute ( array ( ) , Doctrine_Core :: HYDRATE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , 'UO' );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'living_area') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'iodv_infra_other_data_value_value' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'ii_total' ] );
            $rcont ++;
        }
        // total
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getStyle ( 'A1:B1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:B1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:B' . $rcont )->getBorders ()->applyFromArray ( array (
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

    function dotacion ()
    {

        $node_id = $this->input->post ( 'node_id' );
        ini_set ( "memory_limit" , "2000M" );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->leftJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 4 ) //			    A   C  D   E   F   G  H    I   J    K    L
                ->andWhere ( 'iodv.infra_other_data_attribute_id IN (64, 1, 36, 17, 12, 7, 101, 8, 72, 113, 114)' );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'address') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'deputy_manager') );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'property_type') );
        $sheet->setCellValue ( 'F1' , $this->translateTag('General', 'branch_category') ); // 12
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'branch_code') ); // 7
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'cost_center_branch') ); // 101
        $sheet->setCellValue ( 'I1' , $this->translateTag('General', 'organizational_unit') ); // 8
        $sheet->setCellValue ( 'J1' , $this->translateTag('General', 'staffing') );
        $sheet->setCellValue ( 'K1' , $this->translateTag('General', 'womens_purse') );
        $sheet->setCellValue ( 'L1' , $this->translateTag('General', 'men_purse') );

        $rcont = 1;
        $ccont = 0;
        foreach ( $results as $result )
        {

            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , @$result->InfraOtherDataValue[ 4 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'B' . $rcont , @$result->node_name );
            $sheet->setCellValue ( 'C' . $rcont , @$result->InfraOtherDataValue[ 6 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'D' . $rcont , @$result->InfraOtherDataValue[ 5 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'E' . $rcont , @$result->InfraOtherDataValue[ 7 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'F' . $rcont , @$result->InfraOtherDataValue[ 3 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'G' . $rcont , @$result->InfraOtherDataValue[ 1 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'H' . $rcont , @$result->InfraOtherDataValue[ 0 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'I' . $rcont , @$result->InfraOtherDataValue[ 2 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'J' . $rcont , @$result->InfraOtherDataValue[ 8 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'K' . $rcont , @$result->InfraOtherDataValue[ 9 ]->infra_other_data_value_value );
            $sheet->setCellValue ( 'L' . $rcont , @$result->InfraOtherDataValue[ 10 ]->infra_other_data_value_value );
        }
        // total

        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'J' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'K' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'L' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:L1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:L1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:L' . $rcont )->getBorders ()->applyFromArray ( array (
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

    function recinto ()
    {
        $node_id = $this->input->post ( 'node_id' );
        ini_set ( "memory_limit" , "2000M" );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id IN (62, 73, 87, 69, 57)' )
                ->andWhere ( 'iodv.infra_other_data_attribute_id IN (132, 134, 133, 22)' );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'region') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'floor') );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'campus') );
        $sheet->setCellValue ( 'F1' , $this->translateTag('General', 'type_of_lighting') );
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'type_of_furniture') );
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'type_of_floor') );
        $sheet->setCellValue ( 'I1' , $this->translateTag('General', 'using_type') );

        $rcont = 1;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $rcont ++;
            $ccont = 0;
            $ancestors = Doctrine_Core::getTable ( 'Node' )->find ( $result->node_id )->getNode ()->getAncestors ();

            foreach ( $ancestors as $ancestor )
            {
                if ( $ancestor->node_type_id == 15 )
                    continue;

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
                $sheet->setCellValue ( $columnLetter . $rcont , $ancestor->node_name );
            }
            $sheet->setCellValue ( 'E' . $rcont , $result->node_name );
            $sheet->setCellValue ( 'F' . $rcont , $result->InfraOtherDataValue[ 0 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'G' . $rcont , $result->InfraOtherDataValue[ 1 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'H' . $rcont , $result->InfraOtherDataValue[ 2 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'I' . $rcont , $result->InfraOtherDataValue[ 3 ]->InfraOtherDataOption->infra_other_data_option_name );
        }
        // total
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:I1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:I1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:I' . $rcont )->getBorders ()->applyFromArray ( array (
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

    function boveda ()
    {
        $node_id = $this->input->post ( 'node_id' );
        ini_set ( "memory_limit" , "2000M" );
        $this->load->library ( 'PHPExcel' );

        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );

        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id IN (61)' )
                ->andWhere ( 'iodv.infra_other_data_attribute_id IN (22)' );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'region') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'floor') );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'cost_center_branch') );
        $sheet->setCellValue ( 'F1' , $this->translateTag('General', 'branch_code') );
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'venue_name') );
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'using_type') );
        $sheet->setCellValue ( 'I1' , $this->translateTag('Infrastructure', 'living_area') );
        $sheet->setCellValue ( 'J1' , $this->translateTag('General', 'height') );

        $rcont = 1;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $rcont ++;
            $ccont = 0;
            $ancestors = Doctrine_Core::getTable ( 'Node' )->find ( $result->node_id )->getNode ()->getAncestors ();

            foreach ( $ancestors as $ancestor )
            {
                if ( $ancestor->node_type_id == 15 )
                    continue;
                    
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
                $sheet->setCellValue ( $columnLetter . $rcont , $ancestor->node_name );

                if ( $ancestor->node_type_id == 4 )
                {
                    $infoSucursalId = $ancestor->node_id;
                }
            }

            // CENTRO DE COSTO SUCURSAL
            $valueCC = Doctrine_Core::getTable ( 'InfraOtherDataValue' )->retrieveByAttributeNode ( $infoSucursalId , 101 );
            $sheet->setCellValue ( 'E' . $rcont , ($valueCC) ? $valueCC->infra_other_data_value_value : NULL  );

            // CCODIGO SUCURSAL
            $valueCS = Doctrine_Core::getTable ( 'InfraOtherDataValue' )->retrieveByAttributeNode ( $infoSucursalId , 7 );
            $sheet->setCellValue ( 'F' . $rcont , ($valueCS) ? $valueCS->infra_other_data_value_value : NULL  );
            $sheet->setCellValue ( 'G' . $rcont , $result->node_name );
            $sheet->setCellValue ( 'H' . $rcont , $result->InfraOtherDataValue[ 0 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'I' . $rcont , $result->InfraInfo[ 0 ]->infra_info_usable_area );
            $sheet->setCellValue ( 'J' . $rcont , $result->InfraInfo[ 0 ]->infra_info_height );
        }
        // total
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'J' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:J1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:J1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:J' . $rcont )->getBorders ()->applyFromArray ( array (
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

    function site ()
    {
        $node_id = $this->input->post ( 'node_id' );
        ini_set ( "memory_limit" , "2000M" );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id IN (71)' )
                ->andWhere ( 'iodv.infra_other_data_attribute_id IN (131, 132, 133)' );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'region') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'floor') );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'campus') );
        $sheet->setCellValue ( 'F1' , 'M2' );
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'type_sky') );
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'type_of_lighting') );
        $sheet->setCellValue ( 'I1' , $this->translateTag('General', 'type_of_floor') );

        $rcont = 1;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $rcont ++;
            $ccont = 0;
            $ancestors = Doctrine_Core::getTable ( 'Node' )->find ( $result->node_id )->getNode ()->getAncestors ();

            foreach ( $ancestors as $ancestor )
            {
                if ( $ancestor->node_type_id == 15 )
                    continue;

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex ( $ccont ++  );
                $sheet->setCellValue ( $columnLetter . $rcont , $ancestor->node_name );
            }
            $sheet->setCellValue ( 'E' . $rcont , $result->node_name );
            $sheet->setCellValue ( 'F' . $rcont , $result->InfraInfo[ 0 ]->infra_info_usable_area );
            $sheet->setCellValue ( 'G' . $rcont , $result->InfraOtherDataValue[ 0 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'H' . $rcont , $result->InfraOtherDataValue[ 1 ]->InfraOtherDataOption->infra_other_data_option_name );
            $sheet->setCellValue ( 'I' . $rcont , $result->InfraOtherDataValue[ 2 ]->InfraOtherDataOption->infra_other_data_option_name );
        }
        // total
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
        $sheet->getStyle ( 'A1:I1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:I1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:I' . $rcont )->getBorders ()->applyFromArray ( array (
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
    
    function exportPlanServiEstadoListExcelBranch ()
    {
        $node_id = $this->input->post ( 'node_id' );

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 96 );

        $resuls = $q->execute ( array () , Doctrine_Core::HYDRATE_ARRAY );

		$rcont = 1;
		
		
		$sheet->setCellValue('A1' , $this->translateTag('General', 'serviestado'));
		$sheet->setCellValue('B1' , $this->translateTag('General', 'floor'));
		$sheet->setCellValue('C1' , $this->translateTag('General', 'category'));
		$sheet->setCellValue('D1' , $this->translateTag('General', 'date_time_charge'));
		

        foreach ( $resuls as $node )
        {
            
            $node_id = $node['node_id'];
            
            $plans = Doctrine_Core::getTable ( 'Plan' )->retrieveCurrents ( $node_id );
            
            if ($plans->count()) {
            	
            	
            	foreach ($plans as $plan) {
            		
            		$rcont++;
            		
	            	$nodeTree = Doctrine_Core::getTable ( 'Node' )->find ( $node[ 'node_id' ] )->getNode();
	            	
	            	foreach ($nodeTree->getAncestors() as $ancestor) {
	            		
	            		if ($ancestor->node_type_id == 94) {
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

        $node_id = $this->input->post ( 'node_id' );

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 96 );

        $resuls = $q->execute ( array () , Doctrine_Core::HYDRATE_ARRAY );

		$rcont = 1;
		
		
		$sheet->setCellValue('A1' , $this->translateTag('General', 'serviestado'));
		$sheet->setCellValue('B1' , $this->translateTag('General', 'description'));
		$sheet->setCellValue('C1' , $this->translateTag('General', 'category'));
		$sheet->setCellValue('D1' , $this->translateTag('General', 'date_time_charge'));
		

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
    
    function acceso ()
    {
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        
		$statement = Doctrine_Manager::getInstance()->connection();

        $results = $statement->execute('SELECT user.user_name, user_email, MONTHNAME(log_date_time) as mes, YEAR(log_date_time) as anio, COUNT(*) AS cantidad ' .
                                      'FROM user ' .
                                      'JOIN log ON user.user_id = log.user_id ' .
                                      'WHERE log_type_id = 1 ' .
                                      'GROUP BY MONTH(log_date_time), YEAR(log_date_time), log.user_id ' .
                                      'ORDER BY YEAR(log_date_time), MONTH(log_date_time)');

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'user') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Core', 'email') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'month') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'year')  );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'access') );

        $rcont = 1;
        $ccont = 0;
        
        foreach ( $results->fetchAll() as $result )
        {
            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'user_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'user_email' ] );
            $sheet->setCellValue ( 'C' . $rcont , $this->translateTag('General', strtolower($result[ 'mes' ])) );
            $sheet->setCellValue ( 'D' . $rcont , $result[ 'anio' ] );
            $sheet->setCellValue ( 'E' . $rcont , $result[ 'cantidad' ] );
        }
        // total
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        
        $sheet->getStyle ( 'A1:E1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:E1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:E' . $rcont )->getBorders ()->applyFromArray ( array (
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
    
    function ubicacion_arriendo ()
    {

		$node_id = $this->input->post ( 'node_id' );

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 4 );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , 'CTTO' );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'region') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'city') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'address') );
        $sheet->setCellValue ( 'F1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'name_of_lessor') );
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'ruth_landlord') );
        $sheet->setCellValue ( 'I1' , $this->translateTag('General', 'term_contract') );
        $sheet->setCellValue ( 'J1' , $this->translateTag('General', 'income_in_pesos') );
        $sheet->setCellValue ( 'K1' , $this->translateTag('General', 'rent_uf') );

        $rcont = 1;
        $ccont = 0;
        
        $totals = array(
        	'renta_pesos' => 0,
        	'renta_ufs'   => 0
        );
        
        foreach ( $results as $result )
        {
	
            $region_id = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 104)->infra_other_data_option_id;
            $renta_pesos = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 185)->infra_other_data_value_value;
            $renta_ufs = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 186)->infra_other_data_value_value;

            $totals['renta_pesos'] += $renta_pesos;
            $totals['renta_ufs'] += $renta_ufs;

            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 101)->infra_other_data_value_value);
            $sheet->setCellValue ( 'B' . $rcont , @Doctrine_Core::getTable('InfraOtherDataOption')->find($region_id)->infra_other_data_option_name);
            $sheet->setCellValue ( 'C' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 145)->infra_other_data_value_value);
            $sheet->setCellValue ( 'D' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 64)->infra_other_data_value_value);
            $sheet->setCellValue ( 'E' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 1)->infra_other_data_value_value);
            $sheet->setCellValue ( 'F' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 5)->infra_other_data_value_value);
            $sheet->setCellValue ( 'G' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 170)->infra_other_data_value_value);
            $sheet->setCellValue ( 'H' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 184)->infra_other_data_value_value);
            $sheet->setCellValue ( 'I' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 183)->infra_other_data_value_value);
            $sheet->setCellValue ( 'J' . $rcont , $renta_pesos);
            $sheet->setCellValue ( 'K' . $rcont , $renta_ufs);
        }

        // total
        $rcont ++;
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'overall'));
        $sheet->setCellValue ( 'J' . $rcont , $totals['renta_pesos']);
        $sheet->setCellValue ( 'K' . $rcont , $totals['renta_ufs']);
        
        $sheet->getStyle('J' . $rcont . ':K' . $rcont)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        

        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'J' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'K' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:K1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:K1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:K' . $rcont )->getBorders ()->applyFromArray ( array (
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
    
    function valorizacion_arriendo ()
    {

		$node_id = $this->input->post ( 'node_id' );
		
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 4 );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'region') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'city') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'E1' , $this->translateTag('General', 'address') );
        $sheet->setCellValue ( 'F1' , $this->translateTag('General', 'type_of_contract') );
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'name_of_lessor') );
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'income_in_pesos') );
        $sheet->setCellValue ( 'I1' , $this->translateTag('General', 'rent_uf') );
        $sheet->setCellValue ( 'J1' , $this->translateTag('Infrastructure', 'infra_info_terrain_area') );
        $sheet->setCellValue ( 'K1' , $this->translateTag('Infrastructure', 'infra_info_area') );
        $sheet->setCellValue ( 'L1' , $this->translateTag('General', 'start_date') );
        $sheet->setCellValue ( 'M1' , $this->translateTag('General', 'end_date') );
        $sheet->setCellValue ( 'N1' , $this->translateTag('General', 'to_value') );

        $rcont = 1;
        $ccont = 0;
        $totals = array(
            'renta_pesos' => 0,
            'renta_ufs'   => 0,
            'tasacion'   => 0,
            'superficie_terreno' => 0,
            'superficie_construida' => 0
        );
        foreach ( $results as $result )
        {
	
            $region_id = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 104)->infra_other_data_option_id;

            $renta_pesos = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 185)->infra_other_data_value_value;
            $renta_ufs = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 186)->infra_other_data_value_value;
            $tasacion = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 168)->infra_other_data_value_value;
            $superficie_terreno = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 65)->infra_other_data_value_value;
            $superficie_construida = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 3)->infra_other_data_value_value;

            $totals['renta_pesos'] += $renta_pesos;
            $totals['renta_ufs'] += $renta_ufs;
            $totals['tasacion'] += $tasacion;
            $totals['superficie_terreno'] += $superficie_terreno;
            $totals['superficie_construida'] += $superficie_construida;

            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , @Doctrine_Core::getTable('InfraOtherDataOption')->find($region_id)->infra_other_data_option_name);
            $sheet->setCellValue ( 'B' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 145)->infra_other_data_value_value);
            $sheet->setCellValue ( 'C' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 64)->infra_other_data_value_value);
            $sheet->setCellValue ( 'D' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 5)->infra_other_data_value_value);
            $sheet->setCellValue ( 'E' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 1)->infra_other_data_value_value);
            $sheet->setCellValue ( 'F' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 187)->infra_other_data_value_value);
            $sheet->setCellValue ( 'G' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 170)->infra_other_data_value_value);
            $sheet->setCellValue ( 'H' . $rcont , $renta_pesos);
            $sheet->setCellValue ( 'I' . $rcont , $renta_ufs);
            $sheet->setCellValue ( 'J' . $rcont , $superficie_terreno);
            $sheet->setCellValue ( 'K' . $rcont , $superficie_construida);
            $sheet->setCellValue ( 'L' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 171)->infra_other_data_value_value);
            $sheet->setCellValue ( 'M' . $rcont , @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 172)->infra_other_data_value_value);
            $sheet->setCellValue ( 'N' . $rcont , $tasacion);
            $sheet->setCellValue ( 'O' . $rcont , $result->node_name);
            
            $sheet->getStyle('H' . $rcont . ':I' . $rcont)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
        }
        
        // total
        $rcont ++;
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'overall'));
        $sheet->setCellValue ( 'H' . $rcont , $totals['renta_pesos']);
        $sheet->setCellValue ( 'I' . $rcont , $totals['renta_ufs']);
        $sheet->setCellValue ( 'J' . $rcont , $totals['superficie_terreno']);
        $sheet->setCellValue ( 'K' . $rcont , $totals['superficie_construida']);
        $sheet->setCellValue ( 'N' . $rcont , $totals['tasacion']);
        
        $sheet->getStyle('A' . $rcont . ':N' . $rcont)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'J' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'K' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'L' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'M' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'N' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:N1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:N1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:N' . $rcont )->getBorders ()->applyFromArray ( array (
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
    
    public function getsucursalparent ( $node_id, $separator='/' , $include_node=true )
    {

		$node = Doctrine_Core::getTable('Node')->find($node_id);

        $buffer = array ( );

        if ( ! $node->getNode ()->isRoot () )
        {

            $ancestors = $node->getNode ()->getAncestors ();
            if ( $ancestors )
            {
                foreach ( $ancestors as $ancestor )
                {

                    if ($ancestor->node_type_id == 4) {
                    
                    	array_push ( $buffer , $ancestor->node_name );
                    
                    }
                    
                }
            }
        }

        return implode ( $separator , $buffer );
        
    }
    
    function documentosfaltantes () {
    	
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        
        $sheet->setCellValue ( 'A1' , 'ID' );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'documents') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'category') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('Core', 'location') );

        $rcont = 1;
    	
        $q1 = $this->db->query("SELECT * FROM doc_migracion WHERE flag = 0");

        foreach ($q1->result() as $doc) {
        	
            $rcont++;
            $sheet->setCellValue ( 'A' . $rcont , $doc->doc_id);
            $sheet->setCellValue ( 'B' . $rcont , $doc->name);
            $sheet->setCellValue ( 'C' . $rcont , $doc->subject_name);
            $sheet->setCellValue ( 'D' . $rcont , iconv("ISO-8859-1", "UTF-8", $this->getsucursalparent($doc->node_id)));
        	
        }
        
        $objWriter = PHPExcel_IOFactory :: createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( 'documentos_no_migrados.xls' ) );
    	
    }
    
    function puestoTrabajo () {

        $node_id = $this->input->post ( 'node_id' );
        ini_set ( "memory_limit" , "2000M" );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->innerJoin ( 'n.NodeType nt' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id  IN (69, 108)');

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'venue_name') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infraestructura', 'node_type') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infraestructura', 'infra_info_usable_area') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('Infraestructura', 'infra_info_capacity') ); 
        $sheet->setCellValue ( 'E1' , $this->translateTag('Infraestructura', 'capacity_used') ); // 25
        $sheet->setCellValue ( 'F1' , $this->translateTag('Infraestructura', 'platform') ); // 126
        $sheet->setCellValue ( 'G1' , $this->translateTag('Infraestructura', 'trade_platform') ); // 75
        $sheet->setCellValue ( 'H1' , $this->translateTag('General', 'region')  );
        $sheet->setCellValue ( 'I1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'J1' , $this->translateTag('General', 'branch')  );

        $rcont = 1;
        $ccont = 0;
        foreach ( $results as $result )
        {
			$infraInfo = Doctrine_Core::getTable('InfraInfo')->findByNodeId($result->node_id);
			$value25 = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 25);
			$value126 = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 126);
			$value75 = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 75);
			
            $rcont ++;
            $sheet->setCellValue ( 'A' . $rcont , @$result->node_name );
            $sheet->setCellValue ( 'B' . $rcont , @$result->NodeType->node_type_name );
            $sheet->setCellValue ( 'C' . $rcont , $infraInfo->infra_info_usable_area );
            $sheet->setCellValue ( 'D' . $rcont , $infraInfo->infra_info_capacity );
            $sheet->setCellValue ( 'E' . $rcont , @$value25->infra_other_data_value_value );
            $sheet->setCellValue ( 'F' . $rcont , @Doctrine_Core::getTable('InfraOtherDataOption')->find($value126->infra_other_data_option_id)->infra_other_data_option_name );
            $sheet->setCellValue ( 'G' . $rcont , @Doctrine_Core::getTable('InfraOtherDataOption')->find($value75->infra_other_data_option_id)->infra_other_data_option_name );
         
            $nodeTree = Doctrine_Core::getTable ( 'Node' )->find ($result->node_id)->getNode();

            foreach ($nodeTree->getAncestors() as $ancestor) {

                    switch ($ancestor->node_type_id) {
                        case 2:
                                $sheet->setCellValue('H' . $rcont , $ancestor->node_name);
                                break;
                        case 3:
                                $sheet->setCellValue('I' . $rcont , $ancestor->node_name);
                                break;
                        case 4:
                                $sheet->setCellValue('J' . $rcont , $ancestor->node_name);
                                break;
                        default:
                                break;
                }

            }
         
            
        }
        // total

        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'H' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'I' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'J' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:J1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:J1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:J' . $rcont )->getBorders ()->applyFromArray ( array (
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
    
    function reportegos2 () {

        $node_id = $this->input->post ( 'node_id' );

        $sttt = Doctrine_Manager::getInstance()->connection();

        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core :: getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query :: create ()
                ->from ( 'Node n' )
                ->where ( 'node_parent_id = ?' , $node->node_parent_id )
                ->where ( 'n.lft >= ?' , $node->lft )
                ->andWhere ( 'n.rgt <= ?' , $node->rgt )
                ->andWhere ( 'n.node_type_id = ?' , 4 );

        $results = $q->execute ();

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'region') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'commune') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'branch') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('Infrastructure', 'area'));
        $sheet->setCellValue ( 'E1' , $this->translateTag('Infrastructure', 'platform') );
        $sheet->setCellValue ( 'F1' , $this->translateTag('Infrastructure', 'trade_platform') );
        $sheet->setCellValue ( 'G1' , $this->translateTag('General', 'amount_posts') );

        $rcont = 1;
        $ccont = 0;
        
        $totals = array(
        	'renta_pesos' => 0,
        	'renta_ufs'   => 0
        );
        
        foreach ( $results as $result )
        {
			
            $rs = $sttt->execute("SELECT iodo1.infra_other_data_option_name as area, iodo2.infra_other_data_option_name as plataforma, 
            iodo3.infra_other_data_option_name as plataforma_comercial, sum(infra_info_capacity) as cantidad  
            FROM  node 
            JOIN infra_info ON infra_info.node_id = node.node_id 
            LEFT JOIN infra_other_data_value as iodv1 ON iodv1.node_id = node.node_id AND iodv1.infra_other_data_attribute_id = 126 
            LEFT JOIN infra_other_data_option as iodo1 ON iodo1.infra_other_data_option_id = iodv1.infra_other_data_option_id
            LEFT JOIN infra_other_data_value as iodv2 ON iodv2.node_id = node.node_id AND iodv2.infra_other_data_attribute_id = 75 
            LEFT JOIN infra_other_data_option as iodo2 ON iodo2.infra_other_data_option_id = iodv2.infra_other_data_option_id
            LEFT JOIN infra_other_data_value as iodv3 ON iodv3.node_id = node.node_id AND iodv3.infra_other_data_attribute_id = 96 
            LEFT JOIN infra_other_data_option as iodo3 ON iodo3.infra_other_data_option_id = iodv3.infra_other_data_option_id
            WHERE node_parent_id = " . $result->node_parent_id . "  
            AND  lft >= " . $result->lft . "
            AND  rgt <= " . $result->rgt . " 
            AND   infra_info_capacity > 0
            GROUP BY iodv1.infra_other_data_option_id, iodv2.infra_other_data_option_id, iodv3.infra_other_data_option_id");

            foreach ($rs->fetchAll() as $sucrs) {

                $rcont ++;
                        // ancestors
                $nodeTree = Doctrine_Core::getTable ( 'Node' )->find ($result->node_id)->getNode();

                foreach ($nodeTree->getAncestors() as $ancestor) {

                    switch ($ancestor->node_type_id) {
                        case 2:
                            $sheet->setCellValue('A' . $rcont , $ancestor->node_name);
                            break;
                        case 3:
                            $sheet->setCellValue('B' . $rcont , $ancestor->node_name);
                            break;
                        default:
                            break;
                    }

                }

                $sheet->setCellValue('C' . $rcont , $result->node_name);
                $sheet->setCellValue('D' . $rcont , $sucrs['area']);
                $sheet->setCellValue('E' . $rcont , $sucrs['plataforma']);
                $sheet->setCellValue('F' . $rcont , $sucrs['plataforma_comercial']);
                $sheet->setCellValue('G' . $rcont , $sucrs['cantidad']);

            }
			
        }
        
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'E' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'F' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'G' )->setAutoSize ( true );

        $sheet->getStyle ( 'A1:G1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:G1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:G' . $rcont )->getBorders ()->applyFromArray ( array (
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