<?php

/**
 */
class MtnComponentTable extends Doctrine_Table
{
    /*
     * Devuelve todos los insumos de la base de datos.
     * @param array $filters
     *  
     */

    function retrieveAll ( $mtn_price_list_id = NULL, $text_autocomplete = NULL ,$maintainer_type)
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnComponent co' )
                ->leftJoin ( 'co.Brand ba' )
                ->innerJoin ( 'co.MeasureUnit mu' )
                ->innerJoin ( 'co.MtnComponentType ct' )
                ->orderBy ( 'co.mtn_component_name ASC' );

        if ( ! is_null ( $mtn_price_list_id ) )
        {
            $q->where ( 'co.mtn_component_id NOT IN (SELECT mplc.mtn_component_id FROM MtnPriceListComponent mplc WHERE mplc.mtn_price_list_id = ?)' , $mtn_price_list_id );
        }
        
        if (!is_null($text_autocomplete))
        {
            $q->where('co.mtn_component_name LIKE ?', $text_autocomplete . '%');
        }
         $q->andWhere('ct.mtn_maintainer_type_id = ?',$maintainer_type);

        return $q->execute ();
    }

    /**
     * Retorna la info de la tupla del componente
     * 
     * @param integer $provider_id
     * @return 1 row
     */
    function retrieveById ( $mtn_component_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnComponent co' )
                ->innerJoin ( 'co.MtnComponentType ct' )
                ->where ( 'co.mtn_component_id = ?' , $mtn_component_id );

        return $q->fetchOne ();
    }

    /**
     *
     * Retorna true en el caso que exista una lista de precio o una ot asociado al componente y false en el caso contrario
     * @param integer $mtn_component_id
     * @return boolean true|false
     */
    function checkComponentIdInPriceList ( $mtn_component_id )
    {

        //Componente asociado a un seguro
        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceListComponent mplc' )
                ->where ( 'mplc.mtn_component_id  = ?' , $mtn_component_id )
                ->limit ( 1 );

        $results = $q->execute ();

        $priceListComponent = ($results->count () == 0 ? false : true);

        //Componente asociado a una ot
        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderTaskComponent mwotc' )
                ->where ( 'mwotc.mtn_component_id = ?' , $mtn_component_id )
                ->limit ( 1 );

        $results = $q->execute ();

        $mtnWorkOrderComponent = ($results->count () == 0 ? false : true);


        return ( ( $priceListComponent == false && $mtnWorkOrderComponent == false ) ? false : true);
    }

}
