<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mKegiatan extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_kegiatan';
		const sequence = 'kegiatan_idkegiatan_seq';
		const order = 'idkegiatan';
		const key = 'idkegiatan';
		const label = 'Kegiatan';

		// mendapatkan array data
		/*
		function listQuery($conn) {
			$sql = "select k.*, namaorganisasi, m.nim ||'-'|| m.nama as ketupel
					from ".static::table()." k
					join ".static::table('ms_organisasi')." o using (kodeorganisasi)
					left join akademik.ms_mahasiswa m on k.nimketupel = m.nim ";

			return $sql;
		}
		*/

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select kodeorganisasi,namaorganisasi, o.keterangan,
					mk.nama as namaketua, mw.nama as namawakil,telpwakil,alamatorganisasi,nippegawai,
					telporganisasi,o.kodeunit,namaunit,
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pembina
					from ".static::table()." k
					left join akademik.ms_mahasiswa mk on o.nimketua = mk.nim
					left join akademik.ms_mahasiswa mw on o.nimwakil = mw.nim
					left join sdm.ms_pegawai p on p.idpegawai::text = o.nippegawai
					left join gate.ms_unit u on o.kodeunit = u.kodeunit ";

			return $sql;
		}


		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." k
					where ".static::getCondition($key);

			return $sql;
		}

		// mendapatkan combo
		function getArray($conn) {
			$sql = "select idkegiatan, namakegiatan
					from ".static::table()." k ";

			return Query::arrQuery($conn,$sql);

		}

		// get list kegiatan
		function getSQLListKegiatan() {
			$sql = "select * from (select k.*,
					       namaorganisasi,
					       Concat(m.nim, '-', m.nama) as ketupel
					from   kemahasiswaan.mw_kegiatan k
					       join kemahasiswaan.ms_organisasi o using (kodeorganisasi)
					       left join akademik.ms_mahasiswa m
					              on k.nimketupel = m.nim) a";

			return $sql;
		}

		function getListKegiatan($conn,$filter,$arrfilter) {
			$a_filter = array();
			if(!empty($filter)) {
				$a_filter[] = "(Lower(( namaorganisasi ) :: varchar) like '%".$filter."%'
					          or Lower(( namakegiatan ) :: varchar) like '%".$filter."%'
					          or Lower(( periode ) :: varchar) like '%".$filter."%'
					          or Lower(( Concat(m.nim, ' ', m.nama) ) :: varchar) like '%".$filter."%')";
			}
			if(!empty($arrfilter))
				$a_filter = array_merge($a_filter,$arrfilter);

			$sql = static::getSQLListKegiatan()."
					".(empty($a_filter) ? '' : "where  ".implode(' and ',$a_filter))."
					order  by idkegiatan ";

			return $conn->GetArray($sql);
		}

		function getByProposal($conn,$key)
		{
			$sql = "select *  from ".static::table()." k
							where idproposal = $key
							";
			return $conn->GetArray($sql);
		}

		function deleteByProposal($conn,$key)
		{
			$sql = "delete  from ".static::table()." k
							where idproposal = $key
							";
			return $conn->Execute($sql);
		}
	}
?>
