<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKontakTeman extends mModel {
		const schema = 'akademik';
		const table = 'ms_kontakteman';
		const order = 'nimteman';
		const key = 'nim,nimteman';
		const label = 'Data Kontak Teman';
		
		function getArray($conn,$key){
			$data = $conn->GetArray("select t.*,m.nama as namateman,coalesce(m.hp,m.hp2) as hp_teman,coalesce(m.telp,m.telp2) as telp_teman from ".static::table()." t
					join ".static::table('ms_mahasiswa')." m on m.nim=t.nimteman
					where ".static::getCondition($key,'t.nim')." order by ".static::order."");
			return $data;
		}
		
		
	}
?>
