<?php

include "../include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы редактировать тексты. Вернитесь на <a href=\"/\">главную страницу</a>.");

$post_get = new GetVarClass();
$textid = $post_get->getint("id");
$header = $post_get->getvar("header");
$text_public = $post_get->getvar("text_public");
$text_private = $post_get->getvar("text_private");

$rights = $post_get->getarray_cb("right", "getint");
var_dump($rights);

if(!$header)
	die("Не могу создать текст без заголовка.");

$sql = "UPDATE ".PREF."texts
  SET header='$header',
      text_public='$text_public',
      text_private='$text_private'
  WHERE id=$textid
  LIMIT 1";
query($sql);

$sql = "DELETE FROM ".PREF."text_rights
  WHERE textid=$textid";
query($sql);

if(count($rights)){
  foreach($rights as &$right){
    $right = "($textid, $right)";
    }
  $valuesLine = implode(", ", $rights);
  $sql = "INSERT INTO ".PREF."text_rights (textid, userid)
    VALUES $valuesLine";
  query($sql);
  }

redirect("/text-edit.php?" . http_build_query(["id" => $textid]));

?>
