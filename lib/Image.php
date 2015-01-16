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

class Image {

    public static function resizer(){
        define('FILE_CACHE_DIRECTORY', \Registry::get('_config')['path']['TimThumb_cache']);
        define('LOCAL_FILE_BASE_DIRECTORY', \Registry::get('_config')['path']['share_files']);
        $timthumb = new timthumb();
        $timthumb->start();
    }

} 