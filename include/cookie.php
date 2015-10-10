<?php
if(!defined("INCMS")) die();

function cookie_set($name, $value, $duration=false, $path="/", $domain='', $secure=false, $httponly=false){
	$_COOKIE[$name] = $value;
	if($duration===false)
		$expires = time() + 86400 * 7;
	else if($duration==0){
		$expires = 0;
		}
	else {
		$expires = time() + $duration;
		}
	return setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
	}

function cookie_set_httponly($name, $value, $duration=false, $path="/", $domain='', $secure=false){
	return cookie_set($name, $value, $duration, $path, $domain, $secure, true);
	}

function cookie_drop($name){
	unset($_COOKIE[$name]);
	return setcookie($name, "", 0, "/");
	}

?>
