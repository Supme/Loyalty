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

namespace App\Personal;

class init extends \Db
{
    function isInstalled()
    {
        return $this->database->count("sqlite_master",["name[~]" => "personal_%"])==3?true:false;
    }

    function install()
    {
        $this->database->query('
            CREATE TABLE "personal_city" (
              "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              "name" text NOT NULL
            )
        ');

        $this->database->query('
            CREATE TABLE "personal_department" (
              "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              "city_id" integer NOT NULL,
              "name" text NOT NULL,
              FOREIGN KEY ("city_id") REFERENCES "personal_city" ("id") ON DELETE CASCADE ON UPDATE CASCADE
              )
        ');
        $this->database->query('
            CREATE TABLE "personal_people" (
              "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
              "department_id" integer NOT NULL,
              "name" text NOT NULL,
              "photo" text NULL,
              "position" text NULL,
              "function" text NULL,
              "email" text NULL,
              "birthday" text NULL,
              "telephone_internal" text NULL,
              "telephone_mobile" text NULL,
              "telephone_external" text NULL,
              "change" text NULL,
              FOREIGN KEY ("department_id") REFERENCES "personal_department" ("id") ON DELETE NO ACTION ON UPDATE CASCADE
              )
');
        return "Ничего не сделано для инсталяции";
    }
} 