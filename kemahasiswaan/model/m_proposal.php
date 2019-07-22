<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mProposal extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_proposalkegiatan';
		const sequence = 'proposal_kegiatan_idproposal_seq';
		const order = 'idproposal';
		const key = 'idproposal';
		const label = 'Proposal';
		const uptype = 'proposal';

		// mendapatkan array data
		function listQuery($conn) {
			$sql = "select p.*, namaorganisasi,
					(case
						when isstatus2 = '-1' and coalesce(isstatus3,0) = 0 then
						'Disetujui BEMU'
						when isstatus2 = '-1' and isstatus3 = '-1' then
						'Tervalidasi'
						else
						'Belum Tervalidasi'
					end)
					as status
					from ".static::table()." p
					left join ".static::table('ms_organisasi')." o using (kodeorganisasi)
					join gate.ms_unit u on o.kodeunit = u.kodeunit
					 ";

			return $sql;
		}

		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'fromtanggal')
				return 'p.tglpermohonan >= '.Query::escape(CStr::formatDate($key));
			else if($col == 'sdtanggal')
				return 'p.tglpermohonan <= '.Query::escape(CStr::formatDate($key));
			else if($col == 'unit'){
				global $conn, $conf;
				require_once(Route::getModelPath('unit'));

				$row = mUnit::getData($conn,$key);

				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}else
				return parent::getListFilter($col,$key);
		}

		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." k
					where ".static::getCondition($key);

			return $sql;
		}

		// mendapatkan array data
		function getArray($conn) {
			$sql = "select idproposal, namaprogram
					from ".static::table()."
					join ".static::table('mw_kegiatan')." using (idproposal)
					order by ".static::order;

			return Query::arrQuery($conn,$sql);
		}
	}
?>
