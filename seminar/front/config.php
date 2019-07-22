<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'SEMINAR' );
	define( 'SITE_NAME', 'Seminar' );

	$conf['reg_dir'] = '../';
	$conf['row_autocomplete'] = 30;
	
	$conf['model_dir'] = '../model/';
	$conf['kemahasiswaanmodel_dir'] = '../../kemahasiswaan/model/';
	$conf['view_dir'] = 'html/';
	$conf['frontcontroller_dir'] = 'controller/';
	$conf['controller_dir'] = '../controller/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['ui_dir'] = '../ui/';
	$conf['upload_dir'] = '../back/uploads/';
	$conf['frontmodel_dir'] = 'model/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = '172.16.88.21';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'postgres';
	$conf['db_password'] = 'sembarang';
	$conf['db_dbname'] = 'akademik';
	
	$conf['smtp_host'] = 'smtp.gmail.com';
	$conf['smtp_user'] = 'dayat.informatic@gmail.com';
	$conf['smtp_pass'] = '';
	$conf['smtp_admin'] = 'SIM Pendaftaran';
	$conf['smtp_email'] = 'dayat.informatic@gmail.com';
?>