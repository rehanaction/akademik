<?php
	// konfigurasi database
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = 'localhost';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'siakad';
	$conf['db_password'] = 'keberkahan2020';
	$conf['db_dbname'] = 'siakad_fix_coba';

	// konfigurasi ssh
	$conf['ssh_active'] = true;
	$conf['ssh_host'] = 'siakad.inaba.ac.id';
	$conf['ssh_port'] = '22';
	$conf['ssh_username'] = 'h2h';
	$conf['ssh_password'] = 'jakarta2020';
	
	// konfigurasi aplikasi
	$conf['wsdl_path'] = 'http://siakad.inaba.ac.id/h2h/wsdl/service.wsdl';
	$conf['rekon_dir'] = '/home/';
	
	$conf['test_ws'] = true;
	$conf['test_companycode'] = 'SVM';
	$conf['test_channelid'] = 'TELLER';
	$conf['test_terminalid'] = '01';
?>

