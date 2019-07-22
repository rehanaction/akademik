<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mRawatDetail extends mModel {
		const schema = 'aset';
		const table = 'as_rawatdetail';
		const order = 'iddetrawat';
		const key = 'iddetrawat';
		const label = 'Detail Perawatan';
		
        function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetrawat,right('000000'+convert(varchar(6), s.noseri), 6) as noseri,d.idseri,d.biaya,
		        s.idbarang,b.namabarang,s.merk,s.spesifikasi 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
		        where d.idrawat = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetrawat,d.idrawat,d.idseri,d.biaya,d.catatan,
		        s.idbarang,b.namabarang,s.merk,s.spesifikasi, 
		        right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        s.idbarang+' - '+b.namabarang as labelbarang 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'labelbarang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idseri', 'type' => 'H');
	        $a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'readonly' => true);
	        $a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'readonly' => true);
	        $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'readonly' => true);
	        $a_input[] = array('kolom' => 'biaya', 'label' => 'Biaya', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);

	        return $a_input;
        }		
	}
?>
