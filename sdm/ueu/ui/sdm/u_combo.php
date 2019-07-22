<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('combo'));
	
	class uCombo {
	
		function fakultas($conn,&$valvar,$nameid='fakultas',$add='',$empty=true) {
			$data = mCombo::fakultas($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		
		function jurusan($conn,&$valvar,$fakultas='',$nameid='jurusan',$add='',$empty=true) {
			$data = mCombo::jurusan($conn,$fakultas);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jurusan');
		}
		
		function programPendidikan($conn,&$valvar,$nameid='progpend',$add='',$empty=true) {
			$data = mCombo::programPendidikan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Program');
		}
		
		function semester(&$valvar,$singkat=false,$nameid='semester',$add='',$empty=true) {
			$data = mCombo::semester($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Semester');
		}
		
		function propinsi($conn,&$valvar,$nameid='propinsi',$add='',$empty=true) {
			$data = mCombo::propinsi($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Propinsi');
		}
		
		function kabupaten($conn,&$valvar,$nameid='kabupaten',$add='',$empty=true,$where='') {
			$data = mCombo::kabupaten($conn,$where);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kabupaten');
		}
		
		function kecamatan($conn,&$valvar,$nameid='kecamatan',$add='',$empty=true,$where='') {
			$data = mCombo::kecamatan($conn,$where);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kecamatan');
		}
				
		function tipepegawai($conn,&$valvar,$nameid='idtipepeg',$add='',$empty=true) {
			$data = mCombo::tipepegawai($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tipe Pegawai');
		}
		
		function tahun(&$valvar,$singkat=true,$nameid='tahun',$add='',$empty=true) {
			$data = mCombo::tahun($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tahun');
		}
		
		function bulan(&$valvar,$singkat=true,$nameid='tahun',$add='',$empty=true) {
			$data = Date::arrayMonth($singkat);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tahun');
		}
		
		function unit($conn,&$valvar,$nameid='unit',$add='',$empty=true,$akademik=false) {
			$add .='style="width:350px;"';
			$data = mCombo::unit($conn,true,$akademik);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		
		function unitSave($conn,&$valvar,$nameid='unit',$add='',$empty=true,$akademik=false) {
			$add='style="width:350px;"';
			$data = mCombo::unitSave($conn,true,$akademik);
			
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
		function listColumn($kolom,$add='',$post='') {
			$data = array();
			foreach($kolom as $datakolom) {
				if(!empty($datakolom['kolom']))
					if (!empty($datakolom['filter']))
						$datakolom['kolom'] = $datakolom['filter'];
					$data[$datakolom['kolom']] = $datakolom['label'];
			}
			
			return UI::createSelect('cfilter',$data,$post,'ControlStyle',true,$add);
		}
		
		// combo jumlah baris
		function listRowNum($value='',$add='') {
			$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','50'=>'50','100'=>'100','-1'=>'All');
			
			return UI::createSelect('row',$data,$value,'ControlStyle',true,$add);
		}
	
		function sanksi($conn,&$valvar,$nameid='jenissanksi',$add='',$empty=true) {
			$data = mCombo::sanksi($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Sanksi');
		}

		///
		function tipepegawaibaru($conn,&$valvar,$nameid='idtipepeg',$add='',$empty=true) {
			$data = mCombo::tipepegawaibaru($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tipe Pegawai');
		}

		function kelompokpeg($conn,&$valvar,$nameid='idkelompok',$add='',$empty=true) {
			$data = mCombo::kelompokpeg($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kelompok Pegawai');
		}

		function jenispegawaibaru($conn,&$valvar,$nameid='idjenispegawai',$add='',$empty=true) {
			$data = mCombo::jenispegawaibaru($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Pegawai');
		}
	}
?>
