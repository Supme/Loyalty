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
        $data = $this->select('personal_people', ['id', 'name'], [	"ORDER" => "name ASC",]);
        return $data;
    }

    function put($data)
    {
        $r = $this->insert('cake_log',
            [
                'people_id' =>  $data['name'],
                'method'    =>  $data['method'],
                'comment'   =>  $data['comment'],
                'date'      =>  time(),
            ]
        );

        return $r;
    }

    function result($from, $to)
    {
        $begdate = strtotime($from);
        $enddate = strtotime($to);

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

} 