<?php

include "include/config.php";

$editorid = loginbycookie();
if($editorid){
  redirect(isAdmin($editorid) ? "/table.php" : "/edit.php?" . http_build_query(["email" => idToEmail($editorid)]));
  }

$a = rand(10, 99);
$b = rand(1, 9);
$c = $a + $b;

$render_data = [
  "a"     => $a,
  "b"     => $b,
  "chash" => antispamhash($c),
  ];

$ret = constructTwig()->render("index.twig", $render_data);

echo $ret;

?>
