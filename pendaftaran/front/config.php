<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'PENDAFTARAN' );
	define( 'SITE_NAME', 'Pendaftaran' );
	
	$conf['reg_dir'] = '../';
	$conf['row_autocomplete']=30;
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['frontcontroller_dir'] = '../controller';
	$conf['controller_dir'] = '../controller/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['ui_dir'] = '../ui/';
	$conf['upload_dir'] = '../back/uploads/';
	$conf['akademikmodel_dir'] = '../../siakad/model/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad_fix';
	
	$conf['smtp_host'] = 'smtp.office365.com';
	$conf['smtp_user'] = 'support.it@inaba.ac.id';
	$conf['smtp_pass'] = 'Support5678';


?>
