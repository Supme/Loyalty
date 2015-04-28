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
        $this->auth = new \Auth();
        if(!$this->auth->canRead()) header("Location: ../error/403");
    }

    function index()
    {
        $users = new \App\Core\Model\users(['name' => 'users']);
        if (!$users->isRequest()) {
            if(isset($_REQUEST['add']))
            {
                $result = $this->auth->addUser(
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
            $this->render(['table' => $table, 'groups' => $this->auth->getGroupName()]);
        } else {
            $users->ajax();
        }
    }

    function add(){
        if(!$this->auth->canCreate()) header("Location: ../error/403");
        $this->users->add('n.kozkin','nikolay@kozkin.ru', 'pass');
    }

    function edit($params){
        if(!$this->auth->canUpdate()) header("Location: ../error/403");
        $this->users;
    }

}
