<?php

include "../include/config.php";

function randomPassword(){
	$n    = mt_rand();
	$hash = antispamhash($n);
	$ret  = substr($hash, 0, 8);
	return $ret;
	}

$post_get = new GetVarClass();
$email = $post_get->getemail("email");
$c     = $post_get->getint("c");
$chash = $post_get->getvar("chash");

if(!$email)
	die("Регистрация невозможна: введите корректный e-mail. ");
if(antispamhash($c)!=$chash)
	die("Регистрация невозможна: анти-спам тест не пройден. ");

$userid = emailToId($email);
if($userid){
	$text = <<<EOT
E-mail {$email} уже зарегистрирован. На всякий случай мы отправили вам ваш пароль на почту еще раз.
<br />
Воспользуйтесь <a href="/#login">формой входа</a> на главной странице.
<br />
По техническим вопросам обращайтесь к Бодигриму (andrew.lelechenko@gmail.com, skype bodigrim).
EOT;
	echo $text;

	remindPassword($userid);

	die();
	}

$password = randomPassword();
$hash = bcrypt($password);

$sql = "INSERT INTO ".PREF."users (email, pw, pwhash, active)
	VALUE ('$email', '$password', '$hash', 1)";
query($sql);
$userid = insert_id();

remindPassword($userid);

set_login_cookies($userid, $email, $hash);

redirect("/edit.php?" . http_build_query(["email" => $email]));

?>
