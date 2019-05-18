<?php

/**
 */
class UserProviderTable extends Doctrine_Table
{

    function retrieveAll($provider_id = null, $user_id = null, $mtn_maintainer_type_id = null)
    {

        $q = Doctrine_Query::create()
                ->select('up.*, u.*, p.* , mmt.*')
                ->from('UserProvider up')
                ->innerJoin('up.User u')
                ->innerJoin('up.Provider p')
                ->innerJoin('p.MtnMaintainerType mmt');


        if (!is_null($provider_id))
        {
            $q->where('up.provider_id = ?', $provider_id);
        }
        if (!is_null($user_id))
        {
            $q->where('up.user_id = ?', $user_id);
        }
        
        if (!is_null($mtn_maintainer_type_id))
        {
            $q->where('p.mtn_maintainer_type_id = ?', $mtn_maintainer_type_id);
        }

        return $q->execute();
    }

    function findOneBy($user_id)
    {

        $q = Doctrine_Query::create()
                ->from('UserProvider up');

        $q->where('up.user_id = ?', $user_id);

        return $q->fetchOne();
    }

    function retrieveByFilter($text_autocomplete = NULL)
    {

        $q = Doctrine_Query::create()
                ->select('up.*, u.*')
                ->from('User u')
                ->leftJoin('u.UserProvider up')
                ->Where('u.user_id NOT IN ( SELECT upr.user_id FROM UserProvider upr)')
                ->andWhere('u.user_type = ?', 'P')
                ->orderBy('u.user_name ASC');
        
        if (!is_null($text_autocomplete))
        {
            $q->where('u.user_name LIKE ?', $text_autocomplete . '%');
        }


        return $q->execute();
    }

}
