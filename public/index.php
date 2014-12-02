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

// load the (optional) Composer auto-loader
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

// load application config (error reporting etc.)
require '../application/config/config.php';

// The auto-loader to load project classes
require '../libs/_autoload.php';

// start the application
$app = new Application();
