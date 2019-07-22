<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'UEUGATE' );
	define( 'SITE_NAME', 'UEU Gate' );
		
	define( 'GATE_SITE_ID', 'UEUGATE' );
	
	$conf['row_autocomplete'] = 20;
	
	$conf['gate_dir'] = '../';
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'ueudb';
		
	$conf['ADMIN_path'] = '../admin/index.php?page=login';
	$conf['SDM_path'] = '../../ueu/sdm/index.php?page=login';
	$conf['ASET_path'] = '../../ueu/aset/index.php?page=login';
?>