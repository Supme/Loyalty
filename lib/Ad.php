<?php
/**
 * @package Loyality Portal.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */


class Ad {

    protected
        $ad,
        $name = 'Guest',
        $login_checked = false;

    function __construct()
    {
        /*
        $config = [
            'account_suffix' => Registry::get('_config')['ad']['account_suffix'],
            'domain_controllers' => explode(",",str_replace(" ", '', \Registry::get('_config')['ad']['domain_controllers'])),
            'base_dn' => Registry::get('_config')['ad']['base_dn'],
            'admin_username' => Registry::get('_config')['ad']['admin_username'],
            'admin_password' => Registry::get('_config')['ad']['admin_password'],
        ];
        */

        $config = new \Adldap\Connections\Configuration();
        $config->setAccountSuffix(\Registry::get('_config')['ad']['account_suffix']);
        $config->setDomainControllers(explode(",",str_replace(" ", '', \Registry::get('_config')['ad']['domain_controllers'])));
        $config->setBaseDn(\Registry::get('_config')['ad']['base_dn']);
        $config->setAdminUsername(\Registry::get('_config')['ad']['admin_username']);
        $config->setAdminPassword(\Registry::get('_config')['ad']['admin_password']);

        $this->ad = new \Adldap\Adldap($config);
        //$this->ad = new \Adldap\Connections\Provider($config);
    }

    private function authenticate($login, $password)
    {
         return $this->ad->authenticate($login, $password);
//        return $this->ad->auth()->attempt($login, $password);
    }

    private function userInfo($login)
    {
        $filds = [
            'cn',
            'sn',
            'givenname',
            'name',
            'department',
            'title',
            'mail',
            'telephonenumber',
            'homephone',
            'mobile',
            'ipphone'
        ];
        $userAttr =  $this->ad->users()->find($login, $filds)->getAttributes();
        $user = [];
        foreach ($filds as $attr)
        {
            $user[$attr] = $userAttr[$attr][0];
        }
        return $user;

    }

    public function login($login, $password)
    {
        if ($this->authenticate($login, $password))
        {
            $user = $this->userInfo($login);
            $auth = new \Auth();

            if ($auth->isUser($login))
            {
                $id = $auth->idUser($login);
                $auth->updateUser($id, $user['name'], $login, $user['mail'], $password);
            } else {
                $auth->createUser($user['name'], $login, $user['mail'], $password);
            }

        } else
            return false;

        return true;
    }
}