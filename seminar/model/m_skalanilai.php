<?php
	// model skala nilai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSkalaNilai extends mModel {
		const schema = 'akademik';
		const table = 'ak_skalanilai';
		const order = 'nangkasn desc';
		const key = 'thnkurikulum,programpend,nangkasn,istoefl';
		const label = 'skala nilai';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'thnkurikulum': return "thnkurikulum = '$key'";
				case 'progpend': return "programpend = '$key'";
				case 'toefl': return "istoefl = '$key'";
			}
		}
		
		// salin data
		function copy($conn,$kurasal,$kurtujuan) {
			$sql = "delete from ".static::table()." where thnkurikulum = '$kurtujuan';
					insert into ".static::table()." (thnkurikulum,programpend,nhuruf,nangkasn,batasbawah,batasatas,istoefl)
					select '$kurtujuan'::numeric,programpend,nhuruf,nangkasn,batasbawah,batasatas,istoefl from ".static::table()."
					where thnkurikulum = '$kurasal'";
			$ok = $conn->Execute($sql);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		// mendapatkan data per kurikulum
		function getDataKurikulum($conn,$kurikulum) {
			$sql = "select nangkasn,nangkasn||' : '||nhuruf from ".static::table()." where thnkurikulum = '$kurikulum' order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
