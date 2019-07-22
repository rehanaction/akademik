<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// konfigurasi aplikasi
	$conf['lang'] = 'en';
	$conf['site'] = 'SIM Akademik Universitas Esa Unggul';
	
	$conf['root'] = 'http://192.168.1.8';
	$conf['url'] = '/esademo/www/akademik/siakad/siamobile/';
	$conf['gate'] = $conf['root'].'/esademo/www/akademik/front/gate/';
	
	// konfigurasi path
	$conf['model_dir'] = '../model/';
	$conf['view_dir'] = 'class/'; // diisi class, bukan view :D
	$conf['includes_dir'] = '../includes/';
	$conf['helpers_dir'] = '../helpers/';
	
	// konfigurasi database
	$conf['db_driver'] = 'postgres';
	$conf['db_host'] = '192.168.1.8';
	$conf['db_port'] = '5433';
	$conf['db_username'] = 'postgres';
	$conf['db_password'] = 'sembarang';
	$conf['db_dbname'] = 'esademo';
	
	//konfigurasi notifikasi
	$conf['api_key']		= 'AIzaSyB2XU6hgYgP0bz7MzLi3ZiITGRIyMOe8CM';
	$conf['sender_id']		= '664823514690';
	$conf['package_path']	= 'com.sevima.sia';
?>