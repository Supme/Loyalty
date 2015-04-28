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

class news extends \Db
{

    function last($count, $from = 0, $sitemap_id = false)
    {
        if ( !$sitemap_id ) $sitemap_id = \Registry::get('_page')['id'];

        $data = $this->select(
            'core_content',
            [
                '[>]core_content_data' => ['id' => 'data_id'],
//                '[>]core_lang_locale' => ['lang_id' => ['id']],

            ],
            [
                'core_content.id(id)',
//                'core_lang_locale.code(lang)',
                'core_content_data.key(field)',
                'core_content_data.value(text)',
                'core_content.time(date)',
            ],
            [
                'AND' =>
                    [
                        'core_content.sitemap_id' => $sitemap_id,
                        'core_content.visible' => null,
                    ],
                "ORDER" => "core_content.time DESC",
                'LIMIT' => [$from, $count]
            ]
        );

        $news = [];
        foreach ( $data as $n)
        {
            $news[$n['id']][$n['field']] = $n['text'];
            $news[$n['id']]['date'] = $n['date'];
        }

        return $news;

    }

    function load($news_id, $sitemap_id = false)
    {
        if ( !$sitemap_id ) $sitemap_id = \Registry::get('_page')['id'];

        $data = $this->select(
            'core_content',
            [
                '[>]core_content_data' => ['id' => 'data_id'],
//                '[>]core_lang_locale' => ['lang_id' => ['id']],

            ],
            [
                'core_content.id(id)',
//                'core_lang_locale.code(lang)',
                'core_content_data.key(field)',
                'core_content_data.value(text)',
                'core_content.time(date)',
            ],
            [
                'AND' =>
                    [
                        'core_content.sitemap_id' => $sitemap_id,
                        'core_content.id' => $news_id,
                        'core_content.visible' => null,
                    ],
            ]
        );

        $news = [];
        foreach ( $data as $n)
        {
            $news[$n['field']] = $n['text'];
            $news['date'] = $n['date'];
        }

        return $news;
    }

    function edit($id, $title, $announce, $text = false, $date = false, $sitemap_id = false){

        if ( !$sitemap_id ) $sitemap_id = \Registry::get('_page')['id'];
        if( !$date ) $date = time();

        if ( !$id )
        {
            // Add news


            $last_id = $this->insert(
                'core_content',
                [
                    'sitemap_id' => $sitemap_id,
                    'lang_id' => 2,
                    'time' => $date
                ]);

            //Title
            $this->insert(
                'core_content_data',
                [
                    'data_id' => $last_id,
                    'key' => 'title',
                    'value' => $title
                ]
            );

            //Announce
            $this->insert(
                'core_content_data',
                [
                    'data_id' => $last_id,
                    'key' => 'announce',
                    'value' => $announce
                ]
            );

            //If exit full text
            if ( $text )
            {
                $this->insert(
                    'core_content_data',
                    [
                        'data_id' => $id,
                        'key' => 'text',
                        'value' => $text
                    ]
                );
            }
        } else {

         // Edit news

            $this->update(
                'core_content',
                [
                    'time' => $date
                ],
                [
                    'AND' =>
                    [
                        'id' => $id,
                        'sitemap_id' => $sitemap_id,
                        'lang_id' => 2,
                    ]

                ]);

            //Title
            $this->update(
                'core_content_data',
                [
                    'value' => $title
                ],
                [
                    'AND' =>
                        [
                            'data_id' => $id,
                            'key' => 'title',
                        ]
                ]
            );

            //Announce
            $this->update(
                'core_content_data',
                [
                    'value' => $announce
                ],
                [
                    'AND' =>
                        [
                            'data_id' => $id,
                            'key' => 'announce',
                        ]
                ]
            );

            //If exit full text
            if ( $text )
            {
                $this->update(
                    'core_content_data',
                    [
                        'value' => $text
                    ],
                    [
                        'AND' =>
                            [
                                'data_id' => $id,
                                'key' => 'text',
                            ]
                    ]
                );
            } else {
                $this->delete(
                    'core_content_data',
                    [
                    'AND' =>
                        [
                            'data_id' => $id,
                            'key' => 'text',
                        ]
                    ]);
            }
        }
    }
}
