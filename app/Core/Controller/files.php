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

namespace App\Core\Controller;

//use Respect\Validation\Rules\File;

class files extends \Controller
{
    private $file;

    function __construct()
    {
        $this->file = new \File();
    }

    function files($params){

        $path = str_replace('..','',implode('/',$params));

        if(!isset($params[0])) header("Location: /error/404");

        // Public file
        if(file_exists(\Registry::get('_config')['path']['share_files'].$path)){
            $file = new \Download(\Registry::get('_config')['path']['share_files'].$path);
            $file->download();
        } else {
        // Private file
            $this->file->downloadFile($_SERVER['REDIRECT_URL']);
        }
    }

    static function access($attr, $path, $data, $volume) {
        return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
            ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
            :  null;                                    // else elFinder decide it itself
    }

    function fm($params)
    {
        if(isset($params[0]))
        {
            switch ($params[0])
            {
                case 'conf.json':
                    $this->confJson();
                    break;
                case 'lang':
                    $this->langJson(isset($params[1])?$params[1]:'en');
                    break;

                default:
                    $validActions = [
                        'dirtree',
                        'createdir',
                        'deletedir',
                        'movedir',
                        'copydir',
                        'renamedir',
                        'fileslist',
                        'upload',
                        'download',
                        'downloaddir',
                        'deletefile',
                        'movefile',
                        'copyfile',
                        'renamefile',
                        'thumb',
                        'download',
                        'downloaddir'
                    ];
                    $validData = ['p', 'f', 'd', 's', 't', 'w', 'h', 'n', 'f', 'width', 'height'];
                    if (in_array($params[0], $validActions) and method_exists($this, $params[0]))
                    {
                        $data = [];
                        foreach($_REQUEST as $k => $d){
                            if(in_array($k, $validData)){
                                $data[$k] = $d;
                            }
                        }
                        $response = $this->{$params[0]}($data);
                    } else {
                        $response = ['res' => 'error','msg' => 'Method "'.$params[0].'" not found!'];
                    }

                    $this->json($response);
            }

        } else {
            \Registry::css([
                "/assets/fileman/css/main.min.css",
                "/assets/fileman/css/jquery-ui-1.10.4.custom.min.css",
            ]);

            \Registry::js([
                "//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js",
                "/assets/fileman/js/jquery-ui-1.10.4.custom.min.js",
                "/assets/fileman/js/custom.js",
                "/assets/fileman/js/main.min.js",
                "/assets/fileman/js/jquery-dateFormat.min.js"
            ]);

            \Registry::$store['_page']['view'] = 'files';
            \Registry::$store['_page']['layout'] = 'clear';

            $this->render([]);
        }
    }

    private function confJson()
    {
        $conf = [
            "FILES_ROOT" => "",
            "RETURN_URL_PREFIX" => "/download", //"/files/",
            "SESSION_PATH_KEY" => "",
            "THUMBS_VIEW_WIDTH" => "140",
            "THUMBS_VIEW_HEIGHT" => "120",
            "PREVIEW_THUMB_WIDTH" => "100",
            "PREVIEW_THUMB_HEIGHT" => "100",
            "MAX_IMAGE_WIDTH" => "1000",
            "MAX_IMAGE_HEIGHT" => "1000",
            "INTEGRATION" => "tinymce4",
            "DIRLIST" => "fm/dirtree",
            "CREATEDIR" => "fm/createdir",
            "DELETEDIR" => "fm/deletedir",
            "MOVEDIR" => "fm/movedir",
            "COPYDIR" => "fm/copydir",
            "RENAMEDIR" => "fm/renamedir",
            "FILESLIST" => "fm/fileslist",
            "UPLOAD" => "fm/upload",
            "DOWNLOAD" => "fm/download",
            "DOWNLOADDIR" => "fm/downloaddir",
            "DELETEFILE" => "fm/deletefile",
            "MOVEFILE" => "fm/movefile",
            "COPYFILE" => "fm/copyfile",
            "RENAMEFILE" => "fm/renamefile",
            "GENERATETHUMB" => "fm/thumb",
            "DEFAULTVIEW" => "list",
            "FORBIDDEN_UPLOADS" => "zip js jsp jsb mhtml mht xhtml xht php phtml php3 php4 php5 phps shtml jhtml pl sh py cgi exe application gadget hta cpl msc jar vb jse ws wsf wsc wsh ps1 ps2 psc1 psc2 msh msh1 msh2 inf reg scf msp scr dll msi vbs bat com pif cmd vxd cpl htpasswd htaccess",
            "ALLOWED_UPLOADS" => "",
            "FILEPERMISSIONS" => "0644",
            "DIRPERMISSIONS" => "0755",
            "LANG" => "auto",
            "DATEFORMAT" => "dd/MM/yyyy HH:mm",
            "OPEN_LAST_DIR" => "yes"
        ];
        $this->json($conf);
    }

    private function langJson($lang)
    {
        $lang = [
            "CreateDir" => \Translate::get("fm.Create"),
            "RenameDir" => \Translate::get("fm.Rename"),
            "DeleteDir" => \Translate::get("fm.Delete"),
            "AddFile" => \Translate::get("fm.Add file"),
            "Preview" => \Translate::get("fm.Preview"),
            "RenameFile" => \Translate::get("fm.Rename"),
            "DeleteFile" => \Translate::get("fm.Delete"),
            "SelectFile" => \Translate::get("fm.Select"),
            "OrderBy" => \Translate::get("fm.Order by"),
            "Name_asc" => \Translate::get("fm.Order up by name"),
            "Size_asc" => \Translate::get("fm.Order up by size"),
            "Date_asc" => \Translate::get("fm.Order up by date"),
            "Name_desc" => \Translate::get("fm.Order down by name"),
            "Size_desc" => \Translate::get("fm.Order down by size"),
            "Date_desc" => \Translate::get("fm.Order down by date"),
            "Name" => \Translate::get("fm.Name"),
            "Size" => \Translate::get("fm.Size"),
            "Date" => \Translate::get("fm.Date"),
            "Dimensions" => \Translate::get("fm.Dimensions"),
            "Cancel" => \Translate::get("fm.Cancel"),
            "LoadingDirectories" => \Translate::get("fm.Loading folders..."),
            "LoadingFiles" => \Translate::get("fm.Loading files..."),
            "DirIsEmpty" => \Translate::get("fm.This folder is empty"),
            "NoFilesFound" => \Translate::get("fm.No files found"),
            "Upload" => \Translate::get("fm.Upload"),
            "T_CreateDir" => \Translate::get("fm.Create new folder"),
            "T_RenameDir" => \Translate::get("fm.Rename folder"),
            "T_DeleteDir" => \Translate::get("fm.Delete selected folder"),
            "T_AddFile" => \Translate::get("fm.Upload files"),
            "T_Preview" => \Translate::get("fm.Preview selected file"),
            "T_RenameFile" => \Translate::get("fm.Rename file"),
            "T_DeleteFile" => \Translate::get("fm.Delete file"),
            "T_SelectFile" => \Translate::get("fm.Select highlighted file"),
            "T_ListView" => \Translate::get("fm.List view"),
            "T_ThumbsView" => \Translate::get("fm.Thumbnails view"),
            "Q_DeleteFolder" => \Translate::get("fm.Delete selected directory?"),
            "Q_DeleteFile" => \Translate::get("fm.Delete selected file?"),
            "E_LoadingConf" => \Translate::get("fm.Error loading configuration"),
            "E_ActionDisabled" => \Translate::get("fm.This action is disabled"),
            "E_LoadingAjax" => \Translate::get("fm.Error loading"),
            "E_MissingDirName" => \Translate::get("fm.Missing folder name"),
            "E_SelectFiles" => \Translate::get("fm.Select files to upload"),
            "E_CannotRenameRoot" => \Translate::get("fm.Cannot rename root folder."),
            "E_NoFileSelected" => \Translate::get("fm.No file selected."),
            "E_CreateDirFailed" => \Translate::get("fm.Error creating directory"),
            "E_CreateDirInvalidPath" => \Translate::get("fm.Cannot create directory - path doesn't exist"),
            "E_CannotDeleteDir" => \Translate::get("fm.Error deleting directory"),
            "E_DeleteDirInvalidPath" => \Translate::get("fm.Cannot delete directory - path doesn't exist"),
            "E_DeletеFile" => \Translate::get("fm.Error deleting file"),
            "E_DeleteFileInvalidPath" => \Translate::get("fm.Cannot delete file - path doesn't exist"),
            "E_DeleteNonEmpty" => \Translate::get("fm.Cannot delete - folder is not empty"),
            "E_CannotMoveDirToChild" => \Translate::get("fm.Cannot move directory to its subdirectory"),
            "E_DirAlreadyExists" => \Translate::get("fm.Directory with the same name already exists"),
            "E_MoveDir" => \Translate::get("fm.Error moving directory"),
            "E_MoveDirInvalisPath" => \Translate::get("fm.Cannot move directory - directory doesn't exist"),
            "E_MoveFile" => \Translate::get("fm.Error moving file"),
            "E_MoveFileInvalisPath" => \Translate::get("fm.Cannot move file - file doesn't exist"),
            "E_MoveFileAlreadyExists" => \Translate::get("fm.File with the same name already exists"),
            "E_RenameDir" => \Translate::get("fm.Error renaming directory"),
            "E_RenameDirInvalidPath" => \Translate::get("fm.Cannot rename directory - path doesn't exist"),
            "E_RenameFile" => \Translate::get("fm.Error renaming file"),
            "E_RenameFileInvalidPath" => \Translate::get("fm.Cannot rename file - file doesn't exist"),
            "E_UploadNotAll" => \Translate::get("fm.There is and error uploading some files. "),
            "E_UploadNoFiles" => \Translate::get("fm.There are no files to upload or file is too big."),
            "E_UploadInvalidPath" => \Translate::get("fm.Cannot upload files - path doesn't exist"),
            "E_FileExtensionForbidden" => \Translate::get("fm.This type of files cannot be handeled - invalid extension "),
            "Download" => \Translate::get("fm.Download folder"),
            "DownloadFile" => \Translate::get("fm.Download"),
            "T_DownloadFile" => \Translate::get("fm.Download file"),
            "E_CannotDeleteRoot" => \Translate::get("fm.Cannot delete root folder"),
            "file" => \Translate::get("fm.file"),
            "files" => \Translate::get("fm.files"),
            "Cut" => \Translate::get("fm.Cut"),
            "Copy" => \Translate::get("fm.Copy"),
            "Paste" => \Translate::get("fm.Paste"),
            "E_CopyFile" => \Translate::get("fm.Error copying file"),
            "E_CopyFileInvalisPath" => \Translate::get("fm.Cannot copy file - path doesn't exist"),
            "E_CopyDirInvalidPath" => \Translate::get("fm.Cannot copy directory - path doesn't exist"),
            "E_CreateArchive" => \Translate::get("fm.Error creating zip archive."),
            "E_UploadingFile" => \Translate::get("fm.error")
        ];

        $this->json($lang);
    }

    private function dirtree($data)
    {
        return $this->file->getFolders();
    }

    private function createdir($data)
    {
        $r = $this->file->createFolder($data['d'], $data['n']);
        if ($r === true)
        {
            return ['res' => 'ok','msg' => ''];
        } else {
            return ['res' => 'error','msg' => $r];
        }
    }

    private function deletedir($data)
    {
        if($this->file->deleteFolder($data['d']) === true){
            return ['res' => 'ok','msg' => ''];
        } else {
            return ['res' => 'error','msg' => 'Error delete directory'];
        }
    }

    private function movedir($data)
    {
        $r = $this->file->moveFolder($data['d'], $data['n']);
        if($r === true){
            return ['res' => 'ok','msg' => ''];
        } else {
            return ['res' => 'error','msg' => \Translate::get($r)];
        }
    }

    private function copydir($data)
    {
        $r = $this->file->copyFolder($data['d'], $data['n']);
        if($r === true){
            return ['res' => 'ok','msg' => ''];
        } else {
            return ['res' => 'error','msg' => \Translate::get($r)];
        }
    }

    private function renamedir($data)
    {
        $r = $this->file->renameFolder($data['d'], $data['n']);
        if($r === true)
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => \Translate::get($r)];
        }

        return $response;
    }

    private function fileslist($data)
    {
        return $this->file->getFolderFiles($data['d']);
    }

    private function upload($data)
    {
        $r = $this->file->uploadFile($data['d']);
        if($r === true)
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => \Translate::get($r)];
        }

        return $response;
    }

    private function download($data)
    {
        $this->file->downloadFile($data['f']);
    }

    private function downloaddir($data)
    {
        $r = $this->file->downloadArchiveFolder($data['d']);
        if($r === true)
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => \Translate::get($r)];
        }

        return $response;
    }

    private function deletefile($data)
    {
        if($this->file->deleteFile($data['f']))
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => 'Error delete file'];
        }

        return $response;
    }

    private function movefile($data)
    {
        $r = $this->file->moveFile($data['f'], $data['n']);
        if($r === true)
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => \Translate::get($r)];
        }

        return $response;
    }

    private function renamefile($data)
    {
        $r = $this->file->renameFile($data['f'], $data['n']);
        if($r === true)
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => \Translate::get($r)];
        }

        return $response;
    }

    private function thumb($data)
    {
        $this->file->thumbImage($data['f'], $data['width'], $data['height']);
    }

    private function copyfile($data)
    {
        $r = $this->file->copyFile($data['f'], $data['n']);
        if($r === true)
        {
            $response = ['res' => 'ok','msg' => ''];
        } else {
            $response = ['res' => 'error','msg' => \Translate::get($r)];
        }

        return $response;
    }

}