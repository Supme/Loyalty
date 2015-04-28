<?php
/**
 * @package Loyality Portal
 * @author Supme
 * @copyright Supme 2014
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

namespace App\Personal\Controller;

class main extends \Controller {

    protected
        $personal;

    function __init()
    {
        $this->personal = new \App\Personal\Model\personal();
    }

    function main($params)
    {

        if (isset($params[0])){
            switch ($params[0]){
                case 'edit':
                    \Registry::$store['_page']['view'] = 'edit';
                    $this->edit($params);
                    break;
                case 'upload':
                    $this->upload();
                    break;
                case 'crop':
                    $this->crop();
                    break;
                default:
                    $this->index($params);
                    break;
            }
            exit;
        } else {
            $this->index($params);
        }
    }

    private function index($params)
    {
        $user = new \Auth();
        // add submenu
        if ($user->canUpdate())
            \Registry::menu([
                'Add personal' => [
                    'href'=>'../'.\Registry::get('_page')['segment'].'/edit/',
                ]
            ]);

        $this->render([
            'result' => $this->personal->load(),

        ]);
    }

    private function edit($params)
    {
        $user = new \Auth();
        if(!$user->canUpdate()) header("Location: /error/403");

        \Registry::css([
            "/assets/imguploader/css/jquery.Jcrop.css",
            "/assets/imguploader/css/fileuploader.css",
        ]);

        \Registry::js([
            "/assets/imguploader/js/fileuploader.js",
            "/assets/imguploader/js/jquery.Jcrop.js",
            "/assets/imguploader/js/image-uploader.js",
        ]);

        $data = [];
        $data['id'] = '';

        if(isset($params[1]))
        {
            $data['id'] = (int)$params[1];
            $data = $this->personal->personal($data['id']);
            $data['action'] = 'Edit';
        } else {
            $data['action'] = 'Add';
        }

        if (isset($_POST['submit_add_personal'])){

            $valid = [];
            if(!\Validator::numeric()->validate($_REQUEST['department']))
            {
                $valid[] = 'Department has error data';
            } else {
                $data['department_id'] = $_REQUEST['department'];
            }

            if(isset($_REQUEST['name']))
            {
                $data['name'] = html_entity_decode($_REQUEST['name']);
            }
            if(isset($_REQUEST['photo']))
            {
                $data['photo'] = str_replace('/files/personal/', '', html_entity_decode($_REQUEST['photo']));
            }
            if(isset($_REQUEST['position']))
            {
                $data['position'] = html_entity_decode($_REQUEST['position']);
            }
            if(isset($_REQUEST['function']))
            {
                $data['function'] = html_entity_decode($_REQUEST['function']);
            }
            if(!empty($_REQUEST['email']) and !\Validator::email()->validate($_REQUEST['email']))
            {
                $valid[] = 'Email has error data';
            } else {
                $data['email'] = $_REQUEST['email'];
            }
            if(isset($_REQUEST['birthday']))
            {
                $data['birthday'] = html_entity_decode($_REQUEST['birthday']);
            }
            if(isset($_REQUEST['telephone_internal']))
            {
                $data['telephone_internal'] = html_entity_decode($_REQUEST['telephone_internal']);
            }
            if(isset($_REQUEST['telephone_mobile']))
            {
                $data['telephone_mobile'] = html_entity_decode($_REQUEST['telephone_mobile']);
            }
            if(isset($_REQUEST['telephone_external']))
            {
                $data['telephone_external'] = html_entity_decode($_REQUEST['telephone_external']);
            }
            if(isset($_REQUEST['change']))
            {
                $data['change'] = html_entity_decode($_REQUEST['change']);
            }

            if (count($valid) == 0)
            {
                $this->personal->edit($data);
                \Cache::clear();
                header("Location: ../");
            } else {
                \Registry::notification([
                    'warning' => $valid
                ]);
            }

        } elseif(isset($_POST['submit_del_personal'])) {
            $this->personal->del($data['id']);
            \Cache::clear();
            header("Location: ../");
        }

        $this->render([
            'departments' => $this->personal->departments(),
            'data' => $data,
        ]);

    }

    private function upload()
    {

        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        // max file size in bytes
        $sizeLimit = 1 * 1024 * 1024;

        $uploader = new \App\Personal\Helpers\fileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload('../data/files/personal/', TRUE);

        // to pass data through iframe you will need to encode all html tags
        if (isset($result['success']) && $result['success']) {
            $file = $result['file'];
            $result['url'] = '/files/personal/' . $file;
            unset($result['file']);

            $imgsize = getimagesize('../data/files/personal/' . $file);
            $result['width'] = $imgsize[0];
            $result['height'] = $imgsize[1];
        }

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    private function crop()
    {

        $x1 = $_POST['x1'];
        $y1 = $_POST['y1'];
        $x2 = $_POST['x2'];
        $y2 = $_POST['y2'];

        $tw = $_POST['target_width'];
        $th = $_POST['target_height'];

        $url = $_POST['url'];
        $file = str_replace('/files/personal/', '../data/files/personal/', $url);

        $cropped_file = $file . '_'.$tw.'x'.$th.'.jpg';

        $cropped = imagecreatetruecolor($tw, $th);

        $original = file_get_contents($file);
        $original = imagecreatefromstring($original);

        imagecopyresampled ($cropped, $original, 0, 0, $x1, $y1, $tw, $th, $x2-$x1, $y2-$y1);
        imagejpeg($cropped, $cropped_file, 80);

        $result = array(
            'success' => true,
            'url' => str_replace('../data/files/personal/', '/files/personal/', $cropped_file),
            'width' => $tw,
            'height' => $th
        );

        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
}