<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSupplier extends mModel {
		const schema = 'aset';
		const table = 'ms_supplier';
		const order = 'idsupplier';
		const key = 'idsupplier';
		const label = 'Supplier';
		
	    //list supplier
	    function listQuery() {
		    $sql = "select idsupplier,namasupplier,jenissupplier,alamat,notlp,nohp,email 
				    from ".self::table()." s 
				    left join ".static::schema.".ms_jenissupplier j on j.idjenissupplier = s.idjenissupplier";
		
		    return $sql;
	    }
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'jenissupplier': 
				    return "s.idjenissupplier = '$key'";
			    break;
			}
		}
		
		function getNamaSupplier($conn,$key){
		    return empty($key) ? '' : $conn->GetOne("select namasupplier from ".self::table()." where idsupplier = '$key'");
		}
	}
?>
