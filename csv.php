<?php
require_once('session.php');

$rf = isset($_GET["rf"]) ? trim($_GET["rf"]) : '0';
$tp = isset($_GET["tp"]) ? trim($_GET["tp"]) : '0';

if ($rf == '0' || $tp == '0'){
	header('location:relatorios.php');
} else {
	
	header("Content-Type: application/csv");
	header("Content-Disposition: attachment; filename=".$rf.".csv");
	header("Pragma: no-cache");
	
	if (file_exists("csv/".$rf.".csv")){
		//require_once("csv/".$rf.".csv");
		readfile("csv/".$rf.".csv");
	} else {
		header("location:".$tp.".php");
	}
}
?>
