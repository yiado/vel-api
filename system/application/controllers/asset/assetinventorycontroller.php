<?php

/**
 * @package    Controller
 * @subpackage AssetTypeController
 */
class AssetInventoryController extends APP_Controller
{
    function AssetInventoryController ()
    {

        parent::APP_Controller ();
    }

    function get ()
    {
        $assetInventory = Doctrine_Core::getTable ( 'AssetInventory' )->findByUsuario ( $this->auth->get_user_data ( 'user_id' ) );

        if ( $assetInventory->count () )
        {
            echo '({"total":"' . $assetInventory->count () . '", "results":' . $this->json->encode ( $assetInventory->toArray () ) . '})';
        }
        else
        {
            echo '({"total":"0", "results":[]})';
        }
    }

    function upload ()
    {
        $file = $_FILES[ 'inventory_file' ][ 'tmp_name' ];

        switch ( $this->input->post ( 'output_type' ) )
        {
            case 'r':
                $this->uploadDiff ( $file );
                break;

            case 'm':
                $this->uploadMove ( $file );
                break;
        }
    }

    function uploadDiff ( $file )
    {
        $this->load->helper ( 'file' );
        $this->load->library ( 'PHPExcel' );

        $array_node = array ( );
        $array_asset_exist = array ( );
        
        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( $this->translateTag ( 'General' , 'movements' ) );

        $sheet->setCellValue ( 'A1' , $this->translateTag ( 'Asset' , 'name_asset' ) )
                ->setCellValue ( 'B1' , $this->translateTag ( 'Asset' , 'internal_number' ) )
                ->setCellValue ( 'C1' , $this->translateTag ( 'Asset' , 'original_location' ) )
                ->setCellValue ( 'D1' , $this->translateTag ( 'Asset' , 'location_transfer' ) );

        $rcount = 1;
        
        $lines = file ( $file ); // gets file in array using new lines character
        foreach ( $lines as $line )
        {
            
            $ids = explode ( ',' , $line );
            $asset = Doctrine_Core::getTable ( 'Asset' )->find ( $ids[ 1 ] );
            $node = Doctrine_Core::getTable ( 'Node' )->find ($ids[0]);
            
            if (!isset($asset->asset_id)) { // asset not registered
            	
                $rcount ++;
                $sheet->setCellValueExplicit ( 'A' . $rcount , null )
                        ->setCellValueExplicit ( 'B' . $rcount , $ids[1] );
                        
                if (isset($node->node_id)) {
	                $sheet->setCellValueExplicit ( 'C' . $rcount , $node->getPath());
                } else {
                	$sheet->setCellValueExplicit ( 'C' . $rcount , null);
                }
                
                $sheet->setCellValueExplicit ( 'D' . $rcount , $this->translateTag('Asset', 'not_registered_in_active_igeo'));
                        
            	continue;
            	
            }
            
            if (!isset($node->node_id)) { // node not registered
            	
                $rcount ++;
                
                $sheet->setCellValueExplicit ( 'A' . $rcount , $ids[1] );
                if (isset($asset->asset_id)) {
	                $sheet->setCellValueExplicit ( 'B' . $rcount , $asset->asset_num_serie_intern );
                } else {
	                $sheet->setCellValueExplicit ( 'B' . $rcount , null );
                }
                
            	$sheet->setCellValueExplicit ( 'C' . $rcount , null);
                
                $sheet->setCellValueExplicit ( 'D' . $rcount , $this->translateTag('Asset', 'node_not_registered'));
                        
            	continue;
            	
            }
            

            if ( ! array_key_exists ( $ids[ 0 ] , $array_node ) )
            {
                $array_node[ $ids[ 0 ] ] = array ( );
                $array_asset_exist[ $ids[ 0 ] ] = array ( );
            }

            if ( $asset->node_id != $ids[ 0 ] )
            {
                $assetInventory = new AssetInventory();
                $assetInventory->node_id = $ids[ 0 ];
                $assetInventory->asset_id = $asset->asset_id;
                $assetInventory->user_id = $this->auth->get_user_data ( 'user_id' );
                $assetInventory->save ();

                $array_node[ $ids[ 0 ] ][ ] = $assetInventory;
            }
            else if ( $asset->node_id == $ids[ 0 ] )
            {
                $array_asset_exist[ $ids[ 0 ] ][ ] = $asset->asset_id;
                
                // update last inventory date field
                $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
                $asset->save();
                
            }
        }

        foreach ( $array_node as $node_id => $node )
        {
            foreach ( $node as $inventory )
            {
                $asset_move_node = Doctrine_Core::getTable ( 'Node' )->find ( $inventory->node_id )->getPath ();
                $asset = Doctrine_Core::getTable ( 'Asset' )->find ( $inventory->asset_id );
                $rcount ++;
                $sheet->setCellValueExplicit ( 'A' . $rcount , $asset->asset_name )
                        ->setCellValueExplicit ( 'B' . $rcount , $asset->asset_num_serie_intern , PHPExcel_Cell_DataType::TYPE_STRING )
                        ->setCellValueExplicit ( 'C' . $rcount , Doctrine_Core::getTable ( 'Node' )->find ( $asset->node_id )->getPath () )
                        ->setCellValue ( 'D' . $rcount , $asset_move_node );
            }

            foreach ( Doctrine_Core::getTable ( 'Asset' )->getAssetDiff ( $node_id , $array_asset_exist[ $node_id ] ) as $asset )
            {
                $rcount ++;
                $sheet->setCellValueExplicit ( 'A' . $rcount , $asset->asset_name )
                        ->setCellValueExplicit ( 'B' . $rcount , $asset->asset_num_serie_intern , PHPExcel_Cell_DataType::TYPE_STRING )
                        ->setCellValueExplicit ( 'C' . $rcount , Doctrine_Core::getTable ( 'Node' )->find ( $asset->node_id )->getPath () )
                        ->setCellValue ( 'D' . $rcount , $this->translateTag('Asset', 'it_has_not_been_moved_from_Hometown') );
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
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:D' . $rcount )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $report_name = 'informe_inventario_' . date ( 'Y-m-d H:i:s' );
        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $report_name . '.xls' ) );
        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function uploadMove ( $file )
    {
        $this->load->helper ( 'file' );
        $this->load->library ( 'PHPExcel' );
        
        $array_not_found_assets = array();

        $sheet = $this->phpexcel->setActiveSheetIndex ( 0 );
        $sheet->setTitle ( $this->translateTag ( 'General' , 'movements' ) );
		
        $sheet->setCellValue ( 'A1' , $this->translateTag ( 'General' , 'movements' ) )
                ->setCellValue ( 'B1' , $this->translateTag ( 'Asset' , 'internal_number' ) )
                ->setCellValue ( 'C1' , $this->translateTag ( 'Asset' , 'original_location' ) )
                ->setCellValue ( 'D1' , $this->translateTag ( 'Asset' , 'location_transfer' ) );

        $rcount = 1;
        $lines = file ( $file ); // gets file in array using new lines character
        foreach ( $lines as $line )
        {
            $ids = explode ( ',' , $line );

            $asset = Doctrine_Core::getTable ( 'Asset' )->find ( $ids[ 1 ] );
            $node = Doctrine_Core::getTable ( 'Node' )->find ($ids[0]);
            
            if (!isset($asset->asset_id)) { // asset not registered
            	
                $rcount ++;
                $sheet->setCellValueExplicit ( 'A' . $rcount , $ids[1] )
                        ->setCellValueExplicit ( 'B' . $rcount , null );
                        
                if (isset($node->node_id)) {
	                $sheet->setCellValueExplicit ( 'C' . $rcount , $node->getPath());
                } else {
                	$sheet->setCellValueExplicit ( 'C' . $rcount , null);
                }
                
                $sheet->setCellValueExplicit ( 'D' . $rcount , $this->translateTag('Asset', 'not_registered_in_active_igeo'));
                        
            	continue;
            	
            }
            
            if (!isset($node->node_id)) { // node not registered
            	
                $rcount ++;
                
                $sheet->setCellValueExplicit ( 'A' . $rcount , $ids[1] );
                if (isset($asset->asset_id)) {
	                $sheet->setCellValueExplicit ( 'B' . $rcount , $asset->asset_num_serie_intern );
                } else {
	                $sheet->setCellValueExplicit ( 'B' . $rcount , null );
                }
                
            	$sheet->setCellValueExplicit ( 'C' . $rcount , null);
                
                $sheet->setCellValueExplicit ( 'D' . $rcount ,$this->translateTag('Asset', 'node_not_registered'));
                        
            	continue;
            	
            }
            
            // update last inventory date field
            $asset->asset_last_inventory = $this->input->post('asset_inventory_date');
            $asset->save();

            if ( $asset->node_id != $ids[ 0 ] )
            {
                $asset_move_node = Doctrine_Core::getTable ( 'Node' )->find ( $ids[ 0 ] )->getPath ();

                $rcount ++;
                $sheet->setCellValueExplicit ( 'A' . $rcount , $asset->asset_name )
                        ->setCellValueExplicit ( 'B' . $rcount , $asset->asset_num_serie_intern , PHPExcel_Cell_DataType::TYPE_STRING )
                        ->setCellValueExplicit ( 'C' . $rcount , Doctrine_Core::getTable ( 'Node' )->find ( $asset->node_id )->getPath () )
                        ->setCellValue ( 'D' . $rcount , $asset_move_node );


                $asset->node_id = $ids[ 0 ];
                $asset->save ();

                Doctrine_Core::getTable ( 'AssetLog' )->logMoveAsset ( $asset->asset_id , $this->session->userdata ( 'user_id' ) , 'asset_log_move' , $asset_move_node );
                
            } else if ( $asset->node_id == $ids[ 0 ] ) {
            	
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
            'type' => PHPExcel_Style_Fill::FILL_SOLID ,
            'color' => array (
                'rgb' => 'd9e5f4'
            )
        ) );

        $sheet->getStyle ( 'A1:D' . $rcount )->getBorders ()->applyFromArray ( array (
            'allborders' => array (
                'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array (
                    'rgb' => '808080'
                )
            )
        ) );

        $report_name = 'informe_inventario_' . date ( 'Y-m-d H:i:s' );
        $objWriter = PHPExcel_IOFactory::createWriter ( $this->phpexcel , 'Excel5' );
        $objWriter->save ( $this->app->getTempFileDir ( $report_name . '.xls' ) );

        echo '{"success": true, "file": "' . $report_name . '.xls"}';
    }

    function move ()
    {
        $asset_inventoy_ids = $this->input->post ( 'asset_inventoy_id' );

        foreach ( explode ( ',' , $asset_inventoy_ids ) as $asset_inventoy_id )
        {
            $assetInventory = Doctrine_Core::getTable ( 'AssetInventory' )->find ( $asset_inventoy_id );
            $asset = Doctrine_Core::getTable ( 'Asset' )->find ( $assetInventory->asset_id );
            $asset->node_id = $assetInventory->node_id;
            $asset->save ();

            $asset_log_detail = Doctrine_Core::getTable ( 'Node' )->find ( $assetInventory->node_id )->getPath ();
            Doctrine_Core::getTable ( 'AssetLog' )->logMoveAsset ( $assetInventory->asset_id , $this->session->userdata ( 'user_id' ) , 'asset_log_move' , $asset_log_detail );

            $assetInventory->delete ();
        }
        $json_data = $this->json->encode ( array ( 'success' => true ) );
        echo $json_data;
    }

    function returnOrigen ()
    {
        $asset_inventoy_ids = $this->input->post ( 'asset_inventoy_id' );

        foreach ( explode ( ',' , $asset_inventoy_ids ) as $asset_inventoy_id )
        {
            $assetInventory = Doctrine_Core::getTable ( 'AssetInventory' )->find ( $asset_inventoy_id );
            $assetInventory->delete ();
        }
        $json_data = $this->json->encode ( array ( 'success' => true ) );
        echo $json_data;
    }

}