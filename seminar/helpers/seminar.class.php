<?php
	// fungsi pembantu modul seminar
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Seminar {
		// cek apakah sudah login
		function isAuthenticated() {
			$cek = self::getNoPendaftar();
			
			return empty($cek) ? false : true;
		}
		
		// ambil no pendaftar
		function getNoPendaftar() {
			return $_SESSION[SITE_ID]['FRONT']['NOPENDAFTAR'];
		}
		
		// ambil nim
		function getNIM() {
			return $_SESSION[SITE_ID]['FRONT']['NIM'];
		}
		
		// ambil nip
		function getNIP() {
			return $_SESSION[SITE_ID]['FRONT']['NIP'];
		}
	}
?>
