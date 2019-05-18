<?php

/**
 */
class AssetDocumentTable extends Doctrine_Table
{

    /**
     * assetRecoveryDocument 
     * 
     * retorna el documento asociado al actual activo por id
     * 
     * @param int asset_document_id
     */
    function retrieveByAsset ( $asset_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetDocument ad' )
                ->innerJoin ( 'ad.Asset aa' )
                ->innerJoin ( 'ad.User au' )
                ->where ( 'asset_id = ?' , $asset_id )
                ->orderBy ( 'asset_document_filename' );
        return $q->execute ();
    }
}
