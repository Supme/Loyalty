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

/**
 * Class Cache
 *
 *
 *
 */

class Cache {

    protected static $memcached = false;
    protected static $cache = false;
    protected static $log;

    /**
     * Защита от создания экземпляров статического класса
     */
    protected function __construct() {}
    protected function __clone() {}

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public static function set($name, $value)
    {
        if(isset(self::$log['set']))
            ++self::$log['set'];
        else
            self::$log['set'] = 1;

        $metod = strtolower(CACHE_TYPE);
        if(!in_array($metod, ['file', 'memcached'])) $metod = 'file';
        $function = $metod.'Set';

        return self::$function($name, $value, time()+CACHE_EXPIRATION);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        if(isset(self::$log['get']))
            ++self::$log['get'];
        else
            self::$log['get'] = 1;

        $metod = strtolower(CACHE_TYPE);
        if(!in_array($metod, ['file', 'memcached'])) $metod = 'file';
        $function = $metod.'Get';

        return self::$function($name);

    }

    /**
     * @return string
     */
    public static function log()
    {
        return
            "Cache: set ".(isset(self::$log['set'])?self::$log['set']:0).
            " ".
            "get ".(isset(self::$log['get'])?self::$log['get']:0);
    }

    /**
     * @return bool|Memcached
     */
    private  static function memcached()
    {
        if(!self::$memcached){
            self::$memcached = new Memcached();
            self::$memcached->addServer(MEMCACHED_SERVER, MEMCACHED_PORT);
        }
        return self::$memcached;
    }

    /**
     * @param $name
     * @param $value
     * @param $expiration
     * @return bool
     */
    private static function memcachedSet($name, $value, $expiration)
    {
        return self::memcached()->set($name, $value, $expiration);
    }

    /**
     * @param $name
     * @return mixed
     */
    private static function memcachedGet($name)
    {
        return self::memcached()->get($name);
    }

    /**
     * @param $name
     * @param $value
     * @param $expiration
     * @return bool|mixed
     */
    private static function fileSet($name, $value, $expiration){
        self::$cache = json_decode(file_get_contents(CACHE_FILE), true);
        self::$cache['nm'][$name] = $value;
        self::$cache['ex'][$name] = $expiration;
        file_put_contents(CACHE_FILE, json_encode(self::$cache));
        return self::$cache;
    }

    /**
     * @param $name
     * @return null
     */
    private static function fileGet($name){
        if(self::$cache == false){
            self::$cache = json_decode(file_get_contents(CACHE_FILE), true);
        }
        if(self::$cache['ex'][$name] >= time())
            return self::$cache['nm'][$name];
        else
            return null;
    }

} 