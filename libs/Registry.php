<?php
/**
 * Created by PhpStorm.
 * User: aagafonov
 * Date: 18.06.14
 * Time: 15:11
 *
 * Статический класс registry
 */

class Registry
{
    /**
     * Статическое хранилище для данных
     * ToDo использовать кэширование, но учитывая работу memcache с ресурсами (коннекты с базой, например)
     */
    protected static $store = array();

    /**
     * Защита от создания экземпляров статического класса
     */
    protected function __construct() {}
    protected function __clone() {}

    /**
     * Проверяет существуют ли данные по ключу
     *
     * @param string $name
     * @return bool
     */
    public static function exists($name)
    {
        return isset(self::$store[$name]);
    }

    /**
     * Возвращает данные по ключу или null, если не данных нет
     *
     * @param string $name
     * @return unknown
     */
    public static function get($name)
    {
        return (isset(self::$store[$name])) ? self::$store[$name] : null;
    }

    /**
     * Сохраняет данные по ключу в статическом хранилище
     *
     * @param string $name
     * @param unknown $obj
     * @return unknown
     */
    public static function set($name, $obj)
    {
        return self::$store[$name] = $obj;
    }

    /**
     * Упаковка CSS файлов в переменную
     *
     * @param $css
     */
    public static function css($css){
        if (!isset(self::$store['_css'])) self::$store['_css'] = [];
        if (is_array($css)){
            foreach ($css as $file){
                array_push(self::$store['_css'],$file);
                //self::$store['_css'] .= "/*".$file."*/\n".self::compressCss($file)."\n\r";
            }
        } else {
            array_push(self::$store['_css'],$css);
            //self::$store['_css'] .= "/*".$css."*/\n".self::compressCss($css)."\n\r";
        }
    }

    public static function js($js){
        if (!isset(self::$store['_js'])) self::$store['_js'] = [];
        if (is_array($js)){
            foreach ($js as $file){
                array_push(self::$store['_js'],$file);
                //self::$store['_js'] .= "/*".$file."*/\n".self::compressJs($file)."\n\r";
            }
        } else {
            array_push(self::$store['_js'],$js);
            //self::$store['_js'] .= "/*".$js."*/\n".self::compressJs($js)."\n\r";
        }
    }

    private static function compressCss($file){
        if(file_exists($file)){
            return
                str_replace(
                    array("\r\n", "\r", "\n", "\t", 'Â  ', 'Â Â Â  ', 'Â Â Â  '),
                    '',
                    preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '',
                        file_get_contents($file)
                    )
                );

        } else {
            return "";
        }
    }

    private static function compressJs($file){
        if(file_exists($file)){
            return
                str_replace(
                    array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '),
                    '',
                        file_get_contents($file)
                );

        } else {
            return "";
        }
    }

}
?>