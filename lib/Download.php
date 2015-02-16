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

class Download extends Db
{

    /* Example
    $file = new Download("example.zip");                             // use the original file name, disallow resuming, no speed limit
    $file = new Download("example.zip","My Example.zip") ;           // rename the file, disallow resuming, no speed limit
    $file = new Download("example.zip","My Example.zip","on") ;      // rename the file, allow resuming, no speed limit
    $file = new Download("example.zip","My Example.zip","on",80) ;   // rename the file, allow resuming, speed limit 80ko/s

    $file->download_file();
    */

    // just one array to gather all the properties of a download
    private $properties = array("path" => "",       // the real path to the file
        "name" => "",       // to rename the file on the fly
        "extension" => "",  // extension of the file
        "type" => "",       // the type of the file
        "size" => "",       // the file size
        "resume" => "",     // allow / disallow resuming
        "max_speed" => ""   // speed limit (ko) ( 0 = no limit)
    );

    // the constructor
    public function __construct($path, $name="", $resume="off", $max_speed=0)   // by default, resuming is NOT allowed and there is no speed limit
    {
        $name = ($name == "")? substr(strrchr("/".$path,"/"),1) : $name;       // if "name" is not specified, the file won't be renamed

        $file_extension = strtolower(substr(strrchr($path,"."),1));             // the file extension
        $content_type = mime_content_type($path);
        /* если здесь тип определен, то отдаем браузеру на просмотр
         * если нет (default), то на скачивание
         */

        /*switch( $file_extension ) {                                             // the file type
            case "mpg": $content_type="video/mpeg"; break;
            case "avi": $content_type="video/x-msvideo"; break;
            case "wmv": $content_type="video/x-ms-wmv"; break;
            case "wma": $content_type="audio/x-ms-wma"; break;
            case "pdf": $content_type="application/pdf"; break;
            case "jpg": $content_type="image/jpeg"; break;
            case "gif": $content_type="image/gif"; break;
            case "png": $content_type="image/png"; break;
            default:    $content_type="application/force-download";
        }*/
        $file_size = filesize($path);                                           // the file size
        $this->properties =  array(
            "path" => $path,
            "name" => $name,
            "extension" =>$file_extension,
            "type"=>$content_type,
            "size" => $file_size,
            "resume" => $resume,
            "max_speed" => $max_speed
        );
    }

    // public function to get the value of a property
    public function get_property ($property)
    {
        if ( array_key_exists($property,$this->properties) )   // check if the property do exist
            return $this->properties[$property];               // get its value
        else
            return null;                                       // else return null
    }

    // public function to set the value of a property
    public function set_property ($property, $value)
    {
        if ( array_key_exists($property, $this->properties) ){ // check if the property do exist
            $this->properties[$property] = $value;             // set the new value
            return true;
        } else
            return false;
    }

    public function getFileHash($name){
        if ($this->has('files', ['file' => $name])){
            $hash = $this->select('files', 'hash', ['file' => $name])[0];
            return $hash;
        } else {
            return false;
        };
    }

    // public function to start the download
    public function download()
    {
        if ( $this->properties['path'] == "" )                 // if the path is unset, then error !
            echo "Nothing to download!";
        else {
            // if resuming is allowed ...
            if ($this->properties["resume"] == "on") {
                if(isset($_SERVER['HTTP_RANGE'])) {            // check if http_range is sent by browser (or download manager)
                    list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']);
                    //ereg ( string $pattern , string $string [, array &$regs ] )
                    //preg_match ( string $pattern , string $subject [, array &$matches [, int $flags = 0 [, int $offset = 0 ]]] )
                    ereg("([0-9]+)-([0-9]*)/?([0-9]*)",$range,$range_parts); // parsing Range header
                    $byte_from = $range_parts [1];     // the download range : from $byte_from ...
                    $byte_to = $range_parts [2];       // ... to $byte_to
                } else
                    if(isset($_ENV['HTTP_RANGE'])) {       // some web servers do use the $_ENV['HTTP_RANGE'] instead
                        list($a, $range)=explode("=",$_ENV['HTTP_RANGE']);
                        ereg("([0-9]+)-([0-9]*)/?([0-9]*)",$range,$range_parts); // parsing Range header
                        $byte_from = $range_parts [1];     // the download range : from $byte_from ...
                        $byte_to = $range_parts [2];       // ... to $byte_to
                    }else{
                        $byte_from = 0;                         // if no range header is found, download the whole file from byte 0 ...
                        $byte_to = $this->properties["size"] - 1;   // ... to the last byte
                    }
                if ($byte_to == "")                             // if the end byte is not specified, ...
                    $byte_to = $this->properties["size"] -1;    // ... set it to the last byte of the file
                header("HTTP/1.1 206 Patial Content");          // send the partial content header
                // ... else, download the whole file
            } else {
                $byte_from = 0;
                $byte_to = $this->properties["size"] - 1;
            }

            $download_range = $byte_from."-".$byte_to."/".$this->properties["size"]; // the download range
            $download_size = $byte_to - $byte_from;                                  // the download length

            // download speed limitation
            if (($speed = $this->properties["max_speed"]) > 0)                       // determine the max speed allowed ...
                $sleep_time = (8 / $speed) * 1e6;                                    // ... if "max_speed" = 0 then no limit (default)
            else
                $sleep_time = 0;

            // send the headers
            header("Pragma: public");                                                // purge the browser cache
            header("Expires: 0");                                                    // ...
            header("Cache-Control: public");                                         // ...
            header("Content-Description: File Transfer");                            //
            header("Content-Type: ".$this->properties["type"]);                     // file type
            if ($this->properties["type"] == "application/force-download"){
                header('Content-Disposition: attachment; filename="'.$this->properties["name"].'";');
            }
            else {
                header('Content-Disposition: inline; filename="'.$this->properties["name"].'";');
            }
            header("Content-Transfer-Encoding: binary");                             // transfer method
            header("Content-Range: $download_range");                                // download range
            header("Content-Length: $download_size");                                // download length

            // send the file content
            $fp=fopen($this->properties["path"],"rb");      // open the file
            fseek($fp,$byte_from);                          // seek to start of missing part
            while(!feof($fp)){                              // start buffered download
                set_time_limit(0);                          // reset time limit for big files (has no effect if php is executed in safe mode)
                print(fread($fp,1024*8));                   // send 8ko
                flush();
                usleep($sleep_time);                        // sleep (for speed limitation)
            }
            fclose($fp);                                    // close the file
            exit;
        }
    }

    public function upload()
    {

    }
}
