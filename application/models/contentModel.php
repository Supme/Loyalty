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

class contentModel extends Model
{

    function get(){

        return $this->database->select(
            'content',
            ['id', 'lang', 'position', 'text'],
            ['smapId' => Registry::get('_page')['id']]
        );

    }

    function edit($position, $text){

        $id = $this->database->select(
            'content',
            'id',
            [
                'AND' => [
                    'smapId' => Registry::get('_page')['id'],
                    'position' => $position
                ]

            ]
        )[0];

        if($id) {
            $this->database->update(
                'content',
                ['text' => $text],
                ['id' => $id]
            );
        } else {
            $this->database->insert(
                'content',
                [
                    'smapId' => Registry::get('_page')['id'],
                    'lang' => 1, // Todo реализовать мультиязычность
                    'position' => 1, // ToDo косяк тут, если это не первый блок на странице
                    'text' => $text
                ]
            );
        }
    }

}