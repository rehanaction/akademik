<?php
	// ui combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('combo'));
	
	class uCombo {
		function aktif($conn,&$valvar,$nameid='aktif',$add='',$empty=true) {
			$data = mCombo::aktif();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Aktif');
		}

		function brgstock($conn,&$valvar,$nameid='idbarang',$add='',$empty=true) {
			$data = mCombo::brgstock($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Barang Stock');
		}
	
		function cabang($conn,&$valvar,$nameid='cabang',$add='',$empty=true) {
			$data = mCombo::cabang($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Cabang');
		}

		function coa($conn,&$valvar,$nameid='coa',$add='',$empty=true) {
			$data = mCombo::coa($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Coa');
		}

		function gedung($conn,&$valvar,$nameid='gedung',$add='',$empty=true) {
			$data = mCombo::gedung($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Gedung');
		}
		
		function barang($conn,&$valvar,$nameid='barang',$add='',$empty=true) {
			$data = mCombo::barang($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Barang');
		}

		function lantai($conn,&$valvar,$nameid='lantai',$add='',$empty=true) {
			$data = mCombo::lantai();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Lantai');
		}

		function levelbarang($conn,&$valvar,$nameid='levelbarang',$add='',$empty=true) {
			$data = mCombo::levelbarang();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Level Barang');
		}

		function levelcoa($conn,&$valvar,$nameid='levelcoa',$add='',$empty=true) {
			$data = mCombo::levelcoa();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Level COA');
		}

		function jenislokasi($conn,&$valvar,$nameid='jenislokasi',$add='',$empty=true) {
			$data = mCombo::jenislokasi($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Lokasi');
		}
		
		function jenispenghapusan($conn,&$valvar,$nameid='jenispenghapusan',$add='',$empty=true) {
			$data = mCombo::jenispenghapusan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Penghapusan');
		}

		function jenispenyusutan($conn,&$valvar,$nameid='jenispenyusutan',$add='',$empty=true) {
			$data = mCombo::jenispenyusutan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Penyusutan');
		}

		function jenisperolehan($conn,&$valvar,$nameid='jenisperolehan',$add='',$empty=true) {
			$data = mCombo::jenisperolehan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Perolehan');
		}

		function jenisrawat($conn,&$valvar,$nameid='jenisrawat',$add='',$empty=true) {
			$data = mCombo::jenisrawat($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Perawatan');
		}

		function jenissupplier($conn,&$valvar,$nameid='jenissupplier',$add='',$empty=true) {
			$data = mCombo::jenissupplier($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Jenis Supplier');
		}
				
		function kondisi($conn,&$valvar,$nameid='kondisi',$add='',$empty=true) {
			$data = mCombo::kondisi($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Kondisi');
		}

		function lokasi($conn,&$valvar,$nameid='lokasi',$add='',$empty=true,$idunit='') {
			$data = mCombo::lokasi($conn,$idunit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Lokasi');
		}

		function lokasibrg($conn,&$valvar,$nameid='lokasi',$add='',$empty=true,$idunit='') {
			$data = mCombo::lokasibrg($conn,$idunit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Lokasi');
		}

		function pemakai($conn,&$valvar,$nameid='pemakai',$add='',$empty=true,$idunit='') {
			$data = mCombo::pemakai($conn,$idunit);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Pemakai');
		}

		function satuan($conn,&$valvar,$nameid='satuan',$add='',$empty=true) {
			$data = mCombo::satuan($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Satuan');
		}

		function status($conn,&$valvar,$nameid='status',$add='',$empty=true) {
			$data = mCombo::status($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Status');
		}

		function statusproses($conn,&$valvar,$nameid='statusproses',$add='',$empty=true) {
			$data = mCombo::statusproses();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Status');
		}

		function sumberdana($conn,&$valvar,$nameid='sumberdana',$add='',$empty=true) {
			$data = mCombo::sumberdana($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Sumber Dana');
		}

		function supplier($conn,&$valvar,$nameid='supplier',$add='',$empty=true) {
			$data = mCombo::supplier($conn);
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Supplier');
		}

		function merk($conn,&$valvar,$nameid='merk',$add='',$empty=true) {
			$data = mCombo::merk($conn);
			return self::combo($data,$valvar,$nameid,$add,$empty,'Merk');
		}

		function tahun($conn,&$valvar,$nameid='tahun',$add='',$empty=true) {
			$data = mCombo::tahun();
			return self::combo($data,$valvar,$nameid,$add,$empty,'Tahun');
		}

		function bulan($conn,&$valvar,$nameid='bulan',$add='',$empty=true) {
			$data = mCombo::bulan();
			return self::combo($data,$valvar,$nameid,$add,$empty,'Bulan');
		}

		function unit($conn,&$valvar,$nameid='unit',$add='',$empty=true) {
			$data = mCombo::unit($conn,false);			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Unit');
		}
		
		function unitAuto($conn,&$valvar,$nameid='unit',$add='',$empty=true) {
		    if(empty($valvar)){
		        $lr = Modul::getLeftRight();
		        $data = mCombo::unitByLeft($conn, $lr['LEFT']);
		        $valvar = $data['idunit'];
	        }else
		        $data = mCombo::unitData($conn, $valvar);
		    
	        $l_unit .= '<input id="namaunit" class="ControlAuto" type="text" size="35" maxlength="50" autocomplete="off" value="'.$data['namaunit'].'">';
	        $l_unit .= '<input type="hidden" id="'.$nameid.'" name="'.$nameid.'" '.$add.' value="'.$data['idunit'].'">';
	        $l_unit .= '&nbsp;
	                    <img id="imgunit_c" src="images/green.gif">
	                    <img id="imgunit_u" src="images/red.gif" style="display:none">';
            
		    return $l_unit;
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
		function listColumn($kolom,$add='style="width:100px;"') {
			$data = array();
			foreach($kolom as $datakolom) {
			    if($datakolom['nosearch']) continue;
			    
				if(!empty($datakolom['kolom']))
					$data[$datakolom['kolom']] = $datakolom['label'];
			}
			
			return UI::createSelect('cfilter',$data,'','ControlStyle',true,$add);
		}
		
		// combo jumlah baris
		function listRowNum($value='',$add='') {
			$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30');
			
			return UI::createSelect('row',$data,$value,'ControlStyle',true,$add);
		}

		function level($conn,&$valvar,$nameid='level',$add='',$empty=true) {
			$data = mCombo::level();
			
			return self::combo($data,$valvar,$nameid,$add,$empty,'Level');
		}
	}
?>
