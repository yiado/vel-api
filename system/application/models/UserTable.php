<?php

/**
 * @package    Model
 * @subpackage UserTable
 */
class UserTable extends Doctrine_Table {

    /**
     * validate
     *
     * Valida si el usuario existe en la base de datos.
     *
     * @param string $user_username
     * @param string $user_password
     */
    function validate($user_username, $user_password) {

        $q = Doctrine_Query::Create()
                ->from('User')
                ->where('user_username = ?', $user_username)
                ->andWhere('user_password = ?', md5($user_password))
                ->limit(1);

        return $q->fetchOne();
    }

    function retrieveByID($user_id) {

        $q = Doctrine_Query::Create()
                ->from('UserGroupUser ugu')
                ->where('ugu.user_id = ?', $user_id);

        return $q->execute();
    }

    /**
     * retrieveAll
     * 
     * Retorna todas las Marcas del equipo equipo
     */
    function retrieveAllFull($text_autocomplete = NULL) {

        $q = Doctrine_Query :: create()
                ->from('User u')
                ->orderBy('u.user_name ASC');

        if (!is_null($text_autocomplete)) {
            $q->where('u.user_name LIKE ?', $text_autocomplete . '%');
        }

        return $q->execute();
    }

    /**
     * validateUser
     *
     * Valida si el nombre de usuario existe en la base de datos.
     *
     * @param string $user_username
     */
    function checkUser($user_username) {

        $q = Doctrine_Query::Create()
                ->from('User')
                ->where('user_username = ?', $user_username)
                ->limit(1);

        return $q->fetchOne();
    }

    /**
     * Retorna los usuarios del sistema;
     *   - Permite buscar por autocompletado de texto
     *   - Y filtrar si se muestra o no el usuario administrador
     *
     * @param string $text_autocomplete
     * @param bool $show_admin_user
     */
    function retrieveAll($text_autocomplete = NULL, $show_admin_user = false, $display_the_user_system = false, $filters) {

        $q = Doctrine_Query::create()
                ->from('User u')
                ->leftJoin('u.UserGroupUser ugu')
                ->leftJoin('ugu.UserGroup ug')
                ->where('u.user_type IS NOT NULL')
                ->orderBy('u.user_username ASC');

        if (!is_null($text_autocomplete)) {

            $q->andWhere('u.user_username LIKE ?', $text_autocomplete . '%');
        }

        if ($show_admin_user === false) {
            //A = Tipo de Usuario administrador
            $q->andWhere('u.user_type <> ?', 'A');
        }

        if ($display_the_user_system === false) {
            //S = Tipo de Usuario system
            $q->andWhere('u.user_type <> ?', 'S');
        }

        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $q->andWhere($field, $value);
            }
        }

        return $q->execute();
    }

    /**
     * Retorna los usuarios del sistema;
     *   - Permite buscar por autocompletado de texto
     *   - Y filtrar si se muestra o no el usuario administrador
     *
     * @param string $text_autocomplete
     * @param bool $show_admin_user
     */
    function retrieveAllNotification($text_autocomplete = NULL, $show_admin_user = false, $display_the_user_system = false, $filters) {

        $q = Doctrine_Query::create()
                ->from('User u')
                ->leftJoin('u.UserGroupUser ugu')
                ->leftJoin('ugu.UserGroup ug')
                ->where('u.user_type IS NOT NULL')
                ->andWhere('u.user_email IS NOT NULL')
                ->andWhere('u.user_email <> ?', '')
                ->orderBy('u.user_name ASC');

        if (!is_null($text_autocomplete)) {
            $q->andWhere('(u.user_username LIKE ? OR u.user_name LIKE ?)', array("%{$text_autocomplete}%", "%{$text_autocomplete}%"));
        }

        if ($show_admin_user === false) {
            //A = Tipo de Usuario administrador
            $q->andWhere('u.user_type <> ?', 'A');
        }

        if ($display_the_user_system === false) {
            //S = Tipo de Usuario system
            $q->andWhere('u.user_type <> ?', 'S');
        }
        
        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $q->andWhere($field, $value);
            }
        }

        return $q->execute();
    }

    /**
     * Verifica si un usuario tiene acceso a un nodo
     * @param integer $node_id
     * @param integer $user_id
     * @return true|false
     *
     */
    function checkAccessNode($user_id, $node_id) {

        $q = Doctrine_Query::Create()
                ->from('Node n')
                ->innerJoin('n.UserGroupNode ugn')
                ->innerJoin('ugn.UserGroup ug')
                ->innerJoin('ug.UserGroupUser ugu')
                ->where('ugu.user_id = ?', $user_id)
                ->andWhere('n.node_id = ?', $node_id)
                ->limit(1);

        $result = $q->fetchOne();
        if ($result) {
            $valid = true;
        } else {
            $valid = false;
        }

        return $valid;
    }

    /**
     *
     * Retorna los permisos del usuario. Las acciones que tiene asociadas los grupos del usuario
     * @param integer $usuarioId
     * @return array
     */
    function getPermissionsActionsUser($user_id) {
        $q = Doctrine_Query::Create()
                ->from('UserGroupAction uga')
                ->innerJoin('uga.ModuleAction ma')
                ->innerJoin('uga.UserGroup ug')
                ->innerJoin('ug.UserGroupUser ugu')
                ->where('ugu.user_id = ?', $user_id);

        $result = $q->execute();
        //Pasamos los actions a la forma de array asociativo; 'module' 'controller' 'action' = flag
        $user_actions = array();

        foreach ($result as $action) {

            $uri = explode('/', $action->ModuleAction->module_action_uri);
            $user_actions[$uri[0]][$uri[1]][$uri[2]] = true;
        }

        return $user_actions;
    }

}
