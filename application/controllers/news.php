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


class News extends Controller {

    private $news_model;

    function __init(){
        $this->news_model = new newsModel();
    }

    function index($params){

        if (isset($_POST["save"])) {
            $this->edit($params);
        } else {
            switch (count($params)){
                case 1:
                    $this->render([
                        'news' => $this->news_model->get($params[0]),
                    ]);
                    break;

                case 2:
                    $news = $this->news_model->last($params[0], $params[1]);
                    $this->render([
                        'count' => count($news),
                        'news' => $news,
                    ]);
                    break;

                default:
                    $news = $this->news_model->last();
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
            if(!Registry::get('_auth')->add) header('location: ' . URL . '403');
        else
            if(!Registry::get('_auth')->edit) header('location: ' . URL . '403');

            $this->news_model->edit($_POST["title"], $_POST["text"]);
    }

}