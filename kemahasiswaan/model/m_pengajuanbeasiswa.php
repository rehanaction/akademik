<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mPengajuanBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_pengajuanbeasiswa';
		const sequence = 'mw_pengajuanbeasiswa_idpngajuanbeasiswa_seq';
		const order = 'idpengajuanbeasiswa';
		const key = 'idpengajuanbeasiswa';
		const label = 'Pengajuan Beasiswa';

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select a.*, namajenisbeasiswa,namatahap,nama,namabeasiswa,m.ipk,namastatus
					from ".static::table()." a
					join akademik.ms_mahasiswa m  using (nim)
					join akademik.lv_statusmhs s  using (statusmhs)
					join kemahasiswaan.ms_beasiswa b  using (idbeasiswa)
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa)
					join kemahasiswaan.lv_tahapbeasiswa t  using (idtahapbeasiswa)
					";

			return $sql;
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select p.*,m.ipk,s.namastatus
					from ".static::table()." p
					join akademik.ms_mahasiswa m using(nim)
					left join akademik.lv_statusmhs s on m.statusmhs = s.statusmhs
					where ".static::getCondition($key);

			return $sql;
		}

		function getListFilter($col,$key) {
			switch($col) {
				case 'ipk':
					list($bawah,$atas) = explode('|',$key);
					return " ipk between $bawah and $atas ";
				case 'ipkatas':
					return 'ipk <= '.(float)$key;
				case 'ipkbawah':
					return 'ipk >= '.(float)$key;
				default:
					return parent::getListFilter($col,$key);
			}
		}
		function getSyarat($conn,$idbeasiswa,$idpengajuanbeasiswa){
			$sql = " select sk.kodesyaratbeasiswa, sk.namasyaratbeasiswa,sk.qty,
						case when
							sm.kodesyaratbeasiswa is not null then
								1
							else
								0
						end as syarat,sk.isberkas,b.fileberkas
						 from kemahasiswaan.lv_syaratbeasiswa sk
						 join kemahasiswaan.ms_beasiswa ma on sk.idbeasiswa = ma.idbeasiswa
						 left join kemahasiswaan.mw_syaratbeasiswamhs sm on sk.kodesyaratbeasiswa = sm.kodesyaratbeasiswa and sm.idpengajuanbeasiswa = ".Query::escape($idpengajuanbeasiswa)."
						 left join kemahasiswaan.mw_berkasbeasiswamhs b on sk.kodesyaratbeasiswa = b.kodesyaratbeasiswa and b.idpengajuanbeasiswa = ".Query::escape($idpengajuanbeasiswa)."
						where ma.idbeasiswa = $idbeasiswa
						group by sk.kodesyaratbeasiswa, sk.namasyaratbeasiswa ,sk.qty, syarat,sk.isberkas,b.fileberkas ";

			return static::getDetail($conn,$sql,$label,$post);
		}

		function getPrestasi($conn,$key){
			$sql = "select pb.*,namajenisprestasi,namatingkatprestasi,namakategoriprestasi,namaprestasi
					from kemahasiswaan.mw_prestasibeasiswa pb
					left join ".static::table('lv_jenisprestasi')." jp on pb.kodejenisprestasi = jp.kodejenisprestasi
					left join ".static::table('lv_tingkatprestasi')." tp on pb.kodetingkatprestasi = tp.kodetingkatprestasi
					left join ".static::table('lv_kategoriprestasi')." kp on pb.kodekategoriprestasi = kp.kodekategoriprestasi
					where pb.idpengajuanbeasiswa = $key ";
			return static::getDetail($conn,$sql,$label,$post);
		}

		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();

			switch($detail) {
				case 'syarat':
					$info['table'] = 'mw_syaratbeasiswamhs';
					$info['key'] = 'idpengajuanbeasiswa';
					$info['label'] = 'Syarat Beasiswa';
					break;
				case 'prestasi':
					$info['table'] = 'mw_prestasibeasiswa';
					$info['key'] = 'idprestasibeasiswa';
					$info['label'] = 'Prestasi';
					break;
			}

			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}


	}
?>
