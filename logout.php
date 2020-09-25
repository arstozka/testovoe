<?php
include_once("core/config.php");
checkLoggedIn("yes");
flushAuthSession();
header("Location: login.php");
?>