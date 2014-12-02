<?php

class Model
{
    /**
     * @var null Database Connection
     */
    public $db = null;

    function __construct()
    {
        $this->db = Registry::get('_db');


        /**
         * Medoo
         */
        if (DB_TYPE == 'sqlite'){
            $connect = [
                'database_type' => DB_TYPE,
                'database_file' => DB_HOST,
                'charset'       =>  'utf8',
                ];
        } else {
            $connect = [
                'database_type' =>  DB_TYPE,
                'database_name' =>  DB_NAME,
                'server'        =>  DB_HOST,
                'port'          =>  DB_PORT,
                'username'      =>  DB_USER,
                'password'      =>  DB_PASS,
                'charset'       =>  'utf8',
                'option'        =>  [ PDO::ATTR_CASE => PDO::CASE_NATURAL ]
            ];
        }
        $this->database = new medoo($connect);
    }
}
