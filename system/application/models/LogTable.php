<?php

/**
 */
class LogTable extends Doctrine_Table
{

    /**
     * Retorna los log del sistema;
     *   - Permite buscar por autocompletado de texto
     *
     * @param string $text_autocomplete
     */
    function retrieveAll( $filters)
    {

        $q = Doctrine_Query::create()
                ->from('Log l')
                ->leftJoin('l.LogType lt')
                ->leftJoin('l.User u')
              
                ->orderBy('u.user_name ASC');

       
        foreach ($filters as $field => $value)
        {

            if (!is_null($value))
            {
                $q->andWhere($field, $value);
            }
        }

        return $q->execute();
    }
    
    function retrieveAllExport( $filters)
    {

        $q = Doctrine_Query::create()
                ->from('Log l')
                ->leftJoin('l.LogType lt')
                ->leftJoin('l.User u')
                ->leftJoin('l.LogDetail ld')
                
                ->orderBy('u.user_name ASC');

       
        foreach ($filters as $field => $value)
        {

            if (!is_null($value))
            {
                $q->andWhere($field, $value);
            }
        }

        return $q->execute( array ( ) , Doctrine_Core :: HYDRATE_SCALAR );
    }

}
