<?php
if(!defined("INCMS")) die();

function readConfig(){
	$ret = [];

	$sql = "SELECT `key`, `value`
		FROM ".PREF."config";
	$result = query($sql);
	$ret = fetch_mapping($result);

	return $ret;
	}

function gConfig($key){
	static $config = null;

	if($config==null)
		$config = readConfig();

	$ret = isset($config[$key]) ? $config[$key] : "";

	return $ret;
	}

function gConfigLocalized($key){
	return gConfig($key . "_" . LANGUAGE);
	}

function sConfig($key, $value){
	$key = db_escape($key);
	$value = db_escape($value);
	$sql = "REPLACE INTO ".PREF."config
		VALUE ('$key', '$value')";
	query($sql);
	}


?>
