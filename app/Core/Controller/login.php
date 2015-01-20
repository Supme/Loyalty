<?php
/**
 * @package Loyality Portal
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

class login extends \Controller {
    function index(){

        // Login user
        if (isset($_REQUEST["login"])) {
            if(\Registry::get('_auth')->login($_POST['email'], $_POST['password'])){
                \Registry::notification([
                    'success' => [
                        \Translate::get('You are login as').' '.\Registry::get('_auth')->name,
                    ]
                ]);
            } else {
                \Registry::notification([
                    'danger' => [
                        \Translate::get('Error login'),
                    ]
                ]);
            }
        }

        // Logout user
        if (isset($_REQUEST["logout"])) {
            \Registry::get('_auth')->logout();
            \Registry::notification([
                'info' => [
                    \Translate::get('Success logout'),
                    \Translate::get('Thanks for using service'),
                ],
            ]);
        }

        $this->render();
    }

}