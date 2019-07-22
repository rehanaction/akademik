<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSyaratBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_syaratbeasiswa';
		const order = 'idbeasiswa,kodesyaratbeasiswa';
		const key = 'idbeasiswa,kodesyaratbeasiswa';
		const label = 'syarat beasiswa';
		
		// hapus syarat lain
		function deleteOther($conn,$idbeasiswa,$keynot=null) {
			$condition = 'idbeasiswa = '.Query::escape($idbeasiswa);
			if(!empty($keynot))
				$condition .= ' and '.str_replace(',',"||'|'||",static::key)." not in ('".implode("','",$keynot)."')";
			
			return Query::qDelete($conn,static::table(),$condition);
		}
	}
?>
