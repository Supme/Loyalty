<?php
/**
 * @package Loyality Portal
 * @author Supme
 * @copyright Supme 2014
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

namespace App\Personal\Controller;

class main extends \Controller {

    function __init(){

        $this->personal = new \App\Personal\Model\personal();

        // add submenu
        if (\Registry::get('_auth')->add)
            \Registry::menu([
                'Add' => [
                    'href'=>'./'.\Registry::get('_page')['segment'].'/add',
                ]
            ]);
        if (\Registry::get('_auth')->edit)
            \Registry::menu([
                'Edit' => [
                    'href'=>'./'.\Registry::get('_page')['segment'].'/edit',
                ]
            ]);

    }

    function index(){

        $this->render([
                'result' => $this->personal->get(),

        ]);
    }

    function add() {

        if (isset($_POST['submit_add_personal']) && \Registry::get('_auth')->is_login){

            //ToDo save data
            header('location: ' . URL .\Registry::get('_page')['segment']);
        }

        $this->render([
                'departments' => $this->personal->department(),
        ]);
    }
}