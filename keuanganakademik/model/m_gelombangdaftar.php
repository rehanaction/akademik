<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGelombangDaftar extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_gelombangdaftar';
		const order = 'periodedaftar';
		const key = 'jalurpenerimaan,periodedaftar,idgelombang';
		const label = 'jalur penerimaan';
		
		function getListGelombang($conn,$jalur,$periode) {
			$sql = "select gd.idgelombang, g.namagelombang
					from ".static::table()." gd
					join ".static::table('lv_gelombang')." g on gd.idgelombang = g.idgelombang
					where gd.jalurpenerimaan = ".Query::escape($jalur)."
					and gd.periodedaftar = ".Query::escape($periode)."
					order by gd.idgelombang";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['idgelombang']] = $row['namagelombang'];
			
			return $data;
		}
	}
?>