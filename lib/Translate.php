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

class Translate extends Model
{

    private static $strs;
    private static $langList;
    private static $currlang = 'en';

    function __construct()
    {
        parent::__construct();

        self::$langList = Cache::get('_lang');
        if(empty(self::$langList)){
            self::$langList = $this->database->select(
                'core_lang_locales',
                ['code', 'name']
            );
            Cache::set('_lang', self::$langList);
        }

        self::$strs = Cache::get('_tr');
        if(empty(self::$strs)){
            foreach(self::$langList as $lang){
                self::$strs[$lang['code']] = [];
                foreach($this->database->select(
                    'core_lang_translation',
                    [
                        '[>]core_lang_locales' => ['locales_id' => 'id'],
                    ],
                    [
                        'core_lang_translation.key',
                        'core_lang_translation.value'
                    ],
                    [
                        'core_lang_locales.code' => $lang
                    ]
                ) as $tr){
                    self::$strs[$lang['code']][$tr['key']] = $tr['value'];
                }
            }
            Cache::set('_tr', self::$strs);
        }
    }

    public static function setDefaultLang($lang)
    {
        self::$currlang = $lang;
    }

    public static function langName($lang)
    {
        $langName = [
            'en' => 'English',
            'ru' => 'Русский',
            'ua' => 'Український'
            //ToDo more lang
        ];

        return $langName[$lang];
    }

    public static function get($key, $lang="")
    {
        if ($lang == "") $lang = self::$currlang;
        if(isset(self::$strs[$lang][$key])){
            $str = self::$strs[$lang][$key];
        } else {
            $str =
                isset(self::$strs[Registry::get('_config')['site']['lang']][$key])?
                self::$strs[Registry::get('_config')['site']['lang']][$key]:
                "$lang.$key";
        }
        return $str;
    }

    public static function langList()
    {
        return self::$langList;
    }

    public static function &getAllStrings()
    {
            return self::$strs[self::$currlang];
    }
}