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

namespace App\Gallery\Model;


class data {

    function getFolders($f = '')
    {
        $f = $f == ''?'':$f.'/';
        $root = '../data/files/gallery';
        $folders = [];
        $arr = glob($root.'/'.$f.'*');
        rsort($arr);
        foreach ($arr as $folder)
        {
            if (is_dir($folder))
            {
                $folders[basename($folder)] = str_replace($root, '', $folder);
            }
        }

        return $folders;
    }

    function getPictures($f = '')
    {
        $f = $f == ''?'':$f.'/';
        $root = '../data/files/gallery';
        $files = [];
        $types = [
            'image/gif',
            'image/jpeg',
        ];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        foreach (glob($root.'/'.$f.'*') as $file)
        {
            if (in_array($finfo->file($file), $types))
            {
                $files[basename($file)] = urlencode(str_replace($root, '', $file));
            }
        }

        return $files;
    }

}
