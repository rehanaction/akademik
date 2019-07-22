<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('combo'));
	
	class uCombo {
		// combo unit
		function unit($conn,&$valvar,$nameid='unit',$add='',$empty=true,$edit=true) {
			$data = mCombo::unit($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit',$edit);
		}
		
		// combo jalur
		function jalur($conn,&$valvar,$nameid='jalur',$add='',$empty=true,$edit=true) {
			$data = mCombo::jalur($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jalur Pendaftaran',$edit);
		}
		// combo gelombang
		function gelombang($conn,&$valvar,$nameid='gelombang',$add='',$empty=true,$edit=true) {
			require_once(Route::getModelPath('akademik'));
			$data = mAkademik::getArraygelombang($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Gelombang',$edit);
		}
		
		// combo gelombang
		function programpend($conn,&$valvar,$nameid='programpend',$add='',$empty=true,$edit=true) {
			require_once(Route::getModelPath('akademik'));
			$data = mAkademik::getProgrampend($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Program Pendidikan',$edit);
		}
		
		// combo periode
		function periode($conn,&$valvar,$nameid='periode',$add='',$empty=true,$edit=true) {
			$data = mCombo::periode($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Periode',$edit);
		}
		
		// combo periode
		function periodeDaftar($conn,&$valvar,$nameid='periodedaftar',$add='',$empty=true,$edit=true) {
			require_once(Route::getModelPath('akademik'));
			
			$data = mAkademik::getPeriodedaftar($conn);
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tahun Pendaftar',$edit);
		}
		
		// combo jalurpenerimaan
		function jenistagihan($conn,&$valvar,$nameid='jenis',$add='',$empty=true,$edit=true) {
			require_once(Route::getModelPath('jenistagihan'));
			
			$data = mJenistagihan::getDatacombo($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Penerimaan',$edit);
		}
		
		// combo format
		function format() {
			$data = array('html' => 'HTML', 'doc' => 'DOC', 'xls' => 'EXCEL', 'pdf' => 'PDF');
			
			return UI::createSelect('format',$data,'','ControlStyle');
		}
		
		// combo umum
		function combo($data,&$valvar,$nameid='unit',$add='',$empty=true,$label='',$edit=true) {
			if(!$empty and empty($data[$valvar]))
				$valvar = key($data);
			
			return UI::createSelect($nameid,$data,$valvar,'ControlStyle',$edit,$add,$empty,'-- Pilih '.$label.' --');
		}
		
		// combo daftar kolom
		function listColumn($kolom,$add='') {
			$data = array();
			foreach($kolom as $datakolom) {
				if(!empty($datakolom['kolom']))
					$data[$datakolom['kolom']] = $datakolom['label'];
			}
			
			return UI::createSelect('cfilter',$data,'','ControlStyle',true,$add);
		}
		
		// combo jumlah baris
		function listRowNum($value='',$add='') {
			$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','-1'=>'Semua');
			
			return UI::createSelect('row',$data,$value,'ControlStyle',true,$add);
		}
		
		// combo periode
		function periodewisuda($conn,&$valvar,$nameid='periode',$add='',$empty=true,$edit=true) {
			$data = mCombo::periodewisuda($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Periode',$edit);
		}
		
		function isedit(&$valvar,$nameid='isedit',$add='',$empty=true,$edit=true) {
			$data = array('G' => 'Generated','E' => 'Edited');
			return self::combo($data,$valvar,$nameid,$add,$empty,'All Status',$edit);
		}
	}
?>