<?php

/**
 */
class UserGroupNodeTable extends Doctrine_Table
{

    /**
     * Elimina los accesos a los nodos asociados a un grupo
     * @param integer $user_group_id
     */
    function deleteCurrentAccess ( $user_group_id )
    {

        $q = Doctrine_Query::create ()
                ->delete ( 'UserGroupNode ugn' )
                ->where ( 'ugn.user_group_id = ?' , $user_group_id );

        return $q->execute ();
    }
    
    function findOneByUserGroupIdAndNodeId ( $user_group_id, $node_id ) {
    	
        $q = Doctrine_Query::Create ()
                ->from('UserGroupNode')
                ->where('user_group_id = ?' , $user_group_id)
                ->andWhere('node_id = ?' , $node_id);

        return $q->fetchOne();
    	
    }

}
