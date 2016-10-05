<?php

include "../include/config.php";

$post_get = new GetVarClass();
$email = $post_get->getemail("email");

if(!$email)
  die("Редактирование взноса невозможно: введите корректный e-mail. ");

$editorid = loginbycookie();
if(!isAdmin($editorid))
  die("У вас недостаточно прав доступа, чтобы изменить состояние взноса {$email}. ");

$payment = $post_get->getvar("payment", 1, 0);

$sql = "UPDATE ".PREF."users
  SET payment_room=$payment
  WHERE email='$email'
  LIMIT 1";
query($sql);

?>
