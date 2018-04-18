<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
  die("У вас недостаточно прав доступа, чтобы редактировать расселение. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT u.houseid, u.id, u.character_name, u.email
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.name<>''
    AND u.email NOT LIKE '%@example.com'
    AND u.status IN ('query', 'participant')
  ORDER BY u.character_name ASC";
$userCount = (int)db_result00($sql);
$result = query($sql);
$inhabitants = fetch_rows($result, 1);

foreach($inhabitants as &$houseInhabitants){
  foreach($houseInhabitants as &$inhabitant){
    $photoname = photoFileName($inhabitant[2]);
    $inhabitant["photo"] = file_exists("photos/$photoname.jpg") ? "$photoname.jpg" : "";
    }
  }

$render_data = [
  "houses"       => $housesList,
  "inhabitants"  => $inhabitants,
  ];

$ret = constructTwig()->render("houses.twig", $render_data);

echo $ret;

?>
