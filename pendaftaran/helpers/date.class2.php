<?php
	// fungsi bantuan waktu
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class Date {
		// nama hari di bahasa indonesia
		function arrayDay($full=true) {
			$hari = array();
			
			if($full) {
				$hari[1] = 'Senin';
				$hari[2] = 'Selasa';
				$hari[3] = 'Rabu';
				$hari[4] = 'Kamis';
				$hari[5] = 'Jumat';
				$hari[6] = 'Sabtu';
				$hari[7] = 'Minggu';
			}
			else {
				$hari[1] = 'Senin';
				$hari[2] = 'Selasa';
				$hari[3] = 'Rabu';
				$hari[4] = 'Kamis';
				$hari[5] = 'Jumat';
				$hari[6] = 'Sabtu';
				$hari[7] = 'Minggu';
			}
			
			return $hari;
		}
		
		function indoDay($nhari,$full=true) {
			$hari = self::arrayDay();
			
			return $hari[$nhari];
		}
		
		// nama bulan di bahasa indonesia
		function arrayMonth($full=true) {
			$bulan = array();
			
			if($full) {
				$bulan[1] = 'Januari';
				$bulan[2] = 'Pebruari';
				$bulan[3] = 'Maret';
				$bulan[4] = 'April';
				$bulan[5] = 'Mei';
				$bulan[6] = 'Juni';
				$bulan[7] = 'Juli';
				$bulan[8] = 'Agustus';
				$bulan[9] = 'September';
				$bulan[10] = 'Oktober';
				$bulan[11] = 'Nopember';
				$bulan[12] = 'Desember';
			}
			else {
				$bulan[1] = 'Jan';
				$bulan[2] = 'Peb';
				$bulan[3] = 'Mar';
				$bulan[4] = 'Apr';
				$bulan[5] = 'Mei';
				$bulan[6] = 'Jun';
				$bulan[7] = 'Jul';
				$bulan[8] = 'Agu';
				$bulan[9] = 'Sep';
				$bulan[10] = 'Okt';
				$bulan[11] = 'Nop';
				$bulan[12] = 'Des';
			}
			
			return $bulan;
		}
		
		function indoMonth($nbulan,$full=true) {
			$bulan = self::arrayMonth($full);
			
			return $bulan[(int)$nbulan];
		}
		
		// dari tanggal excel
		function fromOADate($oadate) {
			return ($oadate-25510)*(60*60*24); // jam 07:00:00 
		}
		
		function indoDate($date){
			$tanggal=explode("-",$date);
			$tahun=$tanggal[0];
			$bulan=$tanggal[1];
			$hari=$tanggal[2];
			return $hari."-".self::indoMonth($bulan)."-".$tahun;
		}
		
		function RandomCode($max){
			//Huruf dan angka yang akan di acak
			$char = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y",
						  "Z","a","b","c","d","e","f","g","h","j","k","l","m","n","p","q","r","s","t","u","v","w","x",
						  "y","z","1","2","3","4","5","6","7","8","9");
			 
			$keys = array();
			while(count($keys) < $max) {
				$x = mt_rand(0, count($char)-1);
				if(!in_array($x, $keys)) {
				   $keys[] = $x;  
				}      
			}
			foreach($keys as $key => $val){
			   $random .= $char[$val]; 
			}
			return $random;
		}
		
		function convertTime($ptime) {
			if ($ptime) {
				$ttime = str_pad($ptime,4,"0",STR_PAD_LEFT);
				return substr($ttime,0,2) . ":" .  substr($ttime,2,2);
			}
			else 
				return "00:00";
		}
		
	}
?>