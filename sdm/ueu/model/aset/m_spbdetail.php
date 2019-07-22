<?php
	// model surat permintaan barang detail
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSpbDetail extends mModel {
		const schema = 'prcm';
		const table = 'pr_spbdetail';
		const order = 'iddetspb';
		const key = 'iddetspb';
		const label = 'detail surat permintaan barang';
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select iddetspb,p.idbarang1,b.namabarang,p.qtyaju,p.qtysetuju
		        from ".static::schema.".pr_spbdetail p
		        left join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 
		        where p.idspb = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function dataQuery($key){
		    $sql = "select iddetspb,p.idbarang1,b.namabarang,p.qtyaju,p.qtysetuju
		        p.idbarang1+' - '+b.namabarang as barang
		        from ".static::schema.".pr_spbdetail p 
		        left join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idbarang1', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'qtyaju', 'label' => 'Jml. Diajukan', 'type' => 'N,2', 'size' => 10, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'qtysetuju', 'label' => 'Jml. Disetujui', 'type' => 'N,2', 'size' => 10, 'readonly' => $p['isro']);
	        return $a_input;
        }
        
        function getMDetData($conn, $key){
            return $conn->GetRow("select idbarang1 from ".static::schema.".pr_spbdetail where iddetspb = '$key'");
        }
		
	}
?>
