<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSetting extends mModel {
		const schema = 'akademik';
		const table = 'ms_setting';
		const order = 'idsetting';
		const key = 'idsetting';
		const label = 'setting global';
		
		// mendapatkan data untuk session
		function getDataSession($conn) {
			$sql = "select * from ".static::table()." where idsetting = 1";
			$row = $conn->GetRow($sql);
			
			$rows = array();
			$rows['KURIKULUM'] = $row['kurikulum'];
			$rows['PERIODE'] = $row['periodesekarang'];
			$rows['TAHAP'] = $row['tahapfrs'];
			$rows['ISINILAI'] = $row['isinilai'];
			$rows['BIODATAMHS'] = $row['biodatamhs'];
			$rows['PERIODENILAI'] = $row['periodenilai'];
			
			return $rows;
		}
		
		// mendapatkan pesan pengesahan
		function getPesanPengesahan($conn) {
			$sql = "select pesanpengesahan from ".static::table()." where idsetting = 1";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan status lintas kurikulum
		function getLintasKurikulum($conn) {
			$sql = "select lintaskurikulum from ".static::table()." where idsetting = 1";
			$lintas = $conn->GetOne($sql);
			
			return (empty($lintas) ? false : true);
		}
		
		// mendapatkan periode krs
		function periodeKRS() {
			$data = array('KRS' => 'Dibuka', 'KULIAH' => 'Ditutup');
			
			return $data;
		}
		
		// mendapatkan pengisian nilai
		function isiNilai() {
			$data = array('DIBUKA' => 'Dibuka', 'DITUTUP' => 'Ditutup');
			
			return $data;
		}
		
		// mendapatkan pengisian biodata
		function isiBiodata() {
			$data = array('1' => 'Dibuka', '0' => 'Ditutup');
			
			return $data;
		}
		
		// mendapatkan lintas kurikulum
		function lintasKurikulum() {
			$data = array('1' => 'Bisa mengambil KRS mata kuliah kurikulum lain');
			
			return $data;
		}
	}
?>