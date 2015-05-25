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
        $user = new \Auth();
        // Login user
        if (isset($_REQUEST["login"])) {
            if($user->login($_REQUEST['username'], $_REQUEST['password'])){
                if(isset($_REQUEST['redir']))
                {
                    header('Location: ' . $_REQUEST['redir']);
                    exit;
                }

                \Registry::notification([
                    'success' => [
                        \Translate::get('You are login as').' '.$user->getUserName(),
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
            $user->logout();

            \Registry::notification([
                'info' => [
                    \Translate::get('Success logout'),
                    \Translate::get('Thanks for using service'),
                ],
            ]);
        }

        \Registry::css([
            "/assets/bootstrap/3.1.1/css/bootstrap.min.css",
            "//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css"
        ]);

        \Registry::js([
            "/assets/jquery/jquery-2.1.3.min.js",
            "/assets/bootstrap/3.1.1/js/bootstrap.min.js",
            "//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js",
            "//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"
        ]);

        $redir = isset($_REQUEST['redir'])?urlencode($_REQUEST['redir']):false;
        $this->render(['redir' => $redir]);
    }
}