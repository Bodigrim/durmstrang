<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы просматривать список заявок. Вернитесь на <a href=\"/\">главную страницу</a>.");

$sql = "SELECT u.*
	FROM ".PREF."users AS u
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
