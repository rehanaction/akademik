<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'PENDAFTARAN' );
	define( 'SITE_NAME', 'Pendaftaran' );
	

	$conf['reg_dir'] = '../';
	$conf['row_autocomplete'] = 30;
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['frontcontroller_dir'] = 'controller/';
	$conf['controller_dir'] = '../controller/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['ui_dir'] = '../ui/';
	$conf['upload_dir'] = '../back/uploads/';
	$conf['akademikmodel_dir'] = '../../siakad/model/';
	
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = '192.168.1.8';
	$conf['db_port'] = '5433';
	$conf['db_username'] = 'postgres';
	$conf['db_password'] = 'sembarang';
	$conf['db_dbname'] = 'esademo';
	 
        //variable smtp
 /*       $conf['smtp_host']='smtp.gmail.com';
        $conf['smtp_user']='sim@esaunggul.ac.id';
        $conf['smtp_pass']='s1Mueu!d';
        $conf['smpt_admin']='SIM Akademik';
        $conf['smtp_email']='sim@esaunggul.ac.id';*/
 
	
	$conf['smtp_host'] = 'smtp.gmail.com';
	$conf['smtp_user'] = 'dayat.informatic@gmail.com';
	$conf['smtp_pass'] = '';
	$conf['smtp_admin'] = 'SIM Pendaftaran';
	$conf['smtp_email'] = 'dayat.informatic@gmail.com';
	
        
        
		
?>
