<?php
define("INCMS", 1);

require "define.php";

require "db.php";
$db_json = file_exists("db.json") ? "db.json" : "../db.json";
$servers = json_decode(file_get_contents($db_json));
db_and_errors_init($servers);

require "const.php";
require "getvar.php";
require "cookie.php";

require "sendpulseInterface.php";
require "sendpulse.php";
require "mail.php";

require "selectors.php";
require "misc.php";
require "file-upload.php";

mb_regex_encoding("utf-8");
mb_internal_encoding("utf-8");

require file_exists("vendor/autoload.php") ? "vendor/autoload.php" : "../vendor/autoload.php";

srand(microtime(true) * 1000);

?>
