<?php
	// model predikat kelulusan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPredikatkelulusan extends mModel {
		const schema = 'akademik';
		const table = 'ak_predikat';
		const order = 'kodepredikat';
		const key = 'thnkurikulum,kodepredikat,programpend';
		const label = 'predikat kelulusan';
		
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
					insert into ".static::table()." (thnkurikulum,programpend,kodepredikat,namapredikat,namapredikaten,ipkatas,ipkbawah,bataswaktu)
					select '$kurtujuan'::numeric,programpend,kodepredikat,namapredikat,namapredikaten,ipkatas,ipkbawah,bataswaktu from ".static::table()."
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