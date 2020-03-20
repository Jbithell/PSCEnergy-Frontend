<?php
require_once __DIR__ . "/apihead.php";
header('Content-Type: text/plain');

if (!isset ($_GET["kwh"])) die("Values must be passed");
$kwh = SQLite3::escapeString($_GET['kwh']);
if (!is_numeric ($kwh)) die("NOT A NUMBER");

$dataresult = $DB->exec('INSERT INTO readings(meterid,kwh,utctime) VALUES ("1","' . $kwh . '","' . time() . '");');
if (!$dataresult) die("FAIL");
else die("DONE");

?>