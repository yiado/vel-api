<?php

/**
 * Model para los usuarios asociados a los grupos
 * @author manuteko
 *
 */
class UserGroupUserTable extends Doctrine_Table {

    /**
     * Retorna los usaurios asociados al grupo
     * @param integer $user_group_id 
     */
    function retrieveUsersByGroup($user_group_id) {

        $q = Doctrine_Query::create()
                ->from('UserGroupUser ugu')
                ->innerJoin('ugu.User u')
                ->where('ugu.user_group_id = ?', $user_group_id)
                ->orderBy('u.user_name ASC');

        return $q->execute();
    }

    /**
     * Elimina la asociación de usuarios al grupo
     * @param integer $user_group_id
     */
    function deleteCurrentUsersInGroup($user_group_id) {

        $q = Doctrine_Query::create()
                ->delete('UserGroupUser ugu')
                ->where('ugu.user_group_id = ?', $user_group_id);

        return $q->execute();
    }

    /**
     * Retorna los usuarios que no son parte del grupo
     * @param integer $user_group_id
     * @param bool $show_admin_user (opcional)
     */
    function retrieveUsersOutsideGroup($user_group_id, $show_admin_user = false) {

        $q = Doctrine_Query::create()
                ->from('User u')
                ->where('u.user_id NOT IN (SELECT u2.user_id FROM User u2 INNER JOIN u2.UserGroupUser ugu WHERE ugu.user_group_id = ?)', $user_group_id)
                ->orderBy('u.user_name ASC');

        if ($show_admin_user === false) {
            //A = Tipo de Usuario administrador
            $q->andWhere('u.user_type <> ?', 'A');
        }

        return $q->execute();
    }

    /**
     * Elimina la asociación de usuarios al grupo
     * @param integer $user_id
     */
    function deleteCurrentGroupsUser($user_id) {

        $q = Doctrine_Query::create()
                ->delete('UserGroupUser ugu')
                ->where('ugu.user_id = ?', $user_id);

        return $q->execute();
    }

    /**
     * Retorna los grupos del usuario
     * @param integer $user_id
     * 
     */
    function retrieveGroupsByUserId($user_id) {

        $q = Doctrine_Query::create()
                ->from('UserGroup ug')
                ->innerJoin('ug.UserGroupUser ugu')
                ->where('ugu.user_id = ?', $user_id)
                ->orderBy('ug.user_group_name ASC');

        return $q->execute();
    }

    /**
     * Retorna los grupos que no estan asociados al usuario
     * @param integer $user_id
     */
    function retrieveGroupOutsideUsers($user_id) {

        $q = Doctrine_Query::create()
                ->from('UserGroupUser ugu')
                ->where('u.user_id NOT IN (SELECT u2.user_id FROM User u2 INNER JOIN u2.UserGroupUser ugu WHERE ugu.user_group_id = ?)', $user_id)
                ->orderBy('u.user_name ASC');

        return $q->execute();
    }

    function retrieveArrayGroup($user_id) {

        $q = Doctrine_Query::create()
                ->from('UserGroupUser ugu')
                ->where('ugu.user_id = ?', $user_id);
        return $q->execute();
    }

}
