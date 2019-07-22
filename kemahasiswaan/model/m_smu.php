<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSmu extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_smu';
		const order = 'idsmu';
		const key = 'idsmu';
		const label = 'SMU';
		
		function getArray($conn) {
			$sql = "select idsmu, namasmu from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		function getListFilter($col,$key) {
			switch($col) {
				case 'propinsi': return "substr(kodekota,1,2) = '$key'";
				case 'kota': return "kodekota = '$key'";
			}
		}
		function getKota($conn){
			$sql="
				SELECT kodekota, namakota FROM akademik.ms_kota order by namakota
				";
			return Query::arrQuery($conn,$sql);
		}
		
		function getSmu(){
			global $conn;
			$sql = "select * from pendaftaran.lv_smu";
			
			return $conn->SelectLimit($sql);
		}
		function insertManual($conn,$recsmu){
			$recsmu['idsmu']=$conn->GetOne("select max(idsmu)+1 from pendaftaran.lv_smu");
			list($p_posterr,$p_postmsg) = self::insertRecord($conn,$recsmu,true);
			
			//return array($p_posterr,$p_postmsg);
			return $recsmu['idsmu'];
		}
	}
?>
