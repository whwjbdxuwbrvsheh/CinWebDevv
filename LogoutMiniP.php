<?php

session_start();
session_destroy();
header("Location: LoginMiniP.php");

exit();

?>