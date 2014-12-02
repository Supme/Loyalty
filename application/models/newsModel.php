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

class newsModel extends Model
{

    function last($from = 0, $count = 10){

        return $this->database->select(
            'news',
            [
                'id',
                'title',
                'announce',
                'text',
                'date'
            ],
            [
                'AND' => [
                    'smapId' => Registry::get('_page')['id'],
                    'date[<]' => 'NOW()',
                ],
                'ORDER' => 'date DESC',
                'LIMIT' => [$from, $count]
            ]
            );
    }

    function get($id){

        return $this->database->select(
            'news',
            [
                'id',
                'title',
                'announce',
                'text',
                'date'
            ],
            [
                'AND' => [
                    'id' => $id,
                    'smapId' => Registry::get('_page')['id']
                ]
            ]
        )[0];

    }

    function edit($title, $announce, $text, $date = FALSE){

        if(!$date) $date = time();
        $query = $this->db->prepare('INSERT INTO news (smapId, title, announce, text, date) VALUES (?, ?, ?, ?)');
        $query->execute([Registry::get('_page')['id'], $title, $announce, $text, $date]);

    }
}