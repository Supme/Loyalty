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

// what time is it?
$start = microtime(true);

require '../vendor/autoload.php';

// load application config
require '../config.php';

// start the application
$app = new Application();

if(DEBUG){
    echo "<pre>Debug information:\n";
    printf('Scripts are executed %.4F seconds.', microtime(true) - $start);
    Registry::log(Cache::log());
    var_dump(array_reverse ( Registry::log() ) );
    echo "</pre>";
}


