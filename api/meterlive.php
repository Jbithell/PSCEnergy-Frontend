<?php
require_once __DIR__ . "/apihead.php";

if (!isset($_GET['meterid'])) apierror("No GET parameter");

$meterresult = $DB->query('SELECT meterid, metername FROM meters WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" AND display=1 LIMIT 1');
if (!$meterresult) apierror("Meter not found");
$meter = $meterresult->fetchArray();
if (!$meter) apierror("Meter not found");

$dataresult = $DB->query('SELECT utctime, kwh FROM readings WHERE meterid="' . $DB->escapeString($_GET['meterid']) . '" ORDER BY utctime DESC LIMIT 1');
if (!$dataresult) apierror("No data for meter");
$data = $dataresult->fetchArray();

if ($data['utctime'] < (time()-(60*60*2))) die("OUTOFSYNC"); //Only return if less than 2 hrs old
elseif (isset($_GET['updatecheck'])) die("UPTODATE");
else die($data['kwh']);
