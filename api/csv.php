<?php
die();
require_once __DIR__ . "/apihead.php";

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=pscenergydata.csv');
header('Pragma: no-cache');

if (!isset($_GET['meterid'])) apierror("No GET parameter");

$meterresult = $DB->query('SELECT meterid, metername FROM meters WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" AND display=1 LIMIT 1');
if (!$meterresult) apierror("Meter not found");
$meter = $meterresult->fetchArray();
if (!$meter) apierror("Meter not found");

$dataresult = $DB->query('SELECT utctime, kwh FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" ORDER BY utctime ASC');
if (!$dataresult) apierror("No data for meter");
$output = 'D-M-Y H:S, kW reading' . "\n";
$currentminute = "";
while($row = $dataresult->fetchArray()) {
	if (date("Y-m-d h:i", $row['utctime']) == $currentminute) continue;
	$output .= date("d-m-Y h:i", $row['utctime']) . "," . $row['kwh'] . "\n";
	
	$currentminute = date("Y-m-d h:i", $row['utctime']); //Only give 1 reading per minute - so don't run out of memory!
}
die($output);
