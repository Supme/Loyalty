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

class User extends Model
{
    function get($user){
        return $this->database->select(
            'authUsers',
            [
                '[>]authGroups' => ['groupId' => 'id']
            ],
            [
                'authUsers.id(userId)',
                'authUsers.userName',
                'authUsers.email(userEmail)',
                'authGroups.name(userGroup)'
            ],
            [
                'OR' =>[
                    'authUsers.userName' => $user,
                    'authUsers.email' => $user
                ]
            ]
        );
    }

    function add($userName, $email, $password){
        $userName = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $userName);

        if($this->database->has('authUsers',
            [
                'OR' => [
                    'userName' => $userName,
                    'email' => $email
                ]
            ]
        )){
            // User already exist

            return 'User already exist';
        } else {

            $salt = $this->randomString();
            $password = hash('sha512', $password.$salt);

            $groupId = $this->database->select('authGroups','id', ['name' => Registry::get('_config')['user']['default_group']])[0];

            return $this->database->insert( 'authUsers',
                [
                    'groupId' => $groupId,
                    'userName' => $userName,
                    'email' => $email,
                    'password' => $password,
                    'salt' => $salt
                ]

            );
        }
    }

    function update($userId,$userName, $groupId, $email, $password){
        $salt = $this->saltGen();
        $password = hash('sha512', $password.$salt);

        return $this->database->update( 'authUsers',
            [
                'groupId' => $groupId,
                'userName' => $userName,
                'email' => $email,
                'password' => $password,
                'salt' => $salt
            ],
            ['id' => $userId]

        );

    }

} 