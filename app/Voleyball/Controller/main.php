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

namespace App\Voleyball\Controller;


class main extends \Controller
{
    function index($params)
    {
        $data = new \App\Voleyball\Model\data();

        $dateNow = new \DateTime();
        $diff = date("w", $dateNow->getTimestamp());
        $diff = $diff == 0?6:$diff-1;
        $dateMon = new \DateTime();
        $dateMon->sub(\DateInterval::createFromDateString($diff.' days 12 hour'));
        $dateSat = new \DateTime();
        $dateSat->sub(\DateInterval::createFromDateString($diff.' days'));
        $dateSat->add(\DateInterval::createFromDateString('6 days'));

        \Registry::css([
            "/assets/bootstrap/3.1.1/css/bootstrap.min.css",
            "/assets/select2/css/select2.min.css",
        ]);

        \Registry::js([
            "/assets/jquery/jquery-2.1.3.min.js",
            "/assets/bootstrap/3.1.1/js/bootstrap.min.js",
        ]);

        $user = new \Auth();

        if (isset($_REQUEST['get']) and $user->isLogin) {
            if ($_REQUEST['get'] == "people") {
                $this->json($data->people($dateMon->getTimestamp(), $dateSat->getTimestamp()));
                return;
            }
            if ($_REQUEST['get'] == "status" and $user->isLogin) {
                $this->json(["status" => $data->status($user->getUserId(), $dateMon->getTimestamp(), $dateSat->getTimestamp())]);
                return;
            }

        }
        if (isset($_REQUEST['set']) and $user->isLogin) {
            if ($_REQUEST['set'] == "on") {
                $res = $data->set($user->getUserId());
                $this->json(["status" => $res]);
                return;
            }
            if ($_REQUEST['set'] == "off") {
                $res = $data->del($user->getUserId(), $dateMon->getTimestamp(), $dateSat->getTimestamp());
                $this->json(["status" => $res]);
                return;
            }
        }
var_dump($dateMon->getTimestamp()." ".$dateSat->getTimestamp());
        $this->render();
    }
}