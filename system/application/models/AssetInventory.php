<?php

/**
 * AssetInventory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class AssetInventory extends BaseAssetInventory
{
    public function preHydrate ( Doctrine_Event $event )
    {
        $data = $event->data;
        $asset = Doctrine_Core::getTable ( 'asset' )->find ( $data[ 'asset_id' ] );
        $data[ 'node_path' ] = Doctrine_Core::getTable ( 'Node' )->find ( $data[ 'node_id' ] )->getPath ();
        $data[ 'asset_node_path' ] = Doctrine_Core::getTable ( 'Node' )->find ( $asset->node_id )->getPath ();
        $event->data = $data;
    }
}