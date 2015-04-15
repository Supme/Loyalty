<?php

include('qqfileuploader.php');

define('BASE_PATH', str_replace('\\','/',realpath('.')).'/');
define('BASE_URL', dirname($_SERVER["SCRIPT_NAME"]).'/');

function path_to_url($path) {
    $path = str_replace('\\','/',$path);
    return strpos($path, BASE_PATH)===0 ? BASE_URL.substr($path, strlen(BASE_PATH)) : $path;
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
// max file size in bytes
$sizeLimit = 1 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload(BASE_PATH.'upload/', TRUE);
// to pass data through iframe you will need to encode all html tags

if (isset($result['success']) && $result['success']) {
    $file = $result['file'];
    $result['url'] = path_to_url($file);
    unset($result['file']);

    $imgsize = getimagesize($file);
    $result['width'] = $imgsize[0];
    $result['height'] = $imgsize[1];
}

echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
