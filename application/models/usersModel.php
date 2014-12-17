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
class usersModel extends Table
{
    function total(){
        return $this->database->count('authUsers');
    }

    function filtred(){
        return $this->total();
    }

    function column(){
        return ['userId', 'userName', 'userEmail', 'userGroup'];
    }

    function filtered(){

    }

    function data($start, $lenght, $order, $filter){
        return $this->database->select(
            'authUsers',
            [
                '[>]authGroups' => ['groupId' => 'id']
            ],
            [
                'authUsers.id(userId)',
                'authUsers.userName',
                'authUsers.email(userEmail)',
                'authGroups.name(userGroup)',
            ],
            [
                "ORDER" => ['authUsers.userName ASC'],
                "LIMIT" => [[$start, $lenght]],
            ]
        );
    }

}