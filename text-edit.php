<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы редактировать тексты. Вернитесь на <a href=\"/\">главную страницу</a>.");

$get_get = new GetVarClass("_GET");
$textid = $get_get->getint("id");

if(!$textid)
  die("Не указан идентификатор текста.");

$sql = "SELECT t.*
	FROM ".PREF."texts AS t
  WHERE t.id=$textid
  LIMIT 1";
$result = query($sql);
$textDatа = fetch_assoc($result);

$sql = "SELECT u.id, u.character_name, IF(tr.id, 1, 0) as selected
  FROM ".PREF."users AS u
  LEFT JOIN ".PREF."text_rights AS tr ON u.id=tr.userid AND tr.textid=$textid
  WHERE u.active=1
    AND u.character_name<>''
    AND u.email NOT LIKE '%@example.com'
    AND u.status IN ('query', 'participant')
  ORDER BY u.character_name ASC";
$result = query($sql);
$rights = fetch_assocs($result);

$render_data = $textDatа + [
  "rights"     => $rights,
  ];

$ret = constructTwig()->render("text-edit.twig", $render_data);

echo $ret;

?>
