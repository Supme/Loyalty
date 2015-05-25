<?php
/**
 * @package ly.
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

use Adldap\Adldap;

class Ad {

    protected
        $ad,
        $name = 'Guest',
        $login_checked = false;

    function __construct()
    {
        $config = [
            'account_suffix' => Registry::get('_config')['ad']['account_suffix'],
            'domain_controllers' => explode(",",str_replace(" ", '', \Registry::get('_config')['ad']['domain_controllers'])),
            'base_dn' => Registry::get('_config')['ad']['base_dn'],
            'admin_username' => Registry::get('_config')['ad']['admin_username'],
            'admin_password' => Registry::get('_config')['ad']['admin_password'],
        ];

        $this->ad = new Adldap($config);
    }

    private function authenticate($login, $password)
    {
        return $this->ad->authenticate($login, $password);
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
        return $this->ad->user()->find($login, $filds);
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