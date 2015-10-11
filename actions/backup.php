<?php

include "../include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы скачать бекап. ");

$filename = "durmstrang.sql";

header("Content-Type: application/sql");
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$command = "mysqldump --compact --user=" . DB_USER . " --password=" . DB_PASS . " " . DB_NAME
					. " ".PREF."sessions ".PREF."users ".PREF."config ".PREF."messages";
passthru($command);

?>
