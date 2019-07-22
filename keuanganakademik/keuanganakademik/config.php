<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'ESAUNGGULKEUAKAD' );
	define( 'SITE_NAME', 'SIM Akademik' );
	define( 'GATESITE_ID', 'ESAUNGGULGATE' );
	
	$conf['namauniversitas']='UNIVERSITAS ESA UNGGUL';
	$conf['alamat'] = 'Jalan Arjuna Utara No.9, Kebon Jeruk - Jakarta Barat 11510 ';	
	$conf['alamatb'] = '021 - 5674223 (hunting) 021- 5682510 (direct) Fax : 021 - 5674248';
	$conf['alamatc'] = 'Website: www.esaunggul.ac.id, email: info@esaunggul.ac.id';
	
	$conf['email']='info@esaunggul.ac.id';
	$conf['notelp']=' (031) 8291920 - 8284508';
	$conf['fax']=' (031) 8298582';
	
	$conf['website']='http://www.esaunggul.ac.id';
	
	$conf['row_autocomplete'] = 20;
	$conf['row_diskusi'] = 20;
	$conf['row_pesan'] = 20;
	
	$conf['gate_dir'] = '../../front/';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['upload_dir'] = 'uploads/';
	
	$conf['fotopeg_dir'] = 'uploads/fotopegawai/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad_fix';
?>
