<?php
/**
 * @package Loyality Portal
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

function autoload($class) {

    if (strpos($class, 'Model', 1) === false) { //ToDo заменить на regexp

        // load lib class
        if (file_exists(LIBS_PATH . $class . ".php")) {
            require LIBS_PATH . $class . ".php";
        } else {
            exit ('The file ' . $class . '.php is missing in the libs folder.');
        }

    } else {

        // load model class
        if (file_exists(MODELS_PATH . $class . ".php")) {
            require MODELS_PATH . $class . ".php";
        } else {
            exit ('The file ' . $class . '.php is missing in the models folder.');
        }

    }

}

spl_autoload_register("autoload");
