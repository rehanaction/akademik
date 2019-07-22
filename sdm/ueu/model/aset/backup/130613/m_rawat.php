<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mRawat extends mModel {
		const schema = 'aset';
		const table = 'as_rawat';
		const order = 'idrawat';
		const key = 'idrawat';
		const label = 'perawatan';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idrawat,namaunit,tglpembukuan,jenisrawat,tglpengajuan,
		        (case when isverify = 1 then 'Verified' else '' end) as isverify, 
		        (case when isok1 = 1 then 'Disetujui' else '' end) as isok1 
				from ".self::table()." p 
				left join ".static::schema.".ms_unit u on u.idunit = p.idunit
				left join ".static::schema.".ms_jenisrawat j on j.idjenisrawat = p.idjenisrawat";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'jenisrawat': 
				    return "p.idjenisrawat = '$key'";
			    break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function getDataAcpt($conn, $key){
		    return $conn->GetRow("select isok1,isverify from ".self::table()." where idrawat = '$key'");
		}
	}
?>
