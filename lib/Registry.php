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
     */
    public static $store = [];
    protected static $notification = [];
    protected static $log = [];

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
     * Возвращает данные по ключу или null, если данных нет
     *
     * @param string $name
     * @return unknown
     */
    public static function get($name)
    {
        if ($name != '*') return (isset(self::$store[$name])) ? self::$store[$name] : null; else return self::$store;
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

    public static function notification($notification = false)
    {
        if(isset($_COOKIE['notification']) and is_array($_COOKIE['notification'])){
            $note = $_COOKIE['notification'];
        } else {
            $note = [];
        }

        if($notification != false){
            if(is_array($notification)){
                $note = array_merge($note, $notification);
            } else {
                $note['info'][] = $notification;
            }
            $_COOKIE['notification'] = $note;

            return true;
        } else {
            unset($_COOKIE['notification']);
            return $note;
        }
    }

    /**
     * @param $log string
     * @return array|bool
     */
    public static function log($log = false)
    {
        if($log){
            if(is_array($log)){
                foreach($log as $l){
                    self::$log[] = $l;
                }
            } else {
                self::$log[] = $log;
            }
            return true;
        }
        else return self::$log;
    }


    /**
     * Add css in page
     *
     * @param $css
     */
    public static function css($css){
        if (!isset(self::$store['_css'])) self::$store['_css'] = [];
        if (is_array($css))
        {
            foreach ($css as $file)
            {
                array_push(self::$store['_css'],$file);
                //self::$store['_css'] .= "/*".$file."*/\n".self::compressCss($file)."\n\r";
            }
        } else {
            array_push(self::$store['_css'],$css);
            //self::$store['_css'] .= "/*".$css."*/\n".self::compressCss($css)."\n\r";
        }
    }

    /**
     * Add javascript in page
     *
     * @param $css
     */
    public static function js($js){
        if (!isset(self::$store['_js'])) self::$store['_js'] = [];
        if (is_array($js))
        {
            foreach ($js as $file)
            {
                array_push(self::$store['_js'],$file);
                //self::$store['_js'] .= "/*".$file."*/\n".self::compressJs($file)."\n\r";
            }
        } else {
            array_push(self::$store['_js'],$js);
            //self::$store['_js'] .= "/*".$js."*/\n".self::compressJs($js)."\n\r";
        }
    }

    /**
     * Add menu links in page
     *
     * @param array $items
     */
    public static function menu($items){
        if (!isset(self::$store['_menu'])) self::$store['_menu'] = [];
        if (is_array($items))
        {
            foreach ($items as $i => $item)
            {
                self::$store['_menu'][$i] = $item;
                /*foreach ($item as $key => $value)
                {
                    array_push(self::$store['_menu'][$i], [$key => $value]);
                }*/
            }
        }
    }
}
?>