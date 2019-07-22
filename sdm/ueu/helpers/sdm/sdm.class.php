<?php
	// fungsi pembantu modul HRM
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class SDM {
		// apakah role adalah Pegawai
		function isPegawai() {
			$role = Modul::getRole();
			
			if($role == 'Peg')
				return true;
			else
				return false;
		}
		
		function getIDPegawai($conn,$r_key){
			require_once(Route::getModelPath('pegawai'));
			
			if (!isset($_SESSION[SITE_ID]['IDPEGAWAI'])){
				$idpegawai = mPegawai::getIDPegawai($conn,$r_key);
				$_SESSION[SITE_ID]['IDPEGAWAI'] = $idpegawai;
			}else
				$idpegawai = $_SESSION[SITE_ID]['IDPEGAWAI'];
				
			return $idpegawai;
		}
				
		function getValid(){
			return array("Y" => "Ya", "T" => "Tidak");
		}
		
		function getVerifikasi(){
			return array("Y" => "Verifikasi");
		}
		
		function getStatusClose(){
			return array("Y" => "Ditutup");
		}
		
		function statusPersetujuan(){
			return array("A" => "Diajukan", "S" => "Disetujui", "T" => "Ditolak");
		}
		
		function aRomawiSurat(){
			$a_romawi = array();
			$a_romawi["1"] = "I";
			$a_romawi["2"] = "II";
			$a_romawi["3"] = "III";
			$a_romawi["4"] = "IV";
			$a_romawi["5"] = "V";
			$a_romawi["6"] = "VI";
			$a_romawi["7"] = "VII";
			$a_romawi["8"] = "VIII";
			$a_romawi["9"] = "IX";
			$a_romawi["10"] = "X";
			$a_romawi["11"] = "XI";
			$a_romawi["12"] = "XII";
			
			return $a_romawi;
		}
	}
?>
