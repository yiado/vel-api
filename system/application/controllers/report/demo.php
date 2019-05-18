<?php

/**
 * @package Controller
 * @subpackage ReportController
 */
class demo extends APP_Controller
{
    function demo ()
    {
        parent::APP_Controller ();
    }
    
    function indiceAgrupado ( $infra_other_data_attribute_id, $titulo )
    {
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id )
                ->groupBy ( 'iodv.infra_other_data_option_id' );

        $qt = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id );

        $results = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_SCALAR );
        $resultTotal = $qt->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , $titulo);
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'living_area') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'distribution') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'iodo_infra_other_data_option_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'n_total' ] );
            $sheet->setCellValue ( 'C' . $rcont , round ( ($result[ 'n_total' ] / $resultTotal) * 100 ) . '%' );

            $rcont ++;
        }
        // total
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'total') );
        $sheet->setCellValue ( 'B' . $rcont , $resultTotal );
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );

        $sheet->getStyle ( "C2:C" . $rcont )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );

        $sheet->getStyle ( 'A1:C1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:C1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:C' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
    
    function indiceAgrupadoDetalle ( $infra_other_data_attribute_id, $titulo )
    {
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );
        
        $q = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id )
                ->groupBy ( 'iodv.infra_other_data_option_id, n.node_type_id' );

        $qt = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , $infra_other_data_attribute_id );

        $results = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_SCALAR );
        $resultTotal = $qt->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , $titulo );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'enclosure_type') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'living_area') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'distribution') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'iodo_infra_other_data_option_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'nt_node_type_name' ] );
            $sheet->setCellValue ( 'C' . $rcont , $result[ 'n_total' ] );
            $sheet->setCellValue ( 'D' . $rcont , round ( ($result[ 'n_total' ] / $resultTotal) * 100 ) . '%' );
            $rcont ++;
        }
        // total
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'total') );
        $sheet->setCellValue ( 'C' . $rcont , $resultTotal );
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );

        $sheet->getStyle ( "D2:D" . $rcont )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
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
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );
        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
    
    function tiponodo ()
    {
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );
        
        $q = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total, n.node_id, nt.node_type_name' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'n.level = ?' , 4 )
                ->groupBy ( 'n.node_type_id' );

        $qt = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'n.level = ?' , 4 );

        $results = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_SCALAR );
        $resultTotal = $qt->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'enclosure_type') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'living_area') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'distribution') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'nt_node_type_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'n_total' ] );
            $sheet->setCellValue ( 'C' . $rcont , round ( ($result[ 'n_total' ] / $resultTotal) * 100 ) . '%' );
            $rcont ++;
        }
        // total
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'total') );
        $sheet->setCellValue ( 'B' . $rcont , $resultTotal );
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );

        $sheet->getStyle ( "C2:C" . $rcont )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
        $sheet->getStyle ( 'A1:C1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:C1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:C' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );
        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }

    function indiceClimatizacion ()
    {
    	$this->indiceAgrupado(30, $this->translateTag('General', 'climate_exchange'));
    }
    
    function indiceTermPiso ()
    {
    	$this->indiceAgrupado(19, $this->translateTag('General', 'termination_floor'));
    }
    
    function indiceTermMuro ()
    {
    	$this->indiceAgrupado(36, $this->translateTag('General', 'wall_termination'));
    }
    
    function indiceTermCielo ()
    {
    	$this->indiceAgrupado(38, $this->translateTag('General', 'termination_sky'));
    }
    
    function indiceBancoArea ()
    {
    	$this->indiceAgrupado(52, $this->translateTag('Infrastructure', 'area'));
    }
    
    function indiceBancoPlataforma ()
    {
    	$this->indiceAgrupado(53, $this->translateTag('Infrastructure', 'platform'));
    }

    function indiceBancoAreaDetalle ()
    {
    	$this->indiceAgrupadoDetalle(52, $this->translateTag('Infrastructure', 'area'));
    }
    
    function IndiceUniversidadFacultad ()
    {
    	$this->indiceAgrupado(9, $this->translateTag('General', 'faculty'));
    }

    function IndiceUniversidadFacultadDetalle ()
    {
    	$this->indiceAgrupadoDetalle(9, $this->translateTag('General', 'faculty'));
    }

    function IndiceUniversidadFacultadCapacidad ()
    {
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_capacity) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 9 )
                ->groupBy ( 'iodv.infra_other_data_option_id' );

        $qt = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_capacity) as total' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 9 );

        $results = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_SCALAR );
        $resultTotal = $qt->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('General', 'faculty') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'infra_info_capacity') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('General', 'distribution') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'iodo_infra_other_data_option_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'n_total' ] );
            $sheet->setCellValue ( 'C' . $rcont , round ( ($result[ 'n_total' ] / $resultTotal) * 100 ) . '%' );
            $rcont ++;
        }
        // total
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'total') );
        $sheet->setCellValue ( 'B' . $rcont , $resultTotal );
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        
        $sheet->getStyle ( "C2:C" . $rcont )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
        $sheet->getStyle ( 'A1:C1' )->getFont ()->applyFromArray ( array (
            'bold' => true
        ) );

        $sheet->getStyle ( 'A1:C1' )->getFill ()->applyFromArray ( array (
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:C' . $rcont )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );

        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
    
    function IndiceUniversidadFacultadCapacidadDetalle ()
    {
        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_capacity) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 9 )
                ->groupBy ( 'iodv.infra_other_data_option_id, n.node_type_id' );

        $qt = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_capacity) as total' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 9 );

        $results = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_SCALAR );
        $resultTotal = $qt->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('Infrastructure', 'area') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('General', 'enclosure_type') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'infra_info_capacity') );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'distribution') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'iodo_infra_other_data_option_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'nt_node_type_name' ] );
            $sheet->setCellValue ( 'C' . $rcont , $result[ 'n_total' ] );
            $sheet->setCellValue ( 'D' . $rcont , round ( ($result[ 'n_total' ] / $resultTotal) * 100 ) . '%' );
            $rcont ++;
        }
        // total
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'total') );
        $sheet->setCellValue ( 'C' . $rcont , $resultTotal );
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );
        
        $sheet->getStyle ( "C2:D" . $rcont )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
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
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );

        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
    
    function prorrateo () {

        $node_id = $this->input->post ( 'node_id' );
        $this->load->library ( 'PHPExcel' );
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( 'Results' );
        $node = Doctrine_Core::getTable ( 'Node' )->find ( $node_id );

        $q = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total, iodo.infra_other_data_option_name, n.node_id, nt.node_type_name' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->innerJoin ( 'iodv.InfraOtherDataOption iodo' )
                ->innerJoin ( 'n.NodeType nt' )
                ->where ( '.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 53 )
                ->groupBy ( 'iodv.infra_other_data_option_id' );

        $qt = Doctrine_Query::create ()
                ->select ( 'SUM(infra_info_usable_area) as total' )
                ->from ( 'Node n' )
                ->innerJoin ( 'n.InfraInfo ii' )
                ->innerJoin ( 'n.InfraOtherDataValue iodv' )
                ->where ( 'n.node_parent_id = ?' , $node->node_parent_id )
                ->andWhere ( 'n.lft > ?' , $node->lft )
                ->andWhere ( 'n.rgt < ?' , $node->rgt )
                ->andWhere ( 'iodv.infra_other_data_attribute_id = ?' , 53 );

        $qct = Doctrine_Query::create ()
                ->select ( 'SUM(costs_value) as total_cost' )
                ->from ( 'Costs c' )
                ->where ( 'node_id = ?' , $node_id );
                

        $results = $q->execute ( array ( ) , Doctrine_Core::HYDRATE_SCALAR );
        $resultTotal = $qt->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );
        $resultCostTotal = $qct->execute ( array ( ) , Doctrine_Core::HYDRATE_SINGLE_SCALAR );

        // titulos
        $sheet->setCellValue ( 'A1' , $this->translateTag('Infrastructure', 'platform') );
        $sheet->setCellValue ( 'B1' , $this->translateTag('Infrastructure', 'living_area') );
        $sheet->setCellValue ( 'C1' , $this->translateTag('Infrastructure', 'apportionment')  );
        $sheet->setCellValue ( 'D1' , $this->translateTag('General', 'distribution') );

        $rcont = 2;
        $ccont = 0;
        foreach ( $results as $result )
        {
            $porcentaje = ($result[ 'n_total' ] / $resultTotal);
            
            $sheet->setCellValue ( 'A' . $rcont , $result[ 'iodo_infra_other_data_option_name' ] );
            $sheet->setCellValue ( 'B' . $rcont , $result[ 'n_total' ] );
            $sheet->setCellValue ( 'C' . $rcont , round (  ($porcentaje * $resultCostTotal) . '%' ));
            $sheet->setCellValue ( 'D' . $rcont , round ($porcentaje * 100) . '%' );

            $rcont ++;
        }
        // total
        $sheet->setCellValue ( 'A' . $rcont , $this->translateTag('General', 'total') );
        $sheet->setCellValue ( 'B' . $rcont , $resultTotal );
        $sheet->setCellValue ( 'C' . $rcont , $resultCostTotal );
        
        $sheet->getColumnDimension ( 'A' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'B' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'C' )->setAutoSize ( true );
        $sheet->getColumnDimension ( 'D' )->setAutoSize ( true );

        $sheet->getStyle ( "D2:D" . $rcont )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );

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
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    
    	
    }
    
    function comparacionGastosEdificio () {
    	
        copy ( './reporte_comparacion_sucursal.xls' , $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    	
    }

    function hardreporte1 ()
    {
        copy ( './ReporteMantenimiento.xls' , $this->app->getTempFileDir ( $this->input->post ( 'file_name' ) . '.xls' ) );
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';
    }
}