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
 * ToDo пользователь может иметь много групп, а вот группам уже назначать права
 * ToDo да и вообще сделать все это наконец
 */

class User extends Model
{
    function get($user)
    {
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
                'OR' => [
                    'authUsers.userName' => $user,
                    'authUsers.email' => $user
                ]
            ]
        );
    }

    function add($userName, $email, $password)
    {
        $userName = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $userName);

        if ($this->database->has('authUsers',
            [
                'OR' => [
                    'userName' => $userName,
                    'email' => $email
                ]
            ]
        )
        ) {
            // User already exist

            return 'User already exist';
        } else {

            $salt = $this->randomString();
            $password = hash('sha512', $password . $salt);

            $groupId = $this->database->select('authGroups', 'id', ['name' => Registry::get('_config')['user']['default_group']])[0];

            return $this->database->insert('authUsers',
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

    function update($userId, $userName, $groupId, $email, $password)
    {
        $salt = $this->saltGen();
        $password = hash('sha512', $password . $salt);

        return $this->database->update('authUsers',
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

    // ToDo
    public function deleteUser($id)
    {

    }

    //ToDo объеденить или еще чё с $this->add
    public function addUser($login, $name, $email, $password, $group)
    {
        $error = [];

        if (!\Validator::alnum()->validate($login) or $login == '') $error[] = 'Not valid login';
        if (!\Validator::alnum()->validate($name) or $name == '') $error[] = 'Not valid name';
        if (!\Validator::email()->validate($email)) $error[] = 'Not valid email';
        if (!\Validator::int()->validate($group)) $error[] = 'Not valid group';

        if (count($error) == 0) {
            $salt = \Misc::randomString();
            $password = hash('sha512', $password . $salt);
            $this->insert('core_auth_user',
                [
                    'group_id' => $group,
                    'login' => $login,
                    'email' => $email,
                    'name' => $name,
                    'password' => $password,
                    'salt' => $salt
                ]);
            $result = true;
        } else {
            $result['danger'] = $error;
        }

        return $result;
    }


}