<?php
require_once __DIR__ . "/../apihead.php";

if (!isset($_GET['meterid'])) apierror("No GET parameter");

$meterresult = $DB->query('SELECT meterid, metername FROM meters WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" AND display=1 LIMIT 1');
if (!$meterresult) apierror("Meter not found");
$meter = $meterresult->fetchArray();
if (!$meter) apierror("Meter not found");





//Combines the three months,weeks & years availability scripts - edit them first!
//Weeks
$dataresult = $DB->query('SELECT DISTINCT strftime("%W", utctime, "unixepoch") w, strftime("%Y", utctime, "unixepoch") y FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" ORDER BY utctime ASC');
if (!$dataresult) apierror("No data for meter and time range");
$weeksoutput = [];
while($row = $dataresult->fetchArray()) {
	$weekstartdatequery = $DB->query('SELECT strftime("%d-%m-%Y", ' . strtotime($row['y'] . "W" . $row['w']) . ', "unixepoch") firstdayofweek');
	$firstdayofweek = $weekstartdatequery->fetchArray();
	if (!$firstdayofweek) $firstdayofweek = "";
	else $firstdayofweek = $firstdayofweek["firstdayofweek"];
	$weeksoutput[] = ["week" => $row['w'], "weekrange" => "w/b " . $firstdayofweek,"year" => $row['y']];
}
//Months
$dataresult = $DB->query('SELECT DISTINCT strftime("%m", utctime, "unixepoch") m, strftime("%Y", utctime, "unixepoch") y FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" ORDER BY utctime ASC');
if (!$dataresult) apierror("No data for meter and time range");
$monthsoutput = [];
while($row = $dataresult->fetchArray()) {
	$monthsoutput[] = ["month" => $row['m'], "monthname" => date("F", strtotime("1-" . $row['m'] . "-" . $row['y'] . " 00:00:00")) ,"year" => $row['y']];
}
//Years
$dataresult = $DB->query('SELECT DISTINCT strftime("%Y", utctime, "unixepoch") y FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" ORDER BY utctime ASC');
if (!$dataresult) apierror("No data for meter and time range");
$yearsoutput = [];
while($row = $dataresult->fetchArray()) {
	$yearsoutput[] = ["year" => $row['y']];
}
die(json_encode(["YEARS" => $yearsoutput, "MONTHS" => $monthsoutput, "WEEKS" => $weeksoutput]));
