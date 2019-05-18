<?php

/**
 *
 * @author manuteko
 *
 */
class ModuleActionTable extends Doctrine_Table
{

    /**
     * Retorna las acciones del modulo
     * Si se envia el parametro $user_group_module, solo devuelve las actions que no estan asociados al grupo.
     * @param integer $module_id
     * @param integer $user_group_id
     * @param integer $language_id
     * @param integer $show_public_actions
     */
    function retrieveByModule ( $module_id , $user_group_id = NULL , $language_id = 1 , $show_public_actions = false )
    {

        $q = Doctrine_Query::create ()
                ->from ( 'ModuleAction ma' )
                ->innerJoin ( 'ma.LanguageTag lt' )
                ->where ( 'lt.module_id = ?' , $module_id )
                ->andWhere ( 'lt.language_id = ?' , $language_id )
                ->orderBy ( 'ma.module_action_uri ASC' );

        if ( ! is_null ( $user_group_id ) )
        {

            $q->andWhere ( 'ma.module_action_id NOT IN (SELECT uga.module_action_id FROM UserGroupAction uga INNER JOIN uga.ModuleAction ma2 WHERE uga.user_group_id = ?)' , $user_group_id );
        }

        if ( $show_public_actions === true )
        {

            $q->andWhere ( 'ma.module_action_is_public = ?' , 1 );
        }
        else
        {
            $q->andWhere ( 'ma.module_action_is_public = ?' , 0 );
        }

        return $q->execute ();
    }
    
    
      function retrieveModule($module_id, $user_group_id = NULL, $module_action_id,$language_id = 1) {

        $q = Doctrine_Query::create()
                ->from('ModuleAction ma')
                ->innerJoin('ma.LanguageTag lt')
                ->where('lt.module_id = ?', $module_id)
                ->andWhere('lt.language_id = ?', $language_id)
                ->andWhere('ma.module_action_id = ?', $module_action_id)
                ->orderBy('ma.module_action_uri ASC');

       
        return $q->fetchOne();
    }

    /**
     *
     * Retorna los permisos publicos del sistema
     * @param integer $usuarioId
     * @return array
     */
    function getPublicActions ()
    {
        $q = Doctrine_Query::Create ()
                ->from ( 'ModuleAction ma' )
                ->where ( 'ma.module_action_is_public = ?' , 1 );

        $result = $q->execute ();

        //Pasamos los actions a la forma de array asociativo; 'module' 'controller' 'action' = flag
        $public_actions = array ( );

        foreach ( $result as $action )
        {

            $uri = explode ( '/' , $action->module_action_uri );
            $public_actions[ $uri[ 0 ] ][ $uri[ 1 ] ][ $uri[ 2 ] ] = true;
        }

        return $public_actions;
    }

}
