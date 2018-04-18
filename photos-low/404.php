<?php
define("BACKEND", 1);
require "../include/config.php";

require "../include/resize.php";

header("Content-type: image/jpeg");

$uri = $_SERVER["REQUEST_URI"];
$pattern = "/^\/photos\-low\/(\d+)\.(\d+)\.(.*)\.(jpg|jpeg|png|gif)$/i";
preg_match_all($pattern, $uri, $matches);

//print_r($matches);

if(!count($matches[0]))
	die();

$w   = $matches[1][0];
$h   = $matches[2][0];
$src = $matches[3][0];
$ext = $matches[4][0];

$infile  = "../photos/$src.$ext";
$outfile = "$w.$h.$src.$ext";

if(!file_exists($infile))
	die();

smartResizeImg($infile, $outfile, $w, $h, true);

header("HTTP/1.0 200 OK");
readfile($outfile);

//unlink($outfile);

?>
