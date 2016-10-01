<?php

include "../include/config.php";

function sendCsvHeaders($filename){
  sendDownloadHeaders("text/csv", $filename);
}

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы скачать CSV. ");

$hiddenFields =
  [ "pwhash"
  , "pw"
  , "active"
  , "activecode"
  , "added"
  , "is_admin"
  , "photo_src"
  , "updated"
  , "unread"
  , "quenta"
  ];

sendCsvHeaders(PROJECT_NAME . ".csv");

$out = fopen('php://output', 'w');

$sql = "SELECT *
	FROM ".PREF."users AS u
	ORDER BY u.id";
$result = query($sql);

while($row = fetch_assoc($result)){
  foreach($hiddenFields as $hiddenField){
    unset($row[$hiddenField]);
  }
	fputcsv($out, $row);
	}

fclose($out);

?>
