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

class Db extends medoo {
    /**
     * @var null Database Connection
     */
    private $db = null;

    public function __construct()
    {
        if (Registry::get('_config')['db']['type'] == 'sqlite'){
            $options = [
                'database_type' => Registry::get('_config')['db']['type'],
                'database_file' => Registry::get('_config')['db']['host'],
                'charset'       =>  'utf8',
            ];
        } else {
            $options = [
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
        parent::__construct($options);
    }

    function structure($tableLike = '%')
    {
        $dump = '';
        if (Registry::get('_config')['db']['type'] == 'sqlite'){
            $sql = $this->query("SELECT sql FROM sqlite_master WHERE name <> 'sqlite_sequence' AND name LIKE '$tableLike'");
            foreach($sql as $tables)
            {
                $dump .= $tables['sql']."\n";
            };
        }

        return $dump;
    }

    function tables($tableLike = '%')
    {
        $count = false;
        if (Registry::get('_config')['db']['type'] == 'sqlite') {
            $count = $this->count("sqlite_master", ["name[~]" => "$tableLike"]);
        }

        return $count;
    }

    function dumpData($table = '*')
    {

    }

    function __destruct()
    {
        Registry::log($this->log());
    }
}