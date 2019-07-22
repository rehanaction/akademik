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
	}
?>
