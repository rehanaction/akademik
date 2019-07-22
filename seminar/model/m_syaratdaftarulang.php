<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSyaratDaftarUlang extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_syaratdaftarulang';
		const order = 'kodesyarat';
		const key = 'kodesyarat';
		const label = 'SYARAT DAFTAR ULANG';
		
		function getArray($conn){
		
			$sql = self::getListQuery($kosong,"aktif = 'TRUE' ");
			
			return Query::arrQuery($conn,$sql);
		
		}
		
		function getSyaratpendaftar($conn, $nopendaftar){
			$sql="select * from ".static::table('pd_syaratdaftarulang')." where nopendaftar = '$nopendaftar' ";
		$rs = $conn->Execute($sql);
		
		$data = array();
		
		while ($row = $rs->fetchRow()){
			$data[$row['nopendaftar']][$row['kodesyarat']] = true;
			}
		return $data;
			
		}
		
		
		
		
	}
?>
