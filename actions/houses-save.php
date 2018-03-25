<?php

include "../include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
  die("У вас недостаточно прав доступа, чтобы редактировать расселение. Вернитесь на <a href=\"/\">главную страницу</a>.");

$post_get = new GetVarClass();
$payload = $post_get->getarray_cb("payload", "getint");

foreach($payload as $userid => $houseid){
  if($userid && is_int($houseid)){
    $sql = "UPDATE ".PREF."users
      SET houseid = $houseid
      WHERE id = $userid
      LIMIT 1";
    query($sql);
  }
}

?>
