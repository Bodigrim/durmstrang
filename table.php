<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы просматривать список заявок. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT u.*, g.group_name as groupName
	FROM ".PREF."users AS u
	LEFT JOIN ".PREF."users AS g ON u.group_id=g.id
	WHERE u.active=1
	ORDER BY u.id DESC";
$result = query($sql);
$userData = fetch_assocs($result);

$render_data = [
	"users"       => $userData,
	"statuses"    => $langStatuses,
	"ordStatuses" => $ordStatuses,
  "blocks"      => $langBlocks,
  "bloods"      => $langBloods,
  ];

$ret = constructTwig()->render("table.twig", $render_data);

echo $ret;

?>
