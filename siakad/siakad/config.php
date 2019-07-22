<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'ESAUNGGULGATE' );
	define( 'SITE_NAME', 'SIM Akademik' );
	
	$conf['row_autocomplete'] = 20;
	$conf['row_diskusi'] = 20;
	$conf['row_pesan'] = 20;
	$conf['merchant_id']='INABA2194';
	$conf['merchant_password']='212INABA180';
	
	$conf['gate_dir'] = '../../front/';
	$conf['menu_path'] = $conf['gate_dir'].'gate/index.php?page=menu';
	$conf['logout_path'] = $conf['gate_dir'].'gate/index.php?page=logout';
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['controller_dir'] = '../controller/';
	$conf['ui_dir'] = '../ui/';
	$conf['includes_dir'] = realpath('../includes').'/'; //'../includes/';
	$conf['helpers_dir'] = '../helpers/';
	$conf['upload_dir'] = 'uploads/';
	$conf['uploadkemahasiswaan_dir'] = '../../kemahasiswaan/kemahasiswaan/uploads/';
	
	$conf['fotopeg_dir'] = 'uploads/fotopegawai/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad_fix';

	$conf['elearning_db_driver'] = 'postgres';
	$conf['elearning_db_host'] = 'localhost';
	$conf['elearning_db_port'] = '5432';
	$conf['elearning_db_username'] = 'elearning';
	$conf['elearning_db_password'] = 'keberkahan2020';
	$conf['elearning_db_dbname'] = 'elearning';
	
	$conf['sdm_db_driver'] = 'mssql';
	$conf['sdm_db_host'] = 'UEU_SDM';
	$conf['sdm_db_port'] = '1433';
	$conf['sdm_db_username'] = 'sa';
	$conf['sdm_db_password'] = 'windows3U';
	$conf['sdm_db_dbname'] = 'ueudb';
	
	//variable smtp
	$conf['smtp_host'] = 'smtp.office365.com';
	$conf['smtp_user'] = 'keuangan@inaba.ac.id';
	$conf['smtp_pass'] = 'Support5678';
	$conf['smpt_admin']='SIM Akademik';
	$conf['smtp_email']='keuangan@inaba.ac.id';

	//variabel untuk LDAP
	$conf['ldap_server'] = '172.16.88.121';
	$conf['ldap_port'] = 389;
	$conf['ldap_user'] = 'cn=admin,dc=esaunggul,dc=ac,dc=id';
	$conf['ldap_pass'] = 'bp15t1zoob9';
	$conf['ldap_base'] = 'ou=people,dc=esaunggul,dc=ac,dc=id';

/*	$conf['h2h_db_driver'] = 'postgres';
	$conf['h2h_db_host'] = '';
	$conf['h2h_db_username'] = '';
	$conf['h2h_db_password'] = '';
	$conf['h2h_db_dbname'] = '';
*/
?>
