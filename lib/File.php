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

const TFOLDER = 'core_fs_folder';
const TFILE = 'core_fs_file';

class File extends Db {

    public function getFolder()
    {
        $folders = $this->select(TFOLDER, ['id', 'path'], ['trash[!]' => true]);
        $i = 0;
        $array = [];
        foreach($folders as $folder)
        {
            $array[$i]['p'] = $folder['path'];
            $array[$i]['f'] = $this->getCountChildFile($folder['id']);
            $array[$i]['d'] = $this->getCountChildFolder($folder['id']);
            ++$i;

        }
        return $array;
    }

    public function getFolderFile($path)
    {
        $files = $this->select(TFILE, ['name', 'size', 'modification', 'width', 'height'], ['AND' => ['folder_id' => $this->getPathId($path), 'trash[!]' => true]]);
        $i = 0;
        $array = [];
        foreach($files as $file)
        {
            $array[$i]['p'] = $path.'/'.$file['name'];
            $array[$i]['s'] = $file['size'];
            $array[$i]['t'] = $file['modification'];
            $array[$i]['w'] = $file['width'];
            $array[$i]['h'] = $file['height'];
            ++$i;
        }
        return $array;
    }

    public function isFolder($path, $name)
    {
        return $this->count(TFOLDER, ['AND' => ['name' => $name, 'pid' => $this->getPathId($path)]]);
    }

    public function createFolder($path, $name)
    {
        if($this->isFolder($path, $name) != 0) return false;
        $this->insert(TFOLDER, ['pid' => $this->getPathId($path), 'path' => $path.'/'.$name, 'name' => $name, 'trash' => false]);
        return true;
    }

    public function renameFolder($path, $name)
    {
        $parent = $this->getParentFolder($this->getPathId($path));
        $this->update(TFOLDER, ['name' => $name, 'path' => $parent.'/'.$name], ['path' => $path]);
        return true;
    }

    public function deleteFolder($path)
    {
        $parents = $this->getChildFolder($this->getPathId($path));
        foreach($parents as $parent)
        {
            $this->deleteFolder($parent);
        }
        $files = $this->getFolderFile($path);
        foreach($files as $file)
        {
            $this->deleteFile($file['p']);
        }
        $this->update(TFOLDER,['trash' => true], ['path' => $path]);
        return true;
    }

    public function moveFolder($opath, $npath)
    {
        $name = $this->getPathName($opath);
        $this->update(TFOLDER,['pid' => $this->getPathId($npath), 'path' => $npath.'/'.$name, 'name' => $name], ['path' => $opath]);
        return true;
    }

    public function getCountChildFolder($id)
    {
        return $this->count(TFOLDER, ['AND' => ['pid' => $id, 'trash[!]' => true]]);
    }

    public function getCountChildFile($id)
    {
        return $this->count(TFILE, ['AND' => ['folder_id' => $id, 'trash[!]' => true]]);
    }

    private function getPathId($path)
    {
        $res = $this->select(TFOLDER, 'id', ['path' => $path]);
        return isset($res[0])?$res[0]:false;
    }

    private function getPathName($path)
    {
        $res = $this->select(TFOLDER, 'name', ['path' => $path]);
        return isset($res[0])?$res[0]:false;
    }

    private function getParentFolder($id)
    {
        $pid = $this->select(TFOLDER, 'pid', ['id' => $id]);
        $res = $this->select(TFOLDER,'path', ['id' => $pid]);
        return isset($res[0])?$res[0]:false;
    }

    private function getChildFolder($id)
    {
        return $this->select(TFOLDER, 'path', ['pid' => $id]);
    }

    private function getFilePathName($fullPath)
    {
        $array = explode('/', $fullPath);
        $name = array_pop($array);
        $path = implode('/', $array);
        return ['path' => $path, 'name' => $name];

    }

    public function moveFile($path, $dir)
    {
        $ofile = $this->getFilePathName($path);
        $nfile = $this->getFilePathName($dir);
        $this->update(TFILE,
            ['folder_id' => $this->getPathId($nfile['path'])],
            ['AND' =>
                ['folder_id' => $this->getPathId($ofile['path']), 'name' => $ofile['name']]
            ]);
        return true;
    }

    public function renameFile($file, $name)
    {
        $file = $this->getFilePathName($file);
        $this->update(TFILE,
            ['name' => $name],
            ['AND' => [
                'folder_id' => $this->getPathId($file['path']),
                'name' => $file['name']
            ]]
        );

        return true;
    }

    public function deleteFile($file)
    {
        $file = $this->getFilePathName($file);
        $this->update(TFILE,
            ['trash' => true],
            ['AND' =>
                ['folder_id' => $this->getPathId($file['path']), 'name' => $file['name']]
            ]
        );
        return true;
    }

    public function uploadFile($path)
    {

        if(isset($_FILES['files']) and $_FILES['files']['error'] != 0)
        {
            foreach ($_FILES["files"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $file = $this->nameGenerate();
                    move_uploaded_file($_FILES["files"]["tmp_name"][$key], $file['path'].'/'.$file['name']);
                    $image = getimagesize($file['path'].'/'.$file['name']);
                    if($image)
                    {
                        $w = $image[0];
                        $h = $image[1];
                    } else {
                        $w = $h = 0;
                    }
                    $this->insert(TFILE, [
                        'folder_id' => $this->getPathId($path),
                        'name' => $_FILES['files']['name'][$key],
                        'type' => $_FILES['files']['type'][$key],
                        'size' => $_FILES['files']['size'][$key],
                        'realpath' => $file['path'] . $file['name'],
                        'modification' => time(),
                        'width' => $w,
                        'height' => $h,
                        'trash' => false
                    ]);
                    $res = true;
                } else $res = false;
            }
        }

        return $res;
    }

    public function thumbImage($file, $width, $height)
    {
        $file = $this->fileRealPath($file);
        $image = new \Eventviva\ImageResize($file);
        $image->resizeToWidth($width);
        $image->resizeToHeight($height);
        $image->output(IMAGETYPE_JPEG, 75);
        exit;
    }

    public function fileRealPath($file)
    {
        $file = $this->getFilePathName($file);
        $res = $this->select(TFILE,
            ['realpath'],
            ['AND' =>
                [
                    'folder_id' => $this->getPathId($file['path']),
                    'name' => $file['name']
                ]
            ]);
        return isset($res[0]['realpath'])?$res[0]['realpath']:false;
    }

    private function nameGenerate()
    {
        $file['name'] = \Misc::randomString(30);
        $file['path'] = \Registry::get('_config')['path']['private_files'].date('d-m-Y_H').'/';
        if(is_file($file['path'].$file['name'])) $name = $this->nameGenerate();
        if(!is_dir($file['path'])) mkdir($file['path']);
        return ['path' => $file['path'], 'name' => $file['name']];
    }


}