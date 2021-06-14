<?php
// Generation of simple log file for activity on user page
include("includes/getIPAddress.php");

// Collecting information 
$IP_ADDRESS = getIPAddress();

// Get date
$DATE = date('Y-m-d H:i:s');

// Collecting all possible variables
$do = $_GET["do"];
$csvfile = $_GET["csvfile"];
$control = $_GET["control"];
$user = $_GET["user"];
$action = $_GET["action"];
$image = $_GET["image"];
$nrcores = $_GET["nrcores"];
$memgb = $_GET["memgb"];
$theme = $_GET["theme"];
$allow_sudo = $_GET["allow_sudo"];
$safety_check = $_GET["safety_check"];
$safety_check_required = $_GET["safety_check_required"];

// Combine in string
$LOG = "IP_ADDRESS=".$IP_ADDRESS;
$LOG .= ":::"."DATE=".$DATE;
$LOG .= ":::"."do=".$do;
$LOG .= ":::"."csvfile=".$csvfile;
$LOG .= ":::"."control=".$control;
$LOG .= ":::"."user=".$user;
$LOG .= ":::"."action=".$action;
$LOG .= ":::"."image=".$image;
$LOG .= ":::"."nrcores=".$nrcores;
$LOG .= ":::"."memgb=".$memgb;
$LOG .= ":::"."theme=".$theme;
$LOG .= ":::"."allow_sudo=".$allow_sudo;
$LOG .= ":::"."safety_check=".$safety_check;
$LOG .= ":::"."safety_check_required=".$safety_check_required;

$myfile = fopen("logs/logs_userpage.log", "a") or die("Unable to open file!");
fwrite($myfile, "\n". $LOG);
fclose($myfile);
?>