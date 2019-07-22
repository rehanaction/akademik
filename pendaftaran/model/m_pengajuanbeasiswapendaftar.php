<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mPengajuanBeasiswaPd extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_pengajuanbeasiswapendaftar';
		const sequence = 'mw_pengajuanbeasiswapendaftar_idpengajuanbeasiswa_seq';
		const order = 'idpengajuanbeasiswa';
		const key = 'idpengajuanbeasiswa';
		const label = 'Pengajuan Beasiswa';

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select a.*, b.periode||'/'||namajenisbeasiswa as beasiswa,namatahap,nama,namabeasiswa,m.ipk
					from ".static::table()." a
					join pendaftaran.pd_pendaftar m  using (nopendaftar)
					join kemahasiswaan.ms_beasiswa b  using (idbeasiswa)
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa)
					join kemahasiswaan.lv_tahapbeasiswa t  using (idtahapbeasiswa)
					";

			return $sql;
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *,m.ipk,s.namastatus
					from ".static::table()." p
					join pendaftaran.pd_pendaftar m  using (nopendaftar)
					left join akademik.lv_statusmhs s on m.statusmhs = s.statusmhs
					where ".static::getCondition($key);

			return $sql;
		}

		function getListFilter($col,$key) {
			switch($col) {
				case 'ipk':
				list($bawah,$atas) = explode('|',$key);
				return " ipk between $bawah and $atas ";
			}
		}
		function getSyarat($conn,$key){
			$sql = " select sk.kodesyaratbeasiswa, namasyaratbeasiswa,qty,
						case when
							sm.kodesyaratbeasiswa is not null then
								1
							else
								0
						end as syarat,isberkas,fileberkas
						 from kemahasiswaan.lv_syaratbeasiswa sk
						 join kemahasiswaan.ms_beasiswa ma on sk.idbeasiswa = ma.idbeasiswa
						 left join kemahasiswaan.mw_syaratbeasiswamaba sm on sk.kodesyaratbeasiswa = sm.kodesyaratbeasiswa
						 left join kemahasiswaan.mw_berkasbeasiswamaba b
						 on sk.kodesyaratbeasiswa = b.kodesyaratbeasiswa and b.idpengajuanbeasiswa = ".$key['idpengajuanbeasiswa']."
						where ma.idbeasiswa = ".$key['idbeasiswa']."
						group by sk.kodesyaratbeasiswa, namasyaratbeasiswa ,qty, syarat,isberkas,fileberkas ";

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
		function getIdByPd($conn,$nopendaftar){
			$sql = "select idpengajuanbeasiswa
					from ".static::table('mw_pengajuanbeasiswapendaftar')." p
					where nopendaftar = '$nopendaftar' ";
			return $conn->GetOne($sql);
		}
		function getValidByPd($conn,$nopendaftar){
			$sql = "select iditerima
					from ".static::table('mw_pengajuanbeasiswapendaftar')." p
					where nopendaftar = '$nopendaftar' ";
			return $conn->GetOne($sql);
		}
	}
?>
