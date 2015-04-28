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

namespace App\Core\Controller;

class news extends \Controller {

    protected $news_model;

    function __init(){
        $this->news_model = new \App\Core\Model\news();
    }

    function index($params)
    {

        if (isset($params[0]) and $params[0] == 'edit') {
            $this->edit($params);
        } else {
            $user = new \Auth();
            if ($user->canUpdate())
                \Registry::menu([
                    'Add' => [
                        'href'=>'./edit/',
                    ]
                ]);

            switch (count($params)) {
                case 1:
                    $this->render([
                        'news' => $this->news_model->load($params[0]),
                    ]);
                    break;

                case 2:
                    $news = $this->news_model->last($params[1], $params[0]);
                    $this->render([
                        'count' => count($news),
                        'news' => $news,
                    ]);
                    break;

                default:
                    $news = $this->news_model->last(10);
                    $this->render([
                        'count' => count($news),
                        'news' => $news,
                    ]);
                    break;
            }
        }
    }

    private function edit($params){
        $user = new \Auth();
        if( !isset($params[0]) and $params[0] != 'edit')
            header('location: ' . URL . '403');

        if ( !isset($params[1]) and !$user->canCreate() )
            header('location: ' . URL . '403');
        else
            if (!$user->canUpdate())
                header('location: ' . URL . '403');

        \Registry::$store['_page']['view'] = 'news_edit';

        \Registry::css([
            "/assets/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css",
        ]);

        \Registry::js([
            "/assets/tinymce/tinymce.min.js",
            "/assets/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js",
            "/assets/bootstrap-datepicker/1.4.0/locales/bootstrap-datepicker.".\Translate::getCurrentLang().".min.js",
            ]);

        if ( isset($_POST['title']) and isset($_POST['date']) and isset($_POST['announce']) )
        {
            $this->news_model->edit(
                isset( $params[1])?(int)$params[1]:false,
                $_POST['title'],
                $_POST['announce'],
                $_POST['text'] != ''?$_POST['text']:false,
                strtotime($_POST['date'])
            );

        }

        if( isset($params[1]) )
        {
           $value = $this->news_model->load((int)$params[1]);
        } else {
            $value = [];
        }

        $this->render([
            'value' => $value,
        ]);
    }
}