<?php

include "../include/config.php";

$post_get = new GetVarClass();
$id = $post_get->getint("id");

if(!$id)
	die("Удаление текста невозможно: введите корректный идентификатор. ");

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы удалить текст. ");

$sql = "DELETE FROM ".PREF."texts
	WHERE id='$id'
	LIMIT 1";
query($sql);

redirect("/texts.php");

?>
