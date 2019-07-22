<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAsuransi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_asuransi';
		const sequence = 'asuransi_idasuransi_seq';		
		const order = 'idasuransi';
		const key = 'idasuransi';
		const label = 'nama asuransi';
		
		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select a.*, namaprsasuransi,namajenisasuransi
					from ".static::table()." a 
					join ".static::table('ms_perusahaanasuransi')." p using (kodeprsasuransi)
					join ".static::table('lv_jenisasuransi')." j on a.idjenisasuransi = j.idjenisasuransi
					";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." p
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		function getSyarat($conn,$key){
			$sql = " select kodesyaratklaim, namasyaratklaim from ".static::table('ms_syaratklaimasuransi')." s 
					 join ".static::table('lv_syaratklaim')."  d using (kodesyaratklaim)
					 where idasuransi = $key ";
					 
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'syarat':
					$info['table'] = 'ms_syaratklaimasuransi';
					$info['key'] = 'idasuransi,kodesyaratklaim';
					$info['label'] = 'Syarat Asuransi';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idasuransi, namaasuransi from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan nama asuransi
		function getNama($conn,$idasuransi) {
			$sql = "select  namaasuransi from ".static::table()." where idasuransi = $idasuransi ";
			
			return $conn->GetOne($sql);
		}
		
	}
?>
