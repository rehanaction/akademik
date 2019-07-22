<?php
	// konfigurasi database
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = '172.16.88.21';
	$conf['db_port'] = '5432';
	$conf['db_username'] = 'postgres';
	$conf['db_password'] = 'sembarang';
	$conf['db_dbname'] = 'akademikdummy';
	
	// konfigurasi aplikasi
	$conf['wsdl_path'] = '/var/www/simueu/h2htrial/wsdl/service.wsdl';
	$conf['rekon_dir'] = '/home/';
	
	$conf['test_ws'] = true; // false;
	$conf['test_companycode'] = 'BRI';
	$conf['test_channelid'] = 'TELLER';
	$conf['test_terminalid'] = '01';
?>
