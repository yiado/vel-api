<?php

/**
 * AssetLog
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class AssetLog extends BaseAssetLog
{
    public function preHydrate ( Doctrine_Event $event )
    {
        $data = $event->data;
        $CI = & get_instance ();
        $data[ 'asset_log_type_name' ] = $CI->translateTag ( 'Asset' , $data[ 'asset_log_type' ] );
        $event->data = $data;
    }
}
