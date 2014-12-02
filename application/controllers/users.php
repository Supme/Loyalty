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

class Users extends Controller {

    function __init(){
        if(!Registry::get('_auth')->read) header("Location: ../error/403");

        $this->users = new usersModel();
    }
    function index(){
        $this->render(['users' => $this->users->get()]);
    }

    function add(){
        if(!Registry::get('_auth')->add) header("Location: ../error/403");
        $this->users->add('n.kozkin','nikolay@kozkin.ru', 'pass');
    }

    function edit($params){
        if(!Registry::get('_auth')->edit) header("Location: ../error/403");
        $this->users;
    }

}