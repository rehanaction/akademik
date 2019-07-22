<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
		
		function arrFlagtagihan(){
				return array('A'=>'Awal Perkuliahan','S'=>'Per Semester','P'=>'Per Semester Pendek','W'=>'Per Wisuda','K'=>'Per Kasus');
			}
			
		function arrJenish2h(){
				return array('0'=>'Non H2H','1' => 'H2H' );
			}
			
		function unit($conn,$dot=true,$level='') {
			require_once(Route::getModelPath('akademik'));
			
			return mAkademik::getArrayunit($conn,$dot,$level);
		}	
		
		function jalur($conn) {
			require_once(Route::getModelPath('akademik'));
			
			return mAkademik::getArrayjalur($conn);
		}
		
		function periode($conn) {
			require_once(Route::getModelPath('akademik'));
			
			return mAkademik::getArrayperiode($conn);
		}	
		function gelombang($conn) {
			$sql = "select idgelombang, namagelombang from pendaftaran.lv_gelombang order by namagelombang";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function kategoriukt($conn) {
			require_once(Route::getModelPath('akademik'));
			
			return makademik::getArraykategoriukt($conn);
		}	
		
		function periodewisuda($conn) {
			require_once(Route::getModelPath('akademik'));
			
			return mAkademik::getArrayperiodeyudisium($conn);
		}	
		
		function sistemkuliah($conn) {
			require_once(Route::getModelPath('akademik'));
			
			return mAkademik::getArraySistemKuliahCombo($conn);
		}
		function jurusan($conn,$fakultas='') {
			$sql = "select kodeunit, namaunit from gate.ms_unit where level = 2 and isakad=-1";
			if(!empty($fakultas))
				$sql .= " and kodeunitparent = '$fakultas'";
			$sql .= " order by infoleft";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function tahun_angkatan($singkat=true,$min=1996) {
			$data = array();
			for($i=date('Y')+1;$i>=$min;$i--)
				$data[$i] = ($singkat ? $i : $i.' - '.($i+1));
						
			return $data;
		}

		function jenistagihan($conn)
		{
			$sql = "select jenistagihan, namajenistagihan from h2h.lv_jenistagihan";
			return Query::arrQuery($conn,$sql);
		}
	}
	
?>
