<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHasilQuiz extends mModel {
		const schema = 'akademik';
		const table = 'ak_hasilquiz';
		const order = 'periode, thnkurikulum, kodeunit, kodemk, kelasmk';
		const key = 'periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, nipdosen, idsoal';
		const label = 'Hasil Quisioner';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select distinct periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, nipdosen from ".static::table();
			
			//return Query::arrQuery($conn,$sql);
			return $conn->GetArray($sql);
		}
		function setQuiz($conn,$r_key){
			require_once(Route::getModelPath('krs'));
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,$nim,$nipdosen) = explode('|',$r_key);
			$krs_cond=mKrs::getCondition($r_key);
			$cekQuiz=mKrs::cekQuisioner($conn,$nim,$periode,$kodemk);
			if($cekQuiz){
				$update=$conn->Execute("update ".static::table('ak_krs')." set isquiz=-1 where $krs_cond");
			}
		}
		
		function getDataCek($conn,$nim,$periode){
			$sql = "select distinct periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, nipdosen from ".static::table()." where nim='$nim' and periode='$periode'";
			
			//return Query::arrQuery($conn,$sql);
			return $conn->GetArray($sql);
		}
		
	}
?>
