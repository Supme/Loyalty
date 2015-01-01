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



class personalModel extends Model{

    function departments(){
        $result = [];
        $cities = $this->database->select( 'personalCity', '*' );
        foreach($cities as $city){
            $departments = $this->database->select(
                'personalDepartment',
                '*',
                ['city_id' => $city['id']]
            );
            foreach($departments as $department){
                $result[$department['id']] = $city['name'].' '.$department['name'];
            }
        }

        return $result;
    }

    function get(){
        $result = Cache::get('modPersonal');
        if(empty($result)){
            $city = $this->database->select( 'personalCity', '*' );
            foreach($city as $kc => $c){
                $departament = $this->database->select( 'personalDepartment', '*', ['city_id' => $c['id']] );
                foreach($departament as $kd => $d){
                    $people = $this->database->select('personalPeople', '*', ['department_id' => $d['id']]);
                    foreach($people as $kp => $p){
                        $result[$c['name']][$d['name']][$p['name']] = $p;
                    }
                }
            }
            Cache::set('modPersonal', $result);
        }

        return $result;
    }
}