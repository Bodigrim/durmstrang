<?php

include "../include/config.php";

$post_get = new GetVarClass();
$email = $post_get->getemail("email");

if(!$email)
	die("Отправить сообщение невозможно: введите корректный e-mail. ");

$editorid = loginbycookie();
if(!canEdit($editorid, $email))
	die("У вас недостаточно прав доступа, чтобы отправить сообщение {$email}. ");

$userid = (int)emailToId($email);

$message = $post_get->getvar("message");

$sql = "INSERT INTO ".PREF."messages (userid, authorid)
	VALUE ($userid, $editorid)";
query($sql);

$message_id = insert_id();
$sql = "UPDATE ".PREF."messages
	SET message='$message'
	WHERE id=$message_id
	LIMIT 1";
query($sql);

$message_out = nl2br(db_unescape($message));
$link = $_SERVER["HTTP_HOST"] . "/edit.php?" . http_build_query(["email" => $email]);
if($editorid==$userid){
	markUnread($userid);
	// send_mail_to_admin("$name написал сообщение", "$message_out <br /><a href=\"$link\">Ответить</a>");
	}
else {
	send_mail_by_userid($userid, "Мастер написал сообщение", "$message_out <br /><a href=\"$link\">Ответить</a>");
	}

?>
