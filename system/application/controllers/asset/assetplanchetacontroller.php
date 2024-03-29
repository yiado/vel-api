<?php

/**
 * @package    Controller
 * @subpackage AssetController
 */
class AssetPlanchetaController extends APP_Controller {

    function AssetPlanchetaController() {
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
        $Node = Doctrine_Core::getTable('Node')->find($node_id);
        $users = Doctrine_Core :: getTable('User')->find($this->auth->get_user_data('user_id'));
        $array_br = array();
        $cont = 0;
        foreach ($resuls_br as $asset) {
            $array_br[$cont]['asset_num_serie_intern'] = $asset->asset_num_serie_intern;
            $array_br[$cont]['asset_type_name'] = $asset->AssetType->asset_type_name;
            $array_br[$cont]['asset_name'] = $asset->asset_name;
            $array_br[$cont]['brand_name'] = $asset->Brand->brand_name;
            $array_br[$cont]['asset_num_serie'] = $asset->asset_num_serie;
            $array_br[$cont]['asset_description'] = $asset->asset_description;
            $cont++;
        }

        $data['br_list'] = $array_br;
        $data['organismo'] = 'Dibam'; //@Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 6)->infra_other_data_option_id)->infra_other_data_option_name;
        $data['departamento'] = 'Departamento'; // @Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 8)->infra_other_data_option_id)->infra_other_data_option_name;
        $data['unidad'] = 'Unidad Demo'; //@Doctrine_Core::getTable('InfraOtherDataOption')->find(Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 10)->infra_other_data_option_id)->infra_other_data_option_name;
        $data['codigo_recinto'] = $resuls_br[0]['node_id']; //@Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 46)->infra_other_data_value_value;
        $data['codigo_subrecinto'] = 'Codigo Sub Recinto'; // @Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 3)->infra_other_data_value_value;
        $data['nombre_recinto'] = $Node->node_name; //@Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 2)->infra_other_data_value_value;


        $data['usuario'] = $users->user_name; //@Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($node_id, 398)->infra_other_data_value_value;
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

        $html = $this->load->view('plancheta', $data, true);
        $this->load->library('pdf');
        $this->load->library('zend', array('Zend/Barcode'));


        if ($data['codigo_recinto'] != null) {
            $imageResource = Zend_Barcode::draw('code128', 'image', array('text' => $data['codigo_recinto'], 'drawText' => true, 'barThickWidth' => 1, 'barHeight' => 25), array());
        } else {
            $imageResource = Zend_Barcode::draw('code128', 'image', array('text' => '0', 'drawText' => true, 'barThickWidth' => 1, 'barHeight' => 25), array());
        }

        imagejpeg($imageResource, 'temp/barcode_plancheta.jpg', 100);

        $this->pdf->SetFont('helvetica', '', 8);

        // add a page
        $this->pdf->AddPage();

        $this->pdf->writeHTML($html, true, false, true, false, '');

        $this->pdf->Output('plancheta_' . $data['codigo_recinto'] . '.pdf', 'D');

        $this->syslog->register('asset_export_plancheta', array(
            'plancheta_' . $data['codigo_recinto'] . '.pdf'
        )); // registering log
    }

}
