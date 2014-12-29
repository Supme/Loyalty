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

class Helpers extends Controller {

    function resizer(){
        define('FILE_CACHE_DIRECTORY', Registry::get('_config')['path']['TimThumb_cache']);
        define('LOCAL_FILE_BASE_DIRECTORY', Registry::get('_config')['path']['share_files']);

        if (strpos($_SERVER['QUERY_STRING'], '../') !== false) {
            header("Location: /error/400");
            exit;
        }

        $controller = new timthumb();
        $controller->start();
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

        echo 'Error: '.$HTTPcode[$params[0]];
    }

    function files($params){

        $path = str_replace('..','',implode('/',$params));

        if(!isset($params[0])) header("Location: /error/404");
        if(file_exists(Registry::get('_config')['path']['share_files'].$path)){
            $file = new Download(Registry::get('_config')['path']['share_files'].$path);
            $file->download_file();
        } else {
            $model = new helpersModel();
            if ($real = $model->getFileHash($path)){
                $name = substr(strrchr($real, "/"), 1);
                $file = new Download(Registry::get('_config')['path']['private_files'].$real, $name);
                $file->download_file();
            } else {
                header("Location: /error/404");
            }
        }
    }

}
