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
        $data = $this->select('personal_people', ['id', 'name'], [	'AND' => ['email[!]' => $user->getUserEmail(), 'id[!]' => '98'], 'ORDER' => 'name ASC', ]);
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
/*
        $in = $this->query(
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
                  from personal_people p
                   ORDER BY name
        "
        )->fetchAll();

        $out = $this->query("select
                    p.id,
                    p.name,
                    ( select count(*) from cake_log l1
                        where l1.user_id = u.id
                          and method = 1
                          and date between $begdate and $enddate) as cakes,
                    ( select count(*) from cake_log l2
                        where l2.user_id = u.id
                          and method = 2
                          and date between $begdate and $enddate) as whips
                  from personal_people p
                  left join core_auth_user u on u.email = p.email
                   ORDER BY p.name"
        )->fetchAll();

        $people = $this->select('personal_people', ['id', 'name']);

        return ['people' => $people, 'in' => $in , 'out' => $out];
*/
        return $this->query(
            "select
                    p.id,
                    p.name,
                    ( select count(*) from cake_log l1
                        where l1.people_id = p.id
                          and method = 1
                          and date between $begdate and $enddate) as cakes_in,
                    ( select count(*) from cake_log l2
                        where l2.people_id = p.id
                          and method = 2
                          and date between $begdate and $enddate) as whips_in,
                   ( select count(*) from cake_log l1
                        where l1.user_id = u.id
                          and method = 1
                          and date between $begdate and $enddate) as cakes_out,
                    ( select count(*) from cake_log l2
                        where l2.user_id = u.id
                          and method = 2
                          and date between $begdate and $enddate) as whips_out
                  from personal_people p
                  left join core_auth_user u on u.email = p.email
                   ORDER BY p.name"
        );
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

    function sendStatistic($from, $to)
    {
        $begdate = strtotime($from);
        $enddate = strtotime($to)+86400;
        //select count(cake_log.user_id), core_auth_user.name from cake_log INNER JOIN core_auth_user ON cake_log.user_id = core_auth_user.id group by cake_log.user_id

        return $this->query(
            "SELECT
              core_auth_user.name as name, COUNT(cake_log.user_id) as cnt
              FROM cake_log INNER JOIN core_auth_user ON cake_log.user_id = core_auth_user.id
              WHERE cake_log.date between $begdate and $enddate
              GROUP BY cake_log.user_id
            "
        );
    }

} 