<?php

/**
 */
class UserGroupTable extends Doctrine_Table
{

    /**
     * Devuelve la información de un grupo de usuarios
     * @param integer $user_group_id
     * 
     */
    function retrieveById($user_group_id)
    {

        $q = Doctrine_Query::create()
                ->from('UserGroup ug')
                ->where('ug.user_group_id = ?', $user_group_id);

        return $q->fetchOne();
    }
    
    function retrieveByIdUser($user_group_id)
    {

        $q = Doctrine_Query::create()
                ->from('UserGroupUser ugu')
                ->where('ugu.user_group_id = ?', $user_group_id);

        return $q->fetchOne();
    }

    /**
     * Retorna todos los grupos del sistema
     * Si user_id es NULL, retorna todos los grupos del sistema,de lo contrario, retorna solo los grupos a los que no pertenece al usuario.
     * @param integer $user_id
     */
    function retrieveAll($user_id = NULL, $text_autocomplete = NULL)
    {

        $q = Doctrine_Query::create()
                ->from('UserGroup ug')
                ->orderBy('ug.user_group_name ASC');

        if (!is_null($user_id))
        {
            $q->where('ug.user_group_id NOT IN (SELECT ugu2.user_group_id FROM UserGroupUser ugu2  WHERE ugu2.user_id = ?)', $user_id);
        }

        if (!is_null($text_autocomplete))
        {
            $q->where('ug.user_group_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

}
