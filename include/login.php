<?php
if(!defined("INCMS")) die();

function dropsession($userid,$session){
	$sql="UPDATE ".PREF."sessions AS s
		SET valid=NOW()
		WHERE valid>NOW() AND userid=$userid AND name='$session'";
	query($sql);
	}

function dropallsessions($userid){
	$sql="UPDATE ".PREF."sessions AS s
		SET valid=NOW()
		WHERE valid>NOW() AND userid=$userid";
	query($sql);
	}

function loginbycookie(){
	global $LU,$attempt;

	$cookie_get = new GetVarClass("_COOKIE");

	$email   = $cookie_get->getvar("email");
	$session = $cookie_get->getvar("session","[0-9a-f]+");
	if(!$email || !$session)
		return 0;

	$attempt=1;

	$ip=$_SERVER['REMOTE_ADDR'];
	$alsowhere = ALLOWCHANGEIP ? "" : " AND s.ip=INET_ATON('$ip') ";

	$sql="SELECT u.id
		FROM ".PREF."sessions AS s
		INNER JOIN ".PREF."users AS u ON u.id=s.userid
		WHERE u.email='$email' AND s.name='$session' AND s.pwhash=u.pwhash
			AND s.valid>NOW() AND u.active='1' $alsowhere
		LIMIT 1";
	$result=query($sql);
	$rows=num_rows($result);

	if($rows){
		list($LU["id"])=fetch_row($result);
		if(!ALLOWMULTISESSIONS)
			dropallsessions($LU["id"]);
		}

	if(rand(0,100)==0){
		$sql = "DELETE FROM ".PREF."sessions
			WHERE valid<NOW()";
		query($sql);
		}

	return $rows;
	}

function loginbypost(){
	global $LU, $attempt;

	$post_get = new GetVarClass();

	$login = $post_get->getvar("email");
	$email = $post_get->getemail("email");
	$pw    = $post_get->getvar("pw");
	if(!($email || $login) || !$pw)
		return 0;

	$subquery = $email ? "u.email='$email'" : "u.login='$login'";

	$attempt=1;

	$sql="SELECT u.id,u.pw
		FROM ".PREF."users AS u
		WHERE $subquery AND (u.pwhash=MD5('$pw') OR '{$LU["moderid"]}'<>0) AND u.active
		LIMIT 1";
	$result=query($sql);
	$rows=num_rows($result);

	if($rows){
		list($LU["id"],$knownpw)=fetch_row($result);

		if(!ALLOWMULTISESSIONS)
			dropallsessions($LU["id"]);

		}
	return $rows;
	}



?>
