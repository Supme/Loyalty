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

namespace App\Core\Controller;

class helpers extends \Controller {

    function resizer(){
        if (strpos($_SERVER['QUERY_STRING'], '../') !== false) {
            header("Location: /error/400");
            exit;
        }
        \Image::resizer();
        /*
        $file = isset($_REQUEST['src'])?\Registry::get('_config')['path']['share_files'].'/'.$_REQUEST['src']:'../data/no_image.jpg';
        $width = isset($_REQUEST['w'])?$_REQUEST['w']:0;
        $height = isset($_REQUEST['h'])?$_REQUEST['h']:0;
        $img = new \Image();
        $resized = $img->resize($file, $width, $height);
        $img->send($resized);
        */
    }

    function error($params){
        // HTTP status codes (RFC 2616)
        $HTTPcode = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authorative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
        ];

        header('HTTP/1.0 '.$params[0].' '.$HTTPcode[$params[0]]);
        echo 'Error: '.$HTTPcode[$params[0]];
    }
}
