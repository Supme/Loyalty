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

namespace App\Core\Model;

class content extends \Db
{

    function load( $sitemap_id = false ){

        if ( !$sitemap_id ) $sitemap_id = \Registry::get('_page')['id'];

        $last_time = $this->max(
            'core_content',
            'time',
            [
                'AND' =>
                    [
                        'core_content.sitemap_id' => $sitemap_id,
                        'core_content.visible' => null
                    ]
            ]
        );

        $data = $this->select(
            'core_content',
            [
                '[>]core_content_data' => ['id' => 'data_id'],
//                '[>]core_lang_locale' => ['lang_id' => ['id']],

            ],
            [
                'core_content.id(id)',
//                'core_lang_locale.code(lang)',
                'core_content_data.key(position)',
                'core_content_data.value(text)',
                'core_content.time(time)',
            ],
            [
                'AND' =>
                    [
                        'core_content.sitemap_id' => $sitemap_id,
                        'core_content.visible' => null,
                        'core_content.time' => $last_time,
                    ]
            ]
        );

        return $data;
    }

    function edit($position, $text, $sitemap_id = false){

        if ( !$sitemap_id ) $sitemap_id = \Registry::get('_page')['id'];

        $data = $this->load($sitemap_id);

        $last_id = $this->insert(
            'core_content',
            [
                'sitemap_id' => $sitemap_id,
                'lang_id' => 2,
                'time' => time()
            ]);

        $updated = false;
        foreach($data as $d)
        {
            if ( $d['position'] == $position)
            {
                $d['position'] = $position;
                $d['text'] = $text;
                $updated = true;
            }
            $this->insert(
                'core_content_data',
                [
                    'data_id' => $last_id,
                    'key' => $d['position'],
                    'value' => $d['text']
                ]
            );
        }

        if ( !$updated )
        {
            $this->insert(
                'core_content_data',
                [
                    'data_id' => $last_id,
                    'key' => $position,
                    'value' => $text
                ]
            );
        }

    }

}