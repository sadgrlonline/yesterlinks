<?php
$filename = 'yesterlinks.json';

$file = fopen($filename, 'r');
$file_contents = fread($file, filesize($filename));
$file_contents = json_decode($file_contents);

$random_index = rand(0, count($file_contents));
$site_contents = $file_contents[$random_index];
$url = $file_contents[$random_index]->url;
var_dump($file_contents[$random_index]);

header("location: $url");

fclose($file);
?>
