<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKategoriBeasiswa extends mModel {
		const schema = 'h2h';
		const table = 'lv_kategoribeasiswa';
		const order = 'kodekategori';
		const key = 'kodekategori';
		const label = 'namakategori';
		
		function getListPotonganBeasiswa($conn,$id) {
			$sql = "select p.*, j.namajenistagihan from ".static::table('ke_potonganbeasiswa')." p
					join ".static::table('lv_jenistagihan')." j on p.jenistagihan = j.jenistagihan
					where p.kodekategori = ".Query::escape($id);
			
			return $conn->GetArray($sql);
		}
		
		// aksi
		
		function insertPotonganBeasiswa($conn,$record) {
			return Query::recInsert($conn,$record,static::table('ke_potonganbeasiswa'));
		}
		
		function deletePotonganBeasiswa($conn,$id,$jenis) {
			return Query::qDelete($conn,static::table('ke_potonganbeasiswa'),"kodekategori = ".Query::escape($id)." and jenistagihan = ".Query::escape($jenis));
		}
	}
?>