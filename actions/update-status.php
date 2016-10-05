<?php

include "../include/config.php";

$post_get = new GetVarClass();
$email = $post_get->getemail("email");

if(!$email)
	die("Редактирование заявки невозможно: введите корректный e-mail. ");

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы изменить статус заявки {$email}. ");

$status = $post_get->getenumkeys("status", $langStatuses);
if(!$status)
	die("Редактирование заявки невозможно: введите корректный статус.");

$sql = "UPDATE ".PREF."users
	SET status='$status'
	WHERE email='$email'
	LIMIT 1";
query($sql);

$updated = (bool)affected_rows();
if($updated){
	$status_out = $langStatuses[$status];
	$link = $_SERVER["HTTP_HOST"] . "/edit.php?" . http_build_query(["email" => $email]);
	// send_mail_by_userid(emailToId($email), "Мастер изменил статус вашей заявки на " . $status_out, "<a href=\"$link\">Просмотреть</a>");
	}

?>
