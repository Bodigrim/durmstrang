<?php

include "../include/config.php";

$post_get = new GetVarClass();
$email    = $post_get->getemail("email");
$password = trim($post_get->getvar("password"));

if(!$email)
	die("Введите корректный e-mail, чтобы войти или восстановить пароль. ");

$userid = emailToId($email);
if(!$userid){
	$text = <<<EOT
E-mail {$email} не зарегистрирован.
<br />
Воспользуйтесь <a href="/#register">формой регистрации</a> на главной странице.
<br />
По техническим вопросам обращайтесь к Бодигриму (andrew.lelechenko@gmail.com, skype bodigrim).
EOT;
	echo $text;

	die();
	}

$hash = bcrypt($password);
$sql = "SELECT id
	FROM ".PREF."users
	WHERE id=$userid AND pwhash='$hash'
	LIMIT 1";
$rows = (int)db_result00($sql);
if(!$rows){
	$text = <<<EOT
Не удалось войти, поскольку пароль не подходит. На всякий случай мы отправили вам ваш пароль на почту еще раз.
<br />
Воспользуйтесь <a href="/#register">формой входа</a> на главной странице еще раз.
<br />
По техническим вопросам обращайтесь к Бодигриму (andrew.lelechenko@gmail.com, skype bodigrim).
EOT;
	echo $text;

	remindPassword($userid);

	die();
	}


set_login_cookies($userid, $email, $hash);

redirect(isAdmin($userid) ? "/table.php" : "/edit.php?" . http_build_query(["email" => $email]));

?>
