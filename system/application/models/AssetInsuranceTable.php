<?php

/**
 * @package Model
 * @subpackage AssetInsuranceTable
 */
class AssetInsuranceTable extends Doctrine_Table
{

    /**
     * findByAsset
     *
     * Retorna las garant�as de determinado equipo
     *
     * @param int $asset_id
     */
    function findByAsset ( $asset_id )
    {
        $q = Doctrine_Query::create ()
                ->from ( 'AssetInsurance ai' )
                ->innerJoin ( 'ai.Provider p' )
                ->where ( 'asset_id = ?' , $asset_id )
                ->orderBy ( 'asset_insurance_id ASC' );
        return $q->execute ();
    }
}