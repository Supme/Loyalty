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

class Image
{
    //ToDo своё казино с кэшем и тп
    public static function resizer(){
        define('FILE_CACHE_DIRECTORY', \Registry::get('_config')['path']['image_cache']);
        define('LOCAL_FILE_BASE_DIRECTORY', \Registry::get('_config')['path']['share_files']);
        $timthumb = new timthumb();
        $timthumb->start();
    }

    public function resize($image, $width, $height)
    {
        $hashFile = \Registry::get('_config')['path']['image_cache'].md5($width.$height.$image);
        if(!is_file($hashFile) or filemtime($hashFile) > time()+864000) {
            if (extension_loaded('imagick')) {
                $img = new Imagick($image);
                $img->scaleImage((int)$width, (int)$height);
                $img->writeImage($hashFile);
                $img->clear();
                $img->destroy();
            } else {
                // Если нет Imagick
            }
        }
            return $hashFile;
    }

    public function send($image)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $image); //mime_content_type($image);
        finfo_close($finfo);
        if(! preg_match('/^image\//i', $mimeType)){
            $mimeType = 'image/' . $mimeType;
        }
        $gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
        header ('Content-Type: ' . $mimeType);
        header ('Accept-Ranges: none'); //Changed this because we don't accept range requests
        header ('Last-Modified: ' . date ("F d Y H:i:s.", filemtime($image)));
        header ('Content-Length: ' . filesize($image));
        header('Cache-Control: max-age=864000, must-revalidate');
        header ('Expires: ' . $gmdate_expires);

        readfile($image);
        return true;
    }


} 