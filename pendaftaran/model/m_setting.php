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
			
			return $rows;
		}
		
		// mendapatkan periode krs
		function periodeKRS() {
			$data = array('KRS' => 'KRS', 'KULIAH' => 'Kuliah');
			
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
		
		function getPeriode($conn){
			$data = self::getData($conn, 1);
			return Pendaftaran::getNamaPeriode($data['periodesekarang']);
			}
	}
?>
