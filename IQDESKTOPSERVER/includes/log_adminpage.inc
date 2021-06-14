<?php
// Generation of simple log file on admin page
include("../includes/getIPAddress.php");

// Collecting information 
$IP_ADDRESS = getIPAddress();
$ADMIN_USER = $_SERVER['REMOTE_USER'];

// Get date
$DATE = date('Y-m-d H:i:s');

// Collecting variables
$do = $_GET["do"];
$action = $_GET["action"];

// Combine in string
$LOG = "IP_ADDRESS=".$IP_ADDRESS;
$LOG .= ":::"."DATE=".$DATE;
$LOG .= ":::"."ADMIN_USER=".$ADMIN_USER;
$LOG .= ":::"."do=".$do;
$LOG .= ":::"."action=".$action;

$myfile = fopen("../logs/logs_adminpage.log", "a") or die("Unable to open file!");
fwrite($myfile, "\n". $LOG);
fclose($myfile);
?>