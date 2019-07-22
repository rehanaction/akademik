<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOpnameDetail extends mModel {
		const schema = 'aset';
		const table = 'as_opnamedetail';
		const order = 'iddetopname';
		const key = 'iddetopname';
		const label = 'opname seri barang';
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetopname,s.noseri,s.idbarang+' - '+b.namabarang as barang,d.idseri,s.merk,s.spesifikasi,
		        d.idkondisi,k.kondisi,d.idstatus,t.status,s.tglperolehan 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
		        left join ".static::schema.".ms_kondisi k on k.idkondisi = d.idkondisi 
		        left join ".static::schema.".ms_status t on t.idstatus = d.idstatus 
		        where d.idopname = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetopname,s.idbarang,b.namabarang,d.idseri,s.merk,s.spesifikasi,
		        d.idkondisi,k.kondisi,d.idstatus,t.status,s.idbarang+' - '+b.namabarang as barang 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
		        left join ".static::schema.".ms_kondisi k on k.idkondisi = d.idkondisi 
		        left join ".static::schema.".ms_status t on t.idstatus = d.idstatus 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idbarang', 'type' => 'H');
	        $a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'readonly' => true);
	        $a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'readonly' => true);
	        $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'readonly' => true);
	        $a_input[] = array('kolom' => 'idkondisi', 'label' => 'Kondisi', 'type' => 'S', 'option' => $p['a_kondisi'], 'add' => 'style="width:150px;"', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idstatus', 'label' => 'Status', 'type' => 'S', 'option' => $p['a_status'], 'add' => 'style="width:150px;"', 'readonly' => $p['isro']);

	        return $a_input;
        }
	}
?>
