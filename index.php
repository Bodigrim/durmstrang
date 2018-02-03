<?php

include "include/config.php";

$editorid = loginbycookie();
if($editorid){
  redirect(isAdmin($editorid) ? "/table.php" : "/edit.php?" . http_build_query(["email" => idToEmail($editorid)]));
  }

$sql = "SELECT COUNT(*)
  FROM ".PREF."users AS u
  WHERE u.active=1
    AND u.name<>''
    AND u.publicity<>'nothing'
    AND u.status IN ('query', 'participant')";
$userCount = (int)db_result00($sql);

$a = rand(10, 99);
$b = rand(1, 9);
$c = $a + $b;

$render_data = [
  "a"     => $a,
  "b"     => $b,
  "chash" => antispamhash($c),
  "userCount" => $userCount . " " . multspell($userCount, "участник", "участника", "участников"),
  ];

$ret = constructTwig()->render("index.twig", $render_data);

echo $ret;

?>
