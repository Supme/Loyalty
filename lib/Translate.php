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

            'af' => 'Afrikaans',
            'ar' => 'Arabic',
            'az' => 'Azerbaijani',
            'bg' => 'Bulgarian',
            'be' => 'Belarusian',
            'bn' => 'Bengali',
            'br' => 'Breton',
            'bs' => 'Bosnian',
            'ca' => 'Catalan',
            'cs' => 'Czech',
            'cy' => 'Welsh',
            'da' => 'Danish',
            'de' => 'German',
            'el' => 'Greek',
            'en' => 'English',
            'en-gb' => 'British English',
            'eo' => 'Esperanto',
            'es' => 'Spanish',
            'es-ar' => 'Argentinian Spanish',
            'es-mx' => 'Mexican Spanish',
            'es-ni' => 'Nicaraguan Spanish',
            'es-ve' => 'Venezuelan Spanish',
            'et' => 'Estonian',
            'eu' => 'Basque',
            'fa' => 'Persian',
            'fi' => 'Finnish',
            'fr' => 'French',
            'fy-nl' => 'Frisian',
            'ga' => 'Irish',
            'gl' => 'Galician',
            'he' => 'Hebrew',
            'hi' => 'Hindi',
            'hr' => 'Croatian',
            'hu' => 'Hungarian',
            'ia' => 'Interlingua',
            'id' => 'Indonesian',
            'is' => 'Icelandic',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'ka' => 'Georgian',
            'kk' => 'Kazakh',
            'km' => 'Khmer',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'lb' => 'Luxembourgish',
            'lt' => 'Lithuanian',
            'lv' => 'Latvian',
            'mk' => 'Macedonian',
            'ml' => 'Malayalam',
            'mn' => 'Mongolian',
            'my' => 'Burmese',
            'nb' => 'Norwegian Bokmal',
            'ne' => 'Nepali',
            'nl' => 'Dutch',
            'nn' => 'Norwegian Nynorsk',
            'os' => 'Ossetic',
            'pa' => 'Punjabi',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'pt-br' => 'Brazilian Portuguese',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'sr-latn' => 'Serbian Latin',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'tt' => 'Tatar',
            'udm' => 'Udmurt',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'vi' => 'Vietnamese',
            'zh-cn' => 'Simplified Chinese',
            'zh-tw' => 'Traditional Chinese'
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