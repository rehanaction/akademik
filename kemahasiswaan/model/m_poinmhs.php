<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mPoinmhs extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_poinmhs';
		//const sequence = 'mw_poinmhs_idtranspoin_seq';
		const order = 'nim desc';
		const key = 'nim';
		const label = 'poin';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select * from (select u.infoleft, u.inforight, m.sistemkuliah,m.nim, m.nama, m.sex, m.tgllahir, m.semestermhs, m.skslulus, m.ipk, m.statusmhs, m.alamat, m.telp, m.hp, m.email,
					m.kodeunit, u.namaunit, up.namaunit as namafakultas,poinkegiatan as poinpengalaman,poinpelanggaran,pp.poinprestasi,
					 (pk.poinkegiatan + pp.poinprestasi) as totalpoin
					from akademik.ms_mahasiswa m
					left join kemahasiswaan.v_poinkegiatanmhs pk using (nim)
					left join kemahasiswaan.v_poinprestasimhs pp using (nim)
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join gate.ms_unit up on u.kodeunitparent = up.kodeunit
					left join ".static::table()." p  on p.nim = m.nim) x ";

			return $sql;
		}

		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data = array();
			$data['totalpoin'] = '((poinprestasi+poinpengalaman)-poinpelanggaran)';

			return $data;
		}

		function getListFilter($col,$key) {
			switch($col) {
				case 'totalpoin': return "((poinprestasi+poinpengalaman)-poinpelanggaran) = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));

					$row = mUnit::getData($conn,$key);

					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
				default:
					return parent::getListFilter($col,$key);
			}
		}


		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." p
					where ".static::getCondition($key);

			return $sql;
		}

		// mendapatkan nama mahasiswa
		function getNamaMahasiswa($conn,$nim) {
			$sql = "select nama from akademik.ms_mahasiswa where nim = '$nim'";

			$data = $conn->GetOne($sql);
			return $data;
		}

	}
?>
