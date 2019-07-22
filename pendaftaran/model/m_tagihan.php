<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTagihan extends mModel {
		const schema = 'h2h';
		const table = 'ke_tagihan';
		const order = 'idtagihan';
		const key = 'idtagihan';
		const label = 'idtagihan';
		
		
		function dataQuery($key) {
			$sql =" select t.*, p.nama, p.pilihanditerima, p.hp, p.email, u.namaunit, u.kodeunit from h2h.ke_tagihan t join pendaftaran.pd_pendaftar p on p.nopendaftar = t.nopendaftar join gate.ms_unit u on u.kodeunit = p.pilihanditerima ";
			$sql .= " where ".static::getCondition($key);
			
			return $sql;
		}
		
		function getTagihan($conn, $nopendaftar){
			
			$sql = "select * from ".static::table()." where nopendaftar = '$nopendaftar'";
			$rs = $conn->Execute($sql);
			$data = array();
			while ($row =  $rs->fetchRow()){
					$data[] = $row;
			}
		return $data;		
			
			
		}
		

		

	
	}
?>
