<?php
	// model mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mAlumni extends mBiodata {
		const schema = 'akademik';
		const table = 'ms_mahasiswa';
		const order = 'nim';
		const key = 'nim';
		const label = 'alumni';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select m.nim, m.nama, m.sex, m.noijasah, m.notranskrip, m.semestermhs, m.noblankoijasah, substring(m.periodelulus,1,4) as thnlulus
					from ".static::table()." m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan kondisi kueri list
		function listCondition() {
			return "m.statusmhs = 'L'";
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data['unit'] = 'm.kodeunit';
			$data['tahunlulus'] = 'substring(m.periodelulus,1,4)';
			
			return $data;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				default:
					return parent::getListFilter($col,$key);
			}
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select m.nim, m.nama, m.sex, m.noijasah, m.notranskrip, m.semestermhs, substring(m.periodelulus,1,4) as thnlulus,
					m.kodeunit, u.kodeunitparent as kodefakultas, m.namaperusahaan, m.alamatperusahaan, m.kodekotaperusahaan,
					m.telpperusahaan, m.jenisinstansi, m.jabatan, m.pekerjaan, k.kodepropinsi as kodepropinsiperusahaan
					from ".static::table()." m
					left join gate.ms_unit u on u.kodeunit = m.kodeunit
					left join ".static::table('ms_kota')." k on k.kodekota = m.kodekotaperusahaan
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'penghargaan':
					$info['table'] = 'ak_penghargaan';
					$info['key'] = 'idpenghargaan';
					$info['label'] = 'penghargaan';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// penghargaan
		function getPenghargaan($conn,$key,$label='',$post='') {
			$sql = "select idpenghargaan, tglpenghargaan, namapenghargaan
					from ".static::table('ak_penghargaan')."
					where nim = '$key' order by tglpenghargaan desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// tahun lulus
		function tahunLulus($conn) {
			$sql = "select distinct substring(periodelulus,1,4) from ".static::table()."
					where periodelulus is not null order by substring(periodelulus,1,4) desc";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>