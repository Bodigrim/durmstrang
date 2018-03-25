<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы просматривать список текстов. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT t.*, GROUP_CONCAT(u.character_name ORDER BY u.character_name SEPARATOR ', ') as count
	FROM ".PREF."texts AS t
  LEFT JOIN ".PREF."text_rights AS tr ON t.id=tr.textid
  LEFT JOIN ".PREF."users AS u ON u.id=tr.userid
  GROUP BY t.id
	ORDER BY t.id DESC";
$result = query($sql);
$textDatа = fetch_assocs($result);

$render_data = [
	"texts"       => $textDatа,
  ];

$ret = constructTwig()->render("texts.twig", $render_data);

echo $ret;

?>
