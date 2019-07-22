<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBeasiswa extends mModel {
		const schema = 'akademik';
		const table = 'ms_mahasiswa';
		const order = 'm.nim';
		const key = 'nim';
		const label = 'Beasiswa';
		
		function dataQuery($key) {
			$sql = "select m.nim, m.nama, u.namaunit, substr(m.periodemasuk,1,4) as angkatan,
					m.potongan, m.potsmtawal, m.potsmtakhir, m.potongansp,
					m.keteranganbeasiswa, m.keteranganpotongansp
					from ".static::table()." m
					join gate.ms_unit u on u.kodeunit = m.kodeunit
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		function listQuery() {
			$sql = "select m.nim, m.nama, u.namaunit, substr(m.periodemasuk,1,4) as angkatan,
					m.potongan, m.potsmtawal, m.potsmtakhir
					from ".static::table()." m
					join gate.ms_unit u on u.kodeunit = m.kodeunit";
			
			return $sql;
		}
		
		function listQuerySP() {
			$sql = "select m.nim, m.nama, u.namaunit, substr(m.periodemasuk,1,4) as angkatan, m.potongansp
					from ".static::table()." m
					join gate.ms_unit u on u.kodeunit = m.kodeunit";
			
			return $sql;
		}
		
		function listCondition() {
			$sql = "m.potongan > 0";
			
			return $sql;
		}
		
		function listConditionSP() {
			$sql = "m.potongansp > 0";
			
			return $sql;
		}
		
		function getArrayListFilterCol() {
			$data['angkatan'] = 'substring(m.periodemasuk,1,4)';
			
			return $data;
		}
		
		function getListFilter($col,$key) {
			global $conn;
			
			switch($col) {
				case 'unit':
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return 'u.infoleft >= '.(int)$row['infoleft'].' and u.inforight <= '.(int)$row['inforight'];
				case 'angkatan': return 'substr(m.periodemasuk,1,4) = '.Query::escape($key);
				case 'jalur': return 'm.jalurpenerimaan = '.Query::escape($key);
				case 'gelombang': return 'm.gelombang = '.Query::escape($key);
			}
		}
		
		function delete($conn,$key) {
			$record = array();
			$record['potongan'] = 0;
			
			$err = static::updateRecord($conn,$record,$key);
			
			$err = Query::boolErr($err);
			$msg = 'Penghapusan beasiswa '.($err ? 'gagal' : 'berhasil');
			
			return array($err,$msg);
		}
	}	
?>
