<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'ESAUNGGULMHS' );
	define( 'SITE_NAME', 'SIM Kemahasiswaan' );
	
	$conf['row_autocomplete'] = 20;
	$conf['row_diskusi'] = 20;
	$conf['row_pesan'] = 20;
	$conf['kdpti'] = '071079';
	
	$conf['gate_dir'] = '../../front/';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	$conf['image_fullpath']='http://siakad.esaunggul.ac.id/kemahasiswaan/kemahasiswaan/';	
	
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
	
	$conf['sdm_db_driver'] = 'mssql';
	$conf['sdm_db_host'] = 'UEU_SDM';
	$conf['sdm_db_port'] = '1433';
	$conf['sdm_db_username'] = 'sa';
	$conf['sdm_db_password'] = 'windows3U';
	$conf['sdm_db_dbname'] = 'ueudb';

	//variabel untuk email
	$conf['smtp_host'] = 'smtp.gmail.com';
	$conf['smtp_user'] = 'sevima.dummy@gmail.com';
	$conf['smtp_pass'] = 's3mbarang123';
	$conf['smtp_admin'] = 'SIM Akademik';
	$conf['smtp_email'] = 'sevima.dummy@gmail.com';
?>