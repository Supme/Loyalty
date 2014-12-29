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

        $metod = strtolower(Registry::get('_config')['cache']['type']);
        if(!in_array($metod, ['file', 'memcached'])) $metod = 'file';
        $function = $metod.'Set';

        return self::$function(Registry::get('_config')['cache']['salt'].$name, $value, time()+Registry::get('_config')['cache']['expiration']);
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

        $metod = strtolower(Registry::get('_config')['cache']['type']);
        if(!in_array($metod, ['file', 'memcached'])) $metod = 'file';
        $function = $metod.'Get';

        return self::$function(Registry::get('_config')['cache']['salt'].$name);

    }

    /**
     * @return mixed
     */
    public static function clear()
    {
        if(isset(self::$log['clear']))
            ++self::$log['clear'];
        else
            self::$log['clear'] = 1;

        $metod = strtolower(Registry::get('_config')['cache']['type']);
        if(!in_array($metod, ['file', 'memcached'])) $metod = 'file';
        $function = $metod.'Clear';

        return self::$function();
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

    //--------------------------------- Memcached begin ----------------------------------------------------------------

    /**
     * @return bool|Memcached
     */
    private  static function memcached()
    {
        if(!self::$memcached){
            self::$memcached = new Memcached();
            self::$memcached->addServer(Registry::get('_config')['cache']['memcached_server'], Registry::get('_config')['cache']['memcached_port']);
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

    private static function memcachedClear()
    {
        return self::memcached()->flush();
    }
    //--------------------------------- Memcached end ------------------------------------------------------------------


    //--------------------------------- Filecache begin ----------------------------------------------------------------

    /**
     * @param $name
     * @param $value
     * @param $expiration
     * @return bool|mixed
     */
    private static function fileSet($name, $value, $expiration)
    {
        self::$cache = json_decode(file_get_contents(Registry::get('_config')['cache']['file']), true);
        self::$cache['nm'][$name] = $value;
        self::$cache['ex'][$name] = $expiration;
        file_put_contents(Registry::get('_config')['cache']['file'], json_encode(self::$cache));
        return self::$cache;
    }

    /**
     * @param $name
     * @return null
     */
    private static function fileGet($name)
    {
        if(self::$cache == false){
            self::$cache = json_decode(file_get_contents(Registry::get('_config')['cache']['file']), true);
        }
        if(isset(self::$cache['ex'][$name]) and self::$cache['ex'][$name] >= time()){
            return self::$cache['nm'][$name];
        }
        else {
            self::$cache = json_decode(file_get_contents(Registry::get('_config')['cache']['file']), true);
            unset(self::$cache['ex'][$name]);
            unset(self::$cache['nm'][$name]);
            file_put_contents(Registry::get('_config')['cache']['file'], json_encode(self::$cache));
            return null;
        }
    }

    private static function fileClear()
    {
        self::$cache['ex'] = [];
        self::$cache['nm'] = [];
        return file_put_contents(Registry::get('_config')['cache']['file'], json_encode(self::$cache));
    }

    //--------------------------------- Filecache end ------------------------------------------------------------------
} 