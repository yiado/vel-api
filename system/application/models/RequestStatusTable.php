<?php

/**
 */
class RequestStatusTable extends Doctrine_Table
{

    /**
     * retrieveAll
     * 
     * Recupera los estados de las solicitudes
     */
    function retrieveAll($text_autocomplete = NULL)
    {

        $q = Doctrine_Query :: create()
                ->from('RequestStatus rs')
                ->orderBy('rs.request_status_name ASC');

        if (!is_null($text_autocomplete))
        {
            $q->where('rs.request_status_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

}
