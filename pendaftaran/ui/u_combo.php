<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('combo'));
	
	class uCombo {
		function ruang($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::ruang($conn,$unit);
						
			return self::combo($data,$valvar,$nameid,$add,$empty,'Ruang');
		}
		function periode($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::periode($conn,$unit);
						
			return self::combo($data,$valvar,$nameid,$add,$empty,'Periode');
		}
		function jalur($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalur($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		function jalurpenerimaan($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurpenerimaan($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		function gelombang($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::gelombang($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Gelombang');
		}
		function lulus($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = array('l'=> 'Lulus', 't'=>'Tidak Lulus');
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis');
		}
		function jenis($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = array('mhs'=> 'Mahasiswa', 'pdf'=>'Pendaftar');
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis');
		}
		// combo format
		function format() {
			$data = array('html' => 'HTML', 'doc' => 'DOC', 'xls' => 'EXCEL');
			
			return UI::createSelect('format',$data,'','ControlStyle');
		}
		
		// combo umum
		function combo($data,&$valvar,$nameid='unit',$add='',$empty=true,$label='') {
			if(!$empty and empty($data[$valvar]))
				$valvar = key($data);
			
			return UI::createSelect($nameid,$data,$valvar,'ControlStyle',true,$add,$empty,'-- Pilih '.$label.' --');
		}
		
		// combo daftar kolom
		function listColumn($kolom,$add='') {
			$data = array();
			foreach($kolom as $datakolom) {
				if(!empty($datakolom['kolom']) and empty($datakolom['nosearch']))
					$data[$datakolom['kolom']] = $datakolom['label'];
			}
			
			return UI::createSelect('cfilter',$data,'','ControlStyle',true,$add);
		}
		
		// combo jumlah baris
		function listRowNum($value='',$add='') {
			$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30');
			
			return UI::createSelect('row',$data,$value,'ControlStyle',true,$add);
		}
		
		function getProdi($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::getProdi($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function getOnedayservice($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::getOneDayService($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'onedayservice');
		}
		
		function jalurTPA($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurTPA($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function jalurWawancara($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurWawancara($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function jalurNilaiRaport($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurNilaiRaport($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function jalurTesKesehatan($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurTesKesehatan($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function jalurMapel($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurMapel($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		
		function jalurKompetensi($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jalurKompetensi($conn,$unit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur');
		}
		function getMonth($conn,&$valvar,$unit='',$nameid='',$add='',$empty=false) {
			$data = mCombo::getMonth($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'bulan');
		}
		function tahun($conn,&$valvar,$unit='',$nameid='',$add='',$empty=false) {
			$data = mCombo::tahun(true,2000);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'tahun');
		}
		function jurusan($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::jurusan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'jurusan');
		}
		function fakultas($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::fakultas($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'fakultas');
		}
		function fakJur($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::fakJur($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}

		function unit($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::unit($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		function unitakademik($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::unitakademik($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		
		function sistemKuliah($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true) {
			$data = mCombo::sistemKuliah($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'sistem kuliah');
		}
		function getTahapUjian($conn,&$valvar,$unit='',$nameid='',$add='',$empty=false) {
			$data = mCombo::getTahapUjian();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'tahap ujian');
		}
		function getTahapSeleksi($conn,&$valvar,$unit='',$nameid='',$add='',$empty=false) {
			$data = mCombo::getTahapSeleksi();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'tahap ujian');
		}
		function getWeek($conn,&$valvar,$unit='',$nameid='',$add='',$empty=false) {
			$data = mCombo::getWeek($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'minggu');
		}
		function propinsi($conn,&$valvar,$unit='',$nameid='',$add='',$empty=false) {
			$data = mCombo::propinsi($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Propinsi');
		}
		function kota($conn,&$valvar,$unit='',$nameid='',$add='',$empty=true,$idprop) {
			$data = mCombo::getKotaFilter($idprop);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kota');
		}
		function daftarulang($conn,&$valvar,$nameid='',$add='',$empty=true) {
			$data = array('-1'=> 'Sudah Daftar Ulang', '0'=>'Belum Daftar Ulang');
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Semua');
		}
		function lulusujian($conn,&$valvar,$nameid='',$add='',$empty=true) {
			$data = array('-1'=> 'Sudah Lulus Ujian', '0'=>'Belum/Tidak Lulus Ujian');
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Semua');
		}
	}
?>
