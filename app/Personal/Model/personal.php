<?php
/**
 * @package Loyality Portal
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

namespace App\Personal\Model;

class personal extends \Db
{
    function departments()
    {
        $result = [];
        $cities = $this->select( 'personal_office', '*' );
        foreach($cities as $city){
            $departments = $this->select(
                'personal_department',
                '*',
                ['office_id' => $city['id']]
            );
            foreach($departments as $department){
                $result[$department['id']] = $city['name'].' '.$department['name'];
            }
        }

        return $result;
    }

    function load()
    {
        $result = \Cache::get('modPersonal');
        if(empty($result)){
            $city = $this->select( 'personal_office', '*' );
            foreach($city as $kc => $c){
                $departament = $this->select( 'personal_department', '*', ['office_id' => $c['id']] );
                foreach($departament as $kd => $d){
                    //this only for me
                    if ($d['id'] == "2")
                        $people = $this->select('personal_people', '*', ['department_id' => $d['id'], "ORDER" => "name DESC",]);
                    else
                        $people = $this->select('personal_people', '*', ['department_id' => $d['id'], "ORDER" => "name ASC",]);
                    //this original
                    //$people = $this->select('personal_people', '*', ['department_id' => $d['id'], "ORDER" => "name ASC",]);
                    foreach($people as $kp => $p){
                        $result[$c['name']][$d['name']][$p['name']] = $p;
                    }
                }
            }
            \Cache::set('modPersonal', $result);
        }

        return $result;
    }

    function personal($id)
    {
        $data = $this->select('personal_people','*', ['id' => (int)$id]);
        return isset($data[0])?$data[0]:[];
    }

    function edit($data='')
    {
        if ($data['id'] != '')
        {
            if($data['photo'] == '') $data['photo'] = $this->select('personal_people','photo', ['id' => (int)$data['id']])[0];
            print_r($data['photo']);
            $this->update(
                'personal_people',
                [
                    'department_id' => $data['department_id'],
                    'name' => $data['name'],
                    'photo' => $data['photo'],
                    'position' => $data['position'],
                    'function' => $data['function'],
                    'email' => $data['email'],
                    'birthday' => $data['birthday'],
                    'birthday_date' => $data['birthday_date'],
                    'telephone_internal' => $data['telephone_internal'],
                    'telephone_mobile' => $data['telephone_mobile'],
                    'telephone_external' => $data['telephone_external'],
                    'change' => $data['change']
                ],
                ['id' => $data['id']]
                );
        } else {
            $this->insert(
                'personal_people',
                [
                    'department_id' => $data['department_id'],
                    'name' => $data['name'],
                    'photo' => $data['photo'],
                    'position' => $data['position'],
                    'function' => $data['function'],
                    'email' => $data['email'],
                    'birthday' => $data['birthday'],
                    'birthday_date' => $data['birthday_date'],
                    'telephone_internal' => $data['telephone_internal'],
                    'telephone_mobile' => $data['telephone_mobile'],
                    'telephone_external' => $data['telephone_external'],
                    'change' => $data['change']
                ]

            );
        }
    }

    function del($id)
    {
        $this->delete('personal_people', ['id' => (int)$id]);
    }

    function birthday($range)
    {
        $direction = true;
        if ($range[0] =="-")
        {
            $range = substr($range, 1);
            $direction = false;
        }
        $range = (int)$range;
        $data = [];
        $people = $this->select('personal_people',['name', 'photo', 'birthday_date'], ["ORDER" => "birthday_date ASC"]);
        $dateNow =new \DateTime();
        $dateNow->setTime(0,0,0);
        $dateBirthday = new \DateTime();
        foreach ($people as $p)
        {
            $dateBirthday->setTimestamp($p['birthday_date']);
            $dateBirthday->add(new \DateInterval('P'.(string)((int)date('Y') - (int)date('Y', $p['birthday_date'])).'Y'));
            $diff = $dateNow->diff($dateBirthday, true)->days;
            if ($range == 0)
            {
                $condition = $dateBirthday == $dateNow;
            } else {
                $condition = $direction?(($dateBirthday > $dateNow) and ($diff <= $range)):(($dateBirthday < $dateNow) and ($diff <= $range));
            }

            if ($condition)
            {
                $data[] = [
                    "name"=>$p['name'],
                    "foto"=>$p['photo'],
                    "date"=>$p['birthday_date'],
                    "days"=>$diff,
                ];
            }
        }
        return $data;
    }
}