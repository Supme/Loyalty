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

namespace App\Cake\Model;


class data extends \Db {

    function people()
    {
        $user = new \Auth();
        $data = $this->select('personal_people', ['id', 'name'], [	'email[!]' => $user->getUserEmail(), "ORDER" => "name ASC", ]);
        return $data;
    }

    function put($data)
    {
        $user = new \Auth();
        $r = $this->insert('cake_log',
            [
                'user_id'   => $user->getUserId(),
                'people_id' =>  $data['name'],
                'method'    =>  $data['method'],
                'comment'   =>  $data['comment'],
                'date'      =>  time(),
            ]
        );

        return $r;
    }

    function getPersonalById($id)
    {
        $res = $this->select(
            'personal_people',
            ['name', 'email'],
            ['id' => $id]);
        return isset($res[0])?$res[0]:false;
    }

    function resultScore($from, $to)
    {
        $begdate = strtotime($from);
        $enddate = strtotime($to)+86400;

        return $this->query(
            "select
                    p.id,
                    p.name,
                    ( select count(*) from cake_log l1
                        where l1.people_id = p.id
                          and method = 1
                          and date between $begdate and $enddate) as cakes,
                    ( select count(*) from cake_log l2
                        where l2.people_id = p.id
                          and method = 2
                          and date between $begdate and $enddate) as whips
                  from personal_people p ORDER BY name
        "
        )->fetchAll();
    }

    function resultComment($from, $to)
    {
        $begdate = strtotime($from);
        $enddate = strtotime($to)+86400;

        return $this->select(
            'cake_log',
            [
                '[>]personal_people' => ['people_id' => 'id'],
                '[>]core_auth_user' => ['user_id' => 'id']
            ],
            ['personal_people.name', 'core_auth_user.name(user)', 'cake_log.method', 'cake_log.comment', 'cake_log.date'],
            ['date[<>]' => [$begdate, $enddate],"ORDER" => "cake_log.date ASC"]
        );
    }
} 