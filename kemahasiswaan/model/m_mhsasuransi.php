<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMhsasuransi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_asuransimhs';
		const sequence = 'asuransi_mahasiswa_idasuransimhs_seq';		
		const order = 'idasuransimhs';
		const key = 'idasuransimhs';
		const label = 'Asuransi Mahasiswa';
		
		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select m.nim, m.nama, a.*, namaprsasuransi,namajenisasuransi
					from ".static::table()." a 
					join akademik.ms_mahasiswa m  using (nim)
					join ".static::table('ms_asuransi')." asu on a.idasuransi = asu.idasuransi
					join ".static::table('ms_perusahaanasuransi')." p on asu.kodeprsasuransi = p.kodeprsasuransi
					join ".static::table('lv_jenisasuransi')." j on asu.idjenisasuransi = j.idjenisasuransi
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
			$sql = " select kodesyaratklaim, namasyaratklaim from ".static::table('ms_syaratklaimasuransi')." sk 
					join ".static::table('lv_syaratklaim')." s using (kodesyaratklaim)
					 where idasuransi = $key ";
					 
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'syarat':
					$info['table'] = 'lv_syaratklaim';
					$info['key'] = 'idsyaratasuransi';
					$info['label'] = 'Syarat Asuransi';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		function getAsuransiMhs($conn,$key){
			$sql = "select idasuransimhs, namaasuransi 
					from ".static::table()." s 
					join ".static::table('ms_asuransi')." a using (idasuransi)
					where nim = '$key' ";
			return Query::arrQuery($conn,$sql);
		}
		
	}
?>
