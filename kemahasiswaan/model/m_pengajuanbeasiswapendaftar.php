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
			$sql = "select * from (
					select a.*, periode,namajenisbeasiswa,nama,namabeasiswa,namatahap,b.idjenisbeasiswa,
					round((raport_10_1+raport_10_2+raport_11_1+raport_11_2+raport_12_1)/5,2) as nilairapor,
					raport_10_1,raport_10_2,raport_11_1,raport_11_2,raport_12_1,raport_12_2
					from ".static::table()." a
					join pendaftaran.pd_pendaftar m  using (nopendaftar)
					join kemahasiswaan.ms_beasiswa b  using (idbeasiswa)
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa)
					join kemahasiswaan.lv_tahapbeasiswa t  using (idtahapbeasiswa)
					) a";

			return $sql;
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select p.*,m.*
					from ".static::table()." p
					join pendaftaran.pd_pendaftar m  using (nopendaftar)
					where ".static::getCondition($key);

			return $sql;
		}

		function getListFilter($col,$key) {
			switch($col) {
				case 'nilaiun':
					list($bawah,$atas) = explode('|',$key);
					return " nilaiun between $bawah and $atas ";
				case 'nilaiunatas':
					return 'nilaiun <= '.(float)$key;
				case 'nilaiunbawah':
					return 'nilaiun >= '.(float)$key;
				case 'nilairapor':
					list($bawah,$atas) = explode('|',$key);
					return " nilairapor between $bawah and $atas ";
				case 'nilairaporatas':
					return 'nilairapor <= '.(float)$key;
				case 'nilairaporbawah':
					return 'nilairapor >= '.(float)$key;
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
						 left join kemahasiswaan.mw_syaratbeasiswamaba sm on sk.kodesyaratbeasiswa = sm.kodesyaratbeasiswa and sm.idpengajuanbeasiswa = ".Query::escape($idpengajuanbeasiswa)."
						 left join kemahasiswaan.mw_berkasbeasiswamaba b on sk.kodesyaratbeasiswa = b.kodesyaratbeasiswa and b.idpengajuanbeasiswa = ".Query::escape($idpengajuanbeasiswa)."
						 where ma.idbeasiswa = $idbeasiswa
						group by sk.kodesyaratbeasiswa, sk.namasyaratbeasiswa ,sk.qty, syarat,sk.isberkas,b.fileberkas ";

			return static::getDetail($conn,$sql,$label,$post);
		}

		function getPrestasi($conn,$key){
			$sql = "select pb.*,namajenisprestasi,namatingkatprestasi,namakategoriprestasi,namaprestasi
					from kemahasiswaan.mw_prestasibeasiswamaba pb
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
					$info['table'] = 'mw_syaratbeasiswamaba';
					$info['key'] = 'idpengajuanbeasiswa';
					$info['label'] = 'Syarat Beasiswa';
					break;
				case 'prestasi':
					$info['table'] = 'mw_prestasibeasiswamaba';
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

		function getDataAlasan($conn,$key){
			$sql = "select m.*, pil1.namaunit as pilihan1,pil2.namaunit as pilihan2,pil3.namaunit as pilihan3,alasan1,alasan2
					from pendaftaran.pd_pendaftar m
					left join gate.ms_unit pil1 on pil1.kodeunit=m.pilihan1
					left join gate.ms_unit pil2 on pil2.kodeunit = m.pilihan2
					left join gate.ms_unit pil3 on pil3.kodeunit = m.pilihan3
					left join kemahasiswaan.mw_pengajuanbeasiswapendaftar bs using (nopendaftar)
					where m.nopendaftar = '$key'   ";
			return $conn->getRow($sql);
		}


		function getOrganisasi($conn,$key){
			$sql = "select pb.*
					from kemahasiswaan.mw_organisasibeasiswa pb
					where pb.idpengajuanbeasiswa = $key ";
			return static::getDetail($conn,$sql,$label,$post);
		}

		function getPelatihan($conn,$key){
			$sql = "select pb.*
					from kemahasiswaan.mw_pelatihanbeasiswa pb
					where pb.idpengajuanbeasiswa = $key ";
			return static::getDetail($conn,$sql,$label,$post);
		}
		function getKerja($conn,$key){
			$sql = "select pb.*
					from kemahasiswaan.mw_kerjabeasiswa pb
					where pb.idpengajuanbeasiswa = $key ";
			return static::getDetail($conn,$sql,$label,$post);
		}
		function getDataAnak($conn,$key){
			$sql = "select *
					from  kemahasiswaan.mw_pengajuanbeasiswapendaftar bs
					where idpengajuanbeasiswa = '$key'";
			return $conn->getRow($sql);
		}

	}
?>
