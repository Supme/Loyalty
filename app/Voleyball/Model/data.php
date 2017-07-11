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

namespace App\Voleyball\Model;


class data extends \Db {

    function people($begin, $end) {
        $data = $this->select(
            'voleyball',
            ['[>]core_auth_user' => ['user_id' => 'id']],
            ['core_auth_user.name'],
            ['date[<>]' => [$begin, $end]]
        );
        return $data;
    }

    function status($user, $begin, $end) {
        return $this->count(
            'voleyball',
                ['AND' => ['date[<>]' => [$begin, $end],
                'user_id' => $user]]
            ) != 0;
    }

    function set($user) {
        //insert
        return $this->insert(
            'voleyball',
            [
                'user_id' =>  $user,
                'date' =>  time(),
            ]
        );
    }

    function del($user, $begin, $end) {
        //delete
        $this->delete(
            'voleyball',
                ['AND' => ['date[<>]' => [$begin, $end],
                    'user_id' => $user]]
        );
        return ;
    }

}