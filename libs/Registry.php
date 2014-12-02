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
}
?>