<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOpname extends mModel {
		const schema = 'aset';
		const table = 'as_opname';
		const order = 'idopname';
		const key = 'idopname';
		const label = 'opname';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idopname,tglopname,u.kodeunit+' - '+u.namaunit as unit,o.idlokasi+' - '+l.namalokasi as lokasi,
			        tglopname,nobukti,status 
					from ".self::table()." o 
					left join ".static::schema.".ms_unit u on u.idunit = o.idunit 
					left join ".static::schema.".ms_lokasi l on l.idlokasi = o.idlokasi";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select idunit,status from ".self::table()." where idopname = '$key'");
		}
	}
?>
