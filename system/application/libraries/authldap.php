<?php

class AuthLDAP {

    private $User;

    function __contructor() {
        
    }

    function isValid($username, $password) {

        $CI = & get_instance();

        //Trae el lenguaje por defecto
        $dataDefaultLanguage = Doctrine_Core::getTable('Language')->defaultLanguage();
        $language_id = $dataDefaultLanguage->language_id;

        $CI->load->config('ldap');

        $ldap_uri = $CI->config->item('ldap_uri');
        $ldap_dn = $CI->config->item('ldap_dn');

        $ds = ldap_connect($ldap_uri); // assuming the LDAP server is on this host

        if ($ds) {

            // bind
            if (ldap_bind($ds)) {

                // prepare data
                $dn = str_replace('#udi#', $username, $ldap_dn); // "uid=" . $username . ", ou=People, dc=uchile, dc=cl";
                // compare value
                $r = @ldap_bind($ds, $dn, $password);

                if ($r === -1) {

                    return "Error: " . ldap_error($ds);
                } elseif ($r === TRUE) { // user correct
                    $user = Doctrine_Core :: getTable('User')->checkUser($username);

                    if ($user) {

                        $this->User = $user;
                        return TRUE;
                    } else {

                        return "Usuario no existe en IGEO";
                    }
                } elseif ($r === FALSE) {

                    return FALSE;
                }
            } else {
                return "No es posibile conectar con Pasaporte.";
            }

            ldap_close($ds);
        } else {
            return "No es posibile conectar con Pasaporte.";
        }
    }

    function getUser() {

        return $this->User;
    }

}
