<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'SIAKAD' );
	define( 'SITE_NAME', 'SIM Akademik' );
	
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
	
	$conf['fotopeg_dir'] = '../../kepegawaian/simpeg/uploads/fotopeg/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad3';
	
	
/*	$conf['h2h_db_driver'] = 'postgres';
	$conf['h2h_db_host'] = '';
	$conf['h2h_db_username'] = '';
	$conf['h2h_db_password'] = '';
	$conf['h2h_db_dbname'] = '';
*/
?>