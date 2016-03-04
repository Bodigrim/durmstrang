<?php

include "include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы просматривать список заявок. ");

$sql = "SELECT *
	FROM ".PREF."users
	WHERE active=1
	ORDER BY go_aqua_mortis DESC, id DESC";
$result = query($sql);
$userData = fetch_assocs($result);

$render_data = [
	"users"       => $userData,
	"statuses"    => $langStatuses,
	"ordStatuses" => $ordStatuses,
	"publicities" => $langPublicities,
	"countries"   => $langCountries,
	"birthes"     => $langBirthes,
	"ranks"       => $langRanks,
	"quotas"      => $langQuotas,
  ];

$ret = constructTwig()->render("table.twig", $render_data);

echo $ret;

?>
