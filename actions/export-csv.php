<?php

include "../include/config.php";

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы скачать CSV. ");

header("Content-Type: text/csv");
header("Content-Disposition: attachment;filename=\"base.csv\"");
header("Cache-Control: max-age=0");

$out = fopen('php://output', 'w');

$sql = "SELECT * 
	FROM ".PREF."users AS u  
	ORDER BY u.id";
$result = query($sql);

while($row = fetch_assoc($result)){
	unset($row["pwhash"], $row["pw"], $row["active"], $row["activecode"], $row["added"], $row["is_admin"], $row["photo_src"], $row["updated"], $row["unread"], $row["quenta"]);
	fputcsv($out, $row);
	}

fclose($out);

?>
