<?php

/**
 * 
 * @author manuteko
 *
 */
class MtnPriceListComponentTable extends Doctrine_Table
{
    /*
     * Devuelve la lista de precios vigente para el proveedor
     * 
     * @param integer $provider_id
     * @param string $text_autocomplete (opcional)
     */

    function retrieveCurrentPriceList ( $provider_id , $text_autocomplete = NULL )
    {

        $to_day = date ( 'Y-m-d' );

        $q = Doctrine_Query::create ()
                ->from ( 'MtnComponent mc' )
                ->innerJoin ( 'mc.MtnComponentType ct' )
                ->leftJoin ( 'mc.MtnPriceListComponent plc' )
                ->leftJoin ( 'plc.MtnPriceList pl WITH (pl.mtn_price_list_date_validity_start <= "' . $to_day . '" AND pl.mtn_price_list_date_validity_finish >= "' . $to_day . '")' )
                ->where ( 'pl.provider_id = ?' , $provider_id )
                ->orWhere ( 'pl.provider_id IS NULL' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->andWhere ( 'mc.mtn_component_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }
    function retrieveCurrentPriceListByNode ( $mtn_component_type_id , $provider_id , $text_autocomplete = NULL )
    {

        $to_day = date ( 'Y-m-d' );

        $q = Doctrine_Query::create ()
                ->from ( 'MtnComponent mc' )
                ->innerJoin ( 'mc.MtnComponentType ct' )
                ->innerJoin ( 'mc.MeasureUnit mu' )
                ->leftJoin ( 'mc.MtnPriceListComponent plc' )
                ->leftJoin ( 'plc.MtnPriceList pl WITH (pl.mtn_price_list_date_validity_start <= "' . $to_day . '" AND pl.mtn_price_list_date_validity_finish >= "' . $to_day . '")' )
                
                ->where ( 'pl.provider_id = ?' , $provider_id )
                ->andWhere ( 'mc.mtn_component_type_id = ?' , $mtn_component_type_id  )
                ->orWhere ( 'pl.provider_id IS NULL' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->andWhere ( 'mc.mtn_component_name LIKE ?' , $text_autocomplete . '%' );
        }
        $q->andWhere ( 'pl.mtn_maintainer_type_id = ?' , 2 );
        return $q->execute ();
    }

    /*
     * Devuelve todos los precios de los componentes.
     *  
     */

    function retrieveAll ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceListComponent mplc' )
                ->innerJoin ( 'mplc.MtnComponent mc' )
                ->innerJoin ( 'mplc.MtnPriceList mpl' );


        return $q->execute ();
    }
    
    function retrieveAllNode ()
    {

        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceListComponent mplc' )
                ->innerJoin ( 'mplc.MtnComponent mc' )
                ->innerJoin ( 'mplc.MtnPriceList mpl' )
                ->where ( 'mpl.mtn_maintainer_type_id = ?' , 2 );


        return $q->execute ();
    }

    /**
     *
     * Retorna true en el caso que exista una lista de precio o una ot asociado al componente y false en el caso contrario
     * @param integer $mtn_price_list_component_id
     * @return boolean true|false
     */
    function checkComponentIdInPriceList ( $mtn_price_list_component_id )
    {

        //Componente asociado 
        $q = Doctrine_Query::create ()
                ->from ( 'MtnWorkOrderTaskComponent mwotc' )
                ->where ( 'mwotc.mtn_price_list_component_id  = ?' , $mtn_price_list_component_id )
                ->limit ( 1 );

        $results = $q->execute ();

        $priceListComponent = ($results->count () == 0 ? false : true);


        return $priceListComponent;
    }

    /**
     *
     * Retorna true en el caso que exista una lista de precio o una ot asociado al componente y false en el caso contrario
     * @param integer $mtn_price_list_id
     * @return Devuelve todos los precios de los componentes.
     */
    function checkComponentByIdPriceList ( $mtn_price_list_id )
    {

        //Componente asociado 
        $q = Doctrine_Query::create ()
                ->from ( 'MtnPriceListComponent mplc' )
                ->leftJoin ( 'mplc.MtnComponent mc' )
                ->leftJoin ( 'mc.MeasureUnit mu' )
                ->where ( 'mplc.mtn_price_list_id  = ?' , $mtn_price_list_id )
                ->orderBy ( 'mc.mtn_component_name ASC' );


        return $q->execute ();
    }

}
