<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPeriode extends mModel {
		const schema = 'pendaftaran';
		const table = 'ms_periodedaftar';
		const order = 'periodedaftar';
		const key = 'periodedaftar';
		const label = 'periode';
		
		function getPEriode($conn){
			$sql="
				SELECT periodedaftar FROM pendaftaran.ms_periodedaftar
				";
			return Query::arrQuery($conn,$sql);
		}
		
		function cekaktif($conn){
			return $conn->getOne("select 1 from ".static::table()." where isaktif = -1 ");
			}
	}
?>
