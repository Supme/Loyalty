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

class content extends \Controller {
    function index(){

        $user = new \Auth();
        $content = new \App\Core\Model\content();

        if (isset($_POST['save']) && $user->canUpdate()) {
            $content->edit($_POST['position'], $_POST['text']);
            echo 'Ok';
        } else {
            $result = $content->load();

            if ($user->canUpdate()){

                \Registry::css([
                    "/assets/jquery-ui-1.11.4/jquery-ui.min.css",
                    "/assets/ly/css/loyalty.css"
                ]);

                \Registry::js([
                    "/assets/jquery-ui-1.11.4/jquery-ui.min.js",
                    "/assets/tinymce/tinymce.min.js",
                    "/assets/ly/js/loyalty.js",
                ]);

                \Registry::menu([
                    'Edit' => [
                        'onclick'=>'edit()',
                    ]
                ]);

            }

            if(!$result){
                $result = [0 => ['text' => 'No content', 'position' => 1]];
            }
            $this->render([
                'result' => $result,
            ]);
        }
    }

}