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

namespace App\Gallery\Controller;

class main extends \Controller
{
    function index($params)
    {
        $data = new \App\Gallery\Model\data();
        $folder = urldecode(str_replace('/'.\Registry::get('_page')['segment'], '', $_SERVER['REQUEST_URI']));
        if (count($params) != 0 ) {
            array_pop($params);
            $back = '/' . implode('/', $params);
        } else {
            $back = '';
        }

        \Registry::css([
            "/assets/bootstrap/3.1.1/css/bootstrap.min.css",
            "/assets/lightbox/css/jquery.lightbox-0.5.css",
        ]);

        \Registry::js([
            "/assets/jquery/jquery-2.1.3.min.js",
            "/assets/bootstrap/3.1.1/js/bootstrap.min.js",
            "/assets/lightbox/js/jquery.lightbox-0.5.pack.js"
        ]);


        $this->render([
            'root' => \Registry::get('_page')['segment'],
            'back' => $back,
            'folders' => $data->getFolders($folder),
            'pictures' => $data->getPictures($folder),
        ]);
    }

}