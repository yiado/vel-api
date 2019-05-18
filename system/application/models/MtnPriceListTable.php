<?php

/**
 * Modelo para la tabla MtnPriceList
 * @author manuteko
 *
 */
class MtnPriceListTable extends Doctrine_Table
{
    /*
     * Devuelve todas las listas de precios
     * @param integer provider
     */

    function retrieveAll ( $provider_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceList pl' )
                ->where ( 'pl.provider_id = ?' , $provider_id )
                ->orderBy ( 'pl.mtn_price_list_date_validity_finish DESC' );

        return $q->execute ();
    }

    function retrieveAllPrice ($maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceList pl' )
                ->leftJoin ( 'pl.Currency c' )
                ->leftJoin ( 'pl.Provider p' )               
                ->andWhere('pl.mtn_maintainer_type_id = ?', $maintainer_type)   
                ->orderBy ( 'pl.mtn_price_list_date_validity_finish DESC' );
        
        
        return $q->execute ();
    }

}
