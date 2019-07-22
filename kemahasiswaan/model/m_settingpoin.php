<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSettingpoin extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_settingpoin';
		const order = 'kodejenjang,periode,idtahap';
		const key = 'periode,idtahap,kodejenjang';
		const label = 'setting poin';
		
		
		function listQuery($conn) {
			$sql = "select *
					from ".static::table('mw_tahap')." t 
					left join ".static::table()." p using (idtahap)
					";
			
			return $sql;
		}
		
		function getListData($conn,$periode='',$kodejenjang=''){
			$sql = " select t.idtahap as keytahap, namatahap, p.* from ".static::table('mw_tahap')." t 
					 left join ".static::table()."  p on t.idtahap = p.idtahap ";
			if(!empty($periode))
				$sql .= " and  p.periode = '$periode' ";
			if(!empty($kodejenjang))
				$sql .= " and p.kodejenjang = '$kodejenjang'  ";
			
			$sql .=	" order by kodejenjang,periode,t.idtahap   ";
			
			return $conn->GetArray($sql);
		}
		
		function getListArrayPeriode($conn,$periode='',$kodejenjang=''){
			$sql = " select * from ".static::table()." where 1=1 ";
			
			if(!empty($periode))
				$sql .= " and  periode = '$periode' ";
			if(!empty($kodejenjang))
				$sql .= " and kodejenjang = '$kodejenjang'  ";
			
			$sql .=	" order by kodejenjang,periode,idtahap   ";
			
			$rows = $conn->GetArray($sql);;
			
			$data = array();
			if(!empty($rows)){
				foreach($rows as $row){
					$data[$row['periode']][$row['kodejenjang']][$row['idtahap']] = $row;
				}
			}
			
			return $data;
			//var_dump($data);exit;
		}
		
		// mendapatkan data untuk session
		function getDataSession($conn) {
			$sql = "select * from ".static::table()." where idsetting = 1";
			$row = $conn->GetRow($sql);
			
			$rows = array();
			$rows['KURIKULUM'] = $row['thnkurikulumsekarang'];
			$rows['PERIODE'] = $row['periodesekarang'];
			$rows['PERIODESPA'] = $row['periodespa'];
			$rows['TAHAP'] = $row['tahapfrs'];
			$rows['ISINILAI'] = $row['isinilai'];
			$rows['BIODATAMHS'] = $row['biodatamhs'];
			$rows['PERIODENILAI'] = $row['periodenilai'];
			$rows['PERIODENILAISPA'] = $row['periodenilaispa'];
			$rows['ISDEFAULTSKALANILAI'] = $row['isparameternilai'];
			$rows['DETIP'] = $row['detip'];
			$rows['PROSENTASEABSENSI'] = $row['pros_kehadiran'];
			
			return $rows;
		}
		
	}
?>
