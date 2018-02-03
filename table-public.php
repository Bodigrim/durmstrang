<?php

include "include/config.php";

$editorid = loginbycookie();
if(isAdmin($editorid))
  redirect("/table.php");

$sql = "SELECT u.*
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.name<>''
    AND u.publicity<>'nothing'
    AND u.status IN ('query', 'participant')
  ORDER BY u.id DESC";
$result = query($sql);
$userData = fetch_assocs($result);

foreach($userData as &$user){
  $photoname = photoFileName($user["email"]);
  $user["photo"] = file_exists("photos/$photoname.jpg") ? "$photoname.jpg" : "";
  }

$render_data = [
	"users"       => $userData,
	"statuses"    => $langStatuses,
	"ordStatuses" => $ordStatuses,
  "blocks"      => $langBlocks,
	"isAdmin"     => (bool)isAdmin($editorid),
	"isLoggedIn"  => (bool)$editorid,
  ];

$ret = constructTwig()->render("table-public.twig", $render_data);

echo $ret;

?>
