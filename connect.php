<?php

$con_string = "host=".$dbhost." port=".$dbport." dbname=".$dbdb." user=".$dbuser." password=".$dbpass;
$cdb = pg_connect($con_string) or die ("Sem conexão com o banco de dados");

?>