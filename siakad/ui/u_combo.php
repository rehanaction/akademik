<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('combo'));
	
	class uCombo {
		function angkatan($conn,&$valvar,$nameid='angkatan',$add='',$empty=true) {
			// $data = mCombo::angkatan($conn);
			$data = mCombo::tahun();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Angkatan');
		}
		//negara
		function negara($conn,&$valvar,$nameid='negara',$add='',$empty=true) {
			$data = mCombo::negara($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'negara');
		}
		//jenis dalam atau luar negeri
		function jenisuniversitas($conn,&$valvar,$nameid='jenisuniversitas',$add='',$empty=true) {
			$data = mCombo::statusuniversitas($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Universitas');
		}		
		
		function beasiswa($conn,&$valvar,$nameid='beasiswa',$add='',$empty=true) {
			$data = mCombo::beasiswa($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'beasiswa');
		}
		function namabeasiswa($conn,&$valvar,$nameid='namabeasiswa',$add='',$empty=true) {
			$data = mCombo::namabeasiswa($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Nama Beasiswa');
		}
		
		function bidangStudi($conn,&$valvar,$kodeunit,$nameid='bidangstudi',$add='',$empty=true) {
			$data = mCombo::bidangStudi($conn,$kodeunit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Bidang Studi');
		}
		
		function dosen($conn,&$valvar,$unit='',$nameid='nip',$add='',$empty=true) {
			$data = mCombo::dosen($conn,$unit);
			if(empty($add))
				$add = 'style="width:300px"';
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Dosen');
		}
		
		function fakultas($conn,&$valvar,$nameid='fakultas',$add='',$empty=true) {
			$data = mCombo::fakultas($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Fakultas');
		}
		
		function hari(&$valvar,$full=true,$nameid='nohari',$add='',$empty=true) {
			$data = mCombo::hari($full);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Hari');
		}
		
		function jalurPenerimaan($conn,&$valvar,$nameid='jalurpenerimaan',$add='',$empty=true) {
			$data = mCombo::jalurPenerimaan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function jenisTarif($conn,&$valvar,$nameid='jenis',$add='',$empty=true) {
			$data = mCombo::jenisTarif($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Tarif');
		}
		
		function jurusan($conn,&$valvar,$fakultas='',$nameid='jurusan',$add='',$empty=true,$skippamu=false) {
			$data = mCombo::jurusan($conn,$fakultas,$skippamu);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jurusan');
		}
		
		function jurusan_yudisium($conn,&$valvar,$fakultas='',$nameid='jurusan',$add='',$empty=true) {
			$data = mCombo::unit($conn,false);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jurusan');
		}
		
		function kota($conn,&$valvar,$propinsi='',$nameid='kota',$add='',$empty=true) {
			$data = mCombo::kota($conn,$propinsi);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kota');
		}
		
		function kurikulum($conn,&$valvar,$nameid='kurikulum',$add='',$empty=true) {
			$data = mCombo::kurikulum($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kurikulum');
		}
		function matkul($conn,&$valvar,$kurikulum='', $unit='',$nameid='matkul',$add='',$empty=true) {
			$data = mCombo::matkul($conn,$kurikulum, $unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Mata kuliah');
		}
		
		
		function mahasiswa($conn,&$valvar,$unit='',$periode='',$nameid='npm',$add='',$empty=true) {
			$data = mCombo::mahasiswa($conn,$unit,$periode);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Mahasiswa');
		}
		
		function nAngkaKurikulum($conn,&$valvar,$kurikulum,$nameid='nangka',$add='',$empty=true) {
			$data = mCombo::nAngkaKurikulum($conn,$kurikulum);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Nilai');
		}
		
		function noSemester(&$valvar,$singkat=false,$nameid='nosemester',$add='',$empty=true) {
			$data = mCombo::noSemester($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Semester');
		}
		
		function periodeDaftar($conn,&$valvar,$nameid='periode',$add='',$empty=true) {
			$data = mCombo::periodeDaftar($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Periode Daftar');
		}
		
		function periodeWisuda($conn,&$valvar,$nameid='periodewisuda',$add='',$empty=true) {
			$data = mCombo::periodeWisuda($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Periode Wisuda');
		}
		
		function programPendidikan($conn,&$valvar,$nameid='progpend',$add='',$empty=true) {
			$data = mCombo::programPendidikan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Program');
		}
		
		function propinsi($conn,&$valvar,$nameid='propinsi',$add='',$empty=true) {
			$data = mCombo::propinsi($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Propinsi');
		}

		
		function ruang($conn,&$valvar,$nameid='ruang',$add='',$empty=true) {
			$data = mCombo::ruang($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Ruang');
		}
		
		function semester(&$valvar,$singkat=false,$nameid='semester',$add='',$empty=true) {
			$data = mCombo::semester($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Semester');
		}
		
		function statusMhs($conn,&$valvar,$nameid='statusmhs',$add='',$empty=true) {
			$data = mCombo::statusMhs($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Status');
		}

		//pilihan bulan
		function bulan(&$valvar,$nameid='bulan',$add='',$empty=true) {
			$data = mCombo::bulan();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Bulan');
		}		
		
		function tahun(&$valvar,$singkat=true,$nameid='tahun',$add='',$empty=true) {
			$data = mCombo::tahun($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tahun');
		}
		
		function tahun_angkatan(&$valvar,$singkat=true,$nameid='tahun',$add='',$empty=true) {
			$data = mCombo::tahun_angkatan($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tahun');
		}
		
		
		
		function unit($conn,&$valvar,$nameid='unit',$add='',$empty=true,$skippamu=false) {
			$data = mCombo::unit($conn,true,$skippamu);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		
		// combo format
		function format() {
			$data = array('html' => 'HTML', 'doc' => 'DOC', 'xls' => 'EXCEL', 'pdf' => 'PDF');
			
			return UI::createSelect('format',$data,'','ControlStyle');
		}
		
		// combo umum
		function combo($data,&$valvar,$nameid='unit',$add='',$empty=true,$label='') {
			if(!$empty and empty($data[$valvar]))
				$valvar = key($data);
			
			return UI::createSelect($nameid,$data,$valvar,'ControlStyle',true,$add,$empty,'-- Pilih '.$label.' --');
		}
		
		// combo daftar kolom
		function listColumn($kolom,$add='',$addcolfilter=array()) {
			$data = array();
			foreach($kolom as $datakolom) {
				if(!empty($datakolom['kolom']))
					$data[$datakolom['kolom']] = $datakolom['label'];
			}
			if(!empty($addcolfilter))
				foreach($addcolfilter as $kolom)
					$data[$kolom] = $kolom;
					
			return UI::createSelect('cfilter',$data,'','ControlStyle',true,$add);
		}
		
		// combo jumlah baris
		function listRowNum($value='',$add='') {
			$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','-1'=>'Semua');
			
			return UI::createSelect('row',$data,$value,'ControlStyle',true,$add);
		}
		
		function role($conn,&$valvar,$nameid='role',$add='',$empty=true) {
			$data = mCombo::role($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Role');
		}
		function jenisQuiz($conn,&$valvar,$nameid='idjenissoal',$add='',$empty=true) {
			$data = mCombo::jenisQuiz($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'idjenissoal');
		}
		function listKelasMk($conn,$periode,$kurikulum,$kodeunit,&$valvar,$nameid='kelasmk',$add='',$empty=true){
			$data = mCombo::listKelasmk($conn,$periode,$kurikulum,$kodeunit);
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kelas MK');
		}
		function kelas($conn,&$valvar,$nameid='sistemkuliah',$add='',$empty=true) {
			require_once(Route::getModelPath('mahasiswa'));
			$data = mMahasiswa::sistemKuliah($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kelas');
		}
		function sistemkuliah($conn,&$valvar,$nameid='sistemkuliah',$add='',$empty=true) {
			require_once(Route::getModelPath('mahasiswa'));
			$data = mMahasiswa::sistemKuliah($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Basis');
		}
		function jenisujian($conn,&$valvar,$nameid='jenisujian',$add='',$empty=true) {
			require_once(Route::getModelPath('combo'));
			$data = mCombo::getJenisUjian($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Ujian');
		}
		function periodeGaji($conn_sdm,&$valvar,$nameid='periodegaji',$add='',$empty=true) {
			require_once(Route::getModelPath('pegawai'));
			$data = mPegawai::periodeGaji($conn_sdm);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Periode Gaji');
		}
		function nopengajuan($conn,$model,&$valvar,$nameid='nopengajuan',$add='',$empty=true,$periode,$unit,$periodegaji) {
			
			$data = $model::listNopengajuan($conn,$periode,$unit,$periodegaji);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Nomor Pengajuan');
		}
		function nopembayaran($conn,&$valvar,$nameid='nopembayaran',$add='',$empty=true,$periode,$unit,$periodegaji) {
			require_once(Route::getModelPath('honordosen'));
			$data = mHonorDOsen::listNoPembayaran($conn,$periode,$unit,$periodegaji);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Nomor Pembayaran');
		}
		function semmk(&$valvar,$singkat=false,$nameid='semmk',$add='',$empty=true) {
			$data = mCombo::semmk($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Semester MK');
		}
		
		// combo jumlah baris
		function listStatusTransfer($nameid='mhstransfer',$value='',$add='') {
			$data = array('2'=>'Transfer Dari UEU','-1'=>'Transfer Luar UEU');
			
			return UI::createSelect($nameid,$data,$value,'ControlStyle',true,$add);
		}
		//combo jenis data elearning
		function JenisData() {
			$data = array('modul' => 'MODUL', 'video' => 'VIDEO', 'tugas' => 'TUGAS', 'quiz' => 'QUIZ');
			
			return UI::createSelect('jenis',$data,'','ControlStyle');
		}
		function JenisKuliah() {
			$data = array(1=>'-Semua-',-1 => 'Online', 0 => 'Tatap Mukap');
			
			return UI::createSelect('isonline',$data,'','ControlStyle');
		}
	}
?>
