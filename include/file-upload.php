<?php

if(!defined("INCMS")) die();

class FileUploadOptions {
	public $key;
	public $extensions = [];
	public $is_critical = false;
	public $dir = "";
	public $neoname = "";
	public $rights = 0644;
	}

function upload($about, $exts, $critical=0, $dir="", $neoname=""){
	$options = new FileUploadOptions;
	$options->key = $about;
	$options->extensions = $exts;
	$options->is_critical = (bool)$critical;
	$options->dir = $dir;
	$options->neoname = $neoname;

	return file_upload($options);
	}

function file_upload($options){

	if(!isset($_FILES[$options->key])){
		if($options->is_critical)
			die("\$_FILES[{$options->key}] isn't set!");
		return;
		}

	$file = $_FILES[$options->key];

	if($file["error"]){
		if($options->is_critical)
			die("<a href=\"http://www.php.net/manual/en/features.file-upload.errors.php\">Error {$file["error"]}!</a>");
		return;
		}

	$pathinfo = pathinfo($file["name"]);
	$filename = $pathinfo["filename"];
	$extension = isset($pathinfo["extension"]) ? $pathinfo["extension"] : "";

	$filename = mb_eregi_replace("[^a-z0-9_\-]", "-", $filename);

	$extension = strtolower($extension);
	foreach($options->extensions as &$value){
		$value = strtolower($value);
		}

	if(!in_array($extension, $options->extensions)){
		if($options->is_critical)
			die("Unexpected file extension $extension!");
		return;
		}

	if($options->neoname){
		$pathinfo = pathinfo($options->neoname);
		$neoname = $pathinfo["filename"];
		}
	else {
		$neoname = $filename;
		}

	$newname = "$neoname.$extension";
	if(file_exists($options->dir . $newname)){
		$suff = 0;
		$newname = "$neoname.0.$extension";
		while(file_exists($options->dir . $newname)){
			$suff++;
			$newname = "$neoname.$suff.$extension";
			}
		}

	$uploadfile = $options->dir . $newname;

	if(!move_uploaded_file($file['tmp_name'], $uploadfile)){
		if($options->is_critical)
			die("Cannot move uploaded file!");
		return;
		}

	chmod($uploadfile, $options->rights);

	return $newname;
	}

?>
