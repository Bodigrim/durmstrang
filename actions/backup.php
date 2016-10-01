<?php

include "../include/config.php";

function sendSqlHeaders($filename){
  sendDownloadHeaders("application/sql", $filename);
}

function commandToDumpTables($tableNames){
  if(!count($tableNames))
    throw new Exception("dumpTables: empty list of tables");

  $prependPref = function($s){return PREF . $s;};

  $command = "mysqldump --compact"
           . " --user=" . DB_USER
           . " --password=" . DB_PASS
           . " " . DB_NAME
           . " " . implode(" ", array_map($prependPref, $tableNames));

  return $command;
}

$editorid = loginbycookie();
if(!isAdmin($editorid))
	die("У вас недостаточно прав доступа, чтобы скачать бекап. ");

sendSqlHeaders(PROJECT_NAME . ".sql");

$tableNames =
  [ "sessions"
  , "users"
  , "config"
  , "messages"
  ];
passthru(commandToDumpTables($tableNames));

?>
