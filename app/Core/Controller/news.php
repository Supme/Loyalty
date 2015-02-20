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

    private $news_model;

    function __init(){
        $this->news_model = new \App\Core\Model\news();
    }

    function index($params)
    {

        if (isset($params[0]) and $params[0] == 'edit') {
            $this->edit($params);
        } else {
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

        if($params[0] =='')
            if(!\Registry::get('_auth')->add) header('location: ' . URL . '403');
        else
            if(!\Registry::get('_auth')->edit) header('location: ' . URL . '403');

        \Registry::$store['_page']['view'] = 'news_edit';

        \Registry::css([
            "/assets/ly/css/datepicker.css",
            "/assets/elfinder/css/elfinder.min.css"
        ]);

        \Registry::js([
            "/assets/ly/js/bootstrap-datepicker.js",
            "/assets/tinymce/tinymce.min.js",
            "/assets/elfinder/js/elfinder.min.js",
            ]);

        if(isset($params[1]))
        {
           $value = $this->news_model->load((int)$params[1]);
        } else {
            $value = [];
        }

        $this->render([
            'value' => $value,
        ]);
            //$this->news_model->edit($_POST["title"], $_POST["text"]);
    }

}