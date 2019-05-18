<?php

/**
 * @package    Controller
 * @subpackage AssetController
 */
class AssetUchileListadoFolioController extends APP_Controller {

    function AssetUchileListadoFolioController() {
        parent::APP_Controller();
    }

    /**
     * exportPlancheta
     *
     * Exporta el listado actual de la Plancheta en formato pdf
     *
     */
    function exportListadoFolio($asset_load_id = null) {
        $data = array();

////       $asset_load_id = $this->input->post('asset_load_id');
//        $Assets = Doctrine_Core::getTable('Asset')->findBy('asset_load_id', $asset_load_id);
//
//
//        $array_br = array();
//        $cont = 0;
//        foreach ($Assets as $asset) {
//            $array_br[$cont]['asset_name'] = $asset->asset_name;
//            $array_br[$cont]['asset_num_serie_intern'] = $asset->asset_num_serie_intern;
//            $array_br[$cont]['asset_path'] = $asset->asset_path;
//
//            $cont++;
//        }
        $q = Doctrine_Query::create()
                ->from('Asset a')
                ->innerJoin('a.AssetType at')
                ->innerJoin('a.Brand ba')
                ->innerJoin('a.AssetCondition co')
                ->where('a.asset_load_id = ?', $asset_load_id)
                ->orderBy('asset_name');

        $resuls_br = $q->execute();

 
        $array_br = array();
        $cont = 0;
        foreach ($resuls_br as $asset) {
            $array_br[$cont]['asset_num_serie_intern'] = $asset->asset_num_serie_intern;
            $array_br[$cont]['asset_type_name'] = $asset->AssetType->asset_type_name;
            $array_br[$cont]['asset_name'] = $asset->asset_name;
            $array_br[$cont]['brand_name'] = $asset->Brand->brand_name;
            $array_br[$cont]['condition_name'] = $asset->AssetCondition->asset_condition_name;
            $array_br[$cont]['asset_num_serie'] = $asset->asset_num_serie;
            $array_br[$cont]['asset_description'] = $asset->asset_description;
            $array_br[$cont]['asset_num_factura'] = $asset->asset_num_factura;
            $array_br[$cont]['asset_path_3_niveles'] = $asset->asset_path_3_niveles;
            
            $assetOtherDatas = Doctrine_Core::getTable('AssetOtherDataValue')->retrieveByAssetModelo($asset->asset_id);
            if($assetOtherDatas){
              
               $asset_modelo =  $assetOtherDatas->asset_other_data_value_value;
              
            } else {
               $asset_modelo = "";
            }
            
            $array_br[$cont]['modelo'] = $asset_modelo;
            
            $nodeCodigo = Doctrine_Core::getTable('InfraOtherDataValue')->retrieveByAttributeNode($asset->node_id, 405);
            if($nodeCodigo){
              
               $valueCodigo =  $nodeCodigo->infra_other_data_value_value;
              
            } else {
               $valueCodigo = "";
            }
            
            $array_br[$cont]['encargado_sala'] = $valueCodigo;
            $cont++;
        }
       
        $data['br_list'] = $array_br;

        $AssetLoad = Doctrine_Core::getTable('AssetLoad')->findByAssetLoad($asset_load_id);

        list($anio, $mes, $dia) = explode('-', $AssetLoad->asset_load_date);

        $data['folio'] = $AssetLoad->asset_load_folio;
        $data['fecha_carga'] = $dia . '/' . $mes . '/' . $anio;
        $data['usuario_cargador'] = $AssetLoad->User->user_name;
        $data['comentario'] = $AssetLoad->asset_load_comment;
        

        $html = $this->load->view('uchilelistadofolio', $data, true);
        $this->load->library('pdf');
        $this->pdf->setPageOrientation('l'); // PDF_PAGE_ORIENTATION---> 'l' or 'p
        $this->pdf->SetFont('helvetica', '', 8);
        $dir = str_replace("\\","/",$this->config->item('temp_dir'));
        
        // add a page
        $this->pdf->AddPage();
        $this->pdf->Cell(15,0,$this->pdf->Image('style/default/images/MARCA OFICIAL DSI-04 (1).png',15,10,10),0,0,'C');
//        $this->pdf->Cell(100);
//        $this->pdf->Cell(25,10,'UNIVERSIDAD DE CHILE', 0, 0, 'C',0,'');
//        $this->pdf->Cell(43,20,'VICERRECTORÍA DE ASUNTOS ECONÓMICOS Y GESTIÓN INSTITUCIONAL', 0, 0, 'C',0,'');
//        $this->pdf->Cell(1,30,'DIRECCIÓN DE SERVICIOS E INFRAESTRUCTURA', 0, 0, 'C',0,'');
//        $this->pdf->ln(22);
        $this->pdf->writeHTML($html, true, false, true, false, '');


        $this->pdf->SetY(-25);
        // Set font
        $this->pdf->SetFont('helvetica', 'I', 8);
        // Page number
        $this->pdf->Cell(80, 0, '_______________________________', 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, '_______________________________', 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, '_______________________________', 0, 1, 'C', 0, '', 1); //EL 5TO. PARAMETRO 1 ES SALTO DE LINEA

        $this->pdf->Cell(80, 0, 'JEFE DE SERVICIO', 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, 'CONSERVADOR DE INVENTARIO', 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, 'ENCARGADO DE ACTIVO FIJO', 0, 1, 'C', 0, '', 1);//EL 5TO. PARAMETRO 1 ES SALTO DE LINEA

        $this->pdf->Cell(80, 0, $AssetLoad->asset_load_foot_signature1, 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, $AssetLoad->asset_load_foot_signature2, 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, $AssetLoad->asset_load_foot_signature3, 0, 1, 'C', 0, '', 1);//EL 5TO. PARAMETRO 1 ES SALTO DE LINEA

        $this->pdf->Cell(80, 0, '(Nombre y Firma)', 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, '(Nombre y Firma)', 0, 0, 'C', 0, '', 1);
        $this->pdf->Cell(80, 0, '(Nombre y Firma)', 0, 0, 'C', 0, '', 1);
        $this->pdf->Output('plancheta_listado_folio_'.$AssetLoad->asset_load_folio.'.pdf', 'D');

//        $this->syslog->register('asset_export_plancheta', array(
//            'plancheta_' . $data['codigo_recinto'] . '.pdf'
//        )); // registering log
    }

}
