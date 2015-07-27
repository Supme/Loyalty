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

namespace App\Sender\Controller;

class main extends \Controller {

    function __init($params){

    }

    function index()
    {
        $sender = new \App\Sender\Model\sender(['name' => 'recipients']);
        $sender->campaignId = 2;
        if ($sender->isRequest()) {
            $sender->ajax();
        } else {
            $table = $sender->html();
            $this->render(['table' => $table]);
        }
    }

    function add($params){

        $this->sender->addRecipients(
            2,
            [
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male'
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female'
                ],
                [
                    'name' => 'Misha',
                    'phone' => '+79123213434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Dasha',
                    'phone' => '+79123211212',
                    'gender' => 'female',
                ],
                [
                    'name' => 'Kostya',
                    'phone' => '+79123223434',
                    'gender' => 'male',
                ],
                [
                    'name' => 'Klava',
                    'phone' => '+79124211212',
                    'gender' => 'female',
                ],
            ]
        );
        /*
        $sender->campaign('Second campaign test', 'Test subject', 'Test message');

        $tableHeader = '';
        print_r(
            $this->sender->getRecipients(1, 10)
        );

        print_r($this->sender->recipientDatas(1));
        //echo '<hr/>'; print_r($sender->recipientDatas(2));echo '<hr/>';

        */
    }
}