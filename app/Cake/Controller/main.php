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
            "/assets/bootstrap/3.1.1/css/bootstrap.min.css",
            "/assets/select2/css/select2.min.css",
        ]);

        \Registry::js([
            "/assets/jquery/jquery-2.1.3.min.js",
            "/assets/bootstrap/3.1.1/js/bootstrap.min.js",
            "/assets/select2/js/select2.min.js",
            "/assets/select2/js/i18n/es.js"
        ]);

        $comment_error = false;
        $user = new \Auth();
        if ((isset($_REQUEST['name']) and $_REQUEST['name'] != '') and $user->isLogin)
        {
            if ($_REQUEST['comment'] == ''){
                if ($_REQUEST['method'] == 2){
                    $comment_error = true;
                    \Registry::notification([
                        'warning' => [
                            'Не обоснованые решки не принимаются!!!',
                        ],
                    ]);
                } else {
                    $comment_error = true;
                    \Registry::notification([
                        'warning' => [
                            'Без комментария нельзя послать орла!',
                        ],
                    ]);
                }
            } else {
                if($data->put($user->getUserId(), $_REQUEST))
                {
                    \Registry::notification([
                        'success' => [
                            $data->getRandPhrase(),
                            ],
                        ]);
/*
                    \Registry::notification([
                        'info' => [
                            $data->getRandPhrase(),
                            $data->getRandQuestion(),
//                            'Ваше волеизъявление (душеизлияние?) учтено.',
                        ],
                        'success' => [
                            $data->getRandMotivator(),
//                            'Можете продолжить действовать в том же духе.',
                        ],
                    ]);
*/
                    $person = $data->getPersonalById($_REQUEST['name']);
                    // Create the Transport
                    $transport =
                        \Swift_SmtpTransport::newInstance(
                            \Registry::get('_config')['email']['smtp_host'],
                            \Registry::get('_config')['email']['smtp_port'],
                            \Registry::get('_config')['email']['smtp_encryption']
                        )
                            ->setUsername(\Registry::get('_config')['email']['smtp_username'])
                            ->setPassword(\Registry::get('_config')['email']['smtp_password']);

                    $mailer = \Swift_Mailer::newInstance($transport);

                    if ( $_REQUEST['method'] == 2) //Уведомим письмом человека об ударе
                    {
                        $message = \Swift_Message::newInstance('Вам кинули решку')
                            ->setFrom(['automated.mail@dmbasis.ru' => 'О-решка'])
                            ->setTo([$person['email'] => $person['name']])
                            ->setBody(
                                "Вам кинули решку за: '" . $_REQUEST['comment']."'"
                            );
                    } else {
                        $message = \Swift_Message::newInstance('Вам дали орла')
                            ->setFrom(['automated.mail@dmbasis.ru' => 'О-решка'])
                            ->setTo([$person['email'] => $person['name']])
                            ->setBody(
                                "Вам дали орла за: '" . $_REQUEST['comment']."'"
                            );
                    }

                    $mailer->send($message);
                } else {
                    \Registry::notification([
                        'danger' => [
                            'Что то пошло не так и мы не можем добавить это в нашу реляционную базу данных.',
                        ],
                    ]);
                }
            }
        } else {
            if(isset($_REQUEST['name']) and $_REQUEST['name'] == '')
                \Registry::notification([
                    'danger' => [
                        'Кто то где то что то не выбрал.',
                    ],
                ]);
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
                    case 'activity':
                        $type = 'activity';
                        $res = $data->sendStatistic($from, $to);
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