<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	define( 'SITE_ID', 'ESAUNGGULGATE' );
	define( 'SITE_NAME', 'Esa Unggul' );
	
	$conf['gate_dir'] = '../';
	
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'html/';
	$conf['view_dirsiakad'] = '../../siakad';
	$conf['controller_dir'] = '../controller/';
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad_fix';
	
	//variabel untuk LDAP
	$conf['ldap_server'] = 'localhost';
	$conf['ldap_port'] = 389;
	$conf['ldap_user'] = 'cn=ldapadm,dc=mylive,dc=net,dc=id';
	$conf['ldap_pass'] = '12345678';
	$conf['ldap_base'] = 'ou=people,dc=mylive,dc=net,dc=id';
	
	$conf['ADMIN_path'] = '../admin/index.php?page=login';
	$conf['KEU_path'] = '../../keuangan/keu/index.php?page=login';
	$conf['ASET_path'] = '../../aset/aset/index.php?page=login';
	$conf['SDM_path'] = '../../sdm/gate/index.php?page=login';
	$conf['PERPUS_path'] = '../../perpus/index.php?page=sys_gate';
	$conf['AKAD_path'] = '../../siakad/siakad/index.php?page=login';
	$conf['PMB_path'] = '../../pendaftaran/back/index.php?page=login';
	$conf['H2H_path'] = '../../keuanganakademik/keuanganakademik/index.php?page=login';
	$conf['MHS_path'] = '../../kemahasiswaan/kemahasiswaan/index.php?page=login';
	$conf['SEMINAR_path'] = '../../seminar/back/index.php?page=login';

?>
