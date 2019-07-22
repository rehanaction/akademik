<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJalur extends mModel {
		const schema = 'akademik';
		const table = 'lv_jalurpenerimaan';
		const order = 'kodejalur';
		const key = 'kodejalur';
		const label = 'jalur';
		
		function getJalur($conn){
			$sql="
				SELECT jalurpenerimaan FROM pendaftaran.pd_gelombangdaftar
				";
			return Query::arrQuery($conn,$sql);			
		}
		function getAllJalur($conn){
			$sql=" SELECT jalurpenerimaan FROM ".static::table();
			return Query::arrQuery($conn,$sql);			
		}
		
		function getJalurAktif($conn){
			$sql="SELECT gd.*,g.namagelombang FROM pendaftaran.pd_gelombangdaftar gd 
				join pendaftaran.lv_gelombang g using (idgelombang)
				WHERE gd.isaktif='t' /*AND periodedaftar='".date('Y')."'*/";
			return $conn->SelectLimit($sql);
		}
	}
?>
