<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
		
		function arrFlagtagihan(){
				return array('A'=>'Awal Perkuliahan','B' => 'Per Bulan' , 'S' => 'Per Semester', 'T' => 'Per Tahun', 'W' => 'Per Wisuda');
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
		
		function periodewisuda($conn) {
			require_once(Route::getModelPath('akademik'));
			
			return mAkademik::getArrayperiodeyudisium($conn);
		}	
			
	}
	
?>