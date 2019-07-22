<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'UEUASET' );
	define( 'SITE_NAME', 'SIM Aset' );
	
	//variabel
	$conf['row_autocomplete'] = 20;
	$conf['row_diskusi'] = 20;
	$conf['row_pesan'] = 20;
	
	//gate path
	$conf['gate_dir'] = '../../gate/';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	
	//aset path
	$conf['model_dir'] = '../model/aset/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/aset/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/aset/';
	$conf['upload_dir'] = 'uploads/';
	$conf['docupload_dir'] = 'uploads/doc/';
	$conf['barcode_dir'] = '/var/www/simueu/ueu/includes/barcode/';
	$conf['foto_dir'] = 'f0t0/';
	$conf['full_urlaset'] = 'http://'.$_SERVER["HTTP_HOST"].'/ueu/aset/';
	
	//db access
	$conf['db_driver'] = 'mssql';
	$conf['db_host'] = 'UEU_ASET';
	$conf['db_username'] = 'sa';
	$conf['db_password'] = 'windows3U';
	$conf['db_dbname'] = 'ueudb';
	
	//institusi profile
	$conf['univ_name'] = "UNIVERSITAS ESA UNGGUL";
	$conf['univ_address'] = "Jln. Arjuna Utara No.9, Kebon Jeruk - Jakarta Barat 11510";
	$conf['univ_email'] = "humas@esaunggul.ac.id";
	$conf['univ_telp'] = "(021) 5674223, (021) 5682510";
	$conf['univ_fax'] = " (021) 5674248";
	
	//variabel untuk email
	$conf['smtp_host'] = 'email.esaunggul.ac.id';
	$conf['smtp_user'] = 'sim@esaunggul.ac.id';
	$conf['smtp_pass'] = 's1Mueu!d';
	$conf['smtp_admin'] = 'SIM Aset';
	$conf['smtp_email'] = 'sim@esaunggul.ac.id';

	$conf['keu_db_driver'] = 'postgres';
	$conf['keu_db_host'] = '172.16.88.21';
	$conf['keu_db_port'] = '5432';
	$conf['keu_db_username'] = 'postgres';
	$conf['keu_db_password'] = 'sembarang';
	$conf['keu_db_dbname'] = 'keuangan';
	
?>
