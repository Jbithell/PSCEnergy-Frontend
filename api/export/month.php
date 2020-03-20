<?php
require_once __DIR__ . "/../apihead.php";
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=pscenergydata.csv');
header('Pragma: no-cache');

if (!isset($_GET['meterid']) or !isset($_GET['value'])) apierror("No GET parameter");

$meterresult = $DB->query('SELECT meterid, metername FROM meters WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" AND display=1 LIMIT 1');
if (!$meterresult) apierror("Meter not found");
$meter = $meterresult->fetchArray();
if (!$meter) apierror("Meter not found");


$dataresult = $DB->query('SELECT utctime, kwh FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" AND strftime("%m %Y", utctime, "unixepoch") = "' . $DB->escapeString($_GET['value']) . '" GROUP BY strftime("%M %H %d %m %Y", utctime, "unixepoch")  ORDER BY utctime ASC ');
if (!$dataresult) apierror("No data for meter");
$output = 'D-M-Y H:M, kW reading' . "\n";
while($row = $dataresult->fetchArray()) {
	$output .= date("d-m-Y h:i", $row['utctime']) . "," . $row['kwh'] . "\n";
}
die($output);
