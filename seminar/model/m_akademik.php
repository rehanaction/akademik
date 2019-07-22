<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
		
	class mAkademik{
		function getDataSmu($idsmu){
			global $conn;
			$sql = "SELECT * FROM pendaftaran.lv_smu  WHERE idsmu = '$idsmu'";
			return $conn->SelectLimit($sql,1);
		}
		function getDataPonpes($idponpes){
			global $conn;
			$sql = "SELECT * FROM pendaftaran.lv_ponpes  WHERE idponpes = '$idponpes'";
			return $conn->SelectLimit($sql,1);
		}
		function getDataPt($idptasal){
			global $conn;
			$sql = "SELECT * FROM pendaftaran.lv_ptasal  WHERE idptasal = '$idptasal'";
			return $conn->SelectLimit($sql,1);
		}
		
		function random($panjang)
		{
		   $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
		   $string = '';
		   for($i = 0; $i < $panjang; $i++) {
		   $pos = rand(0, strlen($karakter)-1);
		   $string .= $karakter{$pos};
		   }
			return $string;
		}
	}
?>
