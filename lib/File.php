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

    public function getFolders()
    {
        $folders = $this->select(TFOLDER, ['id'], ['trash[!]' => true]);
        $i = 0;
        $array = [];
        foreach($folders as $folder)
        {
            $array[$i]['p'] = $this->getIdPath($folder['id']);
            $array[$i]['f'] = $this->getCountChildFile($folder['id']);
            $array[$i]['d'] = $this->getCountChildFolder($folder['id']);
            ++$i;

        }
        return $array;
    }

    public function getFolderFiles($path)
    {
        $files = $this->select(TFILE, ['name', 'size', 'modification', 'width', 'height'], ['AND' => ['folder_id' => $this->getFolderId($path), 'trash[!]' => true]]);
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
        return $this->count(TFOLDER, ['AND' => ['name' => $name, 'pid' => $this->getFolderId($path)]]);
    }

    // ToDo Проверить, а нет ли в каталоге назначения каталога с таким именем?
    public function createFolder($path, $name)
    {
        if($this->isFolder($path, $name) != 0) return false;
        $this->insert(TFOLDER, ['pid' => $this->getFolderId($path), 'name' => $name, 'trash' => false]);
        return true;
    }

    // ToDo Проверить, а нет ли в каталоге назначения каталога с таким именем?
    public function renameFolder($path, $name)
    {
        $this->update(TFOLDER, ['name' => $name], ['id' => $this->getFolderId($path)]);
        return true;
    }

    public function deleteFolder($path)
    {
        $parents = $this->getChildFolder($this->getFolderId($path));
        foreach($parents as $parent)
        {
            $this->deleteFolder($parent);
        }
        $files = $this->getFolderFiles($path);
        foreach($files as $file)
        {
            $this->deleteFile($file['p']);
        }
        $this->update(TFOLDER,['trash' => true], ['id' => $this->getFolderId($path)]);
        return true;
    }

    // ToDo Проверить, а нет ли в каталоге назначения каталога с таким именем?
    public function moveFolder($opath, $npath)
    {
        $this->update(TFOLDER,['pid' => $this->getFolderId($npath)], ['id' => $this->getFolderId($opath)]);
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

    private function getFolderId($path)
    {
        $id = NULL;
        $array = explode('/', $path);
        foreach ($array as $name)
        {
            $res = $this->select(TFOLDER, ['id'], ['AND' => ['pid' => $id, 'name' => $name]]);
            $id = isset($res[0])?$res[0]['id']:NULL;
        }

        return $id;
    }

    private function getIdPath($id)
    {
        $arr = $this->select(TFOLDER,['pid', 'name'], ['id' => $id]);
        $path = $arr[0]['name'];
        if($arr[0] != NULL)
        {
            while ($arr[0]['pid'] != NULL)
            {
                $arr = $this->select(TFOLDER,['pid', 'name'], ['id' => $arr[0]['pid']]);
                $path = $arr[0]['name'].'/'.$path;
            };
        }

        $path = '/'.$path;

        return $path;
    }


    private function getChildFolder($id)
    {
        $arr = $this->select(TFOLDER, 'id', ['pid' => $id]);
        $folders = [];
        foreach ($arr as $folder)
        {
            $folders[$folder] = $this->getIdPath($folder);
        }
        return $folders;
    }

    private function getFilePathName($fullPath)
    {
        $array = explode('/', $fullPath);
        $name = array_pop($array);
        $path = implode('/', $array);
        return ['path' => $path, 'name' => $name];

    }

    // ToDo Проверить, а нет ли в каталоге назначения файла с таким именем?
    public function copyFile($path, $dir)
    {
        $sourceFile = $this->getFilePathName($path);
        $destinationFolderId = $this->getFolderId($dir);

        $file = $this->select(TFILE,
            [
                'name',
                'type',
                'size',
                'realpath',
                'width',
                'height',
                'trash'
            ],
            ['AND' =>
                ['folder_id' => $this->getFolderId($sourceFile['path']), 'name' => $sourceFile['name']]
            ]);

        $newFile = $this->nameGenerate();
        copy($file[0]['realpath'], $newFile);

        $this->insert(TFILE, [
            'folder_id' => $this->getFolderId($dir),
            'name' => $file[0]['name'],
            'type' => $file[0]['type'],
            'size' => $file[0]['size'],
            'realpath' => $newFile,
            'modification' => time(),
            'width' => $file[0]['width'],
            'height' => $file[0]['height'],
            'trash' => $file[0]['trash']
        ]);

        return true;
    }

    // ToDo Проверить, а нет ли в каталоге назначения файла с таким именем?
    public function moveFile($source, $destination)
    {
        $sourceFile = $this->getFilePathName($source);
        $destinationFolder = $this->getFilePathName($destination);
        $this->update(TFILE,
            ['folder_id' => $this->getFolderId($destinationFolder['path'])],
            ['AND' =>
                ['folder_id' => $this->getFolderId($sourceFile['path']), 'name' => $sourceFile['name']]
            ]);
        return true;
    }

    public function renameFile($file, $name)
    {
        $file = $this->getFilePathName($file);
        $this->update(TFILE,
            ['name' => $name],
            ['AND' => [
                'folder_id' => $this->getFolderId($file['path']),
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
                ['folder_id' => $this->getFolderId($file['path']), 'name' => $file['name']]
            ]
        );
        return true;
    }

    // ToDo Проверить, а нет ли в каталоге назначения файла с таким именем? Если есть добавить что то в имя файла.
    public function uploadFile($path)
    {
        $res = false;
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
                        'folder_id' => $this->getFolderId($path),
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
                } else
                    $res = false;
            }
        }

        return $res; // ToDo Тут с ошибкой все не совсем так, учитывается результат только последнего файла
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
                    'folder_id' => $this->getFolderId($file['path']),
                    'name' => $file['name']
                ]
            ]);
        return isset($res[0]['realpath'])?$res[0]['realpath']:false;
    }

    public function fileType($file)
    {
        $file = $this->getFilePathName($file);
        $res = $this->select(TFILE,
            ['type'],
            ['AND' =>
                [
                    'folder_id' => $this->getFolderId($file['path']),
                    'name' => $file['name']
                ]
            ]);
        return isset($res[0]['type'])?$res[0]['type']:false;
    }

    public function downloadFile($path)
    {
        if ($real = $this->fileRealPath($path)){
            $name = substr(strrchr($path, "/"), 1);
            $this->download($real, $name, $this->fileType($path));
        } else {
           // header("Location: /error/404");
        }
    }

    /**
     * @param $path
     *
     * Todo решить проблемы с кириллицей
     */
    public function downloadArchiveFolder($path)
    {
        $archiveFile = $this->nameGenerate(true);
        $archive = new PharData($archiveFile['path'].$archiveFile['name'].'.zip', 0, 0, Phar::ZIP);
        //$archive = new \PHPZip\Zip\File\Zip($archiveFile['path'].$archiveFile['name'].'.zip');

        $folders = $this->getFolderTree($path);
        foreach ($folders as $folder)
        {
            $folderName = str_replace(dirname($path) == '/'?'':dirname($path), '', $folder);
            $archive->addEmptyDir($folderName);//iconv('utf8', 'cp866', $folderName));
            $files = $files = $this->select(TFILE,
                [
                    'name',
                    'realpath',
                ],
                ['AND' =>
                    [
                        'folder_id' => $this->getFolderId($folder),
                        'trash[!]' => true
                    ]
                ]);

            foreach ($files as $file)
            {
                $archive->addFile($file['realpath'], $folderName.'/'.$file['name']);
            }
        }

        $this->download($archiveFile['path'].$archiveFile['name'].'.zip', str_replace('/', '_', substr($path, 1)).'.zip', 'application/zip');

        unlink($archiveFile['path'].$archiveFile['name'].'.zip');

        return true;
    }

    public function download($filePath, $fileName, $fileType, $forceDownload = true, $speedLimit = true)
    {
        if ($speedLimit and ($speed = \Registry::get('_config')['site']['download_speed']) > 0)
            $sleep_time = (8 / $speed) * 1e6;
        else
            $sleep_time = 0;

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: public");
        header("Content-Description: Download File Transfer");
        header("Content-Type: ".$fileType);
        if ($forceDownload)
            header('Content-Disposition: attachment; filename="'.$fileName.'";');
        else
            header('Content-Disposition: inline; filename="'.$fileName.'";');
        header("Content-Transfer-Encoding: binary");

        $fp=fopen($filePath,"rb");
        fseek($fp,0);//$byte_from);                          // seek to start of missing part
        while(!feof($fp)){                              // start buffered download
            set_time_limit(0);                          // reset time limit for big files (has no effect if php is executed in safe mode)
            print(fread($fp,1024*8));                   // send 8ko
            flush();
            usleep($sleep_time);                        // sleep (for speed limitation)
        }
        fclose($fp);
        //exit;
    }


    private function getFolderTree($path)
    {
        $folders = [];
        $folders[] =$path;
        $arr = $this->getChildFolder($this->getFolderId($path));
        foreach ($arr as $folder)
        {
            $folders[] = $folder;
            if ($this->getCountChildFolder($this->getFolderId($folder)) != 0)
            {
                $folders = array_merge($folders, $this->getFolderTree($folder));
            }
        }

        return $folders;
    }

    private function nameGenerate($cache = false)
    {
        $file['name'] = \Misc::randomString(30);
        if ($cache)
            $file['path'] = \Registry::get('_config')['path']['file_cache'];
        else
            $file['path'] = \Registry::get('_config')['path']['private_files'].date('d-m-Y_H').'/';
        if(is_file($file['path'].$file['name'])) $name = $this->nameGenerate($cache);
        if(!is_dir($file['path'])) mkdir($file['path']);
        return ['path' => $file['path'], 'name' => $file['name']];
    }


}