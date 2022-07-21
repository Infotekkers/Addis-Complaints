<?php
include "../../inc/redirect.php";
$base_url = "http://localhost:3000";
session_start();
session_destroy();



Redirect("$base_url/auth/login/login.php");
exit;