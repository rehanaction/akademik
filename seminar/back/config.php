<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'PMBESAUNGGUL' );
	define( 'SITE_NAME', 'SIM Pendaftaran' );
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
	$conf['akademikmodel_dir'] = '../../siakad/model/';
	$conf['kemahasiswaanmodel_dir'] = '../../kemahasiswaan/model/';
	$conf['view_dir'] = 'html/';
	$conf['frontview_dir'] = '../front/html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['upload_dir'] = 'uploads/';
	$conf['download_dir'] = 'downloads/';

	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = '172.16.88.21';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'postgres';
	$conf['db_password'] = 'sembarang';
	$conf['db_dbname'] = 'akademik';
	
	/*$conf['h2h_db_driver'] = 'postgres';
	$conf['h2h_db_host'] = '192.168.1.8';
	$conf['h2h_db_username'] = 'postgres';
	$conf['h2h_db_password'] = 'sembarang';
	$conf['h2h_db_dbname'] = 'iain_h2h_existing';*/
	
	// ini set
	ini_set('post_max_size','10M');
	ini_set('upload_max_filesize','4M');
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time','300');
?>
