<?php
require_once __DIR__ . "/../apihead.php";

if (!isset($_GET['meterid'])) apierror("No GET parameter");

$meterresult = $DB->query('SELECT meterid, metername FROM meters WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" AND display=1 LIMIT 1');
if (!$meterresult) apierror("Meter not found");
$meter = $meterresult->fetchArray();
if (!$meter) apierror("Meter not found");


$dataresult = $DB->query('SELECT DISTINCT strftime("%m", utctime, "unixepoch") m, strftime("%Y", utctime, "unixepoch") y FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" ORDER BY utctime ASC');
if (!$dataresult) apierror("No data for meter and time range");
$output = [];
while($row = $dataresult->fetchArray()) {
	$output[] = ["month" => $row['m'], "monthname" => date("F", strtotime("1-" . $row['m'] . "-" . $row['y'] . " 00:00:00")) ,"year" => $row['y']];
}
die(json_encode($output));
