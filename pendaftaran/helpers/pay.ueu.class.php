<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include kelas dasar
	require_once(__DIR__.'/pay.class.php');
	
	class Pay extends BasePay {
		// setting sevimapay
		const PAY_CLIENT_ID = '015';
		const PAY_SECRET_ID = '3c54bb94563a4580a991fb2cb4564920';
		const PAY_ADMIN_AMOUNT = 3000;
		const PAY_DEVELOPMENT = false;

		// default datetime expired
		protected static function getDefaultExpired() {
			return date('c',mktime(23,59,59,date('n'),date('j'),date('Y'))); // hanya untuk hari ini
		}
	}
?>