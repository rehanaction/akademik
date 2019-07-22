<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTarifformulir extends mModel {
		const schema = 'h2h';
		const table = 'ke_tariffrm';
		const order = 'idtariffrm';
		const key = 'idtariffrm';
		const label = 'idtariffrm';
		
	// mendapatkan array data
		function getArraytarif($conn,$periode='',$jalur='',$gelombang='',$programpend='') {
			$sql = "select  t.*,g.namagelombang,s.namasistem||' '||tipeprogram as namasistem from ".static::table()." t
					left join pendaftaran.lv_gelombang g on g.idgelombang = t.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = t.sistemkuliah
					left join akademik.lv_jalurpenerimaan j on j.jalurpenerimaan = t.jalurpenerimaan
					where (1=1)";
			
			if($periode <> '')
				$sql .= " and t.periodedaftar = '$periode'";
			if($jalur<>'')
				$sql .= " and t.jalurpenerimaan = '$jalur'";
			if($gelombang<>'')
				$sql .= " and t.idgelombang = '$gelombang'";
			if($programpend<>'')
				$sql .= " and t.programpend = '$programpend'";
			
			$sql .= " order by g.namagelombang desc, j.kodejalur, s.sistemkuliah desc";
		
			return $conn->GetArray($sql);
		}
		
		//get id tarif 
		function getIdtarif($conn,$data){
			$sql = " select idtariffrm from ".static::table()." where (1=1)";
			foreach($data as $i => $val)
				$sql .= " and ".$i." = '".$val."'";
			
			return $conn->GetOne($sql);
			
			}
			
		function getTarifbykode($conn,$kode,$aktif=false){
			$sql = " select t.*,g.namagelombang,s.namasistem||' '||tipeprogram as namasistem from ".static::table()." t
					left join pendaftaran.lv_gelombang g on g.idgelombang = t.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = t.sistemkuliah
					where kodeformulir = '$kode'".($aktif ? ' and isaktif = 1' : '');
			return $conn->GetRow($sql);
			
			}
		
	}
?>