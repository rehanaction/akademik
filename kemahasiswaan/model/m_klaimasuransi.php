<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mKlaimasuransi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_klaimasuransi';
		const sequence = 'klaim_asuransi_idklaim_seq';
		const order = 'idklaim';
		const key = 'idklaim';
		const label = 'Klaim Asuransi';

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select a.*, m.nim, m.nama, asu.namaasuransi,namaprsasuransi,nopolis
					from ".static::table()." a
					join  ".static::table('ms_asuransimhs')." ma using (idasuransimhs)
					join akademik.ms_mahasiswa m  using (nim)
					join ".static::table('ms_asuransi')." asu on ma.idasuransi = asu.idasuransi
					join ".static::table('ms_perusahaanasuransi')." p on asu.kodeprsasuransi = p.kodeprsasuransi
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
			$sql = " select sk.kodesyaratklaim, namasyaratklaim,
						case when
							sm.kodesyaratklaim is not null then
								1
							else
								0
						end as syarat
						from kemahasiswaan.ms_syaratklaimasuransi sk
						join kemahasiswaan.lv_syaratklaim s using (kodesyaratklaim)
						join kemahasiswaan.ms_asuransimhs ma on sk.idasuransi = ma.idasuransi
						left join kemahasiswaan.mw_syaratklaimmhs sm on sk.kodesyaratklaim = sm.kodesyaratklaim and sm.idasuransimhs = ma.idasuransimhs
						where ma.idasuransimhs = $key
						group by sk.kodesyaratklaim, namasyaratklaim , syarat ";

			return static::getDetail($conn,$sql,$label,$post);
		}

		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();

			switch($detail) {
				case 'syarat':
					$info['table'] = 'mw_syaratklaimmhs';
					$info['key'] = 'idklaim';
					$info['label'] = 'Syarat Klaim Asuransi';
					break;
			}

			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}


	}
?>
