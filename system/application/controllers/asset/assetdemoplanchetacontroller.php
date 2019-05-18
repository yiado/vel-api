<?php

/**
 * @package    Controller
 * @subpackage AssetController
 */
class AssetDemoPlanchetaController extends APP_Controller
{
    function AssetDemoPlanchetaController ()
    {
        parent::APP_Controller ();
    }

    /**
     * exportPlancheta
     *
     * Exporta el listado actual de la Plancheta en formato pdf
     *
     */
    function exportPlancheta ( $node_id = null )
    {
        $data = array ( );

        $q = Doctrine_Query::create ()
                ->from ( 'Asset a' )
                ->innerJoin ( 'a.AssetType at' )
                ->innerJoin ( 'a.Brand ba' )
                ->where ( 'a.node_id = ?' , $node_id )
                ->orderBy ( 'asset_name' );

        $resuls_af = $q->execute ();

        $array_af = array ( );
        $cont = 0;
        foreach ( $resuls_af as $asset )
        {
            $array_af[ $cont ][ 'asset_num_serie_intern' ] = $asset->asset_num_serie_intern;
            $array_af[ $cont ][ 'asset_name' ] = $asset->asset_name;
            $array_af[ $cont ][ 'asset_type_name' ] = $asset->AssetType->asset_type_name;
            $array_af[ $cont ][ 'brand_name' ] = $asset->Brand->brand_name;

            $cont ++;
        }

        $data[ 'asset_list' ] = $array_af;
        $data[ 'nombre_recinto' ] = @Doctrine_Core::getTable ( 'Node' )->find ( $node_id )->node_name;
        $html = $this->load->view ( 'demoplancheta' , $data , true );

        $this->load->library ( 'pdf' );
        $this->load->library ( 'zend' , array ( 'Zend/Barcode' ) );

        $imageResource = Zend_Barcode::draw ( 'code39' , 'image' , array ( 'text' => $node_id , 'drawText' => true , 'barThickWidth' => 3 , 'barHeight' => 25 ) , array ( ) );

        imagejpeg ( $imageResource , 'temp/barcode_node.jpg' , 100 );

        $this->pdf->SetFont ( 'helvetica' , '' , 8 );

        // add a page
        $this->pdf->AddPage ();

        $this->pdf->writeHTML ( $html , true , false , true , false , '' );

        $this->pdf->Output ( 'plancheta_' . $data[ 'nombre_recinto' ] . '.pdf' , 'D' );
    }
    
    function exportPlanchetaFonasa ( $node_id = null )
    {
        $data = array ( );

        $q = Doctrine_Query::create ()
                ->from ( 'Asset a' )
                ->innerJoin ( 'a.AssetType at' )
                ->innerJoin ( 'a.Brand ba' )
                ->where ( 'a.node_id = ?' , $node_id )
                ->orderBy ( 'asset_name' );

        $resuls_af = $q->execute ();

        $array_af = array ( );
        $cont = 0;
        foreach ( $resuls_af as $asset )
        {
            $value = Doctrine_Core::getTable ( 'AssetOtherDataValue' )->retrieveByAttributeAsset ( $asset->asset_id , 38 );
            $array_af[ $cont ][ 'asset_num_serie_intern' ] = $asset->asset_num_serie_intern;
            $array_af[ $cont ][ 'asset_name' ] = $asset->asset_name;
            $array_af[ $cont ][ 'asset_type_name' ] = $asset->AssetType->asset_type_name;
            $array_af[ $cont ][ 'brand_name' ] = $asset->Brand->brand_name;
            $array_af[ $cont ][ 'tipologia' ] = ($value) ? $value->asset_other_data_value_value : NULL;
            

            $cont ++;
        }

        $data[ 'asset_list' ] = $array_af;
        $data[ 'nombre_recinto' ] = @Doctrine_Core::getTable ( 'Node' )->find ( $node_id )->node_name;
        $html = $this->load->view ( 'demoplanchetafonasa' , $data , true );

        $this->load->library ( 'pdf' );
        $this->load->library ( 'zend' , array ( 'Zend/Barcode' ) );

        $imageResource = Zend_Barcode::draw ( 'code39' , 'image' , array ( 'text' => $node_id , 'drawText' => true , 'barThickWidth' => 3 , 'barHeight' => 25 ) , array ( ) );

        imagejpeg ( $imageResource , 'temp/barcode_node.jpg' , 100 );

        $this->pdf->SetFont ( 'helvetica' , '' , 8 );

        // add a page
        $this->pdf->AddPage ();

        $this->pdf->writeHTML ( $html , true , false , true , false , '' );

        $this->pdf->Output ( 'plancheta_' . $data[ 'nombre_recinto' ] . '.pdf' , 'D' );
    }

}
