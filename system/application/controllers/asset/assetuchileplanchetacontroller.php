<?php

/**
 * @package    Controller
 * @subpackage AssetController
 */
class AssetUchilePlanchetaController extends APP_Controller {

    function AssetUchilePlanchetaController() {
        parent::APP_Controller();
    }

    /**
     * exportPlancheta
     *
     * Exporta el listado actual de la Plancheta en formato pdf
     *
     */
    function exportPlancheta($node_id = null) {
        $data = array();

        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->where('a.node_id = ?', $node_id)
                ->orderBy('asset_name');

        $resuls_br = $q->execute();

        $array_br = array();
        $cont = 0;
        foreach ($resuls_br as $asset) {

            $array_br[$cont]['asset_num_serie_intern'] = $asset->asset_num_serie_intern;
            $array_br[$cont]['asset_type_name'] = $asset->AssetType->asset_type_name;
            $array_br[$cont]['asset_name'] = $asset->asset_name;
            $array_br[$cont]['brand_name'] = $asset->Brand->brand_name;
            $array_br[$cont]['asset_num_serie'] = $asset->asset_num_serie;
            $array_br[$cont]['asset_path_3_niveles'] = $asset->asset_path_3_niveles;
            $array_br[$cont]['asset_description'] = $asset->asset_description;
            $cont++;
        }



        $data['br_list'] = $array_br;
        $data['organismo'] = @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 6)->infra_other_data_option_id)->infra_other_data_option_name;
        $data['departamento'] = @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 8)->infra_other_data_option_id)->infra_other_data_option_name;
        $data['unidad'] = @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 10)->infra_other_data_option_id)->infra_other_data_option_name;
        $data['codigo_recinto'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 46)->infra_other_data_value_value;
        $data['codigo_subrecinto'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 3)->infra_other_data_value_value;
        $data['nombre_recinto'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 2)->infra_other_data_value_value;
        $data['usuario'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 398)->infra_other_data_value_value;
        $ancestors = Doctrine_Core::getTable('Node')->find($node_id)->getNode()->getAncestors();

        if ($ancestors) {
            foreach ($ancestors as $ancestor) {

                if ($ancestor->node_type_id == 23) {
                    $data['direccion'] = $ancestor->node_name;
                }

                if ($ancestor->node_type_id == 2) {
                    $data['edificio'] = $ancestor->node_name;
                    $data['conservador'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($ancestor->node_id, 396)->infra_other_data_value_value;
                }
            }
        }
//                echo $this->config->item('company_logo');exit();
        $html = $this->load->view('uchileplancheta', $data, true);
        $this->load->library('pdf');
        $this->load->library('zend', array('Zend/Barcode'));

        $imageResource = Zend_Barcode::draw('code128', 'image', array('text' => $data['codigo_recinto'], 'drawText' => true, 'barThickWidth' => 1, 'barHeight' => 25), array());

        imagejpeg($imageResource, 'temp/barcode_uchile_plancheta.jpg', 100);

        $this->pdf->SetFont('helvetica', '', 8);

        // add a page
        $this->pdf->AddPage();

        $this->pdf->writeHTML($html, true, false, true, false, '');

        $this->pdf->Output('plancheta_' . $data['codigo_recinto'] . '.pdf', 'D');

        $this->syslog->register('asset_export_plancheta', array(
            'plancheta_' . $data['codigo_recinto'] . '.pdf'
        )); // registering log
    }

    function validarNivel() {
        $node_id = $this->input->post('node_id');
        $node = Doctrine_Core::getTable('Node')->find($node_id);

        if ($node->node_type_id != 3) {//3  ES SOLO PARA EL NIVEL
            $json_data = $this->json->encode(array('success' => 'false', 'msg' => $this->translateTag('Asset', 'select_an_enclosure_type_type_level')));
        } else {
            $json_data = $this->json->encode(array('success' => 'true', 'msg' => $this->translateTag('General', 'operation_successful')));
        }
        echo $json_data;
    }

    function exportPlanchetaNivel($node_id = null) {
        $node = Doctrine_Core::getTable('Node')->find($node_id);
        if ((!empty($node)) && (!is_null($node))) {
            if ($node->node_type_id == 3) {//3  ES SOLO PARA EL NIVEL
                $q = Doctrine_Query :: create()
                        ->from('Node n')
                        ->where('node_parent_id = ?', $node->node_parent_id)
                        ->andWhere('n.lft >= ?', $node->lft)
                        ->andWhere('n.rgt <= ?', $node->rgt);

                $results = $q->execute();


                foreach ($results as $result) {
                    if ($result->node_type_id != 3) { //QUE NO SALGA EL TIPO NIVEL(PISO) SOLO LO DE ADENTRO
                        $data = array();

                        $q = Doctrine_Query::create()
                                ->from('Asset a')
                                ->innerJoin('a.AssetType at')
                                ->innerJoin('a.Brand ba')
                                ->where('a.node_id = ?', $result->node_id)
                                ->orderBy('asset_name');

                        $resuls_br = $q->execute();

                        $array_br = array();
                        $cont = 0;
                        foreach ($resuls_br as $asset) {
                            $array_br[$cont]['asset_num_serie_intern'] = $asset->asset_num_serie_intern;
                            $array_br[$cont]['asset_type_name'] = $asset->AssetType->asset_type_name;
                            $array_br[$cont]['asset_name'] = $asset->asset_name;
                            $array_br[$cont]['brand_name'] = $asset->Brand->brand_name;
                            $array_br[$cont]['asset_num_serie'] = $asset->asset_num_serie;
                            $array_br[$cont]['asset_description'] = $asset->asset_description;
                            $array_br[$cont]['asset_path_3_niveles'] = $asset->asset_path_3_niveles;
                            $cont++;
                        }

                        $data['br_list'] = $array_br;
                        $data['organismo'] = @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 6)->infra_other_data_option_id)->infra_other_data_option_name;
                        $data['departamento'] = @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 8)->infra_other_data_option_id)->infra_other_data_option_name;
                        $data['unidad'] = @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 10)->infra_other_data_option_id)->infra_other_data_option_name;
                        $data['codigo_recinto'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 46)->infra_other_data_value_value;
                        $data['codigo_subrecinto'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 3)->infra_other_data_value_value;
                        $data['nombre_recinto'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 2)->infra_other_data_value_value;
                        $data['usuario'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($result->node_id, 398)->infra_other_data_value_value;
                        $ancestors = Doctrine_Core::getTable('Node')->find($result->node_id)->getNode()->getAncestors();

                        if ($ancestors) {
                            foreach ($ancestors as $ancestor) {

                                if ($ancestor->node_type_id == 23) {
                                    $data['direccion'] = $ancestor->node_name;
                                }

                                if ($ancestor->node_type_id == 2) {
                                    $data['edificio'] = $ancestor->node_name;
                                    $data['conservador'] = @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($ancestor->node_id, 396)->infra_other_data_value_value;
                                }
                            }
                        }

                        $html = $this->load->view('uchileplancheta', $data, true);
                        $this->load->library('pdf');
                        $this->load->library('zend', array('Zend/Barcode'));

                        $imageResource = Zend_Barcode::draw('code128', 'image', array('text' => $data['codigo_recinto'], 'drawText' => true, 'barThickWidth' => 1, 'barHeight' => 25), array());

                        imagejpeg($imageResource, 'temp/barcode_uchile_plancheta.jpg', 100);

                        $this->pdf->SetFont('helvetica', '', 8);

                        // add a page
                        $this->pdf->AddPage();
                        $this->pdf->writeHTML($html, true, false, true, false, '');
                    }
                }

                $this->syslog->register('asset_export_plancheta_masiva', array(
                    'plancheta_' . $node->node_name . '.pdf'
                )); // registering log
                $this->pdf->Output('plancheta_' . $node->node_name . '.pdf', 'D');
            }
        }
    }

}
