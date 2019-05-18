<?php

/**
 */
class LogTypeTable extends Doctrine_Table
{
     /**
     * Retorna todos los log_type_description
     * 
     * @param string $text_autocomplete
     */
    function retrieveAll ( $text_autocomplete = NULL )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'LogType lt' )
                ->orderBy ( 'lt.log_type_description ASC' );

        if ( ! is_null ( $text_autocomplete ) )
        {
            $q->where ( 'lt.log_type_description LIKE ?' , $text_autocomplete . '%' );
        }

        return $q->execute ();
    }
}
