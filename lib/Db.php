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

class Db {
    /**
     * @var null Database Connection
     */
    public $db = null;

    public function __construct()
    {

        /**
         * Medoo
         */
        if (Registry::get('_config')['db']['type'] == 'sqlite'){
            $connect = [
                'database_type' => Registry::get('_config')['db']['type'],
                'database_file' => Registry::get('_config')['db']['host'],
                'charset'       =>  'utf8',
            ];
        } else {
            $connect = [
                'database_type' =>  Registry::get('_config')['db']['type'],
                'database_name' =>  Registry::get('_config')['db']['name'],
                'server'        =>  Registry::get('_config')['db']['host'],
                'port'          =>  Registry::get('_config')['db']['port'],
                'username'      =>  Registry::get('_config')['db']['username'],
                'password'      =>  Registry::get('_config')['db']['password'],
                'charset'       =>  'utf8',
                'option'        =>  [ PDO::ATTR_CASE => PDO::CASE_NATURAL ]
            ];
        }
        $this->database = new medoo($connect);
    }

    function __destruct()
    {
        Registry::log($this->database->log());
    }

    function dumpTables()
    {
        $dump = '';
        if (Registry::get('_config')['db']['type'] == 'sqlite'){
            $sql = $this->database->query("SELECT sql FROM sqlite_master WHERE name <> 'sqlite_sequence' AND name LIKE 'core_%'");
            foreach($sql as $tables)
            {
                $dump .= $tables['sql']."\n";
            };
        }



        return $dump;
    }

    function dumpData($table = '*')
    {

    }
}