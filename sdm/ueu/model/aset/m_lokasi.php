<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mLokasi extends mModel {
		const schema = 'aset';
		const table = 'ms_lokasi';
		const order = 'idlokasi';
		const key = 'idlokasi';
		const label = 'Lokasi';
		
		//list lokasi
		function listQuery() {
			$sql = "select idlokasi,namalokasi,jenislokasi,namagedung,luas,kapasitas, u.namaunit as unit,lantai
				from ".static::schema.".ms_lokasi l
				left join ".static::schema.".ms_jenislokasi j on j.idjenislokasi = l.idjenislokasi
				left join ".static::schema.".ms_gedung g on g.idgedung = l.idgedung
				left join ".static::schema.".ms_unit u on u.idunit = l.idunit ";
		
			return $sql;
		}
		
		function dataQuery($key){
			$sql = "select l.*, p.nip, p.namalengkap, p.nip+' - '+p.namalengkap as pegawai, u.namaunit as unit
				from ".self::table()." l
				left join aset.ms_unit u on u.idunit = l.idunit 
				left join sdm.v_biodatapegawai p on p.idpegawai = l.idpetugas 
				where l.".static::getCondition($key);
		
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'jenislokasi': 
				    return "l.idjenislokasi = '$key'"; 
				break;
				case 'gedung': 
				    return "l.idgedung = '$key'"; 
				break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "(l.idunit is null or u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'].")";
				break;
				case 'lantai': 
				    return "l.lantai = '$key'"; 
				break;
			}
		}
		
		function lokasi($conn,$idunit=''){
		    $sql = "select idlokasi,idlokasi+' - '+namalokasi as lokasi from ".static::schema.".ms_lokasi ";
		    if($idunit != '')
		        $sql .= "where idunit = '$idunit' ";
	        $sql .= "order by idlokasi";
	        
	        return Query::arrQuery($conn, $sql);
		}
	}
	
	
?>
