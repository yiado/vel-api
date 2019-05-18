<?php

/**
 */
class BrandTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Retorna todas las Marcas del equipo equipo
     */
    function retrieveAll ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query :: create ()
                ->from ( 'Brand' )
                ->orderBy ( 'brand_name' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->andWhere ( 'brand_name LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }

    /**
     *
     * brandIsFK 
     * Retorna true en el caso que exista una Marca de Activo asociado a un Activo  y False en el caso contrario
     * Se puede verificar la asociacion en caso de que exista una marca asociada a un Insumo
     */
    function brandIsFK ( $brand_id )
    {

        // Esta query verifica que no haya ninguna marca asociada a un activo
        $q = Doctrine_Query::create ()
                ->from ( 'Brand b' )
                ->innerJoin ( 'b.Asset a' )
                ->where ( 'a.brand_id = ?' , $brand_id )
                ->limit ( 1 );

        $results_brand_in_asset = $q->execute ();

        // Query para verificar si ahi una marca asociada a un insumo

        /*
         * Query pendiente


          //$results_brand_in_input = $q->execute();
          //$result = $results_brand_in_asset->count() + $results_brand_in_input->count();
          return ($result == 0 ? false : true);

         */
        return ($results_brand_in_asset->count () == 0 ? false : true);
    }
    
    function assetBrandInName( $brand_name )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Brand b' )
                ->where ( 'b.brand_name = ?' , $brand_name );

        return $q->fetchOne();
    }

}
