<?php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	include('ssp.class.php');
	
	/* DB tables to use with necesary joins */
	$table = "akademik.ms_mahasiswa";
	
	// Table's primary key
	$primaryKey = 'nim';
	
	// Array of database columns which should be read and sent back to DataTables. The `db` parameter represents the column name in the database
	//, while the `dt` parameter represents the DataTables column identifier.
	$columns = array(
		array( 'db' => 'nim', 'dt' => 0),
                array( 'db' => 'nama', 'dt' => 1)
	);
	$pg_details = array(
	 'user' => 'postgres',
	 'pass' => 'sembarang',
	 'db'   => 'akademik',
	 'host' => '172.16.88.21'
	);
	
	echo json_encode(
	 SSP::simple( $_GET, $pg_details, $table, $primaryKey, $columns)
	);
	
	?>