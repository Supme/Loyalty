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

        $comment_error = false;

        if (isset($_REQUEST['send']))
        {
            if ($_REQUEST['method'] == 2 and $_REQUEST['comment'] == ''){
                $comment_error = true;
                \Registry::notification([
                    'warning' => [
                        'Не обоснованые решки не принимаются!!!',
                    ],
                ]);
            } else {
                if ($_REQUEST['name'] != 0)
                {
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
                        if ( $_REQUEST['method'] == 2) //Уведомим письмом человека об ударе
                        {
                            $person = $data->getPersonalById($_REQUEST['name']);
                            // Create the Transport
                            $transport =
                                \Swift_SmtpTransport::newInstance(
                                    \Registry::get('_config')['email']['smtp_host'],
                                    \Registry::get('_config')['email']['smtp_port'],
                                    \Registry::get('_config')['email']['smtp_encryption']
                                )
                                    ->setUsername(\Registry::get('_config')['email']['smtp_username'])
                                    ->setPassword(\Registry::get('_config')['email']['smtp_password'])
                            ;

                            $mailer = \Swift_Mailer::newInstance($transport);

                            $message = \Swift_Message::newInstance('Вам кинули решку')
                                ->setFrom(['automated.mail@dmbasis.ru' => 'О-решка'])
                                ->setTo([$person['email'] => $person['name']])
                                ->setBody(
                                    "Вам кинули решку за: '" . $_REQUEST['comment']."'"
                                );

                            $mailer->send($message);
                        }
                    } else {
                        \Registry::notification([
                            'danger' => [
                                'Что то пошло не так и мы не можем добавить это в нашу реляционную базу данных.',
                            ],
                        ]);
                    }
                } else {
                    \Registry::notification([
                        'danger' => [
                            'Нужно выбрать кому хотите это вручить.',
                        ],
                    ]);
                }
            }
        }

        $peoples = $data->people();
        $this->render([
            'peoples' => $peoples,
            'request' => $_REQUEST,
            'comment_error' => $comment_error

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
        $type = '';
        $res = false;

        if (isset($_REQUEST['send'])){
            if (isset($_REQUEST['from']) and $_REQUEST['from'] != '' and isset($_REQUEST['to']) and $_REQUEST['to'] != '')
            {

                switch ($_REQUEST['type'])
                {
                    case 'score':
                        $type = 'score';
                        $res = $data->resultScore($from, $to);
                        break;
                    case 'comment':
                        $type = 'comment';
                        $res = $data->resultComment($from, $to);
                        break;
                }

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
//echo "<pre>"; print_r($res); echo "</pre>";
        $this->render([
            'from' => $from,
            'to' => $to,
            'type' => $type,
            'res' => $res,
        ]);
    }
}