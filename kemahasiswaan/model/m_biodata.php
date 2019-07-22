<?php
	// model biodata
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBiodata extends mModel {
		// agama
		function agama($conn) {
			require_once(Route::getModelPath('agama'));
			
			return mAgama::getArray($conn);
		}
		
		// golongan darah
		function golonganDarah() {
			$data = array('A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O');
			
			return $data;
		}
		
		// jenis instansi
		function jenisInstansi() {
			$data = array('BUMN' => 'BUMN', 'SWASTA' => 'SWASTA');
			
			return $data;
		}
		
		// jenis kelamin
		function jenisKelamin() {
			$data = array('L' => 'Laki-Laki', 'P' => 'Perempuan');
			
			return $data;
		}
		
		// pekerjaan
		function pekerjaan($conn) {
			require_once(Route::getModelPath('pekerjaan'));
			
			return mPekerjaan::getArray($conn);
		}
		
		// pendapatan
		function pendapatan($conn) {
			require_once(Route::getModelPath('pendapatan'));
			
			return mPendapatan::getArray($conn);
		}
		
		// pendidikan
		function pendidikan($conn) {
			require_once(Route::getModelPath('pendidikan'));
			
			return mPendidikan::getArray($conn);
		}
		
		// status nikah
		function statusNikah($conn) {
			require_once(Route::getModelPath('statusnikah'));
			
			return mStatusNikah::getArray($conn);
		}
		
		// tingkat keahlian
		function tingkatKeahlian() {
			$data = array('1' => 'Tidak Bisa', '2' => 'Pasif', '3' => 'Aktif', '4' => 'Mahir');
			
			return $data;
		}
		
		// status kewarganegaraan
		function wargaNegara($conn) {
			require_once(Route::getModelPath('warganegara'));
			
			return mWargaNegara::getArray($conn);
		}
	}
?>