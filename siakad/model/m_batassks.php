<?php
	// model batas sks
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBatassks extends mModel {
		const schema = 'akademik';
		const table = 'ak_batassks';
		const order = 'sksmax';
		const key = 'programpend,thnkurikulum,sksmax';
		const label = 'batas sks';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'thnkurikulum': return "thnkurikulum = '$key'";
				case 'progpend': return "programpend = '$key'";
			}
		}
		
		// salin data
		function copy($conn,$kurasal,$kurtujuan) {
			$sql = "delete from ".static::table()." where thnkurikulum = '$kurtujuan';
					insert into ".static::table()." (thnkurikulum,programpend,sksmax,ipatas,ipbawah,sksatas,sksbawah)
					select '$kurtujuan'::numeric,programpend,sksmax,ipatas,ipbawah,sksatas,sksbawah from ".static::table()."
					where thnkurikulum = '$kurasal'";
			$ok = $conn->Execute($sql);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
	}
?>