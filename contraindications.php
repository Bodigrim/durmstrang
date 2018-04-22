<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы просматривать список заявок. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT u.surname, u.name, u.contraindication
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.surname<>''
    AND u.contraindication<>''
    AND u.email NOT LIKE '%@example.com'
    AND u.status IN ('query', 'participant')
  ORDER BY u.surname ASC, u.name ASC";
$result = query($sql);
$userData = fetch_assocs($result);

$sql = "SELECT u.email, u.surname, u.name
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.surname<>''
    AND u.contraindication=''
    AND u.email NOT LIKE '%@example.com'
    AND u.status IN ('query', 'participant')
  ORDER BY u.surname ASC, u.name ASC";
$result = query($sql);
$missingContraindications = fetch_assocs($result);

$render_data = [
  "users"   => $userData,
  "missing" => $missingContraindications,
  ];

$ret = constructTwig()->render("contraindications.twig", $render_data);

echo $ret;

?>
