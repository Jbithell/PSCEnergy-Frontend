<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');  
header('Content-Type: application/json');
date_default_timezone_set('UTC');

$DB = new SQLite3(__DIR__ . '/../database/main.sqlite3');
$DB->busyTimeout(10000); //10 secs

function apierror($message) {
	die(json_encode(["error" => true, "success" => false, "message" => $message]));
}
?>
