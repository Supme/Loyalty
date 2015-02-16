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

class Main extends \Controller
{

    public function index()
    {
        $content = new \App\Core\Model\content();
        if (isset($_POST['save']) && \Registry::get('_auth')->edit) {
            $content->edit($_POST['position'], $_POST['text']);
            echo 'Ok';
        } else {
            if (\Registry::get('_auth')->edit){

                \Registry::css([
                    "/assets/ly/css/loyalty.css"
                ]);

                \Registry::js([
                    "/assets/ly/js/jquery.datetimepicker.js",
                    "/assets/ly/js/tinymce/tinymce.min.js",
                    "/assets/ly/js/loyalty.js",
                ]);

                \Registry::menu([
                    'Edit' => [
                        'onclick'=>'edit()',
                    ]
                ]);
            }

        \Registry::notification([
            'info' => [
                'This info notification',
                'And one more notification',
            ],
            'warning' => [
                'This warning notification',
            ],
            'success' => [
                'This success notification',
            ],
            'danger' => [
                'This danger notification',
            ],
        ]);

        $this->render(['result' => $content->load()]);
        }
    }
}
