<?php

include "../include/config.php";

cookie_drop("email");
cookie_drop("session");

redirect("/");

?>
