<?php

class Model
{
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

    public function randomString($lenght = 128)
    {
        $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $num = 0;
        $string = "";
        while ($num < $lenght) {
            $string .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
            $num++;
        }
        return $string;
    }

    function __destruct()
    {
        Registry::log($this->database->log());
    }
}
