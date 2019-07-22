<?php
	// fungsi pembantu modul akademik
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Akademik {
		// apakah role mahasiswa
		function isMhs() {
			$role = Modul::getRole();
			
			if($role == 'mahasiswa')
				return true;
			else
				return false;
		}
		
		// apakah role dosen
		function isDosen() {
			$role = Modul::getRole();
			
			if($role == 'dosen')
				return true;
			else
				return false;
		}
		
		// apakah perlu cek unit
		function cekUnit() {
			if(self::isMhs() or self::isDosen())
				return false;
			else
				return true;
		}
		
		// mendapatkan data session
		function getIsiBiodataMhs() {
			return $_SESSION[SITE_ID]['AKADEMIK']['BIODATAMHS'];
		}
		
		function getIsiNilai() {
			return $_SESSION[SITE_ID]['AKADEMIK']['ISINILAI'];
		}
		
		function getKurikulum() {
			return $_SESSION[SITE_ID]['AKADEMIK']['KURIKULUM'];
		}
		
		function getPeriode() {
			return $_SESSION[SITE_ID]['AKADEMIK']['PERIODE'];
		}
		
		function getPeriodeNilai() {
			return $_SESSION[SITE_ID]['AKADEMIK']['PERIODENILAI'];
		}
		
		function getTahap() {
			return $_SESSION[SITE_ID]['AKADEMIK']['TAHAP'];
		}
		
		// mengambil setting global
		function setGlobal($conn) {
			// ambil model setting global
			require_once(Route::getModelPath('setting'));
			
			$_SESSION[SITE_ID]['AKADEMIK'] = mSetting::getDataSession($conn);
		}
		
		// mengambil data semester
		function semester($singkat=false) {
			if($singkat)
				$data = array('1' => 'Gasal', '2' => 'Genap', '3' => 'Pendek');
			else
				$data = array('1' => 'Semester Gasal', '2' => 'Semester Genap', '3' => 'Semester Pendek');
			
			return $data;
		}
		
		// shortcut :D
		function getNamaPeriode($periode='',$singkat=false) {
			if(empty($periode))
				$periode = self::getPeriode();
			$a_semester = self::semester($singkat);
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return $a_semester[$t_semester].' '.(int)$t_tahun.' - '.($t_tahun+1);
		}
		
		function getNamaPeriodeShort($periode='') {
			if(empty($periode))
				$periode = self::getPeriode();
			$a_semester = self::semester(true);
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return $a_semester[$t_semester].' '.substr($t_tahun,-2).'/'.substr($t_tahun+1,-2);
		}
		
		function getNamaPeriodeLong($periode='') {
			if(empty($periode))
				$periode = self::getPeriode();
			$a_semester = self::semester();
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return $a_semester[$t_semester].' TAHUN AKADEMIK '.$t_tahun.'/'.($t_tahun+1);
		}
		
		function getNamaMahasiswa($conn,$npm) {
			// ambil model mahasiswa
			require_once(Route::getModelPath('mahasiswa'));
			
			return mMahasiswa::getNama($conn,$npm);
		}
		
		function getNamaPegawai($conn,$nip) {
			// ambil model pegawai
			require_once(Route::getModelPath('pegawai'));
			
			return mPegawai::getNamaPegawai($conn,$nip);
		}
		
		function getNamaUnit($conn,$kodeunit) {
			// ambil model unit
			require_once(Route::getModelPath('unit'));
			
			return mUnit::getNamaUnit($conn,$kodeunit);
		}
		
		function getNamaParentUnit($conn,$kodeunit) {
			// ambil model unit
			require_once(Route::getModelPath('unit'));
			
			return mUnit::getNamaParentUnit($conn,$kodeunit);
		}
		
		function getAngkatanMahasiswa($conn,$npm) {
			// ambil model mahasiswa
			require_once(Route::getModelPath('mahasiswa'));
			
			return mMahasiswa::getAngkatan($conn,$npm);
		}
	}
?>