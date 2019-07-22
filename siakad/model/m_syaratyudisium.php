<?php
	// model syarat yudisium
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSyaratYudisium extends mModel {
		const schema = 'akademik';
		const table = 'ak_syaratyudisium';
		const order = 'idsyaratyudisium';
		const key = 'idsyaratyudisium';
		const label = 'syarat yudisium';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periodewisuda': return "idyudisium = '$key'";
			}
		}
		
		// salin data
		function copy($conn,$periodeasal,$periodetujuan) {
			$sql = "delete from ".static::table()." where idyudisium = '$periodetujuan';
					insert into ".static::table()." (idyudisium,keterangan)
					select '$periodetujuan'::numeric,keterangan from ".static::table()."
					where idyudisium = '$periodeasal' order by idsyaratyudisium";
			$ok = $conn->Execute($sql);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
	}
?>