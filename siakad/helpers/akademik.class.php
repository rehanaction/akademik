<?php
	// fungsi pembantu modul akademik
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Akademik {
		function getProsAbsen($conn){
			return $_SESSION[SITE_ID]['AKADEMIK']['PROSENTASEABSENSI'];
		}
	
		// apakah role mahasiswa
		function isMhs() {
			$role = Modul::getRole();
			
			if($role == 'M')
				return true;
			else
				return false;
		}

		function base64url_encode($data) { 
			return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
		} 
		function base64url_decode($data) { 
			return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
		  } 

		function isNilai() {
			$role = Modul::getRole();
			
			if($role == 'ADMNilai')
				return true;
			else
				return false;
		}
		
		function isBAA() {
			$role = Modul::getRole();
			
			if($role == 'ABA')
				return true;
			else
				return false;
		}


		function isKepegawaian() {
			$role = Modul::getRole();
			
			if($role == 'AP')
				return true;
			else
				return false;
		}

		function isKemahasiswaan() {
			$role = Modul::getRole();
			
			if($role == 'ADKMHS')
				return true;
			else
				return false;
		}
		function isMM() {
			$role = Modul::getRole();
			
			if($role == 'MM')
				return true;
			else
				return false;
		}

		function isWk2() {
			$role = Modul::getRole();
			
			if($role == 'WK2')
				return true;
			else
				return false;
		}

		function isMagister() {
			$role = Modul::getRole();
			
			if($role == 'MM')
				return true;
			else
				return false;
		}
		function getPeriodeSebelumnya($periode='') {
			if(empty($periode))
				$periode = self::getPeriode();
			
			$tahun = substr($periode,0,4);
			$smt = substr($periode,-1);
			
			if($smt == '1') {
				$tahun--;
				$smt = '2';
			}
			else
				$smt = '1';
			
			return $tahun.$smt;
		}

		function isKeuangan() {
			$role = Modul::getRole();
			
			if($role == 'AdmDKS')
				return true;
			else
				return false;
		}
		
		// apakah role dosen
		function isDosen() {
			$role = Modul::getRole();
			
			if($role == 'D')
				return true;
			else
				return false;
		}
		function isKaProdi() {
			$role = Modul::getRole();
			
			if($role == 'KPD')
				return true;
			else
				return false;
		}
		function isPerwalianProdi() {
			$role = Modul::getRole();
			
			if($role == 'PAProdi')
				return true;
			else
				return false;
		}
		function isAdminDAA() {
			$role = Modul::getRole();
			
			if($role == 'AdmDAA')
				return true;
			else
				return false;
		}
		function isPPA() {
			$role = Modul::getRole();
			
			if($role == 'PPA')
				return true;
			else
				return false;
		}


		// apakah petugas PMB
		function isHumas(){
			$role = Modul::getRole();
			if($role=='PMB')
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
		//apakah role admin 
		function isAdmin() {
			$role = Modul::getRole();
			
			if($role == 'A')
				return true;
			else
				return false;
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
		function getPeriodeSpa() {
			return $_SESSION[SITE_ID]['AKADEMIK']['PERIODESPA'];
		}
		function getPeriodeNilai() {
			return $_SESSION[SITE_ID]['AKADEMIK']['PERIODENILAI'];
		}
		function getPeriodeNilaiSpa() {
			return $_SESSION[SITE_ID]['AKADEMIK']['PERIODENILAISPA'];
		}
		function getTahap() {
			return $_SESSION[SITE_ID]['AKADEMIK']['TAHAP'];
		}
		
		function getDefaultskalanilai() {
			return $_SESSION[SITE_ID]['AKADEMIK']['ISDEFAULTSKALANILAI'];
		}
		function detIp() {
			return $_SESSION[SITE_ID]['AKADEMIK']['DETIP'];
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
				$data = array('0' => 'Pendek Awal','1' => 'Gasal', '2' => 'Genap', '3' => 'Pendek');
			else
				$data = array('0' => 'Semester Pendek Awal','1' => 'Semester Gasal', '2' => 'Semester Genap', '3' => 'Semester Pendek');
			
			return $data;
		}
		function namaSemesterEsa($smt,$singkat=false) {
			if($singkat){
				$data = array('10' => 'SPA',
							'11' => 'Satu', 
							'12' => 'Dua', 
							'13' => 'SP 1',
							'21' => 'Tiga', 
							'22' => 'Empat', 
							'23' => 'SP 2', 
							'31' => 'Lima',
							'32' => 'Enam',
							'33' => 'Semester 3',
							'41' => 'Tujuh',
							'42' => 'Delapan',
							'43' => 'SP 4',
							'55' => 'Semester 5',
							'66' => 'Semester 6',
							'77' => 'Semester 7',
							'88' => 'Semester 8');
			}else{
				$data = array('10' => 'SPA',
							'11' => 'Semester Satu', 
							'12' => 'Semester Dua', 
							'13' => 'SP 1',
							'21' => 'Semester Tiga', 
							'22' => 'Semester 2', 
							'23' => 'SP 2', 
							'31' => 'Semester Lima',
							'32' => 'Semester Enam',
							'33' => 'Semester 3',
							'41' => 'Semester Tujuh',
							'42' => 'Semester Delapan',
							'43' => 'SP 4',
							'44' => 'Semester 4',
							'55' => 'Semester 5',
							'66' => 'Semester 6',
							'77' => 'Semester 7',
							'88' => 'Semester 8');
			}
			return $data[$smt];
		}

		function namaSemester($smt,$singkat=false) {
			if($singkat){
				$data = array('10' => 'SPA',
							'11' => 'Satu', 
							'12' => 'Dua', 
							'13' => 'SP 1',
							'21' => 'Tiga', 
							'22' => 'Empat', 
							'23' => 'SP 2', 
							'31' => 'Lima',
							'32' => 'Enam',
							'33' => 'Semester 3',
							'41' => 'Tujuh',
							'42' => 'Delapan',
							'43' => 'SP 4');
			}else{
				$data = array('10' => 'SPA',
							'11' => 'Semester Satu', 
							'12' => 'Semester Dua', 
							'13' => 'SP 1',
							'21' => 'Semester Tiga', 
							'22' => 'Semester Empat', 
							'23' => 'SP 2', 
							'31' => 'Semester Lima',
							'32' => 'Semester Enam',
							'33' => 'Semester 3',
							'41' => 'Semester Tujuh',
							'42' => 'Semester Delapan',
							'43' => 'SP 4');
			}
			return $data[$smt];
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
		// shortcut :D
		function getNamaPeriodeAbsen($periode='',$singkat=true) {
			if(empty($periode))
				$periode = self::getPeriode();
			$a_semester = self::semester($singkat);
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return (int)$t_tahun.' / '.($t_tahun+1).' '.$a_semester[$t_semester];
		}
		//periode tanpa semester, rada aneh se, hehehe
		function getNamaPeriodeTh($periode='',$singkat=false) {
			if(empty($periode))
				$periode = self::getPeriode();
			$a_semester = self::semester($singkat);
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return 'Semester Gasal / Genap '.(int)$t_tahun.' - '.($t_tahun+1);
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
		function getPrevPeriode($periode){
			$th=substr($periode,0,4);
			$smt=substr($periode,4,1);
			if($smt==1)
				$r_periode=($th-1).'2';
			else 
				$r_periode=$periode-1;
			return $r_periode;
		}
		function getSemMhs($angkatan,$periode){
			if($angkatan==$periode){
				$sem=1;
			}else{
				$th_angkatan=substr($angkatan,0,4);
				$smt_angkatan=substr($angkatan,4,1);
				//$smt_angkatan=1;
				$th_periode=substr($periode,0,4);
				$smt_periode=substr($periode,4,1);
				$smt1=($th_periode-$th_angkatan)*2;
				if($smt_periode>2)
					$smt_periode=2;
				$smt2=($smt_periode-$smt_angkatan)+1;
				$sem=$smt1+$smt2;
			}
			return $sem;
		}
		
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		function convertPeriodeBayar($nopengajuan){
			$arr=explode('/',$nopengajuan);
			$tahun=$arr[2];
			$bulan=$arr[1];
			
			return Date::indoMonth($bulan).' '.$tahun;
		}
	}
?>
