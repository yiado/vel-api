<?php
/**
 */
class CostsTypeTable extends Doctrine_Table {
    
    
    /**
     * retrieveAllCostType
     * 
     * Recupera los tipos de costos 
     */
    function retrieveAll($text_autocomplete = NULL)
    {

        $q = Doctrine_Query :: create()
                ->from('CostsType ct')
                ->orderBy('ct.costs_type_name ASC');

        if (!is_null($text_autocomplete))
        {
            $q->where('ct.costs_type_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }
    
    /**
     *
     * checkCostTypeInCost
     * Retorna true en el caso que exista un Nombre de Costo asociado a al Costo y False en el caso contrario
     */
    function checkCostTypeInCost ( $costs_type_id )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'Costs c' )
                ->innerJoin ( 'c.CostsType ct' )
                ->where ( 'ct.costs_type_id = ?' , $costs_type_id )
                ->limit ( 1 );

        $results = $q->execute ();

        return ($results->count () == 0 ? false : true);
    }


}
