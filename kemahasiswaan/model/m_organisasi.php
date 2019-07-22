<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mOrganisasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_organisasi';
		const order = 'kodeorganisasi';
		const key = 'kodeorganisasi';
		const label = 'namaorganisasi';

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select kodeorganisasi,namaorganisasi, o.keterangan,
					mk.nama as namaketua, mw.nama as namawakil,telpwakil,alamatorganisasi,nippegawai,
					telporganisasi,o.kodeunit,namaunit,
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pembina
					from ".static::table()." o
					left join akademik.ms_mahasiswa mk on o.nimketua = mk.nim
					left join akademik.ms_mahasiswa mw on o.nimwakil = mw.nim
					left join sdm.ms_pegawai p on p.idpegawai::text = o.nippegawai
					left join gate.ms_unit u on o.kodeunit = u.kodeunit ";

			return $sql;
		}

		// get list oraganisasi
		function getListOrganisasi($conn,$filter) {
			$sql = "select kodeorganisasi,
					       namaorganisasi,
					       o.keterangan,
					       mk.nama
					       as namaketua,
					       mw.nama
					       as namawakil,
					       telpwakil,
					       alamatorganisasi,
					       nippegawai,
					       telporganisasi,
					       o.kodeunit,
					       namaunit,
					       akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pembina

					from ".static::table()." o
					       left join akademik.ms_mahasiswa mk
					              on o.nimketua = mk.nim
					       left join akademik.ms_mahasiswa mw
					              on o.nimwakil = mw.nim
					       left join sdm.ms_pegawai p
					              on p.idpegawai :: text = o.nippegawai
					       left join gate.ms_unit u
					              on o.kodeunit = u.kodeunit
					where  ( Lower(( kodeorganisasi ) :: varchar) like '%".$filter."%'
					          or Lower(( namaorganisasi ) :: varchar) like '%".$filter."%'
					          or Lower(( namaunit ) :: varchar) like '%".$filter."%'  )

					order  by kodeorganisasi ";

			return $conn->GetArray($sql);
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." p
					where ".static::getCondition($key);

			return $sql;
		}

		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;

			return Query::arrQuery($conn,$sql);
		}

		// mendapatkan array data by unit
		function getArrayUnit($conn,$kodeunit='') {
			require_once(Route::getModelPath('unit'));

			$row = mUnit::getData($conn,$kodeunit);

			$sql = "select kodeorganisasi, namaorganisasi
							from ".static::table()." o
							join gate.ms_unit u using(kodeunit)
							where u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']."
							order by ".static::order;

			return Query::arrQuery($conn,$sql);
		}

	}
?>
