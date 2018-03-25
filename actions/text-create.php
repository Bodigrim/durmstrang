<?php

include "../include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы добавлять тексты. Вернитесь на <a href=\"/\">главную страницу</a>.");

$post_get = new GetVarClass();
$header = $post_get->getvar("header");

if(!$header)
	die("Не могу создать текст без заголовка.");

$sql = "INSERT INTO ".PREF."texts (header)
	VALUE ('$header')";
query($sql);
$textid = insert_id();

redirect("/text-edit.php?" . http_build_query(["id" => $textid]));

?>
