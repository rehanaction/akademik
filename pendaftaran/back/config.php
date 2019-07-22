<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'PMB' );
	define( 'SITE_NAME', 'SIM Pendaftaran' );
	
	$conf['row_autocomplete'] = 20;
	$conf['row_diskusi'] = 20;
	$conf['row_pesan'] = 20;
	
	$conf['gate_dir'] = '../../front/';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	
	$conf['model_dir'] = '../model/';
	$conf['akademikmodel_dir'] = '../../siakad/model/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['upload_dir'] = 'uploads/';
	$conf['download_dir'] = 'downloads/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad_fix';
	
	//variabel untuk LDAP
	$conf['ldap_server'] = '172.16.88.121';
	$conf['ldap_port'] = 389;
	$conf['ldap_user'] = 'cn=admin,dc=esaunggul,dc=ac,dc=id';
	$conf['ldap_pass'] = 'bp15t1zoob9';
	$conf['ldap_base'] = 'ou=people,dc=esaunggul,dc=ac,dc=id';
	
	// ini set
	ini_set('post_max_size','10M');
	ini_set('upload_max_filesize','4M');
	ini_set('memory_limit','2048M');
	ini_set('max_execution_time','300');
?>
