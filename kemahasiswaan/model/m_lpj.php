<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mLpj extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_lpj';
		const order = 'idlpj';
		const key = 'idlpj';
		const label = 'LPJ';
		const uptype = 'lpj';
		const sequence = 'mw_lpj_idlpj_seq';

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select j.*, namaorganisasi, namakegiatan, m.nama
					from ".static::table()." j
					left join ".static::table('mw_proposalkegiatan')." p  using (idproposal)
					left join ".static::table('ms_organisasi')." o using (kodeorganisasi)
					left join ".static::table('mw_kegiatan')." k using (idproposal)
					";

			return $sql;
		}

		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'fromtanggal')
				return 'j.tgllpj >= '.Query::escape(CStr::formatDate($key));
			else if($col == 'sdtanggal')
				return 'j.tgllpj <= '.Query::escape(CStr::formatDate($key));
			else if($col == 'unit'){
				global $conn, $conf;
				require_once(Route::getModelPath('unit'));

				$row = mUnit::getData($conn,$key);

				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			else
				return parent::getListFilter($col,$key);
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." k
					where ".static::getCondition($key);

			return $sql;
		}

		// get list lpj
		function getSQLListLpj() {
			$sql = "select j.*, m.nama,
					       namaorganisasi,
					       namakegiatan
					from   kemahasiswaan.mw_lpj j
					       left join kemahasiswaan.mw_proposalkegiatan p using (idproposal)
					       left join kemahasiswaan.ms_organisasi o on o.kodeorganisasi = j.kodeorganisasi
					       left join kemahasiswaan.mw_kegiatan k using (idproposal)
						   left join akademik.ms_mahasiswa m on j.nrp = m.nim
							 join gate.ms_unit u on o.kodeunit = u.kodeunit ";

			return $sql;
		}

		function getListLpj($conn,$filter,$arrfilter) {
			$a_filter = array();
			if(!empty($filter)) {
				$a_filter[] = "( ( j.nosurat ) :: varchar like '%".$filter."%'
					          or ( namaorganisasi ) :: varchar like '%".$filter."%'
					          or ( namakegiatan ) :: varchar like '%".$filter."%'  )";
			}
			if(!empty($arrfilter))
				$a_filter = array_merge($a_filter,$arrfilter);

			$sql = static::getSQLListLpj()."
					".(empty($a_filter) ? '' : "where  ".implode(' and ',$a_filter))."
					order  by idlpj ";

			return $conn->GetArray($sql);
		}
	}
?>
