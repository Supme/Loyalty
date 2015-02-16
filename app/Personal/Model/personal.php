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
                    $people = $this->select('personal_people', '*', ['department_id' => $d['id'], "ORDER" => "name ASC",]);
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
}