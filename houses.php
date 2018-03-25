<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
  die("У вас недостаточно прав доступа, чтобы редактировать расселение. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT u.houseid, u.id, u.character_name
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.name<>''
    AND u.email NOT LIKE '%@example.com'
    AND u.status IN ('query', 'participant')";
$userCount = (int)db_result00($sql);
$result = query($sql);
$inhabitants = fetch_rows($result, 1);

$render_data = [
  "houses"       => $housesList,
  "inhabitants"  => $inhabitants,
  ];

$ret = constructTwig()->render("houses.twig", $render_data);

echo $ret;

?>
