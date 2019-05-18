<?php

/**
 */
class UserGroupActionTable extends Doctrine_Table
{

    /**
     * Retorna los permisos del grupo en un modulo
     * @param integer $user_group_id
     * @param integer $module_id
     * @param integer $language_id
     */
    function permissionsGroupInModule ( $user_group_id , $module_id , $language_id = 1 )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'UserGroupAction uga' )
                ->innerJoin ( 'uga.ModuleAction ma' )
                ->innerJoin ( 'ma.LanguageTag lt' )
                ->where ( 'uga.user_group_id = ?' , $user_group_id )
                ->andWhere ( 'lt.module_id = ?' , $module_id )
                ->andWhere ( 'lt.language_id = ?' , $language_id )
                ->orderBy ( 'ma.module_action_name ASC' );

        return $q->execute ();
    }

    /**
     * Elimina los permisos asociados al grupo
     * @param integer $user_group_id
     * @param integer $module_id
     */
    function deleteCurrentPermissionsGroup ( $user_group_id , $module_id )
    {

        $q = Doctrine_Query::create ()
                ->delete ( 'UserGroupAction uga' )
                ->where ( 'uga.module_action_id IN (SELECT ma.module_action_id FROM ModuleAction ma INNER JOIN ma.LanguageTag lt INNER JOIN WHERE lt.module_id = ?)' , $module_id )
                ->andWhere ( 'uga.user_group_id = ?' , $user_group_id );

        return $q->execute ();
    }

}
