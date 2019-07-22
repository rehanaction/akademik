<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'UEUSDM' );
	define( 'SITE_NAME', 'SIM Human Resources Management' );
	
	//gate path
	$conf['gate_dir'] = '../../gate/';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	
	//sdm path
	$conf['model_dir'] = '../model/sdm/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/sdm/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/sdm/';
	$conf['uploads_dir'] = 'up_l04ds/';
	$conf['docuploads_dir'] = 'up_l04ds/d0c/';
	$conf['temp_dir'] = 'temp/';
	$conf['logo_rep'] = 'http://simueu.esaunggul.ac.id/ueu/sdm/images/logo_rep.png';
	$conf['img_dir'] = 'http://simueu.esaunggul.ac.id/ueu/sdm/images/';
	$conf['uploads_dirrep'] = 'http://simueu.esaunggul.ac.id/ueu/sdm/up_l04ds/';

	//portal
	$conf['url_portal'] = 'http://recruitment.esaunggul.ac.id/rekrutmen/';
	$conf['delete_file_portal'] = $conf['url_portal'].'delete_file/';
	$conf['uploads_portal'] = $conf['url_portal'].'uploads/';
	$conf['token_portal'] = 'a168e87d83582d04b4081f9c1fde5bc3';
	
	//variabel
	$conf['page_title'] = "SIM HRM ESA UNGGUL";
	$conf['row_autocomplete'] = 20;
	
	//institusi profile
	$conf['univ_name'] = "UNIVERSITAS ESA UNGGUL";
	$conf['univ_address'] = "Jalan Arjuna Utara No.9, Kebon Jeruk - Jakarta Barat 11510";
	$conf['univ_email'] = "humas@esaunggul.ac.id";
	$conf['univ_telp'] = "(021) 5674223, (021) 5682510";
	$conf['univ_fax'] = " (021) 5674248";
	
	//variabel untuk email
	$conf['smtp_host'] = 'smtp.gmail.com';
	$conf['smtp_user'] = 'sim@esaunggul.ac.id';
	$conf['smtp_pass'] = 's1Mueu!d';
	$conf['smtp_admin'] = 'Administrator HRM';
	$conf['smtp_email'] = 'sim@esaunggul.ac.id';
	
	//variabel untuk rekrutmen
	$conf['portalsmtp_host'] = 'smtp.gmail.com';
	$conf['portalsmtp_user'] = 'recruitment@esaunggul.ac.id';
	$conf['portalsmtp_pass'] = 'BPSDM2017';
	$conf['portalsmtp_admin'] = 'Universitas Esa Unggul';
	$conf['portalsmtp_email'] = 'recruitment@esaunggul.ac.id';
	
	//db access
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'ueudb';

	

?>
