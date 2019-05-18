<?php

/**
 * @package    Model
 * @subpackage User
 */
class User extends BaseUser
{

    public function preHydrate ( Doctrine_Event $event )
    {

        $data = $event->data;

        $user_type_names = array (
            'A' => "Administrador" ,
            'N' => "Restringido" ,
            'P' => "Proveedor" ,
            'S' => "Sistema"
        );

        $data[ 'user_type_name' ] = $user_type_names[ $data[ 'user_type' ] ];
        $event->data = $data;
    }

    function getUserActions ()
    {

        $q = Doctrine_Query::create ()
                ->select ( 'ma.*' )
                ->from ( 'ModuleAction ma' );

        if ( $this->user_type != 'A' )
        {

            $q->innerJoin ( 'ma.UserGroupAction uga' );
            $q->innerJoin ( 'uga.UserGroup ug' );
            $q->innerJoin ( 'ug.UserGroupUser ugu' );

            $q->where ( 'ugu.user_id = ?' , $this->user_id );
        }

        return $q->execute ();
    }

    function getUserModules ()
    {

        $q = Doctrine_Query::create ()
                ->select ( 'm.*' )
                ->from ( 'Module m' )
                ->where ( 'm.module_position IS NOT ?' , NULL )
//                ->andWhere('m.module_id <> 11')
                ->groupBy ( 'm.module_id' )
                ->orderBy ( 'module_position' );

        if ( $this->user_type != 'A' )
        {

            $q->innerJoin ( 'm.ModuleAction ma' );
            $q->innerJoin ( 'ma.UserGroupAction uga' );
            $q->innerJoin ( 'uga.UserGroup ug' );
            $q->innerJoin ( 'ug.UserGroupUser ugu' );

            $q->where ( 'ugu.user_id = ?' , $this->user_id );
        }

        return $q->execute ();
    }
    
        function getUserModulesAdmin()
    {

        $q = Doctrine_Query::create ()
                ->select ( 'm.*' )
                ->from ( 'Module m' )
                ->where ( 'm.module_position IS NOT ?' , NULL )
                ->andWhere('m.module_id <> 11')
                ->groupBy ( 'm.module_id' )
                ->orderBy ( 'module_position' );

        if ( $this->user_type != 'A' )
        {

            $q->innerJoin ( 'm.ModuleAction ma' );
            $q->innerJoin ( 'ma.UserGroupAction uga' );
            $q->innerJoin ( 'uga.UserGroup ug' );
            $q->innerJoin ( 'ug.UserGroupUser ugu' );

            $q->where ( 'ugu.user_id = ?' , $this->user_id );
        }

        return $q->execute ();
    }

}
