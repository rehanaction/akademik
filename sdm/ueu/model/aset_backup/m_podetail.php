<?php
	// model surat permintaan barang detail
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPoDetail extends mModel {
		const schema = 'prcm';
		const table = 'pr_podetail';
		const order = 'iddetpo';
		const key = 'iddetpo';
		const label = 'detail purchase order';
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select iddetpo,p.idbarang,b.namabarang,p.qtypo,p.harga
		        from ".static::schema.".pr_podetail p
				join prcm.pr_po po on po.idpo = p.idpo
		        left join aset.ms_barang b on b.idbarang = p.idbarang 
		        where p.idpo = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function dataQuery($key){
		    $sql = "select iddetpo,p.idbarang,b.namabarang,p.qtypo,p.harga,
		        p.idbarang+' - '+b.namabarang as barang
		        from ".static::schema.".pr_podetail p 
				join prcm.pr_po po on po.idpo = p.idpo
		        left join aset.ms_barang b on b.idbarang = p.idbarang 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idbarang', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'qtypo', 'label' => 'Jml. PO', 'type' => 'N,2', 'size' => 10, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'harga', 'label' => 'Harga', 'type' => 'N,2', 'size' => 10, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'status', 'label' => 'Status', 'size' => 10, 'readonly' => $p['isro']);
	        return $a_input;
        }
        
        function getMDetData($conn, $key){
            return $conn->GetRow("select idbarang from ".static::schema.".pr_podetail where iddetpo = '$key'");
        }
		
	}
?>
