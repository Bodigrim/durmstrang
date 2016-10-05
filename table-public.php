<?php

include "include/config.php";

$editorid = loginbycookie();
if(isAdmin($editorid))
  redirect("/table.php");

$sql = "SELECT u.*, g.group_name as groupName
  FROM ".PREF."users AS u
  LEFT JOIN ".PREF."users AS g ON u.group_id=g.id
  WHERE u.active=1 AND u.name<>''
  ORDER BY u.name ASC";
$result = query($sql);
$userData = fetch_assocs($result);

$render_data = [
	"users"       => $userData,
	"statuses"    => $langStatuses,
	"ordStatuses" => $ordStatuses,
	"isAdmin"     => (bool)isAdmin($editorid),
	"isLoggedIn"  => (bool)$editorid,
  ];

$ret = constructTwig()->render("table-public.twig", $render_data);

echo $ret;

?>
