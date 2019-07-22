<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'ESAUNGGULGATE' );
	define( 'SITE_NAME', 'Administrasi SIM' );
		
	define( 'GATE_SITE_ID', 'UEUGATE' );
	
	$conf['row_autocomplete'] = 20;
	
	$conf['gate_dir'] = '../';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	
	$conf['sdm_dir'] = '../../sdm/';
	$conf['aset_dir'] = '../../aset/';
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'ueudb';
?>
