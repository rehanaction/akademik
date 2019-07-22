<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOpnameHPDetail extends mModel {
		const schema = 'aset';
		const table = 'as_opnamehpdetail';
		const order = 'iddetopnamehp';
		const key = 'iddetopnamehp';
		const label = 'detail opname habis pakai';
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetopnamehp,d.idbarang1,b.namabarang,d.qtyawal,d.qtyakhir,b.idsatuan,d.catatan  
		        from ".static::table()." d 
		        left join ".static::schema.".ms_barang1 b on b.idbarang1 = d.idbarang1 
		        where d.idopnamehp = '$idparent' order by b.namabarang";
		    return $conn->GetArray($sql);
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetopnamehp,d.idbarang1,b.namabarang,d.qtyawal,d.qtyakhir,b.idsatuan,d.catatan 
		        from ".static::table()." d 
		        left join ".static::schema.".ms_barang1 b on b.idbarang1 = d.idbarang1 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idbarang1', 'type' => 'H');
	        $a_input[] = array('kolom' => 'idsatuan', 'type' => 'H');
	        $a_input[] = array('kolom' => 'qtyawal', 'label' => 'Jml. Awal', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'qtyakhir', 'label' => 'Jml. Akhir', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10, 'readonly' => $p['isro']);

	        return $a_input;
        }
	}
?>
