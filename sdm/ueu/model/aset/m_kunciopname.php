<?php
	// model kunci opname
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKunciOpname extends mModel {
		const schema = 'aset';
		const table = 'as_kunciopname';
		const order = 'idunit';
		const key = 'idunit';
		const label = 'Penguncian Opname';
		
		//parent unit
		function listQuery() {
			$sql = "select kodeunit, namaunit, namasingkat, parentunit, level, idunit from ".static::table('ms_unit');
			
			return $sql;
		}
		
		function getDataEdit($conn,$kolom,$key,$post='',$svalue='') {
		    return parent::getDataEdit($conn,$kolom,$key,$post);
		}
		
/*
		function getRowByIDP($conn, $idparent){
		    $sql = "select idunit
		        from ".static::table()." d left join ".static::schema.".ms_barang1 b on b.idbarang1 = d.idbarang1 
		        where idperolehan = '$idparent'";
		    return $conn->GetArray($sql);
		}
*/
	}
?>
