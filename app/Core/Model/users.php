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

namespace App\Core\Model;

class users extends \Table
{
    function total(){
        return $this->count('core_auth_user');
    }

    function filtered(){
        return $this->total();
    }

    function column(){
        return ['user_id', 'user_login', 'user_name', 'user_email', 'user_group'];
    }

    function data($start, $lenght, $order, $filter){
        return $this->select(
            'core_auth_user',
            [
                '[>]core_auth_group' => ['group_id' => 'id']
            ],
            [
                'core_auth_user.id(user_id)',
                'core_auth_user.login(user_login)',
                'core_auth_user.name(user_name)',
                'core_auth_user.email(user_email)',
                'core_auth_group.name(user_group)',
            ],
            [
                "ORDER" => ['core_auth_user.id ASC'],
                "LIMIT" => [[$start, $lenght]],
            ]
        );
    }

}