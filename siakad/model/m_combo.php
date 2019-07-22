<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
		function akreditasi($conn,$dot=true) {
			require_once(Route::getModelPath('akreditasi'));
			
			return mAkreditasi::getArray($conn,$dot);
		}
		
		function angkatan($conn) {
			require_once(Route::getModelPath('mahasiswa'));
			
			return mMahasiswa::angkatan($conn);
		}
		
		function beasiswa($conn) {
			require_once(Route::getModelPath('beasiswa'));
			
			return mSumberBeasiswa::getArrayNama($conn);
		}
		
		function bidangIlmu($conn) {
			require_once(Route::getModelPath('bidangilmu'));
			
			return mBidangilmu::getArray($conn);
		}
		
		function bidangStudi($conn,$kodeunit) {
			require_once(Route::getModelPath('bidangstudi'));
			
			return mBidangStudi::getArray($conn);
		}
		
		function bulan() {
			return Date::arrayMonth();
		}
		
		function dosen($conn,$unit='') {
			require_once(Route::getModelPath('pegawai'));
			
			return mPegawai::getArray($conn,$unit);
		}
		
		function dosenLengkap($conn,$unit='') {
			require_once(Route::getModelPath('pegawai'));
			
			return mPegawai::getArrayLengkap($conn,$unit);
		}
		
		function fakultas($conn) {
			require_once(Route::getModelPath('unit'));
			
			return mUnit::fakultas($conn);
		}
		function fakjur($conn) {
			require_once(Route::getModelPath('unit'));
			
			return mUnit::fakultas($conn);
		}
		function frekkurikulum($conn,$dot=true) {
			require_once(Route::getModelPath('frekuensikurikulum'));
			
			return mFrekuensikurikulum::getArray($conn,$dot);
		}
		
		function hari($full=true) {
			return Date::arrayDay($full);
		}
		
		function jalurPenerimaan($conn) {
			require_once(Route::getModelPath('jalurpenerimaan'));
			
			return mJalurPenerimaan::getArray($conn,$unit);
		}
		
		function jenisTarif($conn) {
			require_once(Route::getModelPath('tarif'));
			
			return mTarif::jenisTarif($conn);
		}
		
		function jurusan($conn,$fakultas='',$skippamu=false) {
			require_once(Route::getModelPath('unit'));
			
			return mUnit::jurusan($conn,$fakultas,$skippamu);
		}
		
		function kegiatan() {			
			$data = array('P' => 'Seminar Proposal', 'S' => 'Sidang Akhir');
			
			return $data; 
		}
		
		function kerjasama($conn,$dot=true) {
			require_once(Route::getModelPath('universitas'));
			
			return mUniversitas::getArray($conn,$dot);
		}
		
		function kota($conn,$propinsi='') {
			require_once(Route::getModelPath('kota'));
			
			return mKota::getArray($conn,$propinsi);
		}
		
		function kurikulum($conn) {
			require_once(Route::getModelPath('thnkurikulum'));
			
			return mThnkurikulum::getArray($conn);
		}
		function matkul($conn, $kurikulum='', $unit='') {
			require_once(Route::getModelPath('kurikulum'));
			
			return mKurikulum::getArray($conn, $kurikulum, $unit);
		}
		
		
		function mahasiswa($conn,$unit='',$periode='') {
			require_once(Route::getModelPath('mahasiswa'));
			
			return mMahasiswa::getArray($conn,$unit,$periode);
		}
		
		function namabeasiswa($conn) {
			require_once(Route::getModelPath('beasiswa'));
			
			return mBeasiswa::getArrayNama($conn);
		}
		
		function nAngkaKurikulum($conn,$kurikulum) {
			require_once(Route::getModelPath('skalanilai'));
			
			return mSkalaNilai::getDataKurikulum($conn,$kurikulum);
		}
		
		function negara($conn) {
			require_once(Route::getModelPath('negara'));
			
			return mNegara::getArray($conn);
		}
		
		function noSemester($singkat=false) {
			$data = array();
			for($i=1;$i<=8;$i++)
				$data[$i] = ($singkat ? '' : 'Semester ').$i;
			
			return $data;
		}
		
		function pelkurikulum($conn,$dot=true) {
			require_once(Route::getModelPath('pelaksanaankurikulum'));
			
			return mPelaksanaankurikulum::getArray($conn,$dot);
		}
		
		function periode($conn,$singkat=true) {
			require_once(Route::getModelPath('periode'));
			
			return mPeriode::getArray($conn,$singkat);
		}
		
		function periodeDaftar($conn) {
			$sql = "select periodedaftar from pendaftaran.ms_periodedaftar order by periodedaftar desc";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function periodeWisuda($conn) {
			require_once(Route::getModelPath('periodeyudisium'));
			
			return mPeriodeYudisium::getArray($conn);
		}
		
		function programPendidikan($conn) {
			require_once(Route::getModelPath('progpend'));
			
			return mProgrampendidikan::getArray($conn);
		}
		
		function propinsi($conn) {
			require_once(Route::getModelPath('propinsi'));
			
			return mPropinsi::getArray($conn);
		}
		
		function ruang($conn) {
			require_once(Route::getModelPath('ruang'));
			
			return mRuang::getArray($conn);
		}
		
		function semester($singkat=false) {
			return Akademik::semester($singkat);
		}
		
		function statusMhs($conn) {
			require_once(Route::getModelPath('statusmhs'));
			
			return mStatusMhs::getArray($conn);
		}
		function sistemKuliah($conn,$short=false) {
			require_once(Route::getModelPath('sistemkuliah'));
			
			return mSistemkuliah::getArray($conn,$short);
		}
		function statusMasukMhs() {			
			$data = array('0' => 'Mahasiswa Baru', '-1' => 'Mahasiswa Transfer');
			
			return $data; 
		}
		function batalNim() {			
			$data = array('0' => 'Tidak', '-1' => 'Ya');
			
			return $data; 
		}
		function statusuniversitas() {			
			$data = array('0' => 'PT Dalam Negeri', '-1' => 'PT Luar Negeri');
			
			return $data; 
		}
		
		function tahun($singkat=true,$min=1996) {
			global $conn;
			require_once(Route::getModelPath('periode'));
			//$max_periode=mPeriode::getMaxPeriode($conn);
			$max_periode=(int)date('Y')+5;
			$data = array();
			for($i=$max_periode;$i>=$min;$i--)
				$data[$i] = ($singkat ? $i : $i.' - '.($i+1));
			
			return $data;
		}
		
		function tahun_angkatan($singkat=true,$min=1996) {
			$data = array();
			$data['*'] = '--Semua--';
			for($i=date('Y')+1;$i>=$min;$i--)
				$data[$i] = ($singkat ? $i : $i.' - '.($i+1));
						
			return $data;
		}
		
		function unit($conn,$dot=true,$skippamu=false,$cekauth=true) {
			require_once(Route::getModelPath('unit'));
			
			return mUnit::getArray($conn,$dot,$skippamu,$cekauth);
		}
		
		function unitTree($conn,$skippamu=false) {
			require_once(Route::getModelPath('unit'));
			
			return mUnit::getArrayTree($conn,$skippamu);
		}
		
		function universitas($conn) {
			require_once(Route::getModelPath('universitas'));
			
			return mUniversitas::getArray($conn);
		}
		
		function role($conn) {
			$sql = "select koderole, namarole from gate.sc_role order by koderole";
			
			return Query::arrQuery($conn,$sql);
		}

		function koderoles($conn) {
			require_once(Route::getModelPath('koderoles'));
			
			return mKoderoles::getArrayCombo($conn);
		}

		function jenisQuiz($conn) {
			
			require_once(Route::getModelPath('jenisquiz'));
			
			return mJenisQuiz::getArrayCombo($conn);
		}

		function tipePegawai($conn) {
			
			require_once(Route::getModelPath('tipepeg'));
			
			return mTipepeg::getArrayCombo($conn);
		}

		function kelompokKelas($conn){
			require_once(Route::getModelPath('kelas'));
			
			return mKelas::getMaxKelompok($conn);
		}
		function listKelasmk($conn,$periode,$kurikulum,$kodeunit){
			require_once(Route::getModelPath('kelas'));
			
			return mKelas::listKelasmk($conn,$periode,$kurikulum,$kodeunit);
		}
		function agama(){
			return array('0'=>'Tidak','1'=>'MK Agama');
		}
		function aktif(){
			return array('1'=>'Aktif','0'=>'Tidak Aktif');
		}

		function istoefl(){
			return array('1'=>'Toefl 1','2'=>'Toefl 2','0'=>'Bukan MK Toefl');
		}
		function listJumpingClass($conn) {
			require_once(Route::getModelPath('jumpingclass'));
			
			return mJumpingClass::getArray($conn);
		}
		function periodebulan($singkat=true,$isint=true) {
			$data = array();
			$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember');
			for($i=1;$i<13;$i++)
				$data[$i] = $bulan[$i-1];
			
			if(!$isint) {
				$cek = $data;
				$data = array();
				foreach($cek as $k => $v)
					$data[str_pad($k,2,'0',STR_PAD_LEFT)] = $v;
			}
			
			return $data;
		}
		function semmk($singkat=false) {
			$data = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10');
			
			return $data; 
		}
		function getJenisUjian(){
			return array('T'=>'UTS','A'=>'UAS','R'=>'UTS Susulan','S'=>'UAS Susulan');
		}
	}
?>
