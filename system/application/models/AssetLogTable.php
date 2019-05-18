<?php

/**
 */
class AssetLogTable extends Doctrine_Table
{

    function findByAssetId ( $asset_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetLog al' )
                ->innerJoin ( 'al.Asset a' )
                ->innerJoin ( 'al.User u' )
                ->where ( 'al.asset_id=?' , $asset_id );
        return $q->execute ();
    }

    function logMoveAsset ( $selected_asset_ids , $user_id , $asset_log_type , $asset_log_detail )
    {
        if ( is_array ( $selected_asset_ids ) )
        {
            $asset_array = explode ( "," , $selected_asset_ids );
        }
        else
        {
            $asset_array = array ( $selected_asset_ids );
        }
        foreach ( $asset_array as $asset )
        {
            $AssetLog = new AssetLog();
            $AssetLog->asset_id = $asset;
            $AssetLog->user_id = $user_id;
            $AssetLog->asset_log_type = $asset_log_type;
            $AssetLog->asset_log_detail = $asset_log_detail;
            $AssetLog->save ();
        }
    }
}
