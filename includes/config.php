<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session start
session_start();

// Base URL
define('BASE_URL', 'http://localhost/attendance-system/');

// Timezone
date_default_timezone_set('Africa/Lagos'); // Or another valid African timezone