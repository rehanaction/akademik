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
			$sql = "select idlokasi,namalokasi,jenislokasi,namagedung,luas,kapasitas
					from ".static::schema.".ms_lokasi l
					left join ".static::schema.".ms_jenislokasi j on j.idjenislokasi = l.idjenislokasi
					left join ".static::schema.".ms_gedung g on g.idgedung = l.idgedung";
		
			return $sql;
		}
		
		function dataQuery($key){
			$sql = "select l.*, p.nip, p.namalengkap, p.nip+' - '+p.namalengkap as pegawai,
				from ".self::table()." l
				left join sdm.v_biodatapegawai p on p.idpegawai = s.idpetugas 
				where ".static::getCondition($key);
		
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
			}
		}
	
	}
	
	
?>
