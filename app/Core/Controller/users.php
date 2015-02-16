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

namespace App\Core\Controller;

class users extends \Controller {

    function __init(){
       if(!\Registry::get('_auth')->read) header("Location: ../error/403");
    }

    function index()
    {
        $users = new \App\Core\Model\users(['name' => 'users']);
        if (!$users->isRequest()) {
            if(isset($_REQUEST['add']))
            {
                $result = \Registry::get('_auth')->addUser(
                    $_REQUEST['login'],
                    $_REQUEST['name'],
                    $_REQUEST['email'],
                    $_REQUEST['password'],
                    $_REQUEST['group']
                );
                if($result !== true)
                {
                    \Registry::notification($result);
                } else {
                    \Registry::notification(['success' => ['User "'.$_REQUEST['login'].'" successfully created.']]);
                }
            }
            $table = $users->html();
            $this->render(['table' => $table, 'groups' => \Registry::get('_auth')->getGroups()]);
        } else {
            $users->ajax();
        }
    }

    function add(){
        if(!\Registry::get('_auth')->add) header("Location: ../error/403");
        $this->users->add('n.kozkin','nikolay@kozkin.ru', 'pass');
    }

    function edit($params){
        if(!\Registry::get('_auth')->edit) header("Location: ../error/403");
        $this->users;
    }

}
