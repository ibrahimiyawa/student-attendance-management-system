<?php
require_once '../includes/config.php';

session_destroy();
header("Location: " . BASE_URL . "auth/login.php");
exit();