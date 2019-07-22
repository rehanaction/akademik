<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('combo'));
	
	class uCombo {
		function dosen($conn,&$valvar,$unit='',$nameid='nip',$add='',$empty=true) {
			$data = mCombo::dosen($conn,$unit);
			if(empty($add))
				$add = 'style="width:300px"';
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Dosen');
		}
		
		function modul($conn,&$valvar,$nameid='modul',$add='',$empty=true) {
			$data = mCombo::modul($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Modul');
		}
		
		function role($conn,&$valvar,$nameid='role',$add='',$empty=true) {
			$data = mCombo::role($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Role');
		}
		function rolehusus($conn,&$valvar,$nameid='role',$add='',$role) {
			$empty=true;
			if($role=='DAA' or $role=='DAAN' or $role=='DAAR' or $role=='PDAAN' or $role=='PDAAR'){
				$empty=false;
			}
			$data = mCombo::rolehusus($conn,$role);
		
			return self::combo($data,$valvar,$nameid,$add,$empty,'Role');
		}
		function unit($conn,&$valvar,$nameid='unit',$add='',$empty=true) {
			$data = mCombo::unit($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		
		// combo format
		function format() {
			$data = array('HTML','DOC','EXCEL');
			
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
				if(!empty($datakolom['kolom']))
					$data[$datakolom['kolom']] = $datakolom['label'];
			}
			
			return UI::createSelect('cfilter',$data,'','ControlStyle',true,$add);
		}
		
		// combo jumlah baris
		function listRowNum($value='',$add='') {
			$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','300'=>'300');
			
			return UI::createSelect('row',$data,$value,'ControlStyle',true,$add);
		}
	}
?>
