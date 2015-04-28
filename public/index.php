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

/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard errors in production
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);


try{
    // what time is it?
    $start = microtime(true);

    require '../vendor/autoload.php';

    // load application config
    if(file_exists('../config.ini'))
        Registry::set('_config', array_merge(parse_ini_file('../config.dist.ini', true),parse_ini_file('../config.ini', true)));
    else
        Registry::set('_config', parse_ini_file('../config.dist.ini', true));

    // start the application
    $app = new Application();

} catch (Exception $e) {
    echo "<pre>Error information:\n";
    print $e->getMessage();
    echo "</pre>";
}

// debug info
if(Registry::get('_config')['site']['debug']){

    echo "<script type='text/javascript'>function debug_show(){ $('.debug-information').toggle( function(){ $(this).siblings('.debug-information.hide').stop(false, true).slideDown(500);}, function(){ $(this).siblings('.debug-information.hide').stop(false, true).slideUp(500);})};</script>";
    echo "<p style='text-align: right; font-size: xx-small'><a onclick='debug_show();'>Debug</a></p>";
    echo "<div class='debug-information' style='display: none;'>\n<pre>";
    printf('Scripts are executed %.4F seconds.', microtime(true) - $start);
    var_dump( Cache::log() );
    echo "Application information";
    var_dump( array_reverse ( Registry::log() ) );
    echo "User information:\n";
    $user = new \Auth();
    var_dump($user->getRight());
    echo "</pre>\n</div>";
}
