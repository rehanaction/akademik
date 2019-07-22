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
		const sequence = 'lv_smu_idsmu_seq';
		
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

		function getNamasmu($conn,$idsmu){
			
			$sql = "select namasmu from pendaftaran.lv_smu where idsmu = '$idsmu'";
			
			return $conn->getOne($sql);
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
		function findSmu($conn,$str,$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table()." m
					left join akademik.ms_kota t using (kodekota)
					where lower($col::varchar) like '%$str%' order by ".static::order;
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}		
	}
?>
