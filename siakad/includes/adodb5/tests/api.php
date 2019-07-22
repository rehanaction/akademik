<?php
$DBServer = '172.16.88.21'; // server name or IP address
$DBUser = 'postgres';
$DBPass = 'sembarang';
$DBName = 'akademik';
$dsn = "pgsql:host=$DBServer;port=5432;dbname=$DBName;user=$DBUser;password=$DBPass";
$conn = new PDO($dsn);
$schema = $conn->query('SET search_path TO akademik');



if($_GET['aksi'] == 'datadiri'){

}

?>