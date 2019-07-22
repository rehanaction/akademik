<?php
	// model evaluasi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mEvaluasi extends mModel {
		const schema = 'akademik';
		const table = 'ak_evaluasi';
		const order = 'evaluasike';
		const key = 'programpend,thnkurikulum,evaluasike';
		const label = 'evaluasi';
		
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
					insert into ".static::table()." (thnkurikulum,programpend,evaluasike,semesterevaluasi,batassks,batasip)
					select '$kurtujuan'::numeric,programpend,evaluasike,semesterevaluasi,batassks,batasip from ".static::table()."
					where thnkurikulum = '$kurasal'";
			$ok = $conn->Execute($sql);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		// mengambil data evaluasi pada suatu semester
		function getDataSemester($conn,$kurikulum,$progpend,$semester) {
			$sql = "select evaluasike,batassks,batasip from ".static::table()."
					where thnkurikulum = '$kurikulum' and programpend = '$progpend' and semesterevaluasi = '$semester'";
			
			return $conn->GetRow($sql);
		}
	}
?>