<?php

class upla extends APP_Controller {

    function upla () {
        parent::APP_Controller ();
    }

    function indexPiso () {

        $node_id = $this->input->post('node_id');

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);

        $sheet->setTitle('Reporte');
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query :: create ()
                ->select('n.node_id, iodv.infra_other_data_attribute_id, nt.node_type_name, ' .
                                 'iodo.infra_other_data_option_name, iodo2.infra_other_data_option_name, SUM(infra_info_usable_area) as total_usable_area, ' .
                                 'COUNT(n.node_id) as quantity_node')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->innerJoin('n.InfraInfo ii')
                ->innerJoin('n.InfraOtherDataValue iodv')
                ->innerJoin('n.InfraOtherDataValue iodv2')
                ->innerJoin('iodv.InfraOtherDataOption iodo')
                ->innerJoin('iodv2.InfraOtherDataOption iodo2')
                ->where('node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft > ?', $node->lft)
                ->andWhere('n.rgt < ?', $node->rgt)
                ->andWhere('iodv.infra_other_data_attribute_id = ?', 4)
                ->andWhere('iodv2.infra_other_data_attribute_id = ?', 16)
                ->andWhereIn('iodv2.infra_other_data_option_id', array(453, 305, 454, 455, 456, 313, 329, 316))
                ->groupBy('iodv.infra_other_data_option_id, iodv2.infra_other_data_option_id, n.node_type_id')
                ->orderBy('iodo2.infra_other_data_option_name, node_type_name, iodo.infra_other_data_option_name');

        $results = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

        // titulos
        $sheet->setCellValue('A1', $this->translateTag('General', 'faculty'));
        $sheet->setCellValue('B1', $this->translateTag('General', 'enclosure_type'));
        $sheet->setCellValue('C1', $this->translateTag('General', 'termination_floor'));
        $sheet->setCellValue('D1', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('E1', $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        foreach ($results as $resutl) {

                $rcont++;
                $sheet->setCellValue('A' . $rcont, $resutl['iodo2_infra_other_data_option_name']);
                $sheet->setCellValue('B' . $rcont, $resutl['nt_node_type_name']);
                $sheet->setCellValue('C' . $rcont, $resutl['iodo_infra_other_data_option_name']);
                $sheet->setCellValue('D' . $rcont, $resutl['n_quantity_node']);
                $sheet->setCellValue('E' . $rcont, $resutl['n_total_usable_area']);

        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);

        $sheet->getStyle('A1:E1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:E1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:E' . $rcont)->getBorders()->applyFromArray(array(
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save ( $this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';

    }

    function indexIluminacion () {

        $node_id = $this->input->post('node_id');

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);

        $sheet->setTitle('Reporte');
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query :: create ()
                ->select('n.node_id, iodv.infra_other_data_attribute_id, nt.node_type_name, ' .
                                 'iodo.infra_other_data_option_name, iodo2.infra_other_data_option_name, SUM(iodv3.infra_other_data_value_value) as cantidad_iluminacion')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->innerJoin('n.InfraInfo ii')
                ->innerJoin('n.InfraOtherDataValue iodv')
                ->innerJoin('n.InfraOtherDataValue iodv2')
                ->innerJoin('n.InfraOtherDataValue iodv3')
                ->innerJoin('iodv.InfraOtherDataOption iodo')
                ->innerJoin('iodv2.InfraOtherDataOption iodo2')
                ->where('node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft > ?', $node->lft)
                ->andWhere('n.rgt < ?', $node->rgt)
                ->andWhere('iodv.infra_other_data_attribute_id = ?', 12)
                ->andWhere('iodv2.infra_other_data_attribute_id = ?', 16)
                ->andWhere('iodv3.infra_other_data_attribute_id = ?', 17)
                ->andWhereIn('iodv2.infra_other_data_option_id', array(453, 305, 454, 455, 456, 313, 329, 316))
                ->groupBy('iodv.infra_other_data_option_id, iodv2.infra_other_data_option_id, n.node_type_id')
                ->orderBy('iodo2.infra_other_data_option_name, node_type_name, iodo.infra_other_data_option_name');

        $results = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

        // titulos
        $sheet->setCellValue('A1', $this->translateTag('General', 'faculty'));
        $sheet->setCellValue('B1', $this->translateTag('General', 'enclosure_type'));
        $sheet->setCellValue('C1', $this->translateTag('General', 'type_of_lighting'));
        $sheet->setCellValue('D1', $this->translateTag('General', 'quantity'));

        $rcont = 1;
        foreach ($results as $resutl) {

                $rcont++;
                        $sheet->setCellValue('A' . $rcont, $resutl['iodo2_infra_other_data_option_name']);
                $sheet->setCellValue('B' . $rcont, $resutl['nt_node_type_name']);
                $sheet->setCellValue('C' . $rcont, $resutl['iodo_infra_other_data_option_name']);
                $sheet->setCellValue('D' . $rcont, $resutl['iodv3_cantidad_iluminacion']);

        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $sheet->getStyle('A1:D1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:D' . $rcont)->getBorders()->applyFromArray(array(
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save ( $this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';

    }

    /*
     * facultad - recinto - m2
     */
    function indexSuperficie1 () {

        $node_id = $this->input->post('node_id');

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);

        $sheet->setTitle('Reporte');
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query :: create ()
                ->select('n.node_id, nt.node_type_name, ' .
                                 'iodo2.infra_other_data_option_name, SUM(infra_info_usable_area) as total_usable_area, ' .
                                 'COUNT(n.node_id) as quantity_node')
                ->from('Node n')
                ->innerJoin('n.NodeType nt')
                ->innerJoin('n.InfraInfo ii')
                ->innerJoin('n.InfraOtherDataValue iodv2')
                ->innerJoin('iodv2.InfraOtherDataOption iodo2')
                ->where('node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft > ?', $node->lft)
                ->andWhere('n.rgt < ?', $node->rgt)
                ->andWhere('iodv2.infra_other_data_attribute_id = ?', 16)
                ->andWhereIn('iodv2.infra_other_data_option_id', array(453, 305, 454, 455, 456, 313, 329, 316))
                ->groupBy('iodv2.infra_other_data_option_id, n.node_type_id')
                ->orderBy('iodo2.infra_other_data_option_name, node_type_name');

        $results = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

        // titulos
        $sheet->setCellValue('A1', $this->translateTag('General', 'faculty'));
        $sheet->setCellValue('B1', $this->translateTag('General', 'enclosure_type'));
        $sheet->setCellValue('C1', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('D1', $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        foreach ($results as $resutl) {

                $rcont++;
                $sheet->setCellValue('A' . $rcont, $resutl['iodo2_infra_other_data_option_name']);
                $sheet->setCellValue('B' . $rcont, $resutl['nt_node_type_name']);
                $sheet->setCellValue('C' . $rcont, $resutl['n_quantity_node']);
                $sheet->setCellValue('D' . $rcont, $resutl['n_total_usable_area']);

        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);

        $sheet->getStyle('A1:D1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:D1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:D' . $rcont)->getBorders()->applyFromArray(array(
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save ( $this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';

    }

    /*
     * facultad - m2
     */
    function indexSuperficie2 () {

        $node_id = $this->input->post('node_id');

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);

        $sheet->setTitle('Reporte');
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query :: create ()
                ->select('n.node_id, iodo2.infra_other_data_option_name, SUM(infra_info_usable_area) as total_usable_area, ' .
                                 'COUNT(n.node_id) as quantity_node')
                ->from('Node n')
                ->innerJoin('n.InfraInfo ii')
                ->innerJoin('n.InfraOtherDataValue iodv2')
                ->innerJoin('iodv2.InfraOtherDataOption iodo2')
                ->where('node_parent_id = ?', $node->node_parent_id)
                ->andWhere('n.lft > ?', $node->lft)
                ->andWhere('n.rgt < ?', $node->rgt)
                ->andWhere('iodv2.infra_other_data_attribute_id = ?', 16)
                ->andWhereIn('iodv2.infra_other_data_option_id', array(453, 305, 454, 455, 456, 313, 329, 316))
                ->groupBy('iodv2.infra_other_data_option_id')
                ->orderBy('iodo2.infra_other_data_option_name');

        $results = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

        // titulos
        $sheet->setCellValue('A1', $this->translateTag('General', 'faculty'));
        $sheet->setCellValue('B1', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('C1', $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        foreach ($results as $resutl) {

                $rcont++;
                        $sheet->setCellValue('A' . $rcont, $resutl['iodo2_infra_other_data_option_name']);
                $sheet->setCellValue('B' . $rcont, $resutl['n_quantity_node']);
                $sheet->setCellValue('C' . $rcont, $resutl['n_total_usable_area']);

        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $sheet->getStyle('A1:C1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:C1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:C' . $rcont)->getBorders()->applyFromArray(array(
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save ( $this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';

    }

    /*
     * recinto - m2
     */
    function indexSuperficie3 () {

        $node_id = $this->input->post('node_id');

        $this->load->library('PHPExcel');
        $sheet = $this->phpexcel->setActiveSheetIndex(0);

        $sheet->setTitle('Reporte');
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        $q = Doctrine_Query :: create ()
                ->select('n.node_id, nt.node_type_name, iodo2.infra_other_data_option_name, SUM(infra_info_usable_area) as total_usable_area, ' .
                                 'COUNT(n.node_id) as quantity_node')
                ->from('Node n')
                ->innerJoin('n.InfraInfo ii')
                ->innerJoin('n.NodeType nt')
                ->innerJoin('n.InfraOtherDataValue iodv2')
                ->innerJoin('iodv2.InfraOtherDataOption iodo2')
                ->andWhere('n.lft > ?', $node->lft)
                ->andWhere('n.rgt < ?', $node->rgt)
                ->andWhere('iodv2.infra_other_data_attribute_id = ?', 16)
                ->andWhereIn('iodv2.infra_other_data_option_id', array(453, 305, 454, 455, 456, 313, 329, 316))
                ->groupBy('n.node_type_id')
                ->orderBy('nt.node_type_name');

        $results = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

        // titulos
        $sheet->setCellValue('A1', $this->translateTag('General', 'enclosure_type'));
        $sheet->setCellValue('B1', $this->translateTag('General', 'quantity'));
        $sheet->setCellValue('C1', $this->translateTag('Infrastructure', 'living_area'));

        $rcont = 1;
        foreach ($results as $resutl) {

                $rcont++;
                $sheet->setCellValue('A' . $rcont, $resutl['nt_node_type_name']);
                $sheet->setCellValue('B' . $rcont, $resutl['n_quantity_node']);
                $sheet->setCellValue('C' . $rcont, $resutl['n_total_usable_area']);

        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        $sheet->getStyle('A1:C1')->getFont()->applyFromArray(array(
            'bold' => true
        ));

        $sheet->getStyle('A1:C1')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill :: FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ));

        $sheet->getStyle('A1:C' . $rcont)->getBorders()->applyFromArray(array(
            'allborders' => array (
                'style' => PHPExcel_Style_Border :: BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ));

        $objWriter = PHPExcel_IOFactory :: createWriter($this->phpexcel, 'Excel5');
        $objWriter->save ( $this->app->getTempFileDir($this->input->post('file_name') . '.xls'));
        echo '{"success": true, "file": "' . $this->input->post ( 'file_name' ) . '.xls"}';

    }

}