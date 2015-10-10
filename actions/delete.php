<?php

include "../include/config.php";

$post_get = new GetVarClass();
$email = $post_get->getemail("email");

if(!$email)
	die("Редактирование заявки невозможно: введите корректный e-mail. ");

$editorid = loginbycookie();
if(!canEdit($editorid, $email))
	die("У вас недостаточно прав доступа, чтобы редактировать заявку {$email}. ");

$sql = "DELETE FROM ".PREF."users
	WHERE email='$email'
	LIMIT 1";
query($sql);

redirect(isAdmin($editorid) ? "/table.php#{$email}" : "/actions/logout.php");

?>
