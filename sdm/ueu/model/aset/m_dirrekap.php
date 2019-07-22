<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDIRRekap extends mModel {
		const schema = 'aset';
		const table = 'as_seri';
		const key = 'idseri';
		const label = 'DIR Rekap';
		
		//list rekap inventaris ruang
		function listRekap(){
			$sql = "select * from ".static::schema.".vi_rekapseri";
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'lokasi': 
				    return "idlokasi = '$key'";
			    break;
				case 'pemakai': 
				    return "idpegawai = '$key'";
			    break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					//if($default) {
						return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];	
					//}
					
			}
		}
		
	}
?>
