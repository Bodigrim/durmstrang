<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
  die("У вас недостаточно прав доступа, чтобы просматривать список заявок. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT u.*
  FROM ".PREF."users AS u
  WHERE u.active=1 AND u.surname<>''
  ORDER BY u.surname ASC, u.name ASC";
$result = query($sql);
$userData = fetch_assocs($result);

$render_data = [
  "users"       => $userData,
  ];

$ret = constructTwig()->render("passports.twig", $render_data);

echo $ret;

?>
