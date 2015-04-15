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

namespace App\Cake\Controller;

//use \App\Cake\Model\cake as Cake;

class main extends \Controller
{
    function index($params)
    {
        $data = new \App\Cake\Model\data();

        \Registry::css([
            "/assets/bootstrap/3.1.1/css/bootstrap.min.css"
        ]);

        \Registry::js([
            "/assets/jquery/jquery-2.1.3.min.js",
            "/assets/bootstrap/3.1.1/js/bootstrap.min.js",
        ]);

        if (isset($_REQUEST['send'])){

            if($data->put($_REQUEST))
            {
                \Registry::notification([
                    'info' => [
                        'Ваше волеизъявление (душеизлияние?) учтено.',
                    ],
                    'success' => [
                        'Можете продолжить действовать в том же духе.',
                    ],
                ]);
            } else {
                \Registry::notification([
                    'danger' => [
                        'Что то пошло не так и мы не можем добавить это в нашу реляционную базу даных.',
                    ],
                ]);
            }

        }

        $peoples = $data->people();
        $this->render([
            'peoples' => $peoples,
        ]);
    }

    function result($params)
    {
        $data = new \App\Cake\Model\data();

        \Registry::css([
            "/assets/bootstrap-datepicker/1.4.0/css/bootstrap-datepicker.min.css",
            "/assets/tablesorter/css/theme.bootstrap.min.css"
        ]);

        \Registry::js([
            "/assets/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.min.js",
            "/assets/bootstrap-datepicker/1.4.0/locales/bootstrap-datepicker.".\Translate::getCurrentLang().".min.js",
            "/assets/tablesorter/js/jquery.tablesorter.js",
            "/assets/tablesorter/js/jquery.tablesorter.widgets.min.js"
        ]);

        $from = isset($_REQUEST['from'])?$_REQUEST['from']:'';
        $to = isset($_REQUEST['to'])?$_REQUEST['to']:'';
        $res = false;

        if (isset($_REQUEST['send'])){
            if (isset($_REQUEST['from']) and $_REQUEST['from'] != '' and isset($_REQUEST['to']) and $_REQUEST['to'] != '')
            {
                $res = $data->result($from, $to);
            } else {
                \Registry::notification([
                    'danger' => [
                        'Входные данные не заполнены',
                    ],
                ]);
            }
        } else {
            \Registry::notification([
                'info' => [
                    "Задайте интервал.",
                ],
            ]);
        }

        $this->render([
            'from' => $from,
            'to' => $to,
            'res' => $res,
        ]);
    }
}