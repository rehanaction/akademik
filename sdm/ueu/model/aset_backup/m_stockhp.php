<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStockHP extends mModel {
		const schema = 'aset';
		const table = 'as_stockhp';
		const order = 'idstockhp';
		const key = 'idstockhp';
		const label = 'stock habis pakai';
		
		function getIDStock($conn, $idbarang){
		    return $conn->GetOne("select idstockhp from aset.as_stockhp where idbarang = '$idbarang'");
		}
		
		function setStock($conn, $idbarang, $jmlstock, $nilaistock, $idsatuan){
		    $record = array();
		    $record['idbarang'] = $idbarang;
		    $record['idunit'] = '63';
		    $record['jmlstock'] = $jmlstock;
		    $record['nilaistock'] = $nilaistock;
		    $record['idsatuan'] = $idsatuan;
		    
		    $r_idstockhp = self::getIDStock($conn, $idbarang);
		    
		    if(empty($r_idstockhp)){
		        list($p_posterr,$p_postmsg) = parent::insertRecord($conn,$record);
		    }else{
		        list($p_posterr,$p_postmsg) = parent::updateRecord($conn,$record,$r_idstockhp);
		    }
		    
		    return array($p_posterr,$p_postmsg);
		}
	}
?>
