<?php

define('BASE_PATH', str_replace('\\','/',realpath('.')).'/');
define('BASE_URL', dirname($_SERVER["SCRIPT_NAME"]).'/');

function path_to_url($path) {
    $path = str_replace('\\','/',$path);
    return strpos($path, BASE_PATH)===0 ? BASE_URL.substr($path, strlen(BASE_PATH)) : $path;
}

function url_to_path($url) {
    $url = str_replace('\\','/',$url);
    return strpos($url, BASE_URL)===0 ? BASE_PATH.substr($url, strlen(BASE_URL)) : $url;
}

$x1 = $_POST['x1'];
$y1 = $_POST['y1'];
$x2 = $_POST['x2'];
$y2 = $_POST['y2'];

$tw = $_POST['target_width'];
$th = $_POST['target_height'];

$url = $_POST['url'];
$file = url_to_path($url);

$pi = pathinfo($file);
$cropped_file = $pi['dirname'].'/'.$pi['filename'].'_'.$tw.'x'.$th.'.jpg';

$cropped = imagecreatetruecolor($tw, $th);

$original = file_get_contents($file);
$original = imagecreatefromstring($original);

imagecopyresampled ($cropped, $original, 0, 0, $x1, $y1, $tw, $th, $x2-$x1, $y2-$y1);
imagejpeg($cropped, $cropped_file, 80);

$result = array(
    'success' => true,
    'url' => path_to_url($cropped_file),
    'width' => $tw,
    'height' => $th
);

echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
